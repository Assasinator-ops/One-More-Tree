import { NextRequest, NextResponse } from 'next/server';
import { appendNewsletterSubscriber, isModuleEnabled } from '@/server/adminData';
import { revalidateTag } from 'next/cache';
import { checkRateLimit, getRateLimitStatus } from '@/lib/rateLimit';

// Super-small server friendly rate limit.
// Limits POST attempts per IP.
const RATE_LIMIT_WINDOW_MS = 15 * 60 * 1000; // 15 minutes
const RATE_LIMIT_MAX = 30; // requests per window

function getClientIp(req: Request): string {
  const forwarded = req.headers.get('x-forwarded-for');
  return forwarded ? forwarded.split(',')[0].trim() : 'unknown';
}

export async function POST(req: NextRequest) {
  if (!isModuleEnabled('newsletter')) {
    return NextResponse.json(
      { ok: false, message: 'Newsletter is currently disabled.' },
      { status: 403 }
    );
  }

  const clientIp = getClientIp(req);
  if (!checkRateLimit(clientIp, RATE_LIMIT_MAX, RATE_LIMIT_WINDOW_MS)) {
    const status = getRateLimitStatus(clientIp, RATE_LIMIT_MAX, RATE_LIMIT_WINDOW_MS);
    return NextResponse.json(
      { ok: false, message: 'Rate limit exceeded' },
      {
        status: 429,
        headers: {
          'X-RateLimit-Limit': String(RATE_LIMIT_MAX),
          'X-RateLimit-Remaining': String(status.remaining),
          'X-RateLimit-Reset': String(Math.floor(status.resetAt / 1000))
        }
      }
    );
  }

  const body = (await req.json().catch(() => ({}))) as { email?: string };
  const email = (body.email ?? '').trim().toLowerCase();
  if (!email || !email.includes('@')) {
    return NextResponse.json(
      { ok: false, message: 'A valid email is required.' },
      { status: 400 }
    );
  }

  appendNewsletterSubscriber(email);
  revalidateTag('content', 'max');
  return NextResponse.json({ ok: true });
}

