<?php
/**
 * WordPress Admin Settings Pages for OneMoreTree
 *
 * Allows managing all website content directly inside wp-admin.
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register OneMoreTree Admin Menus
 */
function one_more_tree_add_admin_menu() {
    // Top-level menu
    add_menu_page(
        __('OneMoreTree Management', 'one-more-tree'),
        __('OneMoreTree', 'one-more-tree'),
        'manage_options',
        'omt-settings',
        'one_more_tree_render_general_settings',
        'dashicons-palmtree',
        25
    );

    // Submenu: General & Disclaimer
    add_submenu_page(
        'omt-settings',
        __('General & Disclaimer', 'one-more-tree'),
        __('General & Disclaimer', 'one-more-tree'),
        'manage_options',
        'omt-settings',
        'one_more_tree_render_general_settings'
    );

    // Submenu: Counters & Impact KPIs
    add_submenu_page(
        'omt-settings',
        __('Counters & KPIs', 'one-more-tree'),
        __('Counters & KPIs', 'one-more-tree'),
        'manage_options',
        'omt-counters',
        'one_more_tree_render_counters_settings'
    );

    // Submenu: Live Map & Projects
    add_submenu_page(
        'omt-settings',
        __('Live Map & Projects', 'one-more-tree'),
        __('Live Map & Projects', 'one-more-tree'),
        'manage_options',
        'omt-map-projects',
        'one_more_tree_render_map_projects_settings'
    );

    // Submenu: Team & Stories
    add_submenu_page(
        'omt-settings',
        __('Team & Stories', 'one-more-tree'),
        __('Team & Stories', 'one-more-tree'),
        'manage_options',
        'omt-team-stories',
        'one_more_tree_render_team_stories_settings'
    );

    // Submenu: FAQ Manager
    add_submenu_page(
        'omt-settings',
        __('FAQ Manager', 'one-more-tree'),
        __('FAQ Manager', 'one-more-tree'),
        'manage_options',
        'omt-faq',
        'one_more_tree_render_faq_settings'
    );

    // Submenu: Contact Inquiries
    add_submenu_page(
        'omt-settings',
        __('Contact Inquiries', 'one-more-tree'),
        __('Contact Inquiries', 'one-more-tree'),
        'manage_options',
        'omt-contact-submissions',
        'one_more_tree_render_contact_submissions'
    );

    // Submenu: Newsletter Subscribers
    add_submenu_page(
        'omt-settings',
        __('Newsletter Subscribers', 'one-more-tree'),
        __('Newsletter Subscribers', 'one-more-tree'),
        'manage_options',
        'omt-newsletter',
        'one_more_tree_render_newsletter_subscribers'
    );

    // Submenu: Launch Administrator Console (Links to /administrator)
    add_submenu_page(
        'omt-settings',
        __('Launch Console', 'one-more-tree'),
        __('🚀 Site Console', 'one-more-tree'),
        'manage_options',
        'omt-launch-console',
        'one_more_tree_render_launch_console'
    );
}
add_action('admin_menu', 'one_more_tree_add_admin_menu');

/**
 * Handle form saving in WP Admin
 */
