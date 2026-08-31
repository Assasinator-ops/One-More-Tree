// Public blog API with rate limiting and security
// Read-only access for normal users

import { NextRequest, NextResponse } from 'next/server';
import { unstable_cache } from 'next/cache';
import { getBlogPosts, getBlogPostBySlug, getRelatedPosts, type BlogPost } from '@/server/adminData';

// Simple in-memory rate limiter (for production, use Redis or similar)
const rateLimitMap = new Map<string, { count: number; resetTime: number }>();
const RATE_LIMIT_WINDOW = 60000; // 1 minute
const RATE_LIMIT_MAX_REQUESTS = 100;

function checkRateLimit(identifier: string): boolean {
  const now = Date.now();
  const record = rateLimitMap.get(identifier);
  
  if (!record || now > record.resetTime) {
    rateLimitMap.set(identifier, { count: 1, resetTime: now + RATE_LIMIT_WINDOW });
    return true;
  }
  
  if (record.count >= RATE_LIMIT_MAX_REQUESTS) {
    return false;
  }
  
  record.count++;
  return true;
}

function getClientIdentifier(req: NextRequest): string {
  // Use IP address or a fallback identifier
  const forwarded = req.headers.get('x-forwarded-for');
  const ip = forwarded?.split(',')[0] || req.headers.get('x-real-ip') || 'unknown';
  return ip;
}

// Cached blog posts fetch with 5-minute revalidation
const getCachedBlogPosts = unstable_cache(
  async (options?: { published?: boolean; featured?: boolean; category?: string; limit?: number }) => {
    return getBlogPosts(options);
  },
  ['blog-posts'],
  { revalidate: 300, tags: ['blog'] }
);

// Cached single blog post fetch with 5-minute revalidation
const getCachedBlogPost = unstable_cache(
  async (slug: string) => {
    return getBlogPostBySlug(slug, true);
  },
  ['blog-post'],
  { revalidate: 300, tags: ['blog'] }
);

// Cached related posts fetch with 5-minute revalidation
const getCachedRelatedPosts = unstable_cache(
  async (post: BlogPost, limit: number = 3) => {
    return getRelatedPosts(post, limit);
  },
  ['related-posts'],
  { revalidate: 300, tags: ['blog'] }
);

export async function GET(req: NextRequest) {
  const clientId = getClientIdentifier(req);
  
  // Rate limiting
  if (!checkRateLimit(clientId)) {
    return NextResponse.json(
      { error: 'Rate limit exceeded. Please try again later.' },
      { status: 429 }
    );
  }

  const { searchParams } = new URL(req.url);
  const slug = searchParams.get('slug');
  const category = searchParams.get('category');
  const featured = searchParams.get('featured') === 'true';
  const limit = searchParams.get('limit') ? parseInt(searchParams.get('limit')!) : undefined;
  const related = searchParams.get('related');

  try {
    // Get single post by slug
    if (slug) {
      const post = await getCachedBlogPost(slug);
      if (!post) {
        return NextResponse.json(
          { error: 'Post not found' },
          { status: 404 }
        );
      }

      // Get related posts if requested
      if (related === 'true') {
        const relatedPosts = await getCachedRelatedPosts(post, 3);
        return NextResponse.json({ post, relatedPosts });
      }

      return NextResponse.json({ post });
    }

    // Get posts list
    const posts = await getCachedBlogPosts({
      published: true,
      featured: featured || undefined,
      category: category || undefined,
      limit
    });

    return NextResponse.json({ posts });
  } catch (error) {
    console.error('Blog API error:', error);
    return NextResponse.json(
      { error: 'Internal server error' },
      { status: 500 }
    );
  }
}
