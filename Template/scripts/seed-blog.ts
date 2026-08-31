// Seed script to migrate existing blog data to the new dynamic blog system
// Run with: npx tsx scripts/seed-blog.ts

import { getDb } from '../src/server/db';
import { blogPosts } from '../src/data/blog';

async function seedBlogData() {
  const db = getDb();
  
  console.log('Seeding blog authors...');
  
  // Seed authors first
  const authors = [
    {
      name: 'Sarah Johnson',
      role: 'Lead Ecologist',
      avatar: 'https://i.pravatar.cc/200?img=47',
      bio: 'Sarah has spent 15 years studying forest ecosystems across three continents. She leads our species-selection research and writes about the science of restoration.'
    },
    {
      name: 'David Mutua',
      role: 'Field Director, East Africa',
      avatar: 'https://i.pravatar.cc/200?img=12',
      bio: 'David coordinates on-the-ground planting operations across Kenya and Tanzania. He shares stories from the communities we partner with.'
    },
    {
      name: 'Emma Chen',
      role: 'Urban Forestry Researcher',
      avatar: 'https://i.pravatar.cc/200?img=32',
      bio: 'Emma studies how cities can use trees to fight heat, pollution, and social inequality. She holds a PhD in environmental planning.'
    },
    {
      name: 'Michael Rodriguez',
      role: 'Community Programs Lead',
      avatar: 'https://i.pravatar.cc/200?img=15',
      bio: 'Michael helps neighbours turn sidewalks and vacant lots into thriving micro-forests. He runs our community toolkit and training program.'
    },
    {
      name: 'Lisa Wang',
      role: 'Head of Technology',
      avatar: 'https://i.pravatar.cc/200?img=45',
      bio: 'Lisa builds the data tools that power our planting map, monitoring drones, and species-prediction models.'
    },
    {
      name: 'Dr. Robert Green',
      role: 'Climate Science Advisor',
      avatar: 'https://i.pravatar.cc/200?img=68',
      bio: 'Robert translates the latest climate research into practical guidance for restoration projects worldwide.'
    }
  ];

  for (const author of authors) {
    const id = `author-${author.name.toLowerCase().replace(/\s+/g, '-')}`;
    try {
      db.prepare(
        `INSERT OR IGNORE INTO blog_authors (id, name, role, avatar, bio, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))`
      ).run(id, author.name, author.role, author.avatar, author.bio);
      console.log(`  ✓ Author: ${author.name}`);
    } catch (error) {
      console.error(`  ✗ Error inserting author ${author.name}:`, error);
    }
  }

  console.log('\nSeeding blog posts...');
  
  // Convert blog posts to new format
  for (const post of blogPosts) {
    const id = `post-${post.slug}`;
    
    // Convert blocks to markdown-like content
    const content = post.blocks.map(block => {
      if (block.type === 'paragraph') return block.text;
      if (block.type === 'heading') return `## ${block.text}`;
      if (block.type === 'list') return block.items.map(item => `- ${item}`).join('\n');
      if (block.type === 'quote') return `> "${block.text}"${block.cite ? ` — ${block.cite}` : ''}`;
      if (block.type === 'callout') return `**${block.title || 'Note'}**: ${block.text}`;
      return '';
    }).join('\n\n');

    try {
      db.prepare(
        `INSERT OR IGNORE INTO blog_posts 
         (id, slug, title, subtitle, author, date, iso_date, read_time, category, tags, image, image_credit, excerpt, content, published, featured, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))`
      ).run(
        id,
        post.slug,
        post.title,
        post.subtitle || null,
        post.author,
        post.date,
        post.isoDate,
        post.readTime,
        post.category,
        JSON.stringify(post.tags),
        post.image,
        post.imageCredit || null,
        post.excerpt,
        content,
        1, // published
        0, // not featured by default
      );
      console.log(`  ✓ Post: ${post.title}`);
    } catch (error) {
      console.error(`  ✗ Error inserting post ${post.title}:`, error);
    }
  }

  console.log('\n✅ Blog data seeded successfully!');
}

seedBlogData().catch(console.error);
