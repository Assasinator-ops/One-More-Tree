'use client';

import { useEffect, useRef, useState } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Wind, Shield, Heart } from 'lucide-react';
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
  Wind,
  Shield,
  Heart,
};

const StoryGrowth = () => {
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


  const chapter = adminData?.chapters.find((c) => c.key === 'growth') || {
    chapterLabel: 'Chapter Two',
    headline: 'Growing Through',
    headlineEmphasis: 'Adversity',
    descriptions: [
      "Growth isn't always easy. Our young trees face countless challenges—harsh weather, hungry animals, and the constant struggle for sunlight and nutrients.",
      'This is where your support matters most. Our teams provide the protection, irrigation, and care that transforms vulnerable saplings into resilient young trees.',
    ],
    imageSrc: '/sapling.jpg',
    imageAlt: 'A young tree growing',
    infoCards: [
      { icon: 'Wind', label: 'Harsh Weather', desc: 'Storms, droughts, and extreme temperatures' },
      { icon: 'Shield', label: 'Protection Needed', desc: 'Guards against animals and pests' },
      { icon: 'Heart', label: 'Continued Care', desc: 'Regular monitoring and maintenance' },
    ],
    highlight: { value: 'Year 3', title: 'Growth Progress', subtitle: 'Finding strength' },
  };

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(imageRef.current, { opacity: 0, x: 100 }, { opacity: 1, x: 0, duration: 1.2, ease: 'power3.out', scrollTrigger: { trigger: sectionRef.current, start: 'top 80%', toggleActions: 'play none none reverse' } });
      gsap.to(imageRef.current, { yPercent: 10, ease: 'none', scrollTrigger: { trigger: sectionRef.current, start: 'top bottom', end: 'bottom top', scrub: true } });
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  return (
    <section ref={sectionRef} className="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-30" />
      <div className="absolute bottom-1/4 -right-32 w-96 h-96 bg-forest-300/15 rounded-full blur-3xl" />
      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center max-w-7xl mx-auto">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 border border-amber-200 mb-6">
              <span className="text-xs sm:text-sm text-amber-700 font-semibold uppercase tracking-wider">{chapter.chapterLabel}</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 sm:mb-8 leading-tight">
              {chapter.headline}<span className="text-gradient block py-3">{chapter.headlineEmphasis}</span>
            </h2>
            <div className="space-y-4 sm:space-y-6 text-foreground/70 mb-8">
              {(chapter.descriptions ?? []).map((desc, idx) => (
                <p key={idx} className="text-base sm:text-lg leading-relaxed">{desc}</p>
              ))}
            </div>
            {chapter.highlight && (
              <div className="mb-6">
                <div className="flex justify-between text-sm mb-2"><span className="text-foreground/60">{chapter.highlight.title}</span><span className="text-forest-600 font-semibold">{chapter.highlight.value}</span></div>
                <div className="h-3 bg-earth-200 rounded-full overflow-hidden"><div className="h-full w-[35%] bg-gradient-to-r from-forest-600 to-forest-400 rounded-full" /></div>
              </div>
            )}
            <div className="space-y-3">
              <h3 className="text-lg font-semibold text-foreground mb-4">Challenges We Overcome</h3>
              {(chapter.infoCards ?? []).map((c, i) => {
                const IconComponent = ICON_MAP[c.icon];
                return (
                  <div key={i} className="flex items-start gap-4 p-4 rounded-xl glass-card">
                    <div className="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                      {IconComponent ? <IconComponent className="w-5 h-5 text-amber-600" /> : <Wind className="w-5 h-5 text-amber-600" />}
                    </div>
                    <div><h4 className="text-sm font-semibold text-foreground mb-1">{c.label}</h4><p className="text-sm text-foreground/60">{c.desc}</p></div>
                  </div>
                );
              })}
            </div>
          </div>
          <div ref={imageRef} className="relative">
            <div className="relative aspect-[3/4] rounded-3xl overflow-hidden shadow-elevated">
              <img src={chapter.imageSrc} alt={chapter.imageAlt} className="w-full h-full object-cover" />
              <div className="absolute inset-0 bg-gradient-to-t from-background/70 via-transparent to-transparent" />
            </div>
            <div className="absolute -bottom-8 -left-4 sm:-bottom-12 sm:-left-6 p-6 sm:p-8 rounded-2xl glass-card animate-float group hover:scale-110 transition-all duration-300 shadow-2xl hover:shadow-glow hover:-translate-y-1 z-10">
              <div className="flex items-center gap-4 mb-3">
                <img src="/logo-colorful.png" alt="NGO Logo" className="h-20 w-20 flex-shrink-0 drop-shadow-2xl group-hover:drop-shadow-glow" />
                <div>
                  <div className="text-3xl sm:text-4xl font-serif font-bold text-amber-600 group-hover:text-amber-700">{chapter.highlight?.value || 'Year 3'}</div>
                </div>
              </div>
              <div className="text-base font-medium text-foreground/80 group-hover:text-foreground">{chapter.highlight?.subtitle || 'Finding strength'}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default StoryGrowth;
