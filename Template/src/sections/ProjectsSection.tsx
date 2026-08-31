'use client';

import { useEffect, useState } from 'react';
import { Map } from 'lucide-react';
import InteractiveSpainMap from '@/components/InteractiveSpainMap';
import { europePlantingLocations } from '@/lib/europePlantingLocations';
import { publicApi, PublicKeys } from '@/lib/publicApi';
type AdminLocation = {
  id: string;
  name: string;
  region: string;
  description: string;
  coords: { x: string; y: string };
  treesPlanted: number;
  volunteers: number;
  progressImages: Array<{ title: string; description: string; image: string; date: string }>;
  events?: Array<{
    id: string;
    title: string;
    description: string;
    eventType: 'planted' | 'protected' | 'irrigated' | 'growth_stage';
    stageKey?: 'seed' | 'growth' | 'mature';
    count: number;
    date: string;
    image?: string;
    verifiedBy?: string;
  }>;
};


// Live map + counters now come from SQLite via /api/admin/data

type StoredCounters = Partial<{
  treesTracked: number;
  projectsActive: number;
  volunteersEngaged: number;
  regionsCovered: number;
}>;

const ProjectsSection = () => {
  const [adminLiveMap, setAdminLiveMap] = useState<{
    sectionTitle?: string;
    headline?: string;
    description?: string;
    locations?: AdminLocation[];
  } | null>(null);

  const [stored, setStored] = useState<StoredCounters | null>(null);

  useEffect(() => {
    let alive = true;

    (async () => {
      try {
        const lm = await publicApi.read<{
          sectionTitle?: string;
          headline?: string;
          description?: string;
          locations?: AdminLocation[];
        }>(PublicKeys.LiveMap);

        const counters = await publicApi.read<Partial<StoredCounters>>(PublicKeys.HomeCounters);

        if (!alive) return;
        setAdminLiveMap(lm);
        setStored(counters ?? null);
      } catch {
        if (!alive) return;
        setAdminLiveMap(null);
        setStored(null);
      }
    })();

    return () => {
      alive = false;
    };
  }, []);

  const lm = adminLiveMap as { sectionTitle?: string; headline?: string; description?: string; locations?: AdminLocation[] } | null;
  const effectiveLocations = lm?.locations?.length ? lm.locations : europePlantingLocations;
  const totalTrees = Number(stored?.treesTracked ?? effectiveLocations.reduce((sum, location) => sum + location.treesPlanted, 0));
  const totalVolunteers = Number(stored?.volunteersEngaged ?? effectiveLocations.reduce((sum, location) => sum + location.volunteers, 0));
  const sectionTitle = lm?.sectionTitle ?? 'Live Tree Map';
  const headline = lm?.headline ?? 'Track Trees in Real Time';
  const description = lm?.description ?? 'Click markers to explore progress images, planting milestones, and real planting stories across Europe.';

  return (
    <section id="projects" className="relative w-full py-20 sm:py-32 overflow-hidden bg-background">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute bottom-0 left-0 w-[620px] h-[620px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Map className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                {sectionTitle}
              </span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              {headline}
            </h2>
            <p className="text-base sm:text-lg text-foreground/70 max-w-2xl mx-auto">
              {description}
            </p>

          </div>

          <div className="grid sm:grid-cols-2 gap-4 mb-10">
            <div className="rounded-3xl bg-white/90 border border-slate-200 p-6 text-center">
              <p className="text-sm text-foreground/60 uppercase tracking-[0.24em] mb-2">Trees tracked</p>
              <p className="text-4xl font-semibold text-forest-400">{totalTrees.toLocaleString()}</p>
            </div>
            <div className="rounded-3xl bg-white/90 border border-slate-200 p-6 text-center">
              <p className="text-sm text-foreground/60 uppercase tracking-[0.24em] mb-2">Local volunteers</p>
              <p className="text-4xl font-semibold text-forest-400">{totalVolunteers.toLocaleString()}</p>
            </div>
          </div>

          <InteractiveSpainMap locations={effectiveLocations} />

          <div className="mt-10 text-center">
            <button
              type="button"
              className="px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated"
              onClick={() => document.querySelector('#join')?.scrollIntoView({ behavior: 'smooth' })}
            >
              Join these projects
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ProjectsSection;

