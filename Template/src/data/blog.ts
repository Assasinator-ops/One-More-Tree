// Shared blog data + helpers. Used by BlogView (list) and BlogPostView (detail).
// The blog data is inlined here because there is no admin `blog` module yet —
// when that ships, this file is the single place to swap in a CMS source.

export type BlogAuthor = {
  name: string;
  role: string;
  avatar: string;
  bio: string;
};

export type BlogBlock =
  | { type: 'paragraph'; text: string }
  | { type: 'heading'; text: string }
  | { type: 'quote'; text: string; cite?: string }
  | { type: 'list'; items: string[] }
  | { type: 'callout'; title?: string; text: string };

export type BlogPost = {
  slug: string;
  title: string;
  subtitle: string;
  author: string;
  date: string;
  isoDate: string;
  readTime: string;
  category: string;
  tags: string[];
  image: string;
  imageCredit?: string;
  excerpt: string;
  comments: string;
  blocks: BlogBlock[];
};

const authorFor = (name: string): BlogAuthor => {
  const map: Record<string, BlogAuthor> = {
    'Sarah Johnson': {
      name: 'Sarah Johnson',
      role: 'Lead Ecologist',
      avatar: 'https://i.pravatar.cc/200?img=47',
      bio:
        'Sarah has spent 15 years studying forest ecosystems across three continents. She leads our species-selection research and writes about the science of restoration.'
    },
    'David Mutua': {
      name: 'David Mutua',
      role: 'Field Director, East Africa',
      avatar: 'https://i.pravatar.cc/200?img=12',
      bio:
        'David coordinates on-the-ground planting operations across Kenya and Tanzania. He shares stories from the communities we partner with.'
    },
    'Emma Chen': {
      name: 'Emma Chen',
      role: 'Urban Forestry Researcher',
      avatar: 'https://i.pravatar.cc/200?img=32',
      bio:
        'Emma studies how cities can use trees to fight heat, pollution, and social inequality. She holds a PhD in environmental planning.'
    },
    'Michael Rodriguez': {
      name: 'Michael Rodriguez',
      role: 'Community Programs Lead',
      avatar: 'https://i.pravatar.cc/200?img=15',
      bio:
        'Michael helps neighbours turn sidewalks and vacant lots into thriving micro-forests. He runs our community toolkit and training program.'
    },
    'Lisa Wang': {
      name: 'Lisa Wang',
      role: 'Head of Technology',
      avatar: 'https://i.pravatar.cc/200?img=45',
      bio:
        'Lisa builds the data tools that power our planting map, monitoring drones, and species-prediction models.'
    },
    'Dr. Robert Green': {
      name: 'Dr. Robert Green',
      role: 'Climate Science Advisor',
      avatar: 'https://i.pravatar.cc/200?img=68',
      bio:
        'Robert translates the latest climate research into practical guidance for restoration projects worldwide.'
    }
  };
  return (
    map[name] ?? {
      name,
      role: 'Contributor',
      avatar: `https://i.pravatar.cc/200?u=${encodeURIComponent(name)}`,
      bio: `${name} is a contributor to the OneMoreTree blog.`
    }
  );
};

export const getAuthor = (name: string): BlogAuthor => authorFor(name);

