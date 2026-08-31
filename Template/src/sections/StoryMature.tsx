'use client';

import { useEffect, useRef, useState } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Bird, CloudRain, Users, TreePine } from 'lucide-react';
import { publicApi, PublicKeys } from '@/lib/publicApi';

type AdminStoryChapter = {
  key: 'seed' | 'growth' | 'mature';
  chapterLabel?: string;
  headline?: string;
  headlineEmphasis?: string;
  descriptions?: [string, string];
  imageSrc?: string;
  imageAlt?: string;
  infoCards?: Array<{ icon: string; label: string; desc: string }>;
  highlight?: { value: string; title: string; subtitle: string };
};

type AdminStories = { chapters: AdminStoryChapter[] };


gsap.registerPlugin(ScrollTrigger);

const ICON_MAP: Record<string, React.ComponentType<{ className: string }>> = {
  Bird,
  CloudRain,
  Users,
  TreePine,
};

const StoryMature = () => {
  const sectionRef = useRef<HTMLElement>(null);
  const imageRef = useRef<HTMLDivElement>(null);
  const [adminData, setAdminData] = useState<AdminStories | null>(null);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const data = await publicApi.read<{ chapters?: AdminStoryChapter[] }>(PublicKeys.Stories);
        const chapters = (data as any)?.chapters;
        if (!mounted) return;
        setAdminData({ chapters: Array.isArray(chapters) ? (chapters as AdminStoryChapter[]) : [] });
      } catch {
        if (!mounted) return;
        setAdminData(null);
      }
    })();

    return () => {
      mounted = false;
    };
  }, []);


  const chapter = adminData?.chapters.find((c) => c.key === 'mature') || {
    chapterLabel: 'Chapter Three',
    headline: 'A Legacy That',
    headlineEmphasis: 'Lasts Generations',
    descriptions: [
      'Twenty-five years later, that tiny seedling has become a towering giant. Its branches stretch toward the sky, providing shade, shelter, and life to countless creatures.',
      'A single mature tree can absorb 48 pounds of carbon dioxide per year, produce enough oxygen for two people, and provide habitat for over 100 species.',
    ],
    imageSrc: '/mature-tree.jpg',
    imageAlt: 'A majestic mature tree',
    infoCards: [
      { icon: 'Bird', label: 'Wildlife Habitat', desc: 'Home to 100+ species' },
      { icon: 'CloudRain', label: 'Carbon Capture', desc: '48 lbs CO₂/year' },
      { icon: 'Users', label: 'Community', desc: 'Shade & gathering' },
      { icon: 'TreePine', label: 'Ecosystem', desc: 'Supports biodiversity' },
    ],
    highlight: { value: '50', title: 'Years of impact', subtitle: 'Legacy in the making' },
  };

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(imageRef.current, { opacity: 0, scale: 0.9 }, { opacity: 1, scale: 1, duration: 1.2, ease: 'power3.out', scrollTrigger: { trigger: sectionRef.current, start: 'top 80%', toggleActions: 'play none none reverse' } });
      gsap.to(imageRef.current, { yPercent: -8, ease: 'none', scrollTrigger: { trigger: sectionRef.current, start: 'top bottom', end: 'bottom top', scrub: true } });
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  return (
    <section ref={sectionRef} className="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-30" />
      <div className="absolute top-1/3 -left-48 w-96 h-96 bg-forest-300/15 rounded-full blur-3xl" />
      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center max-w-7xl mx-auto">
          <div ref={imageRef} className="relative order-2 lg:order-1">
            <div className="relative aspect-[3/4] rounded-3xl overflow-hidden shadow-elevated">
              <img src={chapter.imageSrc} alt={chapter.imageAlt} className="w-full h-full object-cover" />
              <div className="absolute inset-0 bg-gradient-to-t from-background/80 via-transparent to-transparent" />
            </div>
            <div className="absolute -bottom-8 -right-4 sm:-bottom-12 sm:-right-6 p-6 sm:p-8 rounded-2xl glass-card animate-float group hover:scale-110 transition-all duration-300 shadow-2xl hover:shadow-glow hover:-translate-y-1 z-10">
              <div className="flex items-center gap-4 mb-3">
                <img src="/logo-colorful.png" alt="NGO Logo" className="h-20 w-20 flex-shrink-0 drop-shadow-2xl group-hover:drop-shadow-glow" />
                <div>
                  <div className="text-3xl sm:text-4xl font-serif font-bold text-forest-600 group-hover:text-forest-700">Year 15</div>
                </div>
              </div>
              <div className="text-base font-medium text-foreground/80 group-hover:text-foreground">Full maturity</div>
            </div>
            <div className="absolute top-8 left-8 p-4 rounded-2xl glass-card">
              <div className="text-2xl sm:text-3xl font-serif font-bold text-forest-600">{chapter.highlight?.value || '50'}</div>
              <div className="text-xs text-foreground/60">{chapter.highlight?.title || 'Years of impact'}</div>
            </div>
          </div>
          <div className="order-1 lg:order-2">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">{chapter.chapterLabel}</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 sm:mb-8 leading-tight">
              {chapter.headline}<span className="text-gradient block py-3">{chapter.headlineEmphasis}</span>
            </h2>
            <div className="space-y-4 sm:space-y-6 text-foreground/70 mb-10">
              {(chapter.descriptions ?? []).map((desc, idx) => (
                <p key={idx} className="text-base sm:text-lg leading-relaxed">{desc}</p>
              ))}
            </div>
            <div className="grid grid-cols-2 gap-3 sm:gap-4">
              {(chapter.infoCards ?? []).map((b, i) => {
                const IconComponent = ICON_MAP[b.icon];
                const colorClasses = {
                  'Bird': { bg: 'bg-sky-100', text: 'text-sky-600' },
                  'CloudRain': { bg: 'bg-emerald-100', text: 'text-emerald-600' },
                  'Users': { bg: 'bg-amber-100', text: 'text-amber-600' },
                  'TreePine': { bg: 'bg-forest-100', text: 'text-forest-600' },
                }[b.icon] || { bg: 'bg-forest-100', text: 'text-forest-600' };

                return (
                  <div key={i} className="p-4 rounded-xl glass-card hover:bg-white transition-all">
                    <div className={`w-10 h-10 rounded-lg ${colorClasses.bg} flex items-center justify-center mb-3`}>
                      {IconComponent ? <IconComponent className={`w-5 h-5 ${colorClasses.text}`} /> : <TreePine className={`w-5 h-5 ${colorClasses.text}`} />}
                    </div>
                    <h4 className="text-sm font-semibold text-foreground mb-1">{b.label}</h4>
                    <p className="text-xs text-foreground/60">{b.desc}</p>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default StoryMature;
