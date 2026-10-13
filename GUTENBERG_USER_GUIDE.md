# One More Tree — Gutenberg Full Site Editing (FSE) User Guide

Welcome to the **One More Tree** Full Site Editing (FSE) and Gutenberg system. This guide covers everything you need to build, edit, and customize pages using drag-and-drop pre-made blocks and raw HTML customization.

---

## Table of Contents
1. [Overview & What Was Built](#1-overview--what-was-built)
2. [Accessing the Site Editor](#2-accessing-the-site-editor)
3. [Using Pre-Made Drag-and-Drop Blocks & Patterns](#3-using-pre-made-drag-and-drop-blocks--patterns)
4. [HTML & CSS Customizability](#4-html--css-customizability)
5. [How to Edit Existing vs. New Pages](#5-how-to-edit-existing-vs-new-pages)
6. [Global Styles & 1-Click Color Themes](#6-global-styles--1-click-color-themes)
7. [Deployment Instructions](#7-deployment-instructions)

---

## 1. Overview & What Was Built

The theme has been upgraded to a **Full Site Editing (FSE) Block Theme** (compatible with WordPress 6.4–6.7+, matching Twenty Twenty-Three through Twenty Twenty-Five standards).

* **8 Block Templates**: `index`, `page`, `page-no-title`, `page-canvas`, `single`, `home`, `front-page`, `404`
* **2 Live-Editable Template Parts**: `header` and `footer`
* **41 Block Patterns**: Curated across 9 One More Tree design categories
* **4 Turnkey Full-Page Layouts**: 1-click layouts for Home, About, Impact, and Donation pages
* **3 Global Style Variations**: Dark Canopy, Earth & Sandstone, and Emerald Forest
* **20+ Block Style Variations**: Glass Cards, Forest Buttons, Value Grids, Pill Badges, Testimonial Quotes
* **Always-On Gutenberg**: No toggles, no restrictions, full block editor everywhere

---

## 2. Accessing the Site Editor

To edit global templates, the header, the footer, and site-wide styles:

1. Log into **WP Admin** (`/wp-admin`).
2. Navigate to **Appearance → Editor** (`site-editor.php`).
3. You will see the visual Site Editor navigation:
   - **Templates**: Click to edit the layout of any page type (e.g. `Pages`, `Single Posts`, `Blog Home`, `404`).
   - **Template Parts**: Click to visually edit the global **Header** or **Footer**. Any changes made here immediately update across all pages using block templates.
   - **Styles**: Click the half-circle icon (top right) to open the **Global Styles** panel.

---

## 3. Using Pre-Made Drag-and-Drop Blocks & Patterns

When editing any page or post in Gutenberg:

1. Click the **`+` (Block Inserter)** button in the top-left toolbar.
2. Select the **Patterns** tab.
3. Browse the **One More Tree** categories:

### A. Turnkey Full-Page Layouts (`OMT: Full Page Templates`)
* **Full Home Page Turnkey Template**: Complete homepage with hero banner, animated stats counters, values grid, project showcase, donation tiers, and call to action.
* **Full About Page Turnkey Template**: Complete about page with organizational story, mission, 3-card values, and impact narrative.
* **Full Impact Report Turnkey Template**: Reforestation impact metrics, tree tracking, case studies, and environmental progress charts.
* **Full Donation Campaign Turnkey Template**: Multi-tier donation cards, direct funding impact, and volunteer callouts.

### B. Hero & Header Banners (`OMT: Heroes & Banners`)
* **Hero Banner with Light Theme**: Mission badge, large headline, dual CTA buttons, background forest image, and 3 metric badges.
* **Hero – Classic Light Theme (Flat)**
* **Hero – Forest Gradient**
* **Hero – Image with Elegant Overlay**
* **Hero – Minimal Clean Design**
* **Hero – Split Screen Layout**

### C. Content & Features (`OMT: Features & Values`)
* **About Section with Value Cards**: 3-column glass cards with icons and descriptions.
* **3-Chapter Restoration Story**: Narrative sequential layout for storytelling.
* **Project Feature Showcase**: Detailed project card with funding progress bar, status badges, and imagery.
* **Mission Section**
* **Featured Progress Section**
* **Rich Editorial Article Layout**

### D. Metrics & Stats (`OMT: Stats & Counters`)
* **Counter / Stat Section**: 3-card high-visibility counters for trees planted, active projects, and volunteer count.

### E. Actions, Tiers & Forms (`OMT: Calls to Action & Newsletters`, `OMT: Forms & Signups`)
* **Donation Tiers & 3D Section**: Pre-configured $25, $50, and $100 donation tier cards.
* **Call to Action / Donation Prompt**: High-conversion banner with forest button.
* **Get Involved – Action Cards**: 3-column actionable paths (Donate, Volunteer, Partner).
* **Volunteer – Clean Signup**: Volunteer form card.
* **Contact – Simple Form**: Pre-styled contact block.

---

## 4. HTML & CSS Customizability

Every pre-made block is standard block markup and can be customized at the code level:

### Method 1: "Edit as HTML" on Any Individual Block
1. Click on the block you want to edit (e.g., a card, heading, container, or button).
2. On the floating toolbar above the block, click the **`⋮` (Options)** button.
3. Click **"Edit as HTML"**.
4. You can directly edit the raw HTML tags, inline styles, custom data attributes, or Tailwind utility classes.
5. Click **`⋮` → "Edit visually"** to switch back to visual mode.

### Method 2: Full Page Code Editor
To view and edit the entire page's raw block HTML in one window:
* Press **`Ctrl + Shift + Alt + M`** (Windows/Linux) or **`Cmd + Option + Shift + M`** (Mac).
* Or click the top-right **`⋮` (Options)** menu in the editor header → select **Code Editor**.

### Method 3: Custom HTML Block (`core/html`)
To insert arbitrary HTML, SVG icons, embedded widgets, or scripts:
1. Click `+` and search for **Custom HTML**.
2. Drop it anywhere into the layout.
3. Paste your raw HTML/CSS.

### Method 4: Additional CSS Classes
Every block in Gutenberg has an Advanced panel:
1. Select the block.
2. In the right sidebar, expand **Advanced**.
3. Under **Additional CSS Class(es)**, add custom classes or Tailwind classes (e.g. `is-style-glass-card`, `bg-forest-900`, `shadow-elevated`).

### Method 5: 1-Click Block Styles
Select any block and look in the right sidebar under **Styles**:
* **Cards / Groups**: `Glass Card`, `Value Card`, `Feature Card`
* **Buttons**: `Forest Button`, `Outline Button`
* **Separators**: `Forest Separator`, `Gradient Separator`
* **Lists**: `Checkmark List`
* **Badges**: `Pill Badge`
* **Quotes**: `Testimonial Quote`

---

## 5. How to Edit Existing vs. New Pages

### For New Pages
1. Go to **Pages → Add New Page**.
2. It immediately opens Gutenberg with the block canvas.
3. In the right sidebar under **Page → Template**:
   - Choose **Page (No Title / Hero Canvas)** if your page starts with a full-width hero banner.
   - Choose **Default Template** if you want the page title to display automatically at the top.
4. Click `+` → **Patterns** → drag and drop your desired patterns.
5. Click **Publish**.

### For Existing Pages (`About`, `Projects`, `Impact`, `Volunteer`, etc.)
Existing pages currently have their PHP templates active. You have two options:

* **Keep PHP layout + add Gutenberg blocks**:
  All PHP templates support `the_content()`. Any blocks you add in the Gutenberg editor will render inside the content section of that page.
* **Switch to 100% Gutenberg drag-and-drop**:
  1. Open the page in **Pages → Edit**.
  2. In the right sidebar under **Template**, change the template from `About Page (PHP Template)` to **Page (No Title / Hero Canvas)**.
  3. Click `+` → **Patterns** → **OMT: Full Page Templates** → insert **Full About Page Turnkey Template**.
  4. Edit everything visually or via HTML.
  5. Click **Update**.

---

## 6. Global Styles & 1-Click Color Themes

In **Appearance → Editor → Styles**:

### Browse Style Variations
Click **Browse styles** to switch the color mood of the entire website with 1 click:
* **Default**: Crisp modern light theme with Forest greens and Earth tones.
* **Dark Canopy**: Deep forest dark mode with glowing emerald accents and soft borders.
* **Earth & Sandstone**: Warm earthy aesthetic featuring terracotta, amber, and olive tones.
* **Emerald Forest**: Vibrant lush green theme with high-contrast forest elements.

### Global Typography & Spacing
You can also adjust default heading fonts (Playfair Display), body text (Inter), content max-width (`840px`), and container max-width (`1280px`).

---

## 7. Deployment Instructions

All changes are synchronized in the production folder:
```
prod/wp-content/themes/one-more-tree/
```

To upload to your live server:
1. Upload the contents of `prod/wp-content/themes/one-more-tree/` to `/wp-content/themes/one-more-tree/` on your server.
2. In WP Admin on the server, visit **Settings → Permalinks** and click **Save Changes** (to refresh rewrite rules).
3. If using caching plugins (e.g. WP Rocket, LiteSpeed), click **Purge All Caches**.
4. Both Gutenberg Full Site Editing and the existing PHP pages will immediately be active.
