'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type ContactSubmission = {
  id: string;
  name: string;
  email: string;
  message: string;
  submittedAt: string;
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

function sanitizeSubmissions(raw: unknown): ContactSubmission[] {
  const list = Array.isArray(raw) ? raw : [];
  return list
    .filter(Boolean)
    .map((x: any, idx: number) => {
      const id = String(x?.id ?? `contact-${idx + 1}`).trim() || `contact-${idx + 1}`;
      return {
        id,
        name: String(x?.name ?? '').trim() || '—',
        email: String(x?.email ?? '').trim() || '',
        message: String(x?.message ?? '').trim() || '',
        submittedAt: String(x?.submittedAt ?? x?.submitted_at ?? '').trim() || new Date(0).toISOString(),
      };
    })
    .slice(0, 200);
}

export default function ContactAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [submissions, setSubmissions] = useState<ContactSubmission[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const selected = useMemo(() => submissions.find((s) => s.id === selectedId) ?? null, [submissions, selectedId]);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.ContactSubmissions);
        const next = sanitizeSubmissions((stored as any)?.submissions ?? stored);
        if (!alive) return;
        setSubmissions(next);
        setSelectedId(next[0]?.id ?? null);
      } catch {
        if (!alive) return;
        setSubmissions([]);
        setSelectedId(null);
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const onDelete = async () => {
    if (!selected) return;
    const next = submissions.filter((s) => s.id !== selected.id);
    try {
      await adminApi.write(AdminKeys.ContactSubmissions, { submissions: next });
      setSubmissions(next);
      setSelectedId(next[0]?.id ?? null);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Contact inbox</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Contact inbox</h2>
        <p className="text-foreground/70 mt-1">View and manage messages submitted from the public contact form.</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Messages</h3>
            <div className="text-xs text-foreground/60">{submissions.length}</div>
          </div>

          {submissions.length === 0 ? (
            <div className="text-foreground/70">No messages yet.</div>
          ) : (
            <div className="space-y-3">
              {submissions.map((s) => (
                <button
                  key={s.id}
                  type="button"
                  onClick={() => setSelectedId(s.id)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedId === s.id ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="font-semibold text-foreground">{s.name}</div>
                  <div className="text-xs text-foreground/60 truncate">{s.email || s.id}</div>
                  <div className="text-[11px] text-foreground/40 mt-1">
                    {new Date(s.submittedAt).toLocaleDateString()}
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div>
            <div className="text-sm text-foreground/50">Selected message</div>
            {selected ? (
              <div className="rounded-2xl border border-earth-200 bg-white/60 p-4 sm:p-5 mt-3 space-y-3">
                <div className="flex items-start justify-between gap-3">
                  <div>
                    <div className="text-lg font-semibold text-foreground">{selected.name}</div>
                    <div className="text-sm text-foreground/70">{selected.email || '—'}</div>
                    <div className="text-xs text-foreground/50 mt-1">{new Date(selected.submittedAt).toLocaleString()}</div>
                  </div>
                </div>

                <div className="text-sm text-foreground/80 whitespace-pre-wrap">{selected.message}</div>
              </div>
            ) : (
              <div className="text-foreground/70">Select a message to view.</div>
            )}
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <button
              type="button"
              onClick={() => void onDelete()}
              disabled={!selected}
              className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
            >
              Delete
            </button>

            <div className="text-sm text-foreground/60">
              {saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

