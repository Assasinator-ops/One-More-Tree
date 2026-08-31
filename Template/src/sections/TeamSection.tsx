'use client';

import { useEffect, useMemo, useState } from 'react';

import { publicApi, PublicKeys } from '@/lib/publicApi';

import { Users, Award, Leaf } from 'lucide-react';
import { usePublishedContent } from '@/content/usePublishedContent';


const TeamSection = () => {
  const { content } = usePublishedContent();
  const team = content?.team;
  const [adminTeam, setAdminTeam] = useState<{
    sectionTitle?: string;
    headline?: string;
    description?: string;
    members?: Array<{ name: string; role: string; image?: string; highlight?: string }>;
    stats?: Array<{ value: string; label: string }>;
  } | null>(null);

  useEffect(() => {
    let alive = true;

    (async () => {
      try {
        const t = await publicApi.read<{
          sectionTitle?: string;
          headline?: string;
          description?: string;
          members?: Array<{ name: string; role: string; image?: string; highlight?: string }>;
          stats?: Array<{ value: string; label: string }>;
        }>(PublicKeys.Team);

        if (!alive) return;
        setAdminTeam(t ?? null);
      } catch {
        if (!alive) return;
        setAdminTeam(null);
      }
    })();

    return () => {
      alive = false;
    };
  }, []);


  const mergedTeam = useMemo(() => {
    if (!team) return null;
    if (!adminTeam) return team;

    return {
      sectionTitle: adminTeam.sectionTitle ?? team.sectionTitle,
      headline: adminTeam.headline ?? team.headline,
      description: adminTeam.description ?? team.description,
      members: adminTeam.members?.length ? adminTeam.members : team.members,
      stats: adminTeam.stats?.length ? adminTeam.stats : team.stats,
    };
  }, [adminTeam, team]);

  const members = mergedTeam?.members ?? [];
  const stats = mergedTeam?.stats ?? [];

  return (
    <section id="team" className="relative min-h-[65vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div className="absolute inset-0 leaf-pattern opacity-20" />
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[560px] h-[560px] bg-forest-300/15 rounded-full blur-3xl" />

      <div className="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-14 sm:mb-20">
            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <Users className="w-4 h-4 text-forest-600" />
              <span className="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                {mergedTeam?.sectionTitle ?? 'Our Team'}
              </span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              {mergedTeam?.headline ?? 'People who make planting real'}
            </h2>
            <p className="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              {mergedTeam?.description ?? 'From field work to reporting, our team and partners keep restoration accountable and alive.'}
            </p>
          </div>

          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {members.map((m, i) => (
              <div key={i} className="glass-card rounded-3xl p-6 sm:p-7 hover:bg-white transition-all">
                <div className="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-forest-100 flex items-center justify-center mb-4">
                  <Leaf className="w-6 h-6 text-forest-600" />
                </div>
                <div className="font-serif text-xl font-bold text-foreground mb-1">{m.name}</div>
                <div className="text-sm text-foreground/60">{m.role}</div>
                {m.highlight ? (
                  <div className="mt-4 flex items-center gap-2 text-xs text-foreground/50">
                    <Award className="w-4 h-4 text-forest-600" />
                    {m.highlight}
                  </div>
                ) : null}
              </div>
            ))}
          </div>

          <div className="mt-10 grid md:grid-cols-3 gap-4 sm:gap-6">
            {stats.map((s, idx) => (
              <div key={idx} className="glass-card rounded-3xl p-6 text-center shadow-soft">
                <div className="font-serif text-3xl font-bold text-forest-600 mb-2">{s.value}</div>
                <div className="text-sm text-foreground/60">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};

export default TeamSection;

