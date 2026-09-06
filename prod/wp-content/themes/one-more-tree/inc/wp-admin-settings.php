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

if (!function_exists('one_more_tree_get_default_admin_data')) {
    function one_more_tree_get_default_admin_data($key) {
        return null;
    }
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

    // Submenus
    add_submenu_page('omt-settings', __('General & Branding', 'one-more-tree'), __('General & Branding', 'one-more-tree'), 'manage_options', 'omt-settings', 'one_more_tree_render_general_settings');
    add_submenu_page('omt-settings', __('Homepage Content', 'one-more-tree'), __('Homepage Content', 'one-more-tree'), 'manage_options', 'omt-homepage', 'one_more_tree_render_homepage_settings');
    add_submenu_page('omt-settings', __('About & Team', 'one-more-tree'), __('About & Team', 'one-more-tree'), 'manage_options', 'omt-about', 'one_more_tree_render_about_settings');
    add_submenu_page('omt-settings', __('Impact & Stories', 'one-more-tree'), __('Impact & Stories', 'one-more-tree'), 'manage_options', 'omt-impact', 'one_more_tree_render_impact_settings');
    add_submenu_page('omt-settings', __('Counters & KPIs', 'one-more-tree'), __('Counters & KPIs', 'one-more-tree'), 'manage_options', 'omt-counters', 'one_more_tree_render_counters_settings');
    add_submenu_page('omt-settings', __('Live Map & Projects', 'one-more-tree'), __('Live Map & Projects', 'one-more-tree'), 'manage_options', 'omt-map-projects', 'one_more_tree_render_map_projects_settings');
    add_submenu_page('omt-settings', __('Team & Stories', 'one-more-tree'), __('Team & Stories (View)', 'one-more-tree'), 'manage_options', 'omt-team-stories', 'one_more_tree_render_team_stories_settings');
    add_submenu_page('omt-settings', __('FAQ Manager', 'one-more-tree'), __('FAQ Manager', 'one-more-tree'), 'manage_options', 'omt-faq', 'one_more_tree_render_faq_settings');
    add_submenu_page('omt-settings', __('Donation Tiers', 'one-more-tree'), __('Donation Tiers', 'one-more-tree'), 'manage_options', 'omt-donate', 'one_more_tree_render_donate_settings');
    add_submenu_page('omt-settings', __('Volunteer Page', 'one-more-tree'), __('Volunteer Page', 'one-more-tree'), 'manage_options', 'omt-volunteer-page', 'one_more_tree_render_volunteer_page_settings');
    add_submenu_page('omt-settings', __('Contact Page', 'one-more-tree'), __('Contact Page', 'one-more-tree'), 'manage_options', 'omt-contact-page', 'one_more_tree_render_contact_page_settings');
    add_submenu_page('omt-settings', __('Contact Inquiries', 'one-more-tree'), __('Contact Inquiries', 'one-more-tree'), 'manage_options', 'omt-contact-submissions', 'one_more_tree_render_contact_submissions');
    add_submenu_page('omt-settings', __('Newsletter Subscribers', 'one-more-tree'), __('Newsletter Subscribers', 'one-more-tree'), 'manage_options', 'omt-newsletter', 'one_more_tree_render_newsletter_subscribers');
    add_submenu_page('omt-settings', __('Launch Console', 'one-more-tree'), __('🚀 Site Console', 'one-more-tree'), 'manage_options', 'omt-launch-console', 'one_more_tree_render_launch_console');
}
add_action('admin_menu', 'one_more_tree_add_admin_menu');

/**
 * Handle form saving in WP Admin
 */
