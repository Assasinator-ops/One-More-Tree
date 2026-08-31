'use client';

import { useEffect, useMemo, useRef, useState } from 'react';
import Link from 'next/link';
import DOMPurify from 'dompurify';
import {
  ArrowLeft,
  ArrowRight,
  Calendar,
  Check,
  Clock,
  Copy,
  Facebook,
  Linkedin,
  Share2,
  Twitter,
  User as UserIcon
} from 'lucide-react';
type BlogPost = {
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

type BlogAuthor = {
  id: string;
  name: string;
  role: string;
  avatar: string;
  bio: string;
  created_at: string;
};

type Props = {
  post: BlogPost;
  author: BlogAuthor;
  related: BlogPost[];
};

export default function BlogPostView({ post, author, related }: Props) {

  const articleRef = useRef<HTMLElement>(null);
  const [progress, setProgress] = useState(0);
  const [shareCopied, setShareCopied] = useState(false);
  const [toc, setToc] = useState<{ id: string; text: string }[]>([]);
  const [activeHeading, setActiveHeading] = useState<string | null>(null);

  // Reading progress + active section tracking.
  useEffect(() => {
    const onScroll = () => {
      const el = articleRef.current;
      if (!el) return;
      const rect = el.getBoundingClientRect();
      const total = el.offsetHeight - window.innerHeight;
      const scrolled = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
      setProgress(total > 0 ? Math.min(100, (scrolled / total) * 100) : 0);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  // Build a TOC from headings in HTML content.
  const headings = useMemo(() => {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = post.content;
    const headingElements = tempDiv.querySelectorAll('h1, h2, h3');
    return Array.from(headingElements).map((h, i) => ({
      id: `heading-${i}`,
      text: h.textContent || ''
    }));
  }, [post.content]);

  useEffect(() => {
    setToc(headings);
  }, [headings]);

  useEffect(() => {
    if (headings.length === 0) return;
    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries
          .filter((e) => e.isIntersecting)
          .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);
        if (visible[0]) setActiveHeading(visible[0].target.id);
      },
      { rootMargin: '-30% 0px -55% 0px', threshold: [0, 1] }
    );
    headings.forEach((h) => {
      const el = document.getElementById(h.id);
      if (el) observer.observe(el);
    });
    return () => observer.disconnect();
  }, [headings]);

  // Animate entry.
  useEffect(() => {
    if (typeof window === 'undefined') return;
    let mounted = true;
    (async () => {
      const { gsap } = await import('gsap');
      const { ScrollTrigger } = await import('gsap/ScrollTrigger');
      if (!mounted) return;
      gsap.registerPlugin(ScrollTrigger);
      const ctx = gsap.context(() => {
        gsap.fromTo(
          '[data-anim="fade-up"]',
          { opacity: 0, y: 28 },
          {
            opacity: 1,
            y: 0,
            duration: 0.7,
            stagger: 0.12,
            ease: 'power2.out',
            scrollTrigger: { trigger: articleRef.current, start: 'top 85%' }
          }
        );
      }, articleRef);
      return () => ctx.revert();
    })();
    return () => {
      mounted = false;
    };
  }, [post.slug]);

  const shareUrl =
    typeof window !== 'undefined' ? window.location.href : `https://onemoretree.org/blog/${post.slug}`;

  const copyLink = async () => {
    try {
      await navigator.clipboard.writeText(shareUrl);
      setShareCopied(true);
      setTimeout(() => setShareCopied(false), 1800);
    } catch {
      /* noop */
    }
  };

  // Sanitize HTML content
  const sanitizedContent = useMemo(() => {
    return DOMPurify.sanitize(post.content, {
      ALLOWED_TAGS: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'em', 'u', 'a', 'img', 'ul', 'ol', 'li', 'blockquote', 'br', 'span'],
      ALLOWED_ATTR: ['href', 'src', 'alt', 'class', 'id', 'style'],
      ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i
    });
  }, [post.content]);

  // Add IDs to headings for TOC
  const contentWithIds = useMemo(() => {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = sanitizedContent;
    const headingElements = tempDiv.querySelectorAll('h1, h2, h3');
    headingElements.forEach((h, i) => {
      h.id = `heading-${i}`;
    });
    return tempDiv.innerHTML;
  }, [sanitizedContent]);

  return (
    <div className="relative min-h-screen bg-background">
      {/* Reading progress bar */}
      <div className="fixed top-0 left-0 right-0 z-50 h-1 bg-transparent">
        <div
          className="h-full bg-gradient-to-r from-forest-500 via-forest-600 to-forest-700 transition-[width] duration-150 ease-out"
          style={{ width: `${progress}%` }}
          aria-hidden="true"
        />
      </div>

      <main className="relative">
        {/* Hero */}
        <section className="relative overflow-hidden">
          <div className="absolute inset-0">
            <img
              src={post.image}
              alt=""
              aria-hidden="true"
              className="w-full h-full object-cover scale-105"
            />
            <div className="absolute inset-0 bg-gradient-to-b from-forest-950/80 via-forest-900/70 to-background" />
            <div className="absolute inset-0 opacity-20">
              <div className="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.1)_0%,transparent_70%)]" />
            </div>
          </div>

          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-28 pb-16 sm:pt-36 sm:pb-24">
            <div className="max-w-4xl mx-auto">
              <Link
                href="/blog"
                className="inline-flex items-center gap-2 text-sm text-white/80 hover:text-white transition-colors mb-8 group"
              >
                <ArrowLeft className="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                Back to all articles
              </Link>

              <div className="flex flex-wrap items-center gap-3 mb-6">
                <Link
                  href={`/blog?category=${encodeURIComponent(post.category)}`}
                  className="inline-flex items-center px-4 py-2 rounded-full bg-white/15 backdrop-blur-sm border border-white/30 text-white text-xs font-semibold uppercase tracking-wider hover:bg-white/25 transition-colors"
                >
                  {post.category}
                </Link>
                <span className="text-white/60 text-sm">·</span>
                <span className="inline-flex items-center gap-1.5 text-white/80 text-sm">
                  <Clock className="w-4 h-4" />
                  {post.readTime}
                </span>
                <span className="text-white/60 text-sm">·</span>
                <span className="inline-flex items-center gap-1.5 text-white/80 text-sm">
                  <Calendar className="w-4 h-4" />
                  <time dateTime={post.isoDate}>{post.date}</time>
                </span>
              </div>

              <h1
                data-anim="fade-up"
                className="font-serif text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-6 tracking-tight"
              >
                {post.title}
              </h1>
              {post.subtitle && (
                <p
                  data-anim="fade-up"
                  className="text-lg sm:text-xl text-white/85 max-w-3xl leading-relaxed mb-8"
                >
                  {post.subtitle}
                </p>
              )}

              {/* Author row */}
              <div
                data-anim="fade-up"
                className="flex items-center gap-4"
              >
                <img
                  src={author.avatar}
                  alt={author.name}
                  className="w-14 h-14 rounded-full object-cover ring-2 ring-white/30 shadow-lg"
                />
                <div>
                  <p className="text-white font-semibold text-lg">{author.name}</p>
                  <p className="text-white/70 text-sm">{author.role}</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Body */}
        <section className="relative pb-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 xl:px-20 grid lg:grid-cols-[1fr_20rem] gap-12 -mt-12 sm:-mt-16">
            {/* Article column */}
            <article
              ref={articleRef}
              className="relative bg-white border border-earth-200 rounded-3xl shadow-elevated p-8 sm:p-12 lg:p-16"
            >
              {/* Image caption */}
              {post.imageCredit ? (
                <p className="text-xs text-foreground/50 italic mb-8 -mt-4">
                  {post.imageCredit}
                </p>
              ) : null}

              {/* Content */}
              <div 
                className="prose prose-lg max-w-none space-y-6 prose-headings:font-serif prose-headings:font-bold prose-h1:text-3xl prose-h2:text-2xl prose-h3:text-xl prose-p:text-foreground/85 prose-p:leading-[1.8] prose-a:text-forest-600 prose-a:no-underline hover:prose-a:underline prose-strong:text-foreground prose-em:text-foreground prose-blockquote:border-l-4 prose-blockquote:border-forest-500 prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:text-foreground/70 prose-ul:list-none prose-ol:list-none prose-li:pl-0 prose-img:rounded-xl prose-img:my-4"
                dangerouslySetInnerHTML={{ __html: contentWithIds }}
              />

              {/* Tags */}
              <div className="mt-12 pt-8 border-t border-earth-200">
                <h3 className="text-sm font-semibold text-foreground uppercase tracking-wider mb-4">
                  Topics
                </h3>
                <div className="flex flex-wrap gap-2">
                  {post.tags.map((tag) => (
                    <Link
                      key={tag}
                      href={`/blog?tag=${encodeURIComponent(tag)}`}
                      className="inline-flex items-center px-4 py-2 rounded-full bg-forest-50 hover:bg-forest-100 border border-forest-200 hover:border-forest-300 text-sm text-forest-700 hover:text-forest-800 transition-colors"
                    >
                      #{tag}
                    </Link>
                  ))}
                </div>
              </div>

              {/* Author bio */}
              <div className="mt-12 p-8 rounded-3xl bg-gradient-to-br from-forest-50 to-emerald-50 border border-forest-100">
                <div className="flex flex-col sm:flex-row gap-6 items-start">
                  <img
                    src={author.avatar}
                    alt={author.name}
                    className="w-20 h-20 rounded-2xl object-cover ring-2 ring-white shadow-soft"
                  />
                  <div className="flex-1">
                    <p className="text-xs uppercase tracking-wider text-forest-700 font-semibold mb-2">
                      About the author
                    </p>
                    <h4 className="font-serif text-2xl font-bold text-foreground mb-2">
                      {author.name}
                    </h4>
                    <p className="text-sm text-forest-700 mb-3 font-medium">{author.role}</p>
                    <p className="text-foreground/70 text-sm leading-relaxed">{author.bio}</p>
                  </div>
                </div>
              </div>

              {/* Share section */}
              <div className="mt-12 pt-8 border-t border-earth-200">
                <h3 className="text-sm font-semibold text-foreground uppercase tracking-wider mb-4">
                  Share this article
                </h3>
                <div className="flex flex-wrap gap-3">
                  <ShareButton
                    href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(post.title)}&url=${encodeURIComponent(shareUrl)}`}
                    icon={<Twitter className="w-5 h-5" />}
                    label="Share on X"
                  />
                  <ShareButton
                    href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`}
                    icon={<Facebook className="w-5 h-5" />}
                    label="Share on Facebook"
                  />
                  <ShareButton
                    href={`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`}
                    icon={<Linkedin className="w-5 h-5" />}
                    label="Share on LinkedIn"
                  />
                  <button
                    type="button"
                    onClick={copyLink}
                    className="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-earth-50 hover:bg-earth-100 border border-earth-200 text-foreground/70 hover:text-foreground transition-colors"
                  >
                    {shareCopied ? <Check className="w-5 h-5 text-forest-600" /> : <Copy className="w-5 h-5" />}
                    {shareCopied ? 'Copied!' : 'Copy link'}
                  </button>
                </div>
              </div>
            </article>

            {/* Sidebar */}
            <aside className="lg:sticky lg:top-24 lg:self-start space-y-6">
              {toc.length > 0 && (
                <div className="bg-white border border-earth-200 rounded-2xl p-6 shadow-soft">
                  <h4 className="text-sm font-semibold text-foreground uppercase tracking-wider mb-4">
                    Table of Contents
                  </h4>
                  <nav className="space-y-2">
                    {toc.map((h) => (
                      <a
                        key={h.id}
                        href={`#${h.id}`}
                        className={`block py-2 pl-3 border-l-2 text-sm transition-colors ${
                          activeHeading === h.id
                            ? 'border-forest-600 text-forest-700 font-semibold'
                            : 'border-earth-200 text-foreground/60 hover:text-foreground hover:border-forest-400'
                        }`}
                      >
                        {h.text}
                      </a>
                    ))}
                  </nav>
                </div>
              )}

              <div className="bg-gradient-to-br from-forest-600 to-forest-800 text-white rounded-2xl p-6 shadow-medium">
                <h4 className="font-serif text-xl font-bold mb-3">Support Our Work</h4>
                <p className="text-sm text-white/85 mb-4 leading-relaxed">
                  Every article we publish is fueled by the forest you help restore. Join us in making a difference.
                </p>
                <Link
                  href="/donate"
                  className="inline-flex items-center justify-center w-full px-4 py-3 rounded-xl bg-white text-forest-700 font-semibold hover:bg-forest-50 transition-colors"
                >
                  Donate Now
                  <ArrowRight className="w-4 h-4 ml-2" />
                </Link>
              </div>
            </aside>
          </div>
        </section>

        {/* Related */}
        {related.length > 0 && (
          <section className="py-20 sm:py-24 bg-gradient-to-b from-background to-forest-50/30">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">
              <div className="flex items-end justify-between mb-12">
                <div>
                  <h2 className="font-serif text-3xl sm:text-4xl font-bold text-foreground mb-2">
                    Related Articles
                  </h2>
                  <p className="text-foreground/60">Continue exploring our stories</p>
                </div>
                <Link
                  href="/blog"
                  className="hidden sm:inline-flex items-center gap-2 text-forest-700 hover:text-forest-800 font-medium"
                >
                  View all articles
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>

              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {related.map((p) => (
                  <Link
                    key={p.id}
                    href={`/blog/${p.slug}`}
                    className="group bg-white border border-earth-200 rounded-2xl overflow-hidden hover:shadow-elevated hover:border-forest-400/50 transition-all duration-300"
                  >
                    <div className="relative h-48 overflow-hidden">
                      <img
                        src={p.image}
                        alt={p.title}
                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
                      <span className="absolute top-4 left-4 bg-white text-forest-900 text-xs font-semibold px-3 py-1.5 rounded-full">
                        {p.category}
                      </span>
                    </div>
                    <div className="p-6">
                      <h3 className="font-serif text-lg font-semibold text-foreground mb-2 group-hover:text-forest-600 transition-colors line-clamp-2">
                        {p.title}
                      </h3>
                      <p className="text-foreground/60 text-sm line-clamp-2 mb-4">{p.excerpt}</p>
                      <div className="flex items-center justify-between text-sm text-foreground/50">
                        <span className="inline-flex items-center gap-1.5">
                          <UserIcon className="w-3.5 h-3.5" />
                          {p.author}
                        </span>
                        <span className="inline-flex items-center gap-1.5">
                          <Clock className="w-3.5 h-3.5" />
                          {p.readTime}
                        </span>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          </section>
        )}
      </main>
    </div>
  );
}

// ---------- Subcomponents ----------

function ShareButton({ href, icon, label }: { href: string; icon: React.ReactNode; label: string }) {
  return (
    <a
      href={href}
      target="_blank"
      rel="noopener noreferrer"
      aria-label={label}
      className="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-forest-600 hover:bg-forest-700 text-white transition-colors"
    >
      {icon}
      <span className="font-medium">{label}</span>
    </a>
  );
}
