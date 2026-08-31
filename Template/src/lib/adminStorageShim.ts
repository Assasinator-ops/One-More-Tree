// Tiny in-memory + fetch shim that imitates the `localStorage.getItem` /
// `localStorage.setItem` API used by the existing admin modules.
//
// Each call to `getItem(key)` first returns a cached value, otherwise it
// fetches from the server. `setItem(key, value)` POSTs to the server and
// updates the cache. This lets us keep all the existing admin modules
// (which were written for localStorage) functioning without rewriting each
// one.

import { adminApi } from './adminApi';

const memory = new Map<string, string>();
const pendingFetches = new Map<string, Promise<string | null>>();

async function fetchFromServer(prefix: string): Promise<string | null> {
  const res = await fetch(`/api/admin/data?key=${encodeURIComponent(prefix)}`, { cache: 'no-store' });
  const json = await res.json();
  if (!res.ok || !json.ok) return null;
  return JSON.stringify(json.value);
}

export const serverStorage = {
  async getItem(key: string): Promise<string | null> {
    if (memory.has(key)) return memory.get(key)!;
    if (pendingFetches.has(key)) return pendingFetches.get(key)!;

    // Map storage key -> server key. We do this by looking at the prefix.
    const mapping: Record<string, string> = {
      'admin.homeCounters.v1': 'home_counters',
      'admin.moduleStatus.v1': 'modules',
      'admin.stories.v1': 'stories',
      'admin.team.v1': 'team',
      'admin.liveMap.v1': 'live_map',
      'admin.projects.v1': 'projects',
      'admin.impactKpis.v1': 'impact_kpis',
      'admin.donationTiers.v1': 'donation_tiers',
      'admin.donationOrders.v1': 'donation_orders',
      'admin.faq.v1': 'faq',
      'admin.contact.v1': 'contact_submissions',
      'admin.newsletter.v1': 'newsletter_subscribers',
      'admin.social.v1': 'social',
      'site.maintenance.v1': 'maintenance'
    };
    const prefix = mapping[key];
    if (!prefix) {
      // Unknown key: fall back to local memory only.
      return memory.get(key) ?? null;
    }
    const promise = fetchFromServer(prefix);
    pendingFetches.set(key, promise);
    try {
      const raw = await promise;
      if (raw !== null) memory.set(key, raw);
      return raw;
    } finally {
      pendingFetches.delete(key);
    }
  },

  async setItem(key: string, value: string): Promise<void> {
    memory.set(key, value);
    const mapping: Record<string, string> = {
      'admin.homeCounters.v1': 'home_counters',
      'admin.moduleStatus.v1': 'modules',
      'admin.stories.v1': 'stories',
      'admin.team.v1': 'team',
      'admin.liveMap.v1': 'live_map',
      'admin.projects.v1': 'projects',
      'admin.impactKpis.v1': 'impact_kpis',
      'admin.donationTiers.v1': 'donation_tiers',
      'admin.donationOrders.v1': 'donation_orders',
      'admin.faq.v1': 'faq',
      'admin.contact.v1': 'contact_submissions',
      'admin.newsletter.v1': 'newsletter_subscribers',
      'admin.social.v1': 'social',
      'site.maintenance.v1': 'maintenance'
    };
    const prefix = mapping[key];
    if (!prefix) return;
    try {
      const parsed = JSON.parse(value);
      // For modules, the localStorage value is an array; the server stores
      // modules as a Record<string, boolean>. Normalize.
      if (key === 'admin.moduleStatus.v1') {
        const arr = parsed as Array<{ key: string; enabled: boolean }>;
        const obj = Object.fromEntries(arr.map((m) => [m.key, m.enabled]));
        await adminApi.write(prefix, obj);
      } else {
        await adminApi.write(prefix, parsed);
      }
    } catch {
      // ignore
    }
  },

  clear(): void {
    memory.clear();
  }
};
