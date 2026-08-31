# TODO

- [x] Migrate remaining localStorage-based admin modules to SQLite-backed adminApi reads/writes.
- [x] Remove all `window.localStorage` usage from `src/admin/modules/**`.
- [x] Replace `src/lib/adminPublicMirrors.ts` to eliminate browser localStorage usage.
- [ ] Remove or rename remaining *comment-only* mentions of localStorage if required by your strict grep rule.
- [ ] Run `npm run lint` and `npm run build`.

