import { redirect } from 'next/navigation';
import AdminShell from '@/views/admin/AdminShell';
import { getCurrentAdmin } from '@/server/adminAuth';

export const dynamic = 'force-dynamic';

export default async function Page() {
  // Middleware already redirects unauthenticated visitors, but we re-check
  // server-side so the page renders correctly even if middleware is bypassed.
  const admin = await getCurrentAdmin();
  if (!admin) {
    redirect('/admin/login');
  }
  if (admin.must_reset_password === 1) {
    redirect('/admin/reset-password');
  }
  return <AdminShell username={admin.username} />;
}
