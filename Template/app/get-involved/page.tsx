import { isModuleEnabled } from '@/server/adminData';

import GetInvolvedView from '@/views/public/GetInvolvedView';

export const revalidate = 30;

export default function Page() {
  const show = isModuleEnabled('donations') || isModuleEnabled('newsletter');
  if (!show) {
    return (
      <div className="min-h-screen flex items-center justify-center px-4 py-32">
        <div className="max-w-3xl rounded-3xl border border-earth-200 bg-white/90 p-12 text-center text-foreground/70">
          Donations and newsletter sign-up are currently disabled.
        </div>
      </div>
    );
  }
  return <GetInvolvedView />;
}
