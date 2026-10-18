# One More Tree — Server Deployment Guide

**What this is:** the step-by-step guide for taking the finished local site and putting it
on production (onemoretree.eu) with nothing missing and nothing broken. Read it once end
to end before starting. Every step has an expected result — if a step doesn't produce
that exact result, stop and use the troubleshooting section instead of guessing.

**What you are deploying:**

| File | What it contains |
|---|---|
| `migration/onemoretree-upload.sql` | The complete database: 10 pages + 6 articles, block templates, navigation, options, Wordfence tables, plugin settings. Already rewritten for `https://onemoretree.eu` and the production `wp_` table prefix. |
| `migration/omt-files-bundle.tar.gz` | Theme (`lennox`), mu-plugins (forms/contacts/newsletters/SMTP), all media (`uploads/`), 3 active plugins (`gdpr-cookie-compliance`, `updraftplus`, `wordfence`), plus the content tools `apply.php` / `validate.php`. |
| `migration/RUNBOOK.md` | The short command-focused version of this same process (use it once you're comfortable). |

---

## Phase 0 — Before you touch anything

1. **Back up what is currently live.** This is your rollback anchor and it is not optional.
   In your TransIP panel's database manager (phpMyAdmin or Adminer), use **Export** on the
   live database and save the `.sql` file. If the panel gives you a shell/SSH option, the
   command form is:
   ```bash
   mysqldump --single-transaction -u DB_USER -p DB_NAME > ~/pre-migration-backup.sql
   ```
2. **Back up the current `wp-content/` folder too** (download via SFTP, or use your
   host's file-manager ZIP download). The DB backup alone does not undo theme/plugin
   file changes.
3. **Download the two artifacts** from this repo to your own machine:
   `migration/onemoretree-upload.sql` and `migration/omt-files-bundle.tar.gz`.
4. **Put the site into maintenance mode** if it already receives traffic — the swap
   takes a few minutes and visitors may see a broken page otherwise. Simplest safe
   option on production: a temporary maintenance plugin, or ask your host to enable
   their maintenance page.

---

## Phase 1 — Upload the files first

Files go before the database so that when the DB comes online with the new content,
the theme and media that render it are already in place.

1. Connect to the server with **SFTP** (TransIP's file manager also works; it is slower
   but functional). Find the WordPress root — the folder containing `wp-config.php`.
2. Upload `omt-files-bundle.tar.gz` into that root.
3. Extract it (the file manager usually has an "Extract" button; on SSH:
   `tar -xzf omt-files-bundle.tar.gz`). This creates a temporary `files/` folder.
4. Move the contents of `files/` into place:
   - `files/themes-lennox/` → `wp-content/themes/lennox/` (replace the old folder entirely)
   - `files/mu-plugins/` → `wp-content/mu-plugins/` (replace entirely — **this is what
     makes the contact and newsletter forms work**)
   - `files/plugins/gdpr-cookie-compliance`, `files/plugins/updraftplus`,
     `files/plugins/wordfence` → `wp-content/plugins/` (replace each)
   - `files/uploads/*` → `wp-content/uploads/` (merge; keep any existing files too)
5. Delete the leftover `files/` folder and the `.tar.gz` from the web root when done.
6. Set permissions so WordPress can manage media: `chmod -R u+rwX wp-content/uploads`.

**Expected result:** the site may still show the *old* pages at this point (the DB swap
hasn't happened), but it must not be a fatal error. If you see "critical error" now,
the theme move went wrong — check the folder names in Phase 1 step 4.

---

## Phase 2 — Import the database (three methods, in order of preference)

### Method A — WP-CLI (the default WP-recommended way — try this first)

If your host gives you SSH:

```bash
cd /path/to/wordpress-root
wp db import ~/onemoretree-upload.sql --allow-root
wp cache flush && wp rewrite flush && wp transient delete --all
```

`wp db import` is the safest option because it handles character sets and will refuse
to half-import something it cannot parse — you get a clear error instead of a silent
partial state.

**Expected result:** the command prints "Success: Imported". If it prints any error,
read the exact message — it tells you which line of the SQL is the problem.

### Method B — phpMyAdmin (if your host panel is phpMyAdmin)

1. Open the database manager, select the database `onempc_btpbhl64` (or whatever your
   production DB is called in TransIP's panel).
2. Go to the **Import** tab.
3. Choose `onemoretree-upload.sql`, set format to `SQL`, and — if you see the option —
   enable "Disable foreign key checks" before running.
4. Execute. The page should end with a green "import has been successfully finished"
   message listing the number of queries.

**Common failure here:** most phpMyAdmin installs cap upload size at 2–16 MB. This
dump is ~1.8 MB so it will normally fit, but if your host's limit is unusually low you
will get an "upload exceeds" error before any query runs — that means use Method C.

### Method C — Adminer (use this if A and B both fail)

Adminer is a single self-contained PHP database tool — same idea as phpMyAdmin but it
ships as **one file**, so you can drop it on any PHP host and use it when phpMyAdmin
is broken, missing, or refuses the upload.

1. **Get Adminer.** Download the single PHP file from the official site:
   https://www.adminer.org/en/download/ (choose the plain "PHP" version, no plugins).
2. **Place it in the web root** and rename it to something non-obvious, e.g.
   `db-maint-<random>.php`. **This is important** — Adminer has no password of its
   own, so anyone who guesses the filename gets your database. (Alternatively place it
   in a folder protected by HTTP Basic Auth if your host supports that.)
3. Open `https://onemoretree.eu/db-maint-<random>.php` in your browser.
4. Log in using the exact credentials from `wp-config.php`:
   - System: **MySQL**
   - Server: the `DB_HOST` value
   - Username / Password / Database: the `DB_USER`, `DB_PASSWORD`, `DB_NAME` values
5. Once inside, click **Import** (it appears in the left sidebar of the DB view).
6. Choose `onemoretree-upload.sql`, leave "Encodings" as default, and tick
   **Disable foreign key checks**. Submit.
7. **Verify the result page** — it should end with a green success message showing the
   number of executed statements (the dump contains roughly 50 statements: 39 table
   creates/drops plus the data inserts) and **no red error text**. Any red error line
   means the import stopped partway — fix the reported line, re-run.
8. **Delete `db-maint-<random>.php` from the server immediately** after you're done.
   Leaving it is the single biggest mistake people make with Adminer.

**When to use which:** try A first (cleanest, gives the clearest errors). If you have
no SSH, try B. Reach for C only when the default WP/phpMyAdmin route genuinely fails —
Adminer is powerful but it is a live database console with no built-in password, so it
gets used carefully and deleted right after.

---

## Phase 3 — After the import (5 minutes of setup)

These steps apply no matter which method you used:

1. **Confirm the user situation.** The dump now ships **one user only** (`Natnael`,
   administrator) — the known-password `admin` account was removed from the source
   before export. Quick check in Adminer/phpMyAdmin:
   ```sql
   SELECT ID, user_login FROM wp_users;
   ```
   Expected: exactly one row. Your login after import is that account, with the
   password you used locally.
2. **Re-save permalinks:** wp-admin → Settings → Permalinks → Save (any value; just
   press Save). This flushes nginx/Apache rewrite rules for the new content.
3. **Purge caches** — any cache plugin button, and if your host has a "purge cache"
   (Varnish/CDN) button, press that too. Caches can hold the *old* page for up to an
   hour otherwise.
4. **Check Wordfence:** wp-admin → Wordfence → Firewall. It will detect the new server
   automatically; if it asks to "optimize the Wordfence firewall," click it and let it
   run once. Then enable Login Security / brute-force protection in its settings.
5. **Add the same hardening constants to the production `wp-config.php`** (inside the
   PHP block, near the other `define()` lines — this mirrors what the local
   `wp-config.php` now has):
   ```php
   define( 'DISALLOW_FILE_EDIT', true );
   define( 'WP_POST_REVISIONS', 5 );
   define( 'EMPTY_TRASH_DAYS', 7 );
   define( 'OMT_SMTP_KEY', '<64 random hex characters>' ); // e.g. output of: php -r "echo bin2hex( random_bytes( 32 ) );"
   ```
   `DISALLOW_FILE_EDIT` removes the theme/plugin code editor from wp-admin — a
   standard lock-down so an attacker with a stolen admin session cannot edit PHP
   directly. `OMT_SMTP_KEY` is what lets the SMTP settings page store its password
   encrypted at rest (without it, credentials sit in plain text in the options table
   and would appear in any future DB dump — generate a key, then save the password
   once in the next step).
6. **Configure outgoing mail:** wp-admin → Settings → Email (SMTP). Enter the SMTP
   host, port (`465` = SSL, `587` = STARTTLS), the verified sender address
   (see the provider notes in SECURITY_FIXES.md), tick "Log in with…" and enter the
   credentials + password, then press **Send test email** to itself. Expected: a
   "Test email accepted" success notice. On failure the page shows the real
   PHPMailer transport error (e.g. "Could not connect to SMTP host"), which is
   enough to fix the setting without any other debugging.

---

## Phase 4 — Verification gate (do all of these before calling it done)

| Check | How | Expected |
|---|---|---|
| Homepage | open `https://onemoretree.eu` | 200, hero headline "One more tree." renders with the green theme |
| Every page loads | visit `/about/ /projects/ /impact/ /blog/ /contact/ /donate/ /faq/ /get-involved/ /volunteer/` | each returns a full page, no blank/fallback styling |
| Old duplicate pages gone | visit `/about-2/`, `/faq-2/` | 404 — they no longer exist |
| Forms work | `/contact/` → view source → search for `_omt_nonce` | found (this is what proves the mu-plugin is live) |
| Form actually submits | fill the newsletter box on the homepage, submit | "Thank you" message appears; the entry shows under Contact messages in wp-admin |
| Admin menus | wp-admin → Submissions, Contact messages, Newsletter | all three menus present |
| Media | homepage logo + hero image | load with no 404 in the browser console |
| Favicon | homepage `<head>` source | `<link rel="icon"` present, pointing at `logo-1-*.png` |
| SEO tags | homepage `<head>` source | `<meta name="description"` and `og:` tags present |
| SMTP settings page | wp-admin → Settings → Email (SMTP) | page renders, the configured host is shown, "Send test email" either succeeds or prints the real transport error |
| Newsletter readiness | wp-admin → Newsletter | no "SMTP not configured" warning once the SMTP step above is done |
| Old URLs | `https://onemoretree.eu/wp-sitemap.xml` | no `-2` slugs; pages listed match the 10 real pages |
| Privacy page | `/privacy-policy/` | renders (the footer links to it) |

**If any check fails**, go to the troubleshooting table below *before* making manual
edits — hand-fixing a half-migrated site is how things got broken the last time.

---

## Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| White screen / "critical error" | Theme or mu-plugin files missing or wrong path | Re-do Phase 1 steps 3–4; make sure the folder names are exactly `themes/lennox`, `wp-content/mu-plugins` |
| Pages render but unstyled | `themes/lennox` incomplete, or old theme still active | wp-admin → Appearance → Themes → activate `lennox`; confirm `style.css` exists inside the folder |
| Forms show but submissions vanish | `mu-plugins/omt-forms.php` not present | Re-upload the `mu-plugins` folder; verify via `wp plugin list --allow-root` on SSH or by checking for `_omt_nonce` in page source |
| Import dies mid-file with a syntax error | Truncated upload (binary mode in SFTP, or size cap) | Re-upload in **binary** mode; or use Method C (Adminer) which handles the full 1.8 MB file directly |
| Duplicate `-2` pages still live | Old data mixed with new | Re-run the import (the dump has `DROP TABLE IF EXISTS` so it fully replaces — this is safe) |
| 500 error only on `/wp-admin/` | Stale object cache or `.htaccess` from old site | Delete `.htaccess` in the root, then re-save permalinks (Phase 3 step 2) |
| `wp db import` says table prefix mismatch | `wp-config.php` has a non-`wp_` prefix | This dump assumes `wp_`; if yours differs, change `$table_prefix` in `wp-config.php` to `'wp_'` before importing |

---

## Rollback (nothing merges — it's one-directional)

If the verification gate fails badly and you need the old site back:

```bash
wp db import ~/pre-migration-backup.sql   # or re-import the backup .sql via phpMyAdmin/Adminer
```

plus restore the `wp-content/` snapshot from Phase 0 step 2. The site returns to exactly
its pre-migration state, because nothing was merged — the new import fully replaced the
old tables and the file swap fully replaced the folders.

---

## Why this order (files → DB → setup → verify)

- **Files first:** the new DB content references the new theme's block patterns and the
  mu-plugin's form injection. If the DB lands first, WordPress would try to render
  content with a theme that doesn't know those blocks yet.
- **DB second:** the dump includes `DROP TABLE IF EXISTS` for every table, so the import
  atomically replaces the old broken state (including the duplicate `-2` pages) instead
  of mixing with it — there is no "half old, half new" outcome to clean up later.
- **Setup third:** caching and rewrite rules must be reset *after* the new content
  exists, otherwise WordPress caches the old routing table.
- **Verify last:** every item in the gate table is a symptom that was broken in the
  previous migration — checking each one proves this run didn't repeat it.
