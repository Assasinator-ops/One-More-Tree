import plantingLocations from '@/data/plantingLocations.json';

export interface ProgressImage {
  title: string;
  description: string;
  image: string;
  date: string;
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
}

export const spainPlantingLocations = plantingLocations as PlantingLocation[];

