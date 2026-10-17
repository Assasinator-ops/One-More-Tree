<?php
/**
 * Block styles.
 *
 * @package lennox
 * @since 1.0.0
 */

/**
 * Register block styles
 *
 * @since 1.0.0
 *
 * @return void
 */
function lennox_register_block_styles() {

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/list',
		array(
			'name'  => 'lennox-list-underline',
			'label' => __( 'Underlined list items', 'lennox' ),
		)
	);

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/group',
		array(
			'name'  => 'lennox-box-shadow',
			'label' => __( 'Box shadow', 'lennox' ),
		)
	);

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/column',
		array(
			'name'  => 'lennox-box-shadow',
			'label' => __( 'Box shadow', 'lennox' ),
		)
	);

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/columns',
		array(
			'name'  => 'lennox-box-shadow',
			'label' => __( 'Box shadow', 'lennox' ),
		)
	);

	register_block_style( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_style
		'core/details',
		array(
			'name'  => 'lennox-plus',
			'label' => __( 'Plus & minus', 'lennox' ),
		)
	);

}
add_action( 'init', 'lennox_register_block_styles' );
