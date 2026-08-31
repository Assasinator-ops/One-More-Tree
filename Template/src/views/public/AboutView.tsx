'use client';

import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import AboutSection from '@/sections/AboutSection';
import TestimonialsSection from '@/sections/TestimonialsSection';
import type { TeamContent } from '@/content/types';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

type Props = { team: TeamContent | null };

export default function AboutView({ team }: Props) {
  const sectionRef = useRef<HTMLElement>(null);
  const imageRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      if (imageRef.current) {
        gsap.fromTo(
          imageRef.current,
          { opacity: 0, y: 50 },
          { opacity: 1, y: 0, duration: 1, scrollTrigger: { trigger: sectionRef.current, start: 'top 80%' } }
        );
      }
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  return (
    <div className="relative bg-background">
      <main className="relative">
        <section
          ref={sectionRef}
          className="relative min-h-[60vh] flex items-center justify-center overflow-hidden page-header-texture"
        >
          <div className="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background" />
          <div className="absolute inset-0 leaf-pattern opacity-20 pointer-events-none" />
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
            <div className="max-w-5xl mx-auto text-center">
              <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
                Our Story & Mission
              </h1>
              <p className="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
                Growing forests, building hope in Spain
              </p>
            </div>
          </div>
        </section>

        <AboutSection />
        {team ? <TestimonialsSection /> : null}
      </main>
    </div>
  );
}
