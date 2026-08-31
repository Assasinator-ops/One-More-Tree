import { useEffect, useMemo, useState } from 'react';

type ModuleStatus = {
  key: string;
  label: string;
  enabled: boolean;
  updatedAt?: string;
};


const MODULE_OPTIONS: Array<{ key: string; label: string }> = [
  { key: 'impact', label: 'Impact section' },
  { key: 'team', label: 'Team section' },
  { key: 'stories', label: 'Stories section' },
  { key: 'liveMap', label: 'Live map / projects' },
  { key: 'projects', label: 'Active projects page' },
  { key: 'faq', label: 'FAQ page' },
  { key: 'donations', label: 'Donations page' },
  { key: 'contact', label: 'Contact page' },
  { key: 'newsletter', label: 'Newsletter sign-up' },
];

function optimisticDefaultModuleStatus(): ModuleStatus[] {
  return MODULE_OPTIONS.map((module) => ({
    ...module,
    enabled: true,
    updatedAt: new Date().toISOString()
  }));
}


async function persistModuleStatus(items: ModuleStatus[]) {
  const enabledMap = Object.fromEntries(items.map((m) => [m.key, Boolean(m.enabled)]));
  const res = await fetch('/api/admin/data', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key: 'modules', value: enabledMap })
  });
  const json = (await res.json().catch(() => ({}))) as { ok?: boolean; message?: string };
  if (!res.ok || !json.ok) {
    throw new Error(json.message ?? 'Failed to save modules');
  }
}


