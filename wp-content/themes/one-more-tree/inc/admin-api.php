<?php
/**
 * REST API Endpoints for OneMoreTree Admin and Public Submissions
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API Routes
 */
function one_more_tree_register_rest_routes() {
    // Namespace: omt/v1
    $namespace = 'omt/v1';

    // 1. Auth Status Endpoint
    register_rest_route($namespace, '/auth/me', [
        'methods'             => 'GET',
        'callback'            => 'one_more_tree_rest_auth_me',
        'permission_callback' => '__return_true',
    ]);

    // 2. Auth Login Endpoint
    register_rest_route($namespace, '/auth/login', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_auth_login',
        'permission_callback' => '__return_true',
    ]);

    // 3. Auth Logout Endpoint
    register_rest_route($namespace, '/auth/logout', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_auth_logout',
        'permission_callback' => '__return_true',
    ]);

    // 4. Admin Data Read & Write Endpoint
    register_rest_route($namespace, '/admin/data', [
        [
            'methods'             => 'GET',
            'callback'            => 'one_more_tree_rest_admin_data_get',
            'permission_callback' => 'one_more_tree_rest_admin_permission',
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'one_more_tree_rest_admin_data_post',
            'permission_callback' => 'one_more_tree_rest_admin_permission',
        ],
    ]);

    // 5. Public Contact Form Submission
    register_rest_route($namespace, '/public/contact', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_public_contact',
        'permission_callback' => '__return_true',
    ]);

    // 6. Public Newsletter Submission
    register_rest_route($namespace, '/public/newsletter', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_public_newsletter',
        'permission_callback' => '__return_true',
    ]);

    // 7. Volunteer Form Submission
    register_rest_route($namespace, '/public/volunteer', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_public_volunteer',
        'permission_callback' => '__return_true',
    ]);

    // 8. Delete Contact Submission
    register_rest_route($namespace, '/admin/contact/delete', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_admin_contact_delete',
        'permission_callback' => 'one_more_tree_rest_admin_permission',
    ]);

    // 9. Delete Newsletter Subscriber
    register_rest_route($namespace, '/admin/newsletter/delete', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_admin_newsletter_delete',
        'permission_callback' => 'one_more_tree_rest_admin_permission',
    ]);

    // 10. Add Newsletter Subscriber (admin)
    register_rest_route($namespace, '/admin/newsletter/add', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_admin_newsletter_add',
        'permission_callback' => 'one_more_tree_rest_admin_permission',
    ]);

    // 11. Debug/Analysis Endpoint
    register_rest_route($namespace, '/admin/debug', [
        'methods'             => 'GET',
        'callback'            => 'one_more_tree_rest_admin_debug',
        'permission_callback' => 'one_more_tree_rest_admin_permission',
    ]);

    // 12. CRUD Delete Endpoint
    register_rest_route($namespace, '/admin/data/delete', [
        'methods'             => 'POST',
        'callback'            => 'one_more_tree_rest_admin_data_delete',
        'permission_callback' => 'one_more_tree_rest_admin_permission',
    ]);

    // 13. Maintenance Endpoint (Direct mirror of /api/admin/maintenance)
    register_rest_route($namespace, '/admin/maintenance', [
        [
            'methods'             => 'GET',
            'callback'            => 'one_more_tree_rest_admin_maintenance_get',
            'permission_callback' => '__return_true',
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'one_more_tree_rest_admin_maintenance_post',
            'permission_callback' => 'one_more_tree_rest_admin_permission',
        ],
    ]);

    // 14. Under Construction Endpoint (Direct mirror of /api/admin/under-construction)
    register_rest_route($namespace, '/admin/under-construction', [
        [
            'methods'             => 'GET',
            'callback'            => 'one_more_tree_rest_admin_under_construction_get',
            'permission_callback' => '__return_true',
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'one_more_tree_rest_admin_under_construction_post',
            'permission_callback' => 'one_more_tree_rest_admin_permission',
        ],
    ]);
}
add_action('rest_api_init', 'one_more_tree_register_rest_routes');

/**
 * Permission callback for admin data
 */
function one_more_tree_rest_admin_permission() {
    return current_user_can('manage_options') || current_user_can('edit_posts');
}

function one_more_tree_admin_data_key_allowed($key) {
    return in_array($key, [
        'home_counters',
        'impact_kpis',
        'hero',
        'mission',
        'about_hero',
        'about_values',
        'about_deliverables',
        'testimonials',
        'stories',
        'live_map',
        'faq',
        'donation_tiers',
        'donate_hero',
        'disclaimer',
        'social',
        'site_identity',
        'nav_labels',
        'footer',
        'volunteer_page',
        'contact_page',
        'contact_submissions',
        'newsletter_subscribers',
        'modules',
        'blog_settings',
        'team',
        'debug',
        'maintenance',
        'under_construction',
    ], true);
}

