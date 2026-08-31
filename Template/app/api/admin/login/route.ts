import { NextRequest, NextResponse } from 'next/server';
import {
  authenticateAdmin,
  setSessionCookie
} from '@/server/adminAuth';

export async function POST(req: NextRequest) {
  const body = (await req.json().catch(() => ({}))) as { username?: string; password?: string };
  const username = (body.username ?? '').trim();
  const password = body.password ?? '';
  if (!username || !password) {
    return NextResponse.json({ ok: false, message: 'Username and password are required.' }, { status: 400 });
  }

  const admin = await authenticateAdmin(username, password);
  if (!admin) {
    return NextResponse.json({ ok: false, message: 'Invalid credentials.' }, { status: 401 });
  }

  await setSessionCookie(admin.username);
  return NextResponse.json({
    ok: true,
    mustResetPassword: admin.must_reset_password === 1,
    username: admin.username
  });
}
