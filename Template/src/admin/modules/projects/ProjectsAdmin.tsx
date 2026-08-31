'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type ProjectMilestone = {
  id: string;
  title: string;
  description: string;
  date: string;
  status?: string;
};

type ProjectItem = {
  id: string;
  name: string;
  region: string;
  description: string;
  treesPlanted: number;
  volunteers: number;
  active: boolean;
  milestones: ProjectMilestone[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

function clampNumber(value: unknown) {
  const n = Number(value);
  return Number.isFinite(n) ? Math.max(0, Math.floor(n)) : 0;
}

function sanitizeMilestone(m: any, index: number): ProjectMilestone {
  const id = String(m?.id ?? `milestone-${index + 1}`).trim() || `milestone-${index + 1}`;
  return {
    id,
    title: String(m?.title ?? '').trim() || 'Milestone',
    description: String(m?.description ?? '').trim() || 'Update details',
    date: String(m?.date ?? '').trim() || new Date().toISOString().slice(0, 10),
    status: typeof m?.status === 'string' ? m.status : undefined,
  };
}

function sanitizeProjects(value: any): ProjectItem[] {
  if (!Array.isArray(value)) return [];
  return value
    .filter(Boolean)
    .map((project, index) => {
      const id = String(project?.id ?? `project-${index + 1}`).trim();
      return {
        id,
        name: String(project?.name ?? '').trim() || `Project ${index + 1}`,
        region: String(project?.region ?? '').trim() || 'Spain',
        description: String(project?.description ?? '').trim() || 'A conservation project.',
        treesPlanted: clampNumber(project?.treesPlanted),
        volunteers: clampNumber(project?.volunteers),
        active: typeof project?.active === 'boolean' ? project.active : true,
        milestones: Array.isArray(project?.milestones)
          ? project.milestones.filter(Boolean).map((m: any, msIndex: number) => sanitizeMilestone(m, msIndex))
          : [],
      } as ProjectItem;
    })
    .filter((p) => p.id.length > 0);
}

function makeBlankProject(): ProjectItem {
  return {
    id: `project-${Date.now()}`,
    name: 'New project',
    region: 'Spain',
    description: 'Describe the active restoration initiative.',
    treesPlanted: 0,
    volunteers: 0,
    active: true,
    milestones: [],
  };
}

export default function ProjectsAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [projects, setProjects] = useState<ProjectItem[]>([]);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [draft, setDraft] = useState<ProjectItem>(() => makeBlankProject());
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<{ projects?: ProjectItem[] }>(AdminKeys.Projects);
        const list = sanitizeProjects(stored?.projects ?? stored);
        if (!alive) return;
        const initial = list.length ? list : [makeBlankProject()];
        setProjects(initial);
        setSelectedId(initial[0]?.id ?? null);
        setDraft(initial[0] ?? makeBlankProject());
      } catch {
        if (!alive) return;
        const initial = [makeBlankProject()];
        setProjects(initial);
        setSelectedId(initial[0]?.id ?? null);
        setDraft(initial[0]);
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const selectedProject = useMemo(() => projects.find((p) => p.id === selectedId) ?? null, [projects, selectedId]);

  useEffect(() => {
    if (!loaded) return;
    if (selectedProject) {
      setDraft(selectedProject);
      setSaveResult(null);
    } else {
      const blank = makeBlankProject();
      setDraft(blank);
    }
  }, [loaded, selectedProject]);

  const onSave = async () => {
    const saved: ProjectItem = {
      ...draft,
      id: String(draft.id).trim() || `project-${Date.now()}`,
      treesPlanted: clampNumber(draft.treesPlanted),
      volunteers: clampNumber(draft.volunteers),
      name: String(draft.name).trim() || 'New project',
      region: String(draft.region).trim() || 'Spain',
      description: String(draft.description).trim() || 'Describe the active restoration initiative.',
      active: Boolean(draft.active),
      milestones: Array.isArray(draft.milestones)
        ? draft.milestones.map((m, i) => sanitizeMilestone(m, i))
        : [],
    };

    const nextProjects = selectedProject ? projects.map((p) => (p.id === saved.id ? saved : p)) : [saved, ...projects];
    try {
      await adminApi.write(AdminKeys.Projects, { projects: nextProjects });
      setProjects(nextProjects);
      setSelectedId(saved.id);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onDelete = async () => {
    if (!selectedProject) return;
    const next = projects.filter((p) => p.id !== selectedProject.id);
    const final = next.length ? next : [makeBlankProject()];
    try {
      await adminApi.write(AdminKeys.Projects, { projects: final });
      setProjects(final);
      setSelectedId(final[0]?.id ?? null);
      setDraft(final[0] ?? makeBlankProject());
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onAddNew = () => {
    const blank = makeBlankProject();
    setProjects((prev) => [blank, ...prev]);
    setSelectedId(blank.id);
    setDraft(blank);
    setSaveResult(null);
  };

  const updateDraft = (field: keyof ProjectItem, value: string | number | boolean) => {
    setDraft((prev) => ({ ...prev, [field]: value } as ProjectItem));
    setSaveResult(null);
  };

  const updateMilestone = (milestoneId: string, field: keyof ProjectMilestone, value: string) => {
    setDraft((prev) => ({
      ...prev,
      milestones: prev.milestones.map((m) => (m.id === milestoneId ? { ...m, [field]: value } : m)),
    }));
    setSaveResult(null);
  };

  const addMilestone = () => {
    setDraft((prev) => ({
      ...prev,
      milestones: [
        ...prev.milestones,
        {
          id: `milestone-${Date.now()}`,
          title: 'New milestone',
          description: 'Describe the project update.',
          date: new Date().toISOString().slice(0, 10),
          status: 'In progress',
        },
      ],
    }));
    setSaveResult(null);
  };

  const removeMilestone = (milestoneId: string) => {
    setDraft((prev) => ({
      ...prev,
      milestones: prev.milestones.filter((m) => m.id !== milestoneId),
    }));
    setSaveResult(null);
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Projects Module</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Projects Module</h2>
        <p className="text-foreground/70 mt-1">Manage active restoration projects, progress, volunteer totals, and milestones.</p>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        <div className="lg:w-1/3 rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Active projects</h3>
            <button type="button" onClick={onAddNew} className="rounded-full px-3 py-2 bg-forest-600 text-white text-sm">
              New
            </button>
          </div>

          {projects.length === 0 ? (
            <div className="text-foreground/70">No projects yet. Add one to start.</div>
          ) : (
            <div className="space-y-3">
              {projects.map((project) => (
                <button
                  key={project.id}
                  type="button"
                  onClick={() => setSelectedId(project.id)}
                  className={`w-full text-left rounded-2xl border p-4 transition ${
                    selectedId === project.id ? 'border-forest-500 bg-forest-50' : 'border-earth-200 bg-white'
                  }`}
                >
                  <div className="flex items-center justify-between gap-2">
                    <div>
                      <div className="font-semibold text-foreground">{project.name}</div>
                      <div className="text-xs text-foreground/60">{project.region}</div>
                    </div>
                    <div className="text-xs text-foreground/70">{project.active ? 'Active' : 'Paused'}</div>
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="lg:w-2/3 rounded-3xl border border-earth-200 bg-white/80 p-5 sm:p-6 space-y-6">
          <div className="grid md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Project name</label>
              <input
                value={draft.name}
                onChange={(e) => updateDraft('name', e.target.value)}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Region</label>
              <input
                value={draft.region}
                onChange={(e) => updateDraft('region', e.target.value)}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">Description</label>
            <textarea
              rows={4}
              value={draft.description}
              onChange={(e) => updateDraft('description', e.target.value)}
              className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
          </div>

          <div className="grid sm:grid-cols-3 gap-4">
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Trees planted</label>
              <input
                type="number"
                min={0}
                value={draft.treesPlanted}
                onChange={(e) => updateDraft('treesPlanted', clampNumber(e.target.value))}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Volunteers</label>
              <input
                type="number"
                min={0}
                value={draft.volunteers}
                onChange={(e) => updateDraft('volunteers', clampNumber(e.target.value))}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium text-foreground/80">Status</label>
              <select
                value={draft.active ? 'active' : 'paused'}
                onChange={(e) => updateDraft('active', e.target.value === 'active')}
                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
              >
                <option value="active">Active</option>
                <option value="paused">Paused</option>
              </select>
            </div>
          </div>

          <div className="space-y-4">
            <div className="flex items-center justify-between gap-3">
              <div>
                <h3 className="font-semibold text-foreground">Milestones</h3>
                <p className="text-sm text-foreground/60">Track progress items for the selected project.</p>
              </div>
              <button
                type="button"
                onClick={addMilestone}
                className="rounded-full px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition"
              >
                Add milestone
              </button>
            </div>

            {draft.milestones.length === 0 ? (
              <div className="rounded-3xl border border-earth-200 bg-slate-50 p-4 text-sm text-foreground/70">
                No milestones yet. Add a milestone to track updates and community progress.
              </div>
            ) : (
              <div className="space-y-4">
                {draft.milestones.map((milestone) => (
                  <div key={milestone.id} className="rounded-3xl border border-earth-200 bg-forest-50/40 p-4">
                    <div className="flex items-center justify-between gap-3 mb-3">
                      <div className="font-semibold text-foreground">{milestone.title}</div>
                      <button
                        type="button"
                        onClick={() => removeMilestone(milestone.id)}
                        className="rounded-full px-3 py-1 border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition"
                      >
                        Remove
                      </button>
                    </div>
                    <div className="grid gap-4 sm:grid-cols-3">
                      <input
                        value={milestone.title}
                        onChange={(e) => updateMilestone(milestone.id, 'title', e.target.value)}
                        placeholder="Milestone title"
                        className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                      />
                      <input
                        value={milestone.date}
                        onChange={(e) => updateMilestone(milestone.id, 'date', e.target.value)}
                        placeholder="Date"
                        className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                      />
                      <input
                        value={milestone.status ?? ''}
                        onChange={(e) => updateMilestone(milestone.id, 'status', e.target.value)}
                        placeholder="Status"
                        className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                      />
                    </div>
                    <textarea
                      rows={3}
                      value={milestone.description}
                      onChange={(e) => updateMilestone(milestone.id, 'description', e.target.value)}
                      placeholder="Description"
                      className="mt-4 w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                ))}
              </div>
            )}
          </div>

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div className="flex gap-2">
              <button
                type="button"
                onClick={() => void onDelete()}
                disabled={!selectedProject}
                className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition disabled:opacity-50"
              >
                Delete project
              </button>
              <button type="button" onClick={() => void onSave()} className="rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium">
                Save project
              </button>
            </div>
            <div className="text-sm text-foreground/60">{saveResult?.ok ? `Saved at ${new Date(saveResult.savedAt).toLocaleString()}` : saveResult?.error ?? 'Ready'}</div>
          </div>
        </div>
      </div>
    </div>
  );
}