function one_more_tree_normalize_live_map($value) {
    $locations = is_array($value['locations'] ?? null) ? $value['locations'] : [];
    $used_ids = [];
    $normalized = [];

    foreach ($locations as $location) {
        if (!is_array($location)) continue;
        $name = sanitize_text_field($location['name'] ?? 'Project');
        $id = sanitize_title($location['id'] ?? '') ?: sanitize_title($name) ?: 'project';
        $base_id = $id;
        $suffix = 2;
        while (isset($used_ids[$id])) {
            $id = $base_id . '-' . $suffix++;
        }
        $used_ids[$id] = true;

        $coords = is_array($location['coords'] ?? null) ? $location['coords'] : [];
        $images = is_array($location['progressImages'] ?? null) ? $location['progressImages'] : [];
        $normalized_images = [];
        foreach ($images as $image) {
            if (!is_array($image)) continue;
            $normalized_images[] = [
                'title' => sanitize_text_field($image['title'] ?? ''),
                'description' => sanitize_textarea_field($image['description'] ?? ''),
                'image' => esc_url_raw($image['image'] ?? ($image['image_url'] ?? '')),
                'date' => sanitize_text_field($image['date'] ?? ''),
            ];
        }

        $normalized[] = [
            'id' => $id,
            'name' => $name,
            'region' => sanitize_text_field($location['region'] ?? ''),
            'description' => sanitize_textarea_field($location['description'] ?? ''),
            'coords' => [
                'x' => sanitize_text_field($coords['x'] ?? '50%'),
                'y' => sanitize_text_field($coords['y'] ?? '50%'),
            ],
            'treesPlanted' => max(0, (int) ($location['treesPlanted'] ?? 0)),
            'volunteers' => max(0, (int) ($location['volunteers'] ?? 0)),
            'status' => sanitize_text_field($location['status'] ?? ''),
            'progressImages' => $normalized_images,
        ];
    }

    return ['locations' => $normalized];
}

/**
 * Auth Me Endpoint
 */
function one_more_tree_rest_auth_me() {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        return rest_ensure_response([
            'authenticated'     => true,
            'username'          => $user->user_login,
            'displayName'       => $user->display_name,
            'email'             => $user->user_email,
            'roles'             => $user->roles,
            'mustResetPassword' => false,
        ]);
    }

    return rest_ensure_response([
        'authenticated' => false,
    ]);
}

/**
 * Auth Login Endpoint
 */
function one_more_tree_rest_auth_login($request) {
    $params = $request->get_json_params();
    $username = sanitize_text_field($params['username'] ?? '');
    $password = $params['password'] ?? '';

    if (empty($username) || empty($password)) {
        return new WP_Error('invalid_fields', 'Please provide both username and password.', ['status' => 400]);
    }

    $credentials = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true,
    ];

    $user = wp_signon($credentials, is_ssl());

    if (is_wp_error($user)) {
        return new WP_Error('auth_failed', 'Invalid username or password.', ['status' => 401]);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    return rest_ensure_response([
        'ok'                => true,
        'username'          => $user->user_login,
        'displayName'       => $user->display_name,
        'mustResetPassword' => false,
    ]);
}

/**
 * Auth Logout Endpoint
 */
function one_more_tree_rest_auth_logout() {
    wp_logout();
    return rest_ensure_response([
        'ok' => true,
    ]);
}

/**
 * Default Seed Data for Admin Keys
 */
