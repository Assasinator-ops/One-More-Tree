'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import dynamic from 'next/dynamic';
import { ChevronDown, LayoutDashboard, TreePine, MessageSquare, Users, Settings } from 'lucide-react';

// Admin modules are dynamically loaded so the public site never pays for
// them. Each module renders inside a single page shell.
const HomeCountersAdmin = dynamic(() => import('@/admin/modules/homeCounters/HomeCountersAdmin'), { ssr: false });
const ImpactKpisAdmin = dynamic(() => import('@/admin/modules/impactKpis/ImpactKpisAdmin'), { ssr: false });
const ProjectsAdmin = dynamic(() => import('@/admin/modules/projects/ProjectsAdmin'), { ssr: false });
const TeamAdmin = dynamic(() => import('@/admin/modules/team/TeamAdmin'), { ssr: false });
const StoriesAdmin = dynamic(() => import('@/admin/modules/stories/StoriesAdmin'), { ssr: false });
const LiveMapAdmin = dynamic(() => import('@/admin/modules/liveMap/LiveMapAdmin'), { ssr: false });
const FAQAdmin = dynamic(() => import('@/admin/modules/faq/FAQAdmin'), { ssr: false });
const ContactAdmin = dynamic(() => import('@/admin/modules/contact/ContactAdmin'), { ssr: false });
const NewsletterAdmin = dynamic(() => import('@/admin/modules/newsletter/NewsletterAdmin'), { ssr: false });
const SocialMediaAdmin = dynamic(() => import('@/admin/modules/socialMedia/SocialMediaAdmin'), { ssr: false });
const AdminUsersAdmin = dynamic(() => import('@/admin/modules/adminUsers/AdminUsersAdmin'), { ssr: false });
const ModuleStatusAdmin = dynamic(() => import('@/admin/modules/moduleStatus/ModuleStatusAdmin'), { ssr: false });
const DonationsAdmin = dynamic(() => import('@/admin/modules/donations/DonationsAdmin'), { ssr: false });
const ReportingView = dynamic(() => import('@/views/admin/ReportingView'), { ssr: false });
const DisclaimerAdmin = dynamic(() => import('@/admin/modules/disclaimer/DisclaimerAdmin'), { ssr: false });
const BlogAdmin = dynamic(() => import('@/admin/modules/blog/index'), { ssr: false });

const PAGES = [
  { key: 'home-counters', label: 'Home Counters' },
  { key: 'donations', label: 'Donations' },
  { key: 'impact-kpis', label: 'Impact KPIs' },
  { key: 'projects', label: 'Projects' },
  { key: 'team', label: 'Team' },
  { key: 'stories', label: 'Stories' },
  { key: 'live-map', label: 'Live Map' },
  { key: 'faq', label: 'FAQ' },
  { key: 'contact', label: 'Contact' },
  { key: 'newsletter', label: 'Newsletter' },
  { key: 'social-media', label: 'Social Media' },
  { key: 'admin-users', label: 'Admin Users' },
  { key: 'module-status', label: 'Module Visibility' },
  { key: 'reporting', label: 'Reporting' },
  { key: 'disclaimer', label: 'Disclaimer' },
  { key: 'blog', label: 'Blog' }
] as const;

const NAVIGATION_GROUPS = [
  {
    name: 'Homepage',
    icon: LayoutDashboard,
    pages: PAGES.filter(p => ['home-counters', 'impact-kpis'].includes(p.key))
  },
  {
    name: 'Projects',
    icon: TreePine,
    pages: PAGES.filter(p => ['projects', 'stories', 'live-map'].includes(p.key))
  },
  {
    name: 'Communication',
    icon: MessageSquare,
    pages: PAGES.filter(p => ['blog', 'newsletter', 'social-media', 'contact'].includes(p.key))
  },
  {
    name: 'Organization',
    icon: Users,
    pages: PAGES.filter(p => ['team', 'admin-users'].includes(p.key))
  },
  {
    name: 'Settings',
    icon: Settings,
    pages: PAGES.filter(p => ['faq', 'disclaimer', 'module-status', 'donations', 'reporting'].includes(p.key))
  }
] as const;

type PageKey = (typeof PAGES)[number]['key'];

