'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type TeamMember = {
  name: string;
  role: string;
  image?: string;
  highlight?: string;
};

type TeamStat = {
  value: string;
  label: string;
};

type TeamDraft = {
  sectionTitle?: string;
  headline?: string;
  description?: string;
  members?: TeamMember[];
  stats?: TeamStat[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const MAX_MEMBERS = 8;
const MAX_STATS = 6;

const defaultDraft = (): TeamDraft => ({
  sectionTitle: 'Our Team',
  headline: 'People who make planting real',
  description:
    'From field work to reporting, our team and partners keep restoration accountable and alive.',
  members: [
    {
      name: 'Amina K.',
      role: 'Community Partnerships',
      image: '',
      highlight: 'Trusted restoration partner',
    },
    { name: 'Noah S.', role: 'Restoration Operations', image: '', highlight: 'Restoration specialist' },
    { name: 'Sofia R.', role: 'Monitoring & Impact', image: '', highlight: 'Impact storyteller' },
    { name: 'Ethan M.', role: 'Program Design', image: '', highlight: 'Program architect' },
  ],
  stats: [
    { value: '100+', label: 'Local leaders supported' },
    { value: '24/7', label: 'Monitoring & reporting' },
    { value: '10+', label: 'Program regions globally' },
  ],
});

function sanitizeDraft(d: TeamDraft): TeamDraft {
  return {
    sectionTitle: String(d.sectionTitle ?? defaultDraft().sectionTitle),
    headline: String(d.headline ?? defaultDraft().headline),
    description: String(d.description ?? defaultDraft().description),
    members: Array.isArray(d.members)
      ? d.members
          .filter(Boolean)
          .slice(0, MAX_MEMBERS)
          .map((m) => ({
            name: String(m?.name ?? '').trim() || 'Unnamed',
            role: String(m?.role ?? '').trim() || 'Role',
            image: typeof m?.image === 'string' ? m.image : '',
            highlight: typeof m?.highlight === 'string' ? m.highlight : '',
          }))
      : defaultDraft().members,
    stats: Array.isArray(d.stats)
      ? d.stats
          .filter(Boolean)
          .slice(0, MAX_STATS)
          .map((s) => ({
            value: String(s?.value ?? '').trim() || '—',
            label: String(s?.label ?? '').trim() || 'Stat',
          }))
      : defaultDraft().stats,
  };
}

export default function TeamAdmin() {
  const [draft, setDraft] = useState<TeamDraft>(() => sanitizeDraft(defaultDraft()));
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const [initialSnapshot, setInitialSnapshot] = useState<string>('');
  const [isDirty, setIsDirty] = useState(false);

  useEffect(() => {
    let alive = true;

    (async () => {
      try {
        const t = await adminApi.read<TeamDraft>(AdminKeys.Team);
        if (!alive) return;
        const next = sanitizeDraft(t ?? defaultDraft());
        setDraft(next);
        setInitialSnapshot(JSON.stringify(next));
        setIsDirty(false);
        setLoaded(true);
      } catch {
        if (!alive) return;
        const next = sanitizeDraft(defaultDraft());
        setDraft(next);
        setInitialSnapshot(JSON.stringify(next));
        setIsDirty(false);
        setLoaded(true);
      }
    })();

    return () => {
      alive = false;
    };
  }, []);

  const snapshot = useMemo(() => JSON.stringify(draft), [draft]);

  useEffect(() => {
    if (!loaded) return;
    setIsDirty(snapshot !== initialSnapshot);
  }, [snapshot, initialSnapshot, loaded]);

  const onChangeMember = (index: number, field: keyof TeamMember, value: string) => {
    setDraft((prev) => ({
      ...prev,
      members: (prev.members ?? []).map((member, idx) =>
        idx === index ? { ...member, [field]: value } : member,
      ),
    }));
    setSaveResult(null);
  };

  const onChangeStat = (index: number, field: keyof TeamStat, value: string) => {
    setDraft((prev) => ({
      ...prev,
      stats: (prev.stats ?? []).map((stat, idx) => (idx === index ? { ...stat, [field]: value } : stat)),
    }));
    setSaveResult(null);
  };

  const addMember = () => {
    setDraft((prev) => {
      const members = prev.members ?? [];
      if (members.length >= MAX_MEMBERS) return prev;
      return {
        ...prev,
        members: [...members, { name: 'New Member', role: 'Role', image: '', highlight: '' }],
      };
    });
    setSaveResult(null);
  };

  const removeMember = (index: number) => {
    setDraft((prev) => ({
      ...prev,
      members: (prev.members ?? []).filter((_, idx) => idx !== index),
    }));
    setSaveResult(null);
  };

  const addStat = () => {
    setDraft((prev) => {
      const stats = prev.stats ?? [];
      if (stats.length >= MAX_STATS) return prev;
      return { ...prev, stats: [...stats, { value: '—', label: 'New stat' }] };
    });
    setSaveResult(null);
  };

  const removeStat = (index: number) => {
    setDraft((prev) => ({
      ...prev,
      stats: (prev.stats ?? []).filter((_, idx) => idx !== index),
    }));
    setSaveResult(null);
  };

  const onSave = async () => {
    try {
      const next = sanitizeDraft(draft);
      await adminApi.write(AdminKeys.Team, next);
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setIsDirty(false);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onReset = async () => {
    try {
      const t = await adminApi.read<TeamDraft>(AdminKeys.Team);
      const next = sanitizeDraft(t ?? defaultDraft());
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setIsDirty(false);
      setSaveResult(null);
    } catch {
      const next = sanitizeDraft(defaultDraft());
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setIsDirty(false);
      setSaveResult(null);
    }
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">People Who Make Planting Real</h2>
          <p className="text-foreground/70 mt-1">Loading…</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">People Who Make Planting Real</h2>
        <p className="text-foreground/70 mt-1">Admin module for team data: section copy, member roster, and supporting stats.</p>
      </div>

      <div className="rounded-3xl glass-card border border-earth-200 p-4 sm:p-6 space-y-6">
        <div className="grid sm:grid-cols-2 gap-4">
          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">Section title</label>
            <input
              value={draft.sectionTitle ?? ''}
              onChange={(e) => setDraft((prev) => ({ ...prev, sectionTitle: e.target.value }))}
              className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">Headline</label>
            <input
              value={draft.headline ?? ''}
              onChange={(e) => setDraft((prev) => ({ ...prev, headline: e.target.value }))}
              className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
          </div>
        </div>

        <div className="space-y-2">
          <label className="text-sm font-medium text-foreground/80">Description</label>
          <textarea
            value={draft.description ?? ''}
            onChange={(e) => setDraft((prev) => ({ ...prev, description: e.target.value }))}
            rows={3}
            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
          />
        </div>

        <div className="border-t border-earth-200 pt-5" />

        <div>
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Team members</h3>
            <button
              type="button"
              onClick={addMember}
              className="rounded-full px-4 py-2 bg-forest-600 text-white text-sm font-semibold hover:bg-forest-700 transition-colors"
            >
              Add member
            </button>
          </div>

          <div className="space-y-4">
            {(draft.members ?? []).map((member, index) => (
              <div key={index} className="rounded-2xl border border-earth-200 bg-white p-4 space-y-4">
                <div className="flex items-center justify-between gap-3">
                  <div>
                    <div className="text-sm font-semibold text-foreground">Member {index + 1}</div>
                    <div className="text-xs text-foreground/50">Editable member details</div>
                  </div>
                  <button
                    type="button"
                    onClick={() => removeMember(index)}
                    className="text-sm text-red-600 hover:text-red-800"
                  >
                    Remove
                  </button>
                </div>

                <div className="grid sm:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Name</label>
                    <input
                      value={member.name}
                      onChange={(e) => onChangeMember(index, 'name', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Role</label>
                    <input
                      value={member.role}
                      onChange={(e) => onChangeMember(index, 'role', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Highlight</label>
                  <input
                    value={member.highlight ?? ''}
                    onChange={(e) => onChangeMember(index, 'highlight', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="border-t border-earth-200 pt-5" />

        <div>
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Team stats</h3>
            <button
              type="button"
              onClick={addStat}
              className="rounded-full px-4 py-2 bg-forest-600 text-white text-sm font-semibold hover:bg-forest-700 transition-colors"
            >
              Add stat
            </button>
          </div>

          <div className="space-y-4">
            {(draft.stats ?? []).map((stat, index) => (
              <div key={index} className="rounded-2xl border border-earth-200 bg-white p-4 grid sm:grid-cols-[1fr_auto] gap-4 items-start">
                <div className="grid sm:grid-cols-2 gap-4">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Value</label>
                    <input
                      value={stat.value}
                      onChange={(e) => onChangeStat(index, 'value', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>

                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Label</label>
                    <input
                      value={stat.label}
                      onChange={(e) => onChangeStat(index, 'label', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                </div>

                <button
                  type="button"
                  onClick={() => removeStat(index)}
                  className="self-start rounded-full px-4 py-2 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition-colors"
                >
                  Remove
                </button>
              </div>
            ))}
          </div>
        </div>

        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
          <div className="flex gap-2">
            <button
              type="button"
              onClick={() => void onReset()}
              className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
            >
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
              Save team
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
  );
}