function one_more_tree_get_default_admin_data($key) {
    $defaults = [
        'home_counters' => [
            'treesTracked'      => 1000000,
            'projectsActive'    => 50,
            'volunteersEngaged' => 10000,
            'regionsCovered'    => 4,
        ],
        'impact_kpis' => [
            'goalTarget'  => 1000000,
            'goalCurrent' => 847000,
            'kpis' => [
                ['label' => 'Regions Covered', 'value' => '4', 'sub' => 'Granada, Madrid, Valencia, Galicia'],
                ['label' => 'Projects Active', 'value' => '12', 'sub' => 'Active forest & urban sites'],
                ['label' => 'Tons CO2 Absorbed', 'value' => '4,850', 'sub' => 'Estimated lifetime absorption'],
                ['label' => 'Volunteers Engaged', 'value' => '2,420', 'sub' => 'Community participants'],
            ],
        ],
        'projects' => [
            'items' => [
                [
                    'id'          => 'sierra-nevada',
                    'title'       => 'Sierra Nevada Restoration',
                    'region'      => 'Granada, Andalusia',
                    'description' => 'High-altitude mountain forest recovery program planting native pine and oak.',
                    'trees'       => '42,500',
                    'status'      => 'Active',
                    'image'       => get_template_directory_uri() . '/assets/images/hero-forest.jpg',
                ],
                [
                    'id'          => 'madrid-urban',
                    'title'       => 'Madrid Urban Greening',
                    'region'      => 'Madrid, Central Spain',
                    'description' => 'Urban micro-forest corridors to combat heat islands and promote biodiversity.',
                    'trees'       => '18,900',
                    'status'      => 'Active',
                    'image'       => get_template_directory_uri() . '/assets/images/sapling.jpg',
                ],
                [
                    'id'          => 'wildfire-recovery',
                    'title'       => 'Wildfire Recovery Program',
                    'region'      => 'Valencia & Galicia',
                    'description' => 'Restoring critical soil health and native tree canopy in fire-damaged landscapes.',
                    'trees'       => '65,200',
                    'status'      => 'In Progress',
                    'image'       => get_template_directory_uri() . '/assets/images/mature-tree.jpg',
                ],
            ],
        ],
        'live_map' => [
            'locations' => [
                [
                    'id'             => 'sierra-nevada',
                    'name'           => 'Sierra Nevada Restoration',
                    'region'         => 'Granada, Andalusia',
                    'description'    => 'Mountain forest restoration project',
                    'coords'         => ['x' => '35%', 'y' => '75%'],
                    'treesPlanted'   => 42500,
                    'volunteers'     => 850,
                    'progressImages' => [
                        ['title' => 'Project Start - Bare Ground', 'description' => 'Initial area assessment and preparation', 'image' => get_template_directory_uri() . '/assets/images/seedling.jpg', 'date' => 'Jan 2023'],
                        ['title' => '3 Months Progress', 'description' => 'Seedlings planted and first growth visible', 'image' => get_template_directory_uri() . '/assets/images/sapling.jpg', 'date' => 'Apr 2023'],
                        ['title' => '6 Months Progress', 'description' => 'Strong root development and leaf growth', 'image' => get_template_directory_uri() . '/assets/images/mature-tree.jpg', 'date' => 'Jul 2023'],
                        ['title' => 'Current Status', 'description' => 'Thriving young forest with 95% survival rate', 'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg', 'date' => 'May 2026'],
                    ],
                ],
                [
                    'id'             => 'madrid-urban',
                    'name'           => 'Madrid Urban Greening',
                    'region'         => 'Madrid, Central Spain',
                    'description'    => 'Urban forest and green space initiative',
                    'coords'         => ['x' => '48%', 'y' => '50%'],
                    'treesPlanted'   => 18900,
                    'volunteers'     => 320,
                    'progressImages' => [
                        ['title' => 'Site Preparation', 'description' => 'Urban park layout and soil preparation', 'image' => get_template_directory_uri() . '/assets/images/seedling.jpg', 'date' => 'Feb 2024'],
                        ['title' => '2 Months In', 'description' => 'Young trees installed and staked', 'image' => get_template_directory_uri() . '/assets/images/sapling.jpg', 'date' => 'Apr 2024'],
                        ['title' => 'Current Growth', 'description' => 'Canopy development and community engagement', 'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg', 'date' => 'May 2026'],
                    ],
                ],
                [
                    'id'             => 'wildfire-recovery',
                    'name'           => 'Wildfire Recovery Program',
                    'region'         => 'Various Regions',
                    'description'    => 'Restoring fire-damaged forest areas',
                    'coords'         => ['x' => '42%', 'y' => '60%'],
                    'treesPlanted'   => 65200,
                    'volunteers'     => 1250,
                    'progressImages' => [
                        ['title' => 'Fire Damage Site', 'description' => 'Assessing burn damage and recovery needs', 'image' => get_template_directory_uri() . '/assets/images/seedling.jpg', 'date' => 'Mar 2023'],
                        ['title' => 'Clearing & Preparation', 'description' => 'Removing charred wood and preparing soil', 'image' => get_template_directory_uri() . '/assets/images/sapling.jpg', 'date' => 'Jun 2023'],
                        ['title' => 'Planting Phase', 'description' => 'Installing native tree species', 'image' => get_template_directory_uri() . '/assets/images/mature-tree.jpg', 'date' => 'Sep 2023'],
                        ['title' => 'Recovery Progress', 'description' => 'Forest regeneration underway with excellent growth', 'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg', 'date' => 'May 2026'],
                    ],
                ],
            ],
        ],
        'team' => [
            'members' => [
                ['name' => 'Elena Morales', 'role' => 'Head of Ecology & Native Forestry', 'experience' => '15+ yrs restoration', 'image' => ''],
                ['name' => 'David Gomez', 'role' => 'Operations & Community Stewardship', 'experience' => 'Camino route lead', 'image' => ''],
                ['name' => 'Lucia Fernandez', 'role' => 'Field Research & Data Lead', 'experience' => 'Survival tracking', 'image' => ''],
                ['name' => 'Carlos Ruiz', 'role' => 'Local Volunteer Coordinator', 'experience' => '500+ volunteers lead', 'image' => ''],
            ],
        ],
        'stories' => [
            'seed' => [
                'chapter'  => 'Chapter One',
                'title'    => 'It Starts With a Single Seed',
                'body1'    => 'Beneath the soil, magic happens. A tiny seed, no larger than a grain of rice, holds within it the blueprint for a towering forest giant.',
                'body2'    => 'With patience, care, and the right conditions, life emerges. This is where our journey begins.',
                'badge'    => 'Day 1',
                'subbadge' => 'The beginning',
            ],
            'growth' => [
                'chapter'  => 'Chapter Two',
                'title'    => 'Growing Through Adversity',
                'body1'    => 'Young saplings brave harsh winds, scorching summers, and winter frost. In community, roots intertwine beneath the surface, sharing nutrients and strength.',
                'body2'    => 'Careful irrigation and local stewardship protect vulnerable seedlings during critical growth years.',
                'badge'    => 'Year 2',
                'subbadge' => 'Taking root',
            ],
            'mature' => [
                'chapter'  => 'Chapter Three',
                'title'    => 'A Legacy That Lives On',
                'body1'    => 'Decades later, a thriving canopy shelters diverse wildlife, enriches soil, and cools local microclimates.',
                'body2'    => 'Restored woodlands become gathering grounds, teaching centers, and carbon sinks for generations to come.',
                'badge'    => 'Year 10+',
                'subbadge' => 'Thriving Forest',
            ],
        ],
        'faq' => [
            'faqs' => [
                ['q' => 'Do you plant native species only?', 'a' => 'Yes. We focus on native species suited to the local ecosystem to support biodiversity and long-term survival.'],
                ['q' => 'How do you measure impact?', 'a' => 'We track survival rates and development over time, and we publish updates so partners and supporters can see progress.'],
                ['q' => 'Can communities participate?', 'a' => 'Absolutely. We work with local leaders and training partners to support planting, maintenance, and stewardship.'],
                ['q' => 'How can I get involved?', 'a' => 'Donate, volunteer, or partner with us. Use the "Join the Movement" section to take your next step.'],
                ['q' => 'Are donations tax-deductible?', 'a' => 'Yes, OneMoreTree is a registered non-profit organization. Tax certificates and receipts are available upon request.'],
                ['q' => 'Where are your planting locations located in Spain?', 'a' => 'We have active planting and restoration sites across Sierra Nevada in Granada, Madrid Urban greening corridors, wildfire recovery zones in Valencia, and along the Camino de Santiago route.'],
            ],
        ],
        'donation_tiers' => [
            'tiers' => [
                ['amount' => 25, 'title' => '$25 support', 'description' => 'Supports tree planting, site care, and local communities.', 'trees' => 75, 'volunteers' => 3],
                ['amount' => 50, 'title' => '$50 support', 'description' => 'Expands native tree coverage and regional biodiversity.', 'trees' => 150, 'volunteers' => 5],
                ['amount' => 100, 'title' => '$100 support', 'description' => 'Funds full plot restoration, native saplings, and 5-year monitoring.', 'trees' => 300, 'volunteers' => 10],
            ],
        ],
        'social' => [
            'instagram' => 'https://instagram.com/onemoretree',
            'twitter'   => 'https://twitter.com/onemoretree',
            'facebook'  => 'https://facebook.com/onemoretree',
            'linkedin'  => 'https://linkedin.com/company/onemoretree',
        ],
        'disclaimer' => [
            'enabled' => true,
            'message' => '🌳 OneMoreTree NGO is a volunteer-led initiative. All donations go directly to tree planting, site restoration, and community programs.',
        ],
        'modules' => [
            'home_hero'        => true,
            'home_counters'    => true,
            'home_story'       => true,
            'home_donation'    => true,
            'home_testimonials'=> true,
            'live_map'         => true,
            'impact_story'     => true,
            'faq_section'      => true,
        ],
        'contact_submissions' => [],
        'newsletter_subscribers' => [],
        'hero' => [
            'mission_badge'       => 'Planting trees, restoring forests.',
            'headline_top'        => 'One More',
            'headline_emphasis'   => 'Tree',
            'subtitle'            => 'Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.',
            'primary_cta_text'    => 'Join the Movement',
            'primary_cta_url'     => '/get-involved',
            'secondary_cta_text'  => 'Watch the Journey',
            'hero_image_url'      => '',
            'stat1_value'         => '1M',
            'stat1_label'         => 'Trees Planted',
            'stat2_value'         => '50+',
            'stat2_label'         => 'Projects',
            'stat3_value'         => '10K+',
            'stat3_label'         => 'Volunteers',
        ],
        'mission' => [
            'badge'    => 'How We Work',
            'headline' => 'Three Pillars of Impact',
            'subtitle' => 'Our approach combines ecological expertise with community power to create forests that last.',
            'card1_icon'  => '🌱',
            'card1_title' => 'Europe First',
            'card1_desc'  => 'Starting where the need is greatest — degraded lands across Spain and beyond.',
            'card2_icon'  => '🤝',
            'card2_title' => 'Community Power',
            'card2_desc'  => 'Working hand-in-hand with local volunteers, schools, and stewardship groups.',
            'card3_icon'  => '📊',
            'card3_title' => 'Visible Progress',
            'card3_desc'  => 'Tracking every tree, sharing results openly, earning trust through transparency.',
        ],
        'about_hero' => [
            'badge'    => 'Who We Are',
            'title'    => 'Rooted in Purpose',
            'subtitle' => 'We are a volunteer-led organization committed to restoring native forests across Spain, one tree at a time.',
        ],
        'about_values' => [
            'badge'    => 'Our Principles',
            'headline' => 'What Guides Us',
            'cards'    => [
                ['icon' => '🌱', 'title' => 'Start with seeds', 'desc' => 'Every great forest begins small. We plant with patience and intention.'],
                ['icon' => '🌿', 'title' => 'Respect ecosystems', 'desc' => 'Native species only. We work with nature, not against it.'],
                ['icon' => '📅', 'title' => 'Commit for the long run', 'desc' => 'Forests take decades. We plan and monitor accordingly.'],
                ['icon' => '🤝', 'title' => 'Work with communities', 'desc' => 'Local stewardship is the key to long-term survival.'],
            ],
        ],
        'about_deliverables' => [
            'box_title' => 'What We Deliver',
            'box_desc'  => 'Measurable outcomes that help regenerate ecosystems and empower communities.',
            'stat1_val'   => '3M',
            'stat1_label' => 'Trees Goal',
            'stat2_val'   => '100%',
            'stat2_label' => 'Native Species',
            'stat3_val'   => '12',
            'stat3_label' => 'Active Sites',
        ],
        'testimonials' => [
            'badge'    => 'Community Voices',
            'headline' => 'What People Say',
            'subtitle' => 'From volunteers and partners across Spain.',
            'cta_text' => 'Join the movement →',
            'items'    => [
                ['quote' => 'Planting with OneMoreTree changed how I see the land around me.', 'name' => 'María J.', 'role' => 'Volunteer, Granada'],
                ['quote' => 'The transparency and follow-up reports make it easy to trust the work.', 'name' => 'David K.', 'role' => 'Corporate Partner, Madrid'],
                ['quote' => 'Our school planted 50 trees. The kids still visit them every year.', 'name' => 'Pilar R.', 'role' => 'Teacher, Valencia'],
            ],
        ],
        'impact_hero' => [
            'badge'    => 'Our Impact',
            'title'    => 'Trees That Transform',
            'subtitle' => 'Quantifying the change we\'re creating across Spain\'s forests.',
        ],
        'impact_goal' => [
            'title'          => '1,000,000 Trees by 2040',
            'target'         => 1000000,
            'current'        => 847000,
            'remaining_note' => '153,000 trees left to plant',
        ],
        'impact_stories' => [
            'seed' => [
                'chapter'  => 'Chapter One',
                'badge'    => 'Day 1',
                'subbadge' => 'The beginning',
                'title'    => 'It Starts With a Single Seed',
                'body1'    => 'Beneath the soil, magic happens. A tiny seed, no larger than a grain of rice, holds within it the blueprint for a towering forest giant.',
                'body2'    => 'With patience, care, and the right conditions, life emerges. This is where our journey begins.',
                'image_url' => '',
            ],
            'growth' => [
                'chapter'  => 'Chapter Two',
                'badge'    => 'Year 2',
                'subbadge' => 'Taking root',
                'title'    => 'Growing Through Adversity',
                'body1'    => 'Young saplings brave harsh winds, scorching summers, and winter frost. In community, roots intertwine beneath the surface.',
                'body2'    => 'Careful irrigation and local stewardship protect vulnerable seedlings during critical growth years.',
                'image_url' => '',
            ],
            'mature' => [
                'chapter'  => 'Chapter Three',
                'badge'    => 'Year 10+',
                'subbadge' => 'Thriving Forest',
                'title'    => 'A Legacy That Lives On',
                'body1'    => 'Decades later, a thriving canopy shelters diverse wildlife, enriches soil, and cools local microclimates.',
                'body2'    => 'Restored woodlands become gathering grounds, teaching centers, and carbon sinks for generations to come.',
                'image_url' => '',
            ],
        ],
        'donate_hero' => [
            'badge'    => 'Make a Difference',
            'title'    => 'Plant Your Legacy',
            'subtitle' => 'Every euro you donate plants real trees in real forests across Spain.',
        ],
        'volunteer_page' => [
            'hero_badge'    => 'Get Involved',
            'hero_title'    => 'Volunteer With Us',
            'hero_subtitle' => 'Join our growing community of hands-on environmental stewards.',
            'form_title'    => 'Sign Up to Volunteer',
            'form_subtitle' => 'Tell us how you\'d like to help and we\'ll match you to a planting day near you.',
            'interest_options' => "Tree Planting Day\nSite Maintenance & Stewardship\nCommunity Education\nAdministration & Communications\nFundraising & Partnerships",
            'card1_icon'  => '🌳',
            'card1_title' => 'Planting Days',
            'card1_desc'  => 'Join weekend planting events at our active sites across Spain.',
            'card2_icon'  => '🌿',
            'card2_title' => 'Community Stewardship',
            'card2_desc'  => 'Help maintain and monitor trees over the years after planting.',
            'card3_icon'  => '📍',
            'card3_title' => 'Local Coordination',
            'card3_desc'  => 'Help organize events in your city or region.',
        ],
        'contact_page' => [
            'hero_badge'    => 'Get in Touch',
            'hero_title'    => 'We\'d Love to Hear From You',
            'hero_subtitle' => 'Whether you want to volunteer, partner, or just learn more — reach out.',
            'form_title'    => 'Send a Message',
            'form_subtitle' => 'We typically respond within 24–48 hours.',
            'btn_text'      => 'Send Message',
        ],
        'site_identity' => [
            'site_title' => 'One More Tree',
            'tagline'    => 'Planting 1 million trees across Spain by 2040.',
            'logo_url'   => '',
            'favicon_url'=> '',
        ],
        'nav_labels' => [
            'home'        => 'Home',
            'about'       => 'About',
            'projects'    => 'Projects',
            'impact'      => 'Impact',
            'faq'         => 'FAQ',
            'get_involved'=> 'Get Involved',
            'volunteer'   => 'Volunteer',
            'contact'     => 'Contact',
            'donate'      => 'Donate',
            'cta_text'    => 'Donate Now',
            'cta_url'     => '/donate',
        ],
        'footer' => [
            'tagline'      => 'Planting trees, restoring forests.',
            'copyright'    => '© 2024 OneMoreTree NGO. All rights reserved.',
            'policy_links' => [
                ['label' => 'Privacy Policy', 'url' => '/privacy'],
                ['label' => 'Cookie Policy', 'url' => '/cookies'],
                ['label' => 'Terms of Use', 'url' => '/terms'],
            ],
        ],
        'blog_settings' => [
            'reading_progress_bar' => true,
            'toc_enabled'          => true,
            'social_share'         => true,
            'default_author_bio'   => 'Written by the OneMoreTree team.',
        ],
        'volunteer_submissions' => [],
        'maintenance' => false,
        'under_construction' => false,
    ];

    return $defaults[$key] ?? null;
}

