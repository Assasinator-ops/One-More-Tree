// Single admin data endpoint that supports GET (read) and POST (write).
//
// The admin UI continues to use the same shape as the old localStorage
// payloads — we just persist them through SQLite now.

import { NextRequest, NextResponse } from 'next/server';
import { revalidateTag } from 'next/cache';
import { getCurrentAdmin } from '@/server/adminAuth';
import {
  listContactSubmissions,
  listNewsletterSubscribers,
  getDonationOrders,
  getDonationTiers,
  getDisclaimer,
  getFAQ,
  getHomeCounters,
  getImpactKpis,
  getLiveMap,
  getMaintenanceEnabled,
  getUnderConstructionEnabled,
  getModules,
  getProjects,
  getSocial,
  getStories,
  getTeam,
  saveDonationOrders,
  saveDonationTiers,
  saveFAQ,
  saveHomeCounters,
  saveImpactKpis,
  saveLiveMap,
  setMaintenanceEnabled as saveMaintenanceEnabled,
  setUnderConstructionEnabled as saveUnderConstructionEnabled,
  saveModules,
  saveProjects,
  saveSocial,
  saveStories,
  saveTeam,
  setDisclaimer
} from '@/server/adminData';
import { addAdminAccount, deleteAdminAccount, listAdminUsers, setAdminPassword } from '@/server/adminAuth';
import {
  getBlogPosts,
  getBlogPostById,
  getBlogPostBySlug,
  createBlogPost,
  updateBlogPost,
  deleteBlogPost,
  getBlogAuthors,
  getBlogAuthorById,
  createBlogAuthor,
  updateBlogAuthor,
  deleteBlogAuthor,
  getRelatedPosts
} from '@/server/adminData';


const KEY_TO_READ: Record<string, () => unknown> = {
  'home_counters': () => getHomeCounters(),
  'impact_kpis': () => getImpactKpis(),
  'stories': () => getStories(),
  'team': () => getTeam(),
  'live_map': () => getLiveMap(),
  'projects': () => getProjects(),
  'faq': () => getFAQ(),
  'donation_tiers': () => getDonationTiers(),
  'donation_orders': () => getDonationOrders(),
  'social': () => getSocial(),
  'modules': () => getModules(),
  'maintenance': () => getMaintenanceEnabled(),
  'under_construction': () => getUnderConstructionEnabled(),
  'disclaimer': () => getDisclaimer(),
  'contact_submissions': () => listContactSubmissions(),
  'newsletter_subscribers': () => listNewsletterSubscribers(),
  'admin_users': () => listAdminUsers(),
  'blog_posts': () => getBlogPosts(),
  'blog_authors': () => getBlogAuthors()
};

const KEY_TO_WRITE: Record<string, (value: unknown) => void> = {
  'home_counters': saveHomeCounters,
  'impact_kpis': saveImpactKpis,
  'stories': saveStories,
  'team': saveTeam,
  'live_map': saveLiveMap,
  'projects': saveProjects,
  'faq': saveFAQ,
  'donation_tiers': saveDonationTiers,
  'donation_orders': saveDonationOrders,
  'social': saveSocial,
  'modules': (v) => saveModules((v ?? {}) as Record<string, boolean>),
  'maintenance': (v) => saveMaintenanceEnabled(Boolean(v)),
  'under_construction': (v) => saveUnderConstructionEnabled(Boolean(v)),
  'disclaimer': (v) => setDisclaimer((v ?? {}) as Parameters<typeof setDisclaimer>[0])
};

async function requireAdmin() {
  const admin = await getCurrentAdmin();
  if (!admin) {
    return null;
  }
  return admin;
}

export async function GET(req: NextRequest) {
  if (!(await requireAdmin())) {
    return NextResponse.json({ ok: false, message: 'Unauthorized' }, { status: 401 });
  }
  const key = req.nextUrl.searchParams.get('key');
  if (!key) {
    return NextResponse.json({ ok: false, message: 'Missing key' }, { status: 400 });
  }
  const reader = KEY_TO_READ[key];
  if (!reader) {
    return NextResponse.json({ ok: false, message: 'Unknown key' }, { status: 400 });
  }
  return NextResponse.json({ ok: true, value: reader() });
}

