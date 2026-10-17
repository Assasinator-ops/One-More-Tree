<?php
/**
 * Plugin Name: One More Tree — Contacts
 * Description: Dedicated WP-admin section for Contact-us messages. Each submission stored by the forms plugin is shown here with read/unread state, detail view, and archive, so the owner can work through enquiries in one place instead of the generic posts list.
 * Version: 1.0.0
 * Author: One More Tree
 *
 * Loaded as a must-use plugin so it cannot be deactivated by accident.
 */

defined( 'ABSPATH' ) || exit;

/* ============================================================================
 * 1. Admin menu
 * ========================================================================= */

add_action( 'admin_menu', 'omt_contacts_admin_menu', 20 );
function omt_contacts_admin_menu() {
    add_menu_page(
        __( 'Contact messages', 'omt' ),
        __( 'Contact messages', 'omt' ),
        'edit_posts',
        'omt-contacts',
        'omt_contacts_admin_page',
        'dashicons-email',
        39
    );
}

/* ============================================================================
 * 2. Admin page
 * ========================================================================= */

function omt_contacts_admin_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have permission to view this page.', 'omt' ) );
    }

    // Handle posted actions.
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        // Archive / restore a single message.
        if ( isset( $_POST['omt_contacts_action'] ) && $_POST['omt_contacts_action'] === 'toggle_archive' ) {
            if ( check_admin_referer( 'omt_contacts_toggle_archive', 'omt_contacts_toggle_archive_nonce' ) ) {
                $id = isset( $_POST['omt_contacts_id'] ) ? absint( $_POST['omt_contacts_id'] ) : 0;
                if ( $id ) {
                    omt_contacts_toggle_archive( $id );
                }
            }
        }

        // Mark read.
        if ( isset( $_POST['omt_contacts_action'] ) && $_POST['omt_contacts_action'] === 'mark_read' ) {
            if ( check_admin_referer( 'omt_contacts_mark_read', 'omt_contacts_mark_read_nonce' ) ) {
                $id = isset( $_POST['omt_contacts_id'] ) ? absint( $_POST['omt_contacts_id'] ) : 0;
                if ( $id ) {
                    omt_contacts_mark_read( $id );
                }
            }
        }

        // Mark unread.
        if ( isset( $_POST['omt_contacts_action'] ) && $_POST['omt_contacts_action'] === 'mark_unread' ) {
            if ( check_admin_referer( 'omt_contacts_mark_unread', 'omt_contacts_mark_unread_nonce' ) ) {
                $id = isset( $_POST['omt_contacts_id'] ) ? absint( $_POST['omt_contacts_id'] ) : 0;
                if ( $id ) {
                    omt_contacts_mark_unread( $id );
                }
            }
        }

        // Bulk actions.
        if ( isset( $_POST['omt_contacts_bulk'] ) && $_POST['omt_contacts_bulk'] === '1' ) {
            if ( check_admin_referer( 'omt_contacts_bulk_nonce', 'omt_contacts_bulk_nonce' ) ) {
                $ids      = isset( $_POST['omt_contacts_ids'] ) ? (array) $_POST['omt_contacts_ids'] : array();
                $bulk_act = isset( $_POST['omt_contacts_bulk_action'] ) ? sanitize_key( wp_unslash( $_POST['omt_contacts_bulk_action'] ) ) : 'mark_read';
                $ids      = array_map( 'absint', $ids );
                $ids      = array_filter( $ids );
                if ( ! empty( $ids ) ) {
                    omt_contacts_bulk( $ids, $bulk_act );
                }
            }
        }

        // Redirect back to keep the POST from being re-submitted on refresh.
        wp_safe_redirect( remove_query_arg( array( 'omt_contacts_status', 'omt_contacts_id' ), admin_url( 'admin.php?page=omt-contacts' ) ) );
        exit;
    }

    $status   = isset( $_GET['omt_contacts_status'] ) ? sanitize_key( wp_unslash( $_GET['omt_contacts_status'] ) ) : 'all';
    $view_id  = isset( $_GET['omt_contacts_id'] ) ? absint( $_GET['omt_contacts_id'] ) : 0;

    $query = new WP_Query( array(
        'post_type'      => 'omt_lead',
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(),
    ) );

    if ( 'unread' === $status ) {
        $query->query_vars['meta_query'][] = array(
            'key'     => '_omt_contact_read',
            'value'   => '1',
            'compare' => '!=',
            'type'    => 'CHAR',
        );
    } elseif ( 'read' === $status ) {
        $query->query_vars['meta_query'][] = array(
            'key'     => '_omt_contact_read',
            'value'   => '1',
            'compare' => '=',
            'type'    => 'CHAR',
        );
    } elseif ( 'archived' === $status ) {
        $query->query_vars['meta_query'][] = array(
            'key'     => '_omt_contact_archived',
            'value'   => '1',
            'compare' => '=',
            'type'    => 'CHAR',
        );
    }

    // Re-run with the filtered vars.
    $query->query_vars = $query->query_vars;
    $query             = new WP_Query( $query->query_vars );

    // Detail view.
    $detail = null;
    if ( $view_id ) {
        $detail = get_post( $view_id );
        if ( ! $detail || $detail->post_type !== 'omt_lead' ) {
            $view_id = 0;
        }
    }

    // Counts for the summary.
    $counts = omt_contacts_counts();

    // HTML.
    ?>