function one_more_tree_handle_admin_post_save() {
    if (!isset($_POST['omt_admin_action']) || !current_user_can('manage_options')) {
        return;
    }

    $action = sanitize_key($_POST['omt_admin_action']);
    
    if ($action === 'export_newsletter_csv') {
        $subscribers = get_option('omt_admin_newsletter_subscribers', []);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Email', 'Date']);
        foreach ($subscribers as $row) {
            fputcsv($output, [$row['email'], $row['date']]);
        }
        fclose($output);
        exit;
    }

    check_admin_referer('omt_admin_save_nonce', 'omt_nonce');

    if ($action === 'save_branding' || $action === 'save_general') {
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

        update_option('omt_admin_site_identity', [
            'site_title' => sanitize_text_field($_POST['site_title'] ?? ''),
            'tagline' => sanitize_text_field($_POST['site_tagline'] ?? ''),
            'logo_url' => esc_url_raw($_POST['logo_url'] ?? ''),
            'favicon_url' => esc_url_raw($_POST['favicon_url'] ?? ''),
        ]);

        update_option('omt_admin_nav_labels', [
            'home' => sanitize_text_field($_POST['nav_home'] ?? ''),
            'about' => sanitize_text_field($_POST['nav_about'] ?? ''),
            'projects' => sanitize_text_field($_POST['nav_projects'] ?? ''),
            'impact' => sanitize_text_field($_POST['nav_impact'] ?? ''),
            'faq' => sanitize_text_field($_POST['nav_faq'] ?? ''),
            'get_involved' => sanitize_text_field($_POST['nav_get_involved'] ?? ''),
            'volunteer' => sanitize_text_field($_POST['nav_volunteer'] ?? ''),
            'contact' => sanitize_text_field($_POST['nav_contact'] ?? ''),
            'donate' => sanitize_text_field($_POST['nav_donate'] ?? ''),
            'cta_text' => sanitize_text_field($_POST['nav_cta_text'] ?? ''),
            'cta_url' => esc_url_raw($_POST['nav_cta_url'] ?? ''),
        ]);

        $policy_links = [];
        if (isset($_POST['policy_links']) && is_array($_POST['policy_links'])) {
            foreach ($_POST['policy_links'] as $pl) {
                if (!empty($pl['label'])) {
                    $policy_links[] = ['label' => sanitize_text_field($pl['label']), 'url' => esc_url_raw($pl['url'])];
                }
            }
        }
        update_option('omt_admin_footer', [
            'tagline' => sanitize_text_field($_POST['footer_tagline'] ?? ''),
            'copyright' => sanitize_text_field($_POST['footer_copyright'] ?? ''),
            'policy_links' => $policy_links,
        ]);

        wp_safe_redirect(add_query_arg(['page' => 'omt-settings', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_homepage') {
        update_option('omt_admin_hero', [
            'mission_badge' => sanitize_text_field($_POST['hero_mission_badge'] ?? ''),
            'headline_top' => sanitize_text_field($_POST['hero_headline_top'] ?? ''),
            'headline_emphasis' => sanitize_text_field($_POST['hero_headline_emphasis'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['hero_subtitle'] ?? ''),
            'primary_cta_text' => sanitize_text_field($_POST['hero_primary_cta_text'] ?? ''),
            'primary_cta_url' => esc_url_raw($_POST['hero_primary_cta_url'] ?? ''),
            'secondary_cta_text' => sanitize_text_field($_POST['hero_secondary_cta_text'] ?? ''),
            'hero_image_url' => esc_url_raw($_POST['hero_image_url'] ?? ''),
            'stat1_value' => sanitize_text_field($_POST['hero_stat1_value'] ?? ''),
            'stat1_label' => sanitize_text_field($_POST['hero_stat1_label'] ?? ''),
            'stat2_value' => sanitize_text_field($_POST['hero_stat2_value'] ?? ''),
            'stat2_label' => sanitize_text_field($_POST['hero_stat2_label'] ?? ''),
            'stat3_value' => sanitize_text_field($_POST['hero_stat3_value'] ?? ''),
            'stat3_label' => sanitize_text_field($_POST['hero_stat3_label'] ?? ''),
        ]);
        update_option('omt_admin_mission', [
            'badge' => sanitize_text_field($_POST['mission_badge'] ?? ''),
            'headline' => sanitize_text_field($_POST['mission_headline'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['mission_subtitle'] ?? ''),
            'card1_icon' => sanitize_text_field($_POST['mission_card1_icon'] ?? ''),
            'card1_title' => sanitize_text_field($_POST['mission_card1_title'] ?? ''),
            'card1_desc' => sanitize_textarea_field($_POST['mission_card1_desc'] ?? ''),
            'card2_icon' => sanitize_text_field($_POST['mission_card2_icon'] ?? ''),
            'card2_title' => sanitize_text_field($_POST['mission_card2_title'] ?? ''),
            'card2_desc' => sanitize_textarea_field($_POST['mission_card2_desc'] ?? ''),
            'card3_icon' => sanitize_text_field($_POST['mission_card3_icon'] ?? ''),
            'card3_title' => sanitize_text_field($_POST['mission_card3_title'] ?? ''),
            'card3_desc' => sanitize_textarea_field($_POST['mission_card3_desc'] ?? ''),
        ]);
        update_option('omt_admin_donation_section', [
            'section_title' => sanitize_text_field($_POST['donsec_title'] ?? ''),
            'section_desc' => sanitize_textarea_field($_POST['donsec_desc'] ?? ''),
            'multiplier' => intval($_POST['donsec_multiplier'] ?? 10),
            'volunteer_ratio' => sanitize_text_field($_POST['donsec_volunteer_ratio'] ?? ''),
            'btn_text' => sanitize_text_field($_POST['donsec_btn_text'] ?? ''),
            'tax_note' => sanitize_text_field($_POST['donsec_tax_note'] ?? ''),
        ]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-homepage', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_about') {
        update_option('omt_admin_about_hero', [
            'badge' => sanitize_text_field($_POST['ab_hero_badge'] ?? ''),
            'title' => sanitize_text_field($_POST['ab_hero_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['ab_hero_subtitle'] ?? ''),
        ]);
        $vals = [];
        if (isset($_POST['ab_vals']) && is_array($_POST['ab_vals'])) {
            foreach ($_POST['ab_vals'] as $v) {
                if (!empty($v['title'])) $vals[] = ['icon'=>sanitize_text_field($v['icon']), 'title'=>sanitize_text_field($v['title']), 'desc'=>sanitize_textarea_field($v['desc'])];
            }
        }
        update_option('omt_admin_about_values', [
            'badge' => sanitize_text_field($_POST['ab_vals_badge'] ?? ''),
            'headline' => sanitize_text_field($_POST['ab_vals_headline'] ?? ''),
            'cards' => $vals
        ]);
        update_option('omt_admin_about_deliverables', [
            'box_title' => sanitize_text_field($_POST['ab_deliv_box_title'] ?? ''),
            'box_desc' => sanitize_textarea_field($_POST['ab_deliv_box_desc'] ?? ''),
            'stat1_val' => sanitize_text_field($_POST['ab_deliv_stat1_val'] ?? ''),
            'stat1_label' => sanitize_text_field($_POST['ab_deliv_stat1_label'] ?? ''),
            'stat2_val' => sanitize_text_field($_POST['ab_deliv_stat2_val'] ?? ''),
            'stat2_label' => sanitize_text_field($_POST['ab_deliv_stat2_label'] ?? ''),
            'stat3_val' => sanitize_text_field($_POST['ab_deliv_stat3_val'] ?? ''),
            'stat3_label' => sanitize_text_field($_POST['ab_deliv_stat3_label'] ?? ''),
        ]);
        $mems = [];
        if (isset($_POST['ab_team_mems']) && is_array($_POST['ab_team_mems'])) {
            foreach ($_POST['ab_team_mems'] as $m) {
                if (!empty($m['name'])) $mems[] = ['name'=>sanitize_text_field($m['name']), 'role'=>sanitize_text_field($m['role']), 'highlight'=>sanitize_text_field($m['highlight']), 'image'=>esc_url_raw($m['image'])];
            }
        }
        update_option('omt_admin_team', [
            'badge' => sanitize_text_field($_POST['ab_team_badge'] ?? ''),
            'headline' => sanitize_text_field($_POST['ab_team_headline'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['ab_team_subtitle'] ?? ''),
            'stat1_val' => sanitize_text_field($_POST['ab_team_stat1_val'] ?? ''),
            'stat1_label' => sanitize_text_field($_POST['ab_team_stat1_label'] ?? ''),
            'stat2_val' => sanitize_text_field($_POST['ab_team_stat2_val'] ?? ''),
            'stat2_label' => sanitize_text_field($_POST['ab_team_stat2_label'] ?? ''),
            'stat3_val' => sanitize_text_field($_POST['ab_team_stat3_val'] ?? ''),
            'stat3_label' => sanitize_text_field($_POST['ab_team_stat3_label'] ?? ''),
            'members' => $mems
        ]);
        $tests = [];
        if (isset($_POST['ab_tests_items']) && is_array($_POST['ab_tests_items'])) {
            foreach ($_POST['ab_tests_items'] as $t) {
                if (!empty($t['quote'])) $tests[] = ['quote'=>sanitize_textarea_field($t['quote']), 'name'=>sanitize_text_field($t['name']), 'role'=>sanitize_text_field($t['role'])];
            }
        }
        update_option('omt_admin_testimonials', [
            'badge' => sanitize_text_field($_POST['ab_tests_badge'] ?? ''),
            'headline' => sanitize_text_field($_POST['ab_tests_headline'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['ab_tests_subtitle'] ?? ''),
            'cta_text' => sanitize_text_field($_POST['ab_tests_cta_text'] ?? ''),
            'items' => $tests
        ]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-about', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_impact') {
        update_option('omt_admin_impact_hero', [
            'badge' => sanitize_text_field($_POST['im_hero_badge'] ?? ''),
            'title' => sanitize_text_field($_POST['im_hero_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['im_hero_subtitle'] ?? ''),
        ]);
        update_option('omt_admin_impact_goal', [
            'title' => sanitize_text_field($_POST['im_goal_title'] ?? ''),
            'target' => intval($_POST['im_goal_target'] ?? 0),
            'current' => intval($_POST['im_goal_current'] ?? 0),
            'remaining_note' => sanitize_text_field($_POST['im_goal_remaining_note'] ?? ''),
        ]);
        $kpis = [];
        for($i=1;$i<=4;$i++) {
            $kpis[] = [
                'label' => sanitize_text_field($_POST["im_kpi_{$i}_label"] ?? ''),
                'value' => sanitize_text_field($_POST["im_kpi_{$i}_value"] ?? ''),
                'sub' => sanitize_text_field($_POST["im_kpi_{$i}_sub"] ?? ''),
            ];
        }
        update_option('omt_admin_impact_kpis', [
            'goalTarget' => intval($_POST['im_goal_target'] ?? 0),
            'goalCurrent' => intval($_POST['im_goal_current'] ?? 0),
            'kpis' => $kpis
        ]);
        $stories = [];
        foreach(['seed','growth','mature'] as $c) {
            $stories[$c] = [
                'chapter' => sanitize_text_field($_POST["im_st_{$c}_chapter"] ?? ''),
                'badge' => sanitize_text_field($_POST["im_st_{$c}_badge"] ?? ''),
                'title' => sanitize_text_field($_POST["im_st_{$c}_title"] ?? ''),
                'body1' => sanitize_textarea_field($_POST["im_st_{$c}_body1"] ?? ''),
                'body2' => sanitize_textarea_field($_POST["im_st_{$c}_body2"] ?? ''),
                'image_url' => esc_url_raw($_POST["im_st_{$c}_image_url"] ?? ''),
            ];
        }
        update_option('omt_admin_impact_stories', $stories);
        wp_safe_redirect(add_query_arg(['page' => 'omt-impact', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_donate') {
        update_option('omt_admin_donate_hero', [
            'badge' => sanitize_text_field($_POST['don_hero_badge'] ?? ''),
            'title' => sanitize_text_field($_POST['don_hero_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['don_hero_subtitle'] ?? ''),
        ]);
        $tiers = [];
        if (isset($_POST['don_tiers']) && is_array($_POST['don_tiers'])) {
            foreach ($_POST['don_tiers'] as $t) {
                if (!empty($t['amount'])) $tiers[] = ['amount'=>sanitize_text_field($t['amount']), 'title'=>sanitize_text_field($t['title']), 'description'=>sanitize_textarea_field($t['description']), 'trees'=>sanitize_text_field($t['trees']), 'volunteers'=>sanitize_text_field($t['volunteers'])];
            }
        }
        update_option('omt_admin_donation_tiers', ['tiers' => $tiers]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-donate', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_volunteer_page') {
        update_option('omt_admin_volunteer_hero', [
            'badge' => sanitize_text_field($_POST['vol_hero_badge'] ?? ''),
            'title' => sanitize_text_field($_POST['vol_hero_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['vol_hero_subtitle'] ?? ''),
        ]);
        update_option('omt_admin_volunteer_form', [
            'title' => sanitize_text_field($_POST['vol_form_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['vol_form_subtitle'] ?? ''),
            'interest_options' => sanitize_textarea_field($_POST['vol_form_interests'] ?? ''),
        ]);
        update_option('omt_admin_volunteer_cards', [
            'card1_icon' => sanitize_text_field($_POST['vol_c1_icon'] ?? ''),
            'card1_title' => sanitize_text_field($_POST['vol_c1_title'] ?? ''),
            'card1_desc' => sanitize_textarea_field($_POST['vol_c1_desc'] ?? ''),
            'card2_icon' => sanitize_text_field($_POST['vol_c2_icon'] ?? ''),
            'card2_title' => sanitize_text_field($_POST['vol_c2_title'] ?? ''),
            'card2_desc' => sanitize_textarea_field($_POST['vol_c2_desc'] ?? ''),
            'card3_icon' => sanitize_text_field($_POST['vol_c3_icon'] ?? ''),
            'card3_title' => sanitize_text_field($_POST['vol_c3_title'] ?? ''),
            'card3_desc' => sanitize_textarea_field($_POST['vol_c3_desc'] ?? ''),
        ]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-volunteer-page', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'save_contact_page') {
        update_option('omt_admin_contact_hero', [
            'badge' => sanitize_text_field($_POST['ct_hero_badge'] ?? ''),
            'title' => sanitize_text_field($_POST['ct_hero_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['ct_hero_subtitle'] ?? ''),
        ]);
        update_option('omt_admin_contact_form', [
            'title' => sanitize_text_field($_POST['ct_form_title'] ?? ''),
            'subtitle' => sanitize_textarea_field($_POST['ct_form_subtitle'] ?? ''),
            'btn_text' => sanitize_text_field($_POST['ct_form_btn'] ?? ''),
        ]);
        wp_safe_redirect(add_query_arg(['page' => 'omt-contact-page', 'updated' => 'true'], admin_url('admin.php')));
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
                ['label' => sanitize_text_field($_POST['kpi_1_label'] ?? 'Regions Covered'), 'value' => sanitize_text_field($_POST['kpi_1_val'] ?? '4'), 'sub' => sanitize_text_field($_POST['kpi_1_sub'] ?? 'Granada, Madrid, Valencia, Galicia')],
                ['label' => sanitize_text_field($_POST['kpi_2_label'] ?? 'Projects Active'), 'value' => sanitize_text_field($_POST['kpi_2_val'] ?? '12'), 'sub' => sanitize_text_field($_POST['kpi_2_sub'] ?? 'Active forest & urban sites')],
                ['label' => sanitize_text_field($_POST['kpi_3_label'] ?? 'Tons CO2 Absorbed'), 'value' => sanitize_text_field($_POST['kpi_3_val'] ?? '4,850'), 'sub' => sanitize_text_field($_POST['kpi_3_sub'] ?? 'Estimated lifetime absorption')],
                ['label' => sanitize_text_field($_POST['kpi_4_label'] ?? 'Volunteers Engaged'), 'value' => sanitize_text_field($_POST['kpi_4_val'] ?? '2,420'), 'sub' => sanitize_text_field($_POST['kpi_4_sub'] ?? 'Community participants')],
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
    
    if ($action === 'add_newsletter_subscriber') {
        $email = sanitize_email($_POST['subscriber_email'] ?? '');
        if (is_email($email)) {
            $subs = get_option('omt_admin_newsletter_subscribers', []);
            array_unshift($subs, ['email' => $email, 'date' => date('Y-m-d H:i:s'), 'id' => uniqid()]);
            update_option('omt_admin_newsletter_subscribers', $subs);
        }
        wp_safe_redirect(add_query_arg(['page' => 'omt-newsletter', 'updated' => 'true'], admin_url('admin.php')));
        exit;
    }
    
    if ($action === 'delete_newsletter_subscriber') {
        $email = sanitize_email($_POST['subscriber_email'] ?? '');
        $subs = get_option('omt_admin_newsletter_subscribers', []);
        $subs = array_filter($subs, function($s) use ($email) { return ($s['email'] ?? '') !== $email; });
        update_option('omt_admin_newsletter_subscribers', array_values($subs));
        wp_safe_redirect(add_query_arg(['page' => 'omt-newsletter', 'deleted' => 'true'], admin_url('admin.php')));
        exit;
    }
}
add_action('admin_init', 'one_more_tree_handle_admin_post_save');

/**
 * 1. Render General & Branding Settings
 */
function one_more_tree_render_general_settings() {
    $disclaimer = get_option('omt_admin_disclaimer', one_more_tree_get_default_admin_data('disclaimer'));
    $social = get_option('omt_admin_social', one_more_tree_get_default_admin_data('social'));
    $address = get_option('omt_contact_address', 'Calle Gran Vía 28, Madrid, Spain');
    $email = get_option('omt_contact_email', 'info@onemoretree.eu');
    $phone = get_option('omt_contact_phone', '+34 912 345 678');
    
    $site_id = get_option('omt_admin_site_identity', []);
    $nav = get_option('omt_admin_nav_labels', []);
    $footer = get_option('omt_admin_footer', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-palmtree" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            OneMoreTree — General Settings & Branding
        </h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_branding">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Top Disclaimer Ribbon</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">Enable Ribbon</th>
                        <td><label><input type="checkbox" name="disclaimer_enabled" value="1" <?php checked($disclaimer['enabled'] ?? true); ?>> Show disclaimer</label></td>
                    </tr>
                    <tr>
                        <th scope="row">Disclaimer Text</th>
                        <td><textarea name="disclaimer_message" rows="3" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($disclaimer['message'] ?? ''); ?></textarea></td>
                    </tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Site Identity</h2>
                <table class="form-table">
                    <tr><th scope="row">Site Title</th><td><input type="text" name="site_title" value="<?php echo esc_attr($site_id['site_title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Tagline</th><td><input type="text" name="site_tagline" value="<?php echo esc_attr($site_id['tagline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Logo URL</th><td><input type="url" name="logo_url" value="<?php echo esc_attr($site_id['logo_url'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Favicon URL</th><td><input type="url" name="favicon_url" value="<?php echo esc_attr($site_id['favicon_url'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Navigation Labels</h2>
                <table class="form-table">
                    <tr><th scope="row">Home</th><td><input type="text" name="nav_home" value="<?php echo esc_attr($nav['home'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">About</th><td><input type="text" name="nav_about" value="<?php echo esc_attr($nav['about'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Projects</th><td><input type="text" name="nav_projects" value="<?php echo esc_attr($nav['projects'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Impact</th><td><input type="text" name="nav_impact" value="<?php echo esc_attr($nav['impact'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">FAQ</th><td><input type="text" name="nav_faq" value="<?php echo esc_attr($nav['faq'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Get Involved</th><td><input type="text" name="nav_get_involved" value="<?php echo esc_attr($nav['get_involved'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Volunteer</th><td><input type="text" name="nav_volunteer" value="<?php echo esc_attr($nav['volunteer'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Contact</th><td><input type="text" name="nav_contact" value="<?php echo esc_attr($nav['contact'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Donate</th><td><input type="text" name="nav_donate" value="<?php echo esc_attr($nav['donate'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">CTA Text</th><td><input type="text" name="nav_cta_text" value="<?php echo esc_attr($nav['cta_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">CTA URL</th><td><input type="text" name="nav_cta_url" value="<?php echo esc_attr($nav['cta_url'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Contact Information</h2>
                <table class="form-table">
                    <tr><th scope="row">Email</th><td><input type="email" name="contact_email" value="<?php echo esc_attr($email); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Phone</th><td><input type="text" name="contact_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Address</th><td><input type="text" name="contact_address" value="<?php echo esc_attr($address); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Social Media Channels</h2>
                <table class="form-table">
                    <tr><th scope="row">Instagram</th><td><input type="url" name="social_instagram" value="<?php echo esc_attr($social['instagram'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Twitter/X</th><td><input type="url" name="social_twitter" value="<?php echo esc_attr($social['twitter'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Facebook</th><td><input type="url" name="social_facebook" value="<?php echo esc_attr($social['facebook'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">LinkedIn</th><td><input type="url" name="social_linkedin" value="<?php echo esc_attr($social['linkedin'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Footer Content</h2>
                <table class="form-table">
                    <tr><th scope="row">Footer Tagline</th><td><input type="text" name="footer_tagline" value="<?php echo esc_attr($footer['tagline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Copyright Text</th><td><input type="text" name="footer_copyright" value="<?php echo esc_attr($footer['copyright'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
                <p><strong>Policy Links (Up to 4)</strong></p>
                <div id="policy-links-wrapper">
                <?php 
                $pls = $footer['policy_links'] ?? [['label'=>'', 'url'=>'']];
                foreach ($pls as $i => $pl): ?>
                    <div style="margin-bottom:8px;">
                        <input type="text" name="policy_links[<?php echo $i; ?>][label]" value="<?php echo esc_attr($pl['label']); ?>" placeholder="Label" class="regular-text" style="border-radius:8px;">
                        <input type="url" name="policy_links[<?php echo $i; ?>][url]" value="<?php echo esc_attr($pl['url']); ?>" placeholder="URL" class="regular-text" style="border-radius:8px;">
                    </div>
                <?php endforeach; ?>
                </div>
                <button type="button" class="button" onclick="addPolicyLink()">+ Add Policy Link</button>
                <script>
                    function addPolicyLink() {
                        let w = document.getElementById('policy-links-wrapper');
                        let i = w.children.length;
                        let div = document.createElement('div');
                        div.style.marginBottom = '8px';
                        div.innerHTML = '<input type="text" name="policy_links['+i+'][label]" placeholder="Label" class="regular-text" style="border-radius:8px;"> <input type="url" name="policy_links['+i+'][url]" placeholder="URL" class="regular-text" style="border-radius:8px;">';
                        w.appendChild(div);
                    }
                </script>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Settings" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_homepage_settings() {
    $hero = get_option('omt_admin_hero', []);
    $mission = get_option('omt_admin_mission', []);
    $donation = get_option('omt_admin_donation_section', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-admin-home" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Homepage Content
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_homepage">
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Hero Section</h2>
                <table class="form-table">
                    <tr><th scope="row">Mission Badge</th><td><input type="text" name="hero_mission_badge" value="<?php echo esc_attr($hero['mission_badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline Top</th><td><input type="text" name="hero_headline_top" value="<?php echo esc_attr($hero['headline_top'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline Emphasis</th><td><input type="text" name="hero_headline_emphasis" value="<?php echo esc_attr($hero['headline_emphasis'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="hero_subtitle" rows="3" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Primary CTA Text</th><td><input type="text" name="hero_primary_cta_text" value="<?php echo esc_attr($hero['primary_cta_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Primary CTA URL</th><td><input type="text" name="hero_primary_cta_url" value="<?php echo esc_attr($hero['primary_cta_url'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Secondary CTA Text</th><td><input type="text" name="hero_secondary_cta_text" value="<?php echo esc_attr($hero['secondary_cta_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Hero Image URL</th><td><input type="url" name="hero_image_url" value="<?php echo esc_attr($hero['hero_image_url'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Stat 1</th><td>Value: <input type="text" name="hero_stat1_value" value="<?php echo esc_attr($hero['stat1_value'] ?? ''); ?>" class="small-text" style="border-radius:8px;"> Label: <input type="text" name="hero_stat1_label" value="<?php echo esc_attr($hero['stat1_label'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Stat 2</th><td>Value: <input type="text" name="hero_stat2_value" value="<?php echo esc_attr($hero['stat2_value'] ?? ''); ?>" class="small-text" style="border-radius:8px;"> Label: <input type="text" name="hero_stat2_label" value="<?php echo esc_attr($hero['stat2_label'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Stat 3</th><td>Value: <input type="text" name="hero_stat3_value" value="<?php echo esc_attr($hero['stat3_value'] ?? ''); ?>" class="small-text" style="border-radius:8px;"> Label: <input type="text" name="hero_stat3_label" value="<?php echo esc_attr($hero['stat3_label'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Mission Cards</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="mission_badge" value="<?php echo esc_attr($mission['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline</th><td><input type="text" name="mission_headline" value="<?php echo esc_attr($mission['headline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="mission_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($mission['subtitle'] ?? ''); ?></textarea></td></tr>
                    <?php for($i=1;$i<=3;$i++): ?>
                    <tr><th scope="row">Card <?php echo $i; ?></th><td>
                        Icon: <input type="text" name="mission_card<?php echo $i; ?>_icon" value="<?php echo esc_attr($mission["card{$i}_icon"] ?? ''); ?>" class="small-text" style="border-radius:8px;"><br>
                        Title: <input type="text" name="mission_card<?php echo $i; ?>_title" value="<?php echo esc_attr($mission["card{$i}_title"] ?? ''); ?>" class="regular-text" style="border-radius:8px;margin-top:4px;"><br>
                        Desc: <textarea name="mission_card<?php echo $i; ?>_desc" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"><?php echo esc_textarea($mission["card{$i}_desc"] ?? ''); ?></textarea>
                    </td></tr>
                    <?php endfor; ?>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Donation Section</h2>
                <table class="form-table">
                    <tr><th scope="row">Title</th><td><input type="text" name="donsec_title" value="<?php echo esc_attr($donation['section_title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Description</th><td><textarea name="donsec_desc" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($donation['section_desc'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Multiplier</th><td><input type="number" name="donsec_multiplier" value="<?php echo esc_attr($donation['multiplier'] ?? 10); ?>" class="small-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Volunteer Ratio Text</th><td><input type="text" name="donsec_volunteer_ratio" value="<?php echo esc_attr($donation['volunteer_ratio'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Button Text</th><td><input type="text" name="donsec_btn_text" value="<?php echo esc_attr($donation['btn_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Tax Note</th><td><input type="text" name="donsec_tax_note" value="<?php echo esc_attr($donation['tax_note'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            
            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Homepage Content" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_about_settings() {
    $hero = get_option('omt_admin_about_hero', []);
    $vals = get_option('omt_admin_about_values', []);
    $deliv = get_option('omt_admin_about_deliverables', []);
    $team = get_option('omt_admin_team', []);
    $tests = get_option('omt_admin_testimonials', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-id-alt" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            About & Team Content
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_about">
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">About Hero</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="ab_hero_badge" value="<?php echo esc_attr($hero['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="ab_hero_title" value="<?php echo esc_attr($hero['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="ab_hero_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Core Values</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="ab_vals_badge" value="<?php echo esc_attr($vals['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline</th><td><input type="text" name="ab_vals_headline" value="<?php echo esc_attr($vals['headline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
                <div id="ab-vals-wrapper">
                <?php 
                $vcards = $vals['cards'] ?? array_fill(0,4,['icon'=>'','title'=>'','desc'=>'']);
                foreach($vcards as $i => $vc): ?>
                    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; border-radius:8px;">
                        Icon: <input type="text" name="ab_vals[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($vc['icon']); ?>" class="small-text" style="border-radius:8px;">
                        Title: <input type="text" name="ab_vals[<?php echo $i; ?>][title]" value="<?php echo esc_attr($vc['title']); ?>" class="regular-text" style="border-radius:8px;">
                        Desc: <textarea name="ab_vals[<?php echo $i; ?>][desc]" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"><?php echo esc_textarea($vc['desc']); ?></textarea>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">What We Deliver</h2>
                <table class="form-table">
                    <tr><th scope="row">Box Title</th><td><input type="text" name="ab_deliv_box_title" value="<?php echo esc_attr($deliv['box_title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Box Desc</th><td><textarea name="ab_deliv_box_desc" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($deliv['box_desc'] ?? ''); ?></textarea></td></tr>
                    <?php for($i=1;$i<=3;$i++): ?>
                    <tr><th scope="row">Stat <?php echo $i; ?></th><td>Val: <input type="text" name="ab_deliv_stat<?php echo $i; ?>_val" value="<?php echo esc_attr($deliv["stat{$i}_val"] ?? ''); ?>" class="small-text" style="border-radius:8px;"> Label: <input type="text" name="ab_deliv_stat<?php echo $i; ?>_label" value="<?php echo esc_attr($deliv["stat{$i}_label"] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <?php endfor; ?>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Team Section</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="ab_team_badge" value="<?php echo esc_attr($team['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline</th><td><input type="text" name="ab_team_headline" value="<?php echo esc_attr($team['headline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="ab_team_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($team['subtitle'] ?? ''); ?></textarea></td></tr>
                    <?php for($i=1;$i<=3;$i++): ?>
                    <tr><th scope="row">Summary Stat <?php echo $i; ?></th><td>Val: <input type="text" name="ab_team_stat<?php echo $i; ?>_val" value="<?php echo esc_attr($team["stat{$i}_val"] ?? ''); ?>" class="small-text" style="border-radius:8px;"> Label: <input type="text" name="ab_team_stat<?php echo $i; ?>_label" value="<?php echo esc_attr($team["stat{$i}_label"] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <?php endfor; ?>
                </table>
                <p><strong>Team Members</strong></p>
                <div id="team-members-wrapper">
                <?php 
                $tmems = $team['members'] ?? [['name'=>'','role'=>'','highlight'=>'','image'=>'']];
                foreach ($tmems as $i => $tm): ?>
                    <div class="tm-row" style="border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;">
                        Name: <input type="text" name="ab_team_mems[<?php echo $i; ?>][name]" value="<?php echo esc_attr($tm['name']); ?>" class="regular-text" style="border-radius:8px;">
                        Role: <input type="text" name="ab_team_mems[<?php echo $i; ?>][role]" value="<?php echo esc_attr($tm['role']); ?>" class="regular-text" style="border-radius:8px;"><br>
                        Highlight: <input type="text" name="ab_team_mems[<?php echo $i; ?>][highlight]" value="<?php echo esc_attr($tm['highlight']); ?>" class="large-text" style="border-radius:8px;margin-top:4px;"><br>
                        Avatar URL: <input type="url" name="ab_team_mems[<?php echo $i; ?>][image]" value="<?php echo esc_attr($tm['image']); ?>" class="large-text" style="border-radius:8px;margin-top:4px;">
                        <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>
                    </div>
                <?php endforeach; ?>
                </div>
                <button type="button" class="button" onclick="addTeamMember()">+ Add Team Member</button>
                <script>
                    function addTeamMember() {
                        let w = document.getElementById('team-members-wrapper');
                        let i = Date.now();
                        let div = document.createElement('div');
                        div.className = 'tm-row';
                        div.style = 'border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;';
                        div.innerHTML = `Name: <input type="text" name="ab_team_mems[${i}][name]" class="regular-text" style="border-radius:8px;"> Role: <input type="text" name="ab_team_mems[${i}][role]" class="regular-text" style="border-radius:8px;"><br>Highlight: <input type="text" name="ab_team_mems[${i}][highlight]" class="large-text" style="border-radius:8px;margin-top:4px;"><br>Avatar URL: <input type="url" name="ab_team_mems[${i}][image]" class="large-text" style="border-radius:8px;margin-top:4px;"> <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>`;
                        w.appendChild(div);
                    }
                </script>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Testimonials</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="ab_tests_badge" value="<?php echo esc_attr($tests['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Headline</th><td><input type="text" name="ab_tests_headline" value="<?php echo esc_attr($tests['headline'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="ab_tests_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($tests['subtitle'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">CTA Text</th><td><input type="text" name="ab_tests_cta_text" value="<?php echo esc_attr($tests['cta_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                </table>
                <p><strong>Testimonial Items</strong></p>
                <div id="test-wrapper">
                <?php 
                $titems = $tests['items'] ?? [['quote'=>'','name'=>'','role'=>'']];
                foreach ($titems as $i => $t): ?>
                    <div class="t-row" style="border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;">
                        Name: <input type="text" name="ab_tests_items[<?php echo $i; ?>][name]" value="<?php echo esc_attr($t['name']); ?>" class="regular-text" style="border-radius:8px;">
                        Role: <input type="text" name="ab_tests_items[<?php echo $i; ?>][role]" value="<?php echo esc_attr($t['role']); ?>" class="regular-text" style="border-radius:8px;"><br>
                        Quote: <textarea name="ab_tests_items[<?php echo $i; ?>][quote]" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"><?php echo esc_textarea($t['quote']); ?></textarea>
                        <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>
                    </div>
                <?php endforeach; ?>
                </div>
                <button type="button" class="button" onclick="addTest()">+ Add Testimonial</button>
                <script>
                    function addTest() {
                        let w = document.getElementById('test-wrapper');
                        let i = Date.now();
                        let div = document.createElement('div');
                        div.className = 't-row';
                        div.style = 'border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;';
                        div.innerHTML = `Name: <input type="text" name="ab_tests_items[${i}][name]" class="regular-text" style="border-radius:8px;"> Role: <input type="text" name="ab_tests_items[${i}][role]" class="regular-text" style="border-radius:8px;"><br>Quote: <textarea name="ab_tests_items[${i}][quote]" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"></textarea> <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>`;
                        w.appendChild(div);
                    }
                </script>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save About Content" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_impact_settings() {
    $hero = get_option('omt_admin_impact_hero', []);
    $goal = get_option('omt_admin_impact_goal', []);
    $kpis = get_option('omt_admin_impact_kpis', []);
    $stories = get_option('omt_admin_impact_stories', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-leaf" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Impact & Stories
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_impact">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Impact Hero</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="im_hero_badge" value="<?php echo esc_attr($hero['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="im_hero_title" value="<?php echo esc_attr($hero['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="im_hero_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Goal Progress</h2>
                <table class="form-table">
                    <tr><th scope="row">Goal Title</th><td><input type="text" name="im_goal_title" value="<?php echo esc_attr($goal['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Target (Number)</th><td><input type="number" name="im_goal_target" value="<?php echo esc_attr($kpis['goalTarget'] ?? $goal['target'] ?? 0); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Current (Number)</th><td><input type="number" name="im_goal_current" value="<?php echo esc_attr($kpis['goalCurrent'] ?? $goal['current'] ?? 0); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Remaining Note</th><td><input type="text" name="im_goal_remaining_note" value="<?php echo esc_attr($goal['remaining_note'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Impact KPIs</h2>
                <table class="form-table">
                    <?php 
                    $kpivals = $kpis['kpis'] ?? [];
                    for($i=1;$i<=4;$i++): $k = $kpivals[$i-1] ?? ['label'=>'','value'=>'','sub'=>'']; ?>
                    <tr><th scope="row">KPI <?php echo $i; ?></th><td>
                        Label: <input type="text" name="im_kpi_<?php echo $i; ?>_label" value="<?php echo esc_attr($k['label']); ?>" class="regular-text" style="border-radius:8px;">
                        Value: <input type="text" name="im_kpi_<?php echo $i; ?>_value" value="<?php echo esc_attr($k['value']); ?>" class="small-text" style="border-radius:8px;">
                        Sub: <input type="text" name="im_kpi_<?php echo $i; ?>_sub" value="<?php echo esc_attr($k['sub']); ?>" class="regular-text" style="border-radius:8px;">
                    </td></tr>
                    <?php endfor; ?>
                </table>
            </div>

            <?php foreach(['seed' => 'Chapter 1 (Seed)', 'growth' => 'Chapter 2 (Growth)', 'mature' => 'Chapter 3 (Mature)'] as $c => $lbl): 
                $st = $stories[$c] ?? [];
            ?>
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;"><?php echo $lbl; ?></h2>
                <table class="form-table">
                    <tr><th scope="row">Chapter Label</th><td><input type="text" name="im_st_<?php echo $c; ?>_chapter" value="<?php echo esc_attr($st['chapter'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Badge</th><td><input type="text" name="im_st_<?php echo $c; ?>_badge" value="<?php echo esc_attr($st['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="im_st_<?php echo $c; ?>_title" value="<?php echo esc_attr($st['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Body 1</th><td><textarea name="im_st_<?php echo $c; ?>_body1" rows="3" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($st['body1'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Body 2</th><td><textarea name="im_st_<?php echo $c; ?>_body2" rows="3" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($st['body2'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Image URL</th><td><input type="url" name="im_st_<?php echo $c; ?>_image_url" value="<?php echo esc_attr($st['image_url'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                </table>
            </div>
            <?php endforeach; ?>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Impact Content" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_counters_settings() {
    $counters = get_option('omt_admin_home_counters', one_more_tree_get_default_admin_data('home_counters'));
    $impact = get_option('omt_admin_impact_kpis', one_more_tree_get_default_admin_data('impact_kpis'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-chart-bar" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Counters & KPIs
        </h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Counters updated successfully!</strong></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_counters">

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
                    <?php for($i=1;$i<=4;$i++): ?>
                    <tr>
                        <th scope="row">KPI <?php echo $i; ?></th>
                        <td>
                            <input type="text" name="kpi_<?php echo $i; ?>_label" value="<?php echo esc_attr($impact['kpis'][$i-1]['label'] ?? "KPI $i"); ?>" class="regular-text" style="border-radius:8px;">
                            <input type="text" name="kpi_<?php echo $i; ?>_val" value="<?php echo esc_attr($impact['kpis'][$i-1]['value'] ?? ''); ?>" class="small-text" style="border-radius:8px;">
                            <input type="text" name="kpi_<?php echo $i; ?>_sub" value="<?php echo esc_attr($impact['kpis'][$i-1]['sub'] ?? ''); ?>" class="regular-text" style="border-radius:8px;">
                        </td>
                    </tr>
                    <?php endfor; ?>
                </table>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Counters" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_map_projects_settings() {
    $live_map = get_option('omt_admin_live_map', one_more_tree_get_default_admin_data('live_map'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-location-alt" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Live Map & Projects
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

function one_more_tree_render_team_stories_settings() {
    $team = get_option('omt_admin_team', one_more_tree_get_default_admin_data('team'));
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-groups" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Team & Stories
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
                            <td><strong><?php echo esc_html($m['name'] ?? ''); ?></strong></td>
                            <td><?php echo esc_html($m['role'] ?? ''); ?></td>
                            <td><?php echo esc_html($m['highlight'] ?? $m['experience'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function one_more_tree_render_faq_settings() {
    $faq_data = get_option('omt_admin_faq', one_more_tree_get_default_admin_data('faq'));
    $faqs = $faq_data['faqs'] ?? [];
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-editor-help" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            FAQ Manager
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
                            <p style="margin:0 0 6px 0;"><strong>Question:</strong></p>
                            <input type="text" name="faq_items[<?php echo $i; ?>][q]" value="<?php echo esc_attr($faq['q']); ?>" class="large-text" style="border-radius:8px;margin-bottom:8px;">
                            <p style="margin:0 0 6px 0;"><strong>Answer:</strong></p>
                            <textarea name="faq_items[<?php echo $i; ?>][a]" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($faq['a']); ?></textarea>
                            <button type="button" class="button" style="margin-top:8px;" onclick="this.parentNode.remove()">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button" onclick="addFaq()">+ Add New Question</button>
                <script>
                    function addFaq() {
                        let w = document.getElementById('faq-list');
                        let i = Date.now();
                        let div = document.createElement('div');
                        div.className = 'faq-item-row';
                        div.style = 'background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:10px;margin-bottom:14px;';
                        div.innerHTML = `<p style="margin:0 0 6px 0;"><strong>Question:</strong></p><input type="text" name="faq_items[${i}][q]" class="large-text" style="border-radius:8px;margin-bottom:8px;"><p style="margin:0 0 6px 0;"><strong>Answer:</strong></p><textarea name="faq_items[${i}][a]" rows="2" class="large-text" style="border-radius:8px;"></textarea><button type="button" class="button" style="margin-top:8px;" onclick="this.parentNode.remove()">Remove</button>`;
                        w.appendChild(div);
                    }
                </script>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save FAQ Items" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_donate_settings() {
    $hero = get_option('omt_admin_donate_hero', []);
    $tiers_data = get_option('omt_admin_donation_tiers', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-heart" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Donation Tiers
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_donate">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Donate Hero</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="don_hero_badge" value="<?php echo esc_attr($hero['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="don_hero_title" value="<?php echo esc_attr($hero['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="don_hero_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Donation Tiers</h2>
                <div id="don-tiers-wrapper">
                <?php 
                $tiers = $tiers_data['tiers'] ?? array_fill(0, 3, ['amount'=>'','title'=>'','description'=>'','trees'=>'','volunteers'=>'']);
                foreach ($tiers as $i => $t): ?>
                    <div style="border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;">
                        Amount: <input type="text" name="don_tiers[<?php echo $i; ?>][amount]" value="<?php echo esc_attr($t['amount']); ?>" class="small-text" style="border-radius:8px;">
                        Title: <input type="text" name="don_tiers[<?php echo $i; ?>][title]" value="<?php echo esc_attr($t['title']); ?>" class="regular-text" style="border-radius:8px;"><br>
                        Description: <textarea name="don_tiers[<?php echo $i; ?>][description]" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"><?php echo esc_textarea($t['description']); ?></textarea><br>
                        Trees: <input type="text" name="don_tiers[<?php echo $i; ?>][trees]" value="<?php echo esc_attr($t['trees']); ?>" class="small-text" style="border-radius:8px;">
                        Volunteers: <input type="text" name="don_tiers[<?php echo $i; ?>][volunteers]" value="<?php echo esc_attr($t['volunteers']); ?>" class="small-text" style="border-radius:8px;">
                        <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>
                    </div>
                <?php endforeach; ?>
                </div>
                <button type="button" class="button" onclick="addTier()">+ Add Tier</button>
                <script>
                    function addTier() {
                        let w = document.getElementById('don-tiers-wrapper');
                        let i = Date.now();
                        let div = document.createElement('div');
                        div.style = 'border:1px solid #e2e8f0; padding:10px; margin-bottom:10px; border-radius:8px; background:#f8fafc;';
                        div.innerHTML = `Amount: <input type="text" name="don_tiers[${i}][amount]" class="small-text" style="border-radius:8px;"> Title: <input type="text" name="don_tiers[${i}][title]" class="regular-text" style="border-radius:8px;"><br>Description: <textarea name="don_tiers[${i}][description]" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"></textarea><br>Trees: <input type="text" name="don_tiers[${i}][trees]" class="small-text" style="border-radius:8px;"> Volunteers: <input type="text" name="don_tiers[${i}][volunteers]" class="small-text" style="border-radius:8px;"> <button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>`;
                        w.appendChild(div);
                    }
                </script>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Donation Settings" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_volunteer_page_settings() {
    $hero = get_option('omt_admin_volunteer_hero', []);
    $form = get_option('omt_admin_volunteer_form', []);
    $cards = get_option('omt_admin_volunteer_cards', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-groups" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Volunteer Page Settings
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_volunteer_page">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Volunteer Hero</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="vol_hero_badge" value="<?php echo esc_attr($hero['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="vol_hero_title" value="<?php echo esc_attr($hero['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="vol_hero_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Form Box</h2>
                <table class="form-table">
                    <tr><th scope="row">Form Title</th><td><input type="text" name="vol_form_title" value="<?php echo esc_attr($form['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Form Subtitle</th><td><textarea name="vol_form_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($form['subtitle'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Interest Options (one per line)</th><td><textarea name="vol_form_interests" rows="4" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($form['interest_options'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>
            
            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Info Cards</h2>
                <table class="form-table">
                    <?php for($i=1;$i<=3;$i++): ?>
                    <tr><th scope="row">Card <?php echo $i; ?></th><td>
                        Icon: <input type="text" name="vol_c<?php echo $i; ?>_icon" value="<?php echo esc_attr($cards["card{$i}_icon"] ?? ''); ?>" class="small-text" style="border-radius:8px;"><br>
                        Title: <input type="text" name="vol_c<?php echo $i; ?>_title" value="<?php echo esc_attr($cards["card{$i}_title"] ?? ''); ?>" class="regular-text" style="border-radius:8px;margin-top:4px;"><br>
                        Desc: <textarea name="vol_c<?php echo $i; ?>_desc" rows="2" class="large-text" style="border-radius:8px;margin-top:4px;"><?php echo esc_textarea($cards["card{$i}_desc"] ?? ''); ?></textarea>
                    </td></tr>
                    <?php endfor; ?>
                </table>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Volunteer Settings" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

function one_more_tree_render_contact_page_settings() {
    $hero = get_option('omt_admin_contact_hero', []);
    $form = get_option('omt_admin_contact_form', []);
    ?>
    <div class="wrap" style="max-width: 1000px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-email" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Contact Page Settings
        </h1>
        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Settings saved successfully!</strong></p></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
            <input type="hidden" name="omt_admin_action" value="save_contact_page">

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Contact Hero</h2>
                <table class="form-table">
                    <tr><th scope="row">Badge</th><td><input type="text" name="ct_hero_badge" value="<?php echo esc_attr($hero['badge'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Title</th><td><input type="text" name="ct_hero_title" value="<?php echo esc_attr($hero['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Subtitle</th><td><textarea name="ct_hero_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($hero['subtitle'] ?? ''); ?></textarea></td></tr>
                </table>
            </div>

            <div class="postbox" style="padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                <h2 style="margin-top:0;font-size:18px;border-bottom:1px solid #e5e7eb;padding-bottom:10px;">Form Box</h2>
                <table class="form-table">
                    <tr><th scope="row">Form Title</th><td><input type="text" name="ct_form_title" value="<?php echo esc_attr($form['title'] ?? ''); ?>" class="large-text" style="border-radius:8px;"></td></tr>
                    <tr><th scope="row">Form Subtitle</th><td><textarea name="ct_form_subtitle" rows="2" class="large-text" style="border-radius:8px;"><?php echo esc_textarea($form['subtitle'] ?? ''); ?></textarea></td></tr>
                    <tr><th scope="row">Button Text</th><td><input type="text" name="ct_form_btn" value="<?php echo esc_attr($form['btn_text'] ?? ''); ?>" class="regular-text" style="border-radius:8px;"></td></tr>
                </table>
                <p class="description">Note: Contact address, email, and phone info cards are managed in General & Branding.</p>
            </div>

            <p class="submit">
                <input type="submit" class="button button-primary button-large" value="Save Contact Settings" style="background:#15803d;border-color:#15803d;border-radius:8px;padding:4px 24px;">
            </p>
        </form>
    </div>
    <?php
}

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
        
        <!-- Optional Volunteer tab toggle structure for UI purposes -->
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active">All Messages</a>
        </h2>

        <?php if (empty($submissions)): ?>
            <div class="postbox" style="padding: 30px; text-align: center; border-radius: 12px; margin-top:20px;">
                <p style="color:#64748b;font-size:16px;">No contact inquiries received yet. Submissions sent via the contact and volunteer forms will appear here automatically.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped" style="border-radius: 10px; overflow: hidden; margin-top:20px;">
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

function one_more_tree_render_newsletter_subscribers() {
    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    ?>
    <div class="wrap" style="max-width: 900px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span class="dashicons dashicons-email-alt" style="font-size:32px;width:32px;height:32px;color:#15803d;"></span>
            Newsletter Subscribers (<?php echo count($subscribers); ?>)
        </h1>
        
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <form method="post" action="" style="display:inline-block;">
                <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
                <input type="hidden" name="omt_admin_action" value="add_newsletter_subscriber">
                <input type="email" name="subscriber_email" placeholder="Email address" required class="regular-text" style="border-radius:8px;">
                <input type="submit" class="button button-primary" value="Add Subscriber" style="background:#15803d;border-color:#15803d;border-radius:8px;">
            </form>
            <form method="post" action="" style="display:inline-block;">
                <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
                <input type="hidden" name="omt_admin_action" value="export_newsletter_csv">
                <input type="submit" class="button" value="Export CSV" style="border-radius:8px;">
            </form>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Subscriber added.</strong></p></div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="notice notice-success is-dismissible"><p><strong>Subscriber deleted.</strong></p></div>
        <?php endif; ?>

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
                        <th style="font-weight:bold;width:80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscribers as $idx => $s): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo esc_html($s['email'] ?? ''); ?></strong></td>
                            <td><?php echo esc_html($s['date'] ?? ''); ?></td>
                            <td><span style="color:#15803d;font-weight:600;">Active</span></td>
                            <td>
                                <form method="post" action="" onsubmit="return confirm('Are you sure you want to remove this subscriber?');">
                                    <?php wp_nonce_field('omt_admin_save_nonce', 'omt_nonce'); ?>
                                    <input type="hidden" name="omt_admin_action" value="delete_newsletter_subscriber">
                                    <input type="hidden" name="subscriber_email" value="<?php echo esc_attr($s['email'] ?? ''); ?>">
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
