# Quick Start - How to Use Gutenberg Patterns

## Step 1: Go to WordPress Admin
Open: `http://localhost:8888/wp-admin`
Login with:
- Username: `admin`
- Password: `admin123`

## Step 2: Edit a Page
1. Go to "Pages" in the left menu
2. Click on any page (e.g., "Home", "About")
3. Click "Edit"

## Step 3: Enable Gutenberg
Look in the **right sidebar** for a box labeled **"Template System"**
- Check the checkbox: **"Use Gutenberg Block Editor"**
- Click **"Update"** to save the page

## Step 4: Access Patterns
After the page reloads:
1. Click the **"+" button** in the top left (or press `/`)
2. Click on the **"Patterns"** tab
3. Look for **"One More Tree Patterns"** category
4. Click on any pattern to insert it

## Available Patterns

### Hero (5 variants)
- Hero - Classic Light Theme (Flat)
- Hero - Forest Gradient (Flat)
- Hero - Image with Elegant Overlay
- Hero - Minimal Clean Design
- Hero - Split Screen Layout

### About (3 variants)
- About - Clean Modern
- About - Card Grid Layout
- About - Original

### Other Pages
- Projects - Clean List
- Impact - Minimal Stats
- FAQ - Clean Layout
- Contact - Simple Form
- Get Involved - Action Cards
- Volunteer - Clean Signup
- Donate - Donation Tiers
- Navigation with Ribbon (Flat)
- Footer

## Troubleshooting

### If You Don't See "Template System" Checkbox
The hybrid system may not be active. Check:
1. That `functions.php` includes the hybrid system code
2. That you have permission to edit the page
3. Clear browser cache and reload

### If You Don't See "One More Tree Patterns" Category
The patterns may not be registered. Check:
1. That `inc/block-patterns.php` exists
2. That it's included in `functions.php`
3. Check WordPress error log for PHP errors

### If You See Classic Editor Instead of Gutenberg
Gutenberg may be disabled. Check:
1. That "Use Gutenberg Block Editor" is checked
2. That the hybrid system filter is working
3. Try clearing browser cache

## Git Push

Your git remote is misconfigured. To fix:
```bash
git remote remove origin
git remote add origin YOUR_GITHUB_REPO_URL
git push -u origin testing
```

Replace `YOUR_GITHUB_REPO_URL` with your actual repository URL.
