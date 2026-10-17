<?php
/**
 * Apply One More Tree site content to WordPress.
 *
 * Runs INSIDE the WordPress container:
 *   docker exec one-more-tree-wp php /tmp/omt-apply.php /tmp/omt-content
 *
 * Idempotent: re-running updates the same posts rather than duplicating them.
 * Content lives in site/content/ as block markup so it stays reviewable and
 * version-controlled instead of only existing in the database.
 *
 * @package OneMoreTree
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 'CLI only' );
}

define( 'WP_USE_THEMES', false );
require '/var/www/html/wp-load.php';

$root = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : '/tmp/omt-content';

// Act as a real administrator so kses, revisions and capabilities behave
// exactly as they would for the site owner.
wp_set_current_user( 1 );

$log = array();

/**
 * Read a content file.
 *
 * @param string $path Absolute path.
 * @return string
 */
function omt_read_file( $path ) {
	if ( ! is_readable( $path ) ) {
		return '';
	}
	return (string) file_get_contents( $path );
}

/**
 * Find a block template / template part owned by the active theme.
 *
 * @param string $post_type wp_template|wp_template_part.
 * @param string $slug      Template slug.
 * @return int Post ID, or 0 when absent.
 */
function omt_find_theme_template( $post_type, $slug ) {
	$candidates = get_posts(
		array(
			'post_type'      => $post_type,
			'name'           => $slug,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => get_stylesheet(),
				),
			),
		)
	);

	return $candidates ? (int) $candidates[0]->ID : 0;
}

/**
 * Create or update a block template / template part for the active theme.
 *
 * @param string $post_type wp_template|wp_template_part.
 * @param string $slug      Slug.
 * @param string $title     Human title.
 * @param string $content   Block markup.
 * @param string $area      Template part area, or ''.
 * @return array{id:int,action:string}
 */
function omt_upsert_template( $post_type, $slug, $title, $content, $area = '' ) {
	$existing_id = omt_find_theme_template( $post_type, $slug );

	$data = array(
		'post_type'    => $post_type,
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_content' => $content,
	);

	$action = 'created';
	if ( $existing_id ) {
		$data['ID'] = $existing_id;
		$result     = wp_update_post( wp_slash( $data ), true );
		$action     = 'updated';
	} else {
		$result = wp_insert_post( wp_slash( $data ), true );
	}

	if ( is_wp_error( $result ) ) {
		return array(
			'id'     => 0,
			'action' => 'ERROR: ' . $result->get_error_message(),
		);
	}

	$id = (int) $result;

	wp_set_object_terms( $id, get_stylesheet(), 'wp_theme' );

	if ( $area ) {
		wp_set_object_terms( $id, $area, 'wp_template_part_area' );
	}

	return array(
		'id'     => $id,
		'action' => $action,
	);
}

/**
 * Create or update a page.
 *
 * @param string $slug    Page slug.
 * @param string $content Block markup.
 * @return array{id:int,action:string}
 */
function omt_upsert_page( $slug, $content ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	$data = array(
		'post_type'    => 'page',
		'post_name'    => $slug,
		'post_content' => $content,
		'post_status'  => 'publish',
	);

	$reset = '';

	if ( $existing ) {
		// Pages carried over from the previous theme still point at PHP page
		// templates that no longer exist (page-about.php, front-page.php, …).
		// WordPress rejects updates while such a value is set, and falls back to
		// a broken template resolution, so normalise it to the block theme
		// default before touching the content.
		$current = (string) get_post_meta( $existing->ID, '_wp_page_template', true );
		if ( '' !== $current && 'default' !== $current ) {
			update_post_meta( $existing->ID, '_wp_page_template', 'default' );
			$reset = sprintf( ' (template reset from %s)', $current );
		}

		$data['ID']         = $existing->ID;
		$data['post_title'] = $existing->post_title;
		$result             = wp_update_post( wp_slash( $data ), true );
		$action             = 'updated' . $reset;
	} else {
		$data['post_title'] = ucwords( str_replace( '-', ' ', $slug ) );
		$result             = wp_insert_post( wp_slash( $data ), true );
		$action             = 'created';
	}

	if ( is_wp_error( $result ) ) {
		return array(
			'id'     => 0,
			'action' => 'ERROR: ' . $result->get_error_message(),
		);
	}

	return array(
		'id'     => (int) $result,
		'action' => $action,
	);
}

