'use client';

import { useState, useEffect } from 'react';
import { Plus, Edit2, Trash2, Eye, EyeOff, Star, Calendar, Clock, User } from 'lucide-react';
import RichTextEditor from './RichTextEditor';

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

const CATEGORIES = ['All', 'Ecology', 'Field Report', 'Urban Ecology', 'Community', 'Technology', 'Climate Science'];

export default function BlogModule() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [authors, setAuthors] = useState<BlogAuthor[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('All');
  const [showPublished, setShowPublished] = useState<boolean | null>(null);
  const [editingPost, setEditingPost] = useState<BlogPost | null>(null);
  const [isCreating, setIsCreating] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [postsRes, authorsRes] = await Promise.all([
        fetch('/api/admin/data?key=blog_posts'),
        fetch('/api/admin/data?key=blog_authors')
      ]);
      
      const postsData = await postsRes.json();
      const authorsData = await authorsRes.json();
      
      if (postsData.ok) setPosts(postsData.value || []);
      if (authorsData.ok) setAuthors(authorsData.value || []);
    } catch (error) {
      console.error('Failed to load blog data:', error);
    } finally {
      setLoading(false);
    }
  };

  const togglePublish = async (post: BlogPost) => {
    try {
      const res = await fetch('/api/admin/data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          key: 'blog_posts/update',
          value: { id: post.id, data: { published: !post.published } }
        })
      });
      
      if (res.ok) {
        setPosts(posts.map(p => p.id === post.id ? { ...p, published: !p.published } : p));
      }
    } catch (error) {
      console.error('Failed to toggle publish:', error);
    }
  };

  const toggleFeatured = async (post: BlogPost) => {
    try {
      const res = await fetch('/api/admin/data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          key: 'blog_posts/update',
          value: { id: post.id, data: { featured: !post.featured } }
        })
      });
      
      if (res.ok) {
        setPosts(posts.map(p => p.id === post.id ? { ...p, featured: !p.featured } : p));
      }
    } catch (error) {
      console.error('Failed to toggle featured:', error);
    }
  };

  const deletePost = async (id: string) => {
    if (!confirm('Are you sure you want to delete this post?')) return;
    
    try {
      const res = await fetch('/api/admin/data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          key: 'blog_posts/delete',
          value: { id }
        })
      });
      
      if (res.ok) {
        setPosts(posts.filter(p => p.id !== id));
      }
    } catch (error) {
      console.error('Failed to delete post:', error);
    }
  };

  const filteredPosts = posts.filter(post => {
    if (filter !== 'All' && post.category !== filter) return false;
    if (showPublished !== null && post.published !== showPublished) return false;
    return true;
  });

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-forest-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="font-serif text-2xl font-bold text-foreground">Blog Management</h2>
          <p className="text-foreground/60 text-sm mt-1">Create and manage blog posts</p>
        </div>
        <button
          onClick={() => setIsCreating(true)}
          className="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 hover:bg-forest-700 text-white rounded-lg font-medium transition-colors"
        >
          <Plus className="w-4 h-4" />
          New Post
        </button>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-3 p-4 bg-white border border-earth-200 rounded-xl">
        <div className="flex gap-2">
          {CATEGORIES.map(cat => (
            <button
              key={cat}
              onClick={() => setFilter(cat)}
              className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
                filter === cat
                  ? 'bg-forest-600 text-white'
                  : 'bg-earth-50 text-foreground/70 hover:bg-earth-100'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
        <div className="flex gap-2 ml-auto">
          <button
            onClick={() => setShowPublished(null)}
            className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
              showPublished === null
                ? 'bg-forest-600 text-white'
                : 'bg-earth-50 text-foreground/70 hover:bg-earth-100'
            }`}
          >
            All
          </button>
          <button
            onClick={() => setShowPublished(true)}
            className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
              showPublished === true
                ? 'bg-forest-600 text-white'
                : 'bg-earth-50 text-foreground/70 hover:bg-earth-100'
            }`}
          >
            Published
          </button>
          <button
            onClick={() => setShowPublished(false)}
            className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
              showPublished === false
                ? 'bg-forest-600 text-white'
                : 'bg-earth-50 text-foreground/70 hover:bg-earth-100'
            }`}
          >
            Drafts
          </button>
        </div>
      </div>

      {/* Posts List */}
      <div className="space-y-3">
        {filteredPosts.length === 0 ? (
          <div className="text-center py-12 bg-white border border-dashed border-earth-200 rounded-xl">
            <p className="text-foreground/60">No posts found</p>
          </div>
        ) : (
          filteredPosts.map(post => (
            <div
              key={post.id}
              className="bg-white border border-earth-200 rounded-xl p-4 hover:shadow-soft transition-shadow"
            >
              <div className="flex items-start gap-4">
                <img
                  src={post.image}
                  alt={post.title}
                  className="w-20 h-20 object-cover rounded-lg flex-shrink-0"
                />
                <div className="flex-1 min-w-0">
                  <div className="flex items-start justify-between gap-4">
                    <div className="min-w-0">
                      <h3 className="font-serif text-lg font-semibold text-foreground truncate">
                        {post.title}
                      </h3>
                      <p className="text-sm text-foreground/60 mt-1 line-clamp-2">{post.excerpt}</p>
                    </div>
                    <div className="flex items-center gap-2 flex-shrink-0">
                      <button
                        onClick={() => toggleFeatured(post)}
                        className={`p-2 rounded-lg transition-colors ${
                          post.featured ? 'bg-yellow-100 text-yellow-700' : 'bg-earth-50 text-foreground/60 hover:bg-earth-100'
                        }`}
                        title={post.featured ? 'Remove from featured' : 'Add to featured'}
                      >
                        <Star className={`w-4 h-4 ${post.featured ? 'fill-current' : ''}`} />
                      </button>
                      <button
                        onClick={() => togglePublish(post)}
                        className={`p-2 rounded-lg transition-colors ${
                          post.published ? 'bg-green-100 text-green-700' : 'bg-earth-50 text-foreground/60 hover:bg-earth-100'
                        }`}
                        title={post.published ? 'Unpublish' : 'Publish'}
                      >
                        {post.published ? <Eye className="w-4 h-4" /> : <EyeOff className="w-4 h-4" />}
                      </button>
                      <button
                        onClick={() => setEditingPost(post)}
                        className="p-2 rounded-lg bg-earth-50 text-foreground/60 hover:bg-earth-100 transition-colors"
                        title="Edit"
                      >
                        <Edit2 className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => deletePost(post.id)}
                        className="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                        title="Delete"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                  <div className="flex items-center gap-4 mt-3 text-xs text-foreground/50">
                    <span className="inline-flex items-center gap-1">
                      <User className="w-3 h-3" />
                      {post.author}
                    </span>
                    <span className="inline-flex items-center gap-1">
                      <Calendar className="w-3 h-3" />
                      {post.date}
                    </span>
                    <span className="inline-flex items-center gap-1">
                      <Clock className="w-3 h-3" />
                      {post.readTime}
                    </span>
                    <span className={`px-2 py-0.5 rounded-full ${
                      post.published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                    }`}>
                      {post.published ? 'Published' : 'Draft'}
                    </span>
                    <span className="px-2 py-0.5 rounded-full bg-forest-100 text-forest-700">
                      {post.category}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Create/Edit Modal */}
      {(isCreating || editingPost) && (
        <BlogPostEditor
          post={editingPost}
          authors={authors}
          onClose={() => {
            setIsCreating(false);
            setEditingPost(null);
          }}
          onSave={() => {
            setIsCreating(false);
            setEditingPost(null);
            loadData();
          }}
        />
      )}
    </div>
  );
}

function BlogPostEditor({ post, authors, onClose, onSave }: { post: BlogPost | null; authors: BlogAuthor[]; onClose: () => void; onSave: () => void }) {
  const [formData, setFormData] = useState<Partial<BlogPost>>(
    post || {
      title: '',
      subtitle: '',
      author: authors[0]?.name || '',
      date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }),
      isoDate: new Date().toISOString().split('T')[0],
      readTime: '5 min read',
      category: 'Ecology',
      tags: [],
      image: '',
      imageCredit: '',
      excerpt: '',
      content: '',
      published: false,
      featured: false
    }
  );
  const [saving, setSaving] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);

    try {
      const key = post ? 'blog_posts/update' : 'blog_posts/create';
      const value = post 
        ? { id: post.id, data: formData }
        : formData;

      const res = await fetch('/api/admin/data', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ key, value })
      });

      if (res.ok) {
        onSave();
      }
    } catch (error) {
      console.error('Failed to save post:', error);
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-[100]">
      <div className="bg-white rounded-2xl max-w-4xl w-full max-h-[60vh] overflow-y-auto">
        <div className="sticky top-0 bg-white border-b border-earth-200 p-6 flex items-center justify-between">
          <h3 className="font-serif text-xl font-bold">{post ? 'Edit Post' : 'Create New Post'}</h3>
          <button
            onClick={onClose}
            className="p-2 hover:bg-earth-100 rounded-lg transition-colors"
          >
            ✕
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          <div className="grid md:grid-cols-2 gap-6">
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Title *</label>
              <input
                type="text"
                value={formData.title || ''}
                onChange={e => setFormData({ ...formData, title: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                required
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Subtitle</label>
              <input
                type="text"
                value={formData.subtitle || ''}
                onChange={e => setFormData({ ...formData, subtitle: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Author *</label>
              <input
                type="text"
                list="author-list"
                value={formData.author || ''}
                onChange={e => setFormData({ ...formData, author: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                placeholder="Enter author name"
                required
              />
              <datalist id="author-list">
                {authors.map(author => (
                  <option key={author.id} value={author.name}>{author.name} ({author.role})</option>
                ))}
              </datalist>
              <p className="text-xs text-foreground/50 mt-1">Type existing author name or enter a new one</p>
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Category *</label>
              <select
                value={formData.category || ''}
                onChange={e => setFormData({ ...formData, category: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                required
              >
                {CATEGORIES.filter(c => c !== 'All').map(cat => (
                  <option key={cat} value={cat}>{cat}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Date *</label>
              <input
                type="date"
                value={formData.isoDate || ''}
                onChange={e => {
                  const date = new Date(e.target.value);
                  setFormData({
                    ...formData,
                    isoDate: e.target.value,
                    date: date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
                  });
                }}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Read Time *</label>
              <input
                type="text"
                value={formData.readTime || ''}
                onChange={e => setFormData({ ...formData, readTime: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                placeholder="e.g., 5 min read"
                required
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Featured Image URL *</label>
              <input
                type="url"
                value={formData.image || ''}
                onChange={e => setFormData({ ...formData, image: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                required
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Image Credit</label>
              <input
                type="text"
                value={formData.imageCredit || ''}
                onChange={e => setFormData({ ...formData, imageCredit: e.target.value })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Excerpt *</label>
              <textarea
                value={formData.excerpt || ''}
                onChange={e => setFormData({ ...formData, excerpt: e.target.value })}
                rows={3}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                required
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Content *</label>
              <RichTextEditor
                content={formData.content || ''}
                onChange={content => setFormData({ ...formData, content })}
              />
            </div>

            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-foreground mb-2">Tags (comma-separated)</label>
              <input
                type="text"
                value={formData.tags?.join(', ') || ''}
                onChange={e => setFormData({ ...formData, tags: e.target.value.split(',').map(t => t.trim()).filter(Boolean) })}
                className="w-full px-4 py-2 border border-earth-200 rounded-lg focus:outline-none focus:border-forest-400"
                placeholder="e.g., Ecology, Biodiversity, Forest"
              />
            </div>

            <div className="md:col-span-2 flex items-center gap-6">
              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={formData.published || false}
                  onChange={e => setFormData({ ...formData, published: e.target.checked })}
                  className="w-4 h-4 text-forest-600 rounded"
                />
                <span className="text-sm font-medium">Published</span>
              </label>

              <label className="flex items-center gap-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={formData.featured || false}
                  onChange={e => setFormData({ ...formData, featured: e.target.checked })}
                  className="w-4 h-4 text-forest-600 rounded"
                />
                <span className="text-sm font-medium">Featured</span>
              </label>
            </div>
          </div>

          <div className="flex justify-end gap-3 pt-4 border-t border-earth-200">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 border border-earth-200 rounded-lg hover:bg-earth-50 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={saving}
              className="px-6 py-2 bg-forest-600 hover:bg-forest-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
            >
              {saving ? 'Saving...' : post ? 'Update Post' : 'Create Post'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
