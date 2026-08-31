import Link from 'next/link';
import { cookies } from 'next/headers';

async function hasAdminGodModeCookie() {
  const cookieStore = await cookies();
  return Boolean(cookieStore.get('admin.session')?.value);
}

export default async function MaintenancePage() {
  const canProceedGodMode = await hasAdminGodModeCookie();

  return (
    <div className="min-h-screen bg-background flex items-center justify-center px-4 py-16 sm:py-24">
      <div className="w-full max-w-4xl rounded-3xl border border-earth-200 bg-white/90 p-6 sm:p-10 text-center shadow-xl relative overflow-hidden">
        {/* animated background ornaments (no dependency) */}
        <div className="pointer-events-none absolute inset-0 opacity-80">
          <div className="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-gradient-to-tr from-forest-500/25 via-forest-300/20 to-emerald-300/20 blur-2xl animate-pulse" />
          <div className="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-tr from-earth-200/40 via-slate-200/30 to-forest-400/25 blur-2xl animate-pulse" />
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(34,197,94,0.18),transparent_45%),radial-gradient(circle_at_80%_30%,rgba(16,185,129,0.12),transparent_40%),radial-gradient(circle_at_50%_90%,rgba(5,150,105,0.12),transparent_55%)]" />
          <div className="absolute inset-0 leaf-pattern opacity-10 pointer-events-none" />
        </div>

        <div className="relative z-10">
          <div className="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-earth-200 bg-white/70 shadow-sm">
            <span className="inline-flex h-2.5 w-2.5 rounded-full bg-forest-600 animate-ping" />
            <p className="text-xs sm:text-sm uppercase tracking-[0.25em] text-foreground/60 mb-0">
              Maintenance mode
            </p>
          </div>

          <h1 className="mt-6 font-serif text-4xl sm:text-6xl font-bold text-foreground">
            We&apos;ll be back soon.
          </h1>

          <p className="mt-4 text-base sm:text-lg leading-8 text-foreground/70 max-w-2xl mx-auto">
            The website is temporarily offline so we can improve performance and reliability.
          </p>

          {/* animated info blocks */}
          <div className="mt-8 grid gap-4 sm:grid-cols-2">
            <div className="rounded-2xl border border-earth-200 bg-white/70 p-5 text-left">
              <div className="text-foreground font-semibold">What&apos;s happening</div>
              <div className="mt-1 text-foreground/70 text-sm leading-6">
                Deploying updates and syncing content modules.
              </div>
              <div className="mt-3 h-1.5 w-full rounded-full bg-earth-200 overflow-hidden">
                <div className="h-full w-2/3 bg-forest-600 rounded-full animate-[maintenance-slide_1.6s_ease-in-out_infinite]" />
              </div>
            </div>

            <div className="rounded-2xl border border-earth-200 bg-white/70 p-5 text-left">
              <div className="text-foreground font-semibold">Expected time</div>
              <div className="mt-1 text-foreground/70 text-sm leading-6">
                Short maintenance window. We&apos;ll bring it back as soon as it&apos;s ready.
              </div>
              <div className="mt-3 text-xs uppercase tracking-[0.14em] text-foreground/50">
                Thank you for your patience
              </div>
            </div>
          </div>

          {/* actions */}
          <div className="mt-10 flex flex-col sm:flex-row gap-3 sm:justify-center">
            {/* <Link
              href="/admin/login"
              className="rounded-2xl bg-forest-600 px-6 py-3 text-white transition hover:bg-forest-700 shadow-sm"
            >
              Admin Login
            </Link> */}

            {canProceedGodMode ? (
              <a
                href="/admin"
                className="
                  rounded-2xl px-6 py-3 text-foreground
                  shadow-[0_0_0_1px_rgba(20,83,45,0.15),0_12px_40px_rgba(0,0,0,0.12)]
                  bg-gradient-to-b from-yellow-300 via-amber-400 to-yellow-200
                  hover:brightness-110 transition
                  font-semibold
                "
              >
                Proceed as God Mode
              </a>
            ) : null}
          </div>

          <p className="mt-6 text-xs text-foreground/50">
            If you&apos;re seeing this unexpectedly, please try refreshing once maintenance is complete.
          </p>
        </div>

      </div>
    </div>
  );
}
