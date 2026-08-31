'use client';

import { useEffect, useMemo, useState, type PointerEvent } from 'react';

import { Heart, Sparkles } from 'lucide-react';

import { publicApi, PublicKeys } from '@/lib/publicApi';

type TierPreset = {
  id: string;
  amountEur: number;
  label?: string;
};

const FALLBACK_TIER_AMOUNTS: number[] = [];


const Donation3DSection = () => {
  const [rotation, setRotation] = useState({ x: 0, y: 0 });
  const [selectedTier, setSelectedTier] = useState<number>(100);
  const [storedTiers, setStoredTiers] = useState<TierPreset[]>([]);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const tiers = await publicApi.read<{ tiers?: TierPreset[] }>(PublicKeys.DonationTiers);
        if (!mounted) return;
        const raw = (tiers as any)?.tiers;
        setStoredTiers(Array.isArray(raw) ? raw : []);
      } catch {
        if (!mounted) return;
        setStoredTiers([]);
      }
    })();
    return () => {
      mounted = false;
    };
  }, []);


  const tiersToRender = useMemo<number[]>(() => {
    const amounts = storedTiers
      .map((t) => t.amountEur)
      .filter((n) => Number.isFinite(n) && n > 0);

    const uniqueSorted = Array.from(new Set(amounts)).sort((a, b) => a - b);
    return uniqueSorted.length ? uniqueSorted : FALLBACK_TIER_AMOUNTS;
  }, [storedTiers]);



  const handlePointerMove = (event: PointerEvent<HTMLDivElement>) => {
    const { left, top, width, height } = event.currentTarget.getBoundingClientRect();
    const x = ((event.clientX - left) / width - 0.5) * 16;
    const y = ((event.clientY - top) / height - 0.5) * -16;
    setRotation({ x, y });
  };

  const handlePointerLeave = () => {
    setRotation({ x: 0, y: 0 });
  };

  return (
    <section className="relative overflow-hidden py-20 sm:py-28 bg-background page-header-texture">
      <div className="absolute inset-0 leaf-pattern opacity-15 pointer-events-none" />
      <div className="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-forest-50/90 to-transparent pointer-events-none" />

      <div className="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-12">
        <div className="grid gap-10 xl:grid-cols-[1.1fr_0.9fr] items-center">
          <div className="space-y-6">
            <div className="inline-flex items-center gap-2 rounded-full bg-forest-100/70 px-4 py-2 text-sm font-semibold text-forest-700 shadow-sm">
              <Sparkles className="w-5 h-5" />
              Plant one more tree
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-foreground leading-tight">
              Give with confidence in a beautiful, interactive way
            </h2>
            <p className="max-w-2xl text-base sm:text-lg text-foreground/70 leading-relaxed">
              Explore each donation tier with a 3D preview card, then choose the amount that helps grow forests across Spain. This section is designed to feel engaging, warm, and effortless.
            </p>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              {tiersToRender.slice(0, 3).map((amount: number) => (

                <button
                  key={amount}
                  onClick={() => setSelectedTier(amount)}
                  className={`rounded-3xl border px-4 py-4 text-left transition-all duration-300 ${
                    selectedTier === amount
                      ? 'border-forest-500 bg-forest-50 text-forest-900 shadow-medium'
                      : 'border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50'
                  }`}
                >
                  <p className="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                  <p className="text-3xl font-semibold">${amount}</p>
                  <p className="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
                </button>
              ))}
            </div>
          </div>

          <div className="relative mx-auto w-full max-w-xl">
            <div
              onPointerMove={handlePointerMove}
              onPointerLeave={handlePointerLeave}
              className="relative cursor-grab overflow-hidden rounded-[40px] bg-gradient-to-br from-white via-forest-50 to-forest-100 p-6 shadow-elevated transition-transform duration-300"
              style={{ perspective: 1400 }}
            >
              <div
                className="relative transform-gpu rounded-[32px] border border-slate-200 bg-white p-8 shadow-2xl transition-transform duration-300"
                style={{ transform: `rotateX(${rotation.x}deg) rotateY(${rotation.y}deg)` }}
              >
                <div className="absolute inset-0 rounded-[32px] bg-gradient-to-br from-forest-100/60 to-white/90 opacity-80" />
                <div className="relative space-y-6">
                  <div className="flex items-center justify-between gap-4">
                    <div>
                      <p className="text-sm uppercase tracking-[0.2em] text-forest-600">Donation</p>
                      <h3 className="mt-2 text-2xl font-semibold text-foreground">${selectedTier} support</h3>
                    </div>
                    <div className="flex h-14 w-14 items-center justify-center rounded-3xl bg-forest-600 text-white shadow-lg">
                      <Heart className="w-6 h-6" />
                    </div>
                  </div>

                  <div className="grid gap-4 sm:grid-cols-2">
                    <div className="rounded-3xl bg-forest-50 p-4">
                      <p className="text-sm text-forest-700">Trees planted</p>
                      <p className="mt-2 text-3xl font-semibold text-forest-900">{selectedTier * 3}</p>
                    </div>
                    <div className="rounded-3xl bg-slate-50 p-4">
                      <p className="text-sm text-foreground/70">Community support</p>
                      <p className="mt-2 text-3xl font-semibold text-foreground">+{selectedTier / 10} volunteers</p>
                    </div>
                  </div>

                  <div className="rounded-3xl bg-forest-600/10 p-4 text-sm text-forest-700">
                    <p className="font-medium">Impact snapshot</p>
                    <p className="mt-2 leading-relaxed text-foreground/75">Your donation helps plant trees, improve soil health, and support local reforestation teams in Spain.</p>
                  </div>

                  <button className="w-full rounded-3xl bg-forest-600 px-6 py-4 text-white font-semibold shadow-medium transition-all duration-300 hover:bg-forest-700">
                    Donate ${selectedTier}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Donation3DSection;