<div class="wrap omtt-contacts">
    <h1><?php esc_html_e( 'Contact messages', 'omt' ); ?></h1>

    <?php if ( ! empty( $_GET['omt_contacts_status_message'] ) ) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html( wp_unslash( $_GET['omt_contacts_status_message'] ) ); ?></p></div>
    <?php endif; ?>

    <div class="omtt-card">
        <h2 class="omtt-card__title"><?php esc_html_e( 'Messages', 'omt' ); ?></h2>

        <?php if ( $detail ) : ?>
            <div class="omtt-detail">
                <?php
                $read    = get_post_meta( $detail->ID, '_omt_contact_read', true );
                $archived= get_post_meta( $detail->ID, '_omt_contact_archived', true );
                $email   = get_post_meta( $detail->ID, '_omt_email', true );
                $name    = get_post_meta( $detail->ID, '_omt_name', true );
                $topic   = get_post_meta( $detail->ID, '_omt_topic', true );
                $message = get_post_meta( $detail->ID, '_omt_message', true );
                $source  = get_post_meta( $detail->ID, '_omt_source', true );
                $ua      = get_post_meta( $detail->ID, '_omt_ua', true );
                ?>
                <div class="omtt-detail__header">
                    <div>
                        <h3><?php echo esc_html( get_the_title( $detail ) ); ?></h3>
                        <p class="omtt-meta">
                            <?php printf(
                                '%s <strong>%s</strong> · %s <strong>%s</strong>',
                                esc_html__( 'From', 'omt' ),
                                esc_html( $name ?: $email ),
                                esc_html__( 'Email', 'omt' ),
                                esc_html( $email )
                            ); ?>
                        </p>
                        <?php if ( $topic ) : ?>
                            <p class="omtt-meta"><?php printf( '%s: %s', esc_html__( 'Topic', 'omt' ), esc_html( $topic ) ); ?></p>
                        <?php endif; ?>
                        <p class="omtt-meta"><?php printf( '%s: %s', esc_html__( 'Submitted', 'omt' ), esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $detail->post_date ) ) ); ?></p>
                        <?php if ( $source ) : ?>
                            <p class="omtt-meta"><?php printf( '%s: %s', esc_html__( 'Page', 'omt' ), esc_html( $source ) ); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="omtt-detail__actions">
                        <?php if ( ! $archived ) : ?>
                            <form method="post" class="omtt-form--inline">
                                <?php wp_nonce_field( 'omt_contacts_toggle_archive', 'omt_contacts_toggle_archive_nonce' ); ?>
                                <input type="hidden" name="omt_contacts_action" value="toggle_archive" />
                                <input type="hidden" name="omt_contacts_id" value="<?php echo esc_attr( $detail->ID ); ?>" />
                                <input type="submit" value="<?php esc_attr_e( 'Archive', 'omt' ); ?>" class="button" />
                            </form>
                        <?php else : ?>
                            <form method="post" class="omtt-form--inline">
                                <?php wp_nonce_field( 'omt_contacts_toggle_archive', 'omt_contacts_toggle_archive_nonce' ); ?>
                                <input type="hidden" name="omt_contacts_action" value="toggle_archive" />
                                <input type="hidden" name="omt_contacts_id" value="<?php echo esc_attr( $detail->ID ); ?>" />
                                <input type="submit" value="<?php esc_attr_e( 'Restore', 'omt' ); ?>" class="button" />
                            </form>
                        <?php endif; ?>
                        <?php if ( ! $read ) : ?>
                            <form method="post" class="omtt-form--inline">
                                <?php wp_nonce_field( 'omt_contacts_mark_read', 'omt_contacts_mark_read_nonce' ); ?>
                                <input type="hidden" name="omt_contacts_action" value="mark_read" />
                                <input type="hidden" name="omt_contacts_id" value="<?php echo esc_attr( $detail->ID ); ?>" />
                                <input type="submit" value="<?php esc_attr_e( 'Mark as read', 'omt' ); ?>" class="button" />
                            </form>
                        <?php else : ?>
                            <form method="post" class="omtt-form--inline">
                                <?php wp_nonce_field( 'omt_contacts_mark_unread', 'omt_contacts_mark_unread_nonce' ); ?>
                                <input type="hidden" name="omt_contacts_action" value="mark_unread" />
                                <input type="hidden" name="omt_contacts_id" value="<?php echo esc_attr( $detail->ID ); ?>" />
                                <input type="submit" value="<?php esc_attr_e( 'Mark as unread', 'omt' ); ?>" class="button" />
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="omtt-detail__body">
                    <?php if ( $message ) : ?>
                        <h4><?php esc_html_e( 'Message', 'omt' ); ?></h4>
                        <p><?php echo esc_html( nl2br( $message ) ); ?></p>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No message body.', 'omt' ); ?></p>
                    <?php endif; ?>

                    <?php if ( $ua ) : ?>
                        <h4><?php esc_html_e( 'Sent from', 'omt' ); ?></h4>
                        <p><?php echo esc_html( $ua ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="omtt-detail__back">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=omt-contacts' ) ); ?>" class="button"><?php esc_html_e( '← Back to list', 'omt' ); ?></a>
                </div>
            </div>
        <?php else : ?>
            <?php if ( $query->have_posts() ) : ?>
                <form method="post">
                    <?php wp_nonce_field( 'omt_contacts_bulk_nonce', 'omt_contacts_bulk_nonce' ); ?>
                    <input type="hidden" name="omt_contacts_bulk" value="1" />

                    <div class="omtt-toolbar">
                        <div class="omtt-toolbar__left">
                            <label>
                                <input type="checkbox" id="omt_contacts_select_all" />
                                <?php esc_html_e( 'Select all', 'omt' ); ?>
                            </label>
                        </div>
                        <div class="omtt-toolbar__right">
                            <select name="omt_contacts_bulk_action" class="omtt-select">
                                <option value="mark_read"><?php esc_html_e( 'Mark as read', 'omt' ); ?></option>
                                <option value="mark_unread"><?php esc_html_e( 'Mark as unread', 'omt' ); ?></option>
                                <option value="archive"><?php esc_html_e( 'Archive selected', 'omt' ); ?></option>
                            </select>
                            <input type="submit" value="<?php esc_attr_e( 'Apply', 'omt' ); ?>" class="button" />
                        </div>
                    </div>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width:40px"><input type="checkbox" id="omt_contacts_select_all_head" /></th>
                                <th><?php esc_html_e( 'Time', 'omt' ); ?></th>
                                <th><?php esc_html_e( 'Name', 'omt' ); ?></th>
                                <th><?php esc_html_e( 'Email', 'omt' ); ?></th>
                                <th><?php esc_html_e( 'Topic', 'omt' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'omt' ); ?></th>
                                <th style="width:140px"><?php esc_html_e( 'Actions', 'omt' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                            <?php
                            $read     = get_post_meta( get_the_ID(), '_omt_contact_read', true ) === '1';
                            $archived = get_post_meta( get_the_ID(), '_omt_contact_archived', true ) === '1';
                            $email    = get_post_meta( get_the_ID(), '_omt_email', true );
                            $name     = get_post_meta( get_the_ID(), '_omt_name', true );
                            $topic    = get_post_meta( get_the_ID(), '_omt_topic', true );
                            ?>
                            <tr class="<?php echo $archived ? 'omtt-row--archived' : ''; ?> <?php echo $read ? '' : 'omtt-row--unread'; ?>">
                                <td><input type="checkbox" name="omt_contacts_ids[]" value="<?php echo esc_attr( get_the_ID() ); ?>" /></td>
                                <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), get_the_date( 'U' ) ) ); ?></td>
                                <td><?php echo esc_html( $name ?: '—' ); ?></td>
                                <td><?php echo esc_html( $email ); ?></td>
                                <td><?php echo esc_html( $topic ?: '—' ); ?></td>
                                <td>
                                    <?php if ( $archived ) : ?>
                                        <span class="omtt-badge omtt-badge--archived"><?php esc_html_e( 'Archived', 'omt' ); ?></span>
                                    <?php elseif ( ! $read ) : ?>
                                        <span class="omtt-badge omtt-badge--unread"><?php esc_html_e( 'Unread', 'omt' ); ?></span>
                                    <?php else : ?>
                                        <span class="omtt-badge omtt-badge--read"><?php esc_html_e( 'Read', 'omt' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=omt-contacts&omt_contacts_id=' . get_the_ID() ) ); ?>" class="button"><?php esc_html_e( 'View', 'omt' ); ?></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            <?php else : ?>
                <p><?php esc_html_e( 'No contact messages found.', 'omt' ); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ( ! $detail ) : ?>
        <div class="omtt-card omtt-card--summary">
            <h2 class="omtt-card__title"><?php esc_html_e( 'Summary', 'omt' ); ?></h2>
            <div class="omtt-summary-grid">
                <div class="omtt-summary-item">
                    <div class="omtt-summary-value"><?php echo number_format_i18n( $counts['total'] ); ?></div>
                    <div class="omtt-summary-label"><?php esc_html_e( 'Total messages', 'omt' ); ?></div>
                </div>
                <div class="omtt-summary-item">
                    <div class="omtt-summary-value omtt-summary-value--unread"><?php echo number_format_i18n( $counts['unread'] ); ?></div>
                    <div class="omtt-summary-label"><?php esc_html_e( 'Unread', 'omt' ); ?></div>
                </div>
                <div class="omtt-summary-item">
                    <div class="omtt-summary-value"><?php echo number_format_i18n( $counts['read'] ); ?></div>
                    <div class="omtt-summary-label"><?php esc_html_e( 'Read', 'omt' ); ?></div>
                </div>
                <div class="omtt-summary-item">
                    <div class="omtt-summary-value"><?php echo number_format_i18n( $counts['archived'] ); ?></div>
                    <div class="omtt-summary-label"><?php esc_html_e( 'Archived', 'omt' ); ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
    <?php
}

