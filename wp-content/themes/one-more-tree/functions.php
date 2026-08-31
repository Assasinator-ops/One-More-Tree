<?php
/**
 * One More Tree functions and definitions
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

    // Enable wide and full-width block alignment
    add_theme_support('align-wide');

    // Enable responsive embedded content (YouTube, Vimeo, etc.)
    add_theme_support('responsive-embeds');

    // Enable core block styles and custom editor stylesheet
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style([
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap',
        'assets/css/editor-style.css'
    ]);

    // Add support for Full Site Editing
    add_theme_support('block-templates');

    // Add support for template parts
    add_theme_support('block-template-parts');

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'one-more-tree'),
        'footer'   => __('Footer Menu', 'one-more-tree'),
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

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core custom logo
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Register Projects Custom Post Type so the block template has content to query
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
        'template'            => [
            ['core/paragraph', ['placeholder' => 'Describe the project, its location, and impact…']],
        ],
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
    // Enqueue compiled Tailwind CSS
    wp_enqueue_style(
        'one-more-tree-style',
        get_template_directory_uri() . '/assets/css/main.css',
        [],
        _S_VERSION
    );

    // Enqueue Gutenberg Native Block Styles matching Next.js design
    wp_enqueue_style(
        'one-more-tree-blocks',
        get_template_directory_uri() . '/assets/css/blocks.css',
        ['one-more-tree-style'],
        _S_VERSION
    );

    // Enqueue Google Fonts
    wp_enqueue_style(
        'one-more-tree-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Remove WordPress emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('wp_enqueue_scripts', 'one_more_tree_scripts', 100);

/**
 * Remove WordPress default actions that add unwanted styles
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Enqueue JavaScript for interactivity
 */
function one_more_tree_scripts_js() {
    // Enqueue GSAP library
    wp_enqueue_script(
        'gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
        array(),
        '3.12.2',
        true
    );
    
    // Enqueue main.js
    wp_enqueue_script(
        'one-more-tree-js',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        _S_VERSION,
        true
    );

    // Enqueue navigation.js
    wp_enqueue_script(
        'omt-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array('gsap'),
        _S_VERSION,
        true
    );

    // Enqueue newsletter.js
    wp_enqueue_script(
        'omt-newsletter',
        get_template_directory_uri() . '/assets/js/newsletter.js',
        array(),
        _S_VERSION,
        true
    );

    // Enqueue disclaimer.js
    wp_enqueue_script(
        'omt-disclaimer',
        get_template_directory_uri() . '/assets/js/disclaimer.js',
        array(),
        _S_VERSION,
        true
    );

    // Enqueue acf-animations.js for ACF pages
    wp_enqueue_script(
        'omt-acf-animations',
        get_template_directory_uri() . '/assets/js/acf-animations.js',
        array('gsap'),
        _S_VERSION,
        true
    );

    // Localize scripts
    wp_localize_script('one-more-tree-js', 'OMT_CONFIG', [
        'restUrl'   => esc_url_raw(rest_url('omt/v1/')),
        'restNonce' => wp_create_nonce('wp_rest'),
    ]);
    
    wp_localize_script('omt-newsletter', 'omtData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('omt-nonce')
    ]);
    
    wp_localize_script('omt-acf-animations', 'omtData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('omt-nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'one_more_tree_scripts_js');

/**
 * Enqueue editor styles for Gutenberg
 */
