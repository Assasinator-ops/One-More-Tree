# One More Tree — Full Block Theme (FSE) Upgrade Plan

> **Objective**: Convert `one-more-tree` from a broken hybrid to a **proper Full Site Editing (FSE) block theme** that behaves identically to Twenty Twenty-Three through Twenty Twenty-Five. This enables: live customization via the Site Editor, always-on Gutenberg for every page/post, no toggles, full drag-and-drop of all existing patterns, and all existing PHP pages preserved as optional "legacy" routes.

---

## Root Cause Analysis

### Why `customize.php` is blank

The **WordPress Customizer** (`customize.php`) is the _old_ customization system for **classic themes**. Since WordPress 5.9, modern themes use the **Site Editor** (`site-editor.php`) instead. The current theme has `theme.json` (a block theme file) but is missing the `templates/index.html` file that WordPress checks to decide whether to enable the Site Editor. Without it, neither system works:

- The Customizer has no controls registered (nothing to show)
- The Site Editor menu item does not appear

### Why blocks appear blank / inserter seems empty

When editing a page that has a PHP template assigned (e.g. `page-about.php`), WordPress _does_ open Gutenberg — but the PHP template completely **ignores** the editor's content because it never calls `the_content()`. It renders hardcoded ACF/PHP HTML. So blocks can be dragged in, but they never appear on the page frontend. This makes it look broken.

### Why there was a "tick to enable Gutenberg" toggle

The previous agent added a `use_block_editor_for_post` filter that disabled Gutenberg by default and required a `use_gutenberg = 1` meta field to re-enable it. **This filter was already removed** in the last session — Gutenberg is now always-on. However pages still do not use blocks due to the PHP template override issue.

### Gap vs Twenty Twenty-Three through Twenty Twenty-Five

| Feature | TT3–TT5 | One More Tree (current) |
|---|---|---|
| `templates/index.html` exists | YES | NO |
| Site Editor (`site-editor.php`) | Enabled | Disabled — wrong menu item |
| Customizer (`customize.php`) | Redirects to Site Editor | Blank |
| Header / Footer editable live | Block template parts | Hardcoded PHP |
| Drag-and-drop for ALL pages | `wp:post-content` block | PHP ignores editor content |
| Patterns in inserter | Auto-discovered from `patterns/` | Already registered via PHP |
| Style variations | `styles/*.json` | Already created (3 themes) |
| Global typography / color controls | Via Site Editor Global Styles | Not accessible |
| `is_block_theme()` returns true | YES | NO (missing `templates/index.html`) |

---

## Architecture: What Needs to Be Built

### The Single Trigger

WordPress detects a block theme by checking for **`templates/index.html`**. Creating this one file (plus a `parts/` directory) unlocks everything:

- The **Site Editor** menu item under Appearance
- **Global Styles** panel (typography, colors, spacing sliders)
- **Template editor** (edit header, footer, page layouts live)
- **Full drag-and-drop** for all pages and posts

### Preserving PHP Pages (Hybrid Approach)

The existing PHP templates (`page-about.php`, `front-page.php`, etc.) power the live frontend. They must be **preserved as-is**. The block editor works for _new pages_ and any page using a block canvas template. The PHP pages remain as their own "locked" template option.

The approach used by professional premium themes:

1. Create block `templates/` for generic page editing (the block canvas)
2. Keep all PHP templates for the existing hardcoded pages
3. The Site Editor provides access to header, footer, and global styles
4. New pages default to the block editor with patterns

---

## Implementation Steps

### STEP 1 — Create `templates/` directory with block HTML templates

**These `.html` files are what the Site Editor edits.** They reference `parts/` for header and footer. Create the following files:

