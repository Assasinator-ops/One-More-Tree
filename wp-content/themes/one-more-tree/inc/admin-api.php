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
}
add_action('rest_api_init', 'one_more_tree_register_rest_routes');

/**
 * Permission callback for admin data
 */
function one_more_tree_rest_admin_permission() {
    return current_user_can('manage_options') || current_user_can('edit_posts');
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

    $option_name = 'omt_admin_' . $key;
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