/* ============================================================================
 * 3. Counts
 * ========================================================================= */

function omt_contacts_counts() {
    $total   = count( get_posts( array( 'post_type' => 'omt_lead', 'post_status' => 'publish', 'numberposts' => -1 ) ) );
    $unread  = (int) get_option( 'omt_contacts_unread_count', 0 );
    $read    = $total - $unread;
    $archived = (int) get_option( 'omt_contacts_archived_count', 0 );
    return array(
        'total'     => $total,
        'unread'    => $unread,
        'read'      => max( 0, $read ),
        'archived'  => $archived,
    );
}

/* ============================================================================
 * 4. State mutations
 * ========================================================================= */

add_action( 'omt_lead_stored', 'omt_contacts_on_lead_stored', 10, 2 );
function omt_contacts_on_lead_stored( $lead_id, $kind ) {
    if ( $kind !== 'contact' ) {
        return;
    }
    // Count unread for the summary (we keep a cache option because counting every
    // page load is slow when the list grows).
    $unread = (int) get_option( 'omt_contacts_unread_count', 0 );
    update_option( 'omt_contacts_unread_count', $unread + 1, true );
}

function omt_contacts_mark_read( $id ) {
    update_post_meta( $id, '_omt_contact_read', '1' );
    // Decrement unread count.
    $unread = (int) get_option( 'omt_contacts_unread_count', 0 );
    if ( $unread > 0 ) {
        update_option( 'omt_contacts_unread_count', $unread - 1, true );
    }
    wp_safe_redirect( remove_query_arg( array( 'omt_contacts_status', 'omt_contacts_id' ), admin_url( 'admin.php?page=omt-contacts' ) ) );
    exit;
}

