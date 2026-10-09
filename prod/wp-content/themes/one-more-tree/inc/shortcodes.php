<?php
/**
 * Dynamic Content Shortcodes
 * 
 * Shortcodes that fetch dynamic data from the admin API for use in block patterns.
 * This allows block patterns to display dynamic content while maintaining static markup.
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hero Section Shortcode
 * Displays hero section with dynamic data from admin API
 */
function omt_hero_shortcode($atts) {
    $atts = shortcode_atts([
        'show_stats' => 'true',
        'show_cta' => 'true',
    ], $atts);

    $admin_hero_data = get_option('omt_admin_hero', []);
    $admin_counters = get_option('omt_admin_home_counters', []);

    // Hero stats
    $hero_stat1_val = !empty($admin_hero_data['stat1_value']) ? $admin_hero_data['stat1_value'] : (!empty($admin_counters['treesTracked']) ? number_format($admin_counters['treesTracked']) : '1M');
    $hero_stat1_lbl = !empty($admin_hero_data['stat1_label']) ? $admin_hero_data['stat1_label'] : 'Trees Planted';
    $hero_stat2_val = !empty($admin_hero_data['stat2_value']) ? $admin_hero_data['stat2_value'] : (!empty($admin_counters['projectsActive']) ? $admin_counters['projectsActive'] . '+' : '50+');
    $hero_stat2_lbl = !empty($admin_hero_data['stat2_label']) ? $admin_hero_data['stat2_label'] : 'Projects';
    $hero_stat3_val = !empty($admin_hero_data['stat3_value']) ? $admin_hero_data['stat3_value'] : (!empty($admin_counters['volunteersEngaged']) ? number_format($admin_counters['volunteersEngaged']) . '+' : '10K+');
    $hero_stat3_lbl = !empty($admin_hero_data['stat3_label']) ? $admin_hero_data['stat3_label'] : 'Volunteers';

    ob_start();
    ?>
    <div class="omt-hero-data" data-stats='<?php echo json_encode([
        'stat1' => ['value' => $hero_stat1_val, 'label' => $hero_stat1_lbl],
        'stat2' => ['value' => $hero_stat2_val, 'label' => $hero_stat2_lbl],
        'stat3' => ['value' => $hero_stat3_val, 'label' => $hero_stat3_lbl],
    ]); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_hero', 'omt_hero_shortcode');

/**
 * Counters Section Shortcode
 * Displays counter section with dynamic data
 */
function omt_counters_shortcode($atts) {
    $admin_counters = get_option('omt_admin_home_counters', []);

    $counter_trees = !empty($admin_counters['treesTracked']) ? number_format($admin_counters['treesTracked']) : '1,000,000';
    $counter_projects = !empty($admin_counters['projectsActive']) ? $admin_counters['projectsActive'] . '+' : '50+';
    $counter_volunteers = !empty($admin_counters['volunteersEngaged']) ? number_format($admin_counters['volunteersEngaged']) . '+' : '10,000+';

    ob_start();
    ?>
    <div class="omt-counters-data" data-counters='<?php echo json_encode([
        'trees' => $counter_trees,
        'projects' => $counter_projects,
        'volunteers' => $counter_volunteers,
    ]); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_counters', 'omt_counters_shortcode');

/**
 * Live Map Shortcode
 * Displays interactive map with project locations
 */
function omt_live_map_shortcode($atts) {
    $atts = shortcode_atts([
        'show_all' => 'true',
        'max_locations' => '10',
    ], $atts);

    $admin_map = get_option('omt_admin_live_map');
    $locations = !empty($admin_map['locations']) ? $admin_map['locations'] : [];

    ob_start();
    ?>
    <div class="omt-live-map-container" data-locations='<?php echo json_encode($locations); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_live_map', 'omt_live_map_shortcode');

/**
 * Statistics Cards Shortcode
 * Displays statistics cards with dynamic data
 */
