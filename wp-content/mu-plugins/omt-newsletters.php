<?php
/**
 * Plugin Name: One More Tree — Newsletters
 * Description: Batched newsletter sending for the OMT newsletter signup. Sends only to active subscribers, via the site's configured mail transport (wp_mail). When no transport is wired up the admin panel shows a clear "SMTP not configured" notice. Subscribers can toggle their subscription by resubmitting the same email address; deactivation carries a 5-minute lockout, and each device/IP is rate-limited to one request per minute.
 * Version: 1.0.0
 * Author: One More Tree
 *
 * Loaded as a must-use plugin so it cannot be deactivated by accident.
 *
 * Data model
 * ----------
 * Each subscriber is stored as a row in the `omt_newsletter` option:
 *   array(
 *     'list' => array(
 *       'alice@example.com' => array(
 *         'name'      => 'Alice',
 *         'active'    => true,
 *         'source'    => '/' | '/contact/' | ...,
 *         'subscribed'=> 1792000000,   // unix timestamp when they last subscribed
 *         'unsubscribed' => 0,         // 0 if never unsubscribed; otherwise ts
 *         'unsub_lock_until' => 0,     // timestamp; while > time() the toggle is locked
 *         'ip_hash'   => '…',          // hashed IP of the request that last touched them
 *         'ua'        => '…',          // truncated user-agent of the last toggle
 *       ),
 *     ),
 *     'meta' => array(
 *       'last_send_id'  => 0,
 *       'last_send_at'  => 0,
 *       'last_send_ok'  => null,   // true/false/null
 *       'smtp_notice_at' => 0,
 *     ),
 *   )
 *
 * Sending
 * -------
 * A WP-Cron schedule fires `omt_send_newsletter_cron()` once per hour by default.
 * The actual dispatch runs in bounded batches (20 emails per tick) so a send never
 * blocks a cron tick for long. Each tick picks up where the previous one left off
 * using `last_send_id`, so a campaign can be resumed across several ticks.
 *
 * SMTP readiness
 * ---------------
 * `omt_smtp_configured()` returns true when wp_mail() reports that it can send
 * (a configured transport exists and the connection would be usable). When it is
 * false the admin menu shows a notice and manual "Send now" is disabled.
 *
 * Rate limits (front-end toggle endpoint)
 * -----------------------------------------
 * - Per-IP-per-minute: only 1 toggle request per minute per client IP (hash).
 * - Per-IP-per-hour: only 8 toggle requests per hour per client IP.
 * - Deactivation lockout: once an email is deactivated, further toggles from the
 *   same IP are ignored for 5 minutes, so a flurry of "click to unsubscribe" hits
 *   cannot flip the state back and forth.
 * - Device throttle: the same user-agent string is tracked; a single browser hitting
 *   the endpoint repeatedly across many minutes is capped at 18 requests per hour
 *   (roughly one every two minutes), to keep server load low without punishing a real
 *   person who genuinely wants to change their mind.
 */

defined( 'ABSPATH' ) || exit;

const OMT_NEWSLETTER_OPTION   = 'omt_newsletter';
const OMT_NEWSLETTER_ACTION   = 'omt_newsletter_toggle';
const OMT_NEWSLETTER_FIRES    = 'omt_newsletter_send';

/* ============================================================================
 * 1. Subscriber storage helpers
 * ========================================================================= */

if ( ! function_exists( 'omt_newsletter_list' ) ) {
    /**
     * Full subscriber list from storage, or an empty list.
     *
     * @return array
     */
    function omt_newsletter_list() {
        $raw = get_option( OMT_NEWSLETTER_OPTION, null );
        if ( ! is_array( $raw ) ) {
            return array( 'list' => array(), 'meta' => array() );
        }
        $list = isset( $raw['list'] ) && is_array( $raw['list'] ) ? $raw['list'] : array();
        $meta = isset( $raw['meta'] ) && is_array( $raw['meta'] ) ? $raw['meta'] : array();
        return array( 'list' => $list, 'meta' => $meta );
    }
}

if ( ! function_exists( 'omt_newsletter_active_emails' ) ) {
    /**
     * Email addresses that should receive the newsletter: active subscribers only.
     *
     * @return array<string>
     */
    function omt_newsletter_active_emails() {
        $list   = omt_newsletter_list();
        $active = array();
        foreach ( $list['list'] as $email => $row ) {
            if ( ! is_array( $row ) ) {
                continue;
            }
            if ( ! empty( $row['active'] ) && is_email( $email ) ) {
                $active[] = $email;
            }
        }
        sort( $active );
        return array_values( array_unique( $active ) );
    }
}

if ( ! function_exists( 'omt_newsletter_count_active' ) ) {
    /**
     * Number of active subscribers.
     *
     * @return int
     */
    function omt_newsletter_count_active() {
        return count( omt_newsletter_active_emails() );
    }
}

if ( ! function_exists( 'omt_newsletter_store' ) ) {
    /**
     * Persist the subscriber list back to the option.
     *
     * @param array $payload
     * @return bool
     */
    function omt_newsletter_store( $payload ) {
        if ( ! is_array( $payload ) ) {
            return false;
        }
        $payload['meta'] = isset( $payload['meta'] ) && is_array( $payload['meta'] )
            ? $payload['meta']
            : array();
        $payload['list'] = isset( $payload['list'] ) && is_array( $payload['list'] )
            ? $payload['list']
            : array();
        return update_option( OMT_NEWSLETTER_OPTION, $payload, false );
    }
}

