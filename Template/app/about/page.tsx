import { getPublishedContent } from '@/server/publicContent';
import { isModuleEnabled } from '@/server/adminData';

import AboutView from '@/views/public/AboutView';

export const revalidate = 30;

export default async function Page() {
  const [content, teamEnabled] = await Promise.all([
    getPublishedContent(),
    Promise.resolve(isModuleEnabled('team'))
  ]);
  return <AboutView team={teamEnabled ? content.team : null} />;
}