function omt_stats_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => 'hero',
        'layout' => '3-column',
    ], $atts);

    $admin_counters = get_option('omt_admin_home_counters', []);
    $admin_hero_data = get_option('omt_admin_hero', []);

    $stats = [];
    if ($atts['type'] === 'hero') {
        $stats[] = [
            'value' => !empty($admin_hero_data['stat1_value']) ? $admin_hero_data['stat1_value'] : (!empty($admin_counters['treesTracked']) ? number_format($admin_counters['treesTracked']) : '1M'),
            'label' => !empty($admin_hero_data['stat1_label']) ? $admin_hero_data['stat1_label'] : 'Trees Planted'
        ];
        $stats[] = [
            'value' => !empty($admin_hero_data['stat2_value']) ? $admin_hero_data['stat2_value'] : (!empty($admin_counters['projectsActive']) ? $admin_counters['projectsActive'] . '+' : '50+'),
            'label' => !empty($admin_hero_data['stat2_label']) ? $admin_hero_data['stat2_label'] : 'Projects'
        ];
        $stats[] = [
            'value' => !empty($admin_hero_data['stat3_value']) ? $admin_hero_data['stat3_value'] : (!empty($admin_counters['volunteersEngaged']) ? number_format($admin_counters['volunteersEngaged']) . '+' : '10K+'),
            'label' => !empty($admin_hero_data['stat3_label']) ? $admin_hero_data['stat3_label'] : 'Volunteers'
        ];
    }

    ob_start();
    ?>
    <div class="omt-stats-container" data-stats='<?php echo json_encode($stats); ?>' data-layout="<?php echo esc_attr($atts['layout']); ?>"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_stats', 'omt_stats_shortcode');

/**
 * Donation Tiers Shortcode
 * Displays donation tier cards with dynamic data
 */