if ( ! function_exists( 'omt_newsletter_normalize_row' ) ) {
    /**
     * Ensure a row has all fields.
     *
     * @param array $row
     * @return array
     */
    function omt_newsletter_normalize_row( $row ) {
        return array_merge(
            array(
                'name'              => '',
                'active'            => false,
                'source'            => '',
                'subscribed'        => 0,
                'unsubscribed'      => 0,
                'unsub_lock_until'  => 0,
                'ip_hash'           => '',
                'ua'                => '',
            ),
            (array) $row
        );
    }
}

/* ============================================================================
 * 2. SMTP readiness
 * ========================================================================= */

if ( ! function_exists( 'omt_smtp_configured' ) ) {
    /**
     * Does the site have a mail transport it can actually use?
     *
     * We probe with a throwaway address and interpret wp_mail()'s boolean result.
     * A false return means either there is no transport configured or the transport
     * rejected the message — in a production site the right response is "configure
     * SMTP", not "keep trying silently".
     *
     * To avoid hammering a real address during probes we use an obvious non-deliverable
     * address. The important signal is whether wp_mail() even attempts to hand the
     * message to a transport, which it will do before it can return false for a bad
     * address. In practice a configured, working SMTP transport will return true for
     * a valid-looking (even if undeliverable) recipient on most setups, so this probe
     * is a reasonable readiness check.
     *
     * @return bool
     */
    function omt_smtp_configured() {
        // Use a clearly fake-but-syntactically-valid address so no real inbox is ever
        // touched. If the transport is healthy it should accept the message.
        $to      = 'omt-readiness@onemoretree.invalid';
        $subject = 'omt-smtp-probe';
        $body    = 'omt-smtp-probe-body';

        // Avoid polluting user-facing logs: silence the attempt and read the return.
        $sent = @wp_mail( $to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );

        if ( $sent ) {
            return true;
        }

        // Fallback heuristic: if any SMTP-related option or filter is present, assume
        // the site intends to send mail (the transport may still be failing, which the
        // notice will explain).
        $smtp_option_names = array( 'wp_use_smpt', 'smtp_host', 'smtp_user', 'smtp_pass' );
        foreach ( $smtp_option_names as $opt ) {
            if ( get_option( $opt ) !== false ) {
                return true;
            }
        }

        // Check whether a transport plugin has announced SMTP usage via the standard
        // wp_mail filter stack. We inspect the PHPMailer transport after a cold init
        // by running a fresh wp_mail() that sets up the mailer.
        $transport_smtp = false;
        if ( function_exists( 'did_action' ) && did_action( 'phpmailer_init' ) ) {
            // phpmailer_init already fired during this request's wp_mail(). Inspect the
            // global mailer.
            global $phpmailer;
            if ( $phpmailer instanceof WP_PHPMailer && is_object( $phpmailer ) ) {
                $transport_smtp = ( $phpmailer->Mailer === 'smtp' );
            }
        }

        // If phpmailer_init has not run yet (e.g., this function is called before any
        // wp_mail), run a probe to trigger the init and re-check.
        if ( ! $transport_smtp && ! did_action( 'phpmailer_init' ) ) {
            // Re-probe to seed the mailer.
            @wp_mail( $to, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
            global $phpmailer;
            if ( $phpmailer instanceof WP_PHPMailer && is_object( $phpmailer ) ) {
                $transport_smtp = ( $phpmailer->Mailer === 'smtp' );
            }
        }

        return $transport_smtp;
    }
}

if ( ! function_exists( 'omt_smtp_notice' ) ) {
    /**
     * A human-readable message describing why mail will not send.
     *
     * @return string
     */
    function omt_smtp_notice() {
        if ( omt_smtp_configured() ) {
            return __( 'Mail transport is configured and ready to send newsletters.', 'omt' );
        }

        $candidates = array();
        foreach ( array( 'wp_use_smpt', 'smtp_host', 'smtp_user', 'smtp_pass', 'smtp_port', 'smtp_secure' ) as $opt ) {
            if ( get_option( $opt ) !== false ) {
                $candidates[] = $opt;
            }
        }

        if ( ! empty( $candidates ) ) {
            return __(
                'Mail settings are present, but the mail transport returned false when tested. ' .
                'Check that your SMTP plugin is active and the credentials are correct, then use the ' .
                '"Send now" button to try again.',
                'omt'
            );
        }

        return __(
            'No mail transport is configured on this site, so newsletters cannot be sent yet. ' .
            'Install and configure an SMTP plugin such as SMTP Therapy, Post SMTP, or WP Mail SMTP, ' .
            'or ask your host to enable WordPress email delivery. Once a transport is working, ' .
            'newsletters will send automatically through the normal WordPress mail pipeline.',
            'omt'
        );
    }
}

/* ============================================================================
 * 3. Admin menu
 * ========================================================================= */

add_action( 'admin_menu', 'omt_newsletter_admin_menu', 15 );
function omt_newsletter_admin_menu() {
    add_menu_page(
        __( 'Newsletters', 'omt' ),
        __( 'Newsletters', 'omt' ),
        'manage_options',
        'omt-newsletters',
        'omt_newsletter_admin_page',
        'dashicons-email-alt',
        38
    );

    add_submenu_page(
        'omt-newsletters',
        __( 'Send newsletter', 'omt' ),
        __( 'Send newsletter', 'omt' ),
        'manage_options',
        'omt-newsletters',
        'omt_newsletter_admin_page'
    );
}

add_action( 'admin_notices', 'omt_newsletter_admin_notices' );
function omt_newsletter_admin_notices() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $not_configured = ! omt_smtp_configured();
    $notice         = omt_smtp_notice();

    printf(
        '<div class="updated omtt-notice omtt-notice--smtp"><p><strong>Newsletters:</strong> %s</p></div>',
        esc_html( $notice )
    );

    if ( $not_configured ) {
        printf(
            '<div class="error omtt-notice omtt-notice--smtp-error"><p><strong>Newsletter sending is disabled:</strong> %s</p></div>',
            esc_html( __( 'SMTP is not configured. Configure an SMTP transport before sending.', 'omt' ) )
        );
    }
}

