# One More Tree - Gutenberg Block Patterns Documentation

## Overview

This theme now includes a comprehensive Gutenberg block pattern system that provides visual templates matching the Next.js design while maintaining the existing ACF/PHP templates as the primary production system. This dual-system approach ensures visual fidelity and content flexibility.

## Architecture

### Dual System Approach

**Primary System (Production):**
- ACF/PHP templates (`front-page.php`, `page-about.php`, etc.)
- Dynamic data from WordPress admin API
- Custom JavaScript for interactivity
- Full control over markup and styling

**Secondary System (Backup/Alternative):**
- Gutenberg block patterns
- Dynamic content via shortcodes
- Block style variations
- Visual editor integration

Both systems pull from the same data source (WordPress admin API) ensuring content consistency.

## File Structure

```
wp-content/themes/one-more-tree/
├── theme.json                          # Design tokens and theme configuration
├── assets/css/
│   ├── block-patterns.css              # Custom CSS for advanced effects
│   └── block-styles.css                # Block style variations
├── inc/
│   ├── block-patterns.php              # Pattern registration
│   ├── shortcodes.php                  # Dynamic content shortcodes
│   └── functions.php                  # Style registration and includes
└── parts/                              # Template parts (FSE)
    ├── header.html
    ├── footer.html
    └── hero.html
```

## Design Tokens (theme.json)

### Enhanced Spacing Scale
- `xs`: `clamp(0.5rem, 0.4rem + 0.5vw, 0.75rem)`
- `sm`: `clamp(0.75rem, 0.6rem + 0.75vw, 1rem)`
- `md`: `clamp(1rem, 0.8rem + 1vw, 1.5rem)`
- `lg`: `clamp(1.5rem, 1.2rem + 1.5vw, 2rem)`
- `xl`: `clamp(2rem, 1.6rem + 2vw, 3rem)`
- `2xl`: `clamp(3rem, 2.4rem + 3vw, 4rem)`
- `3xl`: `clamp(4rem, 3.2rem + 4vw, 6rem)`

### Fluid Typography
All font sizes use `clamp()` for responsive scaling:
- `xs`: `clamp(0.75rem, 0.7rem + 0.25vw, 0.875rem)`
- `sm`: `clamp(0.875rem, 0.8rem + 0.375vw, 1rem)`
- `normal`: `clamp(1rem, 0.9rem + 0.5vw, 1.125rem)`
- `medium`: `clamp(1.125rem, 1rem + 0.625vw, 1.25rem)`
- `lg`: `clamp(1.25rem, 1.1rem + 0.75vw, 1.5rem)`
- `xl`: `clamp(1.5rem, 1.3rem + 1vw, 1.875rem)`
- `2xl`: `clamp(1.875rem, 1.6rem + 1.375vw, 2.25rem)`
- `3xl`: `clamp(2.25rem, 1.9rem + 1.75vw, 3rem)`
- `4xl`: `clamp(3rem, 2.5rem + 2.5vw, 4.5rem)`
- `5xl`: `clamp(3.75rem, 3rem + 3.75vw, 6rem)`

### Color Palette
- Forest tones: `forest-50` through `forest-950`
- Earth tones: `earth-100`, `earth-200`, `earth-300`, `earth-800`
- UI colors: `background`, `foreground`, `white`, `black`
- Special: `emerald-400`, `emerald-500`, `slate-200`

### Custom Gradients
- `forest-gradient`: Linear forest tones
- `hero-soft-gradient`: Light hero background
- `dark-forest-gradient`: Dark forest background
- `glass-gradient`: Glassmorphism effect
- `section-gradient`: Section backgrounds

### Shadow Presets
- `soft`: Subtle elevation
- `medium`: Standard elevation
- `elevated`: High elevation
- `glass`: Glassmorphism shadow
- `hero`: Hero section shadow

## Available Block Patterns

### 1. Hero Banner (`one-more-tree/hero-banner`)
**Category:** One More Tree, Header

**Features:**
- Full-width background image with gradient overlays
- Mission badge with glassmorphism
- Responsive typography (serif headline)
- CTA buttons with hover effects
- Three statistics cards
- Dynamic content via shortcodes

