// TreeMapSection.tsx - Integration container for ProjectsSection (Step 5)
import React from 'react';
import MapView from './MapView';
import { Button } from '@/components/ui/button';

const TreeMapSection: React.FC = () => {
  return (
    <section className="relative py-20 sm:py-32 bg-gradient-to-b from-background to-forest-50/50">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-100 border border-forest-200 mb-6 mx-auto">
            <span className="text-2xl">🗺️</span>
            <span className="text-sm font-semibold uppercase tracking-wider text-forest-700">Live Tree Map</span>
          </div>
          <h2 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold bg-gradient-to-r from-forest-600 to-forest-800 bg-clip-text text-transparent mb-6">
            Track Our Trees
            <span className="block text-foreground font-serif italic">across Europe</span>

          </h2>
          <p className="text-xl text-foreground/70 max-w-2xl mx-auto leading-relaxed">
            Click any marker to see the complete growth journey from seed to mature tree. Panoramic views available.
          </p>
        </div>
        
        <div className="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center mb-12">
          <div className="order-2 lg:order-1">
            <MapView />
          </div>
          <div className="order-1 lg:order-2 space-y-6">
            <div className="space-y-4 p-8 rounded-3xl glass-card">
              <h3 className="text-2xl font-bold text-foreground">What You'll See</h3>
              <div className="grid grid-cols-2 gap-4 text-sm">
                <div className="flex items-start gap-3 p-4 rounded-xl bg-forest-50 border border-forest-100">
                  <div className="w-8 h-8 rounded-lg bg-forest-200 flex items-center justify-center mt-0.5 flex-shrink-0">
                    <span className="text-forest-600 font-bold text-sm">📅</span>
                  </div>
                  <div>
                    <div className="font-semibold text-foreground">Timeline</div>
                    <div className="text-foreground/60">Day 1 to Year 15</div>
                  </div>
                </div>
                <div className="flex items-start gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100">
                  <div className="w-8 h-8 rounded-lg bg-sky-200 flex items-center justify-center mt-0.5 flex-shrink-0">
                    <span className="text-sky-600 font-bold text-sm">360°</span>
                  </div>
                  <div>
                    <div className="font-semibold text-foreground">Panoramic</div>
                    <div className="text-foreground/60">Interactive views</div>
                  </div>
                </div>
                <div className="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                  <div className="w-8 h-8 rounded-lg bg-emerald-200 flex items-center justify-center mt-0.5 flex-shrink-0">
                    <span className="text-emerald-600 font-bold text-sm">🌱</span>
                  </div>
                  <div>
                    <div className="font-semibold text-foreground">Growth Stages</div>
                    <div className="text-foreground/60">Seed to mature</div>
                  </div>
                </div>
              </div>
            </div>
            <div className="text-center">
              <Button 
                size="lg" 
                className="bg-forest-600 hover:bg-forest-700 text-xl px-12 py-8 rounded-2xl shadow-xl hover:shadow-2xl h-auto font-serif"
                onClick={() => document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' })}
              >
                Explore Europe Projects

              </Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default TreeMapSection;