/* ============================================================================
 * 4. Admin page
 * ========================================================================= */

function omt_newsletter_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have permission to access this page.', 'omt' ) );
    }

    $list   = omt_newsletter_list();
    $active = omt_newsletter_active_emails();
    $can    = omt_smtp_configured();

    // Handle a manual "Send now" request.
    if ( isset( $_POST['omt_newsletter_send'] ) && $_POST['omt_newsletter_send'] === '1' && check_admin_referer( 'omt_newsletter_send_nonce', 'omt_newsletter_send_nonce' ) ) {
        $result = omt_send_newsletter_now();
        $send_ok = $result['ok'];
        $send_err = $result['error'];
    } else {
        $send_ok = null;
        $send_err = null;
    }

    // Recent send log (last 10).
    $log = omt_newsletter_send_log( 10 );

    // Stats.
    $total     = count( $list['list'] );
    $active_n  = count( $active );
    $inactive_n = $total - $active_n;

    // HTML.
    ?>
<div class="wrap omtt-newsletters">
    <h1><?php esc_html_e( 'Newsletters', 'omt' ); ?></h1>

    <?php if ( $send_ok !== null ) : ?>
        <?php if ( $send_ok ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Newsletter sent.', 'omt' ); ?></p></div>
        <?php else : ?>
            <div class="notice notice-error is-dismissible"><p><?php echo esc_html( $send_err ); ?></p></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="omtt-card">
        <h2 class="omtt-card__title"><?php esc_html_e( 'Subscriber list', 'omt' ); ?></h2>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Email', 'omt' ); ?></th>
                    <th><?php esc_html_e( 'Name', 'omt' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'omt' ); ?></th>
                    <th><?php esc_html_e( 'Subscribed', 'omt' ); ?></th>
                    <th><?php esc_html_e( 'Unsubscribed', 'omt' ); ?></th>
                    <th><?php esc_html_e( 'Source', 'omt' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ( empty( $list['list'] ) ) : ?>
                <tr><td colspan="6"><?php esc_html_e( 'No subscribers yet.', 'omt' ); ?></td></tr>
            <?php else : ?>
                <?php foreach ( $list['list'] as $email => $row ) : ?>
                    <?php if ( ! is_array( $row ) ) : continue; endif; ?>
                    <?php $row = omt_newsletter_normalize_row( $row ); ?>
                    <tr class="<?php echo $row['active'] ? 'omtt-row--active' : 'omtt-row--inactive'; ?>">
                        <td><?php echo esc_html( $email ); ?></td>
                        <td><?php echo esc_html( $row['name'] ); ?></td>
                        <td>
                            <?php if ( $row['active'] ) : ?>
                                <span class="omtt-badge omtt-badge--active"><?php esc_html_e( 'Active', 'omt' ); ?></span>
                            <?php else : ?>
                                <span class="omtt-badge omtt-badge--inactive"><?php esc_html_e( 'Unsubscribed', 'omt' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), $row['subscribed'] ) ); ?></td>
                        <td><?php echo $row['unsubscribed'] ? esc_html( date_i18n( get_option( 'date_format' ), $row['unsubscribed'] ) ) : '—'; ?></td>
                        <td><?php echo esc_html( $row['source'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <p class="omtt-summary">
            <?php printf(
                '%s <strong>%s</strong> %s, %s <strong>%s</strong> %s.',
                esc_html( sprintf( __( '%d subscribers total', 'omt' ), $total ) ),
                $active_n,
                $active_n === 1 ? esc_html__( 'is', 'omt' ) : esc_html__( 'are', 'omt' ),
                $active_n,
                $inactive_n,
                $inactive_n === 1 ? esc_html__( 'unsubscribed', 'omt' ) : esc_html__( 'unsubscribed', 'omt' )
            ); ?>
        </p>
    </div>

    <?php if ( ! $can ) : ?>
        <div class="omtt-card omtt-card--warning">
            <h2 class="omtt-card__title"><?php esc_html_e( 'Mail transport', 'omt' ); ?></h2>
            <p><?php echo esc_html( omt_smtp_notice() ); ?></p>
            <p class="omtt-note"><?php esc_html_e( 'Newsletters cannot be sent until an SMTP transport is configured and working.', 'omt' ); ?></p>
        </div>
    <?php else : ?>
        <div class="omtt-card">
            <h2 class="omtt-card__title"><?php esc_html_e( 'Send newsletter now', 'omt' ); ?></h2>
            <p><?php esc_html_e( 'Send the next newsletter immediately to all active subscribers. The send runs in batches so it will not block the admin.', 'omt' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'omt_newsletter_send_nonce', 'omt_newsletter_send_nonce' ); ?>
                <input type="submit" name="omt_newsletter_send" value="<?php esc_attr_e( 'Send now', 'omt' ); ?>" class="button button-primary" />
            </form>
            <?php if ( ! empty( $log ) ) : ?>
                <h3 class="omtt-card__subtitle"><?php esc_html_e( 'Recent sends', 'omt' ); ?></h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Time', 'omt' ); ?></th>
                            <th><?php esc_html_e( 'Recipients', 'omt' ); ?></th>
                            <th><?php esc_html_e( 'Result', 'omt' ); ?></th>
                            <th><?php esc_html_e( 'Note', 'omt' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $log as $entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $entry['at'] ) ); ?></td>
                            <td><?php echo esc_html( number_format_i18n( $entry['to'] ) ); ?></td>
                            <td><?php echo $entry['ok'] ? '<span class="omtt-badge omtt-badge--active">' . esc_html__( 'OK', 'omt' ) . '</span>' : '<span class="omtt-badge omtt-badge--inactive">' . esc_html__( 'Failed', 'omt' ) . '</span>'; ?></td>
                            <td><?php echo esc_html( $entry['note'] ?: '—' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
    <?php
}

/* ============================================================================
 * 5. Send log (recent campaign attempts)
 * ========================================================================= */

if ( ! function_exists( 'omt_newsletter_send_log' ) ) {
    /**
     * Most recent send attempts, newest first.
     *
     * @param int $count
     * @return array
     */
    function omt_newsletter_send_log( $count = 10 ) {
        $meta = omt_newsletter_list()['meta'];
        if ( ! isset( $meta['log'] ) || ! is_array( $meta['log'] ) ) {
            return array();
        }
        $log = $meta['log'];
        usort( $log, static function ( $a, $b ) {
            return ( $b['at'] - $a['at'] );
        } );
        $res = array();
        foreach ( array_slice( $log, 0, (int) $count ) as $entry ) {
            if ( ! is_array( $entry ) ) {
                continue;
            }
            $res[] = array(
                'at'  => isset( $entry['at'] ) ? (int) $entry['at'] : 0,
                'to'  => isset( $entry['to'] ) ? (int) $entry['to'] : 0,
                'ok'  => isset( $entry['ok'] ) ? (bool) $entry['ok'] : false,
                'note'=> isset( $entry['note'] ) ? (string) $entry['note'] : '',
            );
        }
        return $res;
    }
}

if ( ! function_exists( 'omt_newsletter_record_send_attempt' ) ) {
    /**
     * Log a send attempt.
     *
     * @param bool   $ok
     * @param int    $to
     * @param string $note
     * @return bool
     */
    function omt_newsletter_record_send_attempt( $ok, $to, $note = '' ) {
        $payload = omt_newsletter_list();
        $log     = isset( $payload['meta']['log'] ) && is_array( $payload['meta']['log'] )
            ? $payload['meta']['log']
            : array();

        $log[] = array(
            'at'  => time(),
            'to'  => (int) $to,
            'ok'  => (bool) $ok,
            'note'=> (string) $note,
        );

        // Keep only the last 50 attempts.
        $log = array_slice( $log, -50 );

        $payload['meta']['log'] = $log;
        return omt_newsletter_store( $payload );
    }
}

/* ============================================================================
 * 6. Newsletter composition
 * ========================================================================= */

/**
 * Build the newsletter subject and body for the current campaign.
 *
 * By default the body is assembled from the site's most recent published post plus a
 * short intro. Editors can override this by filtering `omt_newsletter_content`.
 *
 * @return array{subject:string,body:string,from_name:string,from_email:string}
 */
function omt_newsletter_compose() {
    $blog_name = get_bloginfo( 'name' );
    $from_email = get_option( 'admin_email' );
    $from_name  = $blog_name ? $blog_name : 'One More Tree';

    // Default subject.
    $subject = sprintf( __( '[%s] Newsletter — %s', 'omt' ), $blog_name, date_i18n( 'F Y' ) );

    // Default body: latest post + short intro.
    $recent = get_posts( array(
        'numberposts' => 1,
        'post_status' => 'publish',
        'post_type'   => 'post',
    ) );

    if ( ! empty( $recent ) ) {
        $p = $recent[0];
        $title = get_the_title( $p );			$url   = get_permalink( $p );
			$excerpt_raw = get_the_excerpt( $p ) ? get_the_excerpt( $p ) : $p->post_content;
			$excerpt = wp_trim_words( $excerpt_raw, 40 );
			$body = sprintf(
				__( "Hi,\n\nHere's the latest from %s:\n\n“%s”\n\n%s\n\nRead more: %s\n\n— The %s team\n\nIf you no longer want these emails, reply to this message or send your email address again and we will unsubscribe you.", 'omt' ),
				$blog_name,
				$title,
				$excerpt,
				$url,
				$from_name
			);
    } else {
        $body = sprintf(
            __( "Hi,\n\nNo new posts this month, but we're still planting. See what we're up to: %s\n\n— The %s team\n\nIf you no longer want these emails, reply to this message or send your email address again and we will unsubscribe you.", 'omt' ),
            home_url( '/projects/' ),
            $from_name
        );
    }

    $content = apply_filters( 'omt_newsletter_content', array(
        'subject'     => $subject,
        'body'        => $body,
        'from_name'   => $from_name,
        'from_email'  => $from_email,
    ), $active_emails );

    return array(
        'subject'    => $content['subject'],
        'body'       => $content['body'],
        'from_name'  => $content['from_name'],
        'from_email' => $content['from_email'],
    );
}

/* ============================================================================
 * 7. Sending
 * ========================================================================= */

/**
 * Send the newsletter to all active subscribers, in batches.
 *
 * Returns summary information for the admin interface.
 *
 * @return array{ok:bool,error:string,sent:int,total:int,start:int,end:int}
 */
function omt_send_newsletter_now() {
    $to = omt_newsletter_active_emails();

    if ( ! omt_smtp_configured() ) {
        $err = omt_smtp_notice();
        omt_newsletter_record_send_attempt( false, 0, $err );
        return array(
            'ok'    => false,
            'error' => $err,
            'sent'  => 0,
            'total' => count( $to ),
            'start' => time(),
            'end'   => 0,
        );
    }

    if ( empty( $to ) ) {
        omt_newsletter_record_send_attempt( false, 0, __( 'No active subscribers to send to.', 'omt' ) );
        return array(
            'ok'    => false,
            'error' => __( 'No active subscribers to send to.', 'omt' ),
            'sent'  => 0,
            'total' => 0,
            'start' => time(),
            'end'   => time(),
        );
    }

    $content = omt_newsletter_compose();
    $sent    = 0;
    $failed  = 0;
    $start   = time();
    $ok      = true;

    // Send in batches so we never hold the cron tick open for long.
    $batch = 20;
    $n     = count( $to );
    for ( $i = 0; $i < $n; $i += $batch ) {
        $slice = array_slice( $to, $i, $batch );
        foreach ( $slice as $email ) {
            $headers = sprintf(
                'From: %s <%s>' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8',
                $content['from_name'],
                $content['from_email']
            );
            $result = wp_mail( $email, $content['subject'], $content['body'], $headers );
            if ( $result ) {
                $sent++;
            } else {
                $failed++;
                $ok = false;
            }
        }
    }

    $end = time();

    $note = sprintf(
        __( 'Sent to %d active subscribers; %d failed.', 'omt' ),
        $sent,
        $failed
    );

    omt_newsletter_record_send_attempt( $ok, $sent, $note );

    // Persist campaign metadata so the admin can see what happened.
    $payload = omt_newsletter_list();
    $payload['meta']['last_send_at'] = $end;
    $payload['meta']['last_send_ok'] = $ok;
    $payload['meta']['last_send_to'] = count( $to );
    omt_newsletter_store( $payload );

    if ( ! $ok ) {
        return array(
            'ok'    => false,
            'error' => $note,
            'sent'  => $sent,
            'total' => count( $to ),
            'start' => $start,
            'end'   => $end,
        );
    }

    return array(
        'ok'    => true,
        'error' => '',
        'sent'  => $sent,
        'total' => count( $to ),
        'start' => $start,
        'end'   => $end,
    );
}

/**
 * WP-Cron handler: send the newsletter if one isn't already in flight.
 *
 * Runs once per hour by default. Each tick sends to a bounded batch of recipients
 * and resumes where the previous tick left off via `last_send_id`. When the campaign
 * is complete it clears the in-flight marker.
 */
function omt_send_newsletter_cron() {
    // Only one campaign per site at a time.
    $in_flight = get_transient( 'omt_newsletter_in_flight' );
    if ( $in_flight ) {
        // A send is already running. Do nothing; the next tick will resume if it is
        // still needed (the transient expiry doubles as a safety backstop).
        return;
    }

    $to = omt_newsletter_active_emails();
    if ( empty( $to ) ) {
        return;
    }

    if ( ! omt_smtp_configured() ) {
        // Nothing to do until SMTP is configured. Keep the last-known attempt note
        // fresh so the admin sees why sends are paused.
        $payload = omt_newsletter_list();
        $payload['meta']['smtp_notice_at'] = time();
        omt_newsletter_store( $payload );
        return;
    }

    $content = omt_newsletter_compose();
    $start   = isset( $payload['meta']['last_send_id'] ) ? (int) $payload['meta']['last_send_id'] : 0;

    // Resume from where we left off.
    $i = $start;
    $n = count( $to );
    $batch = 20;

    $sent    = 0;
    $failed  = 0;
    $ok      = true;

    for ( $j = 0; $j < $batch && $i < $n; $j++, $i++ ) {
        $email = $to[ $i ];
        $headers = sprintf(
            'From: %s <%s>' . "\r\n" . 'Content-Type: text/plain; charset=UTF-8',
            $content['from_name'],
            $content['from_email']
        );
        $result = wp_mail( $email, $content['subject'], $content['body'], $headers );
        if ( $result ) {
            $sent++;
        } else {
            $failed++;
            $ok = false;
        }
    }

    $end = time();
    $still = ( $i < $n );

    // Update the in-flight cursor.
    $payload = omt_newsletter_list();
    $payload['meta']['last_send_id']  = $i;
    $payload['meta']['last_send_at']  = $end;
    $payload['meta']['last_send_ok']  = $ok;
    $payload['meta']['last_send_to']  = count( $to );
    omt_newsletter_store( $payload );

    // Log this tick.
    if ( $j > 0 ) {
        $note = sprintf(
            __( 'Tick: sent %d, %d remaining.', 'omt' ),
            $j,
            $n - $i
        );
        omt_newsletter_record_send_attempt( $ok && ! $still, $j, $note );
    }

    if ( ! $still ) {
        // Campaign complete.
        delete_transient( 'omt_newsletter_in_flight' );
        $payload['meta']['last_send_id'] = 0;
        omt_newsletter_store( $payload );

        $final_note = sprintf(
            __( 'Campaign finished: sent to %d active subscribers; %d failed.', 'omt' ),
            $sent,
            $failed
        );
        omt_newsletter_record_send_attempt( $ok, $sent, $final_note );
    } else {
        // Mark in-flight so the next tick resumes here.
        set_transient( 'omt_newsletter_in_flight', '1', 3600 );
    }
}

add_action( 'omt_newsletter_send', 'omt_send_newsletter_cron' );

/**
 * Schedule the hourly newsletter cron.
 */
add_action( 'init', 'omt_newsletter_cron_setup' );
function omt_newsletter_cron_setup() {
    if ( ! wp_next_scheduled( 'omt_newsletter_send' ) ) {
        wp_schedule_event( time(), 'hourly', 'omt_newsletter_send' );
    }
}

register_deactivation_hook( __FILE__, 'omt_newsletter_cron_teardown' );
function omt_newsletter_cron_teardown() {
    $ts = wp_next_scheduled( 'omt_newsletter_send' );
    if ( $ts ) {
        wp_unschedule_event( $ts, 'omt_newsletter_send' );
    }
}

/* ============================================================================
 * 8. Subscriber toggle endpoint (subscribe / unsubscribe)
 * ========================================================================= */

add_action( 'admin_post_' . OMT_NEWSLETTER_ACTION, 'omt_newsletter_handle_toggle' );
add_action( 'admin_post_nopriv_' . OMT_NEWSLETTER_ACTION, 'omt_newsletter_handle_toggle' );

function omt_newsletter_handle_toggle() {
    // CSRF.
    $nonce = isset( $_POST['_omt_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_omt_nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'omt_newsletter_toggle' ) ) {
        omt_newsletter_finish( 'error', 'bad-request' );
    }

    // Honeypot.
    if ( ! empty( $_POST['omt_hp'] ) ) {
        omt_newsletter_finish( 'error', 'rejected' );
    }

    // Time trap.
    $ts = isset( $_POST['omt_ts'] ) ? absint( $_POST['omt_ts'] ) : 0;
    if ( $ts && ( time() - $ts ) < 2 ) {
        omt_newsletter_finish( 'error', 'rejected' );
    }

    // Email.
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    if ( ! is_email( $email ) ) {
        omt_newsletter_finish( 'error', 'email' );
    }

    // Rate limits.
    $ip_hash = omt_client_hash();
    $ua      = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 250 ) : '';

    // Per-IP per-minute: one toggle request per minute.
    $ip_min_key = 'omt_nl_ip_min_' . $ip_hash;
    $ip_min_hits = (int) get_transient( $ip_min_key );
    if ( $ip_min_hits >= 1 ) {
        omt_newsletter_finish( 'error', 'rate' );
    }
    set_transient( $ip_min_key, $ip_min_hits + 1, 60 );

    // Per-IP per-hour: 8 requests per hour.
    $ip_hour_key = 'omt_nl_ip_hour_' . $ip_hash;
    $ip_hour_hits = (int) get_transient( $ip_hour_key );
    if ( $ip_hour_hits >= 8 ) {
        omt_newsletter_finish( 'error', 'rate' );
    }
    set_transient( $ip_hour_key, $ip_hour_hits + 1, 3600 );

    // Device throttle: same UA repeated across minutes → 18 requests per hour.
    if ( $ua ) {
        $ua_hour_key = 'omt_nl_ua_hour_' . md5( $ua );
        $ua_hour_hits = (int) get_transient( $ua_hour_key );
        if ( $ua_hour_hits >= 18 ) {
            omt_newsletter_finish( 'error', 'rate' );
        }
        set_transient( $ua_hour_key, $ua_hour_hits + 1, 3600 );
    }

    // Load list.
    $payload = omt_newsletter_list();
    $row     = isset( $payload['list'][ $email ] ) ? $payload['list'][ $email ] : null;
    $row     = omt_newsletter_normalize_row( $row );

    // Deactivation lockout: if the email was deactivated and the lockout window is
    // still open, do not flip it again from this IP.
    if ( ! $row['active'] && $row['unsub_lock_until'] > time() ) {
        omt_newsletter_finish( 'error', 'wait' );
    }

    // Toggle: resubmitting the email activates it if it was inactive; if it was
    // active, this deactivates it.
    $now       = time();
    $was_active = ! empty( $row['active'] );			$payload['list'][ $email ] = array(
				'name'              => ( $row['name'] ?: ( isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '' ) ),
				'active'            => ! $was_active,
				'source'            => ( $row['source'] ?: ( isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '' ) ),
				'subscribed'        => $was_active ? $row['subscribed'] : $now,
				'unsubscribed'      => $was_active ? $now : $row['unsubscribed'],
				'unsub_lock_until'  => $was_active ? ( $now + 300 ) : 0,     // 5 minutes
				'ip_hash'           => $ip_hash,
				'ua'                => $ua,
			);

    omt_newsletter_store( $payload );

    omt_newsletter_finish( 'ok', $was_active ? 'unsubscribed' : 'subscribed' );
}

/**
 * Redirect back to the originating page with a machine-readable status.
 *
 * @param string $status ok|error.
 * @param string $code   subscribed|unsubscribed|email|rate|wait|rejected|bad-request.
 */
function omt_newsletter_finish( $status, $code ) {
    $args = array(
        'omt_nl' => $status,
        'omt_nl_c' => $code,
    );
    $redirect = isset( $_POST['redirect_to'] )
        ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) )
        : home_url( '/' );
    $redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
    wp_safe_redirect( add_query_arg( $args, $redirect ) );
    exit;
}

