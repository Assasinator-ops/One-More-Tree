'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type HomeCounterKey = 'treesTracked' | 'projectsActive' | 'volunteersEngaged' | 'regionsCovered';

type HomeCountersDraft = Record<HomeCounterKey, number>;

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const defaultDraft = (): HomeCountersDraft => ({
  treesTracked: 0,
  projectsActive: 0,
  volunteersEngaged: 0,
  regionsCovered: 0,
});

const fieldMeta: Array<{ key: HomeCounterKey; label: string; help: string }> = [
  { key: 'treesTracked', label: 'Trees tracked', help: 'Total number of trees tracked in the system.' },
  { key: 'projectsActive', label: 'Projects active', help: 'Number of active projects across Spain.' },
  { key: 'volunteersEngaged', label: 'Volunteers engaged', help: 'Total number of volunteers engaged.' },
  { key: 'regionsCovered', label: 'Regions covered', help: 'How many regions are covered by active projects.' },
];

const MAX_COUNTER_VALUE = 1_000_000_000;

function clampCounter(n: number) {
  if (!Number.isFinite(n)) return 0;
  const asInt = Math.max(0, Math.floor(n));
  return Math.min(MAX_COUNTER_VALUE, asInt);
}

export default function HomeCountersAdmin() {
  const [draft, setDraft] = useState<HomeCountersDraft>(() => defaultDraft());
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const [initialSnapshot, setInitialSnapshot] = useState<string>('');

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<Partial<HomeCountersDraft>>(AdminKeys.HomeCounters);
        const base = defaultDraft();
        const next: HomeCountersDraft = {
          treesTracked: clampCounter(Number(stored?.treesTracked ?? base.treesTracked)),
          projectsActive: clampCounter(Number(stored?.projectsActive ?? base.projectsActive)),
          volunteersEngaged: clampCounter(Number(stored?.volunteersEngaged ?? base.volunteersEngaged)),
          regionsCovered: clampCounter(Number(stored?.regionsCovered ?? base.regionsCovered)),
        };
        if (!alive) return;
        setDraft(next);
        const snap = JSON.stringify(next);
        setInitialSnapshot(snap);
      } catch {
        if (!alive) return;
        const next = defaultDraft();
        setDraft(next);
        setInitialSnapshot(JSON.stringify(next));
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const snapshotKey = useMemo(() => JSON.stringify(draft), [draft]);

  const isDirty = useMemo(() => {
    if (!loaded) return false;
    return snapshotKey !== initialSnapshot;
  }, [loaded, snapshotKey, initialSnapshot]);

  const onChangeNumber = (key: HomeCounterKey, value: string) => {
    const n = Number(value);
    setDraft((prev) => ({ ...prev, [key]: Number.isFinite(n) ? Math.max(0, Math.floor(n)) : prev[key] }));
    setSaveResult(null);
  };

  const onReset = () => {
    const next = defaultDraft();
    setDraft(next);
    setSaveResult(null);
    setInitialSnapshot(JSON.stringify(next));
  };

  const onSave = async () => {
    try {
      const next: HomeCountersDraft = {
        treesTracked: clampCounter(draft.treesTracked),
        projectsActive: clampCounter(draft.projectsActive),
        volunteersEngaged: clampCounter(draft.volunteersEngaged),
        regionsCovered: clampCounter(draft.regionsCovered),
      };

      await adminApi.write(AdminKeys.HomeCounters, next);
      setDraft(next);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
      setInitialSnapshot(JSON.stringify(next));
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Home Counters Module</h2>
        </div>
        <div className="text-foreground/70">Loading counters…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Home Counters Module</h2>
        <p className="text-foreground/70 mt-1">
          Record and administer the numeric counters shown on the public Home page.
        </p>
      </div>

      <div className="rounded-2xl border border-earth-200 bg-white/50 p-4 sm:p-5">
        <form
          onSubmit={(e) => {
            e.preventDefault();
            void onSave();
          }}
          className="space-y-4"
        >
          <div className="grid sm:grid-cols-2 gap-4">
            {fieldMeta.map((f) => (
              <div key={f.key} className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">{f.label}</label>
                <input
                  inputMode="numeric"
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground placeholder-foreground/40 focus:outline-none focus:ring-2 focus:ring-forest-200"
                  type="number"
                  min={0}
                  step={1}
                  value={draft[f.key]}
                  onChange={(e) => onChangeNumber(f.key, e.target.value)}
                  onBlur={() => {
                    setDraft((prev) => ({ ...prev, [f.key]: clampCounter(prev[f.key]) }));
                  }}
                />
                <p className="text-xs text-foreground/50">{f.help}</p>
              </div>
            ))}
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between pt-2">
            <div className="flex gap-2">
              <button
                type="button"
                onClick={onReset}
                className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
              >
                Reset
              </button>

              <button
                type="submit"
                disabled={!isDirty}
                className={
                  !isDirty
                    ? 'rounded-xl px-4 py-2 bg-forest-600/40 text-white cursor-not-allowed'
                    : 'rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium'
                }
              >
                Save
              </button>
            </div>

            <div className="text-sm text-foreground/60">{isDirty ? 'Unsaved changes' : saveResult?.ok ? 'All changes saved' : 'Ready'}</div>
          </div>

          {saveResult ? (
            <div
              className={
                saveResult.ok
                  ? 'rounded-xl border border-forest-200 bg-forest-50/60 text-forest-900 px-4 py-3'
                  : 'rounded-xl border border-red-200 bg-red-50/60 text-red-900 px-4 py-3'
              }
            >
              {saveResult.ok ? (
                <>Saved successfully at {new Date(saveResult.savedAt).toLocaleString()}</>
              ) : (
                <>Save failed: {saveResult.error}</>
              )}
            </div>
          ) : null}
        </form>
      </div>
    </div>
  );
}

