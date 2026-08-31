// Shared maintenance-exemption rules used by both the Edge `proxy.ts` and the
// root `app/layout.tsx`. The two gates MUST agree on the same list of
// always-reachable paths, otherwise the proxy will let `/admin/login`
// through and the layout will immediately replace it with the maintenance
// screen (or vice versa).
//
// Rule of thumb: if a request *can* legitimately arrive here while
// maintenance is on (admin sign-in, auth API, public APIs, static assets),
// list it here. Public-facing marketing pages should NOT be in this list.
//
// When called with an empty/unknown pathname, the helper returns `false` —
// i.e. "treat as blocked". This preserves the original behaviour where
// anonymous visitors see the maintenance screen even if the server-side
// path detection is unreliable. `/admin/login` and friends are still
// reachable because `proxy.ts` (which DOES know the real URL) calls this
// helper with the actual request path.

export const MAINTENANCE_EXEMPT_PREFIXES = [
  '/admin/login',
  '/admin/reset-password',
  '/api/admin',
  '/api/public',
  '/maintenance',
  '/_next',
  '/favicon',
  '/apple-touch-icon',
  '/images',
  '/assets',
  '/robots.txt',
  '/sitemap'
] as const;

export const MAINTENANCE_EXEMPT_EXACT = new Set<string>(['/health']);

export function isMaintenanceExempt(pathname: string | null | undefined): boolean {
  const path = (pathname ?? '').trim();
  if (!path) return false;
  if (MAINTENANCE_EXEMPT_EXACT.has(path)) return true;
  return MAINTENANCE_EXEMPT_PREFIXES.some(
    (p) => path === p || path.startsWith(`${p}/`) || path.startsWith(p)
  );
}
