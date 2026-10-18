# One More Tree — Full Website Audit

> **Status update (2026-10-18, latest sweep):** all of the 2026-10-17 resolutions below hold and two
> further additions are now in the codebase: outgoing mail is configured from
> wp-admin → Settings → Email (SMTP) (`omt-smtp.php`, encrypted-at-rest password via
> `OMT_SMTP_KEY`), and anonymous-page staleness is fixed (`omt-cache-headers.php`
> sends explicit Cache-Control so host/CDN caches cannot serve hour-old HTML).
> The bundle now ships 2,604 entries including both plugins.
>
> **Status update (2026-10-17, after final sweep):** P0-1 (forms) and P0-2/P0-3
> (duplicate `-2` pages, drift) are **resolved in the migration package** — the
> rebuilt dump + files bundle carry working mu-plugins, canonical pages only, and
> Wordfence. P1-5 (SEO) is **fixed in code**: meta description, Open Graph tags and
> a favicon now render. P1-6 (security headers), P1-7 (locale) and the DB password
> rotation remain **server-side actions** — steps are in
> `migration/DEPLOYMENT_GUIDE.md` and `SECURITY_FIXES.md`. P2-8 (jQuery) and P2-10
> (photography) are still open content/product decisions.

Date: 2026-10-17 · Local: Docker (`one-more-tree-wp`, port 8888) · Production: https://onemoretree.eu (TransIP, nginx)

Method: live HTTP checks against production (25 URLs), DB checks in the local container,
block-level validation via `site/validate.php`, code review of mu-plugins, theme, and repo
hygiene. All findings below are evidence-based from these checks.

---

## P0 — Critical (site function is broken)

### 1. Forms on production cannot submit
- Evidence: on `https://onemoretree.eu/contact/` the `<form>` renders with
  `action="/wp-admin/admin-post.php"` but **no hidden injection fields** — no
  `omt_form=contact`, no `_omt_nonce`, no `action=omt_form`, no `omt_ts`. Only the
  honeypot (`omt_hp`) is present.
- `omt-forms.css` is **not enqueued** on any prod page.
- The mu-plugin `omt-forms.php` (in `wp-content/mu-plugins/`) is what injects these
  fields at render time and enqueues the CSS. Conclusion: production runs an old or
  partial mu-plugin version — the local one is correct.
