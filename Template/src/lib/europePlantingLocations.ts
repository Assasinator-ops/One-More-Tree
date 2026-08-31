import plantingLocations from '@/data/plantingLocations.json';

export interface ProgressImage {
  title: string;
  description: string;
  image: string;
  date: string;
}

export interface TreeEvent {
  id: string;
  title: string;
  description: string;
  eventType: 'planted' | 'protected' | 'irrigated' | 'growth_stage';
  stageKey?: 'seed' | 'growth' | 'mature';
  count: number;
  date: string;
  image?: string;
  verifiedBy?: string;
}

export interface PlantingLocation {
  id: string;
  name: string;
  region: string;
  description: string;
  coords: { x: string; y: string };
  treesPlanted: number;
  volunteers: number;
  progressImages: ProgressImage[];
  events?: TreeEvent[];
}

/**
 * Europe planting locations.
 *
 * Note: currently backed by the existing `plantingLocations.json` dataset.
 * Once you add true multi-country Europe data, this module is the single
 * import point the UI uses.
 */
export const europePlantingLocations = plantingLocations as PlantingLocation[];

