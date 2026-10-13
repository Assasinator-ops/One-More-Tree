<?php
/**
 * One More Tree functions and definitions
 *
 * Pixel-perfect classic PHP theme implementation mirroring Next.js architecture.
 *
 * @package One_More_Tree
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

/**
 * Theme setup
 */
function one_more_tree_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'one-more-tree'),
        'footer'  => __('Footer Menu', 'one-more-tree'),
    ]);

    // Switch default core markup for search form, comment form, and comments
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Add support for core custom logo
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Register Projects Custom Post Type
    register_post_type('project', [
        'labels' => [
            'name'               => __('Projects', 'one-more-tree'),
            'singular_name'      => __('Project', 'one-more-tree'),
            'add_new_item'       => __('Add New Project', 'one-more-tree'),
            'edit_item'          => __('Edit Project', 'one-more-tree'),
            'all_items'          => __('All Projects', 'one-more-tree'),
            'menu_name'          => __('Projects', 'one-more-tree'),
        ],
        'public'              => true,
        'show_in_rest'        => true,
        'has_archive'         => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-palmtree',
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'],
        // Keep the page slug /projects reserved for the public projects template.
        'rewrite'             => ['slug' => 'project'],
    ]);

    // Gutenberg block theme supports
    add_theme_support('block-templates');
    add_theme_support('block-template-parts');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style([
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600;1,700&display=swap',
        'assets/css/main.css',
        'assets/css/block-patterns.css',
        'assets/css/block-styles.css',
        'assets/css/wp-containers.css',
    ]);
}
add_action('after_setup_theme', 'one_more_tree_setup');

/**
 * Set the content width in pixels
 */
function one_more_tree_content_width() {
    $GLOBALS['content_width'] = apply_filters('one_more_tree_content_width', 1200);
}
add_action('after_setup_theme', 'one_more_tree_content_width', 0);

/**
 * Enqueue scripts and styles
 */
function one_more_tree_scripts() {
    // Enqueue Google Fonts
    wp_enqueue_style(
        'one-more-tree-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600;1,700&display=swap',
        [],
        null
    );

    // Enqueue compiled Tailwind CSS (Single authoritative stylesheet)
    wp_enqueue_style(
        'one-more-tree-style',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        _S_VERSION
    );

    // Enqueue block patterns CSS for Gutenberg support
    wp_enqueue_style(
        'one-more-tree-block-patterns',
        get_template_directory_uri() . '/assets/css/block-patterns.css',
        [],
        _S_VERSION
    );

    // Enqueue block styles CSS
    wp_enqueue_style(
        'one-more-tree-block-styles',
        get_template_directory_uri() . '/assets/css/block-styles.css',
        [],
        _S_VERSION
    );

    // Enqueue WordPress container and block styling
    wp_enqueue_style(
        'one-more-tree-wp-containers',
        get_template_directory_uri() . '/assets/css/wp-containers.css',
        ['one-more-tree-style'],
        _S_VERSION
    );

    // Remove WordPress emoji scripts and styles
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Enqueue GSAP library + ScrollTrigger
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
        [],
        '3.12.5',
        true
    );

    wp_enqueue_script(
        'gsap-scroll-trigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
        ['gsap'],
        '3.12.5',
        true
    );

    // Enqueue main interactive script
    wp_enqueue_script(
        'one-more-tree-main',
        get_template_directory_uri() . '/assets/js/main.js',
        ['gsap', 'gsap-scroll-trigger'],
        _S_VERSION,
        true
    );

    // Enqueue navigation pattern script for Gutenberg navigation
    wp_enqueue_script(
        'one-more-tree-navigation-pattern',
        get_template_directory_uri() . '/assets/js/navigation-pattern.js',
        [],
        _S_VERSION,
        true
    );

    // Localize script data
    wp_localize_script('one-more-tree-main', 'omtData', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'restUrl'   => esc_url_raw(rest_url('omt/v1/')),
        'nonce'     => wp_create_nonce('omt-nonce'),
        'restNonce' => wp_create_nonce('wp_rest'),
        'homeUrl'   => home_url('/'),
    ]);
}
add_action('wp_enqueue_scripts', 'one_more_tree_scripts', 100);

