<?php
/** 
 * Title: Hero 1 Updated
 * Slug: lennox/hero1
 * Categories: lennox-heroes
 * Keywords: seo, agency, marketing, hero
 * Description: Full-width SEO agency hero with proof points, calls to action, and a supporting image.
 *
 * @package lennox
 * @since 1.0.0
 */

?>

<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/images/hero1.jpg' ) ); ?>","alt":"<?php echo esc_attr__( 'SEO agency team collaborating', 'lennox' ); ?>","dimRatio":70,"overlayColor":"custom-dark-background","isUserOverlayColor":true,"minHeight":100,"minHeightUnit":"vh","align":"full","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:var(--wp--preset--spacing--50);padding-bottom:0;padding-left:var(--wp--preset--spacing--50);min-height:100vh"><img class="wp-block-cover__image-background" alt="<?php echo esc_attr__( 'SEO agency team collaborating', 'lennox' ); ?>" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero1.jpg' ) ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-custom-dark-background-background-color has-background-dim-70 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group alignwide" style="margin-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"width":"40px","height":"40px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%","color":"#ffffff","width":"2px"}}} -->
<figure class="wp-block-image size-full is-resized has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-avatar1.jpg' ) ); ?>" alt="<?php echo esc_attr__( 'Client', 'lennox' ); ?>" class="has-border-color" style="border-color:#ffffff;border-width:2px;border-radius:50%;width:40px;height:40px"/></figure>
<!-- /wp:image -->

<!-- wp:image {"width":"40px","height":"40px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%","color":"#ffffff","width":"2px"},"spacing":{"margin":{"left":"-12px"}}}} -->
<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-avatar2.jpg' ) ); ?>" alt="<?php echo esc_attr__( 'Client', 'lennox' ); ?>" class="has-border-color" style="border-color:#ffffff;border-width:2px;border-radius:50%;width:40px;height:40px"/></figure>
<!-- /wp:image -->

<!-- wp:image {"width":"40px","height":"40px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%","color":"#ffffff","width":"2px"},"spacing":{"margin":{"left":"-12px"}}}} -->
<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-avatar3.jpg' ) ); ?>" alt="<?php echo esc_attr__( 'Client', 'lennox' ); ?>" class="has-border-color" style="border-color:#ffffff;border-width:2px;border-radius:50%;width:40px;height:40px"/></figure>
<!-- /wp:image -->

<!-- wp:image {"width":"40px","height":"40px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"50%","color":"#ffffff","width":"2px"},"spacing":{"margin":{"left":"-12px"}}}} -->
<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-avatar4.jpg' ) ); ?>" alt="<?php echo esc_attr__( 'Client', 'lennox' ); ?>" class="has-border-color" style="border-color:#ffffff;border-width:2px;border-radius:50%;width:40px;height:40px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"color":{"text":"#ffffffbd"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#ffffffbd;margin-top:0;margin-bottom:0"><?php echo esc_html__( 'Trusted by', 'lennox' ); ?> <strong>500+</strong> <?php echo esc_html__( 'businesses worldwide', 'lennox' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0"><!-- wp:heading {"textAlign":"center","level":1,"style":{"color":{"text":"#ffffff"},"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|40"}},"typography":{"letterSpacing":"-0.035em","lineHeight":"1"}},"fontSize":"gigantic"} -->
<h1 class="wp-block-heading has-text-align-center has-text-color has-gigantic-font-size" style="color:#ffffff;margin-top:0;margin-bottom:var(--wp--preset--spacing--40);letter-spacing:-0.035em;line-height:1"><?php echo esc_html__( 'SEO Agency Home Page Hero - Editable', 'lennox' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|50"}},"color":{"text":"#ffffffbd"}}} -->
<p class="has-text-align-center has-text-color" style="color:#ffffffbd;margin-top:0;margin-bottom:var(--wp--preset--spacing--50)"><?php echo wp_kses_post( __( 'This hero section is now editable. To change the text, images, buttons, or layout, go to your WordPress dashboard, click 'Appearance' > 'Editor', then click on the front page template or this hero block pattern to customize it directly in the block editor.,\lennox' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons" style="margin-top:0;margin-bottom:0"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#about"><?php echo esc_html__( 'Get Started', 'lennox' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html__( 'Learn More', 'lennox' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"},"padding":{"left":"var:preset|spacing|20"}}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--20)"><!-- wp:image {"width":"32px","height":"32px","sizeSlug":"full","linkDestination":"none","className":"is-resized"} -->
<figure class="wp-block-image size-full is-resized"><img src="data:image/svg+xml,%3Csvg width='36' height='34' viewBox='0 0 36 34' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.22871 7.6402C4.37231 7.25351 4.37456 6.73941 4.65951 6.48012C6.31316 4.7592 7.96682 3.03828 9.73329 1.4764C10.9299 0.433092 11.8085 0.708906 12.2283 2.06219C12.8154 4.08247 13.3741 6.13438 13.9044 8.16081C12.3141 8.39041 11.6641 7.74722 10.5151 4.41762C10.2574 4.93088 10.0005 5.27276 9.85687 5.65946C7.9868 11.429 9.79352 20.1113 17.3032 22.6939C20.4204 23.7831 23.6238 24.6061 27.0585 24.4905C29.3861 24.4093 31.6867 24.017 33.8774 22.7803C34.1904 22.6036 34.5594 22.5921 35.014 22.4857C34.979 24.0025 34.0393 24.7039 32.9876 25.0749C31.5663 25.5716 30.0603 25.9919 28.5838 26.1521C22.8766 26.7426 17.6882 25.5069 13.1316 22.604C8.57492 19.7011 6.53479 12.4715 8.40536 6.58768C8.66429 5.78881 8.98001 4.98379 9.32525 3.91863C7.3056 4.96557 6.41648 7.08888 4.5682 7.88885C4.51166 7.83789 4.37031 7.71048 4.22871 7.6402Z' fill='white'/%3E%3C/svg%3E" alt="<?php echo esc_attr__( 'Arrow', 'lennox' ); ?>" style="width:32px;height:32px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"is-cursive","style":{"color":{"text":"#ffffff"},"typography":{"fontStyle":"italic","lineHeight":"1.3"},"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"medium"} -->
<p class="is-cursive has-text-color has-medium-font-size" style="color:#ffffff;margin-top:0;margin-bottom:0;font-style:italic;line-height:1.3"><?php echo wp_kses_post( __( 'Let's start your<br>journey here!', 'lennox' ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->
