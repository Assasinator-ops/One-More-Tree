// Public mirrors for admin-editable data.
//
// This module used to read/write from browser localStorage.
// All admin modules and public pages have been migrated to SQLite via
// the existing admin API (`src/lib/adminApi.ts`).
//
// Keep this file as a compatibility shim only.

export function isAdminModuleEnabled(_key: string): boolean {
  // When module visibility is needed on the public site,
  // it must be read from SQLite-backed module statuses via adminApi.
  return true;
}

export function loadAdminLiveMap(): null {
  // Public map data is loaded via SQLite-backed reads.
  return null;
}