function omt_contacts_mark_unread( $id ) {
    delete_post_meta( $id, '_omt_contact_read' );
    $unread = (int) get_option( 'omt_contacts_unread_count', 0 );
    update_option( 'omt_contacts_unread_count', $unread + 1, true );
    wp_safe_redirect( remove_query_arg( array( 'omt_contacts_status', 'omt_contacts_id' ), admin_url( 'admin.php?page=omt-contacts' ) ) );
    exit;
}

function omt_contacts_toggle_archive( $id ) {
    $archived = get_post_meta( $id, '_omt_contact_archived', true ) === '1';
    if ( $archived ) {
        delete_post_meta( $id, '_omt_contact_archived' );
        $archive_count = (int) get_option( 'omt_contacts_archived_count', 0 );
        if ( $archive_count > 0 ) {
            update_option( 'omt_contacts_archived_count', $archive_count - 1, true );
        }
        $message = __( 'Message restored.', 'omt' );
    } else {
        update_post_meta( $id, '_omt_contact_archived', '1' );
        update_option( 'omt_contacts_archived_count', ( (int) get_option( 'omt_contacts_archived_count', 0 ) ) + 1, true );
        $message = __( 'Message archived.', 'omt' );
    }
    wp_safe_redirect( add_query_arg( 'omt_contacts_status_message', rawurlencode( $message ), admin_url( 'admin.php?page=omt-contacts' ) ) );
    exit;
}

