'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type StoryChapterKey = 'seed' | 'growth' | 'mature';

type StoryFeature = {
  icon: string;
  label: string;
  desc: string;
};

type StoryHighlight = {
  value: string;
  title: string;
  subtitle: string;
};

type StoryChapter = {
  key: StoryChapterKey;
  chapterLabel: string;
  headline: string;
  headlineEmphasis?: string;
  descriptions: [string, string];
  imageSrc: string;
  imageAlt: string;
  infoCards: StoryFeature[];
  highlight?: StoryHighlight;
};

type StoriesDraft = {
  chapters: StoryChapter[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const MAX_CARDS = 4;

const defaultDraft = (): StoriesDraft => ({
  chapters: [
    {
      key: 'seed',
      chapterLabel: 'Chapter One',
      headline: 'It Starts With a',
      headlineEmphasis: 'Single Seed',
      descriptions: [
        'Beneath the soil, magic happens. A tiny seed, no larger than a grain of rice, holds within it the blueprint for a towering forest giant.',
        'With patience, care, and the right conditions, life emerges. This is where our journey begins.'
      ],
      imageSrc: '/seedling.jpg',
      imageAlt: 'A seedling sprouting from soil',
      infoCards: [
        { icon: 'Droplets', label: 'Water', desc: 'Life source' },
        { icon: 'Sun', label: 'Sunlight', desc: 'Energy' },
        { icon: 'Sprout', label: 'Care', desc: 'Nurturing' }
      ]
    },
    {
      key: 'growth',
      chapterLabel: 'Chapter Two',
      headline: 'Growing Through',
      headlineEmphasis: 'Adversity',
      descriptions: [
        'Growth isn’t always easy. Our young trees face countless challenges—harsh weather, hungry animals, and the constant struggle for sunlight and nutrients.',
        'Our teams provide the protection, irrigation, and care that transforms vulnerable saplings into resilient young trees.'
      ],
      imageSrc: '/sapling.jpg',
      imageAlt: 'A young tree growing',
      infoCards: [
        { icon: 'Wind', label: 'Harsh Weather', desc: 'Storms, droughts, and extremes' },
        { icon: 'Shield', label: 'Protection Needed', desc: 'Guards against animals and pests' },
        { icon: 'Heart', label: 'Continued Care', desc: 'Regular monitoring and maintenance' }
      ],
      highlight: { value: 'Year 3', title: 'Growth Progress', subtitle: 'Finding strength' }
    },
    {
      key: 'mature',
      chapterLabel: 'Chapter Three',
      headline: 'A Legacy That',
      headlineEmphasis: 'Lasts Generations',
      descriptions: [
        'Twenty-five years later, that tiny seedling has become a towering giant. Its branches stretch toward the sky, providing shade, shelter, and life to countless creatures.',
        'A single mature tree can absorb 48 pounds of carbon dioxide per year, produce enough oxygen for two people, and provide habitat for over 100 species.'
      ],
      imageSrc: '/mature-tree.jpg',
      imageAlt: 'A majestic mature tree',
      infoCards: [
        { icon: 'Bird', label: 'Wildlife Habitat', desc: 'Home to 100+ species' },
        { icon: 'CloudRain', label: 'Carbon Capture', desc: '48 lbs CO₂/year' },
        { icon: 'Users', label: 'Community', desc: 'Shade & gathering' },
        { icon: 'TreePine', label: 'Ecosystem', desc: 'Supports biodiversity' }
      ],
      highlight: { value: '50', title: 'Years of impact', subtitle: 'Legacy in the making' }
    }
  ]
});

function sanitizeDraft(draft: StoriesDraft): StoriesDraft {
  return {
    chapters: (draft.chapters ?? []).map((chapter) => ({
      key: chapter.key,
      chapterLabel: String(chapter.chapterLabel ?? '').trim() || 'Chapter',
      headline: String(chapter.headline ?? '').trim() || 'Story',
      headlineEmphasis: String(chapter.headlineEmphasis ?? '').trim() || undefined,
      descriptions: [
        String(chapter.descriptions?.[0] ?? '').trim() || '...',
        String(chapter.descriptions?.[1] ?? '').trim() || '...'
      ],
      imageSrc: String(chapter.imageSrc ?? '').trim() || '/seedling.jpg',
      imageAlt: String(chapter.imageAlt ?? '').trim() || 'Story image',
      infoCards: Array.isArray(chapter.infoCards)
        ? chapter.infoCards
            .slice(0, MAX_CARDS)
            .map((card) => ({
              icon: String(card.icon ?? '').trim() || 'Leaf',
              label: String(card.label ?? '').trim() || 'Feature',
              desc: String(card.desc ?? '').trim() || 'Detail'
            }))
        : [],
      highlight: chapter.highlight
        ? {
            value: String(chapter.highlight.value ?? '').trim() || '',
            title: String(chapter.highlight.title ?? '').trim() || '',
            subtitle: String(chapter.highlight.subtitle ?? '').trim() || ''
          }
        : undefined
    }))
  };
}

export default function StoriesAdmin() {
  const [draft, setDraft] = useState<StoriesDraft>(() => defaultDraft());
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const [initialSnapshot, setInitialSnapshot] = useState('');

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<StoriesDraft>(AdminKeys.Stories);
        if (!alive) return;
        const next = sanitizeDraft(stored ?? defaultDraft());
        setDraft(next);
        setInitialSnapshot(JSON.stringify(next));
      } catch {
        if (!alive) return;
        const next = sanitizeDraft(defaultDraft());
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

  const snapshot = useMemo(() => JSON.stringify(draft), [draft]);

  const isDirty = useMemo(() => {
    if (!loaded) return false;
    return snapshot !== initialSnapshot;
  }, [loaded, snapshot, initialSnapshot]);

  const updateChapter = (key: StoryChapterKey, field: keyof StoryChapter, value: string | string[]) => {
    setDraft((prev) => ({
      ...prev,
      chapters: prev.chapters.map((chapter) =>
        chapter.key === key
          ? ({
              ...chapter,
              [field]: value
            } as StoryChapter)
          : chapter
      )
    }));
    setSaveResult(null);
  };

  const updateCard = (
    chapterKey: StoryChapterKey,
    index: number,
    field: keyof StoryFeature,
    value: string
  ) => {
    setDraft((prev) => ({
      ...prev,
      chapters: prev.chapters.map((chapter) =>
        chapter.key === chapterKey
          ? {
              ...chapter,
              infoCards: chapter.infoCards.map((card, idx) => (idx === index ? { ...card, [field]: value } : card))
            }
          : chapter
      )
    }));
    setSaveResult(null);
  };

  const updateHighlight = (chapterKey: StoryChapterKey, field: keyof StoryHighlight, value: string) => {
    setDraft((prev) => ({
      ...prev,
      chapters: prev.chapters.map((chapter) =>
        chapter.key === chapterKey
          ? {
              ...chapter,
              highlight: {
                ...(chapter.highlight ?? { value: '', title: '', subtitle: '' }),
                [field]: value
              }
            }
          : chapter
      )
    }));
    setSaveResult(null);
  };

  const onSave = async () => {
    try {
      const next = sanitizeDraft(draft);
      await adminApi.write(AdminKeys.Stories, next);
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (error) {
      setSaveResult({
        ok: false,
        error: error instanceof Error ? error.message : 'Unknown error'
      });
    }
  };

  const onReset = () => {
    setSaveResult(null);
    const base = sanitizeDraft(defaultDraft());
    setDraft(base);
    setInitialSnapshot(JSON.stringify(base));
  };

  if (!loaded) {
    return (
      <div className="space-y-6">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Stories from the field</h2>
        </div>
        <div className="text-foreground/70">Loading…</div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Stories from the field</h2>
        <p className="text-foreground/70 mt-1">Admin editor for the three story chapters shown on the Impact page.</p>
      </div>

      <div className="rounded-3xl glass-card border border-earth-200 p-4 sm:p-6 space-y-6">
        {draft.chapters.map((chapter) => (
          <div key={chapter.key} className="rounded-3xl border border-earth-200 bg-white/70 p-5 space-y-5">
            <div className="flex items-center justify-between gap-4">
              <div>
                <div className="text-sm font-semibold text-foreground">{chapter.chapterLabel}</div>
                <div className="text-xs text-foreground/50">{chapter.key} chapter</div>
              </div>
              {chapter.highlight ? (
                <div className="rounded-2xl bg-forest-50 px-3 py-2 text-sm text-forest-700">
                  Highlight: {chapter.highlight.value}
                </div>
              ) : null}
            </div>

            <div className="grid lg:grid-cols-2 gap-4">
              <div className="space-y-4">
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Chapter label</label>
                  <input
                    value={chapter.chapterLabel}
                    onChange={(e) => updateChapter(chapter.key, 'chapterLabel', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Headline</label>
                  <input
                    value={chapter.headline}
                    onChange={(e) => updateChapter(chapter.key, 'headline', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Headline emphasis</label>
                  <input
                    value={chapter.headlineEmphasis ?? ''}
                    onChange={(e) => updateChapter(chapter.key, 'headlineEmphasis', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Image source</label>
                  <input
                    value={chapter.imageSrc}
                    onChange={(e) => updateChapter(chapter.key, 'imageSrc', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Image alt</label>
                  <input
                    value={chapter.imageAlt}
                    onChange={(e) => updateChapter(chapter.key, 'imageAlt', e.target.value)}
                    className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Description paragraph 1</label>
                  <textarea
                    rows={3}
                    value={chapter.descriptions[0]}
                    onChange={(e) => updateChapter(chapter.key, 'descriptions', [e.target.value, chapter.descriptions[1]])}
                    className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
                <div className="space-y-2">
                  <label className="text-sm font-medium text-foreground/80">Description paragraph 2</label>
                  <textarea
                    rows={3}
                    value={chapter.descriptions[1]}
                    onChange={(e) => updateChapter(chapter.key, 'descriptions', [chapter.descriptions[0], e.target.value])}
                    className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                  />
                </div>
              </div>

              <div className="space-y-4">
                <div className="space-y-2">
                  <h3 className="font-semibold text-foreground">Info cards</h3>
                  <p className="text-sm text-foreground/60">Card content shown on the public Impact page.</p>
                </div>

                <div className="grid gap-4">
                  {chapter.infoCards.map((card, idx) => (
                    <div key={idx} className="rounded-2xl border border-earth-200 bg-earth-50 p-4">
                      <div className="text-xs uppercase tracking-[0.25em] text-foreground/50 mb-2">Card {idx + 1}</div>
                      <input
                        value={card.icon}
                        onChange={(e) => updateCard(chapter.key, idx, 'icon', e.target.value)}
                        placeholder="Icon name"
                        className="w-full rounded-xl border border-earth-200 bg-white px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                      />
                      <input
                        value={card.label}
                        onChange={(e) => updateCard(chapter.key, idx, 'label', e.target.value)}
                        placeholder="Label"
                        className="w-full rounded-xl border border-earth-200 bg-white px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200 mt-2"
                      />
                      <textarea
                        rows={2}
                        value={card.desc}
                        onChange={(e) => updateCard(chapter.key, idx, 'desc', e.target.value)}
                        placeholder="Description"
                        className="w-full rounded-2xl border border-earth-200 bg-white px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200 mt-2"
                      />
                    </div>
                  ))}
                </div>

                <div className="grid gap-4 lg:grid-cols-3">
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Highlight value</label>
                    <input
                      value={chapter.highlight?.value ?? ''}
                      onChange={(e) => updateHighlight(chapter.key, 'value', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Highlight title</label>
                    <input
                      value={chapter.highlight?.title ?? ''}
                      onChange={(e) => updateHighlight(chapter.key, 'title', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                  <div className="space-y-2">
                    <label className="text-sm font-medium text-foreground/80">Highlight subtitle</label>
                    <input
                      value={chapter.highlight?.subtitle ?? ''}
                      onChange={(e) => updateHighlight(chapter.key, 'subtitle', e.target.value)}
                      className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        ))}

        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
          <div className="flex gap-2">
            <button
              type="button"
              onClick={onReset}
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
              Save stories
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

