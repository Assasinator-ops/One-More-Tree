<?php
/**
 * Plugin Name: One More Tree — SMTP
 * Description: Configures the site's outgoing email transport (wp_mail) from a secure Settings page. Replaces the need for a third-party SMTP plugin; credentials can be encrypted at rest with a key defined in wp-config.php, the password is never echoed back to the browser, and a built-in test email reports real PHPMailer errors.
 * Version: 1.0.0
 * Author: One More Tree
 *
 * Loaded as a must-use plugin so it cannot be deactivated by accident.
 *
 * Design notes
 * ------------
 * - Settings live under Settings → Email (SMTP), admins only, nonce-protected.
 * - On `phpmailer_init` we point PHPMailer at the configured SMTP server;
 *   with no host configured the plugin does nothing and wp_mail() falls back
 *   to the default (mail()) transport.
 * - The password is written once and never read back into HTML. If
 *   OMT_SMTP_KEY is defined in wp-config.php it is stored encrypted
 *   (AES-256-GCM); otherwise plaintext, with a warning on the settings page.
 * - A "send test email" action reports the real wp_mail_failed error message
 *   so misconfiguration is diagnosable in one click.
 *
 * @package OneMoreTree
 */

defined( 'ABSPATH' ) || exit;

const OMT_SMTP_OPT_HOST    = 'omt_smtp_host';
const OMT_SMTP_OPT_PORT    = 'omt_smtp_port';
const OMT_SMTP_OPT_SECURE  = 'omt_smtp_secure'; // '' | 'ssl' | 'tls'.
const OMT_SMTP_OPT_AUTH    = 'omt_smtp_auth';   // '1' | '0'.
const OMT_SMTP_OPT_USER    = 'omt_smtp_user';
const OMT_SMTP_OPT_PASS    = 'omt_smtp_pass';
const OMT_SMTP_OPT_FROM    = 'omt_smtp_from_email';
const OMT_SMTP_OPT_NAME    = 'omt_smtp_from_name';
const OMT_SMTP_OPT_UPDATED = 'omt_smtp_updated'; // unix timestamp of last save.

/* ---------------------------------------------------------------------------
 * 1. Helpers
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'omt_smtp_settings' ) ) {
    /**
     * All current SMTP options as an array, with resolved defaults.
     *
     * @return array{host:string,port:int,secure:string,auth:bool,user:string,pass:string,from_email:string,from_name:string,updated:int}
     */
    function omt_smtp_settings() {
        return array(
            'host'       => (string) get_option( OMT_SMTP_OPT_HOST, '' ),
            'port'       => absint( get_option( OMT_SMTP_OPT_PORT, 587 ) ),
            'secure'     => in_array( (string) get_option( OMT_SMTP_OPT_SECURE, '' ), array( '', 'ssl', 'tls' ), true ) ? (string) get_option( OMT_SMTP_OPT_SECURE, '' ) : '',
            'auth'       => (bool) get_option( OMT_SMTP_OPT_AUTH, 0 ),
            'user'       => (string) get_option( OMT_SMTP_OPT_USER, '' ),
            'pass'       => (string) get_option( OMT_SMTP_OPT_PASS, '' ),
            'from_email' => (string) get_option( OMT_SMTP_OPT_FROM, '' ),
            'from_name'  => (string) get_option( OMT_SMTP_OPT_NAME, '' ),
            'updated'    => absint( get_option( OMT_SMTP_OPT_UPDATED, 0 ) ),
        );
    }
}

if ( ! function_exists( 'omt_smtp_config_is_set' ) ) {
    /**
     * True when a host is configured, i.e. the plugin should apply SMTP.
     *
     * @return bool
     */
    function omt_smtp_config_is_set() {
        return '' !== trim( (string) get_option( OMT_SMTP_OPT_HOST, '' ) );
    }
}

if ( ! function_exists( 'omt_smtp_key_available' ) ) {
    /**
     * Is an at-rest encryption key defined in wp-config.php?
     *
     * @return bool
     */
    function omt_smtp_key_available() {
        return defined( 'OMT_SMTP_KEY' ) && is_string( OMT_SMTP_KEY ) && strlen( OMT_SMTP_KEY ) >= 32;
    }
}

