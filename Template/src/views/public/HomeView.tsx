'use client';

import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Hero from '@/sections/Hero';
import Donation3DSection from '@/components/Donation3DSection';
import type { ImpactContent } from '@/content/types';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

type Props = {
  totalTrees: number;
  totalProjects: number;
  totalVolunteers: number;
  impact: ImpactContent;
};

export default function HomeView({ totalTrees, totalProjects, totalVolunteers }: Props) {
  const mainRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      ScrollTrigger.refresh();
    }, mainRef);
    return () => ctx.revert();
  }, []);

  return (
    <div ref={mainRef} className="relative min-h-screen bg-background">
      <main className="relative">
        <Hero />

        <section className="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20">
          <div className="max-w-6xl mx-auto">
            <div className="grid md:grid-cols-3 gap-6">
              {[
                { label: 'Trees tracked', value: totalTrees.toLocaleString() },
                { label: 'Projects active', value: totalProjects.toString() },
                { label: 'Volunteers engaged', value: totalVolunteers.toLocaleString() }
              ].map((item) => (
                <div
                  key={item.label}
                  className="rounded-3xl bg-white/90 border border-slate-200 p-8 text-center shadow-soft"
                >
                  <p className="text-xs uppercase tracking-[0.25em] text-forest-500 mb-3">{item.label}</p>
                  <p className="text-4xl font-semibold text-foreground">{item.value}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-16 sm:py-24 bg-white/95 border-t border-slate-200">
          <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">
            <div className="grid gap-8 lg:grid-cols-3">
              {[
                {
                  title: 'Europe First',
                  description:
                    'Our mission is rooted in Europe: local forests, local communities, local restoration.'
                },
                {
                  title: 'Community Power',
                  description:
                    'Volunteers and partners work together to plant, protect, and nurture every site.'
                },
                {
                  title: 'Visible Progress',
                  description:
                    'Every planting story is documented with images and milestones so supporters see real results.'
                }
              ].map((item) => (
                <div key={item.title} className="rounded-3xl bg-white/95 border border-slate-200 p-8">
                  <h3 className="font-serif text-2xl font-semibold text-foreground mb-3">{item.title}</h3>
                  <p className="text-foreground/70 leading-relaxed">{item.description}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20">
          <div className="max-w-5xl mx-auto text-center">
            <p className="text-forest-400 uppercase tracking-[0.3em] text-xs sm:text-sm mb-4">Featured Progress</p>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-6">
              How our first site grew from seedling to canopy
            </h2>
            <p className="text-foreground/70 text-base sm:text-lg leading-relaxed">
              We track every planting with a clear timeline and visuals, so supporters can follow the journey from the first sapling to a thriving forest.
            </p>
          </div>
        </section>

        <Donation3DSection />
      </main>
    </div>
  );
}
