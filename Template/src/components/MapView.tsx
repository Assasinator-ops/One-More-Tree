import React from 'react';
import InteractiveSpainMap from './InteractiveSpainMap';
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

const MapView: React.FC = () => {
  const [locations, setLocations] = React.useState<AdminLocation[] | null>(null);

  React.useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const lm = await publicApi.read<{ locations?: AdminLocation[] }>(PublicKeys.LiveMap);
        if (!alive) return;
        setLocations(lm?.locations ?? null);
      } catch {
        if (!alive) return;
        setLocations(null);
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const effectiveLocations = locations?.length ? locations : europePlantingLocations;

  return (
    <div className="tree-story-map h-[500px] md:h-[600px] rounded-3xl overflow-hidden shadow-2xl relative">

      <InteractiveSpainMap locations={effectiveLocations} />
    </div>
  );
};

export default MapView;
