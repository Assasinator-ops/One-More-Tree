import './globals.css';
import type { Metadata } from 'next';
import PublicChrome from '@/sections/PublicChrome';
import MaintenancePage from './maintenance/page';
import UnderConstructionWatermark from '@/components/UnderConstructionWatermark';
import { cookies, headers } from 'next/headers';
import { getMaintenanceEnabled, getUnderConstructionEnabled } from '@/server/adminData';
import { ADMIN_SESSION_COOKIE } from '@/server/adminAuth';
import { isMaintenanceExempt } from '@/lib/maintenanceExempt';

const DEBUG_MAINTENANCE = process.env.DEBUG_MAINTENANCE === '1';


export const metadata: Metadata = {
  title: 'One More Tree NGO',
  description:
    'Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.',
  metadataBase: new URL('http://onemoretree.eu'),
  icons: {
    icon: '/favicon.ico',
    apple: '/apple-touch-icon.png'
  }
};

export default async function RootLayout({
  children
}: {
  children: React.ReactNode;
}) {
  const cookieStore = await cookies();
  const headerList = await headers();
  // Read the current path from the request headers. We try a few candidates
  // because the exact header Next 15 sets can vary by render path. When none
  // are present, `isMaintenanceExempt` returns `false` and the maintenance
  // screen is shown — which is the safer default for anonymous visitors.
  const headerPath =
    headerList.get('x-invoke-path') ??
    headerList.get('x-url') ??
    headerList.get('next-url') ??
    '';
  const refererPath = (() => {
    const ref = headerList.get('referer');
    // console.log("Pin point");
    if (!ref) return '';
    try {
      return new URL(ref).pathname;
    } catch {
      return '';
    }
  })();
  // const pathname = headerPath || refererPath;
  const pathname = headerList.get('x-current-path') || '';
  const adminSession = cookieStore.get(ADMIN_SESSION_COOKIE)?.value;

  // God mode can be granted either explicitly (god=1 cookie)
  // or implicitly for authenticated admins (admin.session).
  const godMode = cookieStore.get('god')?.value === '1' || Boolean(adminSession);

  const maintenanceOn = getMaintenanceEnabled();
  const underConstructionOn = getUnderConstructionEnabled();

  // Login + auth routes are always exempt from maintenance
  const isExempt = isMaintenanceExempt(pathname);
  const isAdminPath = pathname.startsWith('/admin');

  // DEBUG
  // console.log('[layout] Resolved Path:', pathname);
  // console.log('[layout] isExempt:', isExempt);

  // console.log('[maintenance][layout]', {
  //   maintenanceOn,
  //   godMode,
  //   pathname,
  //   headerPath,
  //   refererPath,
  //   isExempt,
  //   renderOnlyMaintenance: maintenanceOn && !godMode && !isExempt
  // });

  // Hard rule: during maintenance, never render the normal site unless God Mode is active.
  const renderOnlyMaintenance = maintenanceOn && !godMode && !isExempt;
  const showChrome = !maintenanceOn || godMode;


  return (
    <html lang="en">
      <body className="min-h-screen bg-background text-foreground antialiased">
        <div className="relative min-h-screen bg-background">
          <div className="noise-overlay" />
          {renderOnlyMaintenance ? (
            <main className="relative">
              <MaintenancePage />
            </main>
          ) : showChrome ? (
            <PublicChrome isAdmin={Boolean(adminSession)}>{children}</PublicChrome>
          ) : (
            <main className="relative">{children}</main>
          )}
          {underConstructionOn && !isAdminPath && <UnderConstructionWatermark />}
        </div>
      </body>
    </html>
  );
}
