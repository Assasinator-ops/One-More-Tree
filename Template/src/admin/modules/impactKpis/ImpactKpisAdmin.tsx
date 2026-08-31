'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type ImpactKpiKey = 'treesPlanted' | 'projectsActive' | 'volunteersEngaged' | 'regionsCovered' | 'tonsCO2';

type ImpactKpi = {
  key: ImpactKpiKey;
  label: string;
  value: number;
  unit?: string;
};

type ProgressGoal = {
  goalBy2040Value: number;
  progressText: string;
  toGoText: string;
};

type ImpactKpisDraft = {
  kpis: ImpactKpi[];
  progress: ProgressGoal;
  impactHeadline?: string;
  impactDescription?: string;
  sectionTitle?: string;
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const MAX_VALUE = 1_000_000_000;

function clamp(n: number) {
  if (!Number.isFinite(n)) return 0;
  return Math.min(MAX_VALUE, Math.max(0, Math.floor(n)));
}

function computeProgressText(treesPlanted: number, goalBy2040Value: number) {
  if (!Number.isFinite(goalBy2040Value) || goalBy2040Value <= 0) return '0% Complete';
  const ratio = Math.max(0, Math.min(1, treesPlanted / goalBy2040Value));
  return `${(ratio * 100).toFixed(1)}% Complete`;
}

function computeToGoText(treesPlanted: number, goalBy2040Value: number) {
  if (!Number.isFinite(goalBy2040Value) || goalBy2040Value <= 0) return '0 trees to go';
  const remaining = Math.max(0, Math.floor(goalBy2040Value - treesPlanted));
  return `${remaining.toLocaleString()} ${remaining === 1 ? 'tree to go' : 'trees to go'}`;
}

function getDefaultDraft(): ImpactKpisDraft {
  return {
    sectionTitle: 'Our Impact',
    impactHeadline: 'The Numbers Tell Our Story',
    impactDescription: "Every tree planted is a step toward our ambitious goal. Here's how far we've come.",
    kpis: [
      { key: 'regionsCovered', label: 'Regions', value: 12 },
      { key: 'projectsActive', label: 'Planting Sites', value: 48 },
      { key: 'tonsCO2', label: 'Tons CO₂', value: 2_400_000, unit: '' },
      { key: 'volunteersEngaged', label: 'Volunteers', value: 3_200, unit: '+' },
      { key: 'treesPlanted', label: 'Trees Planted', value: 0 },
    ],
    progress: {
      goalBy2040Value: 1_000_000,
      progressText: '84.7% Complete',
      toGoText: '153,000 trees to go',
    },
  };
}

function sanitizeDraft(d: any): ImpactKpisDraft {
  const base = getDefaultDraft();

  const kpisRaw = Array.isArray(d?.kpis) ? d.kpis : base.kpis;
  const nextKpis: ImpactKpi[] = kpisRaw
    .filter(Boolean)
    .map((k: any) => {
      const key = String(k?.key ?? '') as ImpactKpiKey;
      const label = String(k?.label ?? '').trim().slice(0, 80) || String(base.kpis.find((x) => x.key === key)?.label ?? 'KPI');
      const value = clamp(Number(k?.value ?? 0));
      const unit = k?.unit != null ? String(k.unit).trim().slice(0, 20) : undefined;
      return { key, label, value, unit: unit || undefined };
    })
    .filter((k) => !!k.key);

  const progressRaw = d?.progress ?? {};
  const goalBy2040Value = clamp(Number(progressRaw?.goalBy2040Value ?? base.progress.goalBy2040Value));

  const treesPlanted = nextKpis.find((k) => k.key === 'treesPlanted')?.value ?? 0;

  return {
    sectionTitle: String(d?.sectionTitle ?? base.sectionTitle),
    impactHeadline: String(d?.impactHeadline ?? base.impactHeadline),
    impactDescription: String(d?.impactDescription ?? base.impactDescription),
    kpis: nextKpis,
    progress: {
      goalBy2040Value,
      progressText: computeProgressText(treesPlanted, goalBy2040Value),
      toGoText: computeToGoText(treesPlanted, goalBy2040Value),
    },
  };
}

export default function ImpactKpisAdmin() {
  const [draft, setDraft] = useState<ImpactKpisDraft>(() => getDefaultDraft());
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const [errs, setErrs] = useState<string[]>([]);
  const [initialSnapshot, setInitialSnapshot] = useState<string>('');

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.ImpactKpis);
        const next = sanitizeDraft(stored);
        if (!alive) return;
        setDraft(next);
        const snap = JSON.stringify(next);
        setInitialSnapshot(snap);
      } catch {
        if (!alive) return;
        const next = getDefaultDraft();
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

  const snapshot = useMemo(() => (loaded ? JSON.stringify(draft) : ''), [draft, loaded]);

  const isDirty = useMemo(() => {
    if (!loaded) return false;
    return snapshot !== initialSnapshot;
  }, [loaded, snapshot, initialSnapshot]);

  const onChangeKpiValue = (key: ImpactKpiKey, valueStr: string) => {
    const v = clamp(Number(valueStr));
    setDraft((p) => {
      const next = {
        ...p,
        kpis: p.kpis.map((k) => (k.key === key ? { ...k, value: v } : k)),
      };
      const treesPlanted = next.kpis.find((k) => k.key === 'treesPlanted')?.value ?? 0;
      const goalBy2040Value = next.progress.goalBy2040Value;
      next.progress = {
        ...next.progress,
        progressText: computeProgressText(treesPlanted, goalBy2040Value),
        toGoText: computeToGoText(treesPlanted, goalBy2040Value),
      };
      return next;
    });
    setSaveResult(null);
  };

  const onSave = async () => {
    const errors: string[] = [];
    const next = sanitizeDraft(draft);

    for (const k of next.kpis) {
      if (!k.label || !k.label.trim()) errors.push(`${k.key}: label is required.`);
      if (!Number.isFinite(k.value) || k.value < 0) errors.push(`${k.key}: value must be >= 0.`);
    }
    if (!next.progress.goalBy2040Value || next.progress.goalBy2040Value < 1) errors.push('goalBy2040Value must be >= 1');

    setErrs(errors);
    if (errors.length) {
      setSaveResult({ ok: false, error: 'Validation failed.' });
      return;
    }

    try {
      await adminApi.write(AdminKeys.ImpactKpis, next);
      setDraft(next);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
      setInitialSnapshot(JSON.stringify(next));
      setErrs([]);
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onReset = () => {
    const base = getDefaultDraft();
    setDraft(base);
    setSaveResult(null);
    setErrs([]);
    setInitialSnapshot(JSON.stringify(base));
  };

  const kpiFields = draft.kpis;

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Impact / Progress KPIs</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Impact / Progress KPIs</h2>
        <p className="text-foreground/70 mt-1">Admin editor for KPI values used by the public Impact page.</p>
      </div>

      <div className="rounded-3xl glass-card border border-earth-200 p-4 sm:p-6">
        <div className="space-y-5">
          <div className="grid sm:grid-cols-2 gap-4">
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Section title</label>
              <input
                value={draft.sectionTitle ?? ''}
                onChange={(e) => setDraft((p) => ({ ...p, sectionTitle: e.target.value }))}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Headline</label>
              <input
                value={draft.impactHeadline ?? ''}
                onChange={(e) => setDraft((p) => ({ ...p, impactHeadline: e.target.value }))}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">Description</label>
            <textarea
              value={draft.impactDescription ?? ''}
              onChange={(e) => setDraft((p) => ({ ...p, impactDescription: e.target.value }))}
              rows={3}
              className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
          </div>

          <div className="border-t border-earth-200 pt-5" />

          <div>
            <h3 className="font-semibold text-foreground mb-3">KPI values</h3>
            <div className="grid sm:grid-cols-2 gap-4">
              {kpiFields.map((k) => (
                <div key={k.key} className="space-y-2 rounded-2xl border border-earth-200 bg-white/50 p-4">
                  <div>
                    <div className="text-sm font-medium text-foreground/80">{k.label}</div>
                    <div className="text-xs text-foreground/50 capitalize">{k.key}</div>
                  </div>
                  <input
                    type="number"
                    inputMode="numeric"
                    min={0}
                    step={1}
                    value={k.value}
                    onChange={(e) => onChangeKpiValue(k.key, e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                  <div className="text-xs text-foreground/50">
                    Unit: <span className="font-medium">{k.unit ?? '—'}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="border-t border-earth-200 pt-5" />

          <div>
            <h3 className="font-semibold text-foreground mb-3">Progress / goal</h3>
            <div className="grid sm:grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Goal by 2040 value</label>
                <input
                  type="number"
                  inputMode="numeric"
                  min={0}
                  step={1}
                  value={draft.progress.goalBy2040Value}
                  onChange={(e) => {
                    const goalBy2040Value = clamp(Number(e.target.value));
                    setDraft((p) => {
                      const treesPlanted = p.kpis.find((k) => k.key === 'treesPlanted')?.value ?? 0;
                      return {
                        ...p,
                        progress: {
                          ...p.progress,
                          goalBy2040Value,
                          progressText: computeProgressText(treesPlanted, goalBy2040Value),
                          toGoText: computeToGoText(treesPlanted, goalBy2040Value),
                        },
                      };
                    });
                  }}
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>

              <div className="space-y-4">
                <div>
                  <label className="text-sm font-medium text-foreground/80">Progress</label>
                  <div className="mt-2 rounded-2xl border border-earth-200 bg-earth-100 p-3">
                    <div className="flex items-center justify-between text-sm text-foreground/70 mb-2">
                      <span>{draft.progress.progressText}</span>
                    </div>
                  </div>
                </div>

                <div>
                  <label className="text-sm font-medium text-foreground/80">To-go</label>
                  <div className="mt-2 rounded-2xl border border-earth-200 bg-earth-100 px-4 py-3 text-foreground">{draft.progress.toGoText}</div>
                </div>
              </div>
            </div>
          </div>

          {errs.length ? (
            <div className="rounded-xl border border-red-200 bg-red-50/60 text-red-900 px-4 py-3">
              <div className="font-semibold mb-2">Validation errors</div>
              <ul className="list-disc pl-5 space-y-1 text-sm">
                {errs.map((er, idx) => (
                  <li key={idx}>{er}</li>
                ))}
              </ul>
            </div>
          ) : null}

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <div className="flex gap-2">
              <button type="button" onClick={onReset} className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors">
                Reset
              </button>
              <button
                type="button"
                onClick={() => void onSave()}
                disabled={!isDirty}
                className={
                  !isDirty
                    ? 'rounded-xl px-4 py-2 bg-forest-600/40 text-white cursor-not-allowed'
                    : 'rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium'
                }
              >
                Save KPIs
              </button>
            </div>

            <div className="text-sm text-foreground/60">
              {saveResult?.ok ? (
                <>Saved at {new Date(saveResult.savedAt).toLocaleString()}</>
              ) : saveResult && !saveResult.ok ? (
                <>Save failed: {saveResult.error}</>
              ) : isDirty ? (
                <>Unsaved changes</>
              ) : (
                <>Ready</>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

