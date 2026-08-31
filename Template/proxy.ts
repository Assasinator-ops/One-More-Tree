// Middleware runs before every page request (and matching assets).
//
// Responsibilities:
//  1. Maintenance mode: if enabled (cookie `site.maintenance=1`) and the
//     visitor is NOT an admin, redirect every page request to /maintenance.
//  2. Admin protection: visitors without a valid admin session trying to
//     reach /admin/* are redirected to /admin/login. Login + reset-password
//     are always reachable so admins can sign in while maintenance is on.
//  3. God Mode preview: an authenticated admin visiting /maintenance can
//     append ?god=1 to bypass the maintenance redirect for that single
//     request. This is what the "Proceed as God Mode" button uses.
//
// The middleware uses cookies only (Edge runtime cannot open SQLite
// directly). The DB is the source of truth; cookies are mirrored whenever
// an admin toggles a setting.

import { NextRequest, NextResponse } from 'next/server';
import {
  MAINTENANCE_EXEMPT_EXACT,
  MAINTENANCE_EXEMPT_PREFIXES,
  isMaintenanceExempt
} from '@/lib/maintenanceExempt';

const DEBUG_MAINTENANCE = process.env.DEBUG_MAINTENANCE === '1';


const ADMIN_SESSION_COOKIE = 'admin.session';
const MAINTENANCE_COOKIE = 'site.maintenance';

function startsWithAny(path: string, prefixes: readonly string[]) {
  return prefixes.some((p) => path === p || path.startsWith(`${p}/`));
}

export function proxy(req: NextRequest) {
  // console.log("Begin!");
  const { pathname, searchParams } = req.nextUrl;
  const requestHeaders = new Headers(req.headers);
  // 2. Set a custom header for the pathname
  requestHeaders.set('x-current-path', pathname);
  // console.log(pathname);
  // console.log(NextResponse.next());
  // console.log('[maintenance][proxy]', {
  //   pathname,
  //   search: req.nextUrl.search,
  //   exactExempt: MAINTENANCE_EXEMPT_EXACT.has(pathname),
  //   prefixExempt: startsWithAny(pathname, MAINTENANCE_EXEMPT_PREFIXES),
  //   helperExempt: isMaintenanceExempt(pathname)
  // });

  if (
    MAINTENANCE_EXEMPT_EXACT.has(pathname) || 
    startsWithAny(pathname, MAINTENANCE_EXEMPT_PREFIXES) ||
    isMaintenanceExempt(pathname)
  ) {
    return NextResponse.next({
      request: {
        headers: requestHeaders,
      },
    });
  }

  const isAdmin = Boolean(req.cookies.get(ADMIN_SESSION_COOKIE)?.value);
  const isMaintenanceOn = req.cookies.get(MAINTENANCE_COOKIE)?.value === '1';

  // Admin gate: visitors without a session hitting /admin are redirected
  // to /admin/login. /admin/login + /admin/reset-password are exempted
  // above so admins can sign in.
  if (pathname === '/admin' || pathname.startsWith('/admin/')) {
    if (!isAdmin) {
      const url = req.nextUrl.clone();
      url.pathname = '/admin/login';
      url.search = '';
      return NextResponse.redirect(url);
    }
  }

  // Maintenance gate: if enabled and the requester is not an admin and not
  // explicitly using ?god=1, redirect to /maintenance.
  if (isMaintenanceOn && !isAdmin && !searchParams.has('god')) {
    const url = req.nextUrl.clone();
    url.pathname = '/maintenance';
    url.search = '';
    return NextResponse.redirect(url);
  }
  return NextResponse.next({
    request: {
      headers: requestHeaders,
    },
  });
}

// Match every page route except Next.js internals and static assets.
export const config = {
  matcher: [
    /*
     * Match all request paths except for the ones starting with:
     * - _next/static (static files)
     * - _next/image (image optimization files)
     * - favicon.ico (favicon file)
     * - public files with an extension.
     */
    '/((?!_next/static|_next/image|favicon.ico|.*\\..*).*)'
  ]
};