export default function ModuleStatusAdmin() {
  const [modules, setModules] = useState<ModuleStatus[]>([]);
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<string | null>(null);
  const [maintenanceEnabled, setMaintenanceEnabled] = useState(false);
  const [maintenanceResult, setMaintenanceResult] = useState<string | null>(null);
  const [underConstructionEnabled, setUnderConstructionEnabled] = useState(false);
  const [underConstructionResult, setUnderConstructionResult] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      const maintenanceRes = await fetch('/api/admin/data?key=maintenance', { cache: 'no-store' });
      const maintenanceJson = (await maintenanceRes.json().catch(() => ({}))) as { ok?: boolean; value?: unknown };
      const maintenanceEnabled = maintenanceJson.ok ? Boolean(maintenanceJson.value) : false;

      const underConstructionRes = await fetch('/api/admin/data?key=under_construction', { cache: 'no-store' });
      const underConstructionJson = (await underConstructionRes.json().catch(() => ({}))) as { ok?: boolean; value?: unknown };
      const underConstructionEnabled = underConstructionJson.ok ? Boolean(underConstructionJson.value) : false;

      setModules(optimisticDefaultModuleStatus());

      setMaintenanceEnabled(maintenanceEnabled);
      setUnderConstructionEnabled(underConstructionEnabled);
      setLoaded(true);
    })();
  }, []);


  const enabledCount = useMemo(() => modules.filter((item) => item.enabled).length, [modules]);

  const toggleModule = (key: string) => {
    setModules((prev) => prev.map((module) => (module.key === key ? { ...module, enabled: !module.enabled } : module)));
    setSaveResult(null);
  };

  const onSave = () => {
    try {
      const next = modules.map((module) => ({
        ...module,
        updatedAt: new Date().toISOString(),
      }));
      persistModuleStatus(next);
      setModules(next);
      setSaveResult('Saved successfully.');
    } catch {
      setSaveResult('Save failed. Please try again.');
    }
  };

  const toggleMaintenanceMode = async () => {
    const next = !maintenanceEnabled;
    setSaveResult(null);
    try {
      const res = await fetch('/api/admin/maintenance', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ enabled: next })
      });
      const json = (await res.json().catch(() => ({}))) as { ok?: boolean; message?: string };
      if (!res.ok || !json.ok) {
        throw new Error(json.message ?? 'Failed to update maintenance');
      }
      setMaintenanceEnabled(next);
      setMaintenanceResult(
        next
          ? 'Maintenance mode enabled. Public pages will now show the maintenance page.'
          : 'Maintenance mode disabled. Public website is live again.'
      );
      setSaveResult(null);
    } catch (e) {
      const msg = e instanceof Error ? e.message : 'Failed to update maintenance';
      setMaintenanceResult(msg);
      setSaveResult('Save failed. Please try again.');
    }
  };

  const toggleUnderConstructionMode = async () => {
    const next = !underConstructionEnabled;
    setSaveResult(null);
    try {
      const res = await fetch('/api/admin/under-construction', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ enabled: next })
      });
      const json = (await res.json().catch(() => ({}))) as { ok?: boolean; message?: string };
      if (!res.ok || !json.ok) {
        throw new Error(json.message ?? 'Failed to update under construction');
      }
      setUnderConstructionEnabled(next);
      setUnderConstructionResult(
        next
          ? 'Under construction watermark enabled. Website will show preview watermark.'
          : 'Under construction watermark disabled. Website is fully live.'
      );
      setSaveResult(null);
    } catch (e) {
      const msg = e instanceof Error ? e.message : 'Failed to update under construction';
      setUnderConstructionResult(msg);
      setSaveResult('Save failed. Please try again.');
    }
  };


  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Module Visibility</h2>
        <p className="text-foreground/70 mt-1">
          Enable or disable page-level modules and sections. This controls whether the public UI renders each area.
        </p>
      </div>

      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 className="font-semibold text-foreground">Global website maintenance</h2>
            <p className="text-foreground/70 mt-1">
              Disable the whole public website and show a maintenance page. Admin routes remain available while maintenance mode is active.
            </p>
          </div>
          <button
            type="button"
            onClick={toggleMaintenanceMode}
            className={
              maintenanceEnabled
                ? 'rounded-2xl bg-forest-600 px-4 py-3 text-white hover:bg-forest-700 transition'
                : 'rounded-2xl bg-red-600 px-4 py-3 text-white hover:bg-red-700 transition'
            }
          >
            {maintenanceEnabled ? 'Enable public website' : 'Disable public website'}
          </button>
        </div>
        <div className="mt-4 text-sm text-foreground/70">
          {maintenanceEnabled
            ? 'Maintenance mode is active. Only /admin is accessible until you re-enable the website.'
            : 'Public pages are live. Click the button to disable them and show a maintenance page.'}
        </div>
        {maintenanceResult ? (
          <div className="mt-4 rounded-2xl border border-forest-200 bg-forest-50 px-4 py-3 text-forest-900">
            {maintenanceResult}
          </div>
        ) : null}
      </div>
      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6">
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 className="font-semibold text-foreground">Under construction watermark</h2>
            <p className="text-foreground/70 mt-1">
              Show a "Under Construction" watermark on the website. Users can still navigate the site normally, but it will be marked as preview only.
            </p>
          </div>
          <button
            type="button"
            onClick={toggleUnderConstructionMode}
            className={
              underConstructionEnabled
                ? 'rounded-2xl bg-forest-600 px-4 py-3 text-white hover:bg-forest-700 transition'
                : 'rounded-2xl bg-amber-600 px-4 py-3 text-white hover:bg-amber-700 transition'
            }
          >
            {underConstructionEnabled ? 'Disable watermark' : 'Enable watermark'}
          </button>
        </div>
        <div className="mt-4 text-sm text-foreground/70">
          {underConstructionEnabled
            ? 'Watermark is active. Website shows "Under Construction" overlay to visitors.'
            : 'Watermark is disabled. Website appears normally to visitors.'}
        </div>
        {underConstructionResult ? (
          <div className="mt-4 rounded-2xl border border-forest-200 bg-forest-50 px-4 py-3 text-forest-900">
            {underConstructionResult}
          </div>
        ) : null}
      </div>
      <div className="rounded-3xl border border-earth-200 bg-white/50 p-5 sm:p-6">
        {!loaded ? (
          <div className="text-foreground/70">Loading…</div>
        ) : (
          <div className="space-y-4">
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {modules.map((module) => (
                <button
                  key={module.key}
                  type="button"
                  onClick={() => toggleModule(module.key)}
                  className={
                    module.enabled
                      ? 'rounded-2xl border border-forest-400/30 bg-forest-600/10 px-4 py-3 text-left hover:bg-forest-600/15 transition'
                      : 'rounded-2xl border border-earth-200 bg-white px-4 py-3 text-left hover:bg-slate-50 transition'
                  }
                >
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <div className="font-semibold text-foreground">{module.label}</div>
                      <div className="text-xs text-foreground/60">{module.key}</div>
                    </div>
                    <span className={module.enabled ? 'text-forest-700 font-semibold' : 'text-foreground/60'}>
                      {module.enabled ? 'Enabled' : 'Disabled'}
                    </span>
                  </div>
                </button>
              ))}
            </div>

            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4">
              <div className="text-sm text-foreground/60">
                {enabledCount} of {modules.length} modules enabled.
              </div>
              <button
                type="button"
                onClick={onSave}
                className="rounded-xl px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white transition-colors shadow-medium"
              >
                Save modules
              </button>
            </div>

            {saveResult ? (
              <div className="rounded-xl border border-forest-200 bg-forest-50/60 text-forest-900 px-4 py-3">{saveResult}</div>
            ) : null}
          </div>
        )}
      </div>
    </div>
  );
}
