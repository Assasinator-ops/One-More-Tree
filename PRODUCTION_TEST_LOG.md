# One More Tree — Production-Readiness Test Log

**Date:** 2026-10-18 · **Method:** full production-parity simulation on this machine — a fresh
WordPress 7.1 container + an empty MySQL 8 database (`wp_` prod prefix) on `127.0.0.1:8889`,
into which the *actual* migration package (`migration/onemoretree-upload.sql` +
`migration/omt-files-bundle.tar.gz`) was imported using the same steps the production guide
prescribes. Tests then ran the Phase 4 verification gate of `migration/DEPLOYMENT_GUIDE.md`
through `curl -H 'Host: onemoretree.eu'` so every URL matches production form.

**Sim environment (kept for re-testing):** containers `omt-prod-wp`, `omt-prod-db`
(docker volume `omt-prod-db-data`). Remove with:
`docker rm -f omt-prod-wp omt-prod-db && docker volume rm omt-prod-db-data && docker network rm omt-prod-net`
(sim uses `Host: onemoretree.eu` over HTTP; canonical httpsUrl redirects noted below are sim-only).

---

## Where it went wrong (bugs found)

### BUG-1 (High) — Migration bundle shipped without the favicon media → broken favicon after import
- **Found:** after import, `site_icon` (attachment 107) points at `2026/10/logo-1.png`;
  the front page's favicon tags reference `logo-1-150x150.png` / `logo-1-300x300.png`.
  All `logo-1*` files were served **404**, because the files bundle had been built before the
  site icon was imported (favicon landed after that rebuild) and was never rebuilt to catch up.
- **Impact:** every production page would render a broken favicon + Apple-touch icon.
- **Fix (applied):** added `logo-1.png`, `logo-1-150x150.png`, `logo-1-300x300.png`,
  `logo-1-768x768.png` to `migration/files/uploads/2026/10/`, rebuilt
  `migration/omt-files-bundle.tar.gz` (**2,608 entries**), re-imported into the sim.
- **Verified:** all 8 referenced media URLs + both favicon links return **200**.
- **Guardrail added:** see LOG-3 step 13 — a media-count check now runs in the gate.

### BUG-2 (Medium) — Deployment step fragility: cross-filesystem `mv` can silently drop files
- **Found:** moving `files/uploads/*` into `wp-content/uploads/` with `mv … 2>/dev/null`
  *dropped part of the media and the target ended up near-empty* when `/tmp` (overlay)
  and `/var/www/html` (volume) are different filesystems — the error was swallowed, and
  WP then canonical-redirected (301/404) the missing static paths.
- **Impact:** on any host where extraction lands on another filesystem than the web root the
  same silent drop is possible; on prod it depends on where the panel extracts archives.
- **Fix (applied):** RUNBOOK step A now uses `cp -a` (recursive preserved copy) *and* a
  verification line (mu-plugins file count, media file count with non-zero sizes) whose
  failure aborts with a clear instruction to re-sync, instead of `mv` + silent errors.

### BUG-3 (Low, sim-only) — 301 on `/` when testing through the offline sim
- **Found:** with the dump's production `siteurl/home` (`https://onemoretree.eu`) and no TLS
  locally, only the front page canonical-redirects away from the sim; on real production
  `https` is served directly, so this is not reproducible there.
- **Fix (test harness only):** for the finished, fully-honoured offline run the sim's copy of
  `siteurl/home` was set to the `http://` variant. This stays in the sim DB only — the dump on
  disk still carries the https URLs; no migration artifact was changed for this.

### (Note, not a bug) — `wp db import` needs a mysql client binary
- The WordPress image has no `mysql` client, so `wp db import` fails there; the import must
  run where mysql exists (server SSH: host mysql; TransIP: phpMyAdmin/Adminer import path).
- This matches existing docs (RUNBOOK Method B / Adminer Method C); no change needed.

---

## Verification gate — final state after fixes (all from the sim)

| # | Gate item | Result |
|---|---|---|
| 1 | Homepage renders hero `h1` "One more tree. / One more living forest." | ✅ 200 |
| 2 | All 10 content pages + `/privacy-policy/` | ✅ all 200 |
| 3 | Duplicate `-2` slugs (`/about-2/`, `/faq-2/`, `/home-2/`) | ✅ all 404 |
| 4 | Form nonce (`_omt_nonce`) on `/contact/` | ✅ present (2 markers) |
| 5 | Favicon + `<meta name="description">` + `og:` tags | ✅ 8 markers present |
| 6 | Sitemap has no `-2` slugs | ✅ clean |
| 7 | Media: all attachment URLs incl. favicon set | ✅ 8/8 → 200 |
| 8 | Freshness policy headers (`omt-cache-headers.php`) | ✅ `s-maxage=120, stale-while-revalidate=300` on HTML |
| 9 | Submissions CPT (`omt_lead`) exists; newsletter admin callable | ✅ yes / yes |
| 10 | Settings → Email (SMTP) page renders; shows the expected "No SMTP host is configured" notice until creds are saved | ✅ yes / shown |
| 11 | Users after import | ✅ exactly one admin (`Natnael`); no `admin`/`admin123` accounts |

**Statistic summary:** 11/11 gate items pass after BUG-1/BUG-2 fixes; 3 findings logged (2 real,
1 sim-only), all resolved and re-verified in the sim.

## Follow-ups (still owner-dependent, tracked in SECURITY_FIXES.md / DEPLOYMENT_GUIDE.md)

- Real SMTP credentials must be entered under **Settings → Email (SMTP)** on production
  after `OMT_SMTP_KEY` is added to the prod `wp-config.php`; the gate above only proves the
  page and transport wiring work with no credentials yet.
- Server-side items (security headers, DB password rotation, login throttling) stay on the
  production checklist.