/**
 * Admin Data Get Endpoint
 */
function one_more_tree_rest_admin_data_get($request) {
    $key = sanitize_key($request->get_param('key'));
    if (empty($key)) {
        return new WP_Error('missing_key', 'Key parameter is required.', ['status' => 400]);
    }
    if (!one_more_tree_admin_data_key_allowed($key)) {
        return new WP_Error('invalid_key', 'This administrator data key is not available.', ['status' => 400]);
    }

    $option_name = 'omt_admin_' . $key;
    $stored = get_option($option_name, null);

    if ($stored === null) {
        $value = one_more_tree_get_default_admin_data($key);
    } else {
        $value = is_string($stored) ? json_decode($stored, true) : $stored;
    }

    return rest_ensure_response([
        'ok'    => true,
        'key'   => $key,
        'value' => $value,
    ]);
}

/**
 * Admin Data Post Endpoint
 */
function one_more_tree_rest_admin_data_post($request) {
    $params = $request->get_json_params();
    $key = sanitize_key($params['key'] ?? '');
    $value = $params['value'] ?? null;

    if (empty($key)) {
        return new WP_Error('missing_key', 'Key parameter is required.', ['status' => 400]);
    }
    if (!one_more_tree_admin_data_key_allowed($key)) {
        return new WP_Error('invalid_key', 'This administrator data key is not available.', ['status' => 400]);
    }

    $option_name = 'omt_admin_' . $key;
    if ($key === 'live_map') {
        $value = one_more_tree_normalize_live_map($value);
    }
    if ($key === 'maintenance' || $key === 'under_construction') {
        $value = (bool)$value;
    }
    update_option($option_name, $value);

    return rest_ensure_response([
        'ok'      => true,
        'savedAt' => current_time('mysql'),
    ]);
}