**Usage:**
```
[omt_text key="hero.mission_badge" option="omt_admin_hero" fallback="Planting trees, restoring forests."]
[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"]
[omt_text key="hero.headline_emphasis" option="omt_admin_hero" fallback="Tree"]
```

### 2. About Section (`one-more-tree/about-section`)
**Category:** One More Tree, About

**Features:**
- Gradient background with pattern overlay
- Four value cards with icons
- Deliverables statistics
- Glassmorphism effects
- Responsive grid layout

**Usage:**
```
[omt_text key="about.section_title" option="omt_admin_about_hero" fallback="Growing forests, building hope in Spain"]
[omt_text key="about.value_1_title" option="omt_admin_about_values" fallback="Start with seeds"]
```

### 3. Navigation (`one-more-tree/navigation`)
**Category:** One More Tree, Header

**Features:**
- Fixed positioning with disclaimer offset
- Responsive menu
- Logo and brand text
- Navigation links
- CTA button
- Scroll behavior styling

**Usage:**
Template part - use in header template

### 4. Footer (`one-more-tree/footer`)
**Category:** One More Tree, Footer

**Features:**
- Six-column responsive grid
- Brand information
- Navigation links (Organization, Programs, Get Involved, Resources, Contact)
- Social media integration
- Newsletter signup
- Legal links

**Usage:**
```
[omt_social_links style="icons"]
[omt_newsletter_form]
```

## Dynamic Content Shortcodes

### Shortcode Overview
Shortcodes provide dynamic data integration for block patterns, allowing static block markup to display content from the WordPress admin API.

### Available Shortcodes

#### `[omt_hero]`
Displays hero section data with statistics.
```php
[omt_hero show_stats="true" show_cta="true"]
```

#### `[omt_counters]`
Displays counter section data.
```php
[omt_counters]
```

#### `[omt_live_map]`
Displays interactive map with project locations.
```php
[omt_live_map show_all="true" max_locations="10"]
```

#### `[omt_stats]`
Displays statistics cards.
```php
[omt_stats type="hero" layout="3-column"]
```

#### `[omt_donation_tiers]`
Displays donation tier cards.
```php
[omt_donation_tiers max_tiers="3" default_amount="25"]
```

#### `[omt_impact_kpis]`
Displays impact KPI cards.
```php
[omt_impact_kpis]
```

#### `[omt_about_values]`
Displays about section value cards.
```php
[omt_about_values]
```

#### `[omt_testimonials]`
Displays testimonial cards.
```php
[omt_testimonials max_testimonials="3" layout="grid"]
```

#### `[omt_faq]`
Displays FAQ items.
```php
[omt_faq category="" max_items="10"]
```

#### `[omt_contact_form]`
Displays contact form with AJAX integration.
```php
[omt_contact_form show_interest="true" recaptcha="false"]
```

#### `[omt_newsletter_form]`
Displays newsletter signup form.
```php
[omt_newsletter_form show_title="true" compact="false"]
```

#### `[omt_team]`
Displays team member cards.
```php
[omt_team max_members="4" layout="grid"]
```

#### `[omt_social_links]`
Displays social media links.
```php
[omt_social_links style="icons" show_labels="false"]
```

#### `[omt_text]`
Generic shortcode for dynamic text from admin data.
```php
[omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"]
```

**Parameters:**
- `key`: Data key (supports dot notation for nested values)
- `option`: WordPress option name (defaults to `omt_admin_{key}`)
- `fallback`: Fallback text if data not found

## Block Style Variations

### Group Block Styles
- `glass-card`: Glassmorphism card effect
- `forest-gradient`: Forest gradient background
- `stat-card`: Statistics card style
- `value-card`: Value card style
- `donation-tier`: Donation tier card style
- `newsletter-card`: Newsletter card style
- `counter-card`: Counter card style
- `testimonial-card`: Testimonial card style
- `section-gradient`: Section gradient background

### Heading Styles
- `hero-text`: Hero typography style

### Button Styles
- `forest-button`: Forest green button
- `glass-button`: Glassmorphism button

### Column Styles
- `value-grid`: Value grid layout

