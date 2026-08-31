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
 * Register OneMoreTree Block Pattern Category and Patterns
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

    // 1. Hero Section Pattern - Pixel Perfect Next.js Mirroring with Tailwind
    register_block_pattern(
        'one-more-tree/hero-banner',
        [
            'title'       => __('Hero Banner with Full Background', 'one-more-tree'),
            'description' => __('Full-width hero section with background image, mission badge, headline, CTA buttons, and 3 metric cards - pixel perfect match to Next.js design.', 'one-more-tree'),
            'categories'  => ['one-more-tree', 'header'],
            'content'     => '<!-- wp:group {"align":"full","className":"omt-hero-full relative w-full min-h-screen overflow-hidden","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull omt-hero-full relative w-full min-h-screen overflow-hidden" style="padding-top:0;padding-bottom:0">
<!-- wp:group {"className":"omt-hero-background absolute inset-0"} -->
<div class="wp-block-group omt-hero-background absolute inset-0">
<!-- wp:image {"align":"full","sizeSlug":"full","linkDestination":"none","className":"custom-full-width"} -->
<figure class="wp-block-image alignfull size-full custom-full-width"><img src="' . esc_url($theme_uri) . '/assets/images/hero-forest.jpg" alt="Lush forest canopy" class="wp-image-full"/></figure>
<!-- /wp:image -->
<!-- wp:group {"className":"omt-hero-overlay absolute inset-0 bg-gradient-to-r from-forest-900/80 via-forest-900/50 to-transparent"} -->
<div class="wp-block-group omt-hero-overlay absolute inset-0 bg-gradient-to-r from-forest-900/80 via-forest-900/50 to-transparent"></div>
<!-- /wp:group -->
<!-- wp:group {"className":"omt-hero-overlay-side absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-forest-900/90 to-transparent"} -->
<div class="wp-block-group omt-hero-overlay-side absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-forest-900/90 to-transparent"></div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className="omt-hero-content relative z-10 flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-12 xl:px-20","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group omt-hero-content relative z-10 flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-12 xl:px-20">
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group">
<!-- wp:group {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group">
<!-- wp:group {"className="omt-hero-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-soft"} -->
<div class="wp-block-group omt-hero-badge inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-soft">
<!-- wp:image {"width":32,"height":32,"scale":1,"sizeSlug":"small","linkDestination":"none"} -->
<figure class="wp-block-image size-small is-resized"><img src="' . esc_url($theme_uri) . '/assets/images/logo-colorful.png" alt="Our Mission" width="32" height="32"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem"}},"textColor":"white"} -->
<p class="has-white-color has-text-color" style="font-size:0.75rem">Planting trees, restoring forests.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"clamp(2.5rem, 5vw, 4rem)","lineHeight":"1.1"}},"fontFamily":"playfair","textColor":"white"} -->
<h1 class="wp-block-heading has-text-align-center has-playfair-font-family has-white-color has-text-color" style="font-size:clamp(2.5rem, 5vw, 4rem);line-height:1.1">One More <span style="font-style:italic" class="has-forest-200-color">Tree</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1rem, 2vw, 1.25rem)"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:clamp(1rem, 2vw, 1.25rem)">Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem","bottom":"3rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem;margin-bottom:3rem">
<!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"w-full sm:w-auto px-8 py-4"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-forest-600-background-color has-text-color has-background wp-element-button w-full sm:w-auto px-8 py-4" href="#join">Join the Movement</a></div>
<!-- /wp:button -->
<!-- wp:button {"style":{},"backgroundColor":"white","textColor":"forest-700","className":"is-style-outline w-full sm:w-auto px-8 py-4 flex items-center justify-center gap-2 group"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-forest-700-color has-white-background-color has-text-color has-background wp-element-button w-full sm:w-auto px-8 py-4 flex items-center justify-center gap-2 group" href="#story">Watch the Journey <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-left:0.5rem"><path d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"1rem"}}} -->
<div class="wp-block-columns">
<!-- wp:column {"className="omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20"} -->
<div class="wp-block-column omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-200","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-200-color has-text-color has-playfair-font-family" style="font-size:1.5rem">1M</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.75rem">Trees Planted</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20"} -->
<div class="wp-block-column omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-200","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-200-color has-text-color has-playfair-font-family" style="font-size:1.5rem">50+</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.75rem">Projects</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className="omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20"} -->
<div class="wp-block-column omt-hero-stat-card glass-card rounded-2xl p-4 sm:p-6 backdrop-blur-sm bg-white/10 border border-white/20">
<!-- wp:heading {"level":3,"textAlign":"center","style":{"typography":{"fontSize":"1.5rem"}},"textColor":"forest-200","fontFamily":"playfair"} -->
<h3 class="wp-block-heading has-text-align-center has-forest-200-color has-text-color has-playfair-font-family" style="font-size:1.5rem">10K+</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.75rem"}},"textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color" style="font-size:0.75rem">Volunteers</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->',
        ]
    );

    // 2. 3-Chapter Story Timeline Pattern with Tailwind
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