/**
 * Public Contact Form Submission
 */
function one_more_tree_rest_public_contact($request) {
    $params = $request->get_json_params() ?: $request->get_params();
    $name = sanitize_text_field($params['name'] ?? '');
    $email = sanitize_email($params['email'] ?? '');
    $message = sanitize_textarea_field($params['message'] ?? '');
    $interest = sanitize_text_field($params['interest'] ?? 'General Contact');

    if (empty($name) || empty($email) || (empty($message) && empty($interest))) {
        return new WP_Error('missing_fields', 'Please fill out all required fields.', ['status' => 400]);
    }

    $submissions = get_option('omt_admin_contact_submissions', []);
    if (!is_array($submissions)) $submissions = [];

    $new_submission = [
        'id'        => uniqid('sub_'),
        'name'      => $name,
        'email'     => $email,
        'message'   => $message ?: "Interest: {$interest}",
        'interest'  => $interest,
        'date'      => current_time('mysql'),
        'timestamp' => time(),
        'status'    => 'unread',
    ];

    array_unshift($submissions, $new_submission);
    // Keep last 500
    $submissions = array_slice($submissions, 0, 500);
    update_option('omt_admin_contact_submissions', $submissions);

    return rest_ensure_response([
        'ok'      => true,
        'message' => 'Thank you for reaching out! We will contact you soon.',
    ]);
}

