<?php
/**
 * Title: About 2
 * Slug: lennox/about2
 * Categories: lennox-about
 * Keywords: about, section, intro, company
 * Description: About section with a feature image, supporting copy, and review proof.
 *
 * @package lennox
 * @since 1.0.0
 */

?>

<!-- wp:group {"metadata":{"categories":["lennox-sections"],"patternName":"lennox/about2","name":"About 2"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"custom-light-background","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-custom-light-background-background-color has-background" id="about" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%"><!-- wp:image {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/images/about2.jpg' ) ); ?>","alt":"<?php echo esc_attr__( 'Team discussing campaign strategy around a presentation screen', 'lennox' ); ?>","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/about2.jpg' ) ); ?>" alt="<?php echo esc_attr__( 'Team discussing campaign strategy around a presentation screen', 'lennox' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"38%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:38%"><!-- wp:heading {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}}}} -->
<h2 class="wp-block-heading" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--40)"><?php echo esc_html__( 'Built for Sustainable Search Growth', 'lennox' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-top:0;margin-bottom:var(--wp--preset--spacing--30)"><?php echo esc_html__( 'We partner with ambitious brands that want more than temporary traffic spikes. Our work combines technical SEO, content strategy, and performance insights to build search visibility that compounds over time.', 'lennox' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"border":{"top":{"color":"var:preset|color|custom-border","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--custom-border);border-top-width:1px;padding-top:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"style":{"typography":{"lineHeight":"1"}},"fontSize":"gigantic"} -->
<p class="has-gigantic-font-size" style="line-height:1">5.0</p>
<!-- /wp:paragraph -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"700","lineHeight":"1"},"color":{"text":"#f29430"},"elements":{"link":{"color":{"text":"#f29430"}}}},"fontSize":"large"} -->
<p class="has-text-color has-link-color has-large-font-size" style="color:#f29430;margin-top:0;margin-bottom:0;font-weight:700;line-height:1">★★★★★</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"500","lineHeight":"1.25"}},"textColor":"custom-text"} -->
<p class="has-custom-text-color has-text-color" style="margin-top:0;margin-bottom:0;font-weight:500;line-height:1.25"><?php echo esc_html__( '157 Verified Reviews', 'lennox' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
