'use client';

import { useEffect, useState } from 'react';
import { Facebook, Instagram, Linkedin, Mail, MapPin, Phone, Twitter } from 'lucide-react';
import { publicApi, PublicKeys } from '@/lib/publicApi';

const footerLinks = {
  organization: [
    { name: 'About Us', href: '/about' },
    { name: 'Our Team', href: '/about' },
    { name: 'Impact', href: '/impact' },
    { name: 'Press', href: '/about' }
  ],
  programs: [
    { name: 'Reforestation', href: '/projects' },
    { name: 'Live Map', href: '/projects' },
    { name: 'Education', href: '/faq' },
    { name: 'Research', href: '/impact' }
  ],
  getInvolved: [
    { name: 'Donate', href: '/donate' },
    { name: 'Volunteer', href: '/volunteer' },
    { name: 'Partner', href: '/contact' },
    { name: 'Get Involved', href: '/get-involved' }
  ],
  resources: [
    { name: 'Blog', href: '/blog' },
    { name: 'Contact', href: '/contact' },
    { name: 'Volunteer', href: '/volunteer' }
  ]
};

type SocialLink = { platform: string; url: string };
type SocialStore = { links?: SocialLink[]; embeds?: unknown[] } | null;
type ModulesMap = Record<string, boolean> | null;

const socialIcons = [
  { platform: 'instagram', icon: Instagram, label: 'Instagram' },
  { platform: 'twitter', icon: Twitter, label: 'Twitter' },
  { platform: 'facebook', icon: Facebook, label: 'Facebook' },
  { platform: 'linkedin', icon: Linkedin, label: 'LinkedIn' }
];