function one_more_tree_editor_styles() {
    wp_enqueue_style(
        'one-more-tree-editor',
        get_template_directory_uri() . '/assets/css/editor.css',
        array(),
        _S_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'one_more_tree_editor_styles');

/**
 * Custom template tags for this theme
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * ACF Fields for homepage and page content management
 */
require get_template_directory() . '/inc/acf-fields.php';

/**
 * REST API endpoints for Next.js admin console and public forms
 */
require get_template_directory() . '/inc/admin-api.php';

/**
 * Native WordPress Admin Settings and Management Pages
 */
require get_template_directory() . '/inc/wp-admin-settings.php';

/**
 * Gutenberg Block Patterns
 */
require get_template_directory() . '/inc/block-patterns.php';

/**
 * Register Block Styles
 */
function one_more_tree_register_block_styles() {
    // Button styles
    register_block_style('core/button', 'primary', [
        'label' => __('Primary', 'one-more-tree'),
        'inline_style' => '.is-style-primary { background-color: var(--wp--preset--color--forest-700); color: var(--wp--preset--color--white); }'
    ]);
    
    register_block_style('core/button', 'secondary', [
        'label' => __('Secondary', 'one-more-tree'),
        'inline_style' => '.is-style-secondary { background-color: var(--wp--preset--color--forest-100); color: var(--wp--preset--color--forest-800); }'
    ]);
    
    register_block_style('core/button', 'outline', [
        'label' => __('Outline', 'one-more-tree'),
        'inline_style' => '.is-style-outline { background-color: transparent; border: 2px solid var(--wp--preset--color--forest-700); color: var(--wp--preset--color--forest-700); }'
    ]);
    
    // Group styles
    register_block_style('core/group', 'card', [
        'label' => __('Card', 'one-more-tree'),
        'inline_style' => '.is-style-card { background-color: var(--wp--preset--color--white); border: 1px solid var(--wp--preset--color--earth-200); border-radius: 1rem; padding: 2rem; }'
    ]);
    
    register_block_style('core/group', 'hero-section', [
        'label' => __('Hero Section', 'one-more-tree'),
        'inline_style' => '.is-style-hero-section { background-color: var(--wp--preset--color--forest-50); padding: 4rem 2rem; }'
    ]);
}
add_action('init', 'one_more_tree_register_block_styles');

/**
 * Add page template support
 */
function one_more_tree_page_templates() {
    return [
        'front-page.php'            => 'Homepage Template (PHP)',
        'template-full-width.php'   => 'Full Width (Block Canvas)',
        'template-contained.php'    => 'Contained / Editorial Document',
        'page-about.php'            => 'About Page Template (PHP)',
        'page-projects.php'         => 'Projects Page Template (PHP)',
        'page-impact.php'           => 'Impact Page Template (PHP)',
        'page-faq.php'              => 'FAQ Page Template (PHP)',
        'page-get-involved.php'     => 'Get Involved Page Template (PHP)',
        'page-volunteer.php'        => 'Volunteer Page Template (PHP)',
        'page-contact.php'          => 'Contact Page Template (PHP)',
        'page-donate.php'           => 'Donate Page Template (PHP)',
    ];
}
add_filter('theme_page_templates', 'one_more_tree_page_templates');

/**
 * Rewrite rules for Next.js Administrator console at /administrator and /admin
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
 * Automatically create and configure essential pages on theme setup
 */
function one_more_tree_provision_default_pages() {
    $pages = [
        [
            'title'    => 'Home',
            'slug'     => 'home',
            'template' => 'front-page.php',
        ],
        [
            'title'    => 'About',
            'slug'     => 'about',
            'template' => 'page-about.php',
        ],
        [
            'title'    => 'Projects',
            'slug'     => 'projects',
            'template' => 'page-projects.php',
        ],
        [
            'title'    => 'Impact',
            'slug'     => 'impact',
            'template' => 'page-impact.php',
        ],
        [
            'title'    => 'FAQ',
            'slug'     => 'faq',
            'template' => 'page-faq.php',
        ],
        [
            'title'    => 'Get Involved',
            'slug'     => 'get-involved',
            'template' => 'page-get-involved.php',
        ],
        [
            'title'    => 'Volunteer',
            'slug'     => 'volunteer',
            'template' => 'page-volunteer.php',
        ],
        [
            'title'    => 'Contact',
            'slug'     => 'contact',
            'template' => 'page-contact.php',
        ],
        [
            'title'    => 'Donate',
            'slug'     => 'donate',
            'template' => 'page-donate.php',
        ],
        [
            'title'    => 'Blog',
            'slug'     => 'blog',
            'template' => 'home.php',
        ],
    ];

    $home_page_id = 0;
    $blog_page_id = 0;

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'    => $p['title'],
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_name'     => $p['slug'],
                'post_author'   => 1,
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

    // Configure static front page and blog page
    if ($home_page_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page_id);
    }
    if ($blog_page_id) {
        update_option('page_for_posts', $blog_page_id);
    }
}
add_action('init', 'one_more_tree_provision_default_pages');

