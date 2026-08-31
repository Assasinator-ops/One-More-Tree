'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type Subscriber = {
  id: string;
  email: string;
  subscribedAt: string;
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

function sanitizeSubscribers(raw: unknown): Subscriber[] {
  const list = Array.isArray(raw) ? raw : [];
  return list
    .filter(Boolean)
    .map((x: any, idx: number) => {
      const id = String(x?.id ?? `newsletter-${idx + 1}`).trim() || `newsletter-${idx + 1}`;
      return {
        id,
        email: String(x?.email ?? '').trim(),
        subscribedAt: String(x?.subscribedAt ?? x?.subscribed_at ?? '').trim() || new Date(0).toISOString(),
      };
    })
    .filter((s) => s.email.length > 0)
    .slice(0, 500);
}

export default function NewsletterAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [subscribers, setSubscribers] = useState<Subscriber[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const selected = useMemo(() => subscribers.find((s) => s.id === selectedId) ?? null, [subscribers, selectedId]);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.NewsletterSubscribers);
        const next = sanitizeSubscribers(stored);
        if (!alive) return;
        setSubscribers(next);
        setSelectedId(next[0]?.id ?? null);
      } catch {
        if (!alive) return;
        setSubscribers([]);
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
    const next = subscribers.filter((s) => s.id !== selected.id);
    try {
      await adminApi.write(AdminKeys.NewsletterSubscribers, { subscribers: next });
      setSubscribers(next);
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
          <h2 className="font-serif text-2xl font-bold text-foreground">Newsletter Admin</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Newsletter Admin</h2>
        <p className="text-foreground/70 mt-1">View newsletter subscribers (public sign-ups are stored in SQLite).</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Subscribers</h3>
            <div className="text-xs text-foreground/60">{subscribers.length}</div>
          </div>

          {subscribers.length === 0 ? (
            <div className="text-foreground/70">No subscribers yet.</div>
          ) : (
            <div className="space-y-3 max-h-[60vh] overflow-auto pr-1">
              {subscribers.map((s) => (
                <button
                  key={s.id}
                  type="button"
                  onClick={() => setSelectedId(s.id)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedId === s.id ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="font-semibold text-foreground">{s.email}</div>
                  <div className="text-xs text-foreground/60">{new Date(s.subscribedAt).toLocaleDateString()}</div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div>
            <h3 className="font-semibold text-foreground mb-3">Selected subscriber</h3>
            {selected ? (
              <div className="rounded-2xl border border-earth-200 bg-white/60 p-4">
                <div className="text-lg font-semibold text-foreground">{selected.email}</div>
                <div className="text-sm text-foreground/70 mt-1">Subscribed: {new Date(selected.subscribedAt).toLocaleString()}</div>
                <div className="text-xs text-foreground/50 mt-2">ID: {selected.id}</div>
              </div>
            ) : (
              <div className="text-foreground/70">Select a subscriber from the list.</div>
            )}
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <button
              type="button"
              onClick={() => void onDelete()}
              disabled={!selected}
              className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
            >
              Remove subscriber
            </button>
            <div className="text-sm text-foreground/60">{saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}</div>
          </div>
        </div>
      </div>
    </div>
  );
}