function one_more_tree_handle_admin_post_save() {
    if (!isset($_POST['omt_admin_action']) || !current_user_can('manage_options')) {
        return;
    }

    check_admin_referer('omt_admin_save_nonce', 'omt_nonce');
    $action = sanitize_key($_POST['omt_admin_action']);

    if ($action === 'save_general') {
        $disclaimer = [
            'enabled' => isset($_POST['disclaimer_enabled']),
            'message' => sanitize_textarea_field($_POST['disclaimer_message'] ?? ''),
        ];
        update_option('omt_admin_disclaimer', $disclaimer);

        $social = [
            'instagram' => esc_url_raw($_POST['social_instagram'] ?? ''),
            'twitter'   => esc_url_raw($_POST['social_twitter'] ?? ''),
            'facebook'  => esc_url_raw($_POST['social_facebook'] ?? ''),
            'linkedin'  => esc_url_raw($_POST['social_linkedin'] ?? ''),
        ];
        update_option('omt_admin_social', $social);

        update_option('omt_contact_address', sanitize_text_field($_POST['contact_address'] ?? ''));
        update_option('omt_contact_email', sanitize_email($_POST['contact_email'] ?? ''));
        update_option('omt_contact_phone', sanitize_text_field($_POST['contact_phone'] ?? ''));

        wp_safe_redirect(add_query_arg(['page' => 'omt-settings', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }

    if ($action === 'save_counters') {
        $counters = [
            'treesTracked'      => intval($_POST['treesTracked'] ?? 1000000),
            'projectsActive'    => intval($_POST['projectsActive'] ?? 50),
            'volunteersEngaged' => intval($_POST['volunteersEngaged'] ?? 10000),
            'regionsCovered'    => intval($_POST['regionsCovered'] ?? 4),
        ];
        update_option('omt_admin_home_counters', $counters);

        $impact = [
            'goalTarget'  => intval($_POST['goalTarget'] ?? 1000000),
            'goalCurrent' => intval($_POST['goalCurrent'] ?? 847000),
            'kpis' => [
                ['label' => 'Regions Covered', 'value' => sanitize_text_field($_POST['kpi_1_val'] ?? '4'), 'sub' => sanitize_text_field($_POST['kpi_1_sub'] ?? 'Granada, Madrid, Valencia, Galicia')],
                ['label' => 'Projects Active', 'value' => sanitize_text_field($_POST['kpi_2_val'] ?? '12'), 'sub' => sanitize_text_field($_POST['kpi_2_sub'] ?? 'Active forest & urban sites')],
                ['label' => 'Tons CO2 Absorbed', 'value' => sanitize_text_field($_POST['kpi_3_val'] ?? '4,850'), 'sub' => sanitize_text_field($_POST['kpi_3_sub'] ?? 'Estimated lifetime absorption')],
                ['label' => 'Volunteers Engaged', 'value' => sanitize_text_field($_POST['kpi_4_val'] ?? '2,420'), 'sub' => sanitize_text_field($_POST['kpi_4_sub'] ?? 'Community participants')],
            ],
        ];
        update_option('omt_admin_impact_kpis', $impact);

        wp_safe_redirect(add_query_arg(['page' => 'omt-counters', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }

    if ($action === 'save_faq') {
        $raw_faqs = $_POST['faq_items'] ?? [];
        $faqs = [];
        if (is_array($raw_faqs)) {
            foreach ($raw_faqs as $item) {
                $q = sanitize_text_field($item['q'] ?? '');
                $a = sanitize_textarea_field($item['a'] ?? '');
                if (!empty($q)) {
                    $faqs[] = ['q' => $q, 'a' => $a];
                }
            }
        }
        update_option('omt_admin_faq', ['faqs' => $faqs]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-faq', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }

    if ($action === 'delete_contact') {
        $id = sanitize_text_field($_POST['submission_id'] ?? '');
        $subs = get_option('omt_admin_contact_submissions', []);
        $subs = array_filter($subs, function($s) use ($id) { return ($s['id'] ?? '') !== $id; });
        update_option('omt_admin_contact_submissions', array_values($subs));
        wp_safe_redirect(add_query_arg(['page' => 'omt-contact-submissions', 'deleted' => 'true'], admin_url('admin.php')));
        exit;
    }
}
add_action('admin_init', 'one_more_tree_handle_admin_post_save');

/**
 * 1. Render General & Disclaimer Settings
 */
function one_more_tree_render_general_settings() {
    $disclaimer = get_option('omt_admin_disclaimer', one_more_tree_get_default_admin_data('disclaimer'));
    $social = get_option('omt_admin_social', one_more_tree_get_default_admin_data('social'));
    $address = get_option('omt_contact_address', 'Calle Gran Vía 28, Madrid, Spain');
    $email = get_option('omt_contact_email', 'info@onemoretree.eu');
    $phone = get_option('omt_contact_phone', '+34 912 345 678');
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-palmtree" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — General Settings & Disclaimer
        </h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_general">

            <!-- Disclaimer Ribbon Box -->
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Top Disclaimer Ribbon</h2>
                <p class="description">Displays a prominent notification ribbon at the very top of all pages.</p>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Ribbon</th>
                        <td>
                            <label>
                                <input type="checkbox" name="disclaimer_enabled" value="1" <?php checked($disclaimer['enabled'] ?? true); ?>>
                                Show disclaimer ribbon on live website
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Disclaimer Text</th>
                        <td>
                            <textarea name="disclaimer_message" rows="3" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($disclaimer['message'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Contact Information Box -->
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Organization Contact Information</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Contact Email</th>
                        <td>
                            <input type="email" name="contact_email" value="<?php echo esc_attr($email); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Contact Phone</th>
                        <td>
                            <input type="text" name="contact_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Physical Address</th>
                        <td>
                            <input type="text" name="contact_address" value="<?php echo esc_attr($address); ?>" class="large-text" style="border-radius:8px;">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Social Media Box -->
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Social Media Channels</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Instagram URL</th>
                        <td><input type="url" name="social_instagram" value="<?php echo esc_attr($social['instagram'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Twitter / X URL</th>
                        <td><input type="url" name="social_twitter" value="<?php echo esc_attr($social['twitter'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Facebook URL</th>
                        <td><input type="url" name="social_facebook" value="<?php echo esc_attr($social['facebook'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">LinkedIn URL</th>
                        <td><input type="url" name="social_linkedin" value="<?php echo esc_attr($social['linkedin'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Settings" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

/**
 * 2. Render Counters & Impact KPIs Settings
 */
function one_more_tree_render_counters_settings() {
    $counters = get_option('omt_admin_home_counters', one_more_tree_get_default_admin_data('home_counters'));
    $impact = get_option('omt_admin_impact_kpis', one_more_tree_get_default_admin_data('impact_kpis'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-chart-bar" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — Live Counters & Impact KPIs
        </h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Counters updated successfully!</strong></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_counters">

            <!-- Homepage Live Counters -->
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Homepage Live Counters</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Trees Tracked</th>
                        <td><input type="number" name="treesTracked" value="<?php echo esc_attr($counters['treesTracked'] ?? 1000000); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Projects Active</th>
                        <td><input type="number" name="projectsActive" value="<?php echo esc_attr($counters['projectsActive'] ?? 50); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Volunteers Engaged</th>
                        <td><input type="number" name="volunteersEngaged" value="<?php echo esc_attr($counters['volunteersEngaged'] ?? 10000); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Regions Covered</th>
                        <td><input type="number" name="regionsCovered" value="<?php echo esc_attr($counters['regionsCovered'] ?? 4); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                </table>
            </div>

            <!-- Impact Page 2040 Goal & KPIs -->
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Impact Page Progress & Milestone</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">2040 Goal Target</th>
                        <td><input type="number" name="goalTarget" value="<?php echo esc_attr($impact['goalTarget'] ?? 1000000); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">Current Planted Count</th>
                        <td><input type="number" name="goalCurrent" value="<?php echo esc_attr($impact['goalCurrent'] ?? 847000); ?>" class="regular-text" style="border-radius:8px;"></td>
                    </tr>
                    <tr>
                        <th scope="row">KPI 1: Regions Covered</th>
                        <td>
                            <input type="text" name="kpi_1_val" value="<?php echo esc_attr($impact['kpis'][0]['value'] ?? '4'); ?>" class="small-text" style="border-radius:8px;">
                            <input type="text" name="kpi_1_sub" value="<?php echo esc_attr($impact['kpis'][0]['sub'] ?? 'Granada, Madrid, Valencia, Galicia'); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">KPI 2: Projects Active</th>
                        <td>
                            <input type="text" name="kpi_2_val" value="<?php echo esc_attr($impact['kpis'][1]['value'] ?? '12'); ?>" class="small-text" style="border-radius:8px;">
                            <input type="text" name="kpi_2_sub" value="<?php echo esc_attr($impact['kpis'][1]['sub'] ?? 'Active forest & urban sites'); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">KPI 3: Tons CO2 Absorbed</th>
                        <td>
                            <input type="text" name="kpi_3_val" value="<?php echo esc_attr($impact['kpis'][2]['value'] ?? '4,850'); ?>" class="small-text" style="border-radius:8px;">
                            <input type="text" name="kpi_3_sub" value="<?php echo esc_attr($impact['kpis'][2]['sub'] ?? 'Estimated lifetime absorption'); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">KPI 4: Volunteers Engaged</th>
                        <td>
                            <input type="text" name="kpi_4_val" value="<?php echo esc_attr($impact['kpis'][3]['value'] ?? '2,420'); ?>" class="small-text" style="border-radius:8px;">
                            <input type="text" name="kpi_4_sub" value="<?php echo esc_attr($impact['kpis'][3]['sub'] ?? 'Community participants'); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Counters" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

/**
 * 3. Render Live Map & Projects Settings
 */
function one_more_tree_render_map_projects_settings() {
    $live_map = get_option('omt_admin_live_map', one_more_tree_get_default_admin_data('live_map'));
    $projects = get_option('omt_admin_projects', one_more_tree_get_default_admin_data('projects'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-location-alt" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — Live Map & Projects
        </h1>

        <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
            <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Active Reforestation Locations (<?php echo count($live_map['locations'] ?? []); ?>)</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="font-weight:bold;">Project Name</th>
                        <th style="font-weight:bold;">Region</th>
                        <th style="font-weight:bold;">Coordinates (X / Y)</th>
                        <th style="font-weight:bold;">Trees Planted</th>
                        <th style="font-weight:bold;">Volunteers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($live_map['locations'] ?? []) as $loc): ?>
                        <tr>
                            <td><strong><?php echo esc_html($loc['name']); ?></strong></td>
                            <td><?php echo esc_html($loc['region']); ?></td>
                            <td><code><?php echo esc_html($loc['coords']['x'] . ' / ' . $loc['coords']['y']); ?></code></td>
                            <td><?php echo esc_html(number_format($loc['treesPlanted'])); ?></td>
                            <td><?php echo esc_html(number_format($loc['volunteers'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top:15px;">
                <a href="<?php echo esc_url(home_url('/administrator')); ?>" target="_blank" class="button button-secondary">
                    Open Admin Console for Visual Map Editor & Slideshow Uploads →
                </a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * 4. Render Team & Stories Settings
 */
function one_more_tree_render_team_stories_settings() {
    $team = get_option('omt_admin_team', one_more_tree_get_default_admin_data('team'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-groups" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — Team & Stories
        </h1>

        <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
            <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Team Members (<?php echo count($team['members'] ?? []); ?>)</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="font-weight:bold;">Name</th>
                        <th style="font-weight:bold;">Role</th>
                        <th style="font-weight:bold;">Experience & Focus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($team['members'] ?? []) as $m): ?>
                        <tr>
                            <td><strong><?php echo esc_html($m['name']); ?></strong></td>
                            <td><?php echo esc_html($m['role']); ?></td>
                            <td><?php echo esc_html($m['experience']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * 5. Render FAQ Settings
 */
function one_more_tree_render_faq_settings() {
    $faq_data = get_option('omt_admin_faq', one_more_tree_get_default_admin_data('faq'));
    $faqs = $faq_data['faqs'] ?? [];
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-editor-help" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — FAQ Manager
        </h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>FAQs updated successfully!</strong></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_faq">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Questions & Answers</h2>
                <div id="faq-list">
                    <?php foreach ($faqs as $i => $faq): ?>
                        <div class="faq-item-row" style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:10px;margin-bottom:14px;">
                            <p style="margin:0 0 6px 0;"><strong>Question #<?php echo $i + 1; ?>:</strong></p>
                            <input type="text" name="faq_items[<?php echo $i; ?>][q]" value="<?php echo esc_attr($faq['q']); ?>" class="large-text" style="border-radius:8px;margin-bottom:8px;">
                            <p style="margin:0 0 6px 0;"><strong>Answer:</strong></p>
                            <textarea name="faq_items[<?php echo $i; ?>][a]" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($faq['a']); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save FAQ Items" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

/**
 * 6. Render Contact Submissions
 */
function one_more_tree_render_contact_submissions() {
    $submissions = get_option('omt_admin_contact_submissions', []);
    ?>
    <div class="wrap" style="max-width: 1100px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-email" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Contact Inquiries & Volunteer Messages (<?php echo count($submissions); ?>)
        </h1>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Inquiry deleted.</strong></p></div>
        <?php endif; ?>

        <?php if (empty($submissions)): ?>
            <div class="postbox" style="padding: 30px; text-align: center; border-radius: 12px;">
                <p style="color:#64748b;font-size:16px;">No contact inquiries received yet. Submissions sent via the contact and volunteer forms will appear here automatically.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped" style="border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="font-weight:bold;width:140px;">Date</th>
                        <th style="font-weight:bold;width:160px;">Sender</th>
                        <th style="font-weight:bold;width:200px;">Email</th>
                        <th style="font-weight:bold;">Message / Interest</th>
                        <th style="font-weight:bold;width:80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $sub): ?>
                        <tr>
                            <td><?php echo esc_html($sub['date'] ?? ''); ?></td>
                            <td><strong><?php echo esc_html($sub['name'] ?? ''); ?></strong></td>
                            <td><a href="mailto:<?php echo esc_attr($sub['email'] ?? ''); ?>"><?php echo esc_html($sub['email'] ?? ''); ?></a></td>
                            <td><?php echo nl2br(esc_html($sub['message'] ?? '')); ?></td>
                            <td>
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                                    <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
                                    <input type="hidden" name="omt_admin_action" value="delete_contact">
                                    <input type="hidden" name="submission_id" value="<?php echo esc_attr($sub['id'] ?? ''); ?>">
                                    <input type="submit" class="button button-small button-link-delete" value="Delete">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 7. Render Newsletter Subscribers
 */
function one_more_tree_render_newsletter_subscribers() {
    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    ?>
    <div class="wrap" style="max-width: 900px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-email-alt" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Newsletter Subscribers (<?php echo count($subscribers); ?>)
        </h1>

        <?php if (empty($subscribers)): ?>
            <div class="postbox" style="padding: 30px; text-align: center; border-radius: 12px;">
                <p style="color:#64748b;font-size:16px;">No subscribers yet. People who subscribe via the newsletter forms will appear here.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped" style="border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="font-weight:bold;width:60px;">#</th>
                        <th style="font-weight:bold;">Subscriber Email</th>
                        <th style="font-weight:bold;width:200px;">Subscribed Date</th>
                        <th style="font-weight:bold;width:120px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $idx => $s): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo esc_html($s['email'] ?? ''); ?></strong></td>
                            <td><?php echo esc_html($s['date'] ?? ''); ?></td>
                            <td><span style="color:#15803d;font-weight:600;">Active</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 8. Render Launch Console Link Page
 */
function one_more_tree_render_launch_console() {
    ?>
    <div class="wrap" style="max-width: 700px; margin-top: 40px;">
        <div class="postbox" style="padding: 40px; text-align: center; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
            <div style="width:64px;height:64px;border-radius:16px;background:#f0fdf4;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <span class="dashicons dashicons-dashboard" style="font-size:36px;width:36px;height:36px;color:#15803d;"></span>
            </div>
            <h2 style="font-size:24px;margin-bottom:12px;">Launch Next.js Admin Console</h2>
            <p style="color:#64748b;font-size:15px;line-height:1.6;margin-bottom:24px;">
                You can administer the entire site using the Next.js Administrator Console at <code>/administrator</code> with live preview cards, interactive map editing, and drag & drop sorting.
            </p>
            <a href="<?php echo esc_url(home_url('/administrator')); ?>" target="_blank" class="button button-primary button-hero" style="background:#15803d;border-color:#15803d;border-radius:12px;padding:6px 28px;font-size:16px;">
                Open Administrator Console →
            </a>
        </div>
    </div>
    <?php
}
