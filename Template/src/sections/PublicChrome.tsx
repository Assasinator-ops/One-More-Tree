'use client';

import { useEffect, useState } from 'react';
import { Info, X } from 'lucide-react';
import Navigation from './Navigation';
import Footer from './Footer';
import { publicApi, PublicKeys } from '@/lib/publicApi';

type DisclaimerColor = 'green' | 'red' | 'yellow' | 'blue' | 'pink';

type Disclaimer = {
  enabled: boolean;
  message: string;
  color: DisclaimerColor;
};

function getColorClasses(color: DisclaimerColor) {
  switch (color) {
    case 'red':
      return { bg: 'bg-red-950', fg: 'text-red-50', buttonHover: 'hover:bg-red-700/60' };
    case 'yellow':
      return { bg: 'bg-amber-950', fg: 'text-amber-50', buttonHover: 'hover:bg-amber-800/60' };
    case 'blue':
      return { bg: 'bg-blue-950', fg: 'text-blue-50', buttonHover: 'hover:bg-blue-800/60' };
    case 'pink':
      return { bg: 'bg-fuchsia-950', fg: 'text-fuchsia-50', buttonHover: 'hover:bg-fuchsia-700/60' };
    case 'green':
    default:
      return { bg: 'bg-forest-900', fg: 'text-forest-50', buttonHover: 'hover:bg-forest-700/60' };
  }
}

/**
 * PublicChrome owns the disclaimer ribbon + nav + footer chrome. The
 * disclaimer is `sticky top-0` and the navbar sits immediately below it via
 * the `--disclaimer-h` CSS variable. This avoids the "void above header"
 * you would see if the disclaimer were absolutely/fixed-positioned with
 * page content starting at y=0.
 */
export default function PublicChrome({
  children,
  isAdmin
}: {
  children: React.ReactNode;
  isAdmin: boolean;
}) {
  const [dismissed, setDismissed] = useState(false);
  const [hydrated, setHydrated] = useState(false);
  const [disclaimer, setDisclaimer] = useState<Disclaimer | null>(null);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    setHydrated(true);
  }, []);

  useEffect(() => {
    if (!hydrated) return;
    let mounted = true;
    (async () => {
      try {
        const value = (await publicApi.read<Disclaimer>(PublicKeys.Disclaimer)) as Disclaimer | null;
        if (!mounted) return;
        setDisclaimer(value ?? null);
      } catch {
        if (!mounted) return;
        setDisclaimer(null);
      } finally {
        if (mounted) setLoaded(true);
      }
    })();
    return () => {
      mounted = false;
    };
  }, [hydrated]);

  const showDisclaimer =
    hydrated && loaded && disclaimer?.enabled && disclaimer.message && !dismissed;

  // Push the fixed nav down so it sits just below the fixed disclaimer ribbon.
  useEffect(() => {
    if (!hydrated || !showDisclaimer || !disclaimer?.enabled) {
      document.documentElement.style.removeProperty('--disclaimer-h');
      return;
    }
    const node = document.getElementById('disclaimer-ribbon');
    if (!node) return;
    const set = () => {
      document.documentElement.style.setProperty('--disclaimer-h', `${node.offsetHeight}px`);
    };
    set();
    window.addEventListener('resize', set);
    return () => window.removeEventListener('resize', set);
  }, [showDisclaimer, disclaimer, hydrated]);

  return (
    <>
      {/*
        The wrapper always reserves the disclaimer slot (h=0 when hidden) so
        the nav's `top: var(--disclaimer-h, 0px)` resolves correctly even when
        the disclaimer is not displayed.
      */}
      {showDisclaimer && disclaimer ? (
        <DisclaimerBanner
          disclaimer={disclaimer}
          onDismiss={() => setDismissed(true)}
        />
      ) : (
        <div id="disclaimer-ribbon" className="relative z-[60] w-full h-0" aria-hidden="true" />
      )}
      <Navigation isAdmin={isAdmin} />
      <main className="relative">{children}</main>
      <Footer />
    </>
  );
}

function DisclaimerBanner({
  disclaimer,
  onDismiss
}: {
  disclaimer: Disclaimer;
  onDismiss: () => void;
}) {
  const colorClasses = getColorClasses(disclaimer.color);
  return (
    <div
      id="disclaimer-ribbon"
      role="status"
      aria-label="Site disclaimer"
      className={`fixed top-0 left-0 right-0 z-[60] w-full ${colorClasses.bg} ${colorClasses.fg}`}
    >
      <div className="w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="flex items-center justify-between gap-3 py-1.5 text-[11px] sm:text-xs">
          <div className="flex items-center gap-2 min-w-0 flex-1">
            <Info className="w-3.5 h-3.5 flex-shrink-0 opacity-90" aria-hidden="true" />
            
            <p className="leading-snug break-words line-clamp-2 sm:line-clamp-1">
              {disclaimer.message}
            </p>
          </div>

          <button
            type="button"
            onClick={onDismiss}
            aria-label="Dismiss disclaimer"
            className={`flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full ${colorClasses.buttonHover} transition-colors`}
          >
            <X className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>
  );
}