export async function POST(req: NextRequest) {
  const admin = await requireAdmin();
  if (!admin) {
    return NextResponse.json({ ok: false, message: 'Unauthorized' }, { status: 401 });
  }
  if (admin.must_reset_password === 1) {
    return NextResponse.json({ ok: false, message: 'Password reset required' }, { status: 403 });
  }

  const body = (await req.json().catch(() => ({}))) as { key?: string; value?: unknown };
  const key = body.key;
  if (!key) {
    return NextResponse.json({ ok: false, message: 'Missing key' }, { status: 400 });
  }

  // Admin user management lives behind guarded helpers.
  if (key === 'admin_users/create') {
    const v = (body.value ?? {}) as { username?: string; password?: string };
    if (!v.username || !v.password) {
      return NextResponse.json({ ok: false, message: 'Missing username/password' }, { status: 400 });
    }
    if (v.password.length < 8) {
      return NextResponse.json({ ok: false, message: 'Password must be at least 8 characters.' }, { status: 400 });
    }
    await addAdminAccount(v.username, v.password);
    return NextResponse.json({ ok: true });
  }

  if (key === 'admin_users/password') {
    const v = (body.value ?? {}) as { username?: string; password?: string };
    if (!v.username || !v.password) {
      return NextResponse.json({ ok: false, message: 'Missing username/password' }, { status: 400 });
    }
    if (v.password.length < 8) {
      return NextResponse.json({ ok: false, message: 'Password must be at least 8 characters.' }, { status: 400 });
    }
    await setAdminPassword(v.username, v.password);
    return NextResponse.json({ ok: true });
  }

  if (key === 'admin_users/delete') {
    const v = (body.value ?? {}) as { username?: string };
    if (!v.username) {
      return NextResponse.json({ ok: false, message: 'Missing username' }, { status: 400 });
    }
    await deleteAdminAccount(v.username);
    return NextResponse.json({ ok: true });
  }

  // Blog post operations
  if (key === 'blog_posts/create') {
    const post = createBlogPost(body.value as any);
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true, value: post });
  }

  if (key === 'blog_posts/update') {
    const v = body.value as { id?: string; data?: any };
    if (!v.id) {
      return NextResponse.json({ ok: false, message: 'Missing post id' }, { status: 400 });
    }
    const post = updateBlogPost(v.id, v.data);
    if (!post) {
      return NextResponse.json({ ok: false, message: 'Post not found' }, { status: 404 });
    }
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true, value: post });
  }

  if (key === 'blog_posts/delete') {
    const v = body.value as { id?: string };
    if (!v.id) {
      return NextResponse.json({ ok: false, message: 'Missing post id' }, { status: 400 });
    }
    const success = deleteBlogPost(v.id);
    if (!success) {
      return NextResponse.json({ ok: false, message: 'Post not found' }, { status: 404 });
    }
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true });
  }

  // Blog author operations
  if (key === 'blog_authors/create') {
    const author = createBlogAuthor(body.value as any);
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true, value: author });
  }

  if (key === 'blog_authors/update') {
    const v = body.value as { id?: string; data?: any };
    if (!v.id) {
      return NextResponse.json({ ok: false, message: 'Missing author id' }, { status: 400 });
    }
    const author = updateBlogAuthor(v.id, v.data);
    if (!author) {
      return NextResponse.json({ ok: false, message: 'Author not found' }, { status: 404 });
    }
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true, value: author });
  }

  if (key === 'blog_authors/delete') {
    const v = body.value as { id?: string };
    if (!v.id) {
      return NextResponse.json({ ok: false, message: 'Missing author id' }, { status: 400 });
    }
    const success = deleteBlogAuthor(v.id);
    if (!success) {
      return NextResponse.json({ ok: false, message: 'Author not found' }, { status: 404 });
    }
    revalidateTag('blog', 'max');
    return NextResponse.json({ ok: true });
  }

  const writer = KEY_TO_WRITE[key];

  if (!writer) {
    return NextResponse.json({ ok: false, message: 'Unknown key' }, { status: 400 });
  }
  writer(body.value);

  // Bust the published-content cache so the public site picks up changes.
  revalidateTag('content', 'max');

  return NextResponse.json({ ok: true });
}

