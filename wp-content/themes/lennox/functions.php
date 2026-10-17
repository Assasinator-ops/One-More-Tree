<?php
/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package lennox
 * @since 1.0.0
 */

/**
 * The theme version.
 *
 * @since 1.0.0
 */
define( 'LENNOX_VERSION', wp_get_theme()->get( 'Version' ) );

/**
 * Add theme support for block styles and editor style.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_setup() {
	add_editor_style( './assets/css/style-shared.min.css' );

	/*
	 * Load additional block styles.
	 * See details on how to add more styles in the readme.txt.
	 */
	$styled_blocks = [ 'button', 'quote', 'navigation', 'search', 'query-pagination' ];
	foreach ( $styled_blocks as $block_name ) {
		$args = array(
			'handle' => "lennox-$block_name",
			'src'    => get_theme_file_uri( "assets/css/blocks/$block_name.min.css" ),
			'path'   => get_theme_file_path( "assets/css/blocks/$block_name.min.css" ),
		);
		// Replace the "core" prefix if you are styling blocks from plugins.
		wp_enqueue_block_style( "core/$block_name", $args );
	}

}
add_action( 'after_setup_theme', 'lennox_setup' );

/**
 * Enqueue the CSS files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_styles() {

	wp_enqueue_style(
		'lennox-style',
		get_stylesheet_uri(),
		[],
		LENNOX_VERSION
	);
	wp_enqueue_style(
		'lennox-shared-styles',
		get_theme_file_uri( 'assets/css/style-shared.min.css' ),
		[],
		LENNOX_VERSION
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'lennox-transparent-header',
			get_theme_file_uri( 'assets/js/transparent-header.js' ),
			[],
			LENNOX_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lennox_styles' );
/**
 * Store the theme install time for notice scheduling.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_set_installed_time() {
	if ( ! get_option( 'lennox_installed_time' ) ) {
		update_option( 'lennox_installed_time', current_time( 'timestamp' ) );
	}
}
add_action( 'after_switch_theme', 'lennox_set_installed_time' );

/**
 * Register admin notices.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_register_admin_notices() {
	if ( ! is_admin() ) {
		return;
	}

	$review_notice = new Lennox_Review_Notice();
	$review_notice->register_hooks();
}
add_action( 'after_setup_theme', 'lennox_register_admin_notices' );

// Filters.
require_once get_theme_file_path( 'inc/filters.php' );

// Theme icons.
require_once get_theme_file_path( 'inc/class-lennox-theme-icons.php' );

// Block styles.
require_once get_theme_file_path( 'inc/register-block-styles.php' );

// Block patterns and block categories.
require_once get_theme_file_path( 'inc/register-block-patterns.php' );

// Starter sites.
require_once get_theme_file_path( 'inc/class-lennox-starter-sites.php' );

// Theme dashboard.
require_once get_theme_file_path( 'inc/admin/class-lennox-theme-dashboard.php' );

// Notices.
require_once get_theme_file_path( 'inc/notices/class-lennox-notices-base.php' );
require_once get_theme_file_path( 'inc/notices/class-lennox-review-notice.php' );

/**
 * Register the theme dashboard.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_register_theme_dashboard() {
	if ( ! is_admin() || ! class_exists( 'Lennox_Theme_Dashboard' ) ) {
		return;
	}

	$theme_dashboard = new Lennox_Theme_Dashboard();
	$theme_dashboard->register_hooks();
}
add_action( 'after_setup_theme', 'lennox_register_theme_dashboard' );
