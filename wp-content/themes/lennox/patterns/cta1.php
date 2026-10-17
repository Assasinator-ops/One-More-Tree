<?php
/**
 * Title: CTA 1
 * Slug: lennox/cta1
 * Categories: lennox-cta
 * Keywords: cta, call to action, button
 * Description: Centered call to action section with an accent background, title, and white button.
 *
 * @package lennox
 * @since 1.0.0
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":""}},"backgroundColor":"custom-accent","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-custom-accent-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"textAlign":"center","align":"wide","style":{"color":{"text":"#ffffff"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}},"typography":{"letterSpacing":"-0.035em","lineHeight":"1"}},"fontSize":"gigantic"} -->
<h2 class="wp-block-heading alignwide has-text-align-center has-text-color has-gigantic-font-size" style="color:#ffffff;margin-top:0;margin-bottom:var(--wp--preset--spacing--40);letter-spacing:-0.035em;line-height:1"><?php echo esc_html__( 'Ready to launch a site that turns visitors into customers?', 'lennox' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#ffffff","text":"var:preset|color|custom-accent"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" href="#contact" style="color:var(--wp--preset--color--custom-accent);background-color:#ffffff"><?php echo esc_html__( 'Get in touch', 'lennox' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
