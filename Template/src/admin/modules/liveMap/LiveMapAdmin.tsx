'use client';

import { useEffect, useMemo, useState } from 'react';

import { adminApi, AdminKeys } from '@/lib/adminApi';

type ProgressImage = {
  title: string;
  description: string;
  image: string;
  date: string;
};

type TreeEvent = {
  id: string;
  title: string;
  description: string;
  eventType: 'planted' | 'protected' | 'irrigated' | 'growth_stage';
  stageKey?: 'seed' | 'growth' | 'mature';
  count: number;
  date: string;
  image?: string;
  verifiedBy?: string;
};

type LiveMapLocation = {
  id: string;
  name: string;
  region: string;
  description: string;
  coords: { x: string; y: string };
  treesPlanted: number;
  volunteers: number;
  progressImages: ProgressImage[];
  events: TreeEvent[];
};

type LiveMapDraft = {
  sectionTitle: string;
  headline: string;
  description: string;
  locations: LiveMapLocation[];
};

type SaveResult = { ok: true; savedAt: string } | { ok: false; error: string };

const MAX_LOCATIONS = 6;
const MAX_PROGRESS_IMAGES = 3;

const defaultDraft = (): LiveMapDraft => ({
  sectionTitle: 'Live Tree Map',
  headline: 'Track Trees in Real Time',
  description: 'Click markers to explore progress images, planting milestones, and real planting stories across Europe.',
  locations: [
    {
      id: 'location-1',
      name: 'Sierra Nevada',
      region: 'Spain',
      description: 'A planting site where local volunteers regenerated a degraded hillside.',
      coords: { x: '42%', y: '34%' },
      treesPlanted: 320,
      volunteers: 23,
      progressImages: [
        {
          title: 'First planting',
          description: 'The first 50 saplings were planted with local families.',
          image: '/seedling.jpg',
          date: '2025-03-10',
        },
      ],
      events: [
        {
          id: 'event-1',
          title: 'Initial planting day',
          description: 'Local volunteers planted the first wave of saplings.',
          eventType: 'planted',
          stageKey: 'seed',
          count: 50,
          date: '2025-03-10',
          image: '/seedling.jpg',
          verifiedBy: 'Project Coordinator',
        },
      ],
    },
  ],
});

function safeNumber(value: unknown, fallback = 0) {
  const asNumber = Number(value);
  return Number.isFinite(asNumber) ? asNumber : fallback;
}

function sanitizeProgressImage(image: Partial<ProgressImage>): ProgressImage {
  return {
    title: String(image?.title ?? '').trim() || 'Progress update',
    description: String(image?.description ?? '').trim() || 'Milestone description',
    image: String(image?.image ?? '').trim() || '/seedling.jpg',
    date: String(image?.date ?? '').trim() || new Date().toISOString().slice(0, 10),
  };
}

