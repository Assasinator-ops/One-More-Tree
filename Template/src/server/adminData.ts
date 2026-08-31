// Server-side wrappers around the SQLite DB for all admin-editable data.
//
// These functions are the source of truth for the public website. Pages and
// sections should call them (directly from a server component) instead of
// reading from localStorage.

import { getContent, getDb, getModuleStatuses, setContent, setModuleStatuses, getSetting, setSetting } from './db';

// Home counters
export type HomeCounters = {
  treesTracked: number;
  projectsActive: number;
  volunteersEngaged: number;
  regionsCovered: number;
};

export const HOME_COUNTERS_DEFAULT: HomeCounters = {
  treesTracked: 0,
  projectsActive: 0,
  volunteersEngaged: 0,
  regionsCovered: 0
};

export function getHomeCounters(): HomeCounters {
  return getContent<HomeCounters>('home_counters', HOME_COUNTERS_DEFAULT);
}

export function saveHomeCounters(value: HomeCounters) {
  setContent('home_counters', value);
}

// Impact KPIs
export const IMPACT_KPIS_DEFAULT = {
  treesPlanted: 0,
  co2OffsetTons: 0,
  hectaresRestored: 0,
  survivalRate: 0,
  updatedAt: new Date(0).toISOString()
};

export function getImpactKpis() {
  return getContent('impact_kpis', IMPACT_KPIS_DEFAULT);
}
export function saveImpactKpis(value: unknown) {
  // Ensure SQLite parameter binding always receives a JSON-serializable value
  // (previous failures happened when the payload contained non-serializable types).
  setContent('impact_kpis', JSON.parse(JSON.stringify(value)));
}

// Stories (3 chapters)
export const STORIES_DEFAULT = {
  chapters: [] as unknown[]
};
export function getStories() {
  return getContent('stories', STORIES_DEFAULT);
}
export function saveStories(value: unknown) {
  setContent('stories', value);
}

// Team
export const TEAM_DEFAULT = { members: [], stats: [], sectionTitle: '', headline: '', description: '' };
export function getTeam() {
  return getContent('team', TEAM_DEFAULT);
}
export function saveTeam(value: unknown) {
  setContent('team', value);
}

// Live map locations
export const LIVE_MAP_DEFAULT = { locations: [] as unknown[] };
export function getLiveMap() {
  return getContent('live_map', LIVE_MAP_DEFAULT);
}
export function saveLiveMap(value: unknown) {
  setContent('live_map', value);
}

// Projects
export const PROJECTS_DEFAULT: { projects: unknown[] } = { projects: [] };
export function getProjects() {
  return getContent('projects', PROJECTS_DEFAULT);
}
export function saveProjects(value: unknown) {
  setContent('projects', value);
}

// FAQ
export const FAQ_DEFAULT: { items: unknown[] } = { items: [] };
export function getFAQ() {
  return getContent('faq', FAQ_DEFAULT);
}
export function saveFAQ(value: unknown) {
  setContent('faq', value);
}

// Donations (tiers + orders)
export const DONATION_TIERS_DEFAULT = { tiers: [] as unknown[] };
export function getDonationTiers() {
  return getContent('donation_tiers', DONATION_TIERS_DEFAULT);
}
export function saveDonationTiers(value: unknown) {
  setContent('donation_tiers', value);
}

export function getDonationOrders() {
  return getContent<{ orders: unknown[] }>('donation_orders', { orders: [] });
}
export function saveDonationOrders(value: unknown) {
  setContent('donation_orders', value);
}

// Social
export const SOCIAL_DEFAULT = { links: [], embeds: [] };
export function getSocial() {
  return getContent('social', SOCIAL_DEFAULT);
}
export function saveSocial(value: unknown) {
  setContent('social', value);
}

// Module visibility
export function getModules() {
  return getModuleStatuses();
}
export function saveModules(modules: Record<string, boolean>) {
  setModuleStatuses(modules);
}

export function isModuleEnabled(key: string): boolean {
  const modules = getModuleStatuses();
  return modules[key] !== false; // default ON when not present
}

// Maintenance
export function getMaintenanceEnabled(): boolean {
  return getSetting('maintenance_enabled') === 'true';
}
export function setMaintenanceEnabled(enabled: boolean) {
  setSetting('maintenance_enabled', enabled ? 'true' : 'false');
}

// Under Construction Watermark
export function getUnderConstructionEnabled(): boolean {
  return getSetting('under_construction_enabled') === 'true';
}
export function setUnderConstructionEnabled(enabled: boolean) {
  setSetting('under_construction_enabled', enabled ? 'true' : 'false');
}

// Disclaimer ribbon
export type DisclaimerColor = 'green' | 'red' | 'yellow' | 'blue' | 'pink';

export type Disclaimer = {
  enabled: boolean;
  message: string;
  color: DisclaimerColor;
};

export const DISCLAIMER_DEFAULT: Disclaimer = {
  enabled: true,
  message:
    '🌳 OneMoreTree NGO is a volunteer-led initiative. All donations go directly to tree planting, site restoration, and community programs.',
  color: 'green'
};

