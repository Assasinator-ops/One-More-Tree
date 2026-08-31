// Server-side admin auth helpers (replaces the localStorage-based adminAuth.ts).
//
// We use bcryptjs for password hashing (pure JS, works on every Node runtime)
// and an httpOnly cookie "admin.session" carrying the admin username.
// The middleware reads this cookie to determine if a request is authenticated.

import bcrypt from 'bcryptjs';
import { cookies } from 'next/headers';
import { getDb } from './db';

export const ADMIN_SESSION_COOKIE = 'admin.session';
const DEFAULT_USERNAME = 'admin';
const DEFAULT_PASSWORD = 'admin123';

export async function ensureDefaultAdmin(): Promise<void> {
  const db = getDb();
  const row = db.prepare(`SELECT COUNT(*) AS count FROM admin_users`).get() as { count: number };
  if (row.count > 0) return;

  const passwordHash = await bcrypt.hash(DEFAULT_PASSWORD, 10);
  db.prepare(
    `INSERT INTO admin_users (username, password_hash, must_reset_password)
     VALUES (?, ?, 1)`
  ).run(DEFAULT_USERNAME, passwordHash);
}

export type AdminRecord = {
  id: number;
  username: string;
  must_reset_password: 0 | 1;
};

export async function authenticateAdmin(
  username: string,
  password: string
): Promise<AdminRecord | null> {
  await ensureDefaultAdmin();
  const db = getDb();
  const row = db
    .prepare(`SELECT id, username, password_hash, must_reset_password FROM admin_users WHERE username = ?`)
    .get(username.trim().toLowerCase()) as
    | { id: number; username: string; password_hash: string; must_reset_password: number }
    | undefined;
  if (!row) return null;
  const ok = await bcrypt.compare(password, row.password_hash);
  if (!ok) return null;
  return {
    id: row.id,
    username: row.username,
    must_reset_password: row.must_reset_password === 1 ? 1 : 0
  };
}

function normalizeUsername(value: string) {
  return value.trim().toLowerCase();
}

function assertPassword(password: string) {
  if (password.length < 8) {
    throw new Error('Password must be at least 8 characters.');
  }
}

export async function changeAdminPassword(username: string, newPassword: string): Promise<void> {
  assertPassword(newPassword);
  const normalized = normalizeUsername(username);
  const passwordHash = await bcrypt.hash(newPassword, 10);
  getDb()
    .prepare(
      `UPDATE admin_users SET password_hash = ?, must_reset_password = 0 WHERE username = ?`
    )
    .run(passwordHash, normalized);
}

export async function addAdminAccount(username: string, password: string): Promise<void> {
  const normalized = normalizeUsername(username);
  if (!normalized) {
    throw new Error('Username is required.');
  }
  assertPassword(password);
  const passwordHash = await bcrypt.hash(password, 10);
  getDb()
    .prepare(
      `INSERT INTO admin_users (username, password_hash, must_reset_password) VALUES (?, ?, 1)`
    )
    .run(normalized, passwordHash);
}

export async function deleteAdminAccount(username: string): Promise<void> {
  const normalized = normalizeUsername(username);
  if (!normalized) {
    throw new Error('Username is required.');
  }

  // Prevent accidental lockout. The default admin user can never be deleted.
  if (normalized === DEFAULT_USERNAME) {
    throw new Error('The default admin account cannot be deleted.');
  }

  // Avoid deleting the last remaining admin.
  const db = getDb();
  const countRow = db.prepare(`SELECT COUNT(*) AS count FROM admin_users`).get() as { count: number };
  if ((countRow?.count ?? 0) <= 1) {
    throw new Error('Cannot delete the last admin account.');
  }

  db.prepare(`DELETE FROM admin_users WHERE username = ?`).run(normalized);
}

export async function setAdminPassword(username: string, newPassword: string): Promise<void> {
  // Alias for “update credentials” where the operator sets a known password.
  await changeAdminPassword(username, newPassword);
}


export function listAdminUsers(): Array<{
  username: string;
  must_reset_password: boolean;
  created_at: string;
}> {
  const rows = getDb()
    .prepare(`SELECT username, must_reset_password, created_at FROM admin_users ORDER BY id ASC`)
    .all() as Array<{ username: string; must_reset_password: number; created_at: string }>;
  return rows.map((r) => ({
    username: r.username,
    must_reset_password: r.must_reset_password === 1,
    created_at: r.created_at
  }));
}

// Cookie helpers — usable from Route Handlers / Server Actions / middleware.
export async function getSessionUsername(): Promise<string | null> {
  const store = await cookies();
  return store.get(ADMIN_SESSION_COOKIE)?.value ?? null;
}

export async function getCurrentAdmin(): Promise<AdminRecord | null> {
  const username = await getSessionUsername();
  if (!username) return null;
  const row = getDb()
    .prepare(`SELECT id, username, must_reset_password FROM admin_users WHERE username = ?`)
    .get(username) as { id: number; username: string; must_reset_password: number } | undefined;
  if (!row) return null;
  return {
    id: row.id,
    username: row.username,
    must_reset_password: row.must_reset_password === 1 ? 1 : 0
  };
}

export async function setSessionCookie(username: string) {
  const store = await cookies();
  store.set({
    name: ADMIN_SESSION_COOKIE,
    value: username,
    httpOnly: true,
    sameSite: 'lax',
    path: '/',
    // 7 days
    maxAge: 60 * 60 * 24 * 7
  });
}

export async function clearSessionCookie() {
  const store = await cookies();
  store.delete(ADMIN_SESSION_COOKIE);
}
