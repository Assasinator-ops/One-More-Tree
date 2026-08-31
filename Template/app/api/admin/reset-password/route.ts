import { NextRequest, NextResponse } from 'next/server';
import { changeAdminPassword, getCurrentAdmin } from '@/server/adminAuth';

export async function POST(req: NextRequest) {
  const admin = await getCurrentAdmin();
  if (!admin) {
    return NextResponse.json({ ok: false, message: 'Not signed in.' }, { status: 401 });
  }

  const body = (await req.json().catch(() => ({}))) as {
    password?: string;
    confirmPassword?: string;
  };
  const password = body.password ?? '';
  const confirmPassword = body.confirmPassword ?? '';

  if (password.length < 8) {
    return NextResponse.json(
      { ok: false, message: 'Password must be at least 8 characters.' },
      { status: 400 }
    );
  }
  if (password !== confirmPassword) {
    return NextResponse.json(
      { ok: false, message: 'Passwords do not match.' },
      { status: 400 }
    );
  }

  await changeAdminPassword(admin.username, password);
  return NextResponse.json({ ok: true });
}
