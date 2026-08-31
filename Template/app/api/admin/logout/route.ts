import { NextResponse } from 'next/server';
import { clearSessionCookie } from '@/server/adminAuth';

export async function POST() {
  await clearSessionCookie();
  return NextResponse.json({ ok: true });
}