function sanitizeLocation(location: Partial<LiveMapLocation>, index: number): LiveMapLocation {
  return {
    id: String(location?.id ?? `location-${index + 1}`).trim() || `location-${index + 1}`,
    name: String(location?.name ?? '').trim() || 'Untitled location',
    region: String(location?.region ?? '').trim() || 'Region',
    description: String(location?.description ?? '').trim() || 'Location description',
    coords: {
      x: String(location?.coords?.x ?? '').trim() || '50%',
      y: String(location?.coords?.y ?? '').trim() || '50%',
    },
    treesPlanted: safeNumber(location?.treesPlanted, 0),
    volunteers: safeNumber(location?.volunteers, 0),
    progressImages: Array.isArray(location?.progressImages)
      ? (location.progressImages as Partial<ProgressImage>[])
          .filter(Boolean)
          .slice(0, MAX_PROGRESS_IMAGES)
          .map(sanitizeProgressImage)
      : [
          {
            title: 'Progress update',
            description: 'Milestone description',
            image: '/seedling.jpg',
            date: new Date().toISOString().slice(0, 10),
          },
        ],
    events: Array.isArray(location?.events)
      ? (location.events as Partial<TreeEvent>[])
          .filter(Boolean)
          .map((event, evIndex) => {
            const id = String(event?.id ?? `event-${index + 1}-${evIndex + 1}`).trim() || `event-${index + 1}-${evIndex + 1}`;
            const eventTypeRaw = String(event?.eventType ?? 'planted');
            const eventType =
              ['planted', 'protected', 'irrigated', 'growth_stage'].includes(eventTypeRaw) ? (eventTypeRaw as TreeEvent['eventType']) : 'planted';

            const stageKeyRaw = String(event?.stageKey ?? '');
            const stageKey = ['seed', 'growth', 'mature'].includes(stageKeyRaw) ? (stageKeyRaw as TreeEvent['stageKey']) : undefined;

            return {
              id,
              title: String(event?.title ?? '').trim() || 'Update event',
              description: String(event?.description ?? '').trim() || 'Event description',
              eventType,
              stageKey,
              count: safeNumber(event?.count, 0),
              date: String(event?.date ?? '').trim() || new Date().toISOString().slice(0, 10),
              image: typeof event?.image === 'string' ? event.image : undefined,
              verifiedBy: typeof event?.verifiedBy === 'string' ? event.verifiedBy : undefined,
            };
          })
      : [],
  };
}

function sanitizeDraft(draft: LiveMapDraft): LiveMapDraft {
  return {
    sectionTitle: String(draft.sectionTitle ?? '').trim() || 'Live Tree Map',
    headline: String(draft.headline ?? '').trim() || 'Track Trees in Real Time',
    description:
      String(draft.description ?? '').trim() ||
      'Click markers to explore progress images, planting milestones, and real planting stories across Europe.',
    locations: Array.isArray(draft.locations)
      ? draft.locations.slice(0, MAX_LOCATIONS).map((location, index) => sanitizeLocation(location, index))
      : [sanitizeLocation({}, 0)],
  };
}