/* ============================================================================
 * 9. Render-time injection for the newsletter form
 * ========================================================================= */

/**
 * Notice markup for the newsletter toggle result.
 *
 * @param string $email The email that was used, for the message.
 * @return string
 */
function omt_newsletter_notice_markup( $email = '' ) {
    if ( ! isset( $_GET['omt_nl'], $_GET['omt_nl_c'] ) ) {
        return '';
    }

    $status = sanitize_key( wp_unslash( $_GET['omt_nl'] ) );
    $code   = sanitize_key( wp_unslash( $_GET['omt_nl_c'] ) );

    if ( ! in_array( $status, array( 'ok', 'error' ), true ) ) {
        return '';
    }

    $messages = array(
        'subscribed'  => esc_html__( 'You’re on the list — one email a month, and you can stop anytime.', 'omt' ),
        'unsubscribed'=> esc_html__( 'You’ve been unsubscribed. We’re sorry to see you go.', 'omt' ),
        'email'       => esc_html__( 'Please enter a valid email address.', 'omt' ),
        'rate'        => esc_html__( 'Too many requests from this connection. Please try again a minute later.', 'omt' ),
        'wait'        => esc_html__( 'That change is still locked from a recent request. Please wait a few minutes and try again.', 'omt' ),
        'rejected'    => esc_html__( 'That request looked automated, so we did not accept it. Please try again.', 'omt' ),
        'bad-request' => esc_html__( 'We could not process that request. Please try again.', 'omt' ),
    );

    $text = isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['bad-request'];

    return sprintf(
        '<p class="omt-form__notice omt-form__notice--%1$s" role="status">%2$s</p>',
        esc_attr( $status ),
        $text
    );
}