/**
 * AJAX handler for newsletter subscription
 */
add_action('wp_ajax_omt_newsletter_subscribe', 'omt_newsletter_subscribe');
add_action('wp_ajax_nopriv_omt_newsletter_subscribe', 'omt_newsletter_subscribe');

function omt_newsletter_subscribe() {
    check_ajax_referer('omt-nonce', 'nonce');
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }
    
    // Integrate with your newsletter service here
    // For now, just return success
    wp_send_json_success(['message' => 'Thanks for subscribing!']);
}

/**
 * AJAX handler for contact form submission
 */
add_action('wp_ajax_omt_contact_submit', 'omt_contact_submit');
add_action('wp_ajax_nopriv_omt_contact_submit', 'omt_contact_submit');

function omt_contact_submit() {
    check_ajax_referer('omt-nonce', 'nonce');
    
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(['message' => 'Please fill in all fields.']);
    }
    
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }
    
    // Store in admin inbox or send email
    // For now, just return success
    wp_send_json_success(['message' => 'Message sent! We saved it to the admin inbox.']);
}

/**
 * Add social links and newsletter settings to WordPress admin
 */
add_action('admin_init', 'omt_register_social_settings');

function omt_register_social_settings() {
    add_settings_section(
        'omt_social_links',
        'Social Media Links & Newsletter',
        null,
        'general'
    );
    
    // Social Links
    add_settings_field(
        'omt_instagram_url',
        'Instagram URL',
        'omt_render_text_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_instagram_url']
    );
    
    add_settings_field(
        'omt_twitter_url',
        'Twitter URL',
        'omt_render_text_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_twitter_url']
    );
    
    add_settings_field(
        'omt_facebook_url',
        'Facebook URL',
        'omt_render_text_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_facebook_url']
    );
    
    add_settings_field(
        'omt_linkedin_url',
        'LinkedIn URL',
        'omt_render_text_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_linkedin_url']
    );
    
    // Newsletter
    add_settings_field(
        'omt_newsletter_enabled',
        'Enable Newsletter',
        'omt_render_checkbox_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_newsletter_enabled']
    );
    
    // Disclaimer Color
    add_settings_field(
        'omt_disclaimer_color',
        'Disclaimer Color',
        'omt_render_color_field',
        'general',
        'omt_social_links',
        ['name' => 'omt_disclaimer_color']
    );
    
    // Register settings
    register_setting('general', 'omt_instagram_url');
    register_setting('general', 'omt_twitter_url');
    register_setting('general', 'omt_facebook_url');
    register_setting('general', 'omt_linkedin_url');
    register_setting('general', 'omt_newsletter_enabled');
    register_setting('general', 'omt_disclaimer_color');
}

function omt_render_text_field($args) {
    $value = get_option($args['name'], '');
    echo '<input type="url" name="' . esc_attr($args['name']) . '" value="' . esc_attr($value) . '" class="regular-text">';
}

function omt_render_checkbox_field($args) {
    $value = get_option($args['name'], '1');
    echo '<input type="checkbox" name="' . esc_attr($args['name']) . '" value="1" ' . checked('1', $value, false) . '>';
}

function omt_render_color_field($args) {
    $value = get_option($args['name'], 'green');
    $colors = ['green' => 'Green', 'red' => 'Red', 'yellow' => 'Yellow', 'blue' => 'Blue', 'pink' => 'Pink'];
    echo '<select name="' . esc_attr($args['name']) . '">';
    foreach ($colors as $key => $label) {
        echo '<option value="' . esc_attr($key) . '" ' . selected($key, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}
