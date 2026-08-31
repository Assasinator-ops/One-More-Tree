// Client-side helper that talks to the admin API.
//
// All admin modules use this for reads + writes. The DB is the source of
// truth on the server; this file just keeps a small in-memory cache so the
// UI feels instant when switching between admin tabs.
//
// Notes:
// - We intentionally keep the function names similar to the old localStorage
//   helpers so we can update admin modules with minimal code changes.
// - We do not modify any module's UI here — only their data layer.

const cache = new Map<string, unknown>();

async function readKey<T>(key: string): Promise<T | null> {
  if (cache.has(key)) return cache.get(key) as T;
  const res = await fetch(`/api/admin/data?key=${encodeURIComponent(key)}`, { cache: 'no-store' });
  const json = await res.json();
  if (!res.ok || !json.ok) return null;
  cache.set(key, json.value);
  return json.value as T;
}

async function writeKey(key: string, value: unknown) {
  cache.set(key, value);
  const res = await fetch('/api/admin/data', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key, value })
  });
  const json = await res.json();
  if (!res.ok || !json.ok) {
    throw new Error(json.message ?? 'Save failed');
  }
}

export const adminApi = {
  read: readKey,
  write: writeKey,
  invalidate(key?: string) {
    if (key) cache.delete(key);
    else cache.clear();
  }
};

// Typed helpers per module.
export const AdminKeys = {
  HomeCounters: 'home_counters',
  ImpactKpis: 'impact_kpis',
  Stories: 'stories',
  Team: 'team',
  LiveMap: 'live_map',
  Projects: 'projects',
  Faq: 'faq',
  DonationTiers: 'donation_tiers',
  DonationOrders: 'donation_orders',
  Social: 'social',
  Modules: 'modules',
  Maintenance: 'maintenance',
  ContactSubmissions: 'contact_submissions',
  NewsletterSubscribers: 'newsletter_subscribers',
  AdminUsers: 'admin_users'
} as const;
