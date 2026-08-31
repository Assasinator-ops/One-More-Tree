import Link from 'next/link';
import { notFound } from 'next/navigation';
import { ArrowLeft } from 'lucide-react';
import BlogPostView from '@/views/public/BlogPostView';
import { getBlogPostBySlug, getBlogAuthorByName, getRelatedPosts, type BlogAuthor } from '@/server/adminData';

type RouteParams = { slug: string };

export async function generateMetadata({ params }: { params: Promise<RouteParams> }) {
  const { slug } = await params;
  const post = getBlogPostBySlug(slug, true);
  if (!post) return { title: 'Article not found · OneMoreTree' };
  return {
    title: `${post.title} · OneMoreTree Blog`,
    description: post.excerpt,
    openGraph: {
      title: post.title,
      description: post.excerpt,
      type: 'article',
      images: [post.image]
    }
  };
}

export default async function Page({ params }: { params: Promise<RouteParams> }) {
  const { slug } = await params;
  const post = getBlogPostBySlug(slug, true);
  if (!post) notFound();

  const author = getBlogAuthorByName(post.author) || {
    name: post.author,
    role: 'Contributor',
    avatar: 'https://i.pravatar.cc/200?u=' + encodeURIComponent(post.author),
    bio: `${post.author} is a contributor to the OneMoreTree blog.`,
    id: '',
    created_at: ''
  };
  const related = getRelatedPosts(post, 3);

  return (
    <>
      <noscript>
        <div className="max-w-3xl mx-auto px-6 py-24 text-center">
          <h1 className="font-serif text-3xl font-bold mb-3">Enable JavaScript</h1>
          <p className="text-foreground/70">
            This page renders a few interactive elements — please enable JavaScript to read the full article.
          </p>
        </div>
      </noscript>
      <BlogPostView post={post} author={author} related={related} />
    </>
  );
}

export function NotFound() {
  return (
    <div className="min-h-[60vh] flex items-center justify-center px-6">
      <div className="max-w-md text-center">
        <p className="text-sm font-semibold uppercase tracking-wider text-forest-700 mb-2">
          404
        </p>
        <h1 className="font-serif text-3xl sm:text-4xl font-bold text-foreground mb-3">
          We could not find that article
        </h1>
        <p className="text-foreground/70 mb-6">
          The post you are looking for may have been moved or unpublished.
        </p>
        <Link
          href="/blog"
          className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-forest-600 hover:bg-forest-700 text-white font-semibold transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          Back to all articles
        </Link>
      </div>
    </div>
  );
}
