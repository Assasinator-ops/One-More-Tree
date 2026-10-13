# How to Access Gutenberg Patterns

## Where to Do Gutenberg Stuff

### Step 1: Enable Gutenberg for a Page
1. Go to WordPress admin: `http://localhost:8888/wp-admin`
2. Navigate to any page (e.g., Home, About, Projects)
3. Click "Edit" on the page
4. Look for the **"Template System"** meta box in the right sidebar
5. Check the checkbox: **"Use Gutenberg Block Editor"**
6. Click "Update" to save

### Step 2: Access Block Patterns
After enabling Gutenberg:
1. The page will reload in the Gutenberg block editor
2. Click the **"+" button** (add block) in the top left
3. Click on **"Patterns"** tab
4. Scroll to **"One More Tree Patterns"** category
5. Click on any pattern to insert it

### Step 3: Edit Patterns
Once inserted:
- Click on any block to edit its content
- Use the block inspector (right sidebar) to change styles
- Apply block style variations
- Adjust spacing and colors using design tokens

## What If You Don't See Patterns?

### Possible Issues & Solutions

#### Issue 1: Classic Editor is Still Active
**Symptom:** You see the classic TinyMCE editor instead of Gutenberg blocks
**Solution:**
- Check that "Use Gutenberg Block Editor" checkbox is checked
- Clear browser cache
- Check that `inc/block-patterns.php` is included in `functions.php`

#### Issue 2: Patterns Category Not Appearing
**Symptom:** You see "Patterns" tab but no "One More Tree Patterns" category
**Solution:**
- Check that patterns are registered in `inc/block-patterns.php`
- Check PHP syntax: `php -l inc/block-patterns.php`
- Check that the file is included in `functions.php`

#### Issue 3: Hybrid System Not Working
**Symptom:** Checkbox "Use Gutenberg Block Editor" doesn't appear
**Solution:**
- Check that hybrid system code is in `functions.php`
- Check that meta box is registered
- Check user permissions (must be able to edit posts)

## Current Gaps & What's Missing

### Gap 1: Pattern Visibility
**Issue:** Patterns may not be visible if:
- Block pattern registration is not firing
- The theme is not active
- There's a PHP error preventing registration

**Solution Plan:**
- Verify block pattern registration is working
- Check for PHP errors in debug log
- Test with a fresh page

### Gap 2: Drag and Drop Experience
**Issue:** Gutenberg doesn't have traditional "drag and drop sections" like page builders
- Gutenberg uses block-based editing, not section-based
- You insert patterns as blocks, then move them

**Solution Plan:**
- Document how to move blocks up/down
- Create visual guide for block organization
- Consider adding more pattern categories for easier discovery

### Gap 3: Visual Preview
**Issue:** Patterns show as code blocks in the pattern library
- Users see block markup instead of visual preview
- Makes it hard to know what pattern looks like

**Solution Plan:**
- Add pattern screenshots to registration
- Use WordPress pattern preview feature
- Create visual pattern catalog

### Gap 4: Pattern Organization
**Issue:** 17 patterns may be overwhelming
- Hard to find the right pattern
- Categories may not be intuitive

**Solution Plan:**
- Better category organization
- Pattern naming conventions
- Search functionality

### Gap 5: Mobile Menu in Navigation Pattern
**Issue:** Navigation pattern doesn't include mobile menu functionality
- Mobile menu button is static
- No toggle functionality

**Solution Plan:**
- Add JavaScript for mobile menu toggle
- Integrate with existing navigation.js
- Add scroll behavior script

### Gap 6: Hero Image Dependencies
**Issue:** Hero patterns reference images that may not exist
- `assets/images/hero-forest.jpg`
- `assets/images/logo-colorful.png`

**Solution Plan:**
- Verify images exist in theme assets
- Add fallback images
- Use placeholder images if needed

## Immediate Troubleshooting

### Check if Gutenberg is Enabled
```php
// In functions.php, check this line:
add_filter('use_block_editor_for_post', 'one_more_tree_conditional_gutenberg', 10, 2);
```

### Check if Patterns are Registered
```php
// In inc/block-patterns.php, check this line:
register_block_pattern('one-more-tree/hero-classic-light', [...]);
```

### Check if CSS is Enqueued
```php
// In functions.php, check these lines:
wp_enqueue_style('one-more-tree-block-patterns', ...);
wp_enqueue_style('one-more-tree-block-styles', ...);
```

## How to Fix Common Issues

### Issue: Checkbox Not Appearing
Add this to `functions.php`:
```php
add_action('add_meta_boxes', 'one_more_tree_add_gutenberg_meta_box');
```

### Issue: Patterns Not Loading
Check that this is in `functions.php`:
```php
require_once get_template_directory() . '/inc/block-patterns.php';
```

### Issue: CSS Not Applying
Check that these are in `functions.php`:
```php
wp_enqueue_style('one-more-tree-block-patterns', get_template_directory_uri() . '/assets/css/block-patterns.css');
wp_enqueue_style('one-more-tree-block-styles', get_template_directory_uri() . '/assets/css/block-styles.css');
```

## Git Push Issue

The git remote is misconfigured:
```
origin	master (fetch)
origin	master (push)
```

This should be a repository URL, not "master". To fix:

```bash
git remote remove origin
git remote add origin YOUR_REPOSITORY_URL
git push -u origin testing
```

## Next Steps

1. **Fix git remote** (need repository URL from you)
2. **Test Gutenberg access** at `http://localhost:8888/wp-admin`
3. **Verify patterns appear** in pattern library
4. **Test pattern insertion** on a page
5. **Report any issues** so I can fix gaps
