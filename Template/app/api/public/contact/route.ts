import { NextRequest, NextResponse } from 'next/server';
import { appendContactSubmission, isModuleEnabled } from '@/server/adminData';
import { revalidateTag } from 'next/cache';

export async function POST(req: NextRequest) {
  if (!isModuleEnabled('contact')) {
    return NextResponse.json({ ok: false, message: 'Contact is currently disabled.' }, { status: 403 });
  }

  const body = (await req.json().catch(() => ({}))) as {
    name?: string;
    email?: string;
    message?: string;
  };

  const name = (body.name ?? '').trim() || 'Anonymous';
  const email = (body.email ?? '').trim() || 'unknown@example.com';
  const message = (body.message ?? '').trim();
  if (!message) {
    return NextResponse.json({ ok: false, message: 'Message is required.' }, { status: 400 });
  }

  appendContactSubmission({
    id: `contact-${Date.now()}`,
    name,
    email,
    message
  });

  revalidateTag('content', 'max');
  return NextResponse.json({ ok: true });
}
