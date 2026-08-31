import { NextRequest, NextResponse } from 'next/server';
import { getCurrentAdmin } from '@/server/adminAuth';
import { getMaintenanceEnabled, setMaintenanceEnabled } from '@/server/adminData';

const MAINTENANCE_COOKIE = 'site.maintenance';

export async function GET() {
  return NextResponse.json({ enabled: getMaintenanceEnabled() });
}

export async function POST(req: NextRequest) {
  const admin = await getCurrentAdmin();
  if (!admin) {
    return NextResponse.json({ ok: false, message: 'Unauthorized' }, { status: 401 });
  }
  if (admin.must_reset_password === 1) {
    return NextResponse.json({ ok: false, message: 'Password reset required' }, { status: 403 });
  }
  const body = (await req.json().catch(() => ({}))) as { enabled?: boolean };
  const enabled = Boolean(body.enabled);
  setMaintenanceEnabled(enabled);

  const res = NextResponse.json({ ok: true, enabled });
  // Mirror the value into a cookie so the middleware (Edge runtime) can
  // gate requests without hitting the DB.
  res.cookies.set(MAINTENANCE_COOKIE, enabled ? '1' : '0', {
    httpOnly: false,
    sameSite: 'lax',
    path: '/',
    maxAge: 60 * 60 * 24 * 365
  });
  return res;
}