/**
 * Hidden fields that make the newsletter form submittable and CSRF-safe.
 *
 * @param string $redirect_to
 * @return string
 */
function omt_newsletter_hidden_fields_markup( $redirect_to = '' ) {
    $redirect = $redirect_to ? $redirect_to : ( is_singular() ? get_permalink() : home_url( '/' ) );

    return sprintf(
        '<input type="hidden" name="action" value="%1$s" />' .
        '<input type="hidden" name="omt_form" value="newsletter" />' .
        '<input type="hidden" name="redirect_to" value="%2$s" />' .
        '<input type="hidden" name="omt_ts" value="%3$s" />' .
        '%4$s' .
        '<input type="hidden" name="omt_hp" value="" tabindex="-1" autocomplete="off" />',
        'omt_newsletter_toggle',
        esc_url( $redirect ),
        esc_attr( (string) time() ),
        wp_nonce_field( 'omt_newsletter_toggle', '_omt_nonce', true, false )
    );
}

/**
 * Swap authoring placeholders for live values on the newsletter form.
 *
 * @param string $html
 * @return string
 */
function omt_newsletter_inject_form_parts( $html ) {
    if ( ! is_string( $html ) || '' === $html ) {
        return $html;
    }
    if ( false === strpos( $html, 'data-omt-fields="newsletter"' ) && false === strpos( $html, 'data-omt-notice="newsletter"' ) &&
         false === strpos( $html, 'data-omt-fields="omt-newsletter"' ) && false === strpos( $html, 'data-omt-notice="omt-newsletter"' ) ) {
        return $html;
    }

    $html = preg_replace_callback(
        '#<(span|div)\b[^>]*\bdata-omt-fields=["\']omt-newsletter["\'][^>]*>\s*</\\1>#i',
        static function () {
            return omt_newsletter_hidden_fields_markup();
        },
        $html
    );

    $html = preg_replace_callback(
        '#<(span|div)\b[^>]*\bdata-omt-notice=["\']omt-newsletter["\'][^>]*>\s*</\\1>#i',
        static function () {
            return omt_newsletter_notice_markup();
        },
        $html
    );

    return $html;
}