/**
 * Public Newsletter Submission
 */
function one_more_tree_rest_public_newsletter($request) {
    $params = $request->get_json_params() ?: $request->get_params();
    $email = sanitize_email($params['email'] ?? '');

    if (empty($email) || !is_email($email)) {
        return new WP_Error('invalid_email', 'Please provide a valid email address.', ['status' => 400]);
    }

    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    if (!is_array($subscribers)) $subscribers = [];

    // Check if already subscribed
    $exists = false;
    foreach ($subscribers as $s) {
        if (isset($s['email']) && strtolower($s['email']) === strtolower($email)) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        array_unshift($subscribers, [
            'id'        => uniqid('nl_'),
            'email'     => $email,
            'date'      => current_time('mysql'),
            'timestamp' => time(),
            'status'    => 'active',
        ]);
        $subscribers = array_slice($subscribers, 0, 1000);
        update_option('omt_admin_newsletter_subscribers', $subscribers);
    }

    return rest_ensure_response([
        'ok'      => true,
        'message' => 'Thanks for subscribing! You will receive our latest updates.',
    ]);
}

function one_more_tree_rest_public_volunteer($request) {
    $params = $request->get_json_params() ?: $request->get_params();
    $name = sanitize_text_field($params['name'] ?? '');
    $email = sanitize_email($params['email'] ?? '');
    $interest = sanitize_text_field($params['interest'] ?? '');
    $message = sanitize_textarea_field($params['message'] ?? '');

    if (empty($name) || empty($email)) {
        return new WP_Error('missing_fields', 'Please fill out all required fields.', ['status' => 400]);
    }

    $submissions = get_option('omt_admin_contact_submissions', []);
    if (!is_array($submissions)) $submissions = [];
    array_unshift($submissions, [
        'id'        => uniqid('vol_'),
        'name'      => $name,
        'email'     => $email,
        'message'   => $message ?: "Volunteer Interest: {$interest}",
        'interest'  => $interest,
        'type'      => 'volunteer',
        'date'      => current_time('mysql'),
        'timestamp' => time(),
        'status'    => 'unread',
    ]);
    $submissions = array_slice($submissions, 0, 500);
    update_option('omt_admin_contact_submissions', $submissions);
    return rest_ensure_response(['ok' => true, 'message' => 'Thank you! We will be in touch about volunteer opportunities.']);
}

