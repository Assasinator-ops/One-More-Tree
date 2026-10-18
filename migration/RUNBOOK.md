# One More Tree — Production Migration Runbook

Date: 2026-10-17 · Target: https://onemoretree.eu (TransIP)
This is a **full replacement** migration: the production database is entirely replaced
by the exported local state, so the site ends up exactly as it is locally — no
duplicates, no missing mu-plugins, no drifted content.

## Migration package contents (`migration/`)

| File | Purpose |
|---|---|
| `onemoretree-upload.sql` | Complete DB dump, prod-ready: prod `wp_` prefix, URLs already rebased to `https://onemoretree.eu`, `DROP TABLE IF EXISTS` included for every table. Verified with a full round-trip import (row counts identical). |
| `omt-files-bundle.tar.gz` | Code + media bundle: `themes-lennox/` (full Lennox FSE theme), `mu-plugins/` (forms/contacts/newsletters/SMTP/cache-headers + CSS), `uploads/` (all media), `plugins/` (gdpr-cookie-compliance, updraftplus, wordfence — the three active plugins), plus `apply.php` and `validate.php` fallback tools. |
| `omt-dump-urls-omT.sql` | Intermediate artifact (URL-mapped dump before prefix consolidation). Not needed on the server; kept for audit trail only. |

**What this state includes:** 91 posts (13 pages incl. all content pages and the 2
block templates, 2 navigation menus, 2 wp_template, 3 template parts incl. header/footer,
5 private form submissions, send-ready newsletter list), 1 user, all media files
(9 uploads incl. site logo + hero art), 227 options (front page set, blog name,
GDPR plugin settings), and 13 tables renamed to the production `wp_` prefix. Every URL
in post content, meta, options and the block-navigation JSON is `https://onemoretree.eu` —
zero `localhost` references remain (verified).

## Pre-flight (one-time, on the TransIP server)

1. **Back up everything first** (rollback anchor):
   ```bash
   # On the server - full DB snapshot
   mysqldump --single-transaction -u <DB_USER> -p <DB_NAME> > ~/pre-migration-backup-$(date +%F).sql
   # Or via phpMyAdmin "Export".
   ```
   Also snapshot the current `wp-content/` tree (copy or zip it).
2. Confirm prod `wp-config.php` has `$table_prefix = 'wp_';` (it does, per your config)
   and the DB name/user match it.
3. Put the site in maintenance iframe/notice if you care about visitor UX — the swap
   takes minutes.

## Step A — sync files first

Upload `omt-files-bundle.tar.gz` to the server, then:

```bash
cd <path-to>/<site-root>/wp-content
tar -xzf ~/omt-files-bundle.tar.gz
# bundle extracts to ./files/ - place each piece with cp -a (mv can silently
# drop entries when /tmp and the web root are on different filesystems when
# the panel extracts archives elsewhere):
rm -rf wp-content/themes/lennox
cp -a files/themes-lennox wp-content/themes/lennox
rm -rf wp-content/mu-plugins
cp -a files/mu-plugins wp-content/mu-plugins
rm -rf wp-content/plugins/gdpr-cookie-compliance wp-content/plugins/updraftplus
cp -a files/plugins/gdpr-cookie-compliance files/plugins/updraftplus wp-content/plugins/
cp -a files/plugins/wordfence wp-content/plugins/
cp -a files/uploads/. wp-content/uploads/   # media (13 files under 2026/, incl. the logo-1 favicon set)
# apply.php / validate.php stay in files/ as fallback tools - do not web-serve them
chmod -R u+rwX wp-content/uploads
# VERIFY - abort and re-sync if this does not all match:
test "$(ls wp-content/mu-plugins | wc -l)" -ge 7 \
  || { echo 'mu-plugins incomplete - re-extract the bundle'; exit 1; }
test "$(find wp-content/uploads -type f -size +0 | wc -l)" -ge 13 \
  || { echo 'media incomplete - re-extract the bundle and re-copy uploads'; exit 1; }
test -f wp-content/uploads/2026/10/logo-1.png \
  || { echo 'favicon file missing - re-copy uploads'; exit 1; }
```

(SFTP route: same tree mapping copy-by-copy.)

## Step B — replace the database

Option 1 — phpMyAdmin: Import `onemoretree-upload.sql` into the DB.
Option 2 — SSH + mysql (recommended, handles size):

```bash
# using a private credentials file, not inline passwords
mysql --defaults-extra-file=~/private.cnf <DB_NAME> < ~/onemoretree-upload.sql
```

The dump carries its own `DROP TABLE IF EXISTS`, so every table is fully replaced —
all existing duplicate `-2` pages disappear together with the stale options.
No prefix renaming needed: tables arrive as `wp_*`, matching prod config.

## Step C — post-import hardening (one minute)

1. **Users changed with the DB**: log in with the local credentials. The dump ships
   with exactly one account — user #1 `Natnael` (natiemls72@gmail.com). The old dev
   `admin` account and its public password no longer exist in the dump; verify with
   `wp user list --allow-root` (expected: exactly one row).
2. **Outgoing mail:** add `define( 'OMT_SMTP_KEY', '<64 random hex chars>' );` to
   prod `wp-config.php`, then set the transport under **Settings → Email (SMTP)**
   and press *Send test email* (full walkthrough: DEPLOYMENT_GUIDE.md, Phase 3).
3. Purge caches: "Purge cache" on any cache module; or WP-CLI
   `wp cache flush && wp transient delete --all && wp rewrite flush`.
4. Re-save Settings → Permalinks once (flushes rewrite rules for nginx).

## Step D — verify (go/no-go gate)

Run all of these before considering the migration done:

| Check | Expected |
|---|---|
| `curl -s https://onemoretree.eu` | 200, `<h1>` contains "One more tree." |
| All 10 content pages (`/about/`, `/projects/`, `/impact/`, `/faq/`, `/donate/`, `/contact/`, `/volunteer/`, `/get-involved/`, `/blog/`, plus `/`) | 200 and full content (h2 counts match local page files) |
| `-2` duplicates (`/about-2/`, `/faq-2/`, …) | 404 — they no longer exist |
| Forms on `/contact/` + footer newsletter | contain `_omt_nonce`, `omt_form`, `omt_ts` hidden fields |
| `omt-forms.css` | referenced in page HTML |
| Foundation of leads: submit the newsletter form once | "Thank you" notice, lead appears under Contact messages in wp-admin |
| wp-admin | login as user #1; Submissions / Contact messages / Newsletter menus present |
| `wp-sitemap.xml` | no `-2` slugs |
| Media | logo renders, no 404 in browser console/network |
| Emails/notification | no broken internal links (all URLs https://onemoretree.eu) |
| Anonymous freshness | edit a page, open it in an incognito window | change visible within ~2 minutes (host page cache honours the `omt-cache-headers.php` policy). Instantly stale for longer → purge the host cache (TransIP panel → Website Caching → Purge) |

Optional deterministic re-pour (only if content looks off): run `apply.php` and
`validate.php` from the bundle with the server's PHP CLI against the WP root — they
are idempotent and will re-assert pages/templates/nav/reading settings from the
provided content source. Their hardcoded wp-load path assumes `/var/www/html`; adjust
the four path constants at the top if your root differs.

## Rollback (if anything fails the gate)

```bash
mysql --defaults-extra-file=~/private.cnf <DB_NAME> < ~/pre-migration-backup-YYYY-MM-DD.sql
```

plus restore the `wp-content` snapshot from step 1 of pre-flight to undo file changes.
The site returns to its pre-migration state without data loss because nothing merged —
the replacement only went one direction.