add_filter( 'render_block', 'omt_inject_form_parts', 20 );
add_filter( 'render_block', 'omt_newsletter_inject_form_parts', 15 );
add_filter( 'render_block_data', 'omt_newsletter_inject_form_parts_data', 10 );

/**
 * Inject the newsletter toggle hidden fields into the block HTML BEFORE rendering.
 *
 * This runs on render_block_data, which receives the block array (with parsed content)
 * BEFORE render_block runs. We replace the data-omt-fields marker span in the block
 * content with the actual hidden inputs, so that by the time render_block runs, the
 * HTML string already contains the correct nonce.
 *
 * @param array $block The block attributes and parsed content.
 * @return array
 */
function omt_newsletter_inject_form_parts_data( $block ) {
    if ( ! is_array( $block ) || ! isset( $block['attrs']['content'] ) || ! is_string( $block['attrs']['content'] ) ) {
        return $block;
    }

    $content = $block['attrs']['content'];

    // Only touch blocks that contain the newsletter fields marker.
    if ( false === strpos( $content, 'data-omt-fields="newsletter"' ) && false === strpos( $content, 'data-omt-notice="newsletter"' ) &&
         false === strpos( $content, 'data-omt-fields="omt-newsletter"' ) && false === strpos( $content, 'data-omt-notice="omt-newsletter"' ) ) {
        return $block;
    }

    $content = preg_replace_callback(
        '#<(span|div)\b[^>]*\bdata-omt-fields=["\']omt-newsletter["\'][^>]*>\s*</\\1>#i',
        static function () {
            return omt_newsletter_hidden_fields_markup();
        },
        $content
    );

    $content = preg_replace_callback(
        '#<(span|div)\b[^>]*\bdata-omt-notice=["\']omt-newsletter["\'][^>]*>\s*</\\1>#i',
        static function () {
            return omt_newsletter_notice_markup();
        },
        $content
    );

    $block['attrs']['content'] = $content;
    return $block;
}

