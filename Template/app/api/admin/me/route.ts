import { NextResponse } from 'next/server';
import { getCurrentAdmin } from '@/server/adminAuth';

export async function GET() {
  const admin = await getCurrentAdmin();
  if (!admin) {
    return NextResponse.json({ authenticated: false }, { status: 200 });
  }
  return NextResponse.json({
    authenticated: true,
    username: admin.username,
    mustResetPassword: admin.must_reset_password === 1
  });
}
