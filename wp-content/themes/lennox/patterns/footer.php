<?php
/**
 * Title: Footer
 * Slug: lennox/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Compact footer bar with copyright and privacy policy link.
 *
 * @package lennox
 */

?>

<!-- wp:group {"tagName":"footer","align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|40","bottom":"var:preset|spacing|30","left":"var:preset|spacing|40"}}},"backgroundColor":"custom-dark-background","layout":{"type":"constrained"}} -->
<footer class="wp-block-group alignfull has-custom-dark-background-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"textColor":"custom-light-background","fontSize":"small"} -->
<p class="has-custom-light-background-color has-text-color has-small-font-size"><?php echo esc_html( sprintf( /* translators: 1: Current year. 2: Site title. */ __( '© %1$s %2$s. All rights reserved.', 'lennox' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"align":"right","textColor":"custom-light-background","style":{"elements":{"link":{"color":{"text":"var:preset|color|custom-light-background"}}}},"fontSize":"small"} -->
<p class="has-text-align-right has-custom-light-background-color has-text-color has-link-color has-small-font-size"><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'lennox' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></footer>
<!-- /wp:group -->