function omt_donation_tiers_shortcode($atts) {
    $atts = shortcode_atts([
        'max_tiers' => '3',
        'default_amount' => '25',
    ], $atts);

    $admin_tiers_data = get_option('omt_admin_donation_tiers', []);
    $tiers = !empty($admin_tiers_data['tiers']) ? $admin_tiers_data['tiers'] : [
        ['amount' => 25, 'title' => '$25 support', 'description' => 'Supports tree planting, site care, and local communities.', 'trees' => 75, 'volunteers' => 3],
        ['amount' => 50, 'title' => '$50 support', 'description' => 'Expands native tree coverage and regional biodiversity.', 'trees' => 150, 'volunteers' => 5],
        ['amount' => 100, 'title' => '$100 support', 'description' => 'Funds full plot restoration, native saplings, and 5-year monitoring.', 'trees' => 300, 'volunteers' => 10],
    ];

    // Limit tiers if specified
    $max_tiers = intval($atts['max_tiers']);
    if ($max_tiers > 0 && count($tiers) > $max_tiers) {
        $tiers = array_slice($tiers, 0, $max_tiers);
    }

    ob_start();
    ?>
    <div class="omt-donation-tiers-container" data-tiers='<?php echo json_encode($tiers); ?>' data-default="<?php echo esc_attr($atts['default_amount']); ?>"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_donation_tiers', 'omt_donation_tiers_shortcode');

/**
 * Impact KPIs Shortcode
 * Displays impact KPI cards with dynamic data
 */
function omt_impact_kpis_shortcode($atts) {
    $admin_kpis = get_option('omt_admin_impact_kpis', []);
    $admin_goal = get_option('omt_admin_impact_goal', []);

    $kpis = !empty($admin_kpis) ? $admin_kpis : [
        ['label' => 'Trees Planted', 'value' => '847,000', 'icon' => '🌱'],
        ['label' => 'Hectares Restored', 'value' => '420', 'icon' => '🌲'],
        ['label' => 'Volunteers Engaged', 'value' => '12,500', 'icon' => '🤝'],
        ['label' => 'Regions Covered', 'value' => '8', 'icon' => '🌍'],
    ];

    $goal = !empty($admin_goal) ? $admin_goal : [
        'title' => '1,000,000 Trees by 2040',
        'target' => 1000000,
        'current' => 847000,
        'remaining_note' => '153,000 trees left to plant'
    ];

    ob_start();
    ?>
    <div class="omt-impact-kpis-container" data-kpis='<?php echo json_encode($kpis); ?>' data-goal='<?php echo json_encode($goal); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_impact_kpis', 'omt_impact_kpis_shortcode');

/**
 * About Values Shortcode
 * Displays about section value cards with dynamic data
 */
function omt_about_values_shortcode($atts) {
    $admin_values = get_option('omt_admin_about_values', []);

    $values = !empty($admin_values['cards']) ? $admin_values['cards'] : [
        ['icon' => '🌱', 'title' => 'Start with seeds', 'desc' => 'Every great forest begins small. We plant with patience and intention.'],
        ['icon' => '🌿', 'title' => 'Respect ecosystems', 'desc' => 'Native species only. We work with nature, not against it.'],
        ['icon' => '📅', 'title' => 'Commit for the long run', 'desc' => 'Forests take decades. We plan and monitor accordingly.'],
        ['icon' => '🤝', 'title' => 'Work with communities', 'desc' => 'Local stewardship is the key to long-term survival.'],
    ];

    ob_start();
    ?>
    <div class="omt-about-values-container" data-values='<?php echo json_encode($values); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_about_values', 'omt_about_values_shortcode');

/**
 * Testimonials Shortcode
 * Displays testimonial cards with dynamic data
 */
function omt_testimonials_shortcode($atts) {
    $atts = shortcode_atts([
        'max_testimonials' => '3',
        'layout' => 'grid',
    ], $atts);

    $admin_testimonials = get_option('omt_admin_testimonials', []);

    $testimonials = !empty($admin_testimonials['items']) ? $admin_testimonials['items'] : [
        ['quote' => 'Planting with OneMoreTree changed how I see the land around me.', 'name' => 'María J.', 'role' => 'Volunteer, Granada'],
        ['quote' => 'The transparency and follow-up reports make it easy to trust the work.', 'name' => 'David K.', 'role' => 'Corporate Partner, Madrid'],
        ['quote' => 'Our school planted 50 trees. The kids still visit them every year.', 'name' => 'Pilar R.', 'role' => 'Teacher, Valencia'],
    ];

    // Limit testimonials if specified
    $max_testimonials = intval($atts['max_testimonials']);
    if ($max_testimonials > 0 && count($testimonials) > $max_testimonials) {
        $testimonials = array_slice($testimonials, 0, $max_testimonials);
    }

    ob_start();
    ?>
    <div class="omt-testimonials-container" data-testimonials='<?php echo json_encode($testimonials); ?>' data-layout="<?php echo esc_attr($atts['layout']); ?>"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_testimonials', 'omt_testimonials_shortcode');

/**
 * FAQ Shortcode
 * Displays FAQ items with dynamic data
 */
function omt_faq_shortcode($atts) {
    $atts = shortcode_atts([
        'category' => '',
        'max_items' => '10',
    ], $atts);

    $admin_faq = get_option('omt_admin_faq', []);

    $faq_items = !empty($admin_faq['items']) ? $admin_faq['items'] : [
        ['question' => 'How can I volunteer?', 'answer' => 'You can sign up through our volunteer form or contact us directly for opportunities in your area.'],
        ['question' => 'What tree species do you plant?', 'answer' => 'We plant native species appropriate to each region, focusing on biodiversity and long-term survival.'],
        ['question' => 'How do you track tree survival?', 'answer' => 'We monitor planted trees for 5+ years, using GPS tracking and regular site visits to ensure survival rates above 80%.'],
    ];

    // Filter by category if specified
    if (!empty($atts['category'])) {
        $faq_items = array_filter($faq_items, function($item) use ($atts) {
            return isset($item['category']) && $item['category'] === $atts['category'];
        });
    }

    // Limit items if specified
    $max_items = intval($atts['max_items']);
    if ($max_items > 0 && count($faq_items) > $max_items) {
        $faq_items = array_slice($faq_items, 0, $max_items);
    }

    ob_start();
    ?>
    <div class="omt-faq-container" data-faq='<?php echo json_encode(array_values($faq_items)); ?>'></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_faq', 'omt_faq_shortcode');

/**
 * Contact Form Shortcode
 * Displays contact form with AJAX integration
 */
function omt_contact_form_shortcode($atts) {
    $atts = shortcode_atts([
        'show_interest' => 'true',
        'recaptcha' => 'false',
    ], $atts);

    $admin_contact = get_option('omt_admin_contact_page', []);

    $form_title = !empty($admin_contact['form_title']) ? $admin_contact['form_title'] : 'Send a Message';
    $form_subtitle = !empty($admin_contact['form_subtitle']) ? $admin_contact['form_subtitle'] : 'We typically respond within 24–48 hours.';
    $btn_text = !empty($admin_contact['btn_text']) ? $admin_contact['btn_text'] : 'Send Message';

    ob_start();
    ?>
    <div class="omt-contact-form-container" 
         data-settings='<?php echo json_encode([
             'show_interest' => $atts['show_interest'] === 'true',
             'recaptcha' => $atts['recaptcha'] === 'true',
             'form_title' => $form_title,
             'form_subtitle' => $form_subtitle,
             'btn_text' => $btn_text,
         ]); ?>'>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_contact_form', 'omt_contact_form_shortcode');

/**
 * Newsletter Form Shortcode
 * Displays newsletter signup form
 */
function omt_newsletter_form_shortcode($atts) {
    $atts = shortcode_atts([
        'show_title' => 'true',
        'compact' => 'false',
    ], $atts);

    ob_start();
    ?>
    <div class="omt-newsletter-form-container" 
         data-settings='<?php echo json_encode([
             'show_title' => $atts['show_title'] === 'true',
             'compact' => $atts['compact'] === 'true',
         ]); ?>'>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_newsletter_form', 'omt_newsletter_form_shortcode');

