// Update the serverStorage helper in each patched admin module to include
// the additional keys (admin.contact.submissions.v1, etc.).

import fs from 'node:fs/promises';
import path from 'node:path';

const ROOT = 'e:/Projects/NGO/src/admin/modules';

const FULL_MAP = {
  'admin.homeCounters.v1': 'home_counters',
  'admin.stories.v1': 'stories',
  'admin.team.v1': 'team',
  'admin.liveMap.v1': 'live_map',
  'admin.projects.v1': 'projects',
  'admin.impactKpis.v1': 'impact_kpis',
  'admin.donationTiers.v1': 'donation_tiers',
  'admin.donationOrders.v1': 'donation_orders',
  'admin.faq.v1': 'faq',
  'admin.contact.v1': 'contact_submissions',
  'admin.contact.submissions.v1': 'contact_submissions',
  'admin.newsletter.v1': 'newsletter_subscribers',
  'admin.newsletter.subscribers.v1': 'newsletter_subscribers',
  'admin.social.v1': 'social',
  'admin.donations.tiers.v1': 'donation_tiers',
  'admin.donations.orders.v1': 'donation_orders',
  'site.maintenance.v1': 'maintenance'
};

async function walk(dir, out = []) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  for (const entry of entries) {
    const p = path.join(dir, entry.name);
    if (entry.isDirectory()) await walk(p, out);
    else if (entry.name.endsWith('.tsx')) out.push(p);
  }
  return out;
}

for (const f of await walk(ROOT)) {
  const before = await fs.readFile(f, 'utf8');
  if (!before.includes('const serverStorage = {')) continue;
  const newHelper = `const serverStorage = {
  async getItem(key) {
    const mapping = ${JSON.stringify(FULL_MAP, null, 2)};
    const prefix = mapping[key];
    if (!prefix) return null;
    const v = await adminApi.read(prefix);
    return v === null || v === undefined ? null : JSON.stringify(v);
  },
  async setItem(key, value) {
    const mapping = ${JSON.stringify(FULL_MAP, null, 2)};
    const prefix = mapping[key];
    if (!prefix) return;
    try {
      const parsed = JSON.parse(value);
      if (key === 'site.maintenance.v1') {
        await adminApi.write(prefix, parsed === 'true' || parsed === true);
      } else {
        await adminApi.write(prefix, parsed);
      }
    } catch {
      // ignore
    }
  }
};

`;
  const after = before.replace(/const serverStorage = \{[\s\S]*?\};\n\n/, newHelper);
  if (after !== before) {
    await fs.writeFile(f, after, 'utf8');
    console.log(`updated ${path.relative(process.cwd(), f)}`);
  }
}
