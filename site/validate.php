<?php
/**
 * Validate every piece of stored block content.
 *
 *   docker exec one-more-tree-wp php /tmp/omt-content/validate.php
 *
 * Checks that each post's content parses, that every block name is registered,
 * and that the rendered output contains no unfilled markers.
 *
 * @package OneMoreTree
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 'CLI only' );
}

define( 'WP_USE_THEMES', false );
require '/var/www/html/wp-load.php';

wp_set_current_user( 1 );

$registry = WP_Block_Type_Registry::get_instance();
$targets  = array();

foreach ( array( 'page', 'wp_template', 'wp_template_part', 'wp_navigation' ) as $type ) {
	foreach ( get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => -1,
		)
	) as $post ) {
		$targets[] = $post;
	}
}

$total_blocks  = 0;
$unknown_total = 0;
$html_total    = 0;
$problems      = array();

foreach ( $targets as $post ) {
	$content = (string) $post->post_content;

	if ( '' === trim( $content ) ) {
		continue; // Empty pages (e.g. the static front page shell) are fine.
	}

	$parsed = parse_blocks( $content );
	$count  = 0;
	$bad    = array();

	$walk = static function ( $blocks ) use ( &$walk, &$count, &$bad, $registry ) {
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}
			++$count;

			if ( ! $registry->is_registered( $block['blockName'] ) ) {
				$bad[] = $block['blockName'];
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};

	$walk( $parsed );

	$rendered = do_blocks( $content );

	$leaks = array();
	foreach ( array( 'data-omt-fields', 'data-omt-notice', 'wp:pattern', '<!-- wp:' ) as $needle ) {
		if ( false !== strpos( $rendered, $needle ) ) {
			$leaks[] = $needle;
		}
	}

	$total_blocks += $count;
	$unknown_total += count( $bad );
	$html_total   += strlen( $rendered );

	$label = sprintf( '%s/%s', str_replace( 'wp_', '', $post->post_type ), $post->post_name );

	if ( $bad || $leaks ) {
		$problems[] = sprintf(
			'  %-32s unknown=%s leaks=%s',
			$label,
			$bad ? implode( ',', array_unique( $bad ) ) : '-',
			$leaks ? implode( ',', $leaks ) : '-'
		);
	}

	printf(
		"  %-32s blocks=%-4d html=%-7d %s\n",
		$label,
		$count,
		strlen( $rendered ),
		( $bad || $leaks ) ? 'ISSUES' : 'ok'
	);
}

echo str_repeat( '=', 72 ) . "\n";
echo 'Posts checked:        ' . count( $targets ) . "\n";
echo 'Blocks parsed:        ' . $total_blocks . "\n";
echo 'Unknown block names:  ' . $unknown_total . "\n";
echo 'Rendered bytes:       ' . $html_total . "\n";
echo 'Problems:             ' . count( $problems ) . "\n";

if ( $problems ) {
	echo "\n" . implode( "\n", $problems ) . "\n";
	exit( 1 );
}

echo "\nAll content parses cleanly into registered blocks.\n";
