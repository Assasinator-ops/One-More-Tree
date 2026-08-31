import { isModuleEnabled } from '@/server/adminData';

import ContactView from '@/views/public/ContactView';

export const revalidate = 30;

export default function Page() {
  if (!isModuleEnabled('contact')) {
    return (
      <div className="min-h-screen flex items-center justify-center px-4 py-32">
        <div className="max-w-3xl rounded-3xl border border-earth-200 bg-white/90 p-12 text-center text-foreground/70">
          The contact module is currently disabled. Enable it in admin module status to receive messages.
        </div>
      </div>
    );
  }
  return <ContactView />;
}
