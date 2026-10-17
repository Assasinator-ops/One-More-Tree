# One More Tree — site content system

This directory is the **source of truth for the site's content**. The WordPress
database holds the rendered result, but nothing here should be edited by hand in
`/wp-admin` unless you then copy the change back (see *Keeping in sync*).

## Layout

```
site/
  content/
    templates/front-page.html   -> wp_template   (front page layout)
    parts/header.html           -> wp_template_part "header"
    parts/footer.html           -> wp_template_part "footer"
    pages/*.html                -> page posts (about, projects, impact, faq,
                                   get-involved, volunteer, contact, donate, blog)
  apply.php                     -> runs inside the container, writes to WordPress
  validate.php                  -> parses every stored post, asserts all blocks are known
  deploy.sh                     -> stages the above into the container and applies it
  _image-audit.html             -> contact sheet used to audit the theme's imagery
```

Everything is plain **block markup** (the same format WordPress stores in
`post_content` and `templates/*.html`), so it is diff-able, reviewable and
version-controlled instead of existing only in a database.

## Deploy

```bash
./site/deploy.sh
```

The script:

1. checks the `one-more-tree-wp` container is running;
2. copies `content/`, `apply.php` and `validate.php` to `/tmp/omt-content` inside it
   (the container only bind-mounts `./wp-content`, so it cannot see `./site`);
3. lints the PHP, including the must-use plugin;
4. applies the content as user #1 so `kses`, revisions and capabilities behave
   exactly as they would for the site owner;
5. runs `validate.php` and fails loudly if any block is unknown or any marker leaks.

It is **idempotent** — re-running updates the same posts rather than duplicating
them. Override the container with `WP_CONTAINER=... ./site/deploy.sh`.

`apply.php` also performs the one-time jobs that make a restored database usable:

- resets stale `_wp_page_template` values (`page-about.php`, `front-page.php`, …)
  left behind by the previous theme, which otherwise block every page update;
- points the header navigation at the real pages;
- sets `show_on_front=page` / `page_on_front=7` and clears the stale `page_for_posts`;
- publishes the privacy policy (the footer links to it) and drafts the stock
  WordPress *Sample Page*;
- corrects `custom-text` in global styles to `#22312a` — it shipped as the brand
  green `#007a37`, which made every paragraph and heading on the site green.

## Keeping in sync

The Site Editor writes changes straight to the database. If you edit templates or
pages in `wp-admin`, pull the result back so this directory stays authoritative:

```bash
# example: copy the live front-page template back into the repo
docker exec one-more-tree-wp php -r '
  require "/var/www/html/wp-load.php";
  echo get_post_field("post_content", 44);' > site/content/templates/front-page.html
```

Prefer editing here and re-running `deploy.sh`.

## Forms

Newsletter and contact forms are handled by the must-use plugin at
`wp-content/mu-plugins/omt-forms.php` (plus `omt-forms.css`). There is **no form
plugin dependency**.

Forms are authored as ordinary HTML inside a `core/html` block. Two markers are
resolved at render time by a `render_block` filter:

| Marker | Replaced with |
|---|---|
| `<span data-omt-fields="newsletter\|contact"></span>` | `action`, `_omt_nonce`, `redirect_to`, `omt_ts` |
| `<div data-omt-notice="newsletter\|contact"></div>` | success / error message for this request |

Because the nonce is generated at render time it can never go stale, and because
the markup is rendered server-side it never passes through `kses` as content.
`wp_kses_allowed_html` is extended so that a user *without* `unfiltered_html`
editing a page cannot have the form tags stripped on save.

Submissions are stored as the private `omt_lead` post type (admin menu →
**Submissions**) with kind, email, name, topic, message, source page, a hashed IP
and the user agent. Rate limited to 6 per IP per hour, with a honeypot and a
time trap.

**Nothing is emailed.** The container has no mail transport and this install has no
SMTP provider, so submissions are stored only. Hook `omt_lead_stored` to forward
them:

```php
add_action( 'omt_lead_stored', function ( $lead_id, $kind, $data ) {
    // send to a CRM / transactional email provider
}, 10, 3 );
```

## Validation

```bash
docker exec one-more-tree-wp php /tmp/omt-content/validate.php
```

Reports, per post: block count, rendered HTML size, and any unknown block names or
leaked markers. Exit code 1 on problems. Current status: **20 posts, 781 blocks,
0 unknown, 0 problems**.

## Known gaps

- **Imagery is placeholder.** Every image in
  `wp-content/themes/lennox/assets/images/` is generic corporate stock — offices,
  laptops, charts, headshots. None of it shows a forest, a tree or a planting day.
  The layouts use them with empty `alt` (decorative) rather than describing
  content the photograph does not contain. **Real project photography is required**;
  see `_image-audit.html` for the full contact sheet.
- **No payment flow.** `donate.html` presents tiers and a pledge form that stores
  an enquiry; it states openly that card payment is not wired up. A provider
  (Stripe, GoCardless, …) still needs to be chosen and integrated.
- **Social links are absent on purpose** — no verified handles were available, and
  guessing them risks pointing at a stranger's account.
- **Stale second front page.** `wp_template` #35 (`front-page`) belongs to the
  `one-more-tree` theme term. It is inert while Lennox is active but should be
  deleted if that theme is ever restored.
