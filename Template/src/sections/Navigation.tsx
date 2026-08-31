'use client';

import { useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { gsap } from 'gsap';
import { Menu, ShieldCheck, X } from 'lucide-react';
import { publicApi, PublicKeys } from '@/lib/publicApi';

const navLinks = [
  { name: 'Home', href: '/' },
  { name: 'About', href: '/about' },
  { name: 'Projects', href: '/projects' },
  { name: 'Impact', href: '/impact' },
  { name: 'FAQ', href: '/faq' },
  { name: 'Get Involved', href: '/get-involved' }
];

export default function Navigation({ isAdmin = false }: { isAdmin?: boolean }) {
  const isDev = process.env.NODE_ENV !== 'production';
  const showAdminChip = isAdmin && isDev;
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const pathname = usePathname();
  const navRef = useRef<HTMLElement>(null);

  const isActive = (href: string) => {
    if (href === '/') return pathname === '/';
    return pathname.startsWith(href);
  };

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 100);
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    if (navRef.current) {
      gsap.fromTo(
        navRef.current,
        { y: -100, opacity: 0 },
        { y: 0, opacity: 1, duration: 1, delay: 0.5, ease: 'power3.out' }
      );
    }
  }, []);

  return (
    <nav
      ref={navRef}
      style={{ top: 'var(--disclaimer-h, 0px)' }}
      className={`fixed left-0 right-0 z-50 transition-all duration-500 ${
        isScrolled
          ? 'bg-white/98 backdrop-blur-xl border-b border-earth-200 shadow-medium'
          : 'bg-white md:bg-transparent'
      } ${!isScrolled && 'md:bg-gradient-to-b md:from-white/90 md:to-transparent'}`}
    >
      <div className="w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="flex items-center justify-between h-16 sm:h-20">
          <Link href="/" className="flex items-center gap-2 group">
            <img
              src="/logo.png"
              alt="OneMoreTree"
              className="h-8 w-auto sm:h-10 transition-transform duration-300 group-hover:scale-105"
            />
            <span className="font-serif text-lg sm:text-xl font-bold text-foreground">
              OneMore<span className="text-forest-600">Tree</span>
            </span>
          </Link>

          <div className="hidden md:flex items-center gap-8">
            {navLinks.map((link) => (
              <Link
                key={link.name}
                href={link.href}
                className={`text-sm transition-colors duration-300 animated-underline font-medium ${
                  isActive(link.href)
                    ? 'text-forest-700 font-semibold'
                    : 'text-foreground/70 hover:text-forest-600'
                }`}
              >
                {link.name}
              </Link>
            ))}
            <Link
              href="/get-involved"
              className="px-5 py-2.5 bg-forest-600 hover:bg-forest-700 text-white text-sm font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated"
            >
              Donate Now
            </Link>
          </div>

          <button
            className="md:hidden p-2 text-foreground/70 hover:text-forest-600 transition-colors"
            onClick={() => setIsMobileMenuOpen((v) => !v)}
            aria-label="Toggle menu"
          >
            {isMobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>

        {isMobileMenuOpen ? (
          <div className="md:hidden absolute top-full left-0 right-0 bg-white border-b border-earth-200 shadow-medium">
            <div className="px-4 py-6 space-y-4">
              {navLinks.map((link) => (
                <Link
                  key={link.name}
                  href={link.href}
                  onClick={() => setIsMobileMenuOpen(false)}
                  className={`block w-full text-left text-lg transition-colors duration-300 py-2 font-medium ${
                    isActive(link.href)
                      ? 'text-forest-700 font-semibold'
                      : 'text-foreground/70 hover:text-forest-600'
                  }`}
                >
                  {link.name}
                </Link>
              ))}
              <Link
                href="/donate"
                onClick={() => setIsMobileMenuOpen(false)}
                className="block w-full mt-4 px-5 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium text-center"
              >
                Donate Now
              </Link>
            </div>
          </div>
        ) : null}
      </div>

      {/* Floating admin chip — shown for authenticated admins in dev mode only. */}
      {showAdminChip ? (
        <Link
          href="/admin"
          aria-label="Admin"
          title="Admin console"
          className="fixed bottom-4 right-4 z-[70] inline-flex items-center gap-2 px-3 py-2 rounded-full bg-forest-700/95 hover:bg-forest-600 text-white text-xs font-semibold shadow-elevated backdrop-blur-md ring-1 ring-white/20 transition-all"
        >
          <ShieldCheck className="w-4 h-4" />
          <span className="hidden sm:inline">Admin</span>
        </Link>
      ) : null}
    </nav>
  );
}
