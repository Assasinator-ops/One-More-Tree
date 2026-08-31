'use client';

import { useEffect, useRef, useState } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import ProjectsSection from '@/sections/ProjectsSection';
import { spainPlantingLocations } from '@/lib/spainPlantingLocations';
import { TreeDeciduous } from 'lucide-react';

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger);
}

type Project = {
  id: string;
  name: string;
  region: string;
  description: string;
  treesPlanted: number;
  volunteers: number;
  active?: boolean;
};

type Props = {
  liveMapEnabled: boolean;
  projectsEnabled: boolean;
  projects: Project[];
};

export default function ProjectsView({ liveMapEnabled, projectsEnabled, projects }: Props) {
  const sectionRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const ctx = gsap.context(() => {
      ScrollTrigger.refresh();
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  const activeProjects = projects?.length ? projects.filter((project) => project.active !== false) : spainPlantingLocations;

  return (
    <div className="relative bg-background">
      <main className="relative">
        <section ref={sectionRef} className="relative min-h-[60vh] flex items-center justify-center">
          <div className="absolute inset-0 bg-gradient-to-b from-forest-100/90 to-background" />
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
            <div className="max-w-5xl mx-auto text-center">
              <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
                Our Projects Across Spain
              </h1>
              <p className="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
                Explore our active initiatives and see the impact we&apos;re making across Spain
              </p>
            </div>
          </div>
        </section>

        {liveMapEnabled ? (
          <ProjectsSection />
        ) : (
          <section className="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
            <div className="max-w-7xl mx-auto rounded-3xl border border-earth-200 bg-white/90 p-12 text-center text-foreground/70">
              The live map module is currently disabled. Enable it in the admin module status settings to show project tracking and locations.
            </div>
          </section>
        )}

        {projectsEnabled ? (
          <section className="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
            <div className="max-w-7xl mx-auto">
              <h2 className="font-serif text-3xl sm:text-4xl font-bold text-foreground mb-12">Active Projects</h2>
              <div className="grid md:grid-cols-2 gap-8">
                {activeProjects.map((project) => (
                  <div
                    key={project.id}
                    className="bg-white/95 border border-slate-200 rounded-2xl p-8 hover:bg-white transition-all duration-300"
                  >
                    <div className="flex items-start justify-between mb-4">
                      <div>
                        <h3 className="font-serif text-xl font-semibold text-foreground mb-1">{project.name}</h3>
                        <p className="text-sm text-forest-600">{project.region}</p>
                      </div>
                      <TreeDeciduous className="w-6 h-6 text-forest-600" />
                    </div>
                    <p className="text-foreground/70 text-sm mb-6">{project.description}</p>
                    <div className="flex gap-6 pt-6 border-t border-slate-200">
                      <div>
                        <p className="text-xs text-foreground/60 mb-1">Trees Planted</p>
                        <p className="font-serif text-2xl font-bold text-forest-400">{project.treesPlanted.toLocaleString()}</p>
                      </div>
                      <div>
                        <p className="text-xs text-foreground/60 mb-1">Volunteers</p>
                        <p className="font-serif text-2xl font-bold text-forest-400">{project.volunteers.toLocaleString()}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </section>
        ) : (
          <section className="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
            <div className="max-w-7xl mx-auto rounded-3xl border border-earth-200 bg-white/90 p-12 text-center text-foreground/70">
              The active projects section is currently disabled. Enable it in admin module status to display project cards.
            </div>
          </section>
        )}
      </main>
    </div>
  );
}
