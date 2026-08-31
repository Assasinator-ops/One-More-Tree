'use client';

import { useEffect, useMemo, useState } from 'react';

type DisclaimerColor = 'green' | 'red' | 'yellow' | 'blue' | 'pink';

type Disclaimer = {
  enabled: boolean;
  message: string;
  color: DisclaimerColor;
};

const DEFAULT: Disclaimer = {
  enabled: true,
  message:
    '🌳 OneMoreTree NGO is a volunteer-led initiative. All donations go directly to tree planting, site restoration, and community programs. We are a registered non-profit — receipts are issued on request.',
  color: 'green'
};

const COLOR_OPTIONS: Array<{ value: DisclaimerColor; label: string }> = [
  { value: 'green', label: 'Current (green)' },
  { value: 'red', label: 'Error (red)' },
  { value: 'yellow', label: 'Warn (yellow)' },
  { value: 'blue', label: 'Info (blue)' },
  { value: 'pink', label: 'Surprise (pink)' }
];

async function readDisclaimer(): Promise<Disclaimer> {
  const res = await fetch('/api/admin/data?key=disclaimer', { cache: 'no-store' });
  const json = await res.json();
  if (!res.ok || !json.ok) {
    return DEFAULT;
  }
  return (json.value ?? DEFAULT) as Disclaimer;
}

async function writeDisclaimer(next: Disclaimer) {
  const res = await fetch('/api/admin/data', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key: 'disclaimer', value: next })
  });
  const json = await res.json();
  if (!res.ok || !json.ok) {
    throw new Error(json?.message ?? 'Save failed');
  }
}

export default function DisclaimerAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [draft, setDraft] = useState<Disclaimer>(DEFAULT);
  const [saving, setSaving] = useState(false);
  const [result, setResult] = useState<string | null>(null);

  useEffect(() => {
    let mounted = true;
    (async () => {
      const value = await readDisclaimer();
      if (!mounted) return;
      setDraft(value ?? DEFAULT);
      setLoaded(true);
    })();
    return () => {
      mounted = false;
    };
  }, []);

  const canSave = useMemo(() => {
    return loaded && !saving;
  }, [loaded, saving]);

  const onSave = async () => {
    setSaving(true);
    setResult(null);
    try {
      const next: Disclaimer = {
        enabled: Boolean(draft.enabled),
        message: String(draft.message ?? '').trim() || DEFAULT.message,
        color: draft.color
      };
      await writeDisclaimer(next);
      setDraft(next);
      setResult('Saved. Public site will update shortly.');
    } catch (e) {
      setResult(e instanceof Error ? e.message : 'Save failed');
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Disclaimer</h2>
        <p className="text-foreground/70 mt-1">
          Enable/disable the ribbon, edit its message, and choose the color theme.
        </p>
      </div>

      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6 space-y-5">
        {!loaded ? (
          <div className="text-foreground/70">Loading…</div>
        ) : (
          <>
            <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Enabled</label>
                <div className="flex items-center gap-3">
                  <input
                    type="checkbox"
                    checked={draft.enabled}
                    onChange={(e) => setDraft((p) => ({ ...p, enabled: e.target.checked }))}
                    className="h-5 w-5"
                    aria-label="Enable disclaimer ribbon"
                  />
                  <span className="text-sm text-foreground/70">Show the ribbon above navigation</span>
                </div>
              </div>

              <div className="space-y-2 min-w-[280px]">
                <label className="text-sm font-medium text-foreground/80">Color</label>
                <select
                  value={draft.color}
                  onChange={(e) =>
                    setDraft((p) => ({
                      ...p,
                      color: e.target.value as Disclaimer['color']
                    }))
                  }
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                >
                  {COLOR_OPTIONS.map((c) => (
                    <option key={c.value} value={c.value}>
                      {c.label}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Message</label>
              <textarea
                value={draft.message}
                onChange={(e) => setDraft((p) => ({ ...p, message: e.target.value }))}
                rows={3}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>

            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
              <div className="text-sm text-foreground/60">
                When enabled, the public site displays the ribbon using the selected color.
              </div>

              <div className="flex gap-3">
                <button
                  type="button"
                  onClick={() => setDraft(DEFAULT)}
                  className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
                  disabled={saving}
                >
                  Reset
                </button>
                <button
                  type="button"
                  onClick={onSave}
                  disabled={!canSave}
                  className={
                    saving
                      ? 'rounded-xl px-4 py-2 bg-yellow-600/40 text-white cursor-not-allowed'
                      : 'rounded-xl px-4 py-2 bg-gradient-to-b from-yellow-300 via-amber-400 to-yellow-200 text-amber-950 transition-all shadow-medium hover:brightness-110'
                  }
                >
                  {saving ? 'Saving…' : 'Save disclaimer'}
                </button>
              </div>
            </div>

            {result ? (
              <div className="rounded-xl border border-forest-200 bg-forest-50/60 text-forest-900 px-4 py-3">{result}</div>
            ) : null}
          </>
        )}
      </div>

      <div className="text-xs text-foreground/50 leading-relaxed">
        <b>Note:</b> The disclaimer ribbon will update after you save (cache is invalidated by the admin API write).
      </div>
    </div>
  );
}