function one_more_tree_rest_admin_contact_delete($request) {
    $params = $request->get_json_params();
    $id = sanitize_text_field($params['id'] ?? '');
    if (empty($id)) return new WP_Error('missing_id', 'Submission ID required.', ['status' => 400]);
    $submissions = get_option('omt_admin_contact_submissions', []);
    $submissions = array_values(array_filter($submissions, fn($s) => ($s['id'] ?? '') !== $id));
    update_option('omt_admin_contact_submissions', $submissions);
    return rest_ensure_response(['ok' => true]);
}

function one_more_tree_rest_admin_newsletter_delete($request) {
    $params = $request->get_json_params();
    $id = sanitize_text_field($params['id'] ?? '');
    if (empty($id)) return new WP_Error('missing_id', 'Subscriber ID required.', ['status' => 400]);
    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    $subscribers = array_values(array_filter($subscribers, fn($s) => ($s['id'] ?? '') !== $id));
    update_option('omt_admin_newsletter_subscribers', $subscribers);
    return rest_ensure_response(['ok' => true]);
}

function one_more_tree_rest_admin_newsletter_add($request) {
    $params = $request->get_json_params();
    $email = sanitize_email($params['email'] ?? '');
    if (empty($email) || !is_email($email)) return new WP_Error('invalid_email', 'Valid email required.', ['status' => 400]);
    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    if (!is_array($subscribers)) $subscribers = [];
    foreach ($subscribers as $s) { if (strtolower($s['email'] ?? '') === strtolower($email)) return rest_ensure_response(['ok' => true, 'message' => 'Already subscribed.']); }
    array_unshift($subscribers, ['id' => uniqid('nl_'), 'email' => $email, 'date' => current_time('mysql'), 'timestamp' => time(), 'status' => 'active']);
    update_option('omt_admin_newsletter_subscribers', $subscribers);
    return rest_ensure_response(['ok' => true, 'message' => 'Subscriber added.']);
}

/**
 * Debug/Analysis Endpoint
 */
