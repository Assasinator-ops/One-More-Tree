'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

import { Heart, Leaf, Shield, Gift, TreePine } from 'lucide-react';

type DonationTierItem = {
  id: string;
  amountEur: number;
  label?: string;
};

type TierDraft = {
  tiers: DonationTierItem[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const DEFAULT_TIERS: DonationTierItem[] = [
  { id: 'tier-25', amountEur: 25, label: 'Plant One Tree' },
  { id: 'tier-10', amountEur: 10, label: 'Growth Guardian' },
  { id: 'tier-100', amountEur: 100, label: 'Forest Protector' },
  { id: 'tier-250', amountEur: 250, label: 'Legacy Builder' },
];

function clampMoney(n: number) {
  if (!Number.isFinite(n)) return 0;
  return Math.max(0, Math.floor(n));
}

function sanitizeTier(t: any, index: number): DonationTierItem {
  const amountEur = clampMoney(t?.amountEur ?? t?.amount);
  const label = typeof t?.label === 'string' ? t.label : undefined;
  const id = String(t?.id ?? `tier-${index + 1}`).trim() || `tier-${index + 1}`;
  return { id, amountEur, label };
}

function sanitizeDraft(raw: any): TierDraft {
  const tiersRaw = raw?.tiers ?? raw;
  if (!Array.isArray(tiersRaw)) {
    return { tiers: DEFAULT_TIERS };
  }
  const tiers = tiersRaw.filter(Boolean).map((t, i) => sanitizeTier(t, i));
  return { tiers: tiers.length ? tiers : DEFAULT_TIERS };
}

function iconForIndex(i: number) {
  return [TreePine, Heart, Shield, Gift][i % 4];
}

export default function DonationTiersAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [draft, setDraft] = useState<TierDraft>(() => ({ tiers: DEFAULT_TIERS }));
  const [selectedIndex, setSelectedIndex] = useState<number>(0);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const selected = draft.tiers[selectedIndex] ?? null;

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.DonationTiers);
        if (!alive) return;
        const next = sanitizeDraft(stored);
        setDraft(next);
        setSelectedIndex(0);
      } catch {
        if (!alive) return;
        setDraft({ tiers: DEFAULT_TIERS });
        setSelectedIndex(0);
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const onAddTier = () => {
    const nextTier: DonationTierItem = {
      id: `tier-${Date.now()}`,
      amountEur: 50,
      label: 'New tier',
    };
    setDraft((prev) => ({ tiers: [nextTier, ...prev.tiers] }));
    setSelectedIndex(0);
    setSaveResult(null);
  };

  const onDeleteTier = () => {
    if (draft.tiers.length <= 1) return;
    setDraft((prev) => {
      const next = prev.tiers.filter((_, i) => i !== selectedIndex);
      return { tiers: next };
    });
    setSelectedIndex(0);
    setSaveResult(null);
  };

  const onSave = async () => {
    try {
      const next = sanitizeDraft({ tiers: draft.tiers });
      await adminApi.write(AdminKeys.DonationTiers, next);
      setDraft(next);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Donation tiers</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  const Icon = selected ? iconForIndex(selectedIndex) : TreePine;

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Donation tiers</h2>
        <p className="text-foreground/70 mt-1">Configure donation tiers used by the public donation CTA.</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Tiers</h3>
            <button type="button" onClick={onAddTier} className="rounded-full px-3 py-2 bg-forest-600 text-white text-sm">
              New
            </button>
          </div>
          {draft.tiers.length === 0 ? (
            <div className="text-foreground/70">No tiers configured yet.</div>
          ) : (
            <div className="space-y-3">
              {draft.tiers.map((t, i) => (
                <button
                  key={t.id}
                  type="button"
                  onClick={() => setSelectedIndex(i)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedIndex === i ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="flex items-center justify-between gap-2">
                    <div className="min-w-0">
                      <div className="font-semibold text-foreground">{t.label ?? 'Tier'}</div>
                      <div className="text-xs text-foreground/60">€{t.amountEur}</div>
                    </div>
                    <div className="text-xs text-foreground/70">{i === selectedIndex ? 'Editing' : ''}</div>
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-xl bg-forest-100 flex items-center justify-center">
                <Icon className="w-6 h-6 text-forest-600" />
              </div>
              <div>
                <div className="text-sm text-foreground/50">Selected tier</div>
                <div className="font-semibold text-foreground">{selected?.label ?? '—'}</div>
              </div>
            </div>

            <div className="grid sm:grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Amount (€)</label>
                <input
                  type="number"
                  min={0}
                  value={selected?.amountEur ?? 0}
                  onChange={(e) => {
                    const amountEur = clampMoney(Number(e.target.value));
                    setDraft((prev) => ({
                      tiers: prev.tiers.map((t, i) => (i === selectedIndex ? { ...t, amountEur } : t)),
                    }));
                    setSaveResult(null);
                  }}
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Label</label>
                <input
                  value={selected?.label ?? ''}
                  onChange={(e) => {
                    const label = e.target.value;
                    setDraft((prev) => ({
                      tiers: prev.tiers.map((t, i) => (i === selectedIndex ? { ...t, label } : t)),
                    }));
                    setSaveResult(null);
                  }}
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>
            </div>
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div className="flex gap-2">
              <button
                type="button"
                onClick={onDeleteTier}
                disabled={draft.tiers.length <= 1}
                className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
              >
                Delete tier
              </button>
              <button
                type="button"
                onClick={() => void onSave()}
                className="rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium"
              >
                Save tiers
              </button>
            </div>

            <div className="text-sm text-foreground/60">
              {saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