export default function AdminShell({ username }: { username: string }) {
  const router = useRouter();
  const [activePageKey, setActivePageKey] = useState<PageKey>('home-counters');
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);

  const logout = async () => {
    await fetch('/api/admin/logout', { method: 'POST' });
    router.replace('/admin/login');
    router.refresh();
  };

  return (
    <div className="min-h-screen bg-background">
      <main className="relative w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-16 sm:py-24">
        <div className="max-w-6xl mx-auto">
          <h1 className="font-serif text-4xl sm:text-5xl font-bold text-foreground mb-4">Admin</h1>
          <p className="text-foreground/70 max-w-3xl">
            Admin modules are independent working areas. Changes are persisted to the SQLite database and the published
            content cache is invalidated automatically.
          </p>

          <div className="mt-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between md:gap-6">
            <div className="flex-1">
              <div className="flex flex-wrap gap-2">
                {NAVIGATION_GROUPS.map((group) => (
                  <div key={group.name} className="relative">
                    <button
                      type="button"
                      onClick={() => setOpenDropdown(openDropdown === group.name ? null : group.name)}
                      className={`inline-flex items-center gap-2 px-4 py-2 rounded-full transition-colors ${
                        openDropdown === group.name || group.pages.some(p => p.key === activePageKey)
                          ? 'bg-forest-600 text-white shadow-medium'
                          : 'bg-white/60 border border-earth-200 text-foreground/70 hover:text-foreground'
                      }`}
                    >
                      <group.icon className="w-4 h-4" />
                      {group.name}
                      <ChevronDown className={`w-4 h-4 transition-transform ${openDropdown === group.name ? 'rotate-180' : ''}`} />
                    </button>
                    
                    {openDropdown === group.name && (
                      <div className="absolute top-full left-0 mt-2 w-48 bg-white border border-earth-200 rounded-xl shadow-elevated z-50 overflow-hidden">
                        {group.pages.map((p) => (
                          <button
                            key={p.key}
                            type="button"
                            onClick={() => {
                              setActivePageKey(p.key);
                              setOpenDropdown(null);
                            }}
                            className={`w-full text-left px-4 py-2 transition-colors ${
                              activePageKey === p.key
                                ? 'bg-forest-50 text-forest-700 font-semibold'
                                : 'text-foreground/70 hover:bg-earth-50 hover:text-foreground'
                            }`}
                          >
                            {p.label}
                          </button>
                        ))}
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
              <div className="rounded-2xl border border-earth-200 bg-slate-50 px-4 py-3 text-sm text-foreground/70">
                Signed in as <strong>{username}</strong>
              </div>
              <a
                href="/health"
                target="_blank"
                rel="noopener noreferrer"
                className="rounded-2xl bg-blue-600 hover:bg-blue-700 px-4 py-3 text-white transition font-medium text-center"
              >
                Check Health
              </a>
              <button
                type="button"
                onClick={logout}
                className="rounded-2xl bg-forest-600 px-4 py-3 text-white transition hover:bg-forest-700"
              >
                Logout
              </button>
            </div>
          </div>

          <div className="mt-6 rounded-3xl glass-card border border-earth-200 p-4 sm:p-6">
            {activePageKey === 'home-counters' ? (
              <HomeCountersAdmin />
            ) : activePageKey === 'donations' ? (
              <DonationsAdmin />
            ) : activePageKey === 'impact-kpis' ? (
              <ImpactKpisAdmin />
            ) : activePageKey === 'projects' ? (
              <ProjectsAdmin />
            ) : activePageKey === 'team' ? (
              <TeamAdmin />
            ) : activePageKey === 'stories' ? (
              <StoriesAdmin />
            ) : activePageKey === 'live-map' ? (
              <LiveMapAdmin />
            ) : activePageKey === 'faq' ? (
              <FAQAdmin />
            ) : activePageKey === 'contact' ? (
              <ContactAdmin />
            ) : activePageKey === 'newsletter' ? (
              <NewsletterAdmin />
            ) : activePageKey === 'social-media' ? (
              <SocialMediaAdmin />
            ) : activePageKey === 'admin-users' ? (
              <AdminUsersAdmin />
            ) : activePageKey === 'module-status' ? (
              <ModuleStatusAdmin />
            ) : activePageKey === 'disclaimer' ? (
              <DisclaimerAdmin />
            ) : activePageKey === 'blog' ? (
              <BlogAdmin />
            ) : (
              <ReportingView />
            )}
          </div>
        </div>
      </main>
    </div>
  );
}
