'use client';

import { useEffect, useMemo, useRef, useState } from 'react';
import Link from 'next/link';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Calendar, Clock, User, Search, X, TrendingUp, Sparkles } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

const categories = ['All', 'Ecology', 'Field Report', 'Urban Ecology', 'Community', 'Technology', 'Climate Science'];

const POSTS_PER_PAGE = 9;

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

export default function BlogView() {
  const sectionRef = useRef<HTMLElement>(null);
  const postsRef = useRef<HTMLDivElement>(null);
  const featuredRef = useRef<HTMLDivElement>(null);

  const [query, setQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState<string>('All');
  const [visibleCount, setVisibleCount] = useState(POSTS_PER_PAGE);
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchPosts();
  }, []);

  const fetchPosts = async () => {
    try {
      const res = await fetch('/api/public/blog');
      const data = await res.json();
      if (data.posts) {
        setPosts(data.posts);
      }
    } catch (error) {
      console.error('Failed to fetch posts:', error);
    } finally {
      setLoading(false);
    }
  };

  // Reset pagination when filters change.
  useEffect(() => {
    setVisibleCount(POSTS_PER_PAGE);
  }, [query, activeCategory]);

  const filteredPosts = useMemo(() => {
    const q = query.trim().toLowerCase();
    return posts.filter((post) => {
      const matchesCategory = activeCategory === 'All' || post.category === activeCategory;
      if (!matchesCategory) return false;
      if (!q) return true;
      return (
        post.title.toLowerCase().includes(q) ||
        post.excerpt.toLowerCase().includes(q) ||
        post.author.toLowerCase().includes(q) ||
        post.category.toLowerCase().includes(q)
      );
    });
  }, [query, activeCategory, posts]);

  const featuredPosts = posts.filter(p => p.featured && p.published).slice(0, 3);
  const regularPosts = filteredPosts.filter(p => !p.featured);
  const visiblePosts = regularPosts.slice(0, visibleCount);
  const hasMore = visibleCount < regularPosts.length;
  const hasResults = filteredPosts.length > 0;

  // Animate cards whenever the rendered set changes (filter/search/paginate).
  useEffect(() => {
    const ctx = gsap.context(() => {
      if (postsRef.current && postsRef.current.children.length) {
        gsap.fromTo(
          Array.from(postsRef.current.children),
          { opacity: 0, y: 24 },
          { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, ease: 'power2.out' }
        );
      }
    }, sectionRef);
    return () => ctx.revert();
  }, [visiblePosts.length, activeCategory, query]);

  // Animate featured posts
  useEffect(() => {
    const ctx = gsap.context(() => {
      if (featuredRef.current && featuredRef.current.children.length) {
        gsap.fromTo(
          Array.from(featuredRef.current.children),
          { opacity: 0, scale: 0.95 },
          { opacity: 1, scale: 1, duration: 0.6, stagger: 0.15, ease: 'power2.out' }
        );
      }
    }, sectionRef);
    return () => ctx.revert();
  }, [featuredPosts.length]);

  if (loading) {
    return (
      <div className="relative min-h-screen bg-background">
        <div className="flex items-center justify-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-forest-600"></div>
        </div>
      </div>
    );
  }

  return (
    <div className="relative min-h-screen bg-background">
      <main className="relative">
        {/* Hero Section */}
        <section
          ref={sectionRef}
          className="relative min-h-[70vh] flex items-center justify-center overflow-hidden"
        >
          <div className="absolute inset-0 bg-gradient-to-br from-forest-900 via-forest-800 to-emerald-900" />
          <div className="absolute inset-0 opacity-30">
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.1)_0%,transparent_50%)]" />
            <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,rgba(255,255,255,0.08)_0%,transparent_50%)]" />
          </div>
          
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
            <div className="max-w-5xl mx-auto text-center">
              <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/90 text-sm mb-6">
                <Sparkles className="w-4 h-4" />
                <span className="font-medium">Insights from the Field</span>
              </div>
              <h1 className="font-serif text-5xl sm:text-6xl md:text-7xl font-bold text-white mb-6 leading-tight tracking-tight">
                Our Blog
              </h1>
              <p className="text-xl sm:text-2xl text-white/80 max-w-3xl mx-auto leading-relaxed">
                Stories, insights, and updates from our global forest restoration work
              </p>
            </div>
          </div>
        </section>

        {/* Featured Posts */}
        {featuredPosts.length > 0 && (
          <section className="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20 bg-gradient-to-b from-background to-forest-50/30">
            <div className="max-w-7xl mx-auto">
              <div className="flex items-center gap-3 mb-10">
                <TrendingUp className="w-6 h-6 text-forest-600" />
                <h2 className="font-serif text-2xl sm:text-3xl font-bold text-foreground">Featured Stories</h2>
              </div>
              <div
                ref={featuredRef}
                className="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
              >
                {featuredPosts.map((post) => (
                  <Link
                    key={post.id}
                    href={`/blog/${post.slug}`}
                    className="group relative bg-white rounded-3xl overflow-hidden shadow-elevated hover:shadow-2xl transition-all duration-500 hover:-translate-y-2"
                  >
                    <div className="relative h-64 overflow-hidden">
                      <img
                        src={post.image}
                        alt={post.title}
                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
                      <span className="absolute top-4 left-4 bg-white text-forest-900 text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
                        {post.category}
                      </span>
                      <span className="absolute top-4 right-4 bg-forest-600 text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-1.5">
                        <Sparkles className="w-3 h-3" />
                        Featured
                      </span>
                    </div>
                    <div className="p-6">
                      <h3 className="font-serif text-xl font-bold text-foreground mb-3 group-hover:text-forest-600 transition-colors duration-300 line-clamp-2">
                        {post.title}
                      </h3>
                      {post.subtitle && (
                        <p className="text-foreground/60 text-sm mb-4 line-clamp-2">{post.subtitle}</p>
                      )}
                      <div className="flex items-center gap-4 text-sm text-foreground/50">
                        <span className="flex items-center gap-1.5">
                          <User className="w-4 h-4" />
                          {post.author}
                        </span>
                        <span className="flex items-center gap-1.5">
                          <Clock className="w-4 h-4" />
                          {post.readTime}
                        </span>
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          </section>
        )}

        {/* Main Content */}
        <section className="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20">
          <div className="max-w-7xl mx-auto">
            <div className="flex flex-col lg:flex-row gap-8">
              {/* Main Content Area */}
              <div className="flex-1">
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                  <h2 className="font-serif text-2xl sm:text-3xl font-bold text-foreground">
                    {activeCategory === 'All' ? 'All Articles' : activeCategory}
                  </h2>

                  {/* Search */}
                  <div className="group relative w-full sm:w-80">
                    <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-foreground/40 group-focus-within:text-forest-600 transition-colors" />
                    <input
                      type="text"
                      value={query}
                      onChange={(e) => setQuery(e.target.value)}
                      placeholder="Search articles..."
                      aria-label="Search articles"
                      className="w-full pl-12 pr-12 py-3 bg-white border border-earth-200 rounded-2xl text-foreground placeholder-foreground/40 focus:outline-none focus:border-forest-400 focus:ring-2 focus:ring-forest-400/20 shadow-soft transition-all"
                    />
                    {query ? (
                      <button
                        type="button"
                        onClick={() => setQuery('')}
                        aria-label="Clear search"
                        className="absolute right-4 top-1/2 -translate-y-1/2 p-1 rounded-full text-foreground/40 hover:text-foreground hover:bg-earth-100 transition-colors"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    ) : null}
                  </div>
                </div>

                {/* Category Pills */}
                <div className="flex flex-wrap gap-2 mb-8">
                  {categories.map((category) => {
                    const isActive = activeCategory === category;
                    return (
                      <button
                        key={category}
                        type="button"
                        onClick={() => setActiveCategory(category)}
                        className={[
                          'px-5 py-2.5 rounded-full text-sm font-medium border transition-all duration-200',
                          isActive
                            ? 'bg-forest-600 text-white border-forest-600 shadow-medium hover:bg-forest-700'
                            : 'bg-white text-foreground/70 border-earth-200 hover:border-forest-300 hover:text-forest-700 hover:bg-forest-50'
                        ].join(' ')}
                        aria-pressed={isActive}
                      >
                        {category}
                      </button>
                    );
                  })}
                </div>

                {/* Results Meta */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 text-sm text-foreground/60">
                  <p>
                    <span className="font-semibold text-foreground">{Math.min(visibleCount, regularPosts.length)}</span> of{' '}
                    <span className="font-semibold text-foreground">{regularPosts.length}</span> articles
                    {activeCategory !== 'All' && (
                      <span> in <span className="font-semibold text-forest-700">{activeCategory}</span></span>
                    )}
                    {query && (
                      <span> matching "<span className="font-semibold text-forest-700">{query}</span>"</span>
                    )}
                  </p>
                  {(query || activeCategory !== 'All') && (
                    <button
                      type="button"
                      onClick={() => {
                        setQuery('');
                        setActiveCategory('All');
                      }}
                      className="text-forest-700 hover:text-forest-800 font-medium hover:underline"
                    >
                      Clear filters
                    </button>
                  )}
                </div>

                {/* Posts Grid */}
                {hasResults ? (
                  <>
                    <div
                      ref={postsRef}
                      className="grid md:grid-cols-2 gap-6"
                    >
                      {visiblePosts.map((post) => (
                        <Link
                          key={post.id}
                          href={`/blog/${post.slug}`}
                          className="group bg-white border border-earth-200 rounded-2xl overflow-hidden hover:shadow-elevated hover:border-forest-400/50 hover:-translate-y-1 transition-all duration-300"
                        >
                          <div className="relative h-52 overflow-hidden">
                            <img
                              src={post.image}
                              alt={post.title}
                              className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
                            <span className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-forest-900 text-xs font-semibold px-3 py-1.5 rounded-full">
                              {post.category}
                            </span>
                          </div>
                          <div className="p-6">
                            <h3 className="font-serif text-lg font-semibold text-foreground mb-2 group-hover:text-forest-600 transition-colors duration-300 line-clamp-2">
                              {post.title}
                            </h3>
                            <p className="text-foreground/60 text-sm mb-4 line-clamp-2">{post.excerpt}</p>
                            <div className="flex items-center gap-4 text-xs text-foreground/50">
                              <span className="flex items-center gap-1.5">
                                <User className="w-3.5 h-3.5" />
                                {post.author}
                              </span>
                              <span className="flex items-center gap-1.5">
                                <Calendar className="w-3.5 h-3.5" />
                                {post.date}
                              </span>
                              <span className="flex items-center gap-1.5">
                                <Clock className="w-3.5 h-3.5" />
                                {post.readTime}
                              </span>
                            </div>
                          </div>
                        </Link>
                      ))}
                    </div>

                    {hasMore && (
                      <div className="mt-12 text-center">
                        <button
                          type="button"
                          onClick={() => setVisibleCount((c) => c + POSTS_PER_PAGE)}
                          className="inline-flex items-center gap-2 px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-medium rounded-xl transition-all duration-300 shadow-medium hover:shadow-elevated"
                        >
                          Load More Articles
                        </button>
                      </div>
                    )}
                  </>
                ) : (
                  <div className="text-center py-20 bg-white/60 border border-dashed border-earth-200 rounded-3xl">
                    <p className="font-serif text-2xl text-foreground mb-2">No articles found</p>
                    <p className="text-foreground/60 mb-6">
                      Try a different search term or category.
                    </p>
                    <button
                      type="button"
                      onClick={() => {
                        setQuery('');
                        setActiveCategory('All');
                      }}
                      className="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-forest-600 text-white font-medium hover:bg-forest-700 transition-colors"
                    >
                      Reset filters
                    </button>
                  </div>
                )}
              </div>

              {/* Sidebar */}
              <aside className="lg:w-80 space-y-8">
                <div className="bg-white border border-earth-200 rounded-2xl p-6 shadow-soft">
                  <h3 className="font-serif text-lg font-bold text-foreground mb-4">Categories</h3>
                  <div className="space-y-2">
                    {categories.filter(c => c !== 'All').map((category) => {
                      const count = posts.filter(p => p.category === category && p.published).length;
                      return (
                        <button
                          key={category}
                          onClick={() => setActiveCategory(category)}
                          className={`w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm transition-colors ${
                            activeCategory === category
                              ? 'bg-forest-50 text-forest-700 font-medium'
                              : 'text-foreground/70 hover:bg-earth-50'
                          }`}
                        >
                          <span>{category}</span>
                          <span className={`text-xs px-2 py-0.5 rounded-full ${
                            activeCategory === category
                              ? 'bg-forest-200 text-forest-800'
                              : 'bg-earth-100 text-foreground/60'
                          }`}>
                            {count}
                          </span>
                        </button>
                      );
                    })}
                  </div>
                </div>

                <div className="bg-gradient-to-br from-forest-600 to-forest-800 text-white rounded-2xl p-6 shadow-medium">
                  <h3 className="font-serif text-xl font-bold mb-3">Join Our Mission</h3>
                  <p className="text-sm text-white/85 mb-4">
                    Every tree planted makes a difference. Support our restoration work today.
                  </p>
                  <Link
                    href="/donate"
                    className="inline-flex items-center justify-center w-full px-4 py-3 rounded-xl bg-white text-forest-700 font-semibold hover:bg-forest-50 transition-colors"
                  >
                    Donate Now
                  </Link>
                </div>
              </aside>
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
