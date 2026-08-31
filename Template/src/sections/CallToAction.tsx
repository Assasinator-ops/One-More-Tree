'use client';

import { useEffect, useState } from 'react';
import type { ComponentType, FormEvent } from 'react';
import { Heart, TreePine, Shield, Gift, Check } from 'lucide-react';
import Donation3DSection from '@/components/Donation3DSection';



type DonationTierItem = {
  icon: ComponentType<{ className?: string }>;
  amount: number;
  trees: number;
  title: string;
  benefits: string[];
  color: string;
  featured?: boolean;
};

const CallToAction = () => {
  const [selectedAmount, setSelectedAmount] = useState<number | null>(50);
  const [customAmount, setCustomAmount] = useState('');
  const [donationTiers, setDonationTiers] = useState<DonationTierItem[]>([]);
  const [isDonationsEnabled, setIsDonationsEnabled] = useState(true);
  const [isNewsletterEnabled, setIsNewsletterEnabled] = useState(true);
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [newsletterStatus, setNewsletterStatus] = useState<string | null>(null);
  const [newsletterPending, setNewsletterPending] = useState(false);

  useEffect(() => {
    // Donation tiers come from the admin module (loaded from SQLite via the
    // public API); when unauthorized or not loaded, show no tiers instead of
    // misleading fallback data.
    setDonationTiers([]);

    // Module visibility is managed by SQLite-backed module statuses on the admin side.
    // Public defaults stay enabled unless the admin has disabled them.
    setIsDonationsEnabled(true);
    setIsNewsletterEnabled(true);

  }, []);

  const subscribeToNewsletter = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const normalizedEmail = newsletterEmail.trim();
    if (!normalizedEmail || !normalizedEmail.includes('@')) {
      setNewsletterStatus('Please enter a valid email address.');
      return;
    }

    setNewsletterPending(true);
    try {
      const res = await fetch('/api/public/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: normalizedEmail })
      });
      const json = await res.json();
      if (!res.ok || !json.ok) {
        setNewsletterStatus(json.message ?? 'Unable to subscribe at this time. Please try again later.');
      } else {
        setNewsletterStatus('Thanks for subscribing!');
        setNewsletterEmail('');
      }
    } catch {
      setNewsletterStatus('Unable to subscribe at this time. Please try again later.');
    } finally {
      setNewsletterPending(false);
    }
  };

  const donationAmounts = donationTiers.length ? donationTiers.map((tier) => tier.amount) : [25, 50, 100, 250];

  return (
    <section id="join" className="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-100/50 to-transparent" />
      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12 sm:mb-16">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Heart className="w-5 h-5 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">Join the Movement</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-4 sm:mb-6">
              Plant a Tree for <span className="text-forest-700 block">1 Euro</span>
            </h2>
            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto mb-8">
              Every euro becomes a seed of change. From wildfire-damaged forests to school playgrounds, your support brings life back to Spain.
            </p>
            {/*<div className="flex flex-wrap justify-center gap-3 mb-4">
              {donationAmounts.map((amount) => (
                <button
                  key={amount}
                  onClick={() => {
                    setSelectedAmount(amount);
                    setCustomAmount('');
                  }}
                  className={`px-6 py-3 rounded-full font-medium transition-all duration-300 ${
                    selectedAmount === amount && !customAmount
                      ? 'bg-forest-600 text-white shadow-medium'
                      : 'bg-white text-foreground/70 hover:bg-forest-50 hover:text-forest-600 border border-earth-200'
                  }`}
                >
                  ${amount}
                </button>
              ))}
              <div className="relative">
                <span className="absolute left-4 top-1/2 -translate-y-1/2 text-foreground/40">$</span>
                <input
                  type="number"
                  placeholder="Custom"
                  value={customAmount}
                  onChange={(e) => {
                    setCustomAmount(e.target.value);
                    setSelectedAmount(null);
                  }}
                  className="w-28 px-4 py-3 pl-8 rounded-full bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors"
                />
              </div>
            </div>*/}
          </div>

          {/*{isDonationsEnabled ? (
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
              {donationTiers.map((tier, i) => (
                <div key={i} className={`relative rounded-2xl overflow-hidden transition-all duration-300 hover:scale-105 ${tier.featured ? 'ring-2 ring-amber-400' : ''}`}>
                  {tier.featured ? (
                    <div className="absolute top-0 left-0 right-0 bg-gradient-to-r from-amber-400 to-amber-300 text-foreground text-xs font-semibold py-1.5 text-center z-10 shadow-medium">
                      Most Popular
                    </div>
                  ) : null}
                  <div className="glass-card p-5 sm:p-6 h-full flex flex-col shadow-medium hover:shadow-elevated">
                    <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${tier.color} flex items-center justify-center mb-4 ${tier.featured ? 'mt-4' : ''}`}>
                      <tier.icon className="w-6 h-6 text-white" />
                    </div>
                    <h3 className="font-serif text-xl font-bold text-foreground mb-2">{tier.title}</h3>
                    <div className="flex items-baseline gap-1 mb-4">
                      <span className="text-2xl font-bold text-foreground">${tier.amount}</span>
                      <span className="text-foreground/50">/one-time</span>
                    </div>
                    <div className="flex items-center gap-2 mb-4 pb-4 border-b border-earth-200">
                      <TreePine className="w-4 h-4 text-forest-600" />
                      <span className="text-sm text-foreground/60">
                        Plants <span className="text-forest-600 font-semibold">{tier.trees}</span> trees
                      </span>
                    </div>
                    <ul className="space-y-2 mb-6 flex-grow">
                      {tier.benefits.map((benefit, j) => (
                        <li key={j} className="flex items-start gap-2 text-sm text-foreground/60">
                          <Check className="w-4 h-4 text-forest-600 flex-shrink-0 mt-0.5" />
                          {benefit}
                        </li>
                      ))}
                    </ul>
                    <button className={`w-full py-3 rounded-xl font-medium transition-all duration-300 ${
                      tier.featured
                        ? 'bg-gradient-to-r from-amber-400 to-amber-300 text-foreground hover:shadow-lg'
                        : 'bg-forest-50 text-forest-700 hover:bg-forest-100'
                    }`}
                    >
                      Select
                    </button>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="rounded-3xl bg-white/90 border border-earth-200 p-10 text-center text-foreground/70">
              Donation tiers are currently disabled. Enable the donations module in admin settings to show contribution options.
            </div>
          )}

          <div className="mt-12">
            {isNewsletterEnabled ? (
              <div className="glass-card rounded-3xl p-8 shadow-medium">
                <h3 className="font-serif text-2xl font-bold text-foreground mb-3">Subscribe for updates</h3>
                <p className="text-foreground/70 mb-6">Get newsletter updates on planting progress, impact stories, and upcoming volunteer opportunities.</p>
                <form onSubmit={subscribeToNewsletter} className="flex flex-col sm:flex-row items-center gap-4">
                  <input
                    type="email"
                    value={newsletterEmail}
                    onChange={(e) => setNewsletterEmail(e.target.value)}
                    placeholder="Your email"
                    className="min-w-0 flex-1 px-4 py-3 rounded-full bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors"
                  />
                  <button
                    type="submit"
                    disabled={newsletterPending}
                    className="inline-flex items-center justify-center rounded-full bg-forest-600 px-6 py-3 text-white font-semibold transition-colors hover:bg-forest-700 disabled:opacity-70"
                  >
                    {newsletterPending ? 'Subscribing…' : 'Subscribe'}
                  </button>
                </form>
                {newsletterStatus ? (
                  <div className="mt-4 text-sm text-foreground/70">{newsletterStatus}</div>
                ) : null}
              </div>
            ) : (
              <div className="rounded-3xl bg-white/90 border border-earth-200 p-8 text-center text-foreground/70">
                Newsletter sign-up is currently disabled. Enable the newsletter module in admin settings to collect email subscribers.
              </div>
            )}
          </div>

          <div className="mt-12 text-center">
            <p className="text-sm text-foreground/40">All donations are tax-deductible. 100% of your contribution goes directly to planting trees.</p>
          </div>*/}
        </div>
      </div>
      <Donation3DSection />
    </section>
  );
};

export default CallToAction;
