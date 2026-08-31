import { isModuleEnabled } from '@/server/adminData';

import FAQView from '@/views/public/FAQView';

export const revalidate = 30;

export default function Page() {
  if (!isModuleEnabled('faq')) {
    return (
      <div className="min-h-screen flex items-center justify-center px-4 py-32">
        <div className="max-w-3xl rounded-3xl border border-earth-200 bg-white/90 p-12 text-center text-foreground/70">
          The FAQ section is currently disabled. Enable it from the admin module visibility settings.
        </div>
      </div>
    );
  }
  return <FAQView />;
}