export default function LiveMapAdmin() {
  const [draft, setDraft] = useState<LiveMapDraft>(() => defaultDraft());
  const [loaded, setLoaded] = useState(false);
  const [saveResult, setSaveResult] = useState<SaveResult | null>(null);
  const [initialSnapshot, setInitialSnapshot] = useState('');

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const stored = await adminApi.read<LiveMapDraft>(AdminKeys.LiveMap);
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

  const updateField = (field: keyof LiveMapDraft, value: string) => {
    setDraft((prev) => ({ ...prev, [field]: value }));
    setSaveResult(null);
  };

  const updateLocationField = (
    locationId: string,
    field: keyof LiveMapLocation,
    value: string | number | { x: string; y: string },
  ) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId ? ({ ...location, [field]: value } as LiveMapLocation) : location,
      ),
    }));
    setSaveResult(null);
  };

  const addLocation = () => {
    setDraft((prev) => {
      if (prev.locations.length >= MAX_LOCATIONS) return prev;
      return {
        ...prev,
        locations: [
          ...prev.locations,
          {
            id: `location-${prev.locations.length + 1}`,
            name: 'New location',
            region: 'Region',
            description: 'Describe this planting site',
            coords: { x: '50%', y: '50%' },
            treesPlanted: 0,
            volunteers: 0,
            progressImages: [
              {
                title: 'Progress update',
                description: 'Milestone description',
                image: '/seedling.jpg',
                date: new Date().toISOString().slice(0, 10),
              },
            ],
            events: [],
          },
        ],
      };
    });
    setSaveResult(null);
  };

  const removeLocation = (locationId: string) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.filter((location) => location.id !== locationId),
    }));
    setSaveResult(null);
  };

  const updateProgressImage = (
    locationId: string,
    imageIndex: number,
    field: keyof ProgressImage,
    value: string,
  ) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId
          ? ({
              ...location,
              progressImages: location.progressImages.map((image, index) =>
                index === imageIndex ? { ...image, [field]: value } : image,
              ),
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const addProgressImage = (locationId: string) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId && location.progressImages.length < MAX_PROGRESS_IMAGES
          ? ({
              ...location,
              progressImages: [
                ...location.progressImages,
                {
                  title: 'Progress update',
                  description: 'Milestone description',
                  image: '/seedling.jpg',
                  date: new Date().toISOString().slice(0, 10),
                },
              ],
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const removeProgressImage = (locationId: string, imageIndex: number) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId
          ? ({
              ...location,
              progressImages: location.progressImages.filter((_, index) => index !== imageIndex),
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const updateEventField = (
    locationId: string,
    eventId: string,
    field: keyof TreeEvent,
    value: string | number | undefined,
  ) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId
          ? ({
              ...location,
              events: location.events.map((event) =>
                event.id === eventId
                  ? ({
                      ...event,
                      [field]: field === 'count' ? safeNumber(value, 0) : value,
                    } as TreeEvent)
                  : event,
              ),
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const addEvent = (locationId: string) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId
          ? ({
              ...location,
              events: [
                ...location.events,
                {
                  id: `event-${location.events.length + 1}`,
                  title: 'New event',
                  description: 'Describe the milestone or update.',
                  eventType: 'planted',
                  stageKey: 'seed',
                  count: 0,
                  date: new Date().toISOString().slice(0, 10),
                  image: '/seedling.jpg',
                  verifiedBy: '',
                },
              ],
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const removeEvent = (locationId: string, eventId: string) => {
    setDraft((prev) => ({
      ...prev,
      locations: prev.locations.map((location) =>
        location.id === locationId
          ? ({
              ...location,
              events: location.events.filter((event) => event.id !== eventId),
            } as LiveMapLocation)
          : location,
      ),
    }));
    setSaveResult(null);
  };

  const onSave = async () => {
    try {
      const next = sanitizeDraft(draft);
      await adminApi.write(AdminKeys.LiveMap, next);
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setSaveResult({ ok: true, savedAt: new Date().toISOString() });
    } catch (error) {
      setSaveResult({ ok: false, error: error instanceof Error ? error.message : 'Unknown error' });
    }
  };

  const onReset = async () => {
    try {
      const stored = await adminApi.read<LiveMapDraft>(AdminKeys.LiveMap);
      const next = sanitizeDraft(stored ?? defaultDraft());
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setSaveResult(null);
    } catch {
      const next = sanitizeDraft(defaultDraft());
      setDraft(next);
      setInitialSnapshot(JSON.stringify(next));
      setSaveResult(null);
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h2 className="font-serif text-2xl font-bold text-foreground">Live map editor</h2>
        <p className="text-foreground/70 mt-1">Configure the live tree map section and planting locations.</p>
      </div>

      <div className="rounded-3xl glass-card border border-earth-200 p-4 sm:p-6 space-y-6">
        {!loaded ? (
          <div className="text-foreground/70">Loading…</div>
        ) : (
          <>
            <div className="grid gap-4 lg:grid-cols-3">
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Section title</label>
                <input
                  value={draft.sectionTitle}
                  onChange={(e) => updateField('sectionTitle', e.target.value)}
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Section headline</label>
                <input
                  value={draft.headline}
                  onChange={(e) => updateField('headline', e.target.value)}
                  className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>
              <div className="space-y-2">
                <label className="text-sm font-medium text-foreground/80">Section description</label>
                <textarea
                  rows={3}
                  value={draft.description}
                  onChange={(e) => updateField('description', e.target.value)}
                  className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                />
              </div>
            </div>

            <div className="space-y-6">
              {draft.locations.map((location) => (
                <div key={location.id} className="rounded-3xl border border-earth-200 bg-white/80 p-5 space-y-5">
                  <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                      <div className="text-sm font-semibold text-foreground">Location ID</div>
                      <div className="text-xs text-foreground/60">{location.id}</div>
                    </div>
                    <button
                      type="button"
                      onClick={() => removeLocation(location.id)}
                      className="rounded-full px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
                    >
                      Remove location
                    </button>
                  </div>

                  <div className="grid gap-4 lg:grid-cols-2">
                    <div className="space-y-4">
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-foreground/80">Location name</label>
                        <input
                          value={location.name}
                          onChange={(e) => updateLocationField(location.id, 'name', e.target.value)}
                          className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                        />
                      </div>
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-foreground/80">Region</label>
                        <input
                          value={location.region}
                          onChange={(e) => updateLocationField(location.id, 'region', e.target.value)}
                          className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                        />
                      </div>
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-foreground/80">Trees planted</label>
                        <input
                          type="number"
                          min="0"
                          value={location.treesPlanted}
                          onChange={(e) => updateLocationField(location.id, 'treesPlanted', safeNumber(e.target.value, 0))}
                          className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                        />
                      </div>
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-foreground/80">Volunteers</label>
                        <input
                          type="number"
                          min="0"
                          value={location.volunteers}
                          onChange={(e) => updateLocationField(location.id, 'volunteers', safeNumber(e.target.value, 0))}
                          className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                        />
                      </div>
                    </div>

                    <div className="space-y-4">
                      <div className="space-y-2">
                        <label className="text-sm font-medium text-foreground/80">Description</label>
                        <textarea
                          rows={4}
                          value={location.description}
                          onChange={(e) => updateLocationField(location.id, 'description', e.target.value)}
                          className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                        />
                      </div>
                      <div className="grid gap-4 sm:grid-cols-2">
                        <div className="space-y-2">
                          <label className="text-sm font-medium text-foreground/80">Coord X</label>
                          <input
                            value={location.coords.x}
                            onChange={(e) =>
                              updateLocationField(location.id, 'coords', {
                                ...location.coords,
                                x: e.target.value,
                              })
                            }
                            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                        </div>
                        <div className="space-y-2">
                          <label className="text-sm font-medium text-foreground/80">Coord Y</label>
                          <input
                            value={location.coords.y}
                            onChange={(e) =>
                              updateLocationField(location.id, 'coords', {
                                ...location.coords,
                                y: e.target.value,
                              })
                            }
                            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="space-y-4">
                    <div className="flex items-center justify-between gap-3">
                      <div>
                        <h3 className="text-base font-semibold text-foreground">Progress images</h3>
                        <p className="text-sm text-foreground/60">Add up to {MAX_PROGRESS_IMAGES} progress cards for this site.</p>
                      </div>
                      <button
                        type="button"
                        onClick={() => addProgressImage(location.id)}
                        disabled={location.progressImages.length >= MAX_PROGRESS_IMAGES}
                        className="rounded-full px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors disabled:cursor-not-allowed disabled:opacity-50"
                      >
                        Add image
                      </button>
                    </div>

                    {location.progressImages.map((image, index) => (
                      <div key={`${location.id}-image-${index}`} className="rounded-3xl border border-earth-200 bg-forest-50/40 p-4">
                        <div className="flex items-start justify-between gap-3">
                          <div>
                            <p className="text-sm font-semibold text-forest-700">Image {index + 1}</p>
                            <p className="text-xs text-foreground/60">{image.title || 'Untitled update'}</p>
                          </div>
                          <button
                            type="button"
                            onClick={() => removeProgressImage(location.id, index)}
                            className="rounded-full px-3 py-1 border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
                          >
                            Remove
                          </button>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2 mt-4">
                          <input
                            value={image.title}
                            onChange={(e) => updateProgressImage(location.id, index, 'title', e.target.value)}
                            placeholder="Image title"
                            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                          <input
                            value={image.date}
                            onChange={(e) => updateProgressImage(location.id, index, 'date', e.target.value)}
                            placeholder="Date"
                            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                        </div>
                        <div className="mt-4 space-y-4">
                          <textarea
                            rows={3}
                            value={image.description}
                            onChange={(e) => updateProgressImage(location.id, index, 'description', e.target.value)}
                            placeholder="Image description"
                            className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                          <input
                            value={image.image}
                            onChange={(e) => updateProgressImage(location.id, index, 'image', e.target.value)}
                            placeholder="Image URL"
                            className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                          />
                        </div>
                      </div>
                    ))}
                  </div>

                  <div className="space-y-4">
                    <div className="flex items-center justify-between gap-3">
                      <div>
                        <h3 className="text-base font-semibold text-foreground">Events & milestones</h3>
                        <p className="text-sm text-foreground/60">Timeline events for this planting location.</p>
                      </div>
                      <button
                        type="button"
                        onClick={() => addEvent(location.id)}
                        className="rounded-full px-4 py-2 bg-white border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
                      >
                        Add event
                      </button>
                    </div>

                    {location.events.length === 0 ? (
                      <div className="rounded-3xl border border-earth-200 bg-slate-50 p-4 text-sm text-foreground/70">
                        No events added yet.
                      </div>
                    ) : (
                      <div className="space-y-4">
                        {location.events.map((event) => (
                          <div key={event.id} className="rounded-3xl border border-earth-200 bg-forest-50/40 p-4">
                            <div className="flex items-start justify-between gap-3">
                              <div>
                                <p className="text-sm font-semibold text-forest-700">{event.title || 'Untitled event'}</p>
                                <p className="text-xs text-foreground/60">{event.date}</p>
                              </div>
                              <button
                                type="button"
                                onClick={() => removeEvent(location.id, event.id)}
                                className="rounded-full px-3 py-1 border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white transition-colors"
                              >
                                Remove
                              </button>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 mt-4">
                              <input
                                value={event.title}
                                onChange={(e) => updateEventField(location.id, event.id, 'title', e.target.value)}
                                placeholder="Event title"
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                              <input
                                value={event.date}
                                onChange={(e) => updateEventField(location.id, event.id, 'date', e.target.value)}
                                placeholder="Date"
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                            </div>

                            <div className="grid gap-4 sm:grid-cols-3 mt-4">
                              <select
                                value={event.eventType}
                                onChange={(e) => updateEventField(location.id, event.id, 'eventType', e.target.value)}
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              >
                                <option value="planted">Planted</option>
                                <option value="protected">Protected</option>
                                <option value="irrigated">Irrigated</option>
                                <option value="growth_stage">Growth stage</option>
                              </select>
                              <select
                                value={event.stageKey ?? ''}
                                onChange={(e) => updateEventField(location.id, event.id, 'stageKey', e.target.value || undefined)}
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              >
                                <option value="">Stage (optional)</option>
                                <option value="seed">Seed</option>
                                <option value="growth">Growth</option>
                                <option value="mature">Mature</option>
                              </select>
                              <input
                                type="number"
                                min="0"
                                value={event.count}
                                onChange={(e) => updateEventField(location.id, event.id, 'count', safeNumber(e.target.value, 0))}
                                placeholder="Count"
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                            </div>

                            <div className="mt-4 space-y-4">
                              <textarea
                                rows={3}
                                value={event.description}
                                onChange={(e) => updateEventField(location.id, event.id, 'description', e.target.value)}
                                placeholder="Event description"
                                className="w-full rounded-2xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                              <input
                                value={event.image ?? ''}
                                onChange={(e) => updateEventField(location.id, event.id, 'image', e.target.value)}
                                placeholder="Image URL (optional)"
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                              <input
                                value={event.verifiedBy ?? ''}
                                onChange={(e) => updateEventField(location.id, event.id, 'verifiedBy', e.target.value)}
                                placeholder="Verified by"
                                className="w-full rounded-xl border border-earth-200 bg-white px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-forest-200"
                              />
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              ))}

              {draft.locations.length < MAX_LOCATIONS ? (
                <button
                  type="button"
                  onClick={addLocation}
                  className="rounded-2xl px-4 py-3 bg-forest-600 text-white font-semibold hover:bg-forest-700 transition-colors"
                >
                  Add planting location
                </button>
              ) : (
                <div className="text-sm text-foreground/60">Maximum of {MAX_LOCATIONS} locations reached.</div>
              )}

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
                    Save live map
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
          </>
        )}
      </div>
    </div>
  );
}

