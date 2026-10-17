# One More Tree — Security Audit & Fix Plan

Date: 2026-10-17
Scope: local dev box (Docker), this repository, https://onemoretree.eu (TransIP, nginx)
Reads with: [SITE_AUDIT.md](SITE_AUDIT.md) for the broader site quality audit.

Verification method used: live HTTP probes of 13 sensitive paths on production,
file-level secret scan across the repo, code review of auth paths
(`create_admin.php`, `Template/src/server/adminAuth.ts`,
`wp-content/themes/one-more-tree/inc/admin-api.php`, `wp-content/mu-plugins/*`),
WordPress config review (`wp-config-sample.php`, `.gitignore`), and DB checks via the
local Docker container.

---

## Verdict Overview

| # | Severity | Finding | Status on prod today |
|---|---|---|---|
| 1 | Critical | Production DB credentials circulated in chat | Not a repo leak (scan clean), but still a secret |
| 2 | Critical | Default admin credentials committed in bootstrap code (`admin123`) | Not deployed yet (`404` on prod), but in code |
| 3 | High | Missing security headers (HSTS, XCTO, XFO, Referrer-Policy, CSP) | Confirmed missing |
| 4 | High | `wp-login.php` reachable with no rate limiting | Confirmed — no throttle; Wordfence inactive |
| 5 | High | Legacy code in web root (5 unused themes, demo scripts, old Next.js template) | Prod: `wp-content/themes/` dir reachable |
| 6 | Medium | Repo hygiene: `.gitignore` does not exclude `tmp/`, `prod/`, `wp-config.php`, `*.sql`, `*.env` | Local only today; will leak on first push if not fixed |
| 7 | Medium | Local Docker publishes to `0.0.0.0:8888` with default creds (`wordpress:wordpress`, root `rootpassword`) | Local only |
| 8 | Info | Good baseline already present: xmlrpc=403, REST users=401, `readme.html`=404, uploads listing=403, no `error_log`/`debug.log` excerpt, secrets not committed | Keep |
| 9 | Info | Application-layer auth of the mu-plugins is solid (CSRF at render, honeypot+time trap, rate limit on newsletter toggle, hashed IPs) | Keep |

What's already done (nothing to change): per-source rate limits and hashed IPs inside
`omt-newsletters.php`, `omt-forms.php` CSRF design, `wp-config-sample.php` correctly not
containing real credentials, prod script-blocking (`.php` upload execution in uploads).

---

## Findings and How to Fix Each

### 1. Critical — Production DB credentials were shared in chat
The TransIP DB password was pasted into the conversation and is reflected in working
files under `tmp/`. Repo scan is clean (no commit contains it) but it must be treated as
compromised.

**Action:**
1. Finish all remaining DB work first.
2. In the TransIP panel, change the DB password for user `onempc_btpbhl64`.
3. Replace it in the live `wp-config.php` on the server (FTP/SSH there).
4. Purge any cached PHP ops (`wp cache flush`; TransIP TTL for nginx HTML cache is
   around 58 minutes, so re-check after that window).

### 2. Critical — Default admin credentials in bootstrap code
Two places create an admin account with password `admin123` when the DB is empty:

* `create_admin.php` (repo root) — inserts `admin` with `admin@onemoretree.eu`.
* `Template/src/server/adminAuth.ts` — `DEFAULT_USERNAME=admin`,
  `DEFAULT_PASSWORD=admin123`, then asks for a reset (`must_reset_password=1`).

Neither is reachable on production today, but any future deployment of the Next.js
template or a stray upload of `create_admin.php` into the web root would hand over the
site. `admin123` is also on every common wordlist.

**Action:**
1. Delete `create_admin.php` from the repo (it was a one-off local dev helper).
2. In `Template/src/server/adminAuth.ts` remove `DEFAULT_PASSWORD`; require an env var
   (`ADMIN_INITIAL_PASSWORD`) and fail closed with a clear error if it is not set.
3. Never ship either bootstrap path in `wp-content` or the public web root.

### 3. High — Missing security headers on production
Confirmed live: no `Strict-Transport-Security`, no `Content-Security-Policy`,
no `X-Content-Type-Options`, no `X-Frame-Options`, no `Referrer-Policy`.

**Action (TransIP/web server config — add in the vhost or server block):**

```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
# Start permissive, tighten in a second pass:
add_header Content-Security-Policy "default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; frame-ancestors 'self';" always;
```

Notes:
- `style-src 'unsafe-inline'` is required because WordPress emits inline style attributes
  for block alignment.
- Re-check the site + admin after; if something breaks, drop `script-src` first, then
  tighten back progressively.
- Verify afterwards with `curl -sI https://onemoretree.eu | grep -i security`.

### 4. High — Admin login has no throttle
`wp-login.php` answers 200 with no protection, and Wordfence is installed locally but
not in the active plugin list on production.

**Pick one (in order of preference):**
1. Activate Wordfence on production via WP-CLI:
   `wp plugin activate wordfence --path=/home/user/domains/onemoretree.eu/public_html/` and
   enable its brute-force protection module, or
2. Use a server-side rate limit (TransIP nginx):

```nginx
location = /wp-login.php {
    limit_req zone=login burst=3 nodelay;
    include fastcgi_params;
    # fastcgi_pass … as in the existing PHP location block
}
# in http {} zone
limit_req_zone $binary_remote_addr zone=login:10m rate=6r/m;
```

Whichever route, also disable editable file uploads (`DISALLOW_FILEEDIT`), see #7.