- Effect: every submission fails the nonce check → user gets an "expired" error, or
  nothing happens. Lead capture (the site's core conversion) is dead.

**Fix:** rsync/copy the current `wp-content/mu-plugins/` (5 plugins + their stylesheets, incl. `omt-smtp.php` and `omt-cache-headers.php`) to production,
then purge caches. Verify with `grep -c '_omt_nonce' contact page HTML == 1` per form.

### 2. Duplicate `-2` pages still live on production
- Evidence: all 11 duplicate slugs return HTTP 200 and are published:
  `home-2, about-2, projects-2, impact-2, blog-2, contact-2, donate-2, faq-2,
  volunteer-2, get-involved-2` — while the original slugs serve thin/partial content
  (e.g. `/about/` has 3 h2s, `/about-2/` has the full 7 including "Our method, in four
  steps").
- The homepage navigation links to `/faq-2/` — a duplicate URL is in the live menu.
- All duplicates also feed `wp-sitemap.xml` → Google is indexing every page at two URLs.
- The SQL fix produced earlier (`tmp/fix-production-sql.sql`) was **never applied**.

**Fix:** apply the dedupe (copy content `-2` → original, delete duplicates, repoint nav,
rebuild sitemap) or re-run the idempotent `site/apply.php` content installer on prod,
which upserts by original slug. Then add 301 redirects from `-2` URLs.

### 3. Production has drifted from the local source of truth
- Local `site/content/pages/*.html` (validated: 806 blocks, 0 problems) is the canonical
  content. The prod DB was assembled by hand-edits and partial imports, so page content,
  menus, and templates diverge.
- Example: prod nav contains `faq` + `faq-2` links; local `apply.php` builds a nav from
  6 specific links only.

**Fix:** treat local as the source of truth → one idempotent run of `site/apply.php`
(page upserts + templates + nav + reading settings), after the dedupe in #2.

---

## P1 — High (real risk, fix this week)

### 4. Production DB password is exposed
- The TransIP DB host, user and password circulated in chat and sit in
  `tmp/fix-production-sql.sql` (header contains host + user; the password was pasted in
  chat). Repo visibility is unknown; `tmp/` is not gitignored.
**Fix:** add `tmp/`, `prod/`, `wp-config.php`, `*.sql` to `.gitignore`; scrub secrets
from the SQL file; rotate the DB password after the remaining DB work is done.

### 5. No SEO basics
- `<title>OneMoreTree.eu</title>` (also a brand typo — no space), no
  `<meta name="description">`, zero `og:` tags, no favicon `link rel=icon`.
- Duplicate URLs (see #2) compound it.

**Fix:** set Blog Title/Tagline (`blogdescription` is empty), pick home/page titles, add
favicon; optionally a light SEO plugin (e.g. SEOPress) for meta + og tags.

### 6. No security headers; login unthrottled
- Missing: `Strict-Transport-Security`, `Content-Security-Policy`,
  `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`.
- `wp-login.php` returns 200 with no rate limiting; Wordfence is installed locally but
  not in `active_plugins` — confirm it's active on prod or replace with nginx
  login rate-limiting.
- Already good: `xmlrpc.php` → 403, REST users → 401, `readme.html` → 404.

**Fix:** add HSTS/XCTO/XFO/Referrer-Policy in the TransIP nginx/proxy config, and turn
on (or install) login protection.

### 7. Locale mismatch — Dutch strings on an English site
- REST error messages come back in Dutch ("Je hebt geen toestemming…"), and the GDPR
  cookie banner labels are Dutch. TransIP defaulted the install to `nl_NL`.
**Fix:** Settings → General → Site Language = English; also check `WPLANG`/`locale`
option and user-level locale.

---

## P2 — Medium (clean-up and polish)

### 8. WordPress is loading jQuery + jquery-migrate on an FSE block theme
- ~90 KB of JS that Lennox patterns don't need. Find the doing-it-wrong plugin/setting
  and drop it.

### 9. HTML cache is long-lived
- Homepage responses serve with `age: 3483` (~58 min). Good for speed, but combined with
  manual DB edits it means fixes take up to an hour to become visible. Confirm nginx
  cache TTL for `text/html` and know the purge step before each deploy.

### 10. No real photography
- Every image on the site is inline data-URI/generated SVG placeholder art. Media
  library is effectively empty. For the NGO's credibility, real project photos are the
  biggest content gap.

### 11. Local repo hygiene / repo rot
- ~20 AI-generated status docs at the repo root (`FINAL_CONFIRMATION.md`,
  `GUTENBERG_*`, `PHASE_1_COMPLETION.md`, …) — move to `docs/` or delete.
- 5 legacy themes shipped in `wp-content/themes/` (colibri-wp, denovr-lite, webrand-lite,
  one-more-tree, twentytwentythree/twentytwentyfour/twentytwentyfive) — dangerous attack
  surface and confusion; only `lennox` is used.
- Root scripts `inject-demo.php`, `create_admin.php` in the web root; `prod/wp-content`
  partial copy; `tmp/` test scripts. Clean up, keep `site/` as the only pipeline.
- `wp-config-sample.php` is tracked — make sure the real `wp-config.php` is not.

### 12. Local DB has duplicate `front-page` template posts
- `site/validate.php` output lists `template/front-page` **twice** (147-block and
  74-block versions). The `omt_upsert_template` lookup narrows by wp_theme terms, so a
  template whose `wp_theme` term is stale is treated as "not found" and a second copy is
  created. Dedupe: delete the orphan template revision in the local DB, and make the
  locator `posts_per_page => -1` → sorted by modified date with strict taxonomy match
  (it already tax-filters; the orphan likely has a blocked/legacy status — check
  `post_status any` catch).
- Also present locally: `lennox-injection-demo` page and `sample-page` from demo runs.

### 13. No reproducible deploy pipeline for production
- The current prod state was reached by manual SQL. `site/deploy.sh` targets the local
  docker container only. Until items #1–#3 land, any deploy is a manual gamble.

**Fix:** extend the pipeline with a `site/deploy-prod.sh` (files first via
rsync/SFTP, then `apply.php` via SSH wp-cli/php, then cache purge + verification), and
never hand-edit prod SQL again.

---

## What's already good
- Local content pipeline is solid: `site/apply.php` is idempotent (upserts pages by
  slug, resets stale `_wp_page_template`, fixes reading settings), and
  `site/validate.php` confirms 806 blocks parse with 0 problems.
- `omt-forms.php` code quality is high (CSRF nonce at render time, honeypot + time trap
  + rate limit, hashed IPs, kses whitelist for form tags).
- `omt-forms.css` is a well-built modern stylesheet (container queries with fallback,
  `:focus-visible`, `appearance:none` select, on-dark variant).
- Lennox theme itself is intact and production-ready; prod serves `lennox/style.css`
  and `style-shared.min.css` correctly.
- Docker local: correct port map, clean `.htaccess`, correct `siteurl`/`home`/
  front-page settings.

## Suggested order of operations
1. Sync current `wp-content/mu-plugins/` to prod → forms work again (P0-1).
2. Run the dedupe + `site/apply.php` on prod → remove `-2` duplicates, canonical nav (P0-2, P0-3).
3. Rotate the TransIP DB password, then gitignore/scrub secrets (P1-4).
4. Titles/description/favicon + locale fix (P1-5, P1-7).
5. Security headers + login protection (P1-6).
6. Clean-up batch (P2): remove legacy themes/docs, jQuery, demo pages; add real photos.
7. Build `site/deploy-prod.sh` and declare local the source of truth.
