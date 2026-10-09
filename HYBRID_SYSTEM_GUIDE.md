# One More Tree - Hybrid Template System Guide

## Overview

Your WordPress theme now has a **hybrid template system** that allows you to choose between **PHP templates** (custom layouts) and **Gutenberg block editor** (visual editing) on a per-page basis. This gives you maximum flexibility while maintaining design consistency.

## How It Works

### Default Behavior: PHP Templates
By default, all pages use **custom PHP templates** with ACF/custom fields:
- `front-page.php` → Home page
- `page-about.php` → About page  
- `page-projects.php` → Projects page
- `page-impact.php` → Impact page
- `page-faq.php` → FAQ page
- `page-get-involved.php` → Get Involved page
- `page-volunteer.php` → Volunteer page
- `page-contact.php` → Contact page
- `page-donate.php` → Donate page

These templates pull data from the WordPress admin API and provide pixel-perfect layouts matching the Next.js design.

### Optional Behavior: Gutenberg Block Editor
You can enable **Gutenberg block editor** for any page by checking a single checkbox in the page editor. This allows you to:
- Use visual block editing
- Insert block patterns (Hero, About, Navigation, Footer, etc.)
- Use dynamic content shortcodes
- Apply block style variations

## How to Switch Between Systems

### Using PHP Templates (Default)
1. Go to any page in WordPress admin
2. Edit the page
3. Make sure "Use Gutenberg Block Editor" checkbox is **unchecked**
4. Content is managed via WordPress admin API/ACF fields
5. The page will use the corresponding PHP template

### Using Gutenberg Block Editor
1. Go to any page in WordPress admin
2. Edit the page
3. Check the "Use Gutenberg Block Editor" checkbox in the "Template System" meta box
4. Update the page
5. The page will now use Gutenberg block editor
6. You can insert block patterns from "One More Tree Patterns" category
7. Use shortcodes like `[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"]` for dynamic content

## Technical Implementation

### Key Files Modified
- `functions.php` - Added hybrid system logic
- `inc/block-patterns.php` - Re-enabled block pattern registration
- `inc/shortcodes.php` - Dynamic content shortcodes
- `theme.json` - Design tokens for Gutenberg
- `assets/css/block-patterns.css` - Custom CSS for patterns
- `assets/css/block-styles.css` - Block style variations

### New Features Added

#### 1. Meta Box for Template Selection
A new meta box appears in the page editor sidebar:
- **Location:** Page editor → Right sidebar → "Template System"
- **Option:** "Use Gutenberg Block Editor" checkbox
- **Default:** Unchecked (uses PHP template)

#### 2. Conditional Gutenberg Enable/Disable
```php
function one_more_tree_conditional_gutenberg($use_block_editor, $post) {
    $use_gutenberg = get_post_meta($post->ID, 'use_gutenberg', true);
    if ($use_gutenberg === '1' || $use_gutenberg === true) {
        return true; // Enable Gutenberg
    }
    return false; // Disable Gutenberg (use PHP template)
}
```

#### 3. Conditional Template Routing
```php
add_filter('template_include', function($template) {
    global $post;
    if ($post && get_post_meta($post->ID, 'use_gutenberg', true) === '1') {
        return $template; // Use WordPress default routing
    }
    // Force PHP templates for other pages
    $routes = [
        'projects' => 'page-projects.php',
        // ... other routes
    ];
    // ...
});
```

## Available Block Patterns

When using Gutenberg, you have access to these block patterns in the "One More Tree Patterns" category:

1. **Hero Banner** - Full-width hero with mission badge, headline, CTAs, stats
2. **About Section** - Value cards with icons, gradient background
3. **Navigation** - Fixed nav with logo, menu, CTA button
4. **Footer** - Six-column layout with social links, newsletter

## Available Shortcodes

Dynamic content shortcodes work in both systems:

- `[omt_text key="..." option="omt_admin_..." fallback="..."]` - Generic dynamic text
- `[omt_hero]` - Hero section data
- `[omt_counters]` - Counter section data
- `[omt_live_map]` - Interactive map
- `[omt_stats]` - Statistics cards
- `[omt_donation_tiers]` - Donation tiers
- `[omt_impact_kpis]` - Impact KPIs
- `[omt_about_values]` - About value cards
- `[omt_testimonials]` - Testimonials
- `[omt_faq]` - FAQ items
- `[omt_contact_form]` - Contact form
- `[omt_newsletter_form]` - Newsletter form
- `[omt_team]` - Team members
- `[omt_social_links]` - Social media links

## Block Style Variations