### Paragraph Styles
- `lead-text`: Lead paragraph text
- `small-text`: Small paragraph text

### Separator Styles
- `forest-separator`: Forest colored separator
- `gradient-separator`: Gradient separator

### Quote Styles
- `testimonial-quote`: Testimonial quote style

### Image Styles
- `rounded-image`: Rounded image
- `shadow-image`: Image with shadow

## Custom CSS Features

### Glassmorphism Effects
```css
.glass-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
}
```

### SVG Pattern Backgrounds
- `leaf-pattern`: Leaf pattern overlay
- `page-header-texture`: Header texture pattern
- `gradient-bg`: Gradient background

### Advanced Hover States
- `animated-underline`: Animated underline on hover
- `card-hover-lift`: Card lift effect on hover
- `icon-zoom`: Icon zoom on hover

### Scroll Animations
- `animate-fade-in-up`: Fade in from bottom
- `animate-fade-in-left`: Fade in from left
- `animate-fade-in-right`: Fade in from right

### Hero Section Styles
- `omt-hero-full`: Full-width hero container
- `hero-mission-badge`: Glassmorphism badge
- `hero-stat-card`: Statistics card with hover effects

### Navigation Styles
- `omt-navigation`: Fixed navigation with scroll behavior
- `scrolled`: Scroll state styling

### FAQ Accordion Styles
- `faq-item`: FAQ item container
- `faq-answer`: Collapsible answer section
- Automatic height transitions

## Usage Instructions

### Using Block Patterns in Editor

1. **Access Patterns:**
   - Open any page/post in Gutenberg editor
   - Click the "+" button to add a block
   - Navigate to "Patterns" → "One More Tree Patterns"

2. **Insert Pattern:**
   - Click on desired pattern to insert
   - Pattern will be inserted with default content

3. **Customize Content:**
   - Edit text content directly in blocks
   - Use shortcodes for dynamic data
   - Apply block style variations via block inspector

4. **Modify Styling:**
   - Change colors using theme color palette
   - Adjust spacing using spacing presets
   - Apply block style variations for pre-defined styles

### Using Shortcodes

1. **Basic Usage:**
   ```php
   [omt_text key="hero.headline_top" option="omt_admin_hero" fallback="One More"]
   ```

2. **With Parameters:**
   ```php
   [omt_stats type="hero" layout="3-column"]
   ```

3. **Nested in Blocks:**
   - Add a "Shortcode" block in Gutenberg
   - Paste shortcode content
   - Shortcode will render dynamic content

### Using Block Style Variations

1. **Apply Style:**
   - Select a block in the editor
   - Open block inspector (right sidebar)
   - Navigate to "Styles" section
   - Select desired style variation

2. **Custom Styles:**
   - Style variations are defined in `assets/css/block-styles.css`
   - Add custom styles by editing this file
   - Register new styles in `functions.php`

## Integration with Existing PHP Templates

### Data Consistency
Both PHP templates and block patterns use the same data source:
- PHP templates: `get_option('omt_admin_...')`
- Block patterns: `[omt_text key="..." option="omt_admin_..."]`

### Switching Between Systems
- **To use PHP templates:** Assign page templates in WordPress admin
- **To use block patterns:** Insert patterns in Gutenberg editor

### Mixed Approach
You can use PHP templates for some pages and block patterns for others, as both systems coexist.

## Customization Guide

### Adding New Block Patterns

1. **Register Pattern in `inc/block-patterns.php`:**
   ```php
   register_block_pattern(
       'one-more-tree/pattern-name',
       [
           'title'       => __('Pattern Title', 'one-more-tree'),
           'description' => __('Pattern description', 'one-more-tree'),
           'categories'  => ['one-more-tree', 'category'],
           'content'     => '<!-- wp:... -->',
       ]
   );
   ```

2. **Use Core Blocks:**
   - Start with `<!-- wp:group -->` for containers
   - Use `<!-- wp:columns -->` for layouts
   - Apply custom classes for styling

3. **Integrate Shortcodes:**
   - Use `[omt_text]` for dynamic content
   - Use specific shortcodes for complex data

### Adding New Shortcodes