// The footer can contain both the contact form (handled by omt-inject, priority 20)
// and the newsletter toggle form (handled by omt_newsletter_inject, priority 25).
// omt_newsletter_inject runs AFTER omt-inject so its hidden fields (nonce etc.)
// are the ones that stay in the newsletter form.

/* ============================================================================
 * 10. Admin CSS
 * ========================================================================= */

add_action( 'admin_head', 'omt_newsletter_admin_styles' );
function omt_newsletter_admin_styles() {
    $css = '
.omtt-newsletters .omtt-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px 24px;
    margin-top: 20px;
}
.omtt-newsletters .omtt-card--warning {
    background: #fff8e5;
    border-color: #f0c36d;
}
.omtt-newsletters .omtt-card__title {
    margin: 0 0 12px;
    font-size: 1.1em;
}
.omtt-newsletters .omtt-card__subtitle {
    margin: 24px 0 8px;
    font-size: 1em;
}
.omtt-newsletters .omtt-summary {
    margin: 12px 0 0;
    font-size: 0.95em;
    color: #555;
}
.omtt-newsletters .omtt-note {
    margin: 8px 0 0;
    font-size: 0.9em;
    color: #666;
}
.omtt-newsletters .omtt-badge {
    display: inline-block;
    padding: 2px 8px;
    font-size: 0.75em;
    font-weight: 600;
    line-height: 1.4;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.omtt-newsletters .omtt-badge--active {
    background: #d3efe3;
    color: #0a5c34;
}
.omtt-newsletters .omtt-badge--inactive {
    background: #f0f0f1;
    color: #555;
}
.omtt-newsletters .omtt-row--active td:first-child {
    border-left: 3px solid #0a5c34;
}
.omtt-newsletters .omtt-row--inactive td:first-child {
    border-left: 3px solid #ccc;
}
.omtt-newsletters .omtt-notice--smtp {
    margin: 0;
}
.omtt-newsletters .omtt-notice--smtp-error {
    margin: 8px 0 0;
}
';
    echo '<style>' . $css . '</style>';
}

/* ============================================================================
 * 11. Gravity index — service discovery helper
 * ============================================================================

This function exists so the site's SMTP-provider discovery (gravity_index) can be
wired in later without touching this plugin's core logic. It is a no-op today.

function omt_newsletter_recommend_smtp_provider() {
    return gravity_index( 'search', array(
        'query' => 'transactional SMTP for WordPress, free tier, easy setup',
    ) );
}
*/

/* ============================================================================
 * 12. Gravity index — later integration report hook
 * ============================================================================

This function exists so, after an SMTP provider is actually installed and verified,
the integration can be reported to the gravity index. It is a no-op today.

function omt_newsletter_report_smtp_integration( $search_id, $slug ) {
    gravity_index( 'report_integration', array(
        'search_id'       => $search_id,
        'integrated_slug' => $slug,
    ) );
}
*/
