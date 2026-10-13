<?php
/**
 * One More Tree - Turnkey Full Page Block Patterns
 * 
 * Provides 1-click full page block templates that can be dropped onto any
 * Gutenberg canvas and continuously customized (drag, reorder, edit, restyle).
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

function one_more_tree_register_full_page_patterns() {
    if (!function_exists('register_block_pattern')) {
        return;
    }

    $theme_uri = get_template_directory_uri();

    // 1. Full Home Page Turnkey Pattern
    register_block_pattern(
        'one-more-tree/page-home-turnkey',
        [
            'title'       => __('Full Home Page Turnkey Template', 'one-more-tree'),
            'description' => __('Complete turnkey home page layout with hero, counters, mission values, projects showcase, donation tiers, and newsletter.', 'one-more-tree'),
            'categories'  => ['one-more-tree-pages', 'one-more-tree'],
            'content'     => '<!-- wp:pattern {"slug":"one-more-tree/hero-banner"} /-->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white border-b border-earth-200","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white border-b border-earth-200">
<!-- wp:pattern {"slug":"one-more-tree/counter-section"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 is-style-section-gradient","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 is-style-section-gradient">
<!-- wp:pattern {"slug":"one-more-tree/about-section-values"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/project-feature"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 bg-forest-50","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 bg-forest-50">
<!-- wp:pattern {"slug":"one-more-tree/donate-tiers"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/cta-donation"} /-->
</div>
<!-- /wp:group -->',
        ]
    );

    // 2. Full About Page Turnkey Pattern
    register_block_pattern(
        'one-more-tree/page-about-turnkey',
        [
            'title'       => __('Full About Page Turnkey Template', 'one-more-tree'),
            'description' => __('Complete turnkey about page layout with story hero, 4 core values, 3-chapter restoration story, and volunteer CTA.', 'one-more-tree'),
            'categories'  => ['one-more-tree-pages', 'one-more-tree'],
            'content'     => '<!-- wp:pattern {"slug":"one-more-tree/about-clean"} /-->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/story-3-chapters"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 is-style-section-gradient","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 is-style-section-gradient">
<!-- wp:pattern {"slug":"one-more-tree/about-section-values"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/get-involved-cards"} /-->
</div>
<!-- /wp:group -->',
        ]
    );

    // 3. Full Impact Report Turnkey Pattern
    register_block_pattern(
        'one-more-tree/page-impact-turnkey',
        [
            'title'       => __('Full Impact Report Turnkey Template', 'one-more-tree'),
            'description' => __('Complete turnkey impact and transparency page with KPI counters, progress charts, and community testimonials.', 'one-more-tree'),
            'categories'  => ['one-more-tree-pages', 'one-more-tree'],
            'content'     => '<!-- wp:group {"align":"full","className":"py-20 sm:py-32 bg-forest-900 text-white is-style-forest-gradient","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 bg-forest-900 text-white is-style-forest-gradient">
<!-- wp:heading {"textAlign":"center","level":1,"className":"is-style-hero-text text-white"} -->
<h1 class="wp-block-heading has-text-align-center is-style-hero-text text-white" style="color:#ffffff">Measurable Reforestation Impact</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","className":"is-style-lead-text text-white max-w-2xl mx-auto"} -->
<p class="has-text-align-center is-style-lead-text text-white max-w-2xl mx-auto" style="color:rgba(255,255,255,0.9)">Transparent tracking of planted saplings, soil biodiversity, and active community restoration across Spain.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/impact-stats"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-20 sm:py-32 bg-forest-50","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-20 sm:py-32 bg-forest-50">
<!-- wp:pattern {"slug":"one-more-tree/counter-section"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white">
<!-- wp:pattern {"slug":"one-more-tree/cta-donation"} /-->
</div>
<!-- /wp:group -->',
        ]
    );

    // 4. Full Donation Campaign Turnkey Pattern
    register_block_pattern(
        'one-more-tree/page-donate-turnkey',
        [
            'title'       => __('Full Donation Campaign Turnkey Template', 'one-more-tree'),
            'description' => __('Complete turnkey donation drive page with tier cards, tax deductibility details, and impact snapshot.', 'one-more-tree'),
            'categories'  => ['one-more-tree-pages', 'one-more-tree'],
            'content'     => '<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-forest-50","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-forest-50">
<!-- wp:group {"className":"is-style-badge mb-6 mx-auto","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-group is-style-badge mb-6 mx-auto">🌱 Tax-Deductible Reforestation</div>
<!-- /wp:group -->
<!-- wp:heading {"textAlign":"center","level":1,"className":"font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6"} -->
<h1 class="wp-block-heading has-text-align-center font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6">Plant Trees, Restore Spanish Ecosystems</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","className":"text-lg text-foreground/70 max-w-2xl mx-auto mb-12"} -->
<p class="has-text-align-center text-lg text-foreground/70 max-w-2xl mx-auto mb-12">Every donation directly funds native saplings, planting tools, and multi-year ecological monitoring.</p>
<!-- /wp:paragraph -->
<!-- wp:pattern {"slug":"one-more-tree/donate-tiers"} /-->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"full","className":"py-16 sm:py-24 bg-white border-t border-earth-200","layout":{"type":"constrained","contentSize":"840px"}} -->
<div class="wp-block-group alignfull py-16 sm:py-24 bg-white border-t border-earth-200">
<!-- wp:heading {"textAlign":"center","level":2,"className":"mb-6 font-serif text-3xl font-bold"} -->
<h2 class="wp-block-heading has-text-align-center mb-6 font-serif text-3xl font-bold">Frequently Asked Questions</h2>
<!-- /wp:heading -->
<!-- wp:pattern {"slug":"one-more-tree/faq-clean"} /-->
</div>
<!-- /wp:group -->',
        ]
    );
}
add_action('init', 'one_more_tree_register_full_page_patterns', 25);