function omt_contacts_bulk( $ids, $action ) {
    $ok = 0;
    foreach ( $ids as $id ) {
        switch ( $action ) {
            case 'mark_read':
                update_post_meta( $id, '_omt_contact_read', '1' );
                break;
            case 'mark_unread':
                delete_post_meta( $id, '_omt_contact_read' );
                break;
            case 'archive':
                update_post_meta( $id, '_omt_contact_archived', '1' );
                update_option( 'omt_contacts_archived_count', ( (int) get_option( 'omt_contacts_archived_count', 0 ) ) + 1, true );
                break;
        }
        $ok++;
    }
    if ( $action === 'mark_read' ) {
        $unread = (int) get_option( 'omt_contacts_unread_count', 0 );
        update_option( 'omt_contacts_unread_count', max( 0, $unread - $ok ), true );
    }
    if ( $action === 'mark_unread' ) {
        $unread = (int) get_option( 'omt_contacts_unread_count', 0 );
        update_option( 'omt_contacts_unread_count', $unread + $ok, true );
    }
    wp_safe_redirect( add_query_arg( 'omt_contacts_status_message', rawurlencode( sprintf( _n( '%d message updated.', '%d messages updated.', $ok, 'omt' ), $ok ) ), admin_url( 'admin.php?page=omt-contacts' ) ) );
    exit;
}

/* ============================================================================
 * 5. Admin CSS
 * ========================================================================= */

add_action( 'admin_head', 'omt_contacts_admin_styles' );
function omt_contacts_admin_styles() {
    $css = '
.omtt-contacts .omtt-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 20px 24px;
    margin-top: 20px;
}
.omtt-contacts .omtt-card--summary {
    margin-top: 16px;
}
.omtt-contacts .omtt-card__title {
    margin: 0 0 12px;
    font-size: 1.1em;
}
.omtt-contacts .omtt-summary-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 16px 32px;
    margin-top: 8px;
}
.omtt-contacts .omtt-summary-item {
    flex: 0 0 120px;
}
.omtt-contacts .omtt-summary-value {
    font-size: 1.6em;
    font-weight: 600;
    line-height: 1.2;
    color: #1d2327;
}
.omtt-contacts .omtt-summary-value--unread {
    color: #c00;
}
.omtt-contacts .omtt-summary-label {
    font-size: 0.85em;
    color: #646970;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.omtt-contacts .omtt-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #c3c4c7;
}
.omtt-contacts .omtt-toolbar__left label {
    font-size: 0.9em;
    color: #444;
    cursor: pointer;
}
.omtt-contacts .omtt-toolbar__right {
    display: flex;
    gap: 8px;
    align-items: center;
}
.omtt-contacts .omtt-select {
    padding: 4px 8px;
    font-size: 0.9em;
    border: 1px solid #c3c4c7;
    border-radius: 2px;
    background: #fff;
}
.omtt-contacts .omtt-badge {
    display: inline-block;
    padding: 2px 8px;
    font-size: 0.75em;
    font-weight: 600;
    line-height: 1.4;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.omtt-contacts .omtt-badge--unread {
    background: #c00;
    color: #fff;
}
.omtt-contacts .omtt-badge--read {
    background: #d3efe3;
    color: #0a5c34;
}
.omtt-contacts .omtt-badge--archived {
    background: #f0f0f1;
    color: #555;
}
.omtt-contacts .omtt-row--unread td:first-child {
    border-left: 3px solid #c00;
}
.omtt-contacts .omtt-row--read td:first-child {
    border-left: 3px solid transparent;
}
.omtt-contacts .omtt-row--archived {
    opacity: 0.6;
}
.omtt-contacts .omtt-form--inline {
    display: inline;
    margin: 0;
}
.omtt-contacts .omtt-detail__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #c3c4c7;
}
.omtt-contacts .omtt-detail__header h3 {
    margin: 0 0 4px;
    font-size: 1.1em;
}
.omtt-contacts .omtt-meta {
    margin: 2px 0;
    font-size: 0.9em;
    color: #555;
}
.omtt-contacts .omtt-detail__actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-shrink: 0;
}
.omtt-contacts .omtt-detail__body h4 {
    margin: 16px 0 4px;
    font-size: 0.95em;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #555;
}
.omtt-contacts .omtt-detail__back {
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid #c3c4c7;
    text-align: left;
}
.omtt-contacts .omtt-detail__back .button {
    display: inline-block;
}
';
    echo '<style>' . $css . '</style>';
}
