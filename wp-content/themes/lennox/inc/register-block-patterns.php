<?php
/**
 * Block patterns
 *
 * @package lennox
 * @since 1.0.0
 */

/**
 * Registers block patterns and block pattern categories.
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_register_block_patterns() {
	 /**
	 * Register an example block pattern category.
	 *
	 * @since 1.0.0
	 */
	$categories = [
		'lennox-posts'        => esc_html__( 'Posts', 'lennox' ),
		'lennox-common'       => esc_html__( 'Common', 'lennox' ), // This is intended for internal/common patterns.
		'lennox-heroes'       => esc_html__( 'Lennox: Heroes', 'lennox' ),
		'lennox-about'        => esc_html__( 'Lennox: About', 'lennox' ),
		'lennox-services'     => esc_html__( 'Lennox: Services', 'lennox' ),
		'lennox-case-studies' => esc_html__( 'Lennox: Case Studies', 'lennox' ),
		'lennox-testimonials' => esc_html__( 'Lennox: Testimonials', 'lennox' ),
		'lennox-stats'        => esc_html__( 'Lennox: Stats', 'lennox' ),
		'lennox-cta'          => esc_html__( 'Lennox: Calls to Action', 'lennox' ),
	];

	foreach ( $categories as $slug => $label ) {
		register_block_pattern_category( $slug, [ 'label' => $label ] );
	}
}
add_action( 'init', 'lennox_register_block_patterns', 9 );

/**
 * Unregister patterns and pattern categories.
 *
 * @see https://developer.wordpress.org/reference/functions/unregister_block_pattern/
 * @see https://developer.wordpress.org/reference/functions/unregister_block_pattern_category/
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_unregister_patterns() {
	 remove_theme_support( 'core-block-patterns' );
}
add_action( 'init', 'lennox_unregister_patterns', 10 );