if ( ! function_exists( 'omt_smtp_encrypt' ) ) {
    /**
     * Encrypt a secret for storage when a key is available.
     *
     * @param string $plain Password in clear text.
     * @return string 'enc:'-prefixed ciphertext, or the input unchanged.
     */
    function omt_smtp_encrypt( $plain ) {
        if ( '' === $plain || ! omt_smtp_key_available() ) {
            return $plain;
        }

        $key     = hash( 'sha256', OMT_SMTP_KEY, true );
        $nonce   = random_bytes( 12 );
        $cipher  = openssl_encrypt( $plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag );

        if ( false === $cipher ) {
            return $plain;
        }

        return 'enc:' . base64_encode( $nonce . $tag . $cipher );
    }
}

if ( ! function_exists( 'omt_smtp_decrypt' ) ) {
    /**
     * Reverse {@see omt_smtp_encrypt()}. Unrecognised values pass through so a
     * value saved before the key existed still reads (and is re-encrypted on
     * the next save).
     *
     * @param string $stored Stored password value.
     * @return string Decrypted password, or the input unchanged.
     */
    function omt_smtp_decrypt( $stored ) {
        if ( 0 !== strpos( $stored, 'enc:' ) || ! omt_smtp_key_available() ) {
            return $stored;
        }

        $raw = base64_decode( substr( $stored, 4 ), true );
        if ( false === $raw || strlen( $raw ) < 29 ) {
            return '';
        }

        $key    = hash( 'sha256', OMT_SMTP_KEY, true );
        $nonce  = substr( $raw, 0, 12 );
        $tag    = substr( $raw, 12, 16 );
        $cipher = substr( $raw, 28 );
        $plain  = openssl_decrypt( $cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag );

        return false === $plain ? '' : $plain;
    }
}

if ( ! function_exists( 'omt_smtp_password_stored_encrypted' ) ) {
    /**
     * Is the stored password ciphertext rather than plaintext?
     *
     * @return bool
     */
    function omt_smtp_password_stored_encrypted() {
        return 0 === strpos( (string) get_option( OMT_SMTP_OPT_PASS, '' ), 'enc:' );
    }
}

/* ---------------------------------------------------------------------------
 * 2. Transport — configure PHPMailer from the saved options
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'omt_smtp_configure_phpmailer' ) ) {
    /**
     * Apply the saved SMTP settings to PHPMailer before each send.
     *
     * @param PHPMailer $phpmailer The global mailer instance.
     * @return void
     */
    function omt_smtp_configure_phpmailer( $phpmailer ) {
        if ( ! omt_smtp_config_is_set() ) {
            return;
        }

        $settings = omt_smtp_settings();

        $phpmailer->isSMTP();
        $phpmailer->Host       = $settings['host'];
        $phpmailer->Port       = $settings['port'];

        if ( 'ssl' === $settings['secure'] ) {
            $phpmailer->SMTPSecure = 'ssl';
        } elseif ( 'tls' === $settings['secure'] ) {
            $phpmailer->SMTPSecure = 'tls';
        } else {
            $phpmailer->SMTPSecure = '';
        }

        $phpmailer->SMTPAutoTLS = false;

        if ( $settings['auth'] && '' !== $settings['user'] ) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $settings['user'];
            $phpmailer->Password = omt_smtp_decrypt( $settings['pass'] );
        } else {
            $phpmailer->SMTPAuth = false;
        }

        if ( is_email( $settings['from_email'] ) ) {
            $from_name = '' !== $settings['from_name'] ? $settings['from_name'] : get_bloginfo( 'name' );
            $phpmailer->setFrom( $settings['from_email'], $from_name, 0 );
        }
    }
}
add_action( 'phpmailer_init', 'omt_smtp_configure_phpmailer' );

if ( ! function_exists( 'omt_smtp_force_from' ) ) {
    /**
     * Force the From header as a filter as well, so headers hand-built by
     * other code cannot override the verified envelope sender.
     *
     * @param string $from Default from address.
     * @return string
     */
    function omt_smtp_force_from( $from ) {
        $settings = omt_smtp_settings();

        return is_email( $settings['from_email'] ) ? $settings['from_email'] : $from;
    }
}
add_filter( 'wp_mail_from', 'omt_smtp_force_from', 5 );

/* ---------------------------------------------------------------------------
 * 3. Admin page — Settings → Email (SMTP)
 * ------------------------------------------------------------------------- */

