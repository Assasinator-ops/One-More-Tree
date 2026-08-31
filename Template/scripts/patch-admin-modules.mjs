// Patch admin modules to use the new API instead of localStorage.
// Idempotent: re-running on already-patched files is a no-op.

import fs from 'node:fs/promises';
import path from 'node:path';

const ROOT = 'e:/Projects/NGO/src/admin/modules';

const SERVER_KEY_MAP = {
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

const HELPER = `
const serverStorage = {
  async getItem(key) {
    const mapping = ${JSON.stringify(SERVER_KEY_MAP, null, 2)};
    const prefix = mapping[key];
    if (!prefix) return null;
    const v = await adminApi.read(prefix);
    return v === null || v === undefined ? null : JSON.stringify(v);
  },
  async setItem(key, value) {
    const mapping = ${JSON.stringify(SERVER_KEY_MAP, null, 2)};
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

const IMPORTS_TO_ADD = `import { adminApi } from '@/lib/adminApi';\n`;

async function walk(dir, out = []) {
  const entries = await fs.readdir(dir, { withFileTypes: true });
  for (const entry of entries) {
    const p = path.join(dir, entry.name);
    if (entry.isDirectory()) await walk(p, out);
    else if (entry.name.endsWith('.tsx')) out.push(p);
  }
  return out;
}

function detectStorageKey(content) {
  const m = content.match(/STORAGE_KEY\s*=\s*['"]([^'"]+)['"]/);
  return m ? m[1] : null;
}

function patch(content) {
  // Skip files we don't touch.
  const hasStorage =
    content.includes('window.localStorage') ||
    /loadFromStorage|persistToStorage|persistModuleStatus|persistMaintenanceState|loadMaintenanceState/.test(content);
  if (!hasStorage) return content;

  // Add adminApi import if missing.
  if (!content.includes("from '@/lib/adminApi'") && !content.includes('from "@/lib/adminApi"')) {
    content = content.replace(/(^import[^\n]*\n)+/m, (m) => m + IMPORTS_TO_ADD);
  }

  // Replace window.localStorage access patterns.
  content = content.replace(
    /window\.localStorage\.getItem\(\s*STORAGE_KEY\s*\)/g,
    '(await serverStorage.getItem(STORAGE_KEY))'
  );
  content = content.replace(
    /window\.localStorage\.setItem\(\s*STORAGE_KEY\s*,/g,
    'await serverStorage.setItem(STORAGE_KEY,'
  );
  content = content.replace(
    /window\.localStorage\.getItem\(\s*['"]site\.maintenance\.v1['"]\s*\)/g,
    "(await serverStorage.getItem('site.maintenance.v1'))"
  );
  content = content.replace(
    /window\.localStorage\.setItem\(\s*['"]site\.maintenance\.v1['"]\s*,/g,
    "await serverStorage.setItem('site.maintenance.v1',"
  );
  content = content.replace(
    /window\.localStorage\.getItem\(\s*['"]dev\.adminDebug\.enabled['"]\s*\)/g,
    "null /* dev.adminDebug.enabled no longer used */"
  );
  content = content.replace(
    /window\.localStorage\.setItem\(\s*['"]dev\.adminDebug\.enabled['"]\s*,\s*[^)]*\)/g,
    '/* dev.adminDebug.enabled no longer used */'
  );

  // For functions that previously returned synchronously, ensure they're async.
  // We won't try to surgically add `async` to every helper — the build will
  // surface missing `await` and we'll fix specific cases as needed.

  // Inject serverStorage helper if not already present.
  if (!content.includes('const serverStorage =')) {
    content = content.replace(/export default function/, HELPER + 'export default function');
  }

  return content;
}

const files = await walk(ROOT);
for (const f of files) {
  const before = await fs.readFile(f, 'utf8');
  const storageKey = detectStorageKey(before);
  const after = patch(before);
  if (after !== before) {
    await fs.writeFile(f, after, 'utf8');
    console.log(`patched ${path.relative(process.cwd(), f)} (storage=${storageKey ?? 'n/a'})`);
  } else {
    console.log(`skip    ${path.relative(process.cwd(), f)}`);
  }
}