1. **Add to `inc/shortcodes.php`:**
   ```php
   function omt_custom_shortcode($atts) {
       $atts = shortcode_atts([
           'param' => 'default',
       ], $atts);
       
       // Fetch data from admin API
       $data = get_option('omt_admin_custom_data', []);
       
       // Return rendered content
       return render_custom_content($data, $atts);
   }
   add_shortcode('omt_custom', 'omt_custom_shortcode');
   ```

2. **Use in Patterns:**
   ```php
   [omt_custom param="value"]
   ```

### Adding New Block Styles

1. **Add CSS to `assets/css/block-styles.css`:**
   ```css
   .wp-block-group.is-style-custom-style {
       /* Custom styling */
   }
   ```

2. **Register in `functions.php`:**
   ```php
   register_block_style('core/group', [
       'name'  => 'custom-style',
       'label' => __('Custom Style', 'one-more-tree'),
   ]);
   ```

## Troubleshooting

### Patterns Not Appearing
- Check that `inc/block-patterns.php` is included in `functions.php`
- Verify block pattern registration is not commented out
- Clear WordPress cache

### Shortcodes Not Rendering
- Verify `inc/shortcodes.php` is included in `functions.php`
- Check shortcode syntax for typos
- Ensure data exists in WordPress options

### Block Styles Not Available
- Verify `assets/css/block-styles.css` is enqueued
- Check block style registration in `functions.php`
- Clear browser cache

### Styling Issues
- Verify `assets/css/block-patterns.css` is enqueued
- Check theme.json design tokens
- Ensure custom CSS classes are applied correctly

## Performance Considerations

### CSS Optimization
- Custom CSS is minified in production
- Use native block attributes where possible
- Limit custom CSS to essential effects only

### Shortcode Performance
- Shortcodes use WordPress options (cached)
- Data is fetched once per page load
- Consider transient caching for complex data

### Block Pattern Performance
- Block patterns are lightweight HTML
- No external dependencies
- Native WordPress caching applies

## Accessibility

### Semantic HTML
- Use appropriate heading levels
- Maintain proper heading hierarchy
- Use semantic elements when possible

### ARIA Labels
- Add aria-labels to interactive elements
- Ensure form labels are present
- Provide alt text for images

### Keyboard Navigation
- Ensure all interactive elements are keyboard accessible
- Maintain visible focus states
- Test keyboard navigation

### Color Contrast
- Theme colors meet WCAG AA standards
- Verify custom colors for contrast
- Test with screen readers

## Browser Support

### Modern Browsers
- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (with -webkit- prefixes)

### Legacy Support
- Glassmorphism effects fallback gracefully
- Fluid typography uses `clamp()` (modern browsers)
- Grid layouts have flexbox fallbacks

### Mobile Support
- Responsive breakpoints tested
- Touch interactions optimized
- Mobile menu functionality

## Future Development

### Planned Additions
- [ ] Additional page patterns (Projects, Impact, FAQ, Contact, Get Involved, Volunteer, Donate)
- [ ] Template parts for FSE integration
- [ ] Advanced animation patterns
- [ ] E-commerce integration patterns
- [ ] Blog post patterns

### Enhancement Opportunities
- [ ] Block pattern library UI
- [ ] Pattern preview system
- [ ] Dynamic pattern builder
- [ ] Performance monitoring
- [ ] A/B testing framework

## Support and Maintenance

### Documentation Updates
- Keep this file updated with new patterns
- Document new shortcodes as they're added
- Update usage examples regularly

### Code Maintenance
- Follow WordPress coding standards
- Maintain consistent formatting
- Update dependencies regularly

### Testing
- Test new patterns on multiple browsers
- Verify responsive behavior
- Check accessibility compliance
- Performance test before deployment

## License and Attribution

This block pattern system is part of the One More Tree WordPress theme. All patterns and styles are designed to match the original Next.js implementation while providing WordPress-native editing capabilities.

## Credits

- **Theme:** One More Tree
- **Original Design:** Next.js implementation
- **WordPress Adaptation:** Gutenberg block patterns
- **Design System:** Tailwind CSS → WordPress design tokens