if ( ! function_exists( 'omt_smtp_admin_menu' ) ) {
    /**
     * Register the settings page.
     *
     * @return void
     */
    function omt_smtp_admin_menu() {
        add_options_page(
            __( 'Email (SMTP)', 'omt' ),
            __( 'Email (SMTP)', 'omt' ),
            'manage_options',
            'omt-smtp',
            'omt_smtp_render_page'
        );
    }
}
add_action( 'admin_menu', 'omt_smtp_admin_menu' );

if ( ! function_exists( 'omt_smtp_handle_save' ) ) {
    /**
     * Save validated settings. Runs before the page renders so status notices
     * can reflect the result.
     *
     * @return void
     */
    function omt_smtp_handle_save() {
        if ( ! isset( $_POST['omt_smtp_save'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to manage email settings.', 'omt' ) );
        }

        check_admin_referer( 'omt_smtp_save', 'omt_smtp_nonce' );

        $host   = sanitize_text_field( wp_unslash( $_POST['omt_smtp_host'] ?? '' ) );
        $port   = isset( $_POST['omt_smtp_port'] ) ? absint( wp_unslash( $_POST['omt_smtp_port'] ) ) : 587;
        $secure = in_array( sanitize_key( wp_unslash( $_POST['omt_smtp_secure'] ?? '' ) ), array( '', 'ssl', 'tls' ), true )
            ? sanitize_key( wp_unslash( $_POST['omt_smtp_secure'] ?? '' ) )
            : '';
        $auth     = ! empty( $_POST['omt_smtp_auth'] );
        $user     = sanitize_text_field( wp_unslash( $_POST['omt_smtp_user'] ?? '' ) );
        $password = isset( $_POST['omt_smtp_pass'] ) ? (string) wp_unslash( $_POST['omt_smtp_pass'] ) : '';
        $from     = sanitize_text_field( wp_unslash( $_POST['omt_smtp_from_email'] ?? '' ) );
        $fromname = sanitize_text_field( wp_unslash( $_POST['omt_smtp_from_name'] ?? '' ) );

        if ( $port < 1 || $port > 65535 ) {
            $port = 587;
        }

        update_option( OMT_SMTP_OPT_HOST, $host, false );
        update_option( OMT_SMTP_OPT_PORT, $port, false );
        update_option( OMT_SMTP_OPT_SECURE, $secure, false );
        update_option( OMT_SMTP_OPT_AUTH, $auth ? '1' : '0', false );
        update_option( OMT_SMTP_OPT_USER, $user, false );

        // Empty password field = keep the saved one (write-only UI). A literal
        // '-' clears it, so a password can be removed without re-entering one.
        if ( '-' === $password ) {
            update_option( OMT_SMTP_OPT_PASS, '', false );
        } elseif ( '' !== $password ) {
            update_option( OMT_SMTP_OPT_PASS, omt_smtp_encrypt( $password ), false );
        }

        update_option( OMT_SMTP_OPT_FROM, is_email( $from ) ? $from : '', false );
        update_option( OMT_SMTP_OPT_NAME, $fromname, false );
        update_option( OMT_SMTP_OPT_UPDATED, time(), false );

        add_settings_error(
            'omt_smtp',
            'omt_smtp_saved',
            __( 'Email settings saved.', 'omt' ),
            'success'
        );
    }
}

if ( ! function_exists( 'omt_smtp_handle_test' ) ) {
    /**
     * Send a test email and surface the real wp_mail_failed error message.
     *
     * @return void
     */
    function omt_smtp_handle_test() {
        if ( ! isset( $_POST['omt_smtp_test'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to manage email settings.', 'omt' ) );
        }

        check_admin_referer( 'omt_smtp_save', 'omt_smtp_nonce' );

        $to   = isset( $_POST['omt_smtp_test_to'] ) ? sanitize_text_field( wp_unslash( $_POST['omt_smtp_test_to'] ) ) : '';
        $to   = is_email( $to ) ? $to : get_option( 'admin_email' );
        $sent = wp_mail(
            $to,
            sprintf( '[%s] SMTP test email', get_bloginfo( 'name' ) ),
            sprintf(
                "This is a test email from %s, sent at %s.\n\nIf you are reading this, wp_mail() works.",
                home_url(),
                current_time( 'mysql' )
            ),
            array( 'Content-Type: text/plain; charset=UTF-8' )
        );

        if ( $sent ) {
            add_settings_error(
                'omt_smtp',
                'omt_smtp_test_ok',
                sprintf( /* translators: %s: email address. */ __( 'Test email accepted for delivery to %s.', 'omt' ), $to ),
                'success'
            );
            return;
        }

        $detail = __( 'wp_mail() returned false. Check the settings below.', 'omt' );

        $capture = function ( $error ) {
            if ( $error instanceof WP_Error ) {
                $messages = array();

                foreach ( $error->get_error_messages() as $message ) {
                    $messages[] = (string) $message;
                }

                update_option( 'omt_smtp_last_error', implode( ' — ', $messages ), false );
            }
        };

        add_action( 'wp_mail_failed', $capture );
        wp_mail( $to, sprintf( '[%s] SMTP test email', get_bloginfo( 'name' ) ), 'retry to capture the transport error', array( 'Content-Type: text/plain; charset=UTF-8' ) );
        remove_action( 'wp_mail_failed', $capture );

        $last = get_option( 'omt_smtp_last_error' );

        if ( is_string( $last ) && '' !== $last ) {
            $detail = $last;
        }

        delete_option( 'omt_smtp_last_error' );

        add_settings_error(
            'omt_smtp',
            'omt_smtp_test_fail',
            sprintf( /* translators: 1: email address, 2: error detail. */ __( 'Test email to %1$s failed: %2$s', 'omt' ), $to, esc_html( $detail ) ),
            'error'
        );
    }
}

if ( ! function_exists( 'omt_smtp_admin_init' ) ) {
    /**
     * Hook the save/test handlers early enough to issue notices.
     *
     * @return void
     */
    function omt_smtp_admin_init() {
        omt_smtp_handle_save();
        omt_smtp_handle_test();
    }
}
add_action( 'admin_init', 'omt_smtp_admin_init' );

if ( ! function_exists( 'omt_smtp_render_page' ) ) {
    /**
     * Render the settings page.
     *
     * @return void
     */
    function omt_smtp_render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to manage email settings.', 'omt' ) );
        }

        $settings = omt_smtp_settings();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Email (SMTP)', 'omt' ); ?></h1>

            <?php if ( '' === $settings['host'] ) : ?>
                <div class="notice notice-warning"><p>
                    <?php esc_html_e( 'No SMTP host is configured. Mail falls back to the PHP default transport, which most hosts block. Forms and newsletters need a working SMTP account.', 'omt' ); ?>
                </p></div>
            <?php endif; ?>

            <?php if ( '' !== $settings['host'] && ! omt_smtp_password_stored_encrypted() && omt_smtp_key_available() ) : ?>
                <div class="notice notice-warning"><p>
                    <?php esc_html_e( 'A saved password is still stored in plain text (saved before an encryption key existed). Re-entering the password now will store it encrypted.', 'omt' ); ?>
                </p></div>
            <?php endif; ?>

            <?php settings_errors( 'omt_smtp' ); ?>

            <form method="post">
                <?php wp_nonce_field( 'omt_smtp_save', 'omt_smtp_nonce' ); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="omt_smtp_host"><?php esc_html_e( 'SMTP host', 'omt' ); ?></label></th>
                        <td>
                            <input type="text" id="omt_smtp_host" name="omt_smtp_host" value="<?php echo esc_attr( $settings['host'] ); ?>" class="regular-text" placeholder="smtp.example.com" />
                            <p class="description">
                                <?php esc_html_e( 'Leave empty to disable SMTP and use the default transport.', 'omt' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="omt_smtp_port"><?php esc_html_e( 'Port', 'omt' ); ?></label></th>
                        <td><input type="number" id="omt_smtp_port" name="omt_smtp_port" value="<?php echo esc_attr( $settings['port'] ); ?>" min="1" max="65535" class="small-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="omt_smtp_secure"><?php esc_html_e( 'Encryption', 'omt' ); ?></label></th>
                        <td>
                            <select id="omt_smtp_secure" name="omt_smtp_secure">
                                <option value="" <?php selected( $settings['secure'], '' ); ?>><?php esc_html_e( 'None (insecure)', 'omt' ); ?></option>
                                <option value="ssl" <?php selected( $settings['secure'], 'ssl' ); ?>><?php esc_html_e( 'SSL (usually port 465)', 'omt' ); ?></option>
                                <option value="tls" <?php selected( $settings['secure'], 'tls' ); ?>><?php esc_html_e( 'TLS / STARTTLS (usually port 587)', 'omt' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Authentication', 'omt' ); ?></th>
                        <td>
                            <label for="omt_smtp_auth">
                                <input type="checkbox" id="omt_smtp_auth" name="omt_smtp_auth" value="1" <?php checked( $settings['auth'] ); ?> />
                                <?php esc_html_e( 'Log in with a username and password', 'omt' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="omt_smtp_user"><?php esc_html_e( 'Username', 'omt' ); ?></label></th>
                        <td><input type="text" id="omt_smtp_user" name="omt_smtp_user" value="<?php echo esc_attr( $settings['user'] ); ?>" class="regular-text" autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="omt_smtp_pass"><?php esc_html_e( 'Password', 'omt' ); ?></label></th>
                        <td>
                            <input type="password" id="omt_smtp_pass" name="omt_smtp_pass" value="" class="regular-text" autocomplete="new-password" placeholder="<?php echo $settings['pass'] ? esc_attr__( '(saved — never shown)', 'omt' ) : ''; ?>" />
                            <p class="description">
                                <?php esc_html_e( 'Leave empty to keep the saved password. Enter a single hyphen (-) to delete it. The value is written to the database once and never echoed back.', 'omt' ); ?>
                                <?php if ( omt_smtp_key_available() ) : ?>
                                    <br /><?php esc_html_e( 'Encrypted at rest with the OMT_SMTP_KEY defined in wp-config.php.', 'omt' ); ?>
                                <?php else : ?>
                                    <br /><strong><?php esc_html_e( 'Stored in plain text. Define OMT_SMTP_KEY in wp-config.php (32+ random characters) to encrypt it at rest.', 'omt' ); ?></strong>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Sender identity', 'omt' ); ?></th>
                        <td>
                            <label for="omt_smtp_from_email" class="screen-reader-text"><?php esc_html_e( 'From email address', 'omt' ); ?></label>
                            <input type="text" id="omt_smtp_from_email" name="omt_smtp_from_email" value="<?php echo esc_attr( $settings['from_email'] ); ?>" class="regular-text" placeholder="hello@onemoretree.org" />
                            <p class="description">
                                <?php esc_html_e( 'This address must be verified by your SMTP provider or mail will be rejected.', 'omt' ); ?>
                            </p>
                            <label for="omt_smtp_from_name" class="screen-reader-text"><?php esc_html_e( 'From name', 'omt' ); ?></label>
                            <input type="text" id="omt_smtp_from_name" name="omt_smtp_from_name" value="<?php echo esc_attr( $settings['from_name'] ); ?>" class="regular-text" placeholder="One More Tree" />
                            <p class="description">
                                <?php esc_html_e( 'Shown next to the address. Falls back to the site title.', 'omt' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php if ( $settings['updated'] ) : ?>
                    <p><em>
                        <?php
                        printf(
                            /* translators: 1: date, 2: time. */
                            esc_html__( 'Last saved: %1$s at %2$s.', 'omt' ),
                            esc_html( date_i18n( get_option( 'date_format' ), $settings['updated'] ) ),
                            esc_html( date_i18n( get_option( 'time_format' ), $settings['updated'] ) )
                        );
                        ?>
                    </em></p>
                <?php endif; ?>

                <?php submit_button( __( 'Save email settings', 'omt' ), 'primary', 'omt_smtp_save' ); ?>
            </form>

            <hr />
            <h2><?php esc_html_e( 'Send a test email', 'omt' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'omt_smtp_save', 'omt_smtp_nonce' ); ?>
                <p>
                    <label for="omt_smtp_test_to"><?php esc_html_e( 'Send to', 'omt' ); ?></label>
                    <input type="text" id="omt_smtp_test_to" name="omt_smtp_test_to" value="" class="regular-text" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
                </p>
                <p class="description"><?php esc_html_e( 'Leave empty to send to the site administrator address. On failure the real transport error is shown here.', 'omt' ); ?></p>
                <?php submit_button( __( 'Send test email', 'omt' ), 'secondary', 'omt_smtp_test' ); ?>
            </form>
        </div>
        <?php
    }
}
