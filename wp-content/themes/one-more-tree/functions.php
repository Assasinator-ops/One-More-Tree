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
        'rewrite'             => ['slug' => 'projects'],
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

    // Dequeue default WordPress block library styles to avoid CSS pollution
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');

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

/**
 * Page Templates declaration
 */
function one_more_tree_page_templates($templates) {
    return [
        'front-page.php'        => 'Home Page Template',
        'page-about.php'        => 'About Page Template',
        'page-projects.php'     => 'Projects Page Template',
        'page-impact.php'       => 'Impact Page Template',
        'page-faq.php'          => 'FAQ Page Template',
        'page-get-involved.php' => 'Get Involved Page Template',
        'page-volunteer.php'    => 'Volunteer Page Template',
        'page-contact.php'      => 'Contact Page Template',
        'page-donate.php'       => 'Donate Page Template',
    ];
}
add_filter('theme_page_templates', 'one_more_tree_page_templates');

/**
 * Rewrite rules for Admin console at /administrator and /admin
 */
function one_more_tree_admin_rewrite_rules() {
    add_rewrite_rule('^administrator/?$', 'index.php?omt_administrator=1', 'top');
    add_rewrite_rule('^admin/?$', 'index.php?omt_administrator=1', 'top');
}
add_action('init', 'one_more_tree_admin_rewrite_rules');

function one_more_tree_query_vars($vars) {
    $vars[] = 'omt_administrator';
    return $vars;
}
add_filter('query_vars', 'one_more_tree_query_vars');

function one_more_tree_admin_template_redirect() {
    if (get_query_var('omt_administrator')) {
        require get_template_directory() . '/inc/administrator-view.php';
        exit;
    }
}
add_action('template_redirect', 'one_more_tree_admin_template_redirect');

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

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'   => $p['title'],
                'post_content' => '',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_name'    => $p['slug'],
                'post_author'  => 1,
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
    $subscribers = get_option('omt_newsletter_subscribers', []);
    if (!is_array($subscribers)) $subscribers = [];
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        update_option('omt_newsletter_subscribers', $subscribers);
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

    $inbox = get_option('omt_contact_messages', []);
    if (!is_array($inbox)) $inbox = [];
    $inbox[] = [
        'name'      => $name,
        'email'     => $email,
        'message'   => $message,
        'date'      => current_time('mysql'),
    ];
    update_option('omt_contact_messages', $inbox);

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

    $volunteers = get_option('omt_volunteer_signups', []);
    if (!is_array($volunteers)) $volunteers = [];
    $volunteers[] = [
        'name'      => $name,
        'email'     => $email,
        'interest'  => $interest,
        'date'      => current_time('mysql'),
    ];
    update_option('omt_volunteer_signups', $volunteers);

    wp_send_json_success(['message' => 'Thank you for signing up to volunteer! We will reach out with nearby planting events.']);
}

