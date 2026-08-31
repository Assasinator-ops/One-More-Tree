// Server-side SQLite singleton using Node's built-in `node:sqlite`.
//
// We keep one shared connection per Node.js process. Next.js routes /
// server components / middleware all import this helper. The database file
// lives in `data/dev.db` so it is easy to reset during development.
//
// Notes:
// - Node 22+ ships `node:sqlite` natively (no Visual Studio / node-gyp).
// - All callers use the same SQL syntax as better-sqlite3; the only API
//   difference is we expose `prepare()` returning a sync statement.

import { DatabaseSync } from 'node:sqlite';
import fs from 'node:fs';
import path from 'node:path';

const DATA_DIR = path.join(process.cwd(), 'data');
const DB_PATH = path.join(DATA_DIR, 'dev.db');

type SqlValue = string | number | null;

interface SqliteStatement {
  run(...params: SqlValue[]): { lastInsertRowid: number | bigint; changes: number };
  get(...params: SqlValue[]): unknown;
  all(...params: SqlValue[]): unknown[];
}

interface SqliteDatabase {
  exec(sql: string): void;
  prepare(sql: string): SqliteStatement;
}

let dbInstance: SqliteDatabase | null = null;

function ensureDir() {
  if (!fs.existsSync(DATA_DIR)) {
    fs.mkdirSync(DATA_DIR, { recursive: true });
  }
}

function bootstrap(db: SqliteDatabase) {
  db.exec(`
    PRAGMA journal_mode = WAL;
    PRAGMA foreign_keys = ON;

    CREATE TABLE IF NOT EXISTS settings (
      key TEXT PRIMARY KEY,
      value TEXT NOT NULL,
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS admin_users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL,
      must_reset_password INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS content_modules (
      key TEXT PRIMARY KEY,
      enabled INTEGER NOT NULL DEFAULT 1,
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS content_kv (
      key TEXT PRIMARY KEY,
      value TEXT NOT NULL,
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS contact_submissions (
      id TEXT PRIMARY KEY,
      name TEXT NOT NULL,
      email TEXT NOT NULL,
      message TEXT NOT NULL,
      submitted_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS newsletter_subscribers (
      id TEXT PRIMARY KEY,
      email TEXT NOT NULL,
      subscribed_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS blog_posts (
      id TEXT PRIMARY KEY,
      slug TEXT NOT NULL UNIQUE,
      title TEXT NOT NULL,
      subtitle TEXT,
      author TEXT NOT NULL,
      date TEXT NOT NULL,
      iso_date TEXT NOT NULL,
      read_time TEXT NOT NULL,
      category TEXT NOT NULL,
      tags TEXT NOT NULL,
      image TEXT NOT NULL,
      image_credit TEXT,
      excerpt TEXT NOT NULL,
      content TEXT NOT NULL,
      published INTEGER NOT NULL DEFAULT 0,
      featured INTEGER NOT NULL DEFAULT 0,
      created_at TEXT NOT NULL DEFAULT (datetime('now')),
      updated_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS blog_authors (
      id TEXT PRIMARY KEY,
      name TEXT NOT NULL UNIQUE,
      role TEXT NOT NULL,
      avatar TEXT NOT NULL,
      bio TEXT NOT NULL,
      created_at TEXT NOT NULL DEFAULT (datetime('now'))
    );

    CREATE INDEX IF NOT EXISTS idx_blog_posts_slug ON blog_posts(slug);
    CREATE INDEX IF NOT EXISTS idx_blog_posts_category ON blog_posts(category);
    CREATE INDEX IF NOT EXISTS idx_blog_posts_published ON blog_posts(published);
    CREATE INDEX IF NOT EXISTS idx_blog_posts_date ON blog_posts(date DESC);
  `);

  // Seed default module visibility rows (idempotent).
  const defaults = ['impact', 'team', 'stories', 'liveMap', 'projects', 'faq', 'donations', 'contact', 'newsletter'];
  const insert = db.prepare(
    `INSERT OR IGNORE INTO content_modules (key, enabled) VALUES (?, 1)`
  );
  for (const key of defaults) insert.run(key);

  // Seed default settings (maintenance off, under construction off).
  db.prepare(`INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)`).run(
    'maintenance_enabled',
    'false'
  );
  db.prepare(`INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)`).run(
    'under_construction_enabled',
    'false'
  );
}

export function getDb(): SqliteDatabase {
  if (dbInstance) return dbInstance;
  ensureDir();
  const db = new DatabaseSync(DB_PATH) as unknown as SqliteDatabase;
  bootstrap(db);
  dbInstance = db;
  return db;
}

// Settings helpers
export function getSetting(key: string): string | null {
  const row = getDb().prepare(`SELECT value FROM settings WHERE key = ?`).get(key) as
    | { value: string }
    | undefined;
  return row?.value ?? null;
}

export function setSetting(key: string, value: string) {
  getDb()
    .prepare(
      `INSERT INTO settings (key, value) VALUES (?, ?)
       ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = datetime('now')`
    )
    .run(key, value);
}

// Content key/value helpers (store JSON strings)
export function getContent<T>(key: string, fallback: T): T {
  const row = getDb().prepare(`SELECT value FROM content_kv WHERE key = ?`).get(key) as
    | { value: string }
    | undefined;
  if (!row) return fallback;
  try {
    return JSON.parse(row.value) as T;
  } catch {
    return fallback;
  }
}

export function setContent(key: string, value: unknown) {
  getDb()
    .prepare(
      `INSERT INTO content_kv (key, value) VALUES (?, ?)
       ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = datetime('now')`
    )
    .run(key, JSON.stringify(value));
}

// Module visibility helpers
export function getModuleStatuses(): Record<string, boolean> {
  const rows = getDb()
    .prepare(`SELECT key, enabled FROM content_modules`)
    .all() as Array<{ key: string; enabled: number }>;
  return Object.fromEntries(rows.map((r) => [r.key, r.enabled === 1]));
}

export function setModuleStatuses(modules: Record<string, boolean>) {
  const stmt = getDb().prepare(
    `INSERT INTO content_modules (key, enabled) VALUES (?, ?)
     ON CONFLICT(key) DO UPDATE SET enabled = excluded.enabled, updated_at = datetime('now')`
  );
  for (const [key, enabled] of Object.entries(modules)) stmt.run(key, enabled ? 1 : 0);
}