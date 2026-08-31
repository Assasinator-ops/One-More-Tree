'use client';

import { useEffect, useState } from 'react';

type Snapshot = Record<string, unknown>;

export default function ReportingView() {
  const [snap, setSnap] = useState<Snapshot | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      const keys = [
        'home_counters',
        'impact_kpis',
        'stories',
        'team',
        'live_map',
        'projects',
        'faq',
        'donation_tiers',
        'donation_orders',
        'social',
        'modules',
        'maintenance',
        'contact_submissions',
        'newsletter_subscribers',
        'admin_users'
      ];
      const entries: Snapshot = {};
      try {
        for (const key of keys) {
          const res = await fetch(`/api/admin/data?key=${encodeURIComponent(key)}`);
          const json = await res.json();
          if (json.ok) entries[key] = json.value;
        }
        setSnap(entries);
      } catch (e) {
        setError(e instanceof Error ? e.message : 'Failed to load report.');
      }
    })();
  }, []);

  if (error) {
    return <div className="rounded-2xl border border-red-200 bg-red-50 text-red-900 p-4">{error}</div>;
  }
  if (!snap) {
    return <div className="text-foreground/70">Loading report…</div>;
  }

  return (
    <div className="grid gap-6 md:grid-cols-2">
      {Object.entries(snap).map(([key, value]) => (
        <section key={key} className="rounded-2xl bg-white/95 border p-4">
          <h2 className="font-semibold capitalize">{key.replace(/_/g, ' ')}</h2>
          <pre className="text-xs text-foreground/70 mt-2 overflow-auto max-h-80">
            {JSON.stringify(value, null, 2)}
          </pre>
        </section>
      ))}
    </div>
  );
}