### 5. High — Legacy / dead code in the web root
Five inactive themes (`colibri-wp`, `denovr-lite`, `webrand-lite`, `one-more-tree`,
twentytwenty*), the old Next.js `Template/`, and demo scripts (`inject-demo.php`,
`create_admin.php`) increase the attack surface and add patch noise. On production the
`wp-content/themes/` directory is enumerable.

**Action:**
1. On production, remove any theme that isn't `lennox` (after confirming via WordPress
   admin → Themes that nothing uses them).
2. Same for `prod/wp-content/themes/*` locally — delete; it's a stale copy.
3. Delete the legacy `Template/` Next.js app from the repo if it's not planned for
   launch (or archive it in a separate private repo).
4. Keep only `wp-content/themes/lennox/` and `wp-content/mu-plugins/` as authoritative
   code.

### 6. Medium — Repository hygiene can leak secrets on the next push
`.gitignore` currently covers `node_modules/`, `.env`, `*.log`, `dist/`, `build/`,
`mysql-data/`, `wp-content/uploads/`. It does **not** exclude `tmp/`, `prod/`,
`wp-config.php`, `*.sql`, or `.freebuff/`. Anything containing the live DB host/user
(`tmp/fix-production-sql.sql`) or the real `wp-config.php` would be one
`git add -A` away from a commit.

**Action (applied in this repo as part of this fix):** extend `.gitignore` with
`tmp/`, `prod/`, `wp-config.php`, `*.sql`, `.freebuff/` and add a
`wp-config-secret.php`-style pattern if needed. Sanitize committed helper scripts so
they only read credentials from environment variables.

### 7. Medium — Local Docker setup is dev-only, not hardened
`docker-compose.yml` publishes `0.0.0.0:8888:80`, uses DB user/password
`wordpress:wordpress`, and gives MySQL root a weak `rootpassword`. Fine for localhost,
risky if a laptop is on shared Wi-Fi.

**Action:** tighten the binding and password complexity:

```yaml
    ports:
      - "127.0.0.1:8888:80"
```

and move DB credentials into a local-only `.env` (already gitignored) instead of hard
coding them in `docker-compose.yml`. Optionally drop the MySQL root password entirely
by using a socket-internal config with `MYSQL_ROOT_HOST=localhost`.

### 8. Informational — strength already in place
* `xmlrpc.php` → **403** on prod (good — no pingback/brute-force amplification).
* `wp-json/wp/v2/users` → **401** (good — no username enumeration).
* `wp-content/uploads/` → **403** with no directory listing (good — no browsing of
  media metadata).
* No stray `error_log`, `debug.log`, or `wp-config.php.bak` reachable.
* `wp-config-sample.php` uses `password_here` (no real secrets tracked).
* `omt-forms.php` / `omt-newsletters.php`: render-time CSRF nonce, honeypot + minimum
  submit time, per-IP rate limits, hashed IP storage, wp_safe_redirect target
  validation, and a kses whitelist rather than blanket `unfiltered_html`. Nothing to
  change.

---

## What to do next, in order

1. Apply the `.gitignore` + `Template/adminAuth.ts` env-var fix locally, commit, push.
2. Delete `create_admin.php`, `inject-demo.php`, and the `prod/` working copies.
3. On production: activate login protection (Wordfence or the nginx rate limit in #4).
4. On production: add the security headers from #3 and verify with `curl -I`.
5. Rotate the production DB password (after all pending DB work is done) and update the
   live `wp-config.php` (see #1).
6. Remove unused themes from production (see #5).
7. Re-verify the whole site (page render, admin login, forms, uploads) after the
   production-side changes and record results here.

---

## Post-Fix Checklist (status 2026-10-17, after final sweep)

- [x] `.gitignore` excludes secrets: `tmp/`, `prod/`, `wp-config.php`, `*.sql`,
      `*.tar.gz`, `.freebuff/`, `wp-content/wflogs/`
- [x] `create_admin.php` deleted from the working tree (removed in commit `4fb0d89`);
      `inject-demo.php` is **not** deleted yet — it is a local dev helper; remove it
      before any production file sync (or keep it excluded from the deploy bundle)
- [ ] Default bootstrap admin password still present in
      `Template/src/server/adminAuth.ts` — the whole `Template/` legacy app is
      still tracked; archive it to a separate repo or delete it
- [x] Wordfence activated in the source site and included in the deploy bundle —
      prod activation happens automatically on import (the `active_plugins` option
      carries it); brute-force tuning still needed on the live instance
- [ ] Login throttling verified on prod (after import: check Wordfence Login Security)
- [ ] Security headers present on prod (`curl -I` shows HSTS/xcto/XFO) — server-side,
      TransIP nginx config required
- [ ] Production DB password rotated and only referenced from live `wp-config.php`
- [x] No unused themes in the source DB or the deploy bundle; only `lennox` ships
      (all six inactive legacy themes deleted)
- [x] Local Docker binds to `127.0.0.1:8888` (creds still inline in
      `docker-compose.yml` — acceptable for a local-only bind; move to `.env` if
      the stack ever moves to a shared host)
- [x] wp-config hardening constants added locally and prescribed for prod in
      `migration/DEPLOYMENT_GUIDE.md` (`DISALLOW_FILE_EDIT`, `WP_POST_REVISIONS 5`,
      `EMPTY_TRASH_DAYS 7`)
- [x] Known-password `admin` user removed from the source DB; the migration dump
      ships a single administrator account
