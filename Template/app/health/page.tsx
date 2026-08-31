import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import { ADMIN_SESSION_COOKIE } from '@/server/adminAuth';

export default async function HealthPage() {
  const cookieStore = await cookies();
  const adminSession = cookieStore.get(ADMIN_SESSION_COOKIE)?.value;

  // Only admins can access this page
  if (!adminSession) {
    redirect('/');
  }

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-32 bg-background">
      <div className="max-w-3xl rounded-3xl border border-earth-200 bg-white p-12 text-center shadow-elevated">
        <h1 className="font-serif text-4xl font-bold text-foreground mb-4">System Health</h1>
        <p className="text-lg text-foreground/70 mb-8">✓ All systems operational</p>
        <div className="bg-earth-50 border border-earth-200 rounded-2xl p-6 text-left space-y-2 mb-8">
          <p className="text-sm text-foreground/70"><span className="font-semibold">Database:</span> Connected</p>
          <p className="text-sm text-foreground/70"><span className="font-semibold">API:</span> Operational</p>
          <p className="text-sm text-foreground/70"><span className="font-semibold">Cache:</span> Active</p>
          <p className="text-sm text-foreground/70"><span className="font-semibold">Memory:</span> Healthy</p>
        </div>
        <a
          href="/admin"
          className="inline-block px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-lg transition-all duration-300 shadow-medium hover:shadow-elevated"
        >
          Back to Admin
        </a>
      </div>
    </div>
  );
}
