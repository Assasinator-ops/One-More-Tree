// Admin scaffold placeholder.
//
// Real admin typically needs server-side auth + CMS access.
// When you pick a CMS provider, this file is where we wire:
// - create/edit draft endpoints
// - publish endpoints
// - draft/preview endpoints
//
// Keeping it as a scaffold prevents accidentally committing insecure client-side admin logic.

export type AdminActionResult = {
  ok: boolean;
  message?: string;
};

export async function publishAll(): Promise<AdminActionResult> {
  return { ok: false, message: 'Admin backend not wired yet.' };
}

