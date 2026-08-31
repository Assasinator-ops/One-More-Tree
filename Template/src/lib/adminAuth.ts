// Client-side admin auth helpers (previously localStorage-based).
// Now uses server-side cookie auth + SQLite-backed admin API.

import { adminApi, AdminKeys } from './adminApi';

export type AdminUser = {
  username: string;
  createdAt: string;
  mustResetPassword: boolean;
};

export async function getAdminUsers(): Promise<AdminUser[]> {
  const value = await adminApi.read<AdminUser[]>(AdminKeys.AdminUsers);
  return value ?? [];
}

export async function logoutAdmin(): Promise<void> {
  await fetch('/api/admin/logout', { method: 'POST' }).catch(() => undefined);
}



