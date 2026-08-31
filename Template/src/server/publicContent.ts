// Server-side content reader that composes the SQLite-backed admin data with
// the original fallback content from `src/content/publicClient.ts`.
//
// Pages should import `getPublishedContent()` from this module (instead of
// using localStorage) to render content. Server caching is applied via
// `unstable_cache` so we get sub-500ms response times.

import { unstable_cache } from 'next/cache';
import plantingLocations from '@/data/plantingLocations.json';
import { PageContentSchema, type PageContent } from '@/content/types';
import {
  getHomeCounters,
  getImpactKpis,
  getStories,
  getTeam,
  getLiveMap,
  getFAQ,
  getModules
} from './adminData';

const FALLBACK: PageContent = {
  hero: {
    missionBadge: '1 Million Trees in Spain by 2040',
    headlineTop: 'Planting Tomorrow,',
    headlineEmphasis: 'One Tree at a Time',
    subtitle:
      'From a simple conversation among friends to a movement restoring forests across Spain. Every euro plants a tree. Every volunteer plants hope.',
    primaryCtaText: 'Plant a Tree',
    secondaryCtaText: 'Watch the Journey',
    stats: []
  },
  impact: {
    sectionTitle: 'Our Impact',
    headline: 'The Numbers Tell Our Story',
    description:
      "Every tree planted is a step toward our ambitious goal. Here's how far we've come.",
    treesPlantedLabel: 'Trees Planted',
    goalBy2040Label: 'Our Goal by 2040',
    goalBy2040Value: '0',
    progressText: '0% Complete',
    toGoText: '0 trees to go',
    stats: []
  },
  team: {
    sectionTitle: 'Our Team',
    headline: 'People who make planting real',
    description:
      'From field work to reporting, our team and partners keep restoration accountable and alive.',
    members: [],
    stats: []
  },
  stories: {
    chapters: []
  },
  liveMap: {
    sectionTitle: 'Live Tree Map',
    headline: 'Track Trees in Real Time',
    description:
      'Click markers to explore progress images, planting milestones, and real planting stories across Europe.',
    locations: plantingLocations as PageContent['liveMap']['locations']
  },
  footer: {
    mission:
      'Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.'
  },
  modules: [
    { key: 'impact', enabled: true, updatedAt: new Date().toISOString() },
    { key: 'team', enabled: true, updatedAt: new Date().toISOString() },
    { key: 'liveMap', enabled: true, updatedAt: new Date().toISOString() }
  ]
};

function composeContent(): PageContent {
  const modules = getModules();
  const modulesArr = Object.entries(modules).map(([key, enabled]) => ({
    key,
    enabled,
    updatedAt: new Date().toISOString()
  }));

  const team = getTeam() as PageContent['team'] | typeof FALLBACK.team;
  const liveMap = getLiveMap() as PageContent['liveMap'] | typeof FALLBACK.liveMap;
  const stories = getStories() as PageContent['stories'] | typeof FALLBACK.stories;

  // We deliberately keep the fallback hero/impact/footer (they are not yet
  // editable in the admin) and only merge in editable sections that have data.
  return PageContentSchema.parse({
    ...FALLBACK,
    team: (team && (team as { members?: unknown[] }).members?.length)
      ? (team as PageContent['team'])
      : FALLBACK.team,
    liveMap: (liveMap && (liveMap as { locations?: unknown[] }).locations?.length)
      ? (liveMap as PageContent['liveMap'])
      : FALLBACK.liveMap,
    stories: (stories && (stories as { chapters?: unknown[] }).chapters?.length)
      ? (stories as PageContent['stories'])
      : FALLBACK.stories,
    modules: modulesArr
  });
}

// Cached for 30 seconds — fast enough to feel instant, fresh enough to
// reflect admin edits without a redeploy.
export const getPublishedContent = unstable_cache(
  async () => composeContent(),
  ['published-content-v1'],
  { revalidate: 30, tags: ['content'] }
);

// Helper that surfaces the home counters without re-running the full content
// pipeline. Cached similarly.
export const getPublishedHomeCounters = unstable_cache(
  async () => getHomeCounters(),
  ['published-home-counters-v1'],
  { revalidate: 30, tags: ['content'] }
);

export const getPublishedImpactKpis = unstable_cache(
  async () => getImpactKpis(),
  ['published-impact-kpis-v1'],
  { revalidate: 30, tags: ['content'] }
);
