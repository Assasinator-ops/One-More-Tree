// Client-side helper that talks to the public API.
// This is similar to adminApi but doesn't require authentication.
// All public-facing components should use this to fetch admin-edited data.

const cache = new Map<string, unknown>();

async function readKey<T>(key: string): Promise<T | null> {
  if (cache.has(key)) return cache.get(key) as T;
  const res = await fetch(`/api/public/content?key=${encodeURIComponent(key)}`, { cache: 'no-store' });
  const json = await res.json();
  if (!res.ok || !json.ok) return null;
  cache.set(key, json.value);
  return json.value as T;
}

export const publicApi = {
  read: readKey,
  invalidate(key?: string) {
    if (key) cache.delete(key);
    else cache.clear();
  }
};

// Typed helpers per module (matching AdminKeys)
export const PublicKeys = {
  HomeCounters: 'home_counters',
  ImpactKpis: 'impact_kpis',
  Stories: 'stories',
  Team: 'team',
  LiveMap: 'live_map',
  FAQ: 'faq',
  DonationTiers: 'donation_tiers',
  FullContent: 'full_content',
  Disclaimer: 'disclaimer',
  Social: 'social',
  Modules: 'modules',
  Maintenance: 'maintenance',
  Projects: 'projects',
  Hero: 'hero',
  Footer: 'footer'
} as const;