export default function Footer() {
  const currentYear = new Date().getFullYear();

  const [socialStore, setSocialStore] = useState<SocialStore>(null);
  const [newsletterEnabled, setNewsletterEnabled] = useState(false);
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<string | null>(null);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const [social, modules] = await Promise.all([
          publicApi.read<SocialStore>(PublicKeys.Social),
          publicApi.read<ModulesMap>(PublicKeys.Modules)
        ]);
        if (!mounted) return;
        setSocialStore(social);
        setNewsletterEnabled(modules?.newsletter !== false);
      } catch {
        if (!mounted) return;
        setSocialStore(null);
        setNewsletterEnabled(false);
      } finally {
        if (mounted) setLoaded(true);
      }
    })();
    return () => {
      mounted = false;
    };
  }, []);

  const socialLinks = socialStore?.links?.length
    ? socialIcons
        .map((s) => {
          const url = socialStore?.links?.find((l) => l.platform === s.platform)?.url ?? '';
          return { ...s, href: url || null };
        })
        .filter((l) => l.href)
    : [];

  const subscribe = async () => {
    const value = email.trim();
    if (!value || !value.includes('@')) {
      setStatus('Please enter a valid email address.');
      return;
    }
    try {
      const res = await fetch('/api/public/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: value })
      });
      const json = await res.json();
      if (!res.ok || !json.ok) {
        setStatus(json.message ?? 'Unable to subscribe. Please try again later.');
      } else {
        setStatus('Thanks for subscribing!');
        setEmail('');
      }
    } catch {
      setStatus('Unable to subscribe. Please try again later.');
    }
  };

  return (
    <footer className="relative w-full py-16 sm:py-20 overflow-hidden border-t border-earth-200 bg-gradient-to-b from-background to-earth-50">
      <div className="absolute inset-0 leaf-pattern opacity-10" />
      <div className="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-100/30 to-transparent" />
      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20\">
        <div className="mx-auto\">{/*max-w-7xl */}
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 lg:gap-12 mb-12 sm:mb-16\">
            <div className="col-span-2 md:col-span-4 lg:col-span-1 mb-4 lg:mb-0">
              <a href="/" className="flex items-center gap-2 mb-4">
                <img src="/logo.png" alt="OneMoreTree" className="h-8 w-auto sm:h-10" />
                <span className="font-serif text-xl font-bold text-foreground">
                  OneMore<span className="text-forest-600">Tree</span>
                </span>
              </a>
              <p className="text-sm text-foreground/60 mb-6 max-w-xs">
                Restoring forests, one tree at a time.
              </p>
              {loaded && socialLinks.length > 0 ? (
                <div className="flex gap-3">
                  {socialLinks.map((social) => (
                    <a
                      key={social.platform}
                      href={social.href!}
                      aria-label={social.label}
                      className="w-10 h-10 rounded-lg bg-white hover:bg-forest-50 border border-earth-200 hover:border-forest-300 flex items-center justify-center transition-all duration-300 group shadow-soft"
                    >
                      <social.icon className="w-4 h-4 text-foreground/50 group-hover:text-forest-600 transition-colors" />
                    </a>
                  ))}
                </div>
              ) : null}
            </div>
            <div>
              <h4 className="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Organization</h4>
              <ul className="space-y-3">
                {footerLinks.organization.map((link, i) => (
                  <li key={i}>
                    <a href={link.href} className="text-sm text-foreground/60 hover:text-forest-600 transition-colors">
                      {link.name}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Programs</h4>
              <ul className="space-y-3">
                {footerLinks.programs.map((link, i) => (
                  <li key={i}>
                    <a href={link.href} className="text-sm text-foreground/60 hover:text-forest-600 transition-colors">
                      {link.name}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Get Involved</h4>
              <ul className="space-y-3">
                {footerLinks.getInvolved.map((link, i) => (
                  <li key={i}>
                    <a href={link.href} className="text-sm text-foreground/60 hover:text-forest-600 transition-colors">
                      {link.name}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Resources</h4>
              <ul className="space-y-3">
                {footerLinks.resources.map((link, i) => (
                  <li key={i}>
                    <a href={link.href} className="text-sm text-foreground/60 hover:text-forest-600 transition-colors">
                      {link.name}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="text-sm font-semibold text-foreground mb-4 uppercase tracking-wider">Contact</h4>
              <ul className="space-y-3">
                <li className="flex items-start gap-3">
                  <Mail className="w-4 h-4 text-forest-600 flex-shrink-0 mt-0.5" />
                  <a href="mailto:info@onemoretree.eu" className="text-sm text-foreground/60 hover:text-forest-600 transition-colors">
                    info@onemoretree.eu
                  </a>
                </li>
              </ul>
            </div>
          </div>

          {loaded && newsletterEnabled ? (
            <div className="glass-card rounded-2xl p-6 sm:p-8 mb-12 shadow-medium">
              <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                  <h4 className="font-serif text-xl font-bold text-foreground mb-1">Stay in the loop</h4>
                  <p className="text-sm text-foreground/60">Get updates on our planting projects and impact reports.</p>
                </div>
                <div className="flex gap-3 flex-col sm:flex-row">
                  <input
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Enter your email"
                    className="px-4 py-3 rounded-xl bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors w-full sm:w-64 shadow-soft"
                  />
                  <button
                    onClick={subscribe}
                    className="px-6 py-3 bg-forest-600 hover:bg-forest-700 text-white font-medium rounded-xl transition-all duration-300 shadow-medium hover:shadow-elevated whitespace-nowrap"
                  >
                    Subscribe
                  </button>
                </div>
              </div>
              {status ? <div className="mt-3 text-sm text-foreground/70">{status}</div> : null}
            </div>
          ) : null}

          <div className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8 border-t border-earth-200 text-center">
            <p className="text-sm text-foreground/50">
              {currentYear} OneMoreTree. All rights reserved.
            </p>
            <div className="flex gap-6 flex-wrap justify-center">
              <a href="#" className="text-sm text-foreground/50 hover:text-forest-600 transition-colors">
                Privacy Policy
              </a>
              <a href="#" className="text-sm text-foreground/50 hover:text-forest-600 transition-colors">
                Terms of Service
              </a>
              <a href="#" className="text-sm text-foreground/50 hover:text-forest-600 transition-colors">
                Cookie Policy
              </a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
