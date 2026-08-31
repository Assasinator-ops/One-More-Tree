# Convert each admin module from localStorage to the server-backed API.
# Strategy:
#   - Replace `window.localStorage.getItem(KEY)` with `await loadFromServer(KEY)`
#   - Replace `window.localStorage.setItem(KEY, value)` with `await saveToServer(KEY, value)`
#   - Replace imports of `@/lib/adminPublicMirrors` (for storage-related helpers) with `@/lib/adminApi`
# Each module's UI/UX is preserved as-is.

$modulesRoot = 'e:\Projects\NGO\src\admin\modules'
$files = Get-ChildItem -Path $modulesRoot -Recurse -File -Filter *.tsx

foreach ($file in $files) {
    $path = $file.FullName
    $content = Get-Content -Path $path -Raw

    if (-not ($content -match 'window\.localStorage|loadFromStorage|persistToStorage|persistModuleStatus|persistMaintenanceState|loadMaintenanceState')) {
        continue
    }

    # Determine which server key this module maps to by sniffing STORAGE_KEY
    if ($content -match "STORAGE_KEY\s*=\s*'([^']+)'") {
        $storageKey = $matches[1]
    } else {
        Write-Host "Skipping $path — no STORAGE_KEY detected"
        continue
    }

    Write-Host "Patching $path ($storageKey)"

    # Insert import for adminApi near top of file (after first import line).
    $importLine = "import { adminApi } from '@/lib/adminApi';"
    if ($content -notmatch [regex]::Escape($importLine)) {
        # Place after the last existing import line.
        $content = [regex]::Replace(
            $content,
            "((?:^import[^\n]*\n)+)",
            "`$1$importLine`n",
            [System.Text.RegularExpressions.MatchOptions]::Multiline
        )
    }

    # Replace direct window.localStorage references with async helpers that
    # talk to the API. We add `await` automatically because the existing
    # modules already use `try`/`async` patterns.
    $content = $content.Replace("window.localStorage.getItem(STORAGE_KEY)", "(await serverStorage.getItem(STORAGE_KEY))")
    $content = $content.Replace("window.localStorage.setItem(STORAGE_KEY, JSON.stringify(", "await serverStorage.setItem(STORAGE_KEY, JSON.stringify(")
    $content = $content.Replace("window.localStorage.setItem('site.maintenance.v1', String(", "await serverStorage.setItem('site.maintenance.v1', String(")
    $content = $content.Replace("window.localStorage.getItem('site.maintenance.v1')", "(await serverStorage.getItem('site.maintenance.v1'))")
    $content = $content.Replace("window.localStorage.setItem(STORAGE_KEY,", "await serverStorage.setItem(STORAGE_KEY,")

    # Inject the serverStorage helper just before the first `export default`.
    if ($content -notmatch 'const serverStorage = ') {
        $helper = @"

const serverStorage = {
  async getItem(key: string): Promise<string | null> {
    const mapping: Record<string, string> = {
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
      'admin.newsletter.v1': 'newsletter_subscribers',
      'admin.social.v1': 'social',
      'site.maintenance.v1': 'maintenance'
    };
    const prefix = mapping[key];
    if (!prefix) return null;
    const v = await adminApi.read(prefix);
    return v === null || v === undefined ? null : JSON.stringify(v);
  },
  async setItem(key: string, value: string): Promise<void> {
    const mapping: Record<string, string> = {
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
      'admin.newsletter.v1': 'newsletter_subscribers',
      'admin.social.v1': 'social',
      'site.maintenance.v1': 'maintenance'
    };
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

"@
        $content = $content.Replace("export default function", "$helper`nexport default function")
    }

    Set-Content -Path $path -Value $content -Encoding UTF8
}
