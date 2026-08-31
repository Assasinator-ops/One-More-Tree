'use client';

import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ArrowDown } from 'lucide-react';

gsap.registerPlugin(ScrollTrigger);

const Hero = () => {
  const sectionRef = useRef<HTMLElement>(null);
  const imageRef = useRef<HTMLDivElement>(null);
  const contentRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.to(imageRef.current, { yPercent: 30, ease: 'none', scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: 'bottom top', scrub: true } });
      gsap.to(contentRef.current, { opacity: 0, y: -50, ease: 'none', scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: '50% top', scrub: true } });
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  const scrollToStory = () => document.querySelector('#story')?.scrollIntoView({ behavior: 'smooth' });

  return (
    <section ref={sectionRef} className="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
      <div ref={imageRef} className="absolute inset-0 w-full h-[120%] -top-[10%]">
        <img src="/hero-forest.jpg" alt="Lush forest canopy" className="w-full h-full object-cover" />
        {/* Lighter, warmer overlays */}
        <div className="absolute inset-0 bg-gradient-to-b from-white/40 via-white/20 to-background/80" />
        <div className="absolute inset-0 bg-gradient-to-r from-white/30 via-transparent to-white/30" />
      </div>
      <div ref={contentRef} className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-20">
        <div className="max-w-5xl mx-auto text-center">
          {/* Mission badge with light theme */}
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-forest-200 mb-6 sm:mb-8 shadow-medium">
            <img src="/logo-colorful.png" alt="Our Mission" className="h-6 w-auto sm:h-8" />
            <span className="text-xs sm:text-sm text-foreground font-medium">Our Mission: 3 Million Trees by 2040</span>
          </div>
          {/* Headline with better contrast */}
          <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold text-foreground leading-tight mb-4 sm:mb-6 drop-shadow-sm">
            Every Tree<span className="block text-forest-900 italic drop-shadow-lg">Tells a Story</span>
          </h1>
          {/* Improved subtitle readability */}
          <p className="text-base sm:text-lg md:text-xl text-foreground/80 max-w-2xl mx-auto mb-8 sm:mb-10 leading-relaxed px-4">
            Join us on a journey from seed to forest. Together, we're not just planting trees—we're growing hope, restoring ecosystems, and securing our planet's future.
          </p>
          {/* CTA buttons with better visibility */}
          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12 sm:mb-16">
            <a href="#join" onClick={(e) => { e.preventDefault(); document.querySelector('#join')?.scrollIntoView({ behavior: 'smooth' }); }} className="w-full sm:w-auto px-8 py-4 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated text-center">Plant Your First Tree</a>
            <button onClick={scrollToStory} className="w-full sm:w-auto px-8 py-4 bg-white/90 hover:bg-white backdrop-blur-sm border border-forest-200 text-forest-700 font-medium rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated flex items-center justify-center gap-2 group">Watch the Journey<ArrowDown className="w-4 h-4 transition-transform duration-300 group-hover:translate-y-1" /></button>
          </div>
          {/* Statistics cards with light theme */}
          <div className="grid grid-cols-3 gap-4 sm:gap-8 max-w-2xl mx-auto">
            {[{ value: '847K', label: 'Trees Planted' }, { value: '156', label: 'Projects' }, { value: '42', label: 'Countries' }].map((stat, i) => (
              <div key={i} className="text-center p-3 sm:p-4 rounded-2xl bg-white/90 backdrop-blur-sm border border-forest-100 shadow-medium">
                <div className="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-forest-600 mb-1">{stat.value}</div>
                <div className="text-xs sm:text-sm text-foreground/60">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
      {/* Scroll indicator with better visibility */}
      <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-10">
        <button onClick={scrollToStory} className="flex flex-col items-center gap-2 text-foreground/50 hover:text-forest-600 transition-colors duration-300">
          <span className="text-xs uppercase tracking-widest font-medium">Scroll</span>
          <div className="w-6 h-10 rounded-full border-2 border-current flex items-start justify-center p-2"><div className="w-1.5 h-3 bg-forest-600 rounded-full animate-bounce" /></div>
        </button>
      </div>
    </section>
  );
};

export default Hero;

