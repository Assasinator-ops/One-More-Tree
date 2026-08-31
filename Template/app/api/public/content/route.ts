// Public endpoint for published content.
// This serves the same data that the admin panel edits, but without requiring authentication.
// This allows non-logged-in users to see the actual admin data instead of mock data.
//
// Security:
// - Rate limited: 100 requests per 15 minutes per IP
// - Read-only (no POST/PUT/DELETE)
// - No sensitive data (admin users, contact submissions, etc.)
// - CORS headers allow same-site requests only

import { NextResponse } from 'next/server';
import { checkRateLimit, getRateLimitStatus } from '@/lib/rateLimit';
import {
  getPublishedContent,
  getPublishedHomeCounters,
  getPublishedImpactKpis
} from '@/server/publicContent';
import {
  getDisclaimer,
  getDonationTiers,
  getFAQ,
  getLiveMap,
  getMaintenanceEnabled,
  getModules,
  getProjects,
  getSocial,
  getStories,
  getTeam
} from '@/server/adminData';

// Rate limit config
const RATE_LIMIT_WINDOW = 15 * 60 * 1000; // 15 minutes
const RATE_LIMIT_MAX = 100; // requests per window

function getClientIp(req: Request): string {
  const forwarded = req.headers.get('x-forwarded-for');
  const ip = forwarded ? forwarded.split(',')[0].trim() : 'unknown';
  return ip;
}

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

  const { searchParams } = new URL(req.url);
  const key = searchParams.get('key');

  // Return specific data based on key. All public, no auth required.
  if (key === 'home_counters') {
    return NextResponse.json({ ok: true, value: await getPublishedHomeCounters() });
  }

  if (key === 'impact_kpis') {
    return NextResponse.json({ ok: true, value: await getPublishedImpactKpis() });
  }

  if (key === 'stories') {
    return NextResponse.json({ ok: true, value: getStories() });
  }

  if (key === 'team') {
    return NextResponse.json({ ok: true, value: getTeam() });
  }

  if (key === 'live_map') {
    return NextResponse.json({ ok: true, value: getLiveMap() });
  }

  if (key === 'faq') {
    return NextResponse.json({ ok: true, value: getFAQ() });
  }

  if (key === 'donation_tiers') {
    return NextResponse.json({ ok: true, value: getDonationTiers() });
  }

  if (key === 'projects') {
    return NextResponse.json({ ok: true, value: getProjects() });
  }

  if (key === 'disclaimer') {
    return NextResponse.json({ ok: true, value: getDisclaimer() });
  }

  if (key === 'social') {
    return NextResponse.json({ ok: true, value: getSocial() });
  }

  if (key === 'modules') {
    return NextResponse.json({ ok: true, value: getModules() });
  }

  if (key === 'maintenance') {
    return NextResponse.json({ ok: true, value: getMaintenanceEnabled() });
  }

  if (key === 'full_content') {
    return NextResponse.json({ ok: true, value: await getPublishedContent() });
  }

  // Default: return full content
  return NextResponse.json({ ok: true, value: await getPublishedContent() });
}
