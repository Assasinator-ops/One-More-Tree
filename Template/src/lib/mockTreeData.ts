// lib/mockTreeData.ts - Mock data for tree story viewer (per map.md Step 6)
export interface TreeStoryData {
  id: string;
  name: string;
  location: string;
  stages: Array<{
    date: string;
    stage: 'seedling' | 'sapling' | 'growth' | 'mature';
    image: string;
    thumbnail: string;
    panoramic: boolean;
    description: string;
  }>;
}

export const mockTreeTargets: TreeStoryData[] = [
  {
    id: 'spain-1',
    name: 'Mediterranean Pine #001',
    location: 'Sierra Nevada, Spain',
    stages: [
      {
        date: 'Day 1 - Jan 2022',
        stage: 'seedling',
        image: '/seedling.jpg',
        thumbnail: '/seedling.jpg',
        panoramic: false,
        description: 'Freshly planted seedling ready to grow.'
      },
      {
        date: 'Year 1 - Jan 2023',
        stage: 'sapling',
        image: '/sapling.jpg',
        thumbnail: '/sapling.jpg',
        panoramic: false,
        description: 'First year growth - establishing roots.'
      },
      {
        date: 'Year 3 - Jan 2025',
        stage: 'growth',
        image: '/sapling.jpg',
        thumbnail: '/sapling.jpg',
        panoramic: true,
        description: 'Young tree finding strength.'
      },
      {
        date: 'Year 15 - Jan 2037',
        stage: 'mature',
        image: '/mature-tree.jpg',
        thumbnail: '/mature-tree.jpg',
        panoramic: false,
        description: 'Full maturity - ecosystem anchor.'
      }
    ]
  },
  // Add more targets as needed
  {
    id: 'brazil-1',
    name: 'Amazon Mahogany #042',
    location: 'Amazonas, Brazil',
    stages: [
      // Similar structure
      {
        date: 'Day 1',
        stage: 'seedling',
        image: '/seedling.jpg',
        thumbnail: '/seedling.jpg',
        panoramic: false,
        description: 'Amazon rainforest restoration.'
      },
      {
        date: 'Year 15',
        stage: 'mature',
        image: '/mature-tree.jpg',
        thumbnail: '/mature-tree.jpg',
        panoramic: false,
        description: 'Mature canopy tree.'
      }
    ]
  }
];

export const getTreeStoryData = (targetId: string): TreeStoryData | undefined => {
  return mockTreeTargets.find(target => target.id === targetId);
};

