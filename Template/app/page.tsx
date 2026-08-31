// Public Home page — Server Component shell + a thin client view for the
// counter grid + scroll animations.

import { europePlantingLocations } from '@/lib/europePlantingLocations';
import { getPublishedHomeCounters, getPublishedContent } from '@/server/publicContent';
import HomeView from '@/views/public/HomeView';

export const revalidate = 30;

export default async function Page() {
  const [counters, content] = await Promise.all([
    getPublishedHomeCounters(),
    getPublishedContent()
  ]);

  const fallbackTrees = 0;
  const fallbackVolunteers = 0;
  const fallbackProjects = 0;

  return (
    <HomeView
      totalTrees={Number(counters.treesTracked ?? fallbackTrees)}
      totalProjects={Number(counters.projectsActive ?? fallbackProjects)}
      totalVolunteers={Number(counters.volunteersEngaged ?? fallbackVolunteers)}
      impact={content.impact}
    />
  );
}