<?php
/**
 * OneMoreTree Block Patterns Registration
 * Curated block patterns matching the Next.js visual design system
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register OneMoreTree Block Patterns
 * Curated block patterns matching the Next.js visual design system
 */
function one_more_tree_register_block_patterns() {
    if (!function_exists('register_block_pattern_category')) {
        return;
    }

    // Register Category
    register_block_pattern_category(
        'one-more-tree',
        ['label' => __('One More Tree Patterns', 'one-more-tree')]
    );

    $theme_uri = get_template_directory_uri();

    // 1. Hero Section Pattern - Updated to match current light theme design
    register_block_pattern(
        'one-more-tree/hero-banner',
        [
            'title'       => __('Hero Banner with Light Theme', 'one-more-tree'),
            'description' => __('Full-width hero section with light theme, background image, mission badge, headline, CTA buttons, and 3 metric cards - matches current PHP template design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-full relative w-full min-h-screen overflow-hidden","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-full relative w-full min-h-screen overflow-hidden" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"omt-hero-background absolute inset-0 w-full h-[120%] -top-[10%]"} -->
<div class="wp-block-group omt-hero-background absolute inset-0 w-full h-[120%] -top-[10%]">
<!-- wp:image {"align":"full","sizeSlug":"full","linkDestination":"none","className":"custom-full-width"} -->
<figure class="wp-block-image alignfull size-full custom-full-width"><img src="' . esc_url($theme_uri) . '/assets/images/hero-forest.jpg" alt="Lush forest canopy" class="wp-image-full"/></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"omt-hero-overlay absolute inset-0 bg-gradient-to-b from-white/40 via-white/20 to-background/80"} -->
<div class="wp-block-group omt-hero-overlay absolute inset-0 bg-gradient-to-b from-white/40 via-white/20 to-background/80"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"omt-hero-overlay-side absolute inset-0 bg-gradient-to-r from-white/30 via-transparent to-white/30"} -->
<div class="wp-block-group omt-hero-overlay-side absolute inset-0 bg-gradient-to-r from-white/30 via-transparent to-white/30"></div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"omt-hero-content relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-24 sm:pt-28","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group omt-hero-content relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-24 sm:pt-28">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"hero-mission-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-forest-200 mb-6 sm:mb-8 shadow-medium"} -->
<div class="wp-block-group hero-mission-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-forest-200 mb-6 sm:mb-8 shadow-medium">
<!-- wp:image {"width":24,"height":32,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="24" height="32"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 5vw, 4rem)","lineHeight":"1.1"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2.5rem, 5vw, 4rem);line-height:1.1">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-700-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 2vw, 1.25rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 2vw, 1.25rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"white","textColor":"forest-700","className":"is-style-glass-button"} -->
<div class="wp-block-button is-style-glass-button"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background wp-element-button is-style-glass-button" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"] <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-left:0.5rem"><path d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className":"hero-stat-card"} -->
<div class="wp-block-column hero-stat-card">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-600","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-600-color has-text-color has-playfair-font-family" style="font-size:1.5rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"hero-stat-card"} -->
<div class="wp-block-column hero-stat-card">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-600","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-600-color has-text-color has-playfair-font-family" style="font-size:1.5rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"hero-stat-card"} -->
<div class="wp-block-column hero-stat-card">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-600","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-600-color has-text-color has-playfair-font-family" style="font-size:1.5rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 1.1 Hero - Classic Light Theme (Flat Modern)
    register_block_pattern(
        'one-more-tree/hero-classic-light',
        [
            'title'       => __('Hero - Classic Light Theme (Flat)', 'one-more-tree'),
            'description' => __('Elegant flat hero with solid colors, geometric patterns, and centered content - modern and ergonomic.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header', 'hero'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-classic relative w-full min-h-screen overflow-hidden bg-white","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-classic relative w-full min-h-screen overflow-hidden bg-white" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className="absolute inset-0 leaf-pattern opacity-8"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-8"></div>
<!-- /wp:group -->
<!-- wp:group {"className="absolute top-0 right-0 w-[300px] h-[300px] bg-forest-100 rounded-full"} -->
<div class="wp-block-group absolute top-0 right-0 w-[300px] h-[300px] bg-forest-100 rounded-full"></div>
<!-- /wp:group -->
<!-- wp:group {"className="absolute bottom-20 left-10 w-[200px] h-[200px] bg-emerald-50 rounded-full"} -->
<div class="wp-block-group absolute bottom-20 left-10 w-[200px] h-[200px] bg-emerald-50 rounded-full"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border-2 border-forest-200 mb-8"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border-2 border-forest-200 mb-8">
<!-- wp:image {"width":20,"height":24,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="20" height="24"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 4vw, 3.5rem)","lineHeight":"1.15"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2.5rem, 4vw, 3.5rem);line-height:1.15">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-600-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.5vw, 1.25rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.5vw, 1.25rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"white","textColor":"forest-700","borderColor":"forest-600","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background has-border-color wp-element-button is-style-outline" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"]</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className="bg-forest-50 p-6 rounded-xl border-2 border-forest-100"} -->
<div class="wp-block-column bg-forest-50 p-6 rounded-xl border-2 border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="bg-forest-50 p-6 rounded-xl border-2 border-forest-100"} -->
<div class="wp-block-column bg-forest-50 p-6 rounded-xl border-2 border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="bg-forest-50 p-6 rounded-xl border-2 border-forest-100"} -->
<div class="wp-block-column bg-forest-50 p-6 rounded-xl border-2 border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 1.2 Hero - Forest Gradient Background
    register_block_pattern(
        'one-more-tree/hero-gradient-forest',
        [
            'title'       => __('Hero - Forest Gradient (Flat)', 'one-more-tree'),
            'description' => __('Flat forest gradient hero with solid colors and geometric elements - modern and bold.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header', 'hero'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-forest relative w-full min-h-screen overflow-hidden","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-forest relative w-full min-h-screen overflow-hidden" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"absolute inset-0 bg-gradient-to-br from-forest-600 via-forest-700 to-forest-900"} -->
<div class="wp-block-group absolute inset-0 bg-gradient-to-br from-forest-600 via-forest-700 to-forest-900"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"absolute inset-0 leaf-pattern opacity-5"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-5"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-950/50 to-transparent"} -->
<div class="wp-block-group absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-950/50 to-transparent"></div>
<!-- /wp:group -->

<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-8 shadow-glass"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-8 shadow-glass">
<!-- wp:image {"width":20,"height":24,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="20" height="24"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"white"} -->
<p class="has-white-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 4vw, 3.5rem)","lineHeight":"1.15"}},"fontFamily":"playfair","textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-white-color has-text-color" style="font-size:clamp(2.5rem, 4vw, 3.5rem);line-height:1.15">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-200-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.5vw, 1.25rem)"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:clamp(1rem, 1.5vw, 1.25rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"white","textColor":"forest-700","className":"is-style-glass-button"} -->
<div class="wp-block-button is-style-glass-button"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background wp-element-button is-style-glass-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"forest-200","textColor":"forest-900","className":"is-style-glass-button"} -->
<div class="wp-block-button is-style-glass-button"><a class="wp-block-button__link has-forest-900-color has-forest-200-background-color has-text-color has-background wp-element-button is-style-glass-button" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"]</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className":"glass-card-dark"} -->
<div class="wp-block-column glass-card-dark">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-200"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-200-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"glass-card-dark"} -->
<div class="wp-block-column glass-card-dark">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-200"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-200-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"glass-card-dark"} -->
<div class="wp-block-column glass-card-dark">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-200"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-200-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 1.3 Hero - Image with Elegant Overlay
    register_block_pattern(
        'one-more-tree/hero-image-overlay',
        [
            'title'       => __('Hero - Image with Elegant Overlay', 'one-more-tree'),
            'description' => __('Full-screen image background with sophisticated gradient overlays and centered modern typography.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header', 'hero'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-image relative w-full min-h-screen overflow-hidden","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-image relative w-full min-h-screen overflow-hidden" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"absolute inset-0 w-full h-[110%] -top-[5%]"} -->
<div class="wp-block-group absolute inset-0 w-full h-[110%] -top-[5%]">
<!-- wp:image {"align":"full","sizeSlug":"full","linkDestination":"none","className":"custom-full-width"} -->
<figure class="wp-block-image alignfull size-full custom-full-width"><img src="' . esc_url($theme_uri) . '/assets/images/hero-forest.jpg" alt="Lush forest canopy" class="wp-image-full"/></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"absolute inset-0 bg-gradient-to-b from-black/30 via-black/20 to-background/90"} -->
<div class="wp-block-group absolute inset-0 bg-gradient-to-b from-black/30 via-black/20 to-background/90"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-black/20"} -->
<div class="wp-block-group absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-black/20"></div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/95 backdrop-blur-lg border border-white/30 mb-8 shadow-hero"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/95 backdrop-blur-lg border border-white/30 mb-8 shadow-hero">
<!-- wp:image {"width":20,"height":24,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="20" height="24"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 4vw, 3.5rem)","lineHeight":"1.15"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2.5rem, 4vw, 3.5rem);line-height:1.15">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-600-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.5vw, 1.25rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.5vw, 1.25rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"white","textColor":"forest-700","className":"is-style-glass-button"} -->
<div class="wp-block-button is-style-glass-button"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background wp-element-button is-style-glass-button" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"]</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className":"is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 1.4 Hero - Minimal Clean Design
    register_block_pattern(
        'one-more-tree/hero-minimal',
        [
            'title'       => __('Hero - Minimal Clean Design', 'one-more-tree'),
            'description' => __('Clean, minimal hero with subtle background and elegant centered typography - perfect for modern brands.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header', 'hero'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-minimal relative w-full min-h-screen overflow-hidden bg-background","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-minimal relative w-full min-h-screen overflow-hidden bg-background" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"absolute top-1/4 right-1/4 w-96 h-96 bg-forest-100/30 rounded-full blur-3xl"} -->
<div class="wp-block-group absolute top-1/4 right-1/4 w-96 h-96 bg-forest-100/30 rounded-full blur-3xl"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"absolute bottom-1/4 left-1/4 w-64 h-64 bg-emerald-100/20 rounded-full blur-3xl"} -->
<div class="wp-block-group absolute bottom-1/4 left-1/4 w-64 h-64 bg-emerald-100/20 rounded-full blur-3xl"></div>
<!-- /wp:group -->

<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40","layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-32 sm:pt-40">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:image {"width":16,"height":20,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="16" height="20"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.25rem, 3.5vw, 3rem)","lineHeight":"1.2"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2.25rem, 3.5vw, 3rem);line-height:1.2">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-600-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"2.5rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:2.5rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"transparent","textColor":"forest-700","borderColor":"forest-200","className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-forest-700-color has-background has-border-color wp-element-button is-style-outline" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"]</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:group {"className="grid grid-cols-3 gap-6 max-w-2xl mx-auto mt-12"} -->
<div class="wp-block-group grid grid-cols-3 gap-6 max-w-2xl mx-auto mt-12">
<!-- wp:group {"className":"text-center"} -->
<div class="wp-block-group text-center">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.5rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center"} -->
<div class="wp-block-group text-center">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.5rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center"} -->
<div class="wp-block-group text-center">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.5rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 1.5 Hero - Split Screen Layout
    register_block_pattern(
        'one-more-tree/hero-split-screen',
        [
            'title'       => __('Hero - Split Screen Layout', 'one-more-tree'),
            'description' => __('Modern split-screen hero with content on left and visual element on right - perfect for storytelling.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header', 'hero'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-split relative w-full min-h-screen overflow-hidden bg-background","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-split relative w-full min-h-screen overflow-hidden bg-background" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"absolute inset-0 leaf-pattern opacity-5"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-5"></div>
<!-- /wp:group -->

<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-24","layout":{"type":"constrained","contentSize":"1400px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-24">
<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal","flexWrap":"wrap"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"flex-1 lg:w-1/2"} -->
<div class="wp-block-group flex-1 lg:w-1/2">
<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"flex-start"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:image {"width":16,"height":20,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="16" height="20"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.875rem">[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.75rem)","lineHeight":"1.2"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h1 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.75rem);line-height:1.2">[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"] <span style="font-style:italic" class="has-forest-600-color">[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="hero.subtitle" option="omt_admin_hero" fallback="Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests."]</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"flex-start","flexWrap":"wrap"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="#join">[omt_text key="hero.primary_cta_text" option="omt_admin_hero" fallback="Join the Movement"]</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"white","textColor":"forest-700","className":"is-style-glass-button"} -->
<div class="wp-block-button is-style-glass-button"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background wp-element-button is-style-glass-button" href="#story">[omt_text key="hero.secondary_cta_text" option="omt_admin_hero" fallback="Watch the Journey"]</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:group {"className="grid grid-cols-3 gap-4 mt-8"} -->
<div class="wp-block-group grid grid-cols-3 gap-4 mt-8">
<!-- wp:group {"className":"text-center p-4 rounded-xl bg-forest-50 border border-forest-100"} -->
<div class="wp-block-group text-center p-4 rounded-xl bg-forest-50 border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="hero.stat1_value" option="omt_admin_hero" fallback="1M"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat1_label" option="omt_admin_hero" fallback="Trees Planted"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center p-4 rounded-xl bg-forest-50 border border-forest-100"} -->
<div class="wp-block-group text-center p-4 rounded-xl bg-forest-50 border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="hero.stat2_value" option="omt_admin_hero" fallback="50+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat2_label" option="omt_admin_hero" fallback="Projects"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center p-4 rounded-xl bg-forest-50 border border-forest-100"} -->
<div class="wp-block-group text-center p-4 rounded-xl bg-forest-50 border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="hero.stat3_value" option="omt_admin_hero" fallback="10K+"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">[omt_text key="hero.stat3_label" option="omt_admin_hero" fallback="Volunteers"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="flex-1 lg:w-1/2 mt-8 lg:mt-0"} -->
<div class="wp-block-group flex-1 lg:w-1/2 mt-8 lg:mt-0">
<!-- wp:image {"align":"full","sizeSlug":"large","linkDestination":"none","className":"rounded-2xl shadow-hero"} -->
<figure class="wp-block-image alignfull size-large rounded-2xl shadow-hero"><img src="' . esc_url($theme_uri) . '/assets/images/hero-forest.jpg" alt="Forest restoration work" class="wp-image-large"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 2.1 About - Clean Modern
    register_block_pattern(
        'one-more-tree/about-clean',
        [
            'title'       => __('About - Clean Modern', 'one-more-tree'),
            'description' => __('Clean, modern about section with subtle gradient and elegant typography - perfect for storytelling.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'about'],
            'content'     => '<!-- wp:group {"align":"full","className":"relative w-full py-20 sm:py-28 bg-background","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-background" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="absolute top-1/4 right-1/4 w-96 h-96 bg-forest-100/20 rounded-full blur-3xl"} -->
<div class="wp-block-group absolute top-1/4 right-1/4 w-96 h-96 bg-forest-100/20 rounded-full blur-3xl"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">OUR STORY</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="about.section_title" option="omt_admin_about_hero" fallback="Growing forests, building hope in Spain"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="about.description" option="omt_admin_about_hero" fallback="We started as a conversation among friends about the need for more trees in our cities. Today, we are a movement of thousands of volunteers planting forests across Spain, one tree at a time."]</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className":"is-style-value-card"} -->
<div class="wp-block-column is-style-value-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_1_title" option="omt_admin_about_values" fallback="Start with seeds"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_1_desc" option="omt_admin_about_values" fallback="We nurture native species from seed to sapling in our nurseries."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-value-card"} -->
<div class="wp-block-column is-style-value-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_2_title" option="omt_admin_about_values" fallback="Plant with care"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_2_desc" option="omt_admin_about_values" fallback="Each tree is planted with attention to soil, water, and companion plants."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-value-card"} -->
<div class="wp-block-column is-style-value-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_3_title" option="omt_admin_about_values" fallback="Grow together"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_3_desc" option="omt_admin_about_values" fallback="Communities come together to plant and care for forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-value-card"} -->
<div class="wp-block-column is-style-value-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_4_title" option="omt_admin_about_values" fallback="Watch it flourish"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_4_desc" option="omt_admin_about_values" fallback="We monitor growth and ensure long-term forest health."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 2.2 About - Card Grid
    register_block_pattern(
        'one-more-tree/about-card-grid',
        [
            'title'       => __('About - Card Grid Layout', 'one-more-tree'),
            'description' => __('About section with card grid layout and elegant spacing - modern and organized.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'about'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-forest-50","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-forest-50" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="absolute inset-0 leaf-pattern opacity-5"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-5"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">OUR STORY</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="about.section_title" option="omt_admin_about_hero" fallback="Growing forests, building hope in Spain"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="about.description" option="omt_admin_about_hero" fallback="We started as a conversation among friends about the need for more trees in our cities. Today, we are a movement of thousands of volunteers planting forests across Spain, one tree at a time."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12"} -->
<div class="wp-block-group grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
<!-- wp:group {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-group bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_1_title" option="omt_admin_about_values" fallback="Start with seeds"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_1_desc" option="omt_admin_about_values" fallback="We nurture native species from seed to sapling in our nurseries."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-group bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_2_title" option="omt_admin_about_values" fallback="Plant with care"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_2_desc" option="omt_admin_about_values" fallback="Each tree is planted with attention to soil, water, and companion plants."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-group bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_3_title" option="omt_admin_about_values" fallback="Grow together"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_3_desc" option="omt_admin_about_values" fallback="Communities come together to plant and care for forests."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-group bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_4_title" option="omt_admin_about_values" fallback="Watch it flourish"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_4_desc" option="omt_admin_about_values" fallback="We monitor growth and ensure long-term forest health."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 3.1 Projects - Clean List
    register_block_pattern(
        'one-more-tree/projects-clean',
        [
            'title'       => __('Projects - Clean List', 'one-more-tree'),
            'description' => __('Clean project list with elegant cards and modern spacing - organized and readable.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'projects'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-background","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-background" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">OUR PROJECTS</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="projects.title" option="omt_admin_projects" fallback="Forest Restoration Projects"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="projects.description" option="omt_admin_projects" fallback="Explore our active forest restoration projects across Spain. Each project is a step towards our goal of 1 million trees."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="mt-12"} -->
<div class="wp-block-group mt-12">
<!-- wp:separator {"className="is-style-forest-separator"} -->
<hr class="wp-block-separator is-style-forest-separator"/>
<!-- /wp:separator -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_live_map show_all="true" max_locations="10"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 4.1 Impact - Minimal Stats
    register_block_pattern(
        'one-more-tree/impact-minimal',
        [
            'title'       => __('Impact - Minimal Stats', 'one-more-tree'),
            'description' => __('Minimal impact section with clean statistics cards - subtle and elegant.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'impact'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-forest-50","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-forest-50" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">OUR IMPACT</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="impact.title" option="omt_admin_impact" fallback="Measuring Our Impact"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="impact.description" option="omt_admin_impact" fallback="Every tree planted is a step towards a greener future. Here is the impact we have made together."]</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className="is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_impact_kpis]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Key Performance Indicators</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_stats type="impact"]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Impact Statistics</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="is-style-stat-card"} -->
<div class="wp-block-column is-style-stat-card">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.75rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.75rem">[omt_stories]</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Success Stories</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 5.1 FAQ - Clean Accordion
    register_block_pattern(
        'one-more-tree/faq-clean',
        [
            'title'       => __('FAQ - Clean Layout', 'one-more-tree'),
            'description' => __('Clean FAQ section with elegant accordion style - modern and readable.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'faq'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-background","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-background" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">FAQ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="faq.title" option="omt_admin_faq" fallback="Frequently Asked Questions"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="faq.description" option="omt_admin_faq" fallback="Find answers to common questions about our projects, volunteering, and donations."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="mt-12"} -->
<div class="wp-block-group mt-12">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_faq max_items="10"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 6.1 Contact - Simple Form
    register_block_pattern(
        'one-more-tree/contact-simple',
        [
            'title'       => __('Contact - Simple Form', 'one-more-tree'),
            'description' => __('Simple contact form with clean layout - modern and functional.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'contact'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-background","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-background" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="absolute top-1/4 left-1/4 w-96 h-96 bg-forest-100/20 rounded-full blur-3xl"} -->
<div class="wp-block-group absolute top-1/4 left-1/4 w-96 h-96 bg-forest-100/20 rounded-full blur-3xl"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">CONTACT US</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="contact.title" option="omt_admin_contact_page" fallback="Get in Touch"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="contact.description" option="omt_admin_contact_page" fallback="Have questions about our projects or want to get involved? We would love to hear from you."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="mt-12"} -->
<div class="wp-block-group mt-12">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_contact_form show_interest="true"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 7.1 Get Involved - Action Cards
    register_block_pattern(
        'one-more-tree/get-involved-cards',
        [
            'title'       => __('Get Involved - Action Cards', 'one-more-tree'),
            'description' => __('Get involved section with action cards - inspiring and actionable.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'get-involved'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-forest-50","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-forest-50" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="absolute inset-0 leaf-pattern opacity-5"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-5"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">GET INVOLVED</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="get_involved.title" option="omt_admin_get_involved" fallback="Join the Movement"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="get_involved.description" option="omt_admin_get_involved" fallback="There are many ways to contribute to our mission. Choose how you want to make a difference."]</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-column bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">Plant Trees</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Join our planting events and get your hands in the soil.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-column bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">Donate</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Support our work with a contribution to plant more trees.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="bg-white p-6 rounded-xl shadow-soft border border-forest-100"} -->
<div class="wp-block-column bg-white p-6 rounded-xl shadow-soft border border-forest-100">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.25rem">Spread the Word</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">Share our mission with your friends and community.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 8.1 Volunteer - Clean Signup
    register_block_pattern(
        'one-more-tree/volunteer-clean',
        [
            'title'       => __('Volunteer - Clean Signup', 'one-more-tree'),
            'description' => __('Clean volunteer signup form with modern layout - inviting and clear.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'volunteer'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-background","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-background" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1000px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-50 border border-forest-200 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">VOLUNTEER</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="volunteer.title" option="omt_admin_volunteer_page" fallback="Become a Volunteer"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="volunteer.description" option="omt_admin_volunteer_page" fallback="Join our community of volunteers and help plant trees across Spain. No experience needed, just enthusiasm."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="mt-12"} -->
<div class="wp-block-group mt-12">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_contact_form type="volunteer"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 9.1 Donate - Tiers
    register_block_pattern(
        'one-more-tree/donate-tiers',
        [
            'title'       => __('Donate - Donation Tiers', 'one-more-tree'),
            'description' => __('Donation section with elegant tier cards - modern and motivating.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'donate'],
            'content'     => '<!-- wp:group {"align":"full","className="relative w-full py-20 sm:py-28 bg-forest-50","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull relative w-full py-20 sm:py-28 bg-forest-50" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className="absolute inset-0 leaf-pattern opacity-5"} -->
<div class="wp-block-group absolute inset-0 leaf-pattern opacity-5"></div>
<!-- /wp:group -->

<!-- wp:group {"className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-forest-200 mb-6 shadow-soft">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">DONATE</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(2rem, 3vw, 2.5rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(2rem, 3vw, 2.5rem)">[omt_text key="donate.title" option="omt_admin_donate" fallback="Support Our Mission"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 1.25vw, 1.125rem)"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:clamp(1rem, 1.25vw, 1.125rem)">[omt_text key="donate.description" option="omt_admin_donate" fallback="Your contribution helps us plant more trees and restore forests across Spain. Every donation makes a difference."]</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className="mt-12"} -->
<div class="wp-block-group mt-12">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.875rem">[omt_donation_tiers max_tiers="3"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 2. About Section Pattern - Value cards with icons (Original)
    register_block_pattern(
        'one-more-tree/about-section',
        [
            'title'       => __('About Section with Value Cards', 'one-more-tree'),
            'description' => __('About section with four value cards, gradient background, and deliverables stats - matches current PHP template.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'about'],
            'content'     => '<!-- wp:group {"align":"full","className":"gradient-bg leaf-pattern relative min-h-[70vh] w-full py-20 sm:py-32 overflow-hidden","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull gradient-bg leaf-pattern relative min-h-[70vh] w-full py-20 sm:py-32 overflow-hidden" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:group {"className":"absolute top-0 right-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl"} -->
<div class="wp-block-group absolute top-0 right-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl"></div>
<!-- /wp:group -->

<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6"} -->
<div class="wp-block-group inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
<!-- wp:html -->
<svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
<!-- /wp:html -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.75rem">OUR STORY</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(1.875rem, 3vw, 3rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(1.875rem, 3vw, 3rem)">[omt_text key="about.section_title" option="omt_admin_about_hero" fallback="Growing forests, building hope in Spain"]</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:1rem">[omt_text key="about.section_description" option="omt_admin_about_hero" fallback="From a conversation among friends to a movement restoring forests across Spain. We plant trees in schools, sports complexes, senior centers, wildfire-damaged forests, and along the Camino de Santiago."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className":"value-card is-style-value-card"} -->
<div class="wp-block-column value-card is-style-value-card">
<!-- wp:group {"className":"w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4"} -->
<div class="wp-block-group w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
<!-- wp:html -->
<svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_1_title" option="omt_admin_about_values" fallback="Start with seeds"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_1_description" option="omt_admin_about_values" fallback="We plant native trees using science-backed methods for long-term growth."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"value-card is-style-value-card"} -->
<div class="wp-block-column value-card is-style-value-card">
<!-- wp:group {"className":"w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4"} -->
<div class="wp-block-group w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
<!-- wp:html -->
<svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_2_title" option="omt_admin_about_values" fallback="Respect ecosystems"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_2_description" option="omt_admin_about_values" fallback="Restoration must protect biodiversity and strengthen local environments."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"value-card is-style-value-card"} -->
<div class="wp-block-column value-card is-style-value-card">
<!-- wp:group {"className":"w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4"} -->
<div class="wp-block-group w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
<!-- wp:html -->
<svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_3_title" option="omt_admin_about_values" fallback="Commit for the long run"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_3_description" option="omt_admin_about_values" fallback="We focus on survival rates, maintenance, and measurable outcomes."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"value-card is-style-value-card"} -->
<div class="wp-block-column value-card is-style-value-card">
<!-- wp:group {"className":"w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4"} -->
<div class="wp-block-group w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center mb-4">
<!-- wp:html -->
<svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.25rem">[omt_text key="about.value_4_title" option="omt_admin_about_values" fallback="Work with communities"]</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">[omt_text key="about.value_4_description" option="omt_admin_about_values" fallback="Local partners lead, we support with resources, training, and transparency."]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"className="glass-card rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row gap-8 items-start lg:items-center shadow-medium mt-10"} -->
<div class="wp-block-group glass-card rounded-3xl p-6 sm:p-10 flex flex-col lg:flex-row gap-8 items-start lg:items-center shadow-medium mt-10">
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group flex-1">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">What we deliver</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.875rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.875rem">Measurable restoration you can trust</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:1rem">From native species selection to long-term monitoring, our programs are designed to maximize survival and restore ecosystems responsibly.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group flex gap-4">
<!-- wp:group {"className":"text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft"} -->
<div class="wp-block-group text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.875rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.875rem">3M</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">Trees by 2040</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft"} -->
<div class="wp-block-group text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.875rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.875rem">Native</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">Species Focus</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft"} -->
<div class="wp-block-group text-center glass-card rounded-2xl p-4 w-[140px] shadow-soft">
<!-- wp:heading {"level":4,"textAlign":"center","style":{"typography":{"fontSize":"1.875rem"}},"fontFamily":"playfair","textColor":"forest-600"} -->
<h4 class="wp-block-heading has-text-align-center has-playfair-font-family has-forest-600-color has-text-color" style="font-size:1.875rem">Local</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:0.75rem">Community-led</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 3. Navigation Pattern with Ribbon
    register_block_pattern(
        'one-more-tree/navigation-ribbon',
        [
            'title'       => __('Navigation with Ribbon (Flat)', 'one-more-tree'),
            'description' => __('Modern flat navigation with ribbon accent, scroll behavior, and mobile responsiveness - clean and ergonomic.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header'],
            'content'     => '<!-- wp:group {"className":"omt-navigation-ribbon fixed left-0 right-0 z-50 bg-white border-b-2 border-forest-200","layout":{"type":"flex","orientation":"horizontal","justifyContent":"spaceBetween","flexWrap":"nowrap"}} -->
<nav class="wp-block-group omt-navigation-ribbon fixed left-0 right-0 z-50 bg-white border-b-2 border-forest-200">
<!-- wp:group {"className="relative px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"flex","orientation":"horizontal","justifyContent":"spaceBetween","flexWrap":"nowrap"}} -->
<div class="wp-block-group relative px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal","flexWrap":"nowrap"}} -->
<div class="wp-block-group">
<!-- wp:image {"width":32,"height":40,"scale":1,"sizeSlug":"small","linkDestination":"custom"} -->
<figure class="wp-block-image size-small is-resized"><a href="/"><img src="' . esc_url($theme_uri) . '/assets/images/logo.png" alt="OneMoreTree" width="32" height="40"/></a></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem"}},"fontFamily":"playfair"} -->
<p class="has-playfair-font-family" style="font-size:1.125rem">OneMore<span class="has-forest-600-color">Tree</span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="hidden md:flex items-center gap-8"} -->
<div class="wp-block-group hidden md:flex items-center gap-8">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/" class="text-foreground/70 hover:text-forest-600 transition-colors">Home</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/about" class="text-foreground/70 hover:text-forest-600 transition-colors">About</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/projects" class="text-foreground/70 hover:text-forest-600 transition-colors">Projects</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/impact" class="text-foreground/70 hover:text-forest-600 transition-colors">Impact</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/faq" class="text-foreground/70 hover:text-forest-600 transition-colors">FAQ</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p class="has-text-color" style="font-size:0.875rem"><a href="/get-involved" class="text-foreground/70 hover:text-forest-600 transition-colors">Get Involved</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal"}} -->
<div class="wp-block-group">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button is-style-forest-button" href="/get-involved">Donate Now</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="absolute -bottom-1 left-0 right-0 h-1 bg-gradient-to-r from-forest-600 via-forest-500 to-emerald-500"} -->
<div class="wp-block-group absolute -bottom-1 left-0 right-0 h-1 bg-gradient-to-r from-forest-600 via-forest-500 to-emerald-500"></div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</nav>
<!-- /wp:group -->',
        ]
    );

    // 4. Footer Pattern
    register_block_pattern(
        'one-more-tree/footer',
        [
            'title'       => __('One More Tree Footer', 'one-more-tree'),
            'description' => __('Comprehensive footer with brand info, navigation links, social media, newsletter signup, and legal links - matches current PHP template.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'footer'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-footer relative w-full py-16 sm:py-20 overflow-hidden border-t border-earth-200 bg-gradient-to-b from-background to-earth-50","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"default"}} -->
<footer class="wp-block-group alignfull omt-footer relative w-full py-16 sm:py-20 overflow-hidden border-t border-earth-200 bg-gradient-to-b from-background to-earth-50" style="padding-top:4rem;padding-bottom:4rem">
<!-- wp:group {"className":"relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1400px"}} -->
<div class="wp-block-group relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1400px"}} -->
<div class="wp-block-group">
<!-- wp:columns {"style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%">
<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal"}} -->
<div class="wp-block-group">
<!-- wp:image {"width":32,"height":40,"scale":1,"sizeSlug":"small","linkDestination":"custom"} -->
<figure class="wp-block-image size-small is-resized"><a href="/"><img src="' . esc_url($theme_uri) . '/assets/images/logo.png" alt="OneMoreTree" width="32" height="40"/></a></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair"} -->
<p class="has-playfair-font-family" style="font-size:1.25rem">OneMore<span class="has-forest-600-color">Tree</span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">Restoring forests, one tree at a time.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>[omt_social_links style="icons"]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<h4 class="wp-block-heading has-foreground-color has-text-color" style="font-size:0.875rem">ORGANIZATION</h4>
<!-- /wp:heading -->
<!-- wp:list {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<ul style="margin-top:1rem">
<li><a href="/about">About Us</a></li>
<li><a href="/about">Our Team</a></li>
<li><a href="/impact">Impact</a></li>
<li><a href="/about">Press</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<h4 class="wp-block-heading has-foreground-color has-text-color" style="font-size:0.875rem">PROGRAMS</h4>
<!-- /wp:heading -->
<!-- wp:list {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<ul style="margin-top:1rem">
<li><a href="/projects">Reforestation</a></li>
<li><a href="/projects">Live Map</a></li>
<li><a href="/faq">Education</a></li>
<li><a href="/impact">Research</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<h4 class="wp-block-heading has-foreground-color has-text-color" style="font-size:0.875rem">GET INVOLVED</h4>
<!-- /wp:heading -->
<!-- wp:list {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<ul style="margin-top:1rem">
<li><a href="/donate">Donate</a></li>
<li><a href="/volunteer">Volunteer</a></li>
<li><a href="/contact">Partner</a></li>
<li><a href="/get-involved">Get Involved</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<h4 class="wp-block-heading has-foreground-color has-text-color" style="font-size:0.875rem">RESOURCES</h4>
<!-- /wp:heading -->
<!-- wp:list {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<ul style="margin-top:1rem">
<li><a href="/blog">Blog</a></li>
<li><a href="/contact">Contact</a></li>
<li><a href="/volunteer">Volunteer</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"15%"} -->
<div class="wp-block-column" style="flex-basis:15%">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<h4 class="wp-block-heading has-foreground-color has-text-color" style="font-size:0.875rem">CONTACT</h4>
<!-- /wp:heading -->
<!-- wp:list {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<ul style="margin-top:1rem">
<li><a href="mailto:info@onemoretree.eu">info@onemoretree.eu</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"className":"newsletter-card is-style-newsletter-card mb-12 sm:mb-16 shadow-medium"} -->
<div class="wp-block-group newsletter-card is-style-newsletter-card mb-12 sm:mb-16 shadow-medium">
<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal","flexWrap":"wrap"}} -->
<div class="wp-block-group flex flex-col md:flex-row md:items-center md:justify-between gap-4">
<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"1.25rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h4 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.25rem">Stay in the loop</h4>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:1rem">Get updates on our planting projects and impact reports.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph -->
<p>[omt_newsletter_form]</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal","justifyContent":"center"}} -->
<div class="wp-block-group flex flex-col sm:flex-row items-center justify-center gap-4 pt-8 border-t border-earth-200 text-center">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">© 2024 OneMoreTree. All rights reserved.</p>
<!-- /wp:paragraph -->
<!-- wp:group {"layout":{"type":"flex","orientation":"horizontal"}} -->
<div class="wp-block-group flex gap-6 flex-wrap justify-center">
<!-- wp:paragraph -->
<p><a href="#">Privacy Policy</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="#">Terms of Service</a></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p><a href="#">Cookie Policy</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</footer>
<!-- /wp:group -->',
        ]
    );

    // 5. 3-Chapter Story Timeline Pattern with Tailwind
    register_block_pattern(
        'one-more-tree/story-chapters',
        [
            'title'       => __('3-Chapter Restoration Story', 'one-more-tree'),
            'description' => __('Three-stage progressive story milestone cards (The Seed, Growth, Mature Canopy).', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'text'],
            'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group py-16 sm:py-20" style="padding-top:4rem;padding-bottom:4rem">
<!-- wp:heading {"textAlign":"center","level":2,"fontFamily":"playfair"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family text-2xl sm:text-3xl font-bold text-foreground mb-4">How a Forest Reclaims the Land</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color text-base sm:text-lg text-foreground/70 max-w-2xl mx-auto" style="font-size:1.1rem">From tiny seeds to multi-canopy ecosystems, here is the lifecycle of our planting program.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"2rem","margin":{"top":"3rem"}}},"className":"grid grid-cols-1 md:grid-cols-3 gap-8"} -->
<div class="wp-block-columns grid grid-cols-1 md:grid-cols-3 gap-8" style="margin-top:3rem">
<!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}},"backgroundColor":"white","className="glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300"} -->
<div class="wp-block-column glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300 has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","fontWeight":"700"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color text-xs font-bold uppercase tracking-wider mb-2" style="font-size:0.75rem">CHAPTER 01 • DAY 1</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-playfair-font-family text-xl font-bold text-foreground mb-3" style="font-size:1.5rem">It Starts with a Seed</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}}} -->
<p class="text-sm text-foreground/70 leading-relaxed" style="font-size:0.95rem">Carefully selected native acorns and seeds are nestled into prepared earth, paired with organic moisture retainers.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}},"backgroundColor":"white","className="glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300"} -->
<div class="wp-block-column glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300 has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","fontWeight":"700"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color text-xs font-bold uppercase tracking-wider mb-2" style="font-size:0.75rem">CHAPTER 02 • YEAR 2</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-playfair-font-family text-xl font-bold text-foreground mb-3" style="font-size:1.5rem">Growing Through Adversity</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}}} -->
<p class="text-sm text-foreground/70 leading-relaxed" style="font-size:0.95rem">Young saplings brave harsh Iberian summers, protected by protective mesh guards and regular volunteer watering.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}},"backgroundColor":"white","className="glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300"} -->
<div class="wp-block-column glass-card rounded-3xl border border-earth-200 shadow-soft hover:shadow-elevated transition-all duration-300 has-white-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","fontWeight":"700"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color text-xs font-bold uppercase tracking-wider mb-2" style="font-size:0.75rem">CHAPTER 03 • YEAR 10+</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-playfair-font-family text-xl font-bold text-foreground mb-3" style="font-size:1.5rem">Thriving Canopy</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.95rem"}}} -->
<p class="text-sm text-foreground/70 leading-relaxed" style="font-size:0.95rem">A mature woodland stabilizes topsoil, shelters diverse wildlife, and captures thousands of tons of atmospheric carbon.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 3. Project Showcase Feature Card Pattern with Tailwind
    register_block_pattern(
        'one-more-tree/project-showcase',
        [
            'title'       => __('Project Feature Showcase', 'one-more-tree'),
            'description' => __('2-column project spotlight with image, tree statistics, and action link.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'featured'],
            'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"3rem","bottom":"3rem"}}},"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group py-12 sm:py-16" style="padding-top:3rem;padding-bottom:3rem">
<!-- wp:columns {"style":{"spacing":{"blockGap":"2.5rem"}},"className="glass-card rounded-3xl bg-white border border-earth-200 p-8 shadow-elevated items-center hover:shadow-2xl transition-all duration-300"} -->
<div class="wp-block-columns glass-card rounded-3xl bg-white border border-earth-200 p-8 shadow-elevated items-center hover:shadow-2xl transition-all duration-300">
<!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%">
<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className="rounded-2xl overflow-hidden aspect-[4/3] object-cover hover:scale-105 transition-transform duration-300"} -->
<figure class="wp-block-image size-large rounded-2xl overflow-hidden aspect-[4/3] object-cover hover:scale-105 transition-transform duration-300"><img src="' . esc_url($theme_uri) . '/assets/images/hero-forest.jpg" alt="Restoration site"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem","fontWeight":"700"}},"textColor":"forest-600"} -->
<p class="has-forest-600-color has-text-color text-xs font-bold uppercase tracking-wider mb-2" style="font-size:0.85rem">GRANADA, ANDALUSIA • ACTIVE SITE</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"2rem"}},"fontFamily":"playfair"} -->
<h2 class="wp-block-heading has-playfair-font-family text-2xl sm:text-3xl font-bold text-foreground mb-4" style="font-size:2rem">Sierra Nevada Mountain Forest Recovery</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p class="text-base text-foreground/70 leading-relaxed mb-6">Re-establishing native pine, oak, and juniper across fire-damaged mountain slopes to protect local watersheds and prevent soil erosion.</p>
<!-- /wp:paragraph -->
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group flex gap-4 mb-6">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9rem","fontWeight":"700"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color text-sm font-semibold text-foreground/70" style="font-size:0.9rem">🌲 42,500 Trees Planted</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.9rem","fontWeight":"700"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color text-sm font-semibold text-foreground/70" style="font-size:0.9rem">🤝 850 Volunteers</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1.5rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:1.5rem">
<!-- wp:button {"backgroundColor":"forest-700","textColor":"white"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-700-background-color has-text-color has-background wp-element-button hover:bg-forest-600 transition-colors" href="/projects/">View Project Details</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 4. Call to Action / Donation Prompt with Tailwind
    register_block_pattern(
        'one-more-tree/cta-box',
        [
            'title'       => __('Call to Action / Donation Prompt', 'one-more-tree'),
            'description' => __('Full-width high-contrast card urging readers to plant a tree or get involved.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'call-to-action'],
            'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem","left":"2rem","right":"2rem"}}},"backgroundColor":"forest-950","textColor":"white","layout":{"type":"constrained","contentSize":"900px"}} -->
<div class="wp-block-group alignfull has-white-color has-forest-950-background-color has-text-color has-background py-16 sm:py-20 px-4 sm:px-6 lg:px-12" style="padding-top:4rem;padding-right:2rem;padding-bottom:4rem;padding-left:2rem">
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"2.5rem"}},"textColor":"white","fontFamily":"playfair"} -->
<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color has-playfair-font-family text-3xl sm:text-4xl font-bold mb-6" style="font-size:2.5rem">Every Great Forest Began with One Seed</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.1rem"}},"textColor":"forest-200"} -->
<p class="has-text-align-center has-forest-200-color has-text-color text-base sm:text-lg text-forest-200 max-w-2xl mx-auto mb-8" style="font-size:1.1rem">Join thousands of individuals and companies planting native trees across Spain. Track your tree\'s growth with live GPS coordinates.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem">
<!-- wp:button {"backgroundColor":"emerald-400","textColor":"forest-950"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-forest-950-color has-emerald-400-background-color has-text-color has-background wp-element-button hover:bg-emerald-300 transition-colors font-semibold" href="/donate/">Plant a Tree Today ($25)</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 5. Donation 3D Section Pattern with Tailwind
    register_block_pattern(
        'one-more-tree/donation-3d-section',
        [
            'title'       => __('Donation 3D Section', 'one-more-tree'),
            'description' => __('Interactive donation section with 3D card effect, tier selection, and impact statistics - pixel perfect match to Next.js design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'call-to-action'],
            'content'     => '<!-- wp:group {"align":"full","className":"page-header-texture leaf-pattern relative overflow-hidden","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull page-header-texture leaf-pattern relative overflow-hidden py-20 px-4 sm:px-6 lg:px-12" style="padding-top:5rem;padding-bottom:5rem">
<!-- wp:columns {"style":{"spacing":{"blockGap":"2.5rem"}}} -->
<div class="wp-block-columns gap-10">
<!-- wp:column {"width":"55%"} -->
<div class="wp-block-column" style="flex-basis:55%">
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem","fontWeight":"600"}},"backgroundColor":"forest-100","textColor":"forest-700","className="rounded-full px-4 py-2"} -->
<p class="rounded-full px-4 py-2 has-forest-700-color has-forest-100-background-color has-text-color has-background text-sm font-semibold mb-4" style="font-size:0.875rem">✨ Plant one more tree</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"clamp(1.875rem, 4vw, 3rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color text-3xl sm:text-4xl lg:text-5xl font-bold mb-6" style="font-size:clamp(1.875rem, 4vw, 3rem)">Give with confidence in a beautiful, interactive way</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color text-base sm:text-lg text-foreground/70 leading-relaxed mb-8" style="font-size:1.125rem">Explore each donation tier with a 3D preview card, then choose the amount that helps grow forests across Spain. This section is designed to feel engaging, warm, and effortless.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"0.75rem"}}} -->
<div class="wp-block-columns gap-3">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--forest-500)"}},"backgroundColor":"forest-50","className="omt-tier shadow-medium hover:shadow-elevated transition-all duration-300 cursor-pointer"} -->
<div class="wp-block-group omt-tier shadow-medium hover:shadow-elevated transition-all duration-300 cursor-pointer has-forest-50-background-color has-background p-4" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--forest-500);padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"forest-600"} -->
<p class="has-forest-600-color has-text-color text-xs font-bold uppercase tracking-wider mb-1" style="font-size:0.75rem">Tier</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.875rem","fontWeight":"600"}}} -->
<p class="text-2xl sm:text-3xl font-bold text-foreground mb-2" style="font-size:1.875rem">$25</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color text-sm text-foreground/70" style="font-size:0.875rem">Supports tree planting, site care, and local communities.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--slate-200)"}},"backgroundColor":"white","className":"omt-tier"} -->
<div class="wp-block-group omt-tier has-white-background-color has-background" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--slate-200);padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"forest-600"} -->
<p class="has-forest-600-color has-text-color" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.18em">Tier</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.875rem","fontWeight":"600"}}} -->
<p style="font-size:1.875rem;font-weight:600">$50</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">Supports tree planting, site care, and local communities.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--slate-200)"}},"backgroundColor":"white","className":"omt-tier"} -->
<div class="wp-block-group omt-tier has-white-background-color has-background" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--slate-200);padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.18em"}},"textColor":"forest-600"} -->
<p class="has-forest-600-color has-text-color" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.18em">Tier</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.875rem","fontWeight":"600"}}} -->
<p style="font-size:1.875rem;font-weight:600">$100</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">Supports tree planting, site care, and local communities.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"45%"} -->
<div class="wp-block-column" style="flex-basis:45%">
<!-- wp:group {"className":"omt-donation-card","style":{"spacing":{"padding":{"top":"1.5rem","bottom":"1.5rem","left":"1.5rem","right":"1.5rem"}}}} -->
<div class="wp-block-group omt-donation-card" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem">
<!-- wp:group {"className":"omt-donation-card-inner","style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}}} -->
<div class="wp-block-group omt-donation-card-inner" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:group {"className":"omt-donation-card-overlay"} -->
<div class="wp-block-group omt-donation-card-overlay"></div>
<!-- /wp:group -->

<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<div class="wp-block-group">
<!-- wp:group -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem","textTransform":"uppercase","letterSpacing":"0.2em"}},"textColor":"forest-600"} -->
<p class="has-forest-600-color has-text-color" style="font-size:0.875rem;text-transform:uppercase;letter-spacing:0.2em">Donation</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"textColor":"foreground"} -->
<h3 class="wp-block-heading has-foreground-color has-text-color" style="font-size:1.5rem">$25 support</h3>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem","left":"0.5rem","right":"0.5rem"}},"border":{"radius":"1.5rem"}},"backgroundColor":"forest-600","textColor":"white"} -->
<div class="wp-block-group has-white-color has-forest-600-background-color has-text-color has-background" style="border-radius:1.5rem;padding-top:0.5rem;padding-right:0.5rem;padding-bottom:0.5rem;padding-left:0.5rem">
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">❤️</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"omt-donation-stat omt-donation-stat--trees","style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem"}}} -->
<div class="wp-block-group omt-donation-stat omt-donation-stat--trees" style="border-radius:1.5rem;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"forest-700"} -->
<p class="has-forest-700-color has-text-color" style="font-size:0.875rem">Trees planted</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.875rem","fontWeight":"600"}}} -->
<p style="font-size:1.875rem;font-weight:600">75</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"className":"omt-donation-stat omt-donation-stat--community","style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem"}}} -->
<div class="wp-block-group omt-donation-stat omt-donation-stat--community" style="border-radius:1.5rem;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:0.875rem">Community support</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.875rem","fontWeight":"600"}}} -->
<p style="font-size:1.875rem;font-weight:600">+2 volunteers</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:group {"className":"omt-donation-impact","style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1rem","right":"1rem"}},"border":{"radius":"1.5rem"}}} -->
<div class="wp-block-group omt-donation-impact" style="border-radius:1.5rem;padding-top:1rem;padding-right:1rem;padding-bottom:1rem;padding-left:1rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem","fontWeight":"600"}}} -->
<p style="font-size:0.875rem;font-weight:600">Impact snapshot</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} -->
<p style="font-size:0.875rem">Your donation helps plant trees, improve soil health, and support local reforestation teams in Spain.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem","left":"1.5rem","right":"1.5rem"}},"border":{"radius":"1.5rem"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button" style="border-radius:1.5rem;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem">Donate $25</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 6. Counter Section Pattern - Pixel Perfect Next.js Mirroring
    register_block_pattern(
        'one-more-tree/counter-section',
        [
            'title'       => __('Counter Section', 'one-more-tree'),
            'description' => __('Three-column statistics section with white cards and shadows - pixel perfect match to Next.js design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'featured'],
            'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group" style="padding-top:4rem;padding-bottom:4rem">
<!-- wp:columns {"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem"}},"backgroundColor":"white","className":"shadow-soft text-center"} -->
<div class="wp-block-group shadow-soft text-center has-white-background-color has-background" style="border-radius:1.5rem;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.25em"}},"textColor":"forest-500"} -->
<p class="has-forest-500-color has-text-color" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.25em">Trees tracked</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.25rem","fontWeight":"600"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:2.25rem;font-weight:600">1,000,000</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem"}},"backgroundColor":"white","className":"shadow-soft text-center"} -->
<div class="wp-block-group shadow-soft text-center has-white-background-color has-background" style="border-radius:1.5rem;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.25em"}},"textColor":"forest-500"} -->
<p class="has-forest-500-color has-text-color" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.25em">Projects active</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.25rem","fontWeight":"600"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:2.25rem;font-weight:600">50+</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem"}},"backgroundColor":"white","className":"shadow-soft text-center"} -->
<div class="wp-block-group shadow-soft text-center has-white-background-color has-background" style="border-radius:1.5rem;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.25em"}},"textColor":"forest-500"} -->
<p class="has-forest-500-color has-text-color" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.25em">Volunteers engaged</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"2.25rem","fontWeight":"600"}},"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color" style="font-size:2.25rem;font-weight:600">10,000+</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 7. Mission Section Pattern - Pixel Perfect Next.js Mirroring
    register_block_pattern(
        'one-more-tree/mission-section',
        [
            'title'       => __('Mission Section', 'one-more-tree'),
            'description' => __('Three-column mission cards with white background and borders - pixel perfect match to Next.js design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'featured'],
            'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"backgroundColor":"white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group has-white-background-color has-background" style="padding-top:4rem;padding-bottom:4rem">
<!-- wp:columns {"style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--earth-200)"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--earth-200);padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.5rem">Europe First</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color">Our mission is rooted in Europe: local forests, local communities, local restoration.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--earth-200)"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--earth-200);padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.5rem">Community Power</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color">Volunteers and partners work together to plant, protect, and nurture every site.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}},"border":{"radius":"1.5rem","width":"1px","color":"var(--wp--preset--color--earth-200)"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:1.5rem;border-width:1px;border-color:var(--wp--preset--color--earth-200);padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h3 class="wp-block-heading has-playfair-font-family has-foreground-color has-text-color" style="font-size:1.5rem">Visible Progress</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"textColor":"foreground"} -->
<p class="has-foreground-color has-text-color">Every planting story is documented with images and milestones so supporters see real results.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 8. Featured Progress Section Pattern - Pixel Perfect Next.js Mirroring
    register_block_pattern(
        'one-more-tree/featured-progress',
        [
            'title'       => __('Featured Progress Section', 'one-more-tree'),
            'description' => __('Center-aligned section heading for featured progress content - pixel perfect match to Next.js design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'text'],
            'content'     => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group" style="padding-top:4rem;padding-bottom:4rem">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1100px"}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem","textTransform":"uppercase","letterSpacing":"0.3em"}},"textColor":"forest-400"} -->
<p class="has-text-align-center has-forest-400-color has-text-color" style="font-size:0.875rem;text-transform:uppercase;letter-spacing:0.3em">Featured Progress</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"clamp(1.875rem, 4vw, 3rem)"}},"fontFamily":"playfair","textColor":"foreground"} -->
<h2 class="wp-block-heading has-text-align-center has-playfair-font-family has-foreground-color has-text-color" style="font-size:clamp(1.875rem, 4vw, 3rem)">How our first site grew from seedling to canopy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.125rem"}},"textColor":"foreground"} -->
<p class="has-text-align-center has-foreground-color has-text-color" style="font-size:1.125rem">We track every planting with a clear timeline and visuals, so supporters can follow the journey from the first sapling to a thriving forest.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 9. Rich Editorial Article Layout Pattern
    register_block_pattern(
        'one-more-tree/rich-article',
        [
            'title'       => __('Rich Editorial Article Layout', 'one-more-tree'),
            'description' => __('Formatted article with lead paragraph, drop cap, callout quote, and key takeaways container.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'text'],
            'content'     => '<!-- wp:paragraph {"dropCap":true,"style":{"typography":{"fontSize":"1.15rem"}}} -->
<p class="has-drop-cap" style="font-size:1.15rem">Forest restoration in Mediterranean climates requires a deep understanding of soil microbiomes, native canopy succession, and strategic water retention techniques. Across southern Spain, desertification challenges are being reversed through community-led ecological stewardship.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"fontFamily":"playfair"} -->
<h2 class="wp-block-heading has-playfair-font-family">Understanding the Microclimate Resilience</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Unlike single-species commercial plantations, biodiverse native groves interplant Holm Oak, Aleppo Pine, and Wild Olive alongside drought-tolerant shrubs like rosemary and thyme. This multilayered structure prevents soil moisture evaporation even during peak summer heatwaves.</p>
<!-- /wp:paragraph -->

<!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p>"Restoration isn\'t just putting seedlings in the ground; it is about rebuilding the living sponge of the earth that sustains future generations."</p><cite>Elena Morales, Head of Ecology</cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}},"backgroundColor":"forest-50","className":"rounded-2xl border border-forest-200"} -->
<div class="wp-block-group rounded-2xl border border-forest-200 has-forest-50-background-color has-background" style="padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem">
<!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.25rem"}},"textColor":"forest-800","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-forest-800-color has-text-color has-playfair-font-family" style="font-size:1.25rem">Key Takeaways from the Field</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li><strong>Native Diversity:</strong> Planting at least 4 complementary species reduces pest vulnerability by 60%.</li>
<li><strong>Organic Mycorrhizae:</strong> Pre-inoculating root systems improves first-year survival rates past 85%.</li>
<li><strong>Community Care:</strong> Quarterly monitoring by local volunteers ensures long-term forest health.</li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:group -->',
        ]
    );
}
add_action('init', 'one_more_tree_register_block_patterns');
