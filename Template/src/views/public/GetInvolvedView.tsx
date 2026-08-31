'use client';

import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import CallToAction from '@/sections/CallToAction';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

export default function GetInvolvedView() {
  const mainRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      ScrollTrigger.refresh();
    }, mainRef);
    return () => ctx.revert();
  }, []);

  return (
    <div ref={mainRef} className="relative bg-background">
      <main className="relative">
        <section className="relative min-h-[50vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
          <div className="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background" />
          <div className="absolute inset-0 leaf-pattern opacity-20 pointer-events-none" />
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
            <div className="max-w-5xl mx-auto text-center">
              <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
                Get Involved
              </h1>
              <p className="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
                Join our movement. Plant trees, make an impact, and help restore Spain&apos;s forests
              </p>
            </div>
          </div>
        </section>

        <CallToAction />
      </main>
    </div>
  );
}