#### `wp-content/themes/one-more-tree/templates/index.html`

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","align":"full","layout":{"type":"constrained","contentSize":"1280px"}} -->
<main class="wp-block-group alignfull">
  <!-- wp:post-content {"align":"full","layout":{"type":"constrained","contentSize":"1280px"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

#### `wp-content/themes/one-more-tree/templates/page.html`

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","align":"full","layout":{"type":"default"}} -->
<main class="wp-block-group alignfull">
  <!-- wp:post-content {"align":"full","layout":{"type":"constrained","contentSize":"1280px"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

#### `wp-content/themes/one-more-tree/templates/page-no-title.html`

Used for full-canvas landing pages where the page title should NOT be shown at the top (hero patterns include their own heading).

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","align":"full","layout":{"type":"default"}} -->
<main class="wp-block-group alignfull">
  <!-- wp:post-content {"align":"full","layout":{"type":"default"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

#### `wp-content/themes/one-more-tree/templates/single.html`

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained","contentSize":"840px"}} -->
<main class="wp-block-group">
  <!-- wp:post-title {"level":1} /-->
  <!-- wp:post-featured-image /-->
  <!-- wp:post-content {"layout":{"type":"constrained"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

#### `wp-content/themes/one-more-tree/templates/home.html`

Blog post list (used when a page is set as "Posts page" in Reading settings).

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained","contentSize":"1280px"}} -->
<main class="wp-block-group">
  <!-- wp:query {"query":{"perPage":10,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
  <div class="wp-block-query">
    <!-- wp:post-template -->
    <!-- wp:post-title {"isLink":true} /-->
    <!-- wp:post-excerpt /-->
    <!-- /wp:post-template -->
    <!-- wp:query-pagination -->
    <!-- wp:query-pagination-previous /-->
    <!-- wp:query-pagination-next /-->
    <!-- /wp:query-pagination -->
  </div>
  <!-- /wp:query -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

#### `wp-content/themes/one-more-tree/templates/404.html`

```html
<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
  <!-- wp:heading {"level":1,"textAlign":"center"} -->
  <h1 class="wp-block-heading has-text-align-center">Page Not Found</h1>
  <!-- /wp:heading -->
  <!-- wp:paragraph {"align":"center"} -->
  <p class="has-text-align-center">The page you are looking for does not exist. <a href="/">Return home</a>.</p>
  <!-- /wp:paragraph -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->
```

---

### STEP 2 — Create `parts/` directory with block template parts

These are the **live-editable header and footer** in the Site Editor. They must be converted from PHP `header.php` and `footer.php` to pure block HTML markup. This is the most work-intensive step but it unlocks full live header/footer editing.

> **CRITICAL**: The implementation agent MUST read the actual `header.php` and `footer.php` files in full before writing the block versions. The block parts must visually match the existing design: same logo, same navigation links, same colors, same layout. The scaffolds below are structural guides only.

#### `wp-content/themes/one-more-tree/parts/header.html`

The header contains: logo image, primary nav links (Home, About, Projects, Impact, Get Involved), and a "Donate" CTA button.

```html
<!-- wp:group {"align":"full","className":"site-header omt-header","style":{"position":{"type":"sticky","top":"0px"},"spacing":{"padding":{"top":"1rem","bottom":"1rem"}},"zIndex":50},"backgroundColor":"white","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<div class="wp-block-group alignfull site-header omt-header has-white-background-color has-background" style="position:sticky;top:0px;padding-top:1rem;padding-bottom:1rem;z-index:50">

  <!-- wp:site-logo {"width":120,"shouldSyncIcon":false} /-->

  <!-- wp:navigation {"ref":null,"overlayMenu":"mobile","icon":"menu","backgroundColor":"white","textColor":"foreground","layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"2rem"}}} /-->

</div>
<!-- /wp:group -->
```

> **Agent instruction**: After creating the minimal scaffold above, go to `wp-admin/site-editor.php` → Template Parts → Header and use the editor to build out the full nav with links, the donate button, and correct colors. Save it. The resulting block markup will be written back to `parts/header.html` by the Site Editor automatically.

#### `wp-content/themes/one-more-tree/parts/footer.html`

The footer contains: logo, tagline, nav column links, social icons, legal text, and copyright.

```html
<!-- wp:group {"align":"full","className":"site-footer","style":{"spacing":{"padding":{"top":"4rem","bottom":"2rem"}}},"backgroundColor":"foreground","textColor":"white","layout":{"type":"constrained","contentSize":"1280px"}} -->
<div class="wp-block-group alignfull site-footer has-foreground-background-color has-white-color has-background has-text-color" style="padding-top:4rem;padding-bottom:2rem">

  <!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":"3rem"}}} -->
  <div class="wp-block-columns alignwide">

    <!-- wp:column {"width":"40%"} -->
    <div class="wp-block-column" style="flex-basis:40%">
      <!-- wp:site-logo {"width":120} /-->
      <!-- wp:paragraph {"textColor":"white","style":{"spacing":{"margin":{"top":"1rem"}}}} -->
      <p class="has-white-color has-text-color" style="margin-top:1rem">Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"30%"} -->
    <div class="wp-block-column" style="flex-basis:30%">
      <!-- wp:heading {"level":4,"textColor":"forest-400","fontSize":"sm"} -->
      <h4 class="wp-block-heading has-forest-400-color has-text-color has-sm-font-size">Quick Links</h4>
      <!-- /wp:heading -->
      <!-- wp:navigation {"ref":null,"textColor":"white","overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /-->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"30%"} -->
    <div class="wp-block-column" style="flex-basis:30%">
      <!-- wp:heading {"level":4,"textColor":"forest-400","fontSize":"sm"} -->
      <h4 class="wp-block-heading has-forest-400-color has-text-color has-sm-font-size">Get Involved</h4>
      <!-- /wp:heading -->
      <!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical"}} -->
      <div class="wp-block-buttons">
        <!-- wp:button {"backgroundColor":"forest-600","textColor":"white","className":"is-style-forest-button"} -->
        <div class="wp-block-button is-style-forest-button"><a class="wp-block-button__link has-forest-600-background-color has-white-color has-background has-text-color wp-element-button" href="/donate">Donate Now</a></div>
        <!-- /wp:button -->
        <!-- wp:button {"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/volunteer">Volunteer</a></div>
        <!-- /wp:button -->
      </div>
      <!-- /wp:buttons -->
    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

  <!-- wp:separator {"className":"is-style-forest-separator","style":{"spacing":{"margin":{"top":"2rem","bottom":"1.5rem"}}}} -->
  <hr class="wp-block-separator is-style-forest-separator" style="margin-top:2rem;margin-bottom:1.5rem"/>
  <!-- /wp:separator -->

  <!-- wp:paragraph {"align":"center","textColor":"white","fontSize":"xs","style":{"color":{"text":"rgba(255,255,255,0.5)"}}} -->
  <p class="has-text-align-center has-white-color has-text-color has-xs-font-size" style="color:rgba(255,255,255,0.5)">&#169; 2025 One More Tree. All rights reserved. Registered charity. Tax deductible donations.</p>
  <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
```

---

### STEP 3 — Update `theme.json`

Add `templateParts` declarations and update `customTemplates` to only include real block HTML templates. Upgrade to version 3.

**Specific changes to make:**

1. Change `"version": 2` to `"version": 3`
2. Add `"templateParts"` array (before `"settings"`)
3. Replace `"customTemplates"` array to only include `page-no-title` (the PHP-based ones get removed since they are PHP files, not block HTML)

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "templateParts": [
    {
      "name": "header",
      "title": "Header",
      "area": "header"
    },
    {
      "name": "footer",
      "title": "Footer",
      "area": "footer"
    }
  ],
  "customTemplates": [
    {
      "name": "page-no-title",
      "title": "Page (No Title)",
      "postTypes": ["page", "post", "project"]
    }
  ],
  "settings": {
    ... ALL EXISTING SETTINGS PRESERVED EXACTLY AS-IS ...
  },
  "styles": {
    ... ALL EXISTING STYLES PRESERVED EXACTLY AS-IS ...
  }
}
```

> **WARNING**: Do NOT change any values inside `settings` or `styles`. Only add the `templateParts` block and replace `customTemplates`. The color palette, typography, spacing, shadow presets must all remain identical.

---

### STEP 4 — Update `functions.php`

**Changes needed:**

#### 4a. Add FSE theme support declarations

Inside `one_more_tree_setup()`, add these two lines after the existing `add_theme_support` calls:

```php
// FSE block theme support
add_theme_support( 'block-templates' );
add_theme_support( 'block-template-parts' );
```

#### 4b. Update `one_more_tree_page_templates()` 

Remove the block canvas templates from the custom templates filter (they are now real HTML templates auto-discovered by WordPress). Keep the PHP page templates:

```php
function one_more_tree_page_templates($templates) {
    // Filter out backup folder entries
    foreach (array_keys((array)$templates) as $file) {
        if (strpos($file, '_block_editing_backup') !== false) {
            unset($templates[$file]);
        }
    }
    $custom = [
        // PHP page templates (kept as optional assignments)
        'front-page.php'        => __('Home Page (PHP Template)', 'one-more-tree'),
        'page-about.php'        => __('About Page (PHP Template)', 'one-more-tree'),
        'page-projects.php'     => __('Projects Page (PHP Template)', 'one-more-tree'),
        'page-impact.php'       => __('Impact Page (PHP Template)', 'one-more-tree'),
        'page-faq.php'          => __('FAQ Page (PHP Template)', 'one-more-tree'),
        'page-get-involved.php' => __('Get Involved Page (PHP Template)', 'one-more-tree'),
        'page-volunteer.php'    => __('Volunteer Page (PHP Template)', 'one-more-tree'),
        'page-contact.php'      => __('Contact Page (PHP Template)', 'one-more-tree'),
        'page-donate.php'       => __('Donate Page (PHP Template)', 'one-more-tree'),
    ];
    return array_merge((array)$templates, $custom);
}
add_filter('theme_page_templates', 'one_more_tree_page_templates');
```

#### 4c. Remove the `navigation-pattern.js` admin gate

Change this:
```php
if (!is_admin()) {
    wp_enqueue_script(
        'one-more-tree-navigation-pattern',
        get_template_directory_uri() . '/assets/js/navigation-pattern.js',
        [],
        _S_VERSION,
        true
    );
}
```

To this (simply remove the `if (!is_admin())` wrapper, or delete entirely if it was only a PHP nav patch):
```php
// navigation-pattern.js is no longer needed — navigation is handled
// by the wp:navigation block in parts/header.html
```

#### 4d. Verify no `use_block_editor_for_post` filter remains

Run a grep to confirm it is gone:
```bash
grep -n "use_block_editor_for_post" wp-content/themes/one-more-tree/functions.php
```
If found, remove it entirely.

---

### STEP 5 — Update `style.css`

Replace the header comment block to include FSE tags and bump the version:

```css
/*
Theme Name: One More Tree
Theme URI: https://onemortree.org
Author: One More Tree Team
Author URI: https://onemortree.org
Description: A reforestation nonprofit theme with full site editing, drag-and-drop block patterns, and the One More Tree design system. Plant trees. Restore forests.
Version: 2.0.0
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: one-more-tree
Tags: full-site-editing, block-patterns, custom-colors, custom-logo, editor-style, featured-images, rtl-language-support, translation-ready, wide-blocks, block-styles, style-variations, accessibility-ready, nonprofit, environment
*/
```

---

### STEP 6 — Clear WordPress caches

After creating the `templates/` folder, WordPress must discover the new theme structure. Run:

```bash
docker exec one-more-tree-wp php -r '
require_once "wp-load.php";
wp_cache_flush();
delete_option("rewrite_rules");
flush_rewrite_rules(true);
// Force theme cache refresh
delete_transient("theme_roots");
delete_transient("_transient_doing_cron");
echo "All caches cleared.\n";
echo "is_block_theme: " . (wp_is_block_theme() ? "YES - FSE active" : "NO - something is wrong") . "\n";
'
```

The output must show `is_block_theme: YES`. If it still shows `NO`, double-check that `templates/index.html` exists at the correct path inside the theme directory.

---

### STEP 7 — Sync to prod

```bash
rsync -av --delete \
  --exclude="node_modules" \
  --exclude=".git" \
  --exclude="package-lock.json" \
  --exclude="tailwind.config.mjs" \
  --exclude="postcss.config.mjs" \
  --exclude="src/" \
  wp-content/themes/one-more-tree/ \
  prod/wp-content/themes/one-more-tree/
```

---

## Final File Structure (After Implementation)

```
wp-content/themes/one-more-tree/
├── templates/                    <- NEW — triggers FSE, enables Site Editor
│   ├── index.html                <- NEW — fallback template
│   ├── page.html                 <- NEW — standard pages (block canvas)
│   ├── page-no-title.html        <- NEW — full-canvas (no title shown)
│   ├── single.html               <- NEW — blog post
│   ├── home.html                 <- NEW — blog list
│   └── 404.html                  <- NEW — error page
├── parts/                        <- NEW — Site Editor editable parts
│   ├── header.html               <- NEW — block header
│   └── footer.html               <- NEW — block footer
├── styles/                       <- EXISTING — keep all 3
│   ├── dark-canopy.json
│   ├── earth-warm.json
│   └── emerald-forest.json
├── assets/                       <- EXISTING — keep all
├── inc/                          <- EXISTING — keep all
│   ├── block-patterns.php        <- keep (41 registered patterns)
│   ├── full-page-patterns.php    <- keep (4 turnkey layouts)
│   └── ...
├── theme.json                    <- UPDATE (version 3, add templateParts)
├── functions.php                 <- UPDATE (add block-templates support)
├── style.css                     <- UPDATE (add FSE tags, version 2.0.0)
├── page-about.php                <- KEEP (PHP frontend, optional template)
├── page-projects.php             <- KEEP
├── page-impact.php               <- KEEP
├── page-faq.php                  <- KEEP
├── page-get-involved.php         <- KEEP
├── page-volunteer.php            <- KEEP
├── page-contact.php              <- KEEP
├── page-donate.php               <- KEEP
├── front-page.php                <- KEEP
├── header.php                    <- KEEP (still used by PHP templates)
├── footer.php                    <- KEEP (still used by PHP templates)
└── ...                           <- KEEP everything else
```

---

## Critical Warnings

1. **DO NOT delete any PHP template files.** They power the live frontend. Only ADD the new block template files — never replace or remove existing PHP files.

2. **The `template_include` filter in `functions.php` must stay.** It routes `/about`, `/projects`, etc. to their PHP templates on the frontend. The block templates only activate for pages with no PHP template assigned.

3. **`parts/header.html` and `parts/footer.html` must be self-contained pure block markup.** They cannot use PHP `require` or `get_header()` calls. Read `header.php` and `footer.php` carefully and replicate all nav links, logo path, social links, and layout in block format.

4. **When upgrading to `"version": 3` in `theme.json`**, keep ALL content of `settings` and `styles` unchanged. Version 3 is mostly backward-compatible for existing settings. Do not remove any palette, typography, spacing, or shadow definitions.

5. **The PHP `header.php` and `footer.php` files remain in use** — they are still `get_header()`/`get_footer()` called by every PHP page template (`page-about.php`, `front-page.php`, etc.). The new block `parts/header.html` and `parts/footer.html` are ONLY used by the new `templates/*.html` block templates.

---

## Verification Checklist

After implementation, verify in browser:

- [ ] `wp-admin/site-editor.php` opens and shows full Site Editor UI
- [ ] Site Editor → Templates shows: `index`, `page`, `page-no-title`, `single`, `home`, `404`
- [ ] Site Editor → Template Parts shows: `header` and `footer` as editable
- [ ] Site Editor → Styles panel shows global color palette and typography controls
- [ ] Site Editor → Styles → Browse styles shows Dark Canopy, Earth Warm, Emerald Forest
- [ ] Editing any page in wp-admin opens Gutenberg immediately with no toggle
- [ ] Gutenberg `+` inserter shows **One More Tree** pattern categories
- [ ] Dragging a pattern into the canvas renders correctly
- [ ] A NEW page (using `templates/page.html`) renders block content on the frontend
- [ ] Existing PHP pages (`/about`, `/projects`, `/impact`, etc.) still render correctly
- [ ] `customize.php` shows a Site Editor redirect message or redirects automatically
- [ ] `wp_is_block_theme()` returns `true` (verify via the cache-clear PHP command above)

---

## Reference: How TT5 (Twenty Twenty-Five) Does It

The complete TT5 architecture reduced to essentials:

1. `style.css` has `Tags: full-site-editing` — marks it as an FSE theme
2. `templates/index.html` exists — the ONE file that triggers `is_block_theme() = true`
3. `parts/header.html` and `parts/footer.html` — editable in Site Editor
4. `patterns/*.php` files with PHP docblock header comments — auto-discovered patterns
5. `theme.json` version 3 with `templateParts` array — declares parts to the Site Editor

**TT5 `functions.php` is only ~150 lines** and does just:
- `add_editor_style()`
- `register_block_style()` for 2 styles
- `register_block_pattern_category()` for 2 categories
- One block bindings registration

No routing filters. No template overrides. No Customizer controls. The `theme.json` and block HTML templates do all the heavy lifting.

---

## Optional Phase 2 Enhancements

These are improvements for after the core FSE conversion is working:

1. **Pattern file thumbnails** — Move patterns from `inc/block-patterns.php` to `patterns/*.php` files with a `Viewport width: 1400` header comment. WordPress will auto-generate visual preview thumbnails for each pattern in the inserter.

2. **Navigation block persistence** — Create the main navigation as a saved Navigation block in the database. Reference it by ID in `parts/header.html` so it is shared across all header variations.

3. **Synced patterns** (formerly Reusable Blocks) — Make the Donate CTA and Newsletter signup synced patterns. Editing them in one place updates every page where they appear.

4. **Template locking** — Add `"lock":{"move":false,"remove":true}` to the `wp:post-content` block in templates to prevent accidental deletion of the content area.

5. **Multiple header variations** — Create `parts/header-transparent.html` for pages with full-screen hero images. Reference it with `{"slug":"header-transparent","area":"header"}` in relevant templates.

6. **Archive template** — Create `templates/archive.html` using `wp:query` for the Projects custom post type archive at `/project/`.

7. **Project single template** — Create `templates/single-project.html` for individual project posts.
