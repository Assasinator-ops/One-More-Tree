<?php
/**
 * Title: 404 Error Page
 * Slug: lennox/hidden-404
 * Categories: lennox-common
 * Inserter: no
 * Keywords: 404, error, not found
 */
?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"textAlign":"center"} -->
<h1 class="wp-block-heading has-text-align-center">
	<?php echo esc_html__( 'Page not found', 'lennox' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php echo esc_html__( 'It looks like nothing was found at this location. Maybe try a search?', 'lennox' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search', 'lennox' ); ?>","showLabel":false,"buttonText":"<?php echo esc_attr__( 'Search', 'lennox' ); ?>"} /--></div>
<!-- /wp:group -->
