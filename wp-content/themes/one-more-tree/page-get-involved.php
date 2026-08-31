<?php
/**
 * Template Name: Get Involved Page
 *
 * The template for displaying the Get Involved page
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js GetInvolvedView -->
    <section class="relative min-h-[50vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-5xl mx-auto text-center">
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('get_involved_hero_title', 'Get Involved'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('get_involved_hero_subtitle', 'Join our movement. Plant trees, make an impact, and help restore Spain\'s forests'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- CallToAction Section - Exact mirror of Next.js CallToAction.tsx -->
    <section id="join" class="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-gradient-to-t from-forest-100/50 to-transparent"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-12 sm:mb-16">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">Join the Movement</span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-4 sm:mb-6">
              Plant a Tree for <span class="text-forest-700 block">1 Euro</span>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto mb-8">
              Every euro becomes a seed of change. From wildfire-damaged forests to school playgrounds, your support brings life back to Spain.
            </p>
          </div>

          <!-- Donation 3D Card Section -->
          <div class="relative overflow-hidden py-12 bg-transparent">
            <div class="grid gap-10 xl:grid-cols-[1.1fr_0.9fr] items-center">
              <div class="space-y-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-forest-100/70 px-4 py-2 text-sm font-semibold text-forest-700 shadow-sm">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                  Plant one more tree
                </div>
                <h3 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-foreground leading-tight">
                  Give with confidence in a beautiful, interactive way
                </h3>
                <p class="max-w-2xl text-base sm:text-lg text-foreground/70 leading-relaxed">
                  Explore each donation tier with a 3D preview card, then choose the amount that helps grow forests across Spain. This section is designed to feel engaging, warm, and effortless.
                </p>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                  <button class="tier-select-btn rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-forest-500 bg-forest-50 text-forest-900 shadow-medium" data-amount="25">
                    <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                    <p class="text-3xl font-semibold">$25</p>
                    <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
                  </button>
                  <button class="tier-select-btn rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50" data-amount="50">
                    <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                    <p class="text-3xl font-semibold">$50</p>
                    <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
                  </button>
                  <button class="tier-select-btn rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50" data-amount="100">
                    <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                    <p class="text-3xl font-semibold">$100</p>
                    <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
                  </button>
                </div>
              </div>

              <div class="relative mx-auto w-full max-w-xl">
                <div class="donation-card-tilt relative cursor-grab overflow-hidden rounded-[40px] bg-gradient-to-br from-white via-forest-50 to-forest-100 p-6 shadow-elevated transition-transform duration-300" style="perspective: 1400px;">
                  <div class="donation-card-inner relative transform-gpu rounded-[32px] border border-slate-200 bg-white p-8 shadow-2xl transition-transform duration-300">
                    <div class="absolute inset-0 rounded-[32px] bg-gradient-to-br from-forest-100/60 to-white/90 opacity-80 pointer-events-none"></div>
                    <div class="relative space-y-6">
                      <div class="flex items-center justify-between gap-4">
                        <div>
                          <p class="text-sm uppercase tracking-[0.2em] text-forest-600">Donation</p>
                          <h4 class="donation-card-amount mt-2 text-2xl font-semibold text-foreground">$25 support</h4>
                        </div>
                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-forest-600 text-white shadow-lg">
                          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                          </svg>
                        </div>
                      </div>

                      <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-forest-50 p-4">
                          <p class="text-sm text-forest-700">Trees planted</p>
                          <p class="donation-trees-count mt-2 text-3xl font-semibold text-forest-900">75</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                          <p class="text-sm text-foreground/70">Community support</p>
                          <p class="donation-volunteers-count mt-2 text-3xl font-semibold text-foreground">+3 volunteers</p>
                        </div>
                      </div>

                      <div class="rounded-3xl bg-forest-600/10 p-4 text-sm text-forest-700">
                        <p class="font-medium">Impact snapshot</p>
                        <p class="mt-2 leading-relaxed text-foreground/75">Your donation helps plant trees, improve soil health, and support local reforestation teams in Spain.</p>
                      </div>

                      <button class="donation-submit-btn w-full rounded-3xl bg-forest-600 px-6 py-4 text-white font-semibold shadow-medium transition-all duration-300 hover:bg-forest-700">
                        Donate $25
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Newsletter Card -->
          <div class="mt-12 max-w-2xl mx-auto">
            <div class="glass-card rounded-3xl p-8 shadow-medium border border-earth-200">
              <h3 class="font-serif text-2xl font-bold text-foreground mb-3">Subscribe for updates</h3>
              <p class="text-foreground/70 mb-6">Get newsletter updates on planting progress, impact stories, and upcoming volunteer opportunities.</p>
              <form id="newsletter-form-main" class="flex flex-col sm:flex-row items-center gap-4">
                <input
                  type="email"
                  name="email"
                  required
                  placeholder="Your email"
                  class="w-full sm:w-64 px-4 py-3 rounded-full bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors shadow-soft"
                />
                <button
                  type="submit"
                  class="inline-flex items-center justify-center rounded-full bg-forest-600 px-6 py-3 text-white font-semibold transition-colors hover:bg-forest-700 shadow-medium whitespace-nowrap"
                >
                  Subscribe
                </button>
              </form>
              <div id="newsletter-form-status" class="mt-3 text-sm text-foreground/70 hidden"></div>
            </div>
          </div>

          <div class="mt-12 text-center">
            <p class="text-sm text-foreground/40">All donations are tax-deductible. 100% of your contribution goes directly to planting trees.</p>
          </div>
        </div>
      </div>
    </section>

    <?php
    // Optional Gutenberg editor blocks added by administrator on Get Involved page
    while (have_posts()) : the_post();
      if (!empty(trim(get_the_content()))): ?>
        <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20 bg-background border-t border-earth-200">
          <div class="max-w-5xl mx-auto prose prose-lg prose-forest max-w-none">
            <?php the_content(); ?>
          </div>
        </section>
      <?php endif;
    endwhile;
    ?>
  </main>
</div>

<?php
get_footer();
