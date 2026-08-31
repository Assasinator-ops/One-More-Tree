'use client';

import { useState } from 'react';
import { Leaf, Heart, MapPin } from 'lucide-react';

export default function VolunteerView() {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [interest, setInterest] = useState('Tree planting');

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setName('');
    setEmail('');
    setInterest('Tree planting');
  };

  return (
    <div className="relative min-h-screen bg-background">
      <main className="relative">
        <section className="relative min-h-[60vh] flex items-center justify-center overflow-hidden page-header-texture">
          <div className="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background" />
          <div className="absolute inset-0 leaf-pattern opacity-20 pointer-events-none" />
          <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
            <div className="max-w-5xl mx-auto">
              <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border border-slate-200 mb-6">
                <Leaf className="w-4 h-4 text-forest-700" />
                <span className="text-xs sm:text-sm text-foreground font-semibold">Volunteer with OneMoreTree</span>
              </div>
              <h1 className="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-4 leading-tight">
                Help restore forests, together
              </h1>
              <p className="text-lg sm:text-xl text-foreground/70 max-w-3xl">
                Sign up to receive local volunteering opportunities and updates.
              </p>
            </div>
          </div>
        </section>

        <section className="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
          <div className="max-w-6xl mx-auto">
            <div className="grid lg:grid-cols-2 gap-8 sm:gap-10 items-start">
              <div className="bg-white border border-earth-200 rounded-3xl p-6 sm:p-8 shadow-medium">
                <div className="font-serif text-2xl font-bold text-foreground mb-2">Your details</div>
                <div className="text-sm text-foreground/70 mb-6">
                  We will only use your information to contact you about volunteering.
                </div>
                <form onSubmit={submit} className="space-y-4">
                  <div>
                    <label className="text-sm text-foreground/70">Name</label>
                    <input
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                      placeholder="Your name"
                    />
                  </div>
                  <div>
                    <label className="text-sm text-foreground/70">Email</label>
                    <input
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      type="email"
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                      placeholder="you@example.com"
                    />
                  </div>
                  <div>
                    <label className="text-sm text-foreground/70">Interest</label>
                    <select
                      aria-label="Select volunteering interest"
                      value={interest}
                      onChange={(e) => setInterest(e.target.value)}
                      className="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                    >
                      <option>Tree planting</option>
                      <option>Community stewardship</option>
                      <option>Monitoring & data</option>
                      <option>Education & outreach</option>
                    </select>
                  </div>
                  <button
                    type="submit"
                    className="w-full px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-elevated"
                  >
                    Request volunteering info
                  </button>
                </form>
              </div>

              <div className="space-y-4">
                {[
                  { icon: Leaf, title: 'Planting days', desc: 'Join local teams for native tree planting events.' },
                  { icon: Heart, title: 'Community stewardship', desc: 'Help care for young forests and strengthen ownership.' },
                  { icon: MapPin, title: 'Local coordination', desc: 'We share nearby opportunities based on your interests.' }
                ].map((s, i) => (
                  <div key={i} className="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
                    <div className="flex items-center gap-3 mb-3">
                      <s.icon className="w-5 h-5 text-forest-600" />
                      <div className="font-serif text-xl font-bold text-foreground">{s.title}</div>
                    </div>
                    <div className="text-sm text-foreground/70 leading-relaxed">{s.desc}</div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}
