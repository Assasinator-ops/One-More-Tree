'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

import { Facebook, Instagram, Linkedin, Twitter, Youtube } from 'lucide-react';

type SocialPlatform = 'instagram' | 'twitter' | 'facebook' | 'linkedin' | 'youtube';

type SocialLink = {
  platform: SocialPlatform;
  url: string;
};

type SocialEmbed = {
  platform: SocialPlatform;
  embedUrl: string;
};

type SocialStore = {
  links: SocialLink[];
  embeds: SocialEmbed[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

type PlatformMeta = {
  platform: SocialPlatform;
  label: string;
  icon: (props: { className?: string }) => React.ReactElement;
};

const PLATFORM_META: PlatformMeta[] = [
  { platform: 'instagram', label: 'Instagram', icon: (p) => <Instagram {...p} /> },
  { platform: 'twitter', label: 'Twitter / X', icon: (p) => <Twitter {...p} /> },
  { platform: 'facebook', label: 'Facebook', icon: (p) => <Facebook {...p} /> },
  { platform: 'linkedin', label: 'LinkedIn', icon: (p) => <Linkedin {...p} /> },
  { platform: 'youtube', label: 'YouTube', icon: (p) => <Youtube {...p} /> }
];

function normalizeUrl(url: string) {
  return url.trim();
}

function clampIframeUrl(url: string) {
  return url.trim().slice(0, 5000);
}

function sanitizePlatform(platform: unknown): SocialPlatform | null {
  const s = String(platform ?? '').trim();
  return (['instagram', 'twitter', 'facebook', 'linkedin', 'youtube'] as SocialPlatform[]).includes(s as SocialPlatform)
    ? (s as SocialPlatform)
    : null;
}

function sanitizeStore(input: unknown): SocialStore {
  const safe: SocialStore = { links: [], embeds: [] };
  const obj = input as Partial<SocialStore> | null;
  if (!obj) return safe;

  const links = Array.isArray(obj.links) ? obj.links : [];
  const embeds = Array.isArray(obj.embeds) ? obj.embeds : [];

  safe.links = links
    .map((l: any) => ({ platform: sanitizePlatform(l?.platform), url: String(l?.url ?? '') }))
    .filter((x: any) => x.platform && x.url.trim().length > 0)
    .map((x: any) => ({ platform: x.platform as SocialPlatform, url: x.url.trim() }));

  safe.embeds = embeds
    .map((e: any) => ({ platform: sanitizePlatform(e?.platform), embedUrl: String(e?.embedUrl ?? '') }))
    .filter((x: any) => x.platform && x.embedUrl.trim().length > 0)
    .map((x: any) => ({ platform: x.platform as SocialPlatform, embedUrl: x.embedUrl.trim() }));

  return safe;
}

export default function SocialMediaAdmin() {
  const [loaded, setLoaded] = useState(false);
  const [store, setStore] = useState<SocialStore>({ links: [], embeds: [] });
  const [activePlatform, setActivePlatform] = useState<SocialPlatform>('instagram');

  const activeLink = useMemo(() => store.links.find((l) => l.platform === activePlatform) ?? null, [store, activePlatform]);
  const activeEmbed = useMemo(() => store.embeds.find((e) => e.platform === activePlatform) ?? null, [store, activePlatform]);

  const [draftLinkUrl, setDraftLinkUrl] = useState('');
  const [draftEmbedUrl, setDraftEmbedUrl] = useState('');
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<unknown>(AdminKeys.Social);
        const next = sanitizeStore(stored);
        if (!alive) return;
        setStore(next);
      } catch {
        if (!alive) return;
        setStore({ links: [], embeds: [] });
      } finally {
        if (alive) setLoaded(true);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  useEffect(() => {
    if (!loaded) return;
    setDraftLinkUrl(activeLink?.url ?? '');
    setDraftEmbedUrl(activeEmbed?.embedUrl ?? '');
    setSaveResult(null);
  }, [loaded, activePlatform, activeLink?.url, activeEmbed?.embedUrl]);

  const isDirty = useMemo(() => {
    return normalizeUrl(draftLinkUrl) !== normalizeUrl(activeLink?.url ?? '') || clampIframeUrl(draftEmbedUrl) !== (activeEmbed?.embedUrl ?? '').trim().slice(0, 5000);
  }, [draftLinkUrl, draftEmbedUrl, activeLink?.url, activeEmbed?.embedUrl]);

  const onSave = async () => {
    try {
      const linkUrl = normalizeUrl(draftLinkUrl);
      const embedUrl = clampIframeUrl(draftEmbedUrl);

      setStore((prev) => {
        const nextLinks = prev.links.filter((l) => l.platform !== activePlatform);
        const nextEmbeds = prev.embeds.filter((e) => e.platform !== activePlatform);

        if (linkUrl) nextLinks.unshift({ platform: activePlatform, url: linkUrl });
        if (embedUrl) nextEmbeds.unshift({ platform: activePlatform, embedUrl });

        return { links: nextLinks, embeds: nextEmbeds };
      });

      const next: SocialStore = {
        links: store.links.filter((l) => l.platform !== activePlatform).concat(linkUrl ? [{ platform: activePlatform, url: linkUrl }] : []),
        embeds: store.embeds
          .filter((e) => e.platform !== activePlatform)
          .concat(embedUrl ? [{ platform: activePlatform, embedUrl }] : [])
      };

      await adminApi.write(AdminKeys.Social, next);
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (e) {
      setSaveResult({ ok: false, error: e instanceof Error ? e.message : 'Unknown error' });
    }
  };

  const onClear = () => {
    setDraftLinkUrl('');
    setDraftEmbedUrl('');
    setSaveResult(null);
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Social Media Admin</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Social Media Admin</h2>
        <p className="text-foreground/70 mt-1">Manage footer social links and optional embeds.</p>
      </div>

      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="flex gap-2 flex-wrap">
          {PLATFORM_META.map((p) => {
            const Icon = p.icon;
            return (
              <button
                key={p.platform}
                type="button"
                onClick={() => setActivePlatform(p.platform)}
                className={
                  activePlatform === p.platform
                    ? 'rounded-full px-4 py-2 bg-forest-600 text-white shadow-medium'
                    : 'rounded-full px-4 py-2 bg-white/60 border border-earth-200 text-foreground/70 hover:text-foreground transition-colors'
                }
              >
                <span className="inline-flex items-center gap-2">
                  <Icon className="w-4 h-4" /> {p.label}
                </span>
              </button>
            );
          })}
        </div>
      </div>

      <div className="grid lg:grid-cols-2 gap-6">
        <div className="rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <h3 className="font-semibold text-foreground mb-3">Footer link</h3>
          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">URL</label>
            <input
              type="url"
              value={draftLinkUrl}
              onChange={(e) => setDraftLinkUrl(e.target.value)}
              placeholder="https://..."
              className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
            <p className="text-xs text-foreground/50">If empty, the footer icon will be hidden for this platform.</p>
          </div>

          <div className="mt-5 rounded-2xl border border-earth-200/80 bg-white/70 p-4">
            <div className="flex items-center justify-between gap-3">
              <div className="inline-flex items-center gap-3">
                {(() => {
                  const Icon = PLATFORM_META.find((p) => p.platform === activePlatform)?.icon;
                  return Icon ? <Icon className="w-5 h-5 text-forest-700" /> : null;
                })()}
                <div>
                  <div className="text-sm font-semibold text-foreground/80">Active platform</div>
                  <div className="text-xs text-foreground/50">{activePlatform}</div>
                </div>
              </div>
              <div className="text-xs text-foreground/50">Preview</div>
            </div>
          </div>
        </div>

        <div className="rounded-3xl border border-earth-200 bg-white/50 p-4 sm:p-5">
          <h3 className="font-semibold text-foreground mb-3">Optional embed</h3>
          <div className="space-y-2">
            <label className="text-sm font-medium text-foreground/80">Embed URL (iframe src)</label>
            <input
              type="url"
              value={draftEmbedUrl}
              onChange={(e) => setDraftEmbedUrl(e.target.value)}
              placeholder="https://www.youtube.com/embed/..."
              className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
            />
            <p className="text-xs text-foreground/50">Footer will render an iframe with this URL.</p>
          </div>

          {saveResult ? (
            <div
              className={
                saveResult.ok
                  ? 'mt-4 rounded-xl border border-forest-200 bg-forest-50/60 text-forest-900 px-4 py-3'
                  : 'mt-4 rounded-xl border border-red-200 bg-red-50/60 text-red-900 px-4 py-3'
              }
            >
              {saveResult.ok ? <>Saved at {new Date(saveResult.savedAt).toLocaleString()}</> : <>Save failed: {saveResult.error}</>}
            </div>
          ) : null}

          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4">
            <div className="flex gap-2">
              <button
                type="button"
                onClick={onClear}
                className="rounded-xl px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
              >
                Clear
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
                Save
              </button>
            </div>
            <div className="text-sm text-foreground/60">{isDirty ? 'Unsaved changes' : 'Ready'}</div>
          </div>
        </div>
      </div>
    </div>
  );
}

