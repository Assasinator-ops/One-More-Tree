'use client';

import { useEffect, useMemo, useRef, useState } from 'react';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Globe, Target, TrendingUp, Leaf } from 'lucide-react';
import { publicApi, PublicKeys } from '@/lib/publicApi';







gsap.registerPlugin(ScrollTrigger);

const Impact = () => {
  const sectionRef = useRef<HTMLElement>(null);

  const [adminImpact, setAdminImpact] = useState<Record<string, unknown> | null>(null);


  useEffect(() => {

    let alive = true;

    (async () => {
      try {
        const admin = await publicApi.read<Record<string, unknown>>(PublicKeys.ImpactKpis);
        if (!alive) return;
        setAdminImpact(admin);
      } catch {
        if (!alive) return;
        setAdminImpact(null);
      }
    })();

    return () => {
      alive = false;
    };
  }, []);


  const effectiveImpact = useMemo(() => {
    if (!adminImpact) return null;

    // Admin payload shape (ImpactKpisAdmin.tsx -> ImpactKpisDraft)
    // { sectionTitle, impactHeadline, impactDescription, kpis: [...], progress: {...} }
    const payload = adminImpact as {
      sectionTitle?: string;
      impactHeadline?: string;
      impactDescription?: string;
      kpis?: Array<{ key?: string; label?: string; value?: number; unit?: string }>;
      progress?: {
        goalBy2040Value?: number;
        progressText?: string;
        toGoText?: string;
      };
    };

    const kpis = Array.isArray(payload.kpis) ? payload.kpis : [];
    const progress = payload.progress ?? {};

    const treesPlantedKpi = kpis.find((k) => k?.key === 'treesPlanted');
    const goalBy2040Value = Number(progress.goalBy2040Value ?? 0);
    const treesPlanted = Number(treesPlantedKpi?.value ?? 0);

    const progressText = typeof progress.progressText === 'string' ? progress.progressText : '';
    const toGoText = typeof progress.toGoText === 'string' ? progress.toGoText : '';

    const findLabel = (key: string, fallback: string) => {
      const k = kpis.find((x) => x?.key === key);
      return (typeof k?.label === 'string' && k!.label.trim().length > 0) ? k!.label : fallback;
    };

    const sectionTitle = typeof payload.sectionTitle === 'string' && payload.sectionTitle.trim().length > 0 ? payload.sectionTitle : '';
    const headline = typeof payload.impactHeadline === 'string' && payload.impactHeadline.trim().length > 0 ? payload.impactHeadline : '';
    const description = typeof payload.impactDescription === 'string' && payload.impactDescription.trim().length > 0 ? payload.impactDescription : '';

    // The public UI expects 4 KPI cards.
    const cardKeys: Array<'regionsCovered' | 'projectsActive' | 'tonsCO2' | 'volunteersEngaged'> = [
      'regionsCovered',
      'projectsActive',
      'tonsCO2',
      'volunteersEngaged',
    ];

    const stats = cardKeys.map((key) => {
      const k = kpis.find((x) => x?.key === key);
      const label = typeof k?.label === 'string' ? k!.label : '';
      const value = k?.value;
      const unit = typeof k?.unit === 'string' ? k!.unit : '';
      // We don't have desc in admin payload; use unit as a lightweight descriptor.
      const desc = unit && unit.trim().length > 0 ? unit : '';
      return { key, label, value: value as unknown as number, desc };
    });

    const parsedProgressPercentFromText = (() => {
      // expected: "84.7% Complete" (see admin computeProgressText)
      if (!progressText) return null;
      const match = progressText.match(/(\d{1,3}(?:\.\d+)?)%/);
      return match ? Number(match[1]) : null;
    })();

    const progressRatio = (() => {
      if (Number.isFinite(goalBy2040Value) && goalBy2040Value > 0) {
        if (Number.isFinite(treesPlanted) && treesPlanted >= 0) {
          return Math.max(0, Math.min(1, treesPlanted / goalBy2040Value));
        }
      }
      if (parsedProgressPercentFromText != null) {
        return Math.max(0, Math.min(1, parsedProgressPercentFromText / 100));
      }
      return null;
    })();

    const progressPercent = (() => {
      if (parsedProgressPercentFromText != null) return parsedProgressPercentFromText;
      if (progressRatio != null) return progressRatio * 100;
      return null;
    })();

    const treesPlantedLabel = findLabel('treesPlanted', '');
    const goalBy2040Label = 'Our Goal by 2040';

    return {
      sectionTitle,
      headline,
      description,
      treesPlantedLabel,
      goalBy2040Label,
      goalBy2040Value,
      toGoText,
      progressText,
      parsedProgressPercentFromText,
      progressRatio,
      progressPercent,
      treesPlanted,
      stats,
      // Helpful for rendering: same text format everywhere.
      progressLabel: progressPercent != null ? `${progressPercent.toFixed(1)}%` : '',
    };
  }, [adminImpact]);




  const [animatedTrees, setAnimatedTrees] = useState(0);

  const formatNumber = (num: number) => num.toLocaleString();

  // Animate the trees count toward the real KPI value.
  useEffect(() => {
    const triggerEl = sectionRef.current;
    if (!triggerEl) return;
    if (!effectiveImpact) return;

    const goalValue = Number.isFinite(effectiveImpact.goalBy2040Value) ? effectiveImpact.goalBy2040Value : 0;
    const treesValue = Number.isFinite(effectiveImpact.treesPlanted) ? effectiveImpact.treesPlanted : 0;
    const maxValue = treesValue > 0 ? treesValue : 0;

    if (!goalValue || !maxValue) {
      // No data yet; keep displayed value at 0 until payload exists.
      setAnimatedTrees(0);
      return;
    }

    const ctx = gsap.context(() => {
      ScrollTrigger.getAll().forEach((t) => {
        if (t.trigger === triggerEl) t.kill();
      });

      ScrollTrigger.create({
        trigger: triggerEl,
        start: 'top 70%',
        onEnter: () => {
          gsap.to({}, {
            duration: 2,
            ease: 'power2.out',
            onUpdate: function () {
              setAnimatedTrees(Math.floor(maxValue * this.progress()));
            },
          });
        },
        once: true,
      });
    }, sectionRef);

    return () => ctx.revert();
  }, [effectiveImpact]);

  const progressRatio = effectiveImpact?.progressRatio ?? null;
  const progressLabel = effectiveImpact?.progressLabel ?? '';

  const stats = (effectiveImpact?.stats ?? []).map((stat, index) => {
    const iconMap = [Globe, Target, TrendingUp, Leaf];
    const colorMap = ['text-sky-400', 'text-amber-400', 'text-emerald-400', 'text-forest-400'];
    const bgMap = ['bg-sky-500/20', 'bg-amber-500/20', 'bg-emerald-500/20', 'bg-forest-500/20'];
    const IconComponent = iconMap[index] || Globe;

    return {
      icon: IconComponent,
      value: stat.value,
      label: stat.label,
      desc: stat.desc,
      key: stat.key,
      color: colorMap[index] || 'text-forest-400',
      bg: bgMap[index] || 'bg-forest-500/20'
    };
  });


  return (
    <section ref={sectionRef} id="impact" className="relative w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Target className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">{effectiveImpact?.sectionTitle ?? 'Our Impact'}</span>

            </div>

            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-4 sm:mb-6">
              {(effectiveImpact?.headline ?? 'The Numbers Tell')}
              <span className="text-gradient">{' Our Story'}</span>
            </h2>


            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              {effectiveImpact?.description ?? "Every tree planted is a step toward our ambitious goal. Here's how far we've come."}
            </p>
          </div>

          <div className="glass-card rounded-3xl p-6 sm:p-10 lg:p-12 mb-16 shadow-medium">
            <div className="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
              <div>
              <div>
                <div className="text-sm text-foreground/50 mb-2">
                  {effectiveImpact?.treesPlantedLabel ?? 'Trees Planted'}
                </div>
                <div className="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-foreground">
                  {formatNumber(Math.floor(animatedTrees || effectiveImpact?.treesPlanted || 0))}
                </div>

              </div>


              </div>

              <div className="text-left lg:text-right">
                <div className="text-sm text-foreground/50 mb-2">{effectiveImpact?.goalBy2040Label ?? 'Our Goal by 2040'}</div>
                <div className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-forest-600">{effectiveImpact?.goalBy2040Value ?? '0'}</div>


              </div>
            </div>

            <div className="relative h-4 sm:h-6 bg-earth-200 rounded-full overflow-hidden mb-4 shadow-inner">
              {(() => {
                const percentText = progressRatio != null ? `${(progressRatio * 100).toFixed(1)}%` : '0%';

                return (
                  <div
                    className="absolute top-0 left-0 h-full bg-gradient-to-r from-forest-600 via-forest-500 to-forest-400 rounded-full"
                    style={{ width: percentText }}
                  >
                    <div className="absolute inset-0 shimmer" />
                  </div>
                );
              })()}
            </div>


            <div className="flex justify-between items-center">
              {(() => {
                const progressPct = progressLabel;
                const toGoText = effectiveImpact?.toGoText ?? '0 trees to go';


                return (
                  <>
                    <div className="text-sm text-foreground/50">{progressPct}</div>
                    <div className="text-sm text-forest-600 font-semibold">{toGoText}</div>
                  </>
                );
              })()}
            </div>


          </div>

          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {stats.map((stat, i) => (
              <div key={i} className="glass-card rounded-2xl p-5 sm:p-6 text-center hover:bg-white transition-all shadow-soft">
                <div className={`w-12 h-14 sm:w-14 sm:h-14 ${stat.bg.replace('bg-', 'bg-').replace('/20', '')} rounded-xl flex items-center justify-center mx-auto mb-4`}>
                  <stat.icon className={`w-6 h-7 sm:w-7 sm:h-7 ${stat.color}`} />
                </div>
                <div className="font-serif text-2xl sm:text-3xl font-bold text-foreground mb-1">{stat.value}</div>
                <div className="text-sm font-medium text-foreground/80 mb-1">{stat.label}</div>
                <div className="text-xs text-foreground/50">
                  {stat.key === 'tonsCO2' && stat.desc ? `${stat.desc} Estimated` : stat.desc}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Impact;

