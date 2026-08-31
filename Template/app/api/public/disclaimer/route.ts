import { NextResponse } from 'next/server';
import { checkRateLimit, getRateLimitStatus } from '@/lib/rateLimit';
import { getDisclaimer } from '@/server/adminData';

// Rate limit config
const RATE_LIMIT_WINDOW = 15 * 60 * 1000; // 15 minutes
const RATE_LIMIT_MAX = 100; // requests per window

function getClientIp(req: Request): string {
  const forwarded = req.headers.get('x-forwarded-for');
  const ip = forwarded ? forwarded.split(',')[0].trim() : 'unknown';
  return ip;
}

// Public endpoint: returns the disclaimer ribbon config.
// The ribbon on the public site reads this once on mount.
// No authentication required. Rate limited to prevent abuse.
export async function GET(req: Request) {
  // Rate limiting
  const clientIp = getClientIp(req);
  if (!checkRateLimit(clientIp, RATE_LIMIT_MAX, RATE_LIMIT_WINDOW)) {
    const status = getRateLimitStatus(clientIp, RATE_LIMIT_MAX, RATE_LIMIT_WINDOW);
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

  return NextResponse.json({ ok: true, value: getDisclaimer() });
}