/**
 * Normalise legacy _wp_page_template values left over from the previous theme.
 *
 * @return int Number of pages corrected.
 */
function omt_reset_legacy_page_templates() {
	$fixed = 0;
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		)
	);

	foreach ( $pages as $page ) {
		$current = (string) get_post_meta( $page->ID, '_wp_page_template', true );

		if ( '' === $current || 'default' === $current ) {
			continue;
		}

		// A block theme renders templates from templates/*.html. Any value ending
		// in .php is a classic-theme page template that no longer exists, so it
		// can only ever fall back to a broken resolution.
		if ( '.php' === substr( $current, -4 ) ) {
			update_post_meta( $page->ID, '_wp_page_template', 'default' );
			++$fixed;
		}
	}

	return $fixed;
}

/* -------------------------------------------------------------------------
 * 1. Pages
 * ---------------------------------------------------------------------- */

$legacy_templates = omt_reset_legacy_page_templates();
if ( $legacy_templates ) {
	$log[] = sprintf( 'pages      reset %d stale _wp_page_template values from the previous theme', $legacy_templates );
}

$page_files = glob( $root . '/pages/*.html' );
sort( $page_files );

foreach ( $page_files as $file ) {
	$slug    = basename( $file, '.html' );
	$content = omt_read_file( $file );

	$out      = omt_upsert_page( $slug, $content );
	$log[]    = sprintf( 'page       %-14s #%-4d %-8s %6d bytes', $slug, $out['id'], $out['action'], strlen( $content ) );
}

/* -------------------------------------------------------------------------
 * 2. Templates and template parts
 * ---------------------------------------------------------------------- */

$template_map = array(
	'templates/front-page.html' => array( 'wp_template', 'front-page', 'Front Page', '' ),
	'parts/header.html'         => array( 'wp_template_part', 'header', 'Header', 'header' ),
	'parts/footer.html'         => array( 'wp_template_part', 'footer', 'Footer', 'footer' ),
);

foreach ( $template_map as $relative => $meta ) {
	$path = $root . '/' . $relative;
	if ( ! is_readable( $path ) ) {
		$log[] = sprintf( 'template   %-14s (missing source file, skipped)', $relative );
		continue;
	}

	list( $post_type, $slug, $title, $area ) = $meta;
	$content = omt_read_file( $path );

	$out   = omt_upsert_template( $post_type, $slug, $title, $content, $area );
	$log[] = sprintf( '%-10s %-14s #%-4d %-8s %6d bytes', str_replace( 'wp_', '', $post_type ), $slug, $out['id'], $out['action'], strlen( $content ) );
}

/* -------------------------------------------------------------------------
 * 3. Primary navigation
 * ---------------------------------------------------------------------- */

$nav_links = array(
	array( 'Home', '/' ),
	array( 'About', '/about/' ),
	array( 'Projects', '/projects/' ),
	array( 'Impact', '/impact/' ),
	array( 'Field notes', '/blog/' ),
	array( 'Contact', '/contact/' ),
);

$nav_content = '';
foreach ( $nav_links as $link ) {
	list( $label, $path ) = $link;
	$nav_content .= sprintf(
		"\n<!-- wp:navigation-link {\"label\":%s,\"type\":\"custom\",\"url\":%s} /-->\n",
		wp_json_encode( $label ),
		wp_json_encode( home_url( $path ) )
	);
}

$nav_posts = get_posts(
	array(
		'post_type'      => 'wp_navigation',
		'name'           => 'header-menu',
		'post_status'    => 'any',
		'posts_per_page' => 1,
	)
);

