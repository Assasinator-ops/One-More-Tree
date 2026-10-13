# Files to Upload for Gutenberg Patterns

## Quick Upload List

Upload these 6 files from your theme to production:

```
wp-content/themes/one-more-tree/inc/block-patterns.php
wp-content/themes/one-more-tree/inc/shortcodes.php
wp-content/themes/one-more-tree/assets/css/block-patterns.css
wp-content/themes/one-more-tree/assets/css/block-styles.css
wp-content/themes/one-more-tree/theme.json
wp-content/themes/one-more-tree/functions.php
```

## Where They Are Located

**Development:**
`/home/nati/Documents/One More Tree WP/wp-content/themes/one-more-tree/`

**Production (Already Deployed):**
`/home/nati/Documents/One More Tree WP/prod/wp-content/themes/one-more-tree/`

## Upload Order

1. `theme.json` - Design tokens
2. `inc/block-patterns.php` - Pattern registration
3. `inc/shortcodes.php` - Dynamic content
4. `assets/css/block-patterns.css` - Custom CSS
5. `assets/css/block-styles.css` - Block styles
6. `functions.php` - Registration

## What Each File Does

| File | Purpose |
|------|---------|
| `block-patterns.php` | Registers 17 Gutenberg patterns |
| `shortcodes.php` | Provides 14 dynamic content shortcodes |
| `block-patterns.css` | Custom CSS for patterns (flat design) |
| `block-styles.css` | Block style variations (flat design) |
| `theme.json` | Design tokens (colors, typography, spacing) |
| `functions.php` | Enqueues CSS, registers styles, hybrid system |

## Verification

After uploading:
1. Go to WordPress admin
2. Edit any page
3. Check "Use Gutenberg Block Editor"
4. Click "+" → "Patterns" → "One More Tree Patterns"
5. Verify patterns appear and render correctly

## Status

✅ All files created
✅ All files deployed to prod/ folder
✅ Flat design applied
✅ Navigation ribbon added
✅ Mobile responsive
✅ Ready for production upload