function one_more_tree_rest_admin_debug($request) {
    global $wpdb;

    $analysis = [
        'availability' => [
            'site_status' => [
                'is_active' => get_option('blog_public') === '1',
                'maintenance_mode' => (bool)get_option('omt_admin_maintenance', false) || wp_is_maintenance_mode(),
                'under_construction' => (bool)get_option('omt_admin_under_construction', false),
                'wp_debug' => defined('WP_DEBUG') && WP_DEBUG,
            ],
            'theme_status' => [
                'active_theme' => get_stylesheet(),
                'theme_version' => wp_get_theme()->get('Version'),
                'theme_support' => [
                    'custom_logo' => current_theme_supports('custom-logo'),
                    'post-thumbnails' => current_theme_supports('post-thumbnails'),
                ],
            ],
            'plugin_status' => [
                'active_plugins' => get_option('active_plugins', []),
                'acf_active' => is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php'),
            ],
        ],
        'security' => [
            'user_capabilities' => [
                'current_user_id' => get_current_user_id(),
                'current_user_roles' => wp_get_current_user()->roles,
                'manage_options' => current_user_can('manage_options'),
                'edit_posts' => current_user_can('edit_posts'),
            ],
            'api_security' => [
                'rest_authentication' => rest_authorization_required_code(),
                'nonce_available' => wp_verify_nonce(wp_create_nonce('wp_rest'), 'wp_rest'),
            ],
            'https_status' => [
                'is_ssl' => is_ssl(),
                'force_ssl' => get_option('force_ssl_admin'),
            ],
        ],
        'data_metadata' => [
            'options_table' => [
                'omt_options_count' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE 'omt_%'"),
                'omt_options_size' => $wpdb->get_var("SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE option_name LIKE 'omt_%'"),
            ],
            'admin_data_keys' => [
                'home_counters' => get_option('omt_admin_home_counters'),
                'hero' => get_option('omt_admin_hero'),
                'live_map' => get_option('omt_admin_live_map'),
                'impact_kpis' => get_option('omt_admin_impact_kpis'),
                'contact_submissions' => get_option('omt_admin_contact_submissions'),
                'newsletter_subscribers' => get_option('omt_admin_newsletter_subscribers'),
            ],
            'acf_fields_status' => [
                'acf_installed' => class_exists('ACF'),
                'acf_groups_count' => function_exists('acf_get_field_groups') ? count(acf_get_field_groups()) : 0,
            ],
        ],
        'counts' => [
            'data' => [
                'total_admin_options' => count(get_option('omt_admin_home_counters')) + count(get_option('omt_admin_hero')) + count(get_option('omt_admin_live_map')),
                'contact_submissions' => count(get_option('omt_admin_contact_submissions', [])),
                'newsletter_subscribers' => count(get_option('omt_admin_newsletter_subscribers', [])),
                'live_map_locations' => count(get_option('omt_admin_live_map')['locations'] ?? []),
            ],
            'info' => [
                'posts_count' => wp_count_posts()->publish,
                'pages_count' => wp_count_posts('page')->publish,
                'users_count' => count_users()['total_users'],
            ],
            'sections' => [
                'registered_pages' => ['home', 'about', 'projects', 'impact', 'faq', 'get-involved', 'volunteer', 'contact', 'donate'],
                'acf_templates' => ['front-page', 'page-about', 'page-projects', 'page-impact', 'page-faq', 'page-get-involved', 'page-volunteer', 'page-contact', 'page-donate'],
            ],
            'earnings' => [
                'donation_tiers' => count(get_option('omt_admin_donation_tiers', [])),
                'note' => 'Earnings tracking not implemented - donation data available via tiers',
            ],
        ],
        'concurrency' => [
            'active_sessions' => [
                'current_time' => current_time('mysql'),
                'timestamp' => time(),
                'user_session' => wp_get_session_token(),
            ],
            'database_connections' => [
                'wpdb_state' => method_exists($wpdb, 'db_connect') ? 'connected' : 'unknown',
                'query_count' => $wpdb->num_queries,
            ],
            'cache_status' => [
                'object_cache' => wp_using_ext_object_cache(),
                'page_cache' => wp_cache_get('page_cache_check') !== false,
            ],
        ],
        'health_check' => [
            'critical_issues' => [],
            'warnings' => [],
            'recommendations' => [],
        ],
    ];

    // Health checks
    if ($analysis['availability']['theme_status']['active_theme'] !== 'one-more-tree') {
        $analysis['health_check']['critical_issues'][] = 'One More Tree theme not active';
    }
    if (!$analysis['security']['https_status']['is_ssl']) {
        $analysis['health_check']['warnings'][] = 'Site not using HTTPS';
    }
    if (!$analysis['availability']['plugin_status']['acf_active']) {
        $analysis['health_check']['warnings'][] = 'ACF plugin not active';
    }

    return rest_ensure_response([
        'ok' => true,
        'timestamp' => current_time('mysql'),
        'analysis' => $analysis,
    ]);
}

/**
 * CRUD Delete Endpoint
 */
function one_more_tree_rest_admin_data_delete($request) {
    $params = $request->get_json_params();
    $key = sanitize_key($params['key'] ?? '');

    if (empty($key)) {
        return new WP_Error('missing_key', 'Key parameter is required.', ['status' => 400]);
    }
    if (!one_more_tree_admin_data_key_allowed($key)) {
        return new WP_Error('invalid_key', 'This administrator data key is not available for deletion.', ['status' => 400]);
    }

    $option_name = 'omt_admin_' . $key;
    $deleted = delete_option($option_name);

    if ($deleted) {
        return rest_ensure_response([
            'ok' => true,
            'message' => "Deleted {$key} successfully",
            'deletedAt' => current_time('mysql'),
        ]);
    } else {
        return new WP_Error('delete_failed', "Failed to delete {$key} or it does not exist.", ['status' => 400]);
    }
}

/**
 * Dedicated Maintenance GET/POST Callbacks
 */
function one_more_tree_rest_admin_maintenance_get() {
    $enabled = (bool)get_option('omt_admin_maintenance', false);
    return rest_ensure_response(['ok' => true, 'enabled' => $enabled]);
}

function one_more_tree_rest_admin_maintenance_post($request) {
    $params = $request->get_json_params();
    $enabled = isset($params['enabled']) ? (bool)$params['enabled'] : false;
    update_option('omt_admin_maintenance', $enabled);
    return rest_ensure_response(['ok' => true, 'enabled' => $enabled]);
}

/**
 * Dedicated Under Construction GET/POST Callbacks
 */
function one_more_tree_rest_admin_under_construction_get() {
    $enabled = (bool)get_option('omt_admin_under_construction', false);
    return rest_ensure_response(['ok' => true, 'enabled' => $enabled]);
}

function one_more_tree_rest_admin_under_construction_post($request) {
    $params = $request->get_json_params();
    $enabled = isset($params['enabled']) ? (bool)$params['enabled'] : false;
    update_option('omt_admin_under_construction', $enabled);
    return rest_ensure_response(['ok' => true, 'enabled' => $enabled]);
}