export function getDisclaimer(): Disclaimer {
  const raw = getSetting('disclaimer');
  if (!raw) return DISCLAIMER_DEFAULT;
  try {
    const parsed = JSON.parse(raw) as Partial<Disclaimer>;
    return {
      enabled: parsed.enabled !== false,
      message:
        typeof parsed.message === 'string' && parsed.message.trim().length > 0
          ? parsed.message
          : DISCLAIMER_DEFAULT.message,
      color: (['green', 'red', 'yellow', 'blue', 'pink'] as DisclaimerColor[]).includes(
        parsed.color as DisclaimerColor
      )
        ? (parsed.color as DisclaimerColor)
        : DISCLAIMER_DEFAULT.color
    };
  } catch {
    return DISCLAIMER_DEFAULT;
  }
}

export function setDisclaimer(disclaimer: Disclaimer) {
  setSetting('disclaimer', JSON.stringify(disclaimer));
}

// Contact / newsletter submissions
export type ContactSubmission = {
  id: string;
  name: string;
  email: string;
  message: string;
  submittedAt: string;
};

export function appendContactSubmission(input: Omit<ContactSubmission, 'submittedAt'>) {
  const submission: ContactSubmission = {
    ...input,
    submittedAt: new Date().toISOString()
  };
  getDb()
    .prepare(
      `INSERT OR REPLACE INTO contact_submissions (id, name, email, message, submitted_at)
       VALUES (?, ?, ?, ?, ?)`
    )
    .run(submission.id, submission.name, submission.email, submission.message, submission.submittedAt);
}

export function listContactSubmissions(): ContactSubmission[] {
  return getDb()
    .prepare(
      `SELECT id, name, email, message, submitted_at FROM contact_submissions ORDER BY submitted_at DESC LIMIT 200`
    )
    .all()
    .map((r) => r as unknown as ContactSubmission);
}

export function appendNewsletterSubscriber(email: string) {
  const id = `newsletter-${Date.now()}`;
  getDb()
    .prepare(
      `INSERT OR REPLACE INTO newsletter_subscribers (id, email) VALUES (?, ?)`
    )
    .run(id, email);
}

export function listNewsletterSubscribers() {
  return getDb()
    .prepare(`SELECT id, email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC LIMIT 500`)
    .all() as Array<{ id: string; email: string; subscribed_at: string }>;
}

// Blog types
export type BlogAuthor = {
  id: string;
  name: string;
  role: string;
  avatar: string;
  bio: string;
  created_at: string;
};

export type BlogPost = {
  id: string;
  slug: string;
  title: string;
  subtitle?: string;
  author: string;
  date: string;
  isoDate: string;
  readTime: string;
  category: string;
  tags: string[];
  image: string;
  imageCredit?: string;
  excerpt: string;
  content: string;
  published: boolean;
  featured: boolean;
  created_at: string;
  updated_at: string;
};

export type BlogPostInput = Omit<BlogPost, 'id' | 'created_at' | 'updated_at'>;

// Blog authors
export function getBlogAuthors(): BlogAuthor[] {
  return getDb()
    .prepare(`SELECT * FROM blog_authors ORDER BY name`)
    .all() as BlogAuthor[];
}

export function getBlogAuthorById(id: string): BlogAuthor | null {
  const row = getDb()
    .prepare(`SELECT * FROM blog_authors WHERE id = ?`)
    .get(id) as BlogAuthor | undefined;
  return row ?? null;
}

export function getBlogAuthorByName(name: string): BlogAuthor | null {
  const row = getDb()
    .prepare(`SELECT * FROM blog_authors WHERE name = ?`)
    .get(name) as BlogAuthor | undefined;
  return row ?? null;
}

