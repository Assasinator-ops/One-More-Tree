# One More Tree

A Spanish reforestation nonprofit's website: a WordPress **block (FSE) site** built on the
custom **Lennox** theme, with a dependency-free contact/newsletter system implemented as
must-use plugins, and a file-based content pipeline that keeps every page reviewable in
version control instead of only living in a database.

- **Production:** https://onemoretree.eu (TransIP hosting, nginx, MySQL)
- **Local development:** Docker Compose on port `8888` (see below)

---

## Repository map

| Path | What it is |
|---|---|
| `wp-content/themes/lennox/` | The production theme — full-site-editing block theme: `theme.json` design tokens (green palette, fluid `clamp()` type scale), block patterns, starter-site manifests, admin dashboard, responsive data-table CSS |
| `wp-content/mu-plugins/` | Must-use plugins (cannot be deactivated): `omt-forms.php` (contact + newsletter handling: render-time CSRF nonce, honeypot, time-trap, per-IP rate limits, hashed IPs, private `omt_lead` post type), `omt-contacts.php` (contact-message admin), `omt-newsletters.php` (batched newsletter sending), plus their stylesheets |
| `site/` | The content pipeline — see next section |
| `docker-compose.yml` | Local WordPress + MySQL 8 stack |
| `migration/` | Production deployment artifacts — see [migration/DEPLOYMENT_GUIDE.md](migration/DEPLOYMENT_GUIDE.md) |
| `mysql-data/` | Local DB volume (gitignored) |
| `SECURITY_FIXES.md` | Security audit and remediation plan |
| `SITE_AUDIT.md` | Full site-quality audit (content, SEO, performance) |

---

## Content pipeline (`site/`)

Pages, templates and navigation are authored as **plain block-markup files** under
`site/content/`, then applied to the database by an idempotent installer:

```
site/
├── content/
│   ├── pages/       # about, blog, contact, donate, faq, get-involved, impact, projects, volunteer
│   ├── parts/       # header.html, footer.html (block template parts)
│   └── templates/   # front-page.html
├── apply.php        # idempotent installer (runs inside the container)
├── validate.php     # parses every content file through the block parser
└── deploy.sh        # one command: stage → lint → apply → validate
```

**Why this shape:** editing pages as files means changes are diffable, reviewable and
replayable — the database stops being the only copy of the content. `apply.php` upserts
each page by slug (re-running it updates instead of duplicating), normalises stale
`_wp_page_template` values left behind by the previous classic theme, rebuilds the
navigation, fixes reading settings, and re-asserts the palette. `validate.php` proves
every file parses into registered blocks (currently: 764 blocks, 0 unknown, 0 problems).

Run it:

```bash
./site/deploy.sh          # everything, with lint + verification
```

---

## Local development

```bash
docker compose up -d      # starts one-more-tree-wp (:8888) + one-more-tree-db
```

- Site: http://localhost:8888 (DB prefix `omT_`, credentials `wordpress`/`wordpress`)
- WP-CLI is available inside the container:
  `docker exec --user www-data one-more-tree-wp wp --allow-root <command>`

The local container binds `127.0.0.1:8888` on purpose — never expose the dev stack on
`0.0.0.0` over untrusted networks.

---

## Deployment to production

Everything needed to move the whole site to onemoretree.eu lives in `migration/`:

| File | Purpose |
|---|---|
| `onemoretree-upload.sql` | Complete database dump, production-ready: `wp_` table prefix, all URLs rebased to `https://onemoretree.eu`, `DROP TABLE IF EXISTS` for every table (full atomic replacement), zero junk (no sample pages, revisions, demo data, test leads) |
| `omt-files-bundle.tar.gz` | Theme + mu-plugins + media + the three active plugins (`gdpr-cookie-compliance`, `updraftplus`, `wordfence`) |
| `DEPLOYMENT_GUIDE.md` | The step-by-step guide — read this first when deploying. Covers the default `wp db import` route, the phpMyAdmin route, and an **Adminer fallback** for when both fail, plus a verification gate and troubleshooting table |
| `RUNBOOK.md` | The short command-focused version of the same process |

**Order matters:** files first, then the database, then caches/permalinks, then the
verification gate. The guide explains why at the end.

---

## Security posture

- Wordfence active (brute-force + scan), login protected; `xmlrpc.php` returns 403,
  REST user enumeration returns 401, no directory listing.
- Forms: render-time CSRF nonce (cannot be stale), honeypot + minimum-submit-time
  trap, per-IP rate limits, hashed IP storage (raw addresses never persisted),
  `wp_safe_redirect` target validation, narrow `wp_kses` whitelist for form markup.
- The production DB password was exposed once and should be rotated; the full plan and
  remaining hardening (security headers, etc.) is in [SECURITY_FIXES.md](SECURITY_FIXES.md).
- Secrets never enter git: `.gitignore` excludes `tmp/`, `prod/`, `wp-config.php`,
  `*.sql`, `*.tar.gz`, `.freebuff/`, `mysql-data/`, `wp-content/uploads/`, `wp-content/wflogs/`.

---

## Troubleshooting quick hits

| Symptom | First thing to check |
|---|---|
| Redirect loop locally | `wp option get siteurl` should be `http://localhost:8888` |
| Forms don't submit | `wp-content/mu-plugins/` exists and contains `omt-forms.php`; page source contains `_omt_nonce` |
| Unstyled pages | Active theme is `lennox` (`wp option get stylesheet`) |
| Duplicate `-2` pages on prod | The DB import wasn't a full replacement — re-import (safe: the dump drops all tables first) |
| Wide tables overflow on phones | `.wp-block-table { overflow-x: auto }` is in `themes/lennox/style.css` — confirm the file shipped |

---

## Docs index

- [migration/DEPLOYMENT_GUIDE.md](migration/DEPLOYMENT_GUIDE.md) — server upload & DB import (incl. Adminer)
- [migration/RUNBOOK.md](migration/RUNBOOK.md) — short command version of the deploy
- [SECURITY_FIXES.md](SECURITY_FIXES.md) — security audit and fixes
- [SITE_AUDIT.md](SITE_AUDIT.md) — site-quality audit
- `site/README.md` — content-pipeline details