if ( $nav_posts ) {
	wp_update_post(
		wp_slash(
			array(
				'ID'           => $nav_posts[0]->ID,
				'post_content' => $nav_content,
			)
		)
	);
	$log[] = sprintf( 'navigation header-menu  #%-4d updated  %6d bytes', $nav_posts[0]->ID, strlen( $nav_content ) );
} else {
	$nav_id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => 'wp_navigation',
				'post_name'    => 'header-menu',
				'post_title'   => 'Header menu',
				'post_status'  => 'publish',
				'post_content' => $nav_content,
			)
		)
	);
	$log[] = sprintf( 'navigation header-menu  #%-4d created  %6d bytes', $nav_id, strlen( $nav_content ) );
}

/* -------------------------------------------------------------------------
 * 4. Reading settings, so the front page is deterministic
 * ---------------------------------------------------------------------- */

$home_page = get_page_by_path( 'home', OBJECT, 'page' );
if ( $home_page ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_page->ID );
	update_option( 'page_for_posts', 0 );
	$log[] = sprintf( 'settings   show_on_front=page page_on_front=%d page_for_posts=0', $home_page->ID );
}

/* -------------------------------------------------------------------------
 * 5. Housekeeping
 * ---------------------------------------------------------------------- */

// The footer links to the privacy policy; a draft there is a broken link.
$privacy = get_page_by_path( 'privacy-policy', OBJECT, 'page' );
if ( $privacy && 'publish' !== $privacy->post_status ) {
	wp_update_post( array( 'ID' => $privacy->ID, 'post_status' => 'publish' ) );
	$log[] = sprintf( 'page       privacy-policy #%-4d published', $privacy->ID );
}

// Stock WordPress sample page has no place on a production site.
$sample = get_page_by_path( 'sample-page', OBJECT, 'page' );
if ( $sample && 'publish' === $sample->post_status ) {
	wp_update_post( array( 'ID' => $sample->ID, 'post_status' => 'draft' ) );
	$log[] = sprintf( 'page       sample-page    #%-4d moved to draft', $sample->ID );
}

/* -------------------------------------------------------------------------
 * 6. Palette: body copy must not be brand-green
 * ---------------------------------------------------------------------- */

$styles_id = 0;
$style_posts = get_posts(
	array(
		'post_type'      => 'wp_global_styles',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'tax_query'      => array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => get_stylesheet(),
			),
		),
	)
);
if ( $style_posts ) {
	$styles_id = (int) $style_posts[0]->ID;
}

if ( $styles_id ) {
	$styles = json_decode( (string) get_post_field( 'post_content', $styles_id ), true );

	if ( is_array( $styles ) ) {
		$changed = false;
		if ( isset( $styles['settings']['color']['palette']['theme'] ) ) {
			foreach ( $styles['settings']['color']['palette']['theme'] as $index => $entry ) {
				if ( 'custom-text' === $entry['slug'] && '#22312a' !== strtolower( $entry['color'] ) ) {
					$styles['settings']['color']['palette']['theme'][ $index ]['color'] = '#22312a';
					$changed = true;
				}
			}
		}

		if ( $changed ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $styles_id,
						'post_content' => wp_json_encode( $styles, JSON_UNESCAPED_SLASHES ),
					)
				)
			);
			$log[] = sprintf( 'styles     global-styles  #%-4d custom-text set to #22312a', $styles_id );
		} else {
			$log[] = sprintf( 'styles     global-styles  #%-4d already correct', $styles_id );
		}
	}
}

wp_cache_flush();

/* -------------------------------------------------------------------------
 * Report
 * ---------------------------------------------------------------------- */

echo "One More Tree — content applied\n";
echo str_repeat( '=', 72 ) . "\n";
foreach ( $log as $line ) {
	echo $line . "\n";
}
echo str_repeat( '=', 72 ) . "\n";
echo 'Front page template: #' . omt_find_theme_template( 'wp_template', 'front-page' ) . "\n";
echo 'Total log entries: ' . count( $log ) . "\n";