/**
 * Team Shortcode
 * Displays team member cards
 */
function omt_team_shortcode($atts) {
    $atts = shortcode_atts([
        'max_members' => '4',
        'layout' => 'grid',
    ], $atts);

    $admin_team = get_option('omt_admin_team', []);

    $team_members = !empty($admin_team['members']) ? $admin_team['members'] : [
        ['name' => 'Team Member', 'role' => 'Role', 'bio' => 'Bio description'],
    ];

    // Limit members if specified
    $max_members = intval($atts['max_members']);
    if ($max_members > 0 && count($team_members) > $max_members) {
        $team_members = array_slice($team_members, 0, $max_members);
    }

    ob_start();
    ?>
    <div class="omt-team-container" data-team='<?php echo json_encode($team_members); ?>' data-layout="<?php echo esc_attr($atts['layout']); ?>"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_team', 'omt_team_shortcode');

/**
 * Social Links Shortcode
 * Displays social media links
 */
function omt_social_links_shortcode($atts) {
    $atts = shortcode_atts([
        'style' => 'icons',
        'show_labels' => 'false',
    ], $atts);

    $admin_social = get_option('omt_admin_social', []);
    $social_links = !empty($admin_social['links']) ? $admin_social['links'] : [];

    ob_start();
    ?>
    <div class="omt-social-links-container" 
         data-links='<?php echo json_encode($social_links); ?>'
         data-style="<?php echo esc_attr($atts['style']); ?>"
         data-show-labels="<?php echo esc_attr($atts['show_labels']); ?>">
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('omt_social_links', 'omt_social_links_shortcode');

/**
 * Dynamic Text Shortcode
 * Generic shortcode for displaying dynamic text from admin data
 */
function omt_dynamic_text_shortcode($atts) {
    $atts = shortcode_atts([
        'key' => '',
        'option' => '',
        'fallback' => '',
    ], $atts);

    if (empty($atts['key']) && empty($atts['option'])) {
        return esc_html($atts['fallback']);
    }

    $value = '';
    if (!empty($atts['option'])) {
        $data = get_option($atts['option'], []);
        if (is_array($data) && !empty($atts['key'])) {
            $value = $data[$atts['key']] ?? '';
        } elseif (is_string($data)) {
            $value = $data;
        }
    } else {
        // Try to guess the option name from the key
        $option_name = 'omt_admin_' . $atts['key'];
        $data = get_option($option_name, []);
        if (is_array($data)) {
            $value = $data;
        } else {
            $value = $data;
        }
    }

    // Handle nested arrays
    if (is_array($value)) {
        // If key contains dot notation, access nested value
        if (strpos($atts['key'], '.') !== false) {
            $keys = explode('.', $atts['key']);
            foreach ($keys as $k) {
                if (is_array($value) && isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    $value = '';
                    break;
                }
            }
        } else {
            $value = '';
        }
    }

    return !empty($value) ? esc_html($value) : esc_html($atts['fallback']);
}
add_shortcode('omt_text', 'omt_dynamic_text_shortcode');
