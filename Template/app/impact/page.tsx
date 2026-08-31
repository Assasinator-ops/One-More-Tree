import { isModuleEnabled } from '@/server/adminData';

import ImpactView from '@/views/public/ImpactView';

export const revalidate = 30;

export default function Page() {
  const showImpact = isModuleEnabled('impact');
  const showStories = isModuleEnabled('stories');
  return <ImpactView showImpact={showImpact} showStories={showStories} />;
}
