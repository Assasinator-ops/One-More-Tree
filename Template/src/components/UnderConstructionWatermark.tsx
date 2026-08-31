'use client';

import { useEffect, useState } from 'react';

export default function UnderConstructionWatermark() {
  const [enabled, setEnabled] = useState(false);

  useEffect(() => {
    const checkUnderConstruction = async () => {
      try {
        const res = await fetch('/api/admin/under-construction', { cache: 'no-store' });
        const json = (await res.json().catch(() => ({}))) as { enabled?: boolean };
        setEnabled(Boolean(json.enabled));
      } catch {
        setEnabled(false);
      }
    };
    checkUnderConstruction();
  }, []);

  if (!enabled) return null;

  return (
    <div className="fixed inset-0 pointer-events-none z-50 flex items-center justify-center">
      <div className="flex flex-col items-center justify-center opacity-20 select-none">
        <img 
          src="/logo.png" 
          alt="One More Tree Logo" 
          className="w-64 h-64 sm:w-96 sm:h-96 object-contain"
        />
        <div className="mt-4 text-4xl sm:text-6xl font-bold text-foreground tracking-wider">
          UNDER CONSTRUCTION
        </div>
        <div className="mt-2 text-xl sm:text-2xl text-foreground/80">
          Preview Only
        </div>
      </div>
    </div>
  );
}