export function createBlogAuthor(input: Omit<BlogAuthor, 'id' | 'created_at'>): BlogAuthor {
  const id = `author-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
  const author: BlogAuthor = {
    ...input,
    id,
    created_at: new Date().toISOString()
  };
  getDb()
    .prepare(
      `INSERT INTO blog_authors (id, name, role, avatar, bio, created_at) VALUES (?, ?, ?, ?, ?, ?)`
    )
    .run(author.id, author.name, author.role, author.avatar, author.bio, author.created_at);
  return author;
}

export function updateBlogAuthor(id: string, input: Partial<Omit<BlogAuthor, 'id' | 'created_at'>>): BlogAuthor | null {
  const existing = getBlogAuthorById(id);
  if (!existing) return null;
  
  const updated = { ...existing, ...input };
  getDb()
    .prepare(
      `UPDATE blog_authors SET name = ?, role = ?, avatar = ?, bio = ? WHERE id = ?`
    )
    .run(updated.name, updated.role, updated.avatar, updated.bio, id);
  return updated;
}

export function deleteBlogAuthor(id: string): boolean {
  const result = getDb()
    .prepare(`DELETE FROM blog_authors WHERE id = ?`)
    .run(id);
  return result.changes > 0;
}

// Blog posts
export function getBlogPosts(options?: { published?: boolean; featured?: boolean; category?: string; limit?: number }): BlogPost[] {
  let query = `SELECT * FROM blog_posts WHERE 1=1`;
  const params: any[] = [];
  
  if (options?.published !== undefined) {
    query += ` AND published = ?`;
    params.push(options.published ? 1 : 0);
  }
  if (options?.featured) {
    query += ` AND featured = ?`;
    params.push(1);
  }
  if (options?.category) {
    query += ` AND category = ?`;
    params.push(options.category);
  }
  
  query += ` ORDER BY date DESC`;
  
  if (options?.limit) {
    query += ` LIMIT ?`;
    params.push(options.limit);
  }
  
  const rows = getDb().prepare(query).all(...params) as any[];
  return rows.map(row => ({
    ...row,
    tags: JSON.parse(row.tags),
    published: row.published === 1,
    featured: row.featured === 1,
    isoDate: row.iso_date
  }));
}

export function getBlogPostBySlug(slug: string, publishedOnly = true): BlogPost | null {
  let query = `SELECT * FROM blog_posts WHERE slug = ?`;
  const params: any[] = [slug];
  
  if (publishedOnly) {
    query += ` AND published = ?`;
    params.push(1);
  }
  
  const row = getDb().prepare(query).get(...params) as any;
  if (!row) return null;
  
  return {
    ...row,
    tags: JSON.parse(row.tags),
    published: row.published === 1,
    featured: row.featured === 1,
    isoDate: row.iso_date
  };
}

export function getBlogPostById(id: string): BlogPost | null {
  const row = getDb().prepare(`SELECT * FROM blog_posts WHERE id = ?`).get(id) as any;
  if (!row) return null;
  
  return {
    ...row,
    tags: JSON.parse(row.tags),
    published: row.published === 1,
    featured: row.featured === 1,
    isoDate: row.iso_date
  };
}

export function createBlogPost(input: BlogPostInput): BlogPost {
  const id = `post-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

  // The admin UI currently does not send `slug`.
  // SQLite parameter binding fails when `slug` is not a plain string.
  const titleForSlug = typeof input.title === 'string' ? input.title : '';
  const derivedSlug = titleForSlug
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');

  const post: BlogPost = {
    ...input,
    id,
    slug: typeof (input as any).slug === 'string' && (input as any).slug.trim().length > 0 ? (input as any).slug : derivedSlug,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  };

  getDb()
    .prepare(
      `INSERT INTO blog_posts (id, slug, title, subtitle, author, date, iso_date, read_time, category, tags, image, image_credit, excerpt, content, published, featured, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
    )
    .run(
      post.id,
      post.slug,
      post.title,
      post.subtitle ?? null,
      post.author,
      post.date,
      post.isoDate,
      post.readTime,
      post.category,
      JSON.stringify(post.tags),
      post.image,
      post.imageCredit ?? null,
      post.excerpt,
      post.content,
      post.published ? 1 : 0,
      post.featured ? 1 : 0,
      post.created_at,
      post.updated_at
    );

  return post;
}


export function updateBlogPost(id: string, input: Partial<BlogPostInput>): BlogPost | null {
  const existing = getBlogPostById(id);
  if (!existing) return null;
  
  const updated = { ...existing, ...input, updated_at: new Date().toISOString() };
  
  getDb()
    .prepare(
      `UPDATE blog_posts 
       SET slug = ?, title = ?, subtitle = ?, author = ?, date = ?, iso_date = ?, read_time = ?, 
           category = ?, tags = ?, image = ?, image_credit = ?, excerpt = ?, content = ?, 
           published = ?, featured = ?, updated_at = ?
       WHERE id = ?`
    )
    .run(
      updated.slug,
      updated.title,
      updated.subtitle ?? null,
      updated.author,
      updated.date,
      updated.isoDate,
      updated.readTime,
      updated.category,
      JSON.stringify(updated.tags),
      updated.image,
      updated.imageCredit ?? null,
      updated.excerpt,
      updated.content,
      updated.published ? 1 : 0,
      updated.featured ? 1 : 0,
      updated.updated_at,
      id
    );
  
  return updated;
}

export function deleteBlogPost(id: string): boolean {
  const result = getDb().prepare(`DELETE FROM blog_posts WHERE id = ?`).run(id);
  return result.changes > 0;
}

export function getRelatedPosts(post: BlogPost, limit = 3): BlogPost[] {
  const allPosts = getBlogPosts({ published: true });
  const scored = allPosts
    .filter(p => p.id !== post.id)
    .map(p => {
      const sharedTags = p.tags.filter(t => post.tags.includes(t)).length;
      const sameCategory = p.category === post.category ? 1 : 0;
      return { post: p, score: sharedTags * 2 + sameCategory };
    })
    .sort((a, b) => b.score - a.score)
    .slice(0, limit);
  
  return scored.map(s => s.post);
}