/**
 * Enqueue scripts and styles into Gutenberg block editor canvas
 */
function one_more_tree_block_editor_assets() {
    wp_enqueue_style(
        'one-more-tree-editor-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600;1,700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'one-more-tree-editor-tailwind',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        _S_VERSION
    );

    wp_enqueue_style(
        'one-more-tree-editor-patterns',
        get_template_directory_uri() . '/assets/css/block-patterns.css',
        [],
        _S_VERSION
    );

    wp_enqueue_style(
        'one-more-tree-editor-block-styles',
        get_template_directory_uri() . '/assets/css/block-styles.css',
        [],
        _S_VERSION
    );

    wp_enqueue_style(
        'one-more-tree-editor-wp-containers',
        get_template_directory_uri() . '/assets/css/wp-containers.css',
        [],
        _S_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'one_more_tree_block_editor_assets');

/**
 * Remove unwanted generator and manifest headers
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');



/**
 * Load template tags & helper functions
 */
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/template-functions.php';
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/admin-api.php';
require_once get_template_directory() . '/inc/wp-admin-settings.php';
require_once get_template_directory() . '/inc/admin-debug-page.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/block-patterns.php';
require_once get_template_directory() . '/inc/full-page-patterns.php';

// Register block style variations
function one_more_tree_register_block_styles() {
    // Group block styles
    register_block_style('core/group', [
        'name'  => 'glass-card',
        'label' => __('Glass Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'forest-gradient',
        'label' => __('Forest Gradient', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'stat-card',
        'label' => __('Stat Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'value-card',
        'label' => __('Value Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'donation-tier',
        'label' => __('Donation Tier', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'newsletter-card',
        'label' => __('Newsletter Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'counter-card',
        'label' => __('Counter Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'testimonial-card',
        'label' => __('Testimonial Card', 'one-more-tree'),
    ]);
    
    register_block_style('core/group', [
        'name'  => 'section-gradient',
        'label' => __('Section Gradient', 'one-more-tree'),
    ]);
    
    // Heading styles
    register_block_style('core/heading', [
        'name'  => 'hero-text',
        'label' => __('Hero Text', 'one-more-tree'),
    ]);
    
    // Button styles
    register_block_style('core/button', [
        'name'  => 'forest-button',
        'label' => __('Forest Button', 'one-more-tree'),
    ]);
    
    register_block_style('core/button', [
        'name'  => 'glass-button',
        'label' => __('Glass Button', 'one-more-tree'),
    ]);

    register_block_style('core/button', [
        'name'  => 'outline',
        'label' => __('Outline Button', 'one-more-tree'),
    ]);

    // Separator styles
    register_block_style('core/separator', [
        'name'  => 'forest-separator',
        'label' => __('Forest Separator', 'one-more-tree'),
    ]);
    
    // Column styles
    register_block_style('core/columns', [
        'name'  => 'value-grid',
        'label' => __('Value Grid', 'one-more-tree'),
    ]);

    register_block_style('core/column', [
        'name'  => 'value-card',
        'label' => __('Value Card', 'one-more-tree'),
    ]);

    // Paragraph styles
    register_block_style('core/paragraph', [
        'name'  => 'lead-text',
        'label' => __('Lead Text', 'one-more-tree'),
    ]);
    
    register_block_style('core/paragraph', [
        'name'  => 'small-text',
        'label' => __('Small Text', 'one-more-tree'),
    ]);
    
    // Separator styles
    register_block_style('core/separator', [
        'name'  => 'forest-separator',
        'label' => __('Forest Separator', 'one-more-tree'),
    ]);
    
    register_block_style('core/separator', [
        'name'  => 'gradient-separator',
        'label' => __('Gradient Separator', 'one-more-tree'),
    ]);
    
    // Quote styles
    register_block_style('core/quote', [
        'name'  => 'testimonial-quote',
        'label' => __('Testimonial Quote', 'one-more-tree'),
    ]);
    
    // List styles
    register_block_style('core/list', [
        'name'  => 'checkmark-list',
        'label' => __('Checkmark List', 'one-more-tree'),
    ]);

    // Group extra styles
    register_block_style('core/group', [
        'name'  => 'feature-card',
        'label' => __('Feature Card', 'one-more-tree'),
    ]);

    register_block_style('core/group', [
        'name'  => 'badge',
        'label' => __('Pill Badge', 'one-more-tree'),
    ]);

    // Columns extra styles
    register_block_style('core/columns', [
        'name'  => 'cards-grid',
        'label' => __('Cards Grid', 'one-more-tree'),
    ]);
}
add_action('init', 'one_more_tree_register_block_styles');

/**
 * Page Templates declaration
 */
function one_more_tree_page_templates($templates) {
    // Filter out backup folder entries
    foreach (array_keys((array)$templates) as $file) {
        if (strpos($file, '_block_editing_backup') !== false) {
            unset($templates[$file]);
        }
    }
    $custom = [
        'front-page.php'        => __('Home Page (PHP Template)', 'one-more-tree'),
        'page-about.php'        => __('About Page (PHP Template)', 'one-more-tree'),
        'page-projects.php'     => __('Projects Page (PHP Template)', 'one-more-tree'),
        'page-impact.php'       => __('Impact Page (PHP Template)', 'one-more-tree'),
        'page-faq.php'          => __('FAQ Page (PHP Template)', 'one-more-tree'),
        'page-get-involved.php' => __('Get Involved Page (PHP Template)', 'one-more-tree'),
        'page-volunteer.php'    => __('Volunteer Page (PHP Template)', 'one-more-tree'),
        'page-contact.php'      => __('Contact Page (PHP Template)', 'one-more-tree'),
        'page-donate.php'       => __('Donate Page (PHP Template)', 'one-more-tree'),
    ];
    return array_merge((array)$templates, $custom);
}
add_filter('theme_page_templates', 'one_more_tree_page_templates');

/**
 * Rewrite rules for Admin console at /administrator and /admin
 */
function one_more_tree_admin_rewrite_rules() {
    add_rewrite_rule('^administrator/?$', 'index.php?omt_administrator=1', 'top');
    add_rewrite_rule('^admin/?$', 'index.php?omt_administrator=1', 'top');
    add_rewrite_rule('^maintenance/?$', 'index.php?omt_maintenance_page=1', 'top');
}
add_action('init', 'one_more_tree_admin_rewrite_rules');

function one_more_tree_query_vars($vars) {
    $vars[] = 'omt_administrator';
    $vars[] = 'omt_maintenance_page';
    return $vars;
}
add_filter('query_vars', 'one_more_tree_query_vars');

/**
 * Maintenance Mode Interceptor
 * Serves 503 maintenance page when maintenance mode is active.
 * Allows logged-in administrators (God Mode) and exempt routes (/admin, wp-login, etc.)
 */
function one_more_tree_maintenance_interceptor() {
    $request_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    // 1. If someone explicitly visits /maintenance, serve the template
    if ($request_path === 'maintenance' || get_query_var('omt_maintenance_page')) {
        require get_template_directory() . '/maintenance.php';
        exit;
    }

    // 2. If administrator view, don't intercept
    if ($request_path === 'administrator' || $request_path === 'admin' || get_query_var('omt_administrator')) {
        return;
    }

    // 3. Exclude admin areas, wp-login, cron, CLI, REST API calls
    if (is_admin() || (defined('DOING_CRON') && DOING_CRON) || (defined('WP_CLI') && WP_CLI)) {
        return;
    }
    if (isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') {
        return;
    }
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    // 4. Check if maintenance mode is enabled
    $maintenance_enabled = (bool)get_option('omt_admin_maintenance', false);
    if (!$maintenance_enabled) {
        return;
    }

    // 5. God Mode / Admin bypass: authenticated admins can view the live site
    if (is_user_logged_in() && (current_user_can('manage_options') || current_user_can('edit_posts'))) {
        return;
    }

    // 6. Serve maintenance screen with 503 Service Unavailable
    status_header(503);
    nocache_headers();
    header('Retry-After: 3600');
    require get_template_directory() . '/maintenance.php';
    exit;
}
add_action('template_redirect', 'one_more_tree_maintenance_interceptor', 5);

function one_more_tree_admin_template_redirect() {
    if (get_query_var('omt_administrator')) {
        require get_template_directory() . '/inc/administrator-view.php';
        exit;
    }
}
add_action('template_redirect', 'one_more_tree_admin_template_redirect');

/**
 * HYBRID TEMPLATE ROUTING
 * Allows choosing between custom Gutenberg canvas templates and default PHP templates.
 * 1. If an explicit custom template is selected (e.g. Full Width Block Canvas), honor it.
 * 2. If default template is active, route core slugs to their respective PHP templates.
 * 3. Otherwise, use WordPress standard template hierarchy.
 */
add_filter('template_include', function($template) {
    global $post;

    if (!$post || !is_page()) {
        return $template;
    }

    // 1. If page has an assigned custom page template, always honor it!
    $page_template_slug = get_page_template_slug($post->ID);
    if (!empty($page_template_slug) && 'default' !== $page_template_slug) {
        $located = locate_template($page_template_slug);
        if ($located) {
            return $located;
        }
        // It is a block template (e.g. 'page-no-title', 'page-canvas', or user-created template in Site Editor)
        return $template;
    }

    // 2. Default routing for core pages if no explicit template was chosen
    $routes = [
        'projects'     => 'page-projects.php',
        'about'        => 'page-about.php',
        'impact'       => 'page-impact.php',
        'faq'          => 'page-faq.php',
        'get-involved' => 'page-get-involved.php',
        'volunteer'    => 'page-volunteer.php',
        'contact'      => 'page-contact.php',
        'donate'       => 'page-donate.php',
    ];

    foreach ($routes as $slug => $file) {
        if (is_page($slug)) {
            $custom_template = locate_template($file);
            if ($custom_template) {
                return $custom_template;
            }
        }
    }

    return $template;
});

/**
 * ENSURE ROUTE EXISTENCE
 * Automatically creates the necessary Pages in the database if they are missing.
 * This prevents WordPress from falling back to index.php (The "No posts matched" error).
 */
function omt_ensure_required_pages() {
    $required_pages = [
        'projects'     => 'Projects',
        'about'        => 'About',
        'impact'       => 'Impact',
        'faq'          => 'FAQ',
        'get-involved' => 'Get Involved',
        'volunteer'    => 'Volunteer',
        'contact'      => 'Contact',
        'donate'       => 'Donate',
    ];

    foreach ($required_pages as $slug => $title) {
        if (!get_page_by_path($slug)) {
            wp_insert_post([
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_status' => 'publish',
                'post_type'   => 'page',
            ]);
        }
    }
}
add_action('init', 'omt_ensure_required_pages');

/**
 * Auto-provision essential pages on theme activation / setup
 */
function one_more_tree_provision_default_pages() {
    $pages = [
        ['title' => 'Home', 'slug' => 'home', 'template' => 'front-page.php'],
        ['title' => 'About', 'slug' => 'about', 'template' => 'page-about.php'],
        ['title' => 'Projects', 'slug' => 'projects', 'template' => 'page-projects.php'],
        ['title' => 'Impact', 'slug' => 'impact', 'template' => 'page-impact.php'],
        ['title' => 'FAQ', 'slug' => 'faq', 'template' => 'page-faq.php'],
        ['title' => 'Get Involved', 'slug' => 'get-involved', 'template' => 'page-get-involved.php'],
        ['title' => 'Volunteer', 'slug' => 'volunteer', 'template' => 'page-volunteer.php'],
        ['title' => 'Contact', 'slug' => 'contact', 'template' => 'page-contact.php'],
        ['title' => 'Donate', 'slug' => 'donate', 'template' => 'page-donate.php'],
        ['title' => 'Blog', 'slug' => 'blog', 'template' => 'home.php'],
    ];

    $home_page_id = 0;
    $blog_page_id = 0;
    $page_author = get_current_user_id();
    if (!$page_author) {
        $administrators = get_users(['role' => 'administrator', 'number' => 1, 'fields' => 'ID']);
        $page_author = !empty($administrators[0]) ? (int) $administrators[0] : 0;
    }

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'   => $p['title'],
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $p['slug'],
                'post_author'  => $page_author,
            ]);
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $p['template']);
                if ($p['slug'] === 'home') $home_page_id = $page_id;
                if ($p['slug'] === 'blog') $blog_page_id = $page_id;
            }
        } else {
            update_post_meta($existing->ID, '_wp_page_template', $p['template']);
            if ($p['slug'] === 'home') $home_page_id = $existing->ID;
            if ($p['slug'] === 'blog') $blog_page_id = $existing->ID;
        }
    }

    if ($home_page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page_id);
    }
    if ($blog_page_id) {
        update_option('page_for_posts', $blog_page_id);
    }
}
add_action('after_switch_theme', 'one_more_tree_provision_default_pages');

/**
 * AJAX handlers for Frontend Interactive Forms
 */

// Newsletter
add_action('wp_ajax_omt_newsletter_subscribe', 'omt_newsletter_subscribe_handler');
add_action('wp_ajax_nopriv_omt_newsletter_subscribe', 'omt_newsletter_subscribe_handler');

function omt_newsletter_subscribe_handler() {
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }
    $subscribers = get_option('omt_admin_newsletter_subscribers', []);
    if (!is_array($subscribers)) $subscribers = [];
    $exists = false;
    foreach ($subscribers as $subscriber) {
        $existing_email = is_array($subscriber) ? ($subscriber['email'] ?? '') : $subscriber;
        if (strtolower((string) $existing_email) === strtolower($email)) {
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
        update_option('omt_admin_newsletter_subscribers', array_slice($subscribers, 0, 1000));
    }
    wp_send_json_success(['message' => 'Thanks for subscribing!']);
}

// Contact Form
add_action('wp_ajax_omt_contact_submit', 'omt_contact_submit_handler');
add_action('wp_ajax_nopriv_omt_contact_submit', 'omt_contact_submit_handler');

function omt_contact_submit_handler() {
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(['message' => 'Please fill in all fields.']);
    }
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }

    $inbox = get_option('omt_admin_contact_submissions', []);
    if (!is_array($inbox)) $inbox = [];
    array_unshift($inbox, [
        'id'        => uniqid('sub_'),
        'name'      => $name,
        'email'     => $email,
        'message'   => $message,
        'interest'  => 'General Contact',
        'type'      => 'contact',
        'date'      => current_time('mysql'),
        'timestamp' => time(),
        'status'    => 'unread',
    ]);
    update_option('omt_admin_contact_submissions', array_slice($inbox, 0, 500));

    wp_send_json_success(['message' => 'Message sent! We saved it to the admin inbox.']);
}

// Volunteer Form
add_action('wp_ajax_omt_volunteer_submit', 'omt_volunteer_submit_handler');
add_action('wp_ajax_nopriv_omt_volunteer_submit', 'omt_volunteer_submit_handler');

function omt_volunteer_submit_handler() {
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $interest = isset($_POST['interest']) ? sanitize_text_field($_POST['interest']) : 'Tree planting';

    if (empty($name) || empty($email)) {
        wp_send_json_error(['message' => 'Please fill in all fields.']);
    }
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }

    $submissions = get_option('omt_admin_contact_submissions', []);
    if (!is_array($submissions)) $submissions = [];
    array_unshift($submissions, [
        'id'        => uniqid('vol_'),
        'name'      => $name,
        'email'     => $email,
        'interest'  => $interest,
        'message'   => "Volunteer Interest: {$interest}",
        'type'      => 'volunteer',
        'date'      => current_time('mysql'),
        'timestamp' => time(),
        'status'    => 'unread',
    ]);
    update_option('omt_admin_contact_submissions', array_slice($submissions, 0, 500));

    wp_send_json_success(['message' => 'Thank you for signing up to volunteer! We will reach out with nearby planting events.']);
}

