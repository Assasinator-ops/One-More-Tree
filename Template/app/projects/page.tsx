import { getProjects, isModuleEnabled } from '@/server/adminData';

import ProjectsView from '@/views/public/ProjectsView';

export const revalidate = 30;

export default function Page() {
  const liveMapEnabled = isModuleEnabled('liveMap');
  const projectsEnabled = isModuleEnabled('projects');
  const stored = getProjects() as
    | { projects?: Array<{ id: string; name: string; region: string; description: string; treesPlanted: number; volunteers: number; active?: boolean }> }
    | null;
  return (
    <ProjectsView
      liveMapEnabled={liveMapEnabled}
      projectsEnabled={projectsEnabled}
      projects={stored?.projects ?? []}
    />
  );
}
