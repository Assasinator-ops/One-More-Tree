'use client';

import { useEffect, useMemo, useState } from 'react';
import { Info, X } from 'lucide-react';

type DisclaimerColor = 'green' | 'red' | 'yellow' | 'blue' | 'pink';

type Disclaimer = {
  enabled: boolean;
  message: string;
  color: DisclaimerColor;
};

const STORAGE_KEY = 'disclaimer.ribbon.dismissed';

const FALLBACK: Disclaimer = {
  enabled: true,
  message:
    '🌳 OneMoreTree NGO is a volunteer-led initiative. All donations go directly to tree planting, site restoration, and community programs. We are a registered non-profit — receipts are issued on request.',
  color: 'green'
};

function getColorClasses(color: DisclaimerColor) {
  switch (color) {
    case 'red':
      return {
        bg: 'bg-red-950',
        fg: 'text-red-50',
        buttonHover: 'hover:bg-red-700/60'
      };
    case 'yellow':
      return {
        bg: 'bg-amber-950',
        fg: 'text-amber-50',
        buttonHover: 'hover:bg-amber-800/60'
      };
    case 'blue':
      return {
        bg: 'bg-blue-950',
        fg: 'text-blue-50',
        buttonHover: 'hover:bg-blue-800/60'
      };
    case 'pink':
      return {
        bg: 'bg-fuchsia-950',
        fg: 'text-fuchsia-50',
        buttonHover: 'hover:bg-fuchsia-800/60'
      };
    case 'green':
    default:
      return {
        bg: 'bg-forest-900',
        fg: 'text-forest-50',
        buttonHover: 'hover:bg-forest-700/60'
      };
  }
}

export default function DisclaimerRibbon() {
  const [dismissed, setDismissed] = useState(false);
  const [hydrated, setHydrated] = useState(false);
  const [disclaimer, setDisclaimer] = useState<Disclaimer>(FALLBACK);

  useEffect(() => {
    setHydrated(true);
    try {
      if (window.localStorage.getItem(STORAGE_KEY) === '1') setDismissed(true);
    } catch {
      // ignore
    }
  }, []);

  useEffect(() => {
    if (!hydrated) return;
    let mounted = true;
    (async () => {
      try {
        const res = await fetch('/api/admin/data?key=disclaimer', { cache: 'no-store' });
        const json = await res.json();
        const value = (json?.ok ? json?.value : null) as Disclaimer | null;
        if (!mounted) return;
        setDisclaimer(value ?? FALLBACK);
      } catch {
        if (!mounted) return;
        setDisclaimer(FALLBACK);
      }
    })();
    return () => {
      mounted = false;
    };
  }, [hydrated]);

  const colorClasses = useMemo(() => getColorClasses(disclaimer.color), [disclaimer.color]);

  // Push the fixed nav down so it sits below this ribbon.
  useEffect(() => {
    if (!hydrated) return;
    if (dismissed || !disclaimer.enabled) {
      document.documentElement.style.removeProperty('--disclaimer-h');
      return;
    }
    const node = document.getElementById('disclaimer-ribbon');
    const set = () => {
      if (node) {
        document.documentElement.style.setProperty('--disclaimer-h', `${node.offsetHeight}px`);
      }
    };
    set();
    window.addEventListener('resize', set);
    return () => window.removeEventListener('resize', set);
  }, [dismissed, disclaimer.enabled, hydrated]);

  if (!hydrated || dismissed || !disclaimer.enabled) return null;

  return (
    <div
      id="disclaimer-ribbon"
      role="status"
      aria-label="Site disclaimer"
      className={`relative z-[60] w-full ${colorClasses.bg} ${colorClasses.fg}`}
    >
      <div className="w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="flex items-center justify-between gap-3 py-1.5 text-[11px] sm:text-xs">
          <div className="flex items-center gap-2 min-w-0">
            <Info className="w-3.5 h-3.5 flex-shrink-0 opacity-90" aria-hidden="true" />
            <p className="truncate leading-snug">
              <span className="sm:hidden">OneMoreTree NGO — all donations fund tree planting.</span>
              <span className="hidden sm:inline">{disclaimer.message}</span>
            </p>
          </div>
          <button
            type="button"
            onClick={() => {
              setDismissed(true);
              try {
                window.localStorage.setItem(STORAGE_KEY, '1');
              } catch {
                // ignore
              }
            }}
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