Predefined styles for core blocks:
- `glass-card` - Glassmorphism effect
- `forest-gradient` - Forest gradient background
- `stat-card` - Statistics card
- `value-card` - Value card
- `donation-tier` - Donation tier card
- `newsletter-card` - Newsletter card
- `counter-card` - Counter card
- `testimonial-card` - Testimonial card
- `forest-button` - Forest green button
- `glass-button` - Glassmorphism button
- `hero-text` - Hero typography
- And more...

## Use Cases

### When to Use PHP Templates (Default)
- Production pages with established layouts
- Pages requiring complex custom logic
- Pages with heavy JavaScript interactivity
- When you want complete control over markup
- When data structure is complex and requires custom processing

### When to Use Gutenberg
- Landing pages you want to customize visually
- Marketing pages that need frequent content changes
- Blog posts and simple content pages
- When you want to use block patterns
- When non-technical users need to edit content
- Rapid prototyping and testing

## Data Consistency

Both systems pull from the **same data source**:
- **PHP templates:** `get_option('omt_admin_...')`
- **Gutenberg patterns:** `[omt_text key="..." option="omt_admin_..."]`

This ensures content consistency regardless of which system you use.

## Migration Strategy

### Converting a Page from PHP to Gutenberg
1. Go to the page in WordPress admin
2. Note down the content structure (copy text, images, etc.)
3. Check "Use Gutenberg Block Editor"
4. Insert appropriate block patterns
5. Add shortcodes for dynamic content
6. Apply block style variations
7. Test the page appearance

### Converting a Page from Gutenberg to PHP
1. Go to the page in WordPress admin
2. Uncheck "Use Gutenberg Block Editor"
3. Configure content via WordPress admin API/ACF fields
4. The page will automatically use the PHP template

## Testing Checklist

### PHP Template Functionality
- [ ] All pages load correctly with PHP templates
- [ ] Dynamic content displays from admin API
- [ ] JavaScript interactions work properly
- [ ] Responsive design is maintained
- [ ] Performance is acceptable

### Gutenberg Functionality
- [ ] Block patterns appear in pattern library
- [ ] Block style variations are available
- [ ] Shortcodes render dynamic content correctly
- [ ] Meta box checkbox toggles editor correctly
- [ ] Template routing switches as expected

### Hybrid System
- [ ] Switching between systems works per page
- [ ] Data consistency between systems
- [ ] No conflicts when both systems are used
- [ ] User experience is intuitive

## Troubleshooting

### Page not switching to Gutenberg
- Ensure "Use Gutenberg Block Editor" checkbox is checked
- Clear page cache
- Check that block patterns are registered
- Verify `inc/block-patterns.php` is included

### Page not switching to PHP template
- Ensure "Use Gutenberg Block Editor" checkbox is unchecked
- Check that the corresponding PHP template exists
- Verify template routing logic in `functions.php`
- Clear page cache

### Shortcodes not rendering
- Verify `inc/shortcodes.php` is included in `functions.php`
- Check shortcode syntax for typos
- Ensure data exists in WordPress options
- Test shortcode with simple content first

### Block patterns not appearing
- Check that `inc/block-patterns.php` is included
- Verify pattern registration is not commented out
- Clear WordPress cache
- Check for PHP syntax errors

## Benefits of This Hybrid System

### Flexibility
- Choose the right tool for each page
- Mix PHP templates and Gutenberg as needed
- Easy to switch between systems

### Design Consistency
- Both systems use the same design tokens
- Shared color palette and typography
- Consistent spacing and sizing

### Data Management
- Single source of truth (WordPress admin API)
- Content edits work across both systems
- No data duplication or sync issues

### Performance
- PHP templates are lightweight and fast
- Gutenberg patterns are optimized
- No unnecessary overhead

### User Experience
- Technical users can use PHP templates
- Non-technical users can use Gutenberg
- Easy to understand and use

## Future Enhancements

Potential additions to the hybrid system:
- [ ] More block patterns for other page types
- [ ] Template parts for FSE integration
- [ ] Visual template builder
- [ ] A/B testing between systems
- [ ] Performance monitoring per system
- [ ] Migration tools between systems

## Support

For issues or questions:
1. Check this guide first
2. Review `BLOCK_PATTERNS.md` for pattern documentation
3. Check WordPress error logs
4. Test with both systems enabled
5. Verify data consistency

## Conclusion

This hybrid system gives you the best of both worlds:
- **PHP templates** for precise control and complex functionality
- **Gutenberg** for visual editing and rapid content changes
- **Seamless switching** between systems per page
- **Data consistency** across both approaches

Use whichever system makes sense for each page, and switch as needed without losing data or design consistency.