export const blogPosts: BlogPost[] = [
  {
    slug: 'native-tree-species-reforestation',
    title: 'The Importance of Native Tree Species in Reforestation',
    subtitle:
      'Why local genetics — not just local species names — are the real foundation of a forest that lasts.',
    author: 'Sarah Johnson',
    date: 'November 15, 2024',
    isoDate: '2024-11-15',
    readTime: '8 min read',
    category: 'Ecology',
    tags: ['Biodiversity', 'Restoration', 'Ecology'],
    image: '/mature-tree.jpg',
    imageCredit: 'Photo: OneMoreTree field team, Atlantic Forest, Brazil',
    excerpt:
      'Understanding why planting native tree species is crucial for restoring ecosystems and supporting biodiversity.',
    comments: '23',
    blocks: [
      {
        type: 'paragraph',
        text:
          'When a forest is lost, the most obvious thing to plant in its place is a tree. The less obvious, and far more important, question is: which tree? Across two decades of restoration work, we have learned that the answer is almost always a native species — and increasingly, a native genotype from a local seed source.'
      },
      {
        type: 'paragraph',
        text:
          'A native tree is one that evolved in a particular region alongside the insects, fungi, birds, and soil microbes that depend on it. Replace it with a fast-growing exotic, and the tree may survive — but the forest around it never will.'
      },
      { type: 'heading', text: 'Why exotics underperform over time' },
      {
        type: 'paragraph',
        text:
          'Exotic plantation species can look spectacular in year three and heartbreaking in year fifteen. They often lack the mutualisms that native trees rely on for nutrients, pest resistance, and pollination. Many also fail to support local food webs: an oak might host 500 species of caterpillar, while an equivalent stand of eucalyptus feeds almost none.'
      },
      {
        type: 'list',
        items: [
          'Native trees sustain 4–10x more insect life than exotics of the same size.',
          'Local seed sources survive regional droughts and frosts 30–60% better.',
          'Mixed-native stands store more carbon per hectare after 20 years than monocultures.'
        ]
      },
      { type: 'heading', text: 'Beyond the species label' },
      {
        type: 'paragraph',
        text:
          'A seed collected 200km away may still be the "right species" on paper, but carry the wrong adaptations. Where possible, we work with local nurseries and seed collectors to ensure that genetic provenance matches the planting site.'
      },
      {
        type: 'quote',
        text:
          'A forest is not a collection of trees. It is a relationship between trees, and the place that made them.',
        cite: 'Dr. Pedro Oliveira, Atlantic Forest Restoration Pact'
      },
      { type: 'heading', text: 'What we do differently' },
      {
        type: 'paragraph',
        text:
          'Every OneMoreTree project begins with a species audit. We inventory what should be there before we decide what to plant, and we partner with ecologists on the ground to source seed that matches the local ecosystem — not the global catalog.'
      },
      {
        type: 'callout',
        title: 'Field note',
        text:
          'In our 2023 Atlantic Forest project, plots planted with locally-sourced seed showed 92% survival at 18 months — versus 61% for the same species from a commercial nursery.'
      }
    ]
  },
  {
    slug: 'planting-10000-trees-kenya',
    title: 'Our Journey to Plant 10,000 Trees in Kenya',
    subtitle:
      'A first-hand account of the Kenyan highland project — the logistics, the surprises, and the people who made it possible.',
    author: 'David Mutua',
    date: 'October 28, 2024',
    isoDate: '2024-10-28',
    readTime: '12 min read',
    category: 'Field Report',
    tags: ['Kenya', 'Community', 'Field Report'],
    image: '/volunteers.jpg',
    imageCredit: 'Photo: Volunteers at the Mau Forest site, October 2024',
    excerpt:
      'A first-hand account of our recent reforestation project in the Kenyan highlands and the impact on local communities.',
    comments: '18',
    blocks: [
      {
        type: 'paragraph',
        text:
          'The plan was simple. Plant 10,000 indigenous trees across three sites in the Mau Forest complex. The reality — as always — was richer.'
      },
      { type: 'heading', text: 'The team' },
      {
        type: 'paragraph',
        text:
          'Fifty-two volunteers. Twelve local community forest guardians. Six nursery staff. Three cooks. And two very tired donkeys named Mango and Tembo, who carried saplings up the steeper slopes we could not reach with vehicles.'
      },
      { type: 'heading', text: 'What surprised us' },
      {
        type: 'list',
        items: [
          'Termite mounds are not a problem if you plant the right species — they actually aerate the soil.',
          'Local schoolchildren outplanted our adult volunteers by a factor of three.',
          'The hardest part of the week was not the planting. It was the planning we had done six months earlier.'
        ]
      },
      {
        type: 'quote',
        text:
          'When the elders blessed the seedlings on day one, the whole project changed. We were no longer visitors. We had been invited.',
        cite: 'Field journal, October 14'
      },
      { type: 'heading', text: 'The numbers' },
      {
        type: 'paragraph',
        text:
          'By the end of the week we had planted 10,418 trees across 14 hectares. Survival at 30 days: 94%. We will return at 6, 12, and 24 months to monitor growth, replace losses, and — most importantly — keep listening to the community guardians who look after these forests every day after we leave.'
      }
    ]
  },
  {
    slug: 'urban-forests-cool-cities',
    title: 'How Urban Forests Can Cool Our Cities',
    subtitle:
      'A look at the data behind urban heat islands — and how the right tree, in the right place, can drop a street by several degrees.',
    author: 'Emma Chen',
    date: 'October 10, 2024',
    isoDate: '2024-10-10',
    readTime: '6 min read',
    category: 'Urban Ecology',
    tags: ['Cities', 'Climate', 'Urban Ecology'],
    image: '/sapling.jpg',
    excerpt: 'Exploring the role of urban forests in mitigating heat islands and improving air quality in our cities.',
    comments: '15',
    blocks: [
      {
        type: 'paragraph',
        text:
          'A single mature tree can transpire hundreds of liters of water on a hot day, cooling the air around it the way a natural air conditioner would. Multiply that by 10,000 trees in a city and you are no longer talking about landscaping — you are talking about infrastructure.'
      },
      { type: 'heading', text: 'The 2–8°C effect' },
      {
        type: 'paragraph',
        text:
          'Studies in Phoenix, Medellín, and Singapore converge on a striking range: well-designed urban canopies reduce street-level temperatures by 2–8°C during heatwaves. That is the difference between a dangerous day and a survivable one for elderly residents.'
      },
      {
        type: 'list',
        items: [
          'Trees planted on the west and south sides of buildings cut cooling costs by up to 25%.',
          'A continuous tree canopy reduces pedestrian exposure to UV by 50–90%.',
          'Airborne particulate matter drops measurably within 50m of busy roads with a mature tree line.'
        ]
      },
      { type: 'heading', text: 'Where to plant first' },
      {
        type: 'paragraph',
        text:
          'If your goal is to reduce heat-related health risk, plant near schools, hospitals, and low-income housing first. If your goal is energy savings, plant on the west and south facades. If your goal is flood mitigation, focus on paved-over tributaries and parking lots.'
      },
      {
        type: 'callout',
        title: 'Quick win',
        text:
          'A single shade tree over a parked car reduces interior temperatures by 30°C — protecting both passengers and the vehicle.'
      }
    ]
  },
  {
    slug: 'community-tree-planting-guide',
    title: 'Tips for Starting Your Own Community Tree Planting Project',
    subtitle:
      'A practical field-tested playbook for organising a successful neighborhood planting — from your first meeting to your hundredth sapling.',
    author: 'Michael Rodriguez',
    date: 'September 25, 2024',
    isoDate: '2024-09-25',
    readTime: '10 min read',
    category: 'Community',
    tags: ['Community', 'How-to', 'Volunteering'],
    image: '/seedling.jpg',
    excerpt:
      'Practical steps and resources for starting a successful tree planting initiative in your neighborhood.',
    comments: '32',
    blocks: [
      {
        type: 'paragraph',
        text:
          'The most common reason community tree projects fail is not lack of funding. It is lack of a watering plan for year two. Below is the playbook we have refined across 80+ community projects.'
      },
      { type: 'heading', text: 'Step 1 — Listen first' },
      {
        type: 'paragraph',
        text:
          'Walk the neighborhood. Talk to shop owners, the local school, the municipal forester. Find out which species have failed before, and which sites get vandalised or compacted.'
      },
      { type: 'heading', text: 'Step 2 — Pick the right species' },
      {
        type: 'list',
        items: [
          'Native to your ecoregion.',
          'Appropriate mature size for the site (not a 25m oak under a power line).',
          'Drought-tolerant once established — and your second-worst summer is what matters, not your best.'
        ]
      },
      { type: 'heading', text: 'Step 3 — Build the watering crew' },
      {
        type: 'paragraph',
        text:
          'The first 18 months are everything. A 5-gallon bucket rota, a shared spreadsheet, and a WhatsApp group will out-perform any irrigation system for the cost. Plan this on day one, not month six.'
      },
      {
        type: 'quote',
        text:
          'A tree planting day is a photograph. A watering rota is a forest.',
        cite: 'A community forester, after 12 projects'
      },
      { type: 'heading', text: 'Step 4 — Celebrate the second summer' },
      {
        type: 'paragraph',
        text:
          'Most volunteers disappear after the planting day. The survivors are the ones who come back. Mark the one-year and two-year anniversaries — cake, photos, a list of what survived. That is what turns a project into a culture.'
      }
    ]
  },
  {
    slug: 'ai-powered-reforestation',
    title: 'The Future of Forests: AI-Powered Reforestation',
    subtitle:
      'From drone-monitored seedling survival to predictive species selection — how machine learning is reshaping restoration at scale.',
    author: 'Lisa Wang',
    date: 'September 12, 2024',
    isoDate: '2024-09-12',
    readTime: '9 min read',
    category: 'Technology',
    tags: ['AI', 'Technology', 'Drones'],
    image: '/hero-forest.jpg',
    excerpt: 'How artificial intelligence is revolutionizing forest management and reforestation efforts.',
    comments: '27',
    blocks: [
      {
        type: 'paragraph',
        text:
          'There is a forest in Malaysia that we monitor, end to end, from a laptop in Lisbon. Every seedling has been counted by a drone, every survival rate modeled against a decade of climate data, every replanting decision made on a probability map that did not exist five years ago.'
      },
      { type: 'heading', text: 'Three things AI is changing' },
      {
        type: 'list',
        items: [
          'Species selection — models predict which native species will thrive under a specific 2050 climate scenario for a given site.',
          'Survival monitoring — drones count and classify seedlings with 95%+ accuracy, replacing manual sampling.',
          'Early warning — acoustic and spectral sensors detect drought stress weeks before it is visible to the eye.'
        ]
      },
      { type: 'heading', text: 'What AI is not good at (yet)' },
      {
        type: 'paragraph',
        text:
          'AI cannot replace local ecological knowledge. It cannot sit with a community elder and learn which micro-site floods in March. Use it as a powerful tool — not as a substitute for the people who have always known the land.'
      },
      {
        type: 'callout',
        title: 'On the map',
        text:
          'Our interactive planting map at /projects uses many of the same prediction models — explore which species are recommended for your region.'
      }
    ]
  },
  {
    slug: 'climate-change-forest-resilience',
    title: 'Climate Change and Forest Resilience: What You Need to Know',
    subtitle:
      'A grounded look at the science — what resilience really means, and what restoration practitioners can do about it today.',
    author: 'Dr. Robert Green',
    date: 'August 30, 2024',
    isoDate: '2024-08-30',
    readTime: '15 min read',
    category: 'Climate Science',
    tags: ['Climate', 'Resilience', 'Science'],
    image: '/mature-tree.jpg',
    excerpt: 'Understanding how climate change affects our forests and what we can do to build resilience.',
    comments: '45',
    blocks: [
      {
        type: 'paragraph',
        text:
          'A resilient forest is not one that never changes. It is one that continues to function — storing carbon, cycling water, hosting biodiversity — through change. The question is not whether your restoration will face a hotter, drier, more volatile climate. It is whether the forest you build today will still be a forest in 2070.'
      },
      { type: 'heading', text: 'The three stressors' },
      {
        type: 'list',
        items: [
          'Heat and drought — pushing many species beyond their physiological limits.',
          'Fire — increasingly frequent and severe in forests not historically fire-adapted.',
          'Pests and pathogens — expanding their range faster than their predators can follow.'
        ]
      },
      { type: 'heading', text: 'Five principles for resilient restoration' },
      {
        type: 'list',
        items: [
          'Diversity at every level — species, genetic, structural, age.',
          'Assisted migration where natural dispersal cannot keep pace.',
          'Protect existing canopy — it is irreplaceable on any human timescale.',
          'Plan for disturbances, not against them.',
          'Involve the people who will live with the forest for the next 50 years.'
        ]
      },
      {
        type: 'quote',
        text:
          'We are not planting forests for the climate of 1990. We are planting them for the climate of 2060.',
        cite: 'A guiding principle we now use on every project'
      },
      {
        type: 'paragraph',
        text:
          'None of this is easy. But restoration has always been an act of long-term thinking. The decisions we make in the next decade will determine whether the forests we plant this decade survive the century.'
      }
    ]
  }
];

export const findPostBySlug = (slug: string): BlogPost | undefined =>
  blogPosts.find((p) => p.slug === slug);

export const getRelatedPosts = (post: BlogPost, limit = 3): BlogPost[] =>
  blogPosts
    .filter((p) => p.slug !== post.slug)
    .map((p) => {
      const sharedTags = p.tags.filter((t) => post.tags.includes(t)).length;
      const sameCategory = p.category === post.category ? 1 : 0;
      return { post: p, score: sharedTags * 2 + sameCategory };
    })
    .sort((a, b) => b.score - a.score)
    .slice(0, limit)
    .map((x) => x.post);

export const slugify = (s: string): string =>
  s
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)+/g, '');
