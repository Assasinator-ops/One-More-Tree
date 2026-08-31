// Cross-browser site maintenance state helpers.
//
// The source of truth is SQLite via `app/api/admin/maintenance` + `src/server/adminData`.
// These helpers keep the same semantic role as the old localStorage-based
// functions, but delegate to the server so the state is consistent across
// devices.

import { cookies } from 'next/headers';

export async function isSiteDisabled(): Promise<boolean> {
  // Kept for compatibility: this file may be imported in Server Components.
  try {
    const store = await cookies();           // ← added await
    const v = store.get('site.maintenance')?.value;
    return v === '1';
  } catch {
    return false;
  }
}


export async function loadMaintenanceState(): Promise<boolean> {
  // Client-side usage: fetch from the maintenance endpoint.
  if (typeof window === 'undefined') return isSiteDisabled();
  try {
    const res = await fetch('/api/admin/maintenance', { cache: 'no-store' });
    const json = (await res.json().catch(() => ({}))) as { enabled?: boolean; ok?: boolean };
    return Boolean(json.enabled);
  } catch {
    return false;
  }
}

export async function persistMaintenanceState(enabled: boolean): Promise<void> {
  if (typeof window === 'undefined') return;
  await fetch('/api/admin/maintenance', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ enabled })
  });
}

