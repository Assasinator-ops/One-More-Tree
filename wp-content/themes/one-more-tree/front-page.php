<?php
/**
 * The template for displaying the front page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Hero Section - Exact mirror of Next.js -->
    <section id="hero-section" class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
      <div id="hero-image" class="absolute inset-0 w-full h-[120%] -top-[10%]">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-forest.jpg" alt="Lush forest canopy" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-b from-white/40 via-white/20 to-background/80"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white/30 via-transparent to-white/30"></div>
      </div>

      <div id="hero-content" class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 pt-24 sm:pt-28">
        <div class="max-w-5xl mx-auto text-center">
          <!-- Mission badge with light theme -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-forest-200 mb-6 sm:mb-8 shadow-medium">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-colorful.png" alt="Our Mission" class="h-6 w-auto sm:h-8" />
            <span class="text-xs sm:text-sm text-foreground font-medium">
              <?php echo get_acf_field('hero_mission_badge', 'Planting trees, restoring forests.'); ?>
            </span>
          </div>

          <!-- Headline with better contrast -->
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold text-foreground leading-tight mb-4 sm:mb-6 drop-shadow-sm">
            <?php echo get_acf_field('hero_headline_top', 'One More'); ?>
            <span class="block text-forest-700 italic"><?php echo get_acf_field('hero_headline_emphasis', 'Tree'); ?></span>
          </h1>

          <p class="text-base sm:text-lg md:text-xl text-foreground/80 max-w-2xl mx-auto mb-8 sm:mb-10 leading-relaxed px-4">
            <?php echo get_acf_field('hero_subtitle', 'Planting 1 million trees across Spain by 2040. From a conversation among friends to a movement restoring forests.'); ?>
          </p>

          <!-- CTA buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12 sm:mb-16">
            <a
              href="#join"
              onclick="event.preventDefault(); document.querySelector('#join')?.scrollIntoView({ behavior: 'smooth' })"
              class="px-8 py-4 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated text-center"
            >
              <?php echo get_acf_field('hero_primary_cta_text', 'Join the Movement'); ?>
            </a>

            <button
              onclick="document.querySelector('#story')?.scrollIntoView({ behavior: 'smooth' })"
              class="px-8 py-4 bg-white/90 hover:bg-white backdrop-blur-sm border border-forest-200 text-forest-700 font-medium rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated flex items-center justify-center gap-2 group"
            >
              <?php echo get_acf_field('hero_secondary_cta_text', 'Watch the Journey'); ?>
              <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
              </svg>
            </button>
          </div>

          <!-- Statistics cards with light theme -->
          <div class="grid grid-cols-3 gap-4 sm:gap-8 max-w-2xl mx-auto">
            <div class="text-center p-3 sm:p-4 rounded-2xl bg-white/90 backdrop-blur-sm border border-forest-100 shadow-medium">
              <div class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-forest-600 mb-1">
                <?php echo get_acf_field('hero_trees_count', '1M'); ?>
              </div>
              <div class="text-xs sm:text-sm text-foreground/60">Trees Planted</div>
            </div>
            <div class="text-center p-3 sm:p-4 rounded-2xl bg-white/90 backdrop-blur-sm border border-forest-100 shadow-medium">
              <div class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-forest-600 mb-1">
                <?php echo get_acf_field('hero_projects_count', '50+'); ?>
              </div>
              <div class="text-xs sm:text-sm text-foreground/60">Projects</div>
            </div>
            <div class="text-center p-3 sm:p-4 rounded-2xl bg-white/90 backdrop-blur-sm border border-forest-100 shadow-medium">
              <div class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-forest-600 mb-1">
                <?php echo get_acf_field('hero_volunteers_count', '10K+'); ?>
              </div>
              <div class="text-xs sm:text-sm text-foreground/60">Volunteers</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Counter Section - Exact mirror of Next.js -->
    <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20">
      <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-3 gap-6">
          <div class="rounded-3xl bg-white/90 border border-slate-200 p-8 text-center shadow-soft">
            <p class="text-xs uppercase tracking-[0.25em] text-forest-500 mb-3">Trees tracked</p>
            <p class="text-4xl font-semibold text-foreground"><?php echo get_acf_field('counter_trees_tracked', '1,000,000'); ?></p>
          </div>
          <div class="rounded-3xl bg-white/90 border border-slate-200 p-8 text-center shadow-soft">
            <p class="text-xs uppercase tracking-[0.25em] text-forest-500 mb-3">Projects active</p>
            <p class="text-4xl font-semibold text-foreground"><?php echo get_acf_field('counter_projects_active', '50+'); ?></p>
          </div>
          <div class="rounded-3xl bg-white/90 border border-slate-200 p-8 text-center shadow-soft">
            <p class="text-xs uppercase tracking-[0.25em] text-forest-500 mb-3">Volunteers engaged</p>
            <p class="text-4xl font-semibold text-foreground"><?php echo get_acf_field('counter_volunteers_engaged', '10,000+'); ?></p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section - Exact mirror of Next.js -->
    <section class="py-16 sm:py-24 bg-white/95 border-t border-slate-200">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="grid gap-8 lg:grid-cols-3">
          <div class="rounded-3xl bg-white/95 border border-slate-200 p-8">
            <h3 class="font-serif text-2xl font-semibold text-foreground mb-3">Europe First</h3>
            <p class="text-foreground/70 leading-relaxed"><?php echo get_acf_field('mission_europe_first', 'Our mission is rooted in Europe: local forests, local communities, local restoration.'); ?></p>
          </div>
          <div class="rounded-3xl bg-white/95 border border-slate-200 p-8">
            <h3 class="font-serif text-2xl font-semibold text-foreground mb-3">Community Power</h3>
            <p class="text-foreground/70 leading-relaxed"><?php echo get_acf_field('mission_community_power', 'Volunteers and partners work together to plant, protect, and nurture every site.'); ?></p>
          </div>
          <div class="rounded-3xl bg-white/95 border border-slate-200 p-8">
            <h3 class="font-serif text-2xl font-semibold text-foreground mb-3">Visible Progress</h3>
            <p class="text-foreground/70 leading-relaxed"><?php echo get_acf_field('mission_visible_progress', 'Every planting story is documented with images and milestones so supporters see real results.'); ?></p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Progress Section - Exact mirror of Next.js -->
    <section id="story" class="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20">
      <div class="max-w-5xl mx-auto text-center">
        <p class="text-forest-400 uppercase tracking-[0.3em] text-xs sm:text-sm mb-4">Featured Progress</p>
        <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-6">
          <?php echo get_acf_field('featured_progress_heading', 'How our first site grew from seedling to canopy'); ?>
        </h2>
        <p class="text-foreground/70 text-base sm:text-lg leading-relaxed">
          <?php echo get_acf_field('featured_progress_description', 'We track every planting with a clear timeline and visuals, so supporters can follow the journey from the first sapling to a thriving forest.'); ?>
        </p>
      </div>
    </section>

    <!-- Donation 3D Section - Exact mirror of Next.js -->
    <section id="donate" class="relative overflow-hidden py-20 sm:py-28 bg-background page-header-texture">
      <div class="absolute inset-0 leaf-pattern opacity-15 pointer-events-none"></div>
      <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-forest-50/90 to-transparent pointer-events-none"></div>

      <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-12">
        <div class="grid gap-10 xl:grid-cols-[1.1fr_0.9fr] items-center">
          <div class="space-y-6">
            <div class="inline-flex items-center gap-2 rounded-full bg-forest-100/70 px-4 py-2 text-sm font-semibold text-forest-700 shadow-sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
              Plant one more tree
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-foreground leading-tight">
              <?php echo get_acf_field('donation_heading', 'Give with confidence in a beautiful, interactive way'); ?>
            </h2>
            <p class="max-w-2xl text-base sm:text-lg text-foreground/70 leading-relaxed">
              <?php echo get_acf_field('donation_description', 'Explore each donation tier with a 3D preview card, then choose the amount that helps grow forests across Spain. This section is designed to feel engaging, warm, and effortless.'); ?>
            </p>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              <button
                onclick="selectDonationTier(1)"
                class="donation-tier-btn donation-tier-1 rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-forest-500 bg-forest-50 text-forest-900 shadow-medium"
                data-amount="<?php echo get_acf_field('donation_tier_1', '$25'); ?>"
              >
                <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                <p class="text-3xl font-semibold"><?php echo get_acf_field('donation_tier_1', '$25'); ?></p>
                <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
              </button>
              <button
                onclick="selectDonationTier(2)"
                class="donation-tier-btn donation-tier-2 rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50"
                data-amount="<?php echo get_acf_field('donation_tier_2', '$50'); ?>"
              >
                <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                <p class="text-3xl font-semibold"><?php echo get_acf_field('donation_tier_2', '$50'); ?></p>
                <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
              </button>
              <button
                onclick="selectDonationTier(3)"
                class="donation-tier-btn donation-tier-3 rounded-3xl border px-4 py-4 text-left transition-all duration-300 border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50"
                data-amount="<?php echo get_acf_field('donation_tier_3', '$100'); ?>"
              >
                <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                <p class="text-3xl font-semibold"><?php echo get_acf_field('donation_tier_3', '$100'); ?></p>
                <p class="text-sm text-foreground/60 mt-2">Supports tree planting, site care, and local communities.</p>
              </button>
            </div>
          </div>

          <div class="relative mx-auto w-full max-w-xl">
            <div
              id="donation-3d-card"
              class="relative cursor-grab overflow-hidden rounded-[40px] bg-gradient-to-br from-white via-forest-50 to-forest-100 p-6 shadow-elevated transition-transform duration-300"
              style="perspective: 1400px;"
            >
              <div
                id="donation-3d-inner"
                class="relative transform-gpu rounded-[32px] border border-slate-200 bg-white p-8 shadow-2xl transition-transform duration-300"
                style="transform: rotateX(0deg) rotateY(0deg);"
              >
                <div class="absolute inset-0 rounded-[32px] bg-gradient-to-br from-forest-100/60 to-white/90 opacity-80"></div>
                <div class="relative space-y-6">
                  <div class="flex items-center justify-between gap-4">
                    <div>
                      <p class="text-sm uppercase tracking-[0.2em] text-forest-600">Donation</p>
                      <h3 id="donation-amount" class="mt-2 text-2xl font-semibold text-foreground donation-amount"><?php echo get_acf_field('donation_tier_1', '$25'); ?> support</h3>
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
                      <p id="trees-planted" class="mt-2 text-3xl font-semibold text-forest-900 trees-planted"><?php echo (int)str_replace(['$', ',', '€'], '', get_acf_field('donation_tier_1', '$25')) * 3; ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4">
                      <p class="text-sm text-foreground/70">Community support</p>
                      <p id="volunteers" class="mt-2 text-3xl font-semibold text-foreground volunteers">+<?php echo round((int)str_replace(['$', ',', '€'], '', get_acf_field('donation_tier_1', '$25')) / 10); ?> volunteers</p>
                    </div>
                  </div>

                  <div class="rounded-3xl bg-forest-600/10 p-4 text-sm text-forest-700">
                    <p class="font-medium">Impact snapshot</p>
                    <p class="mt-2 leading-relaxed text-foreground/75">Your donation helps plant trees, improve soil health, and support local reforestation teams in Spain.</p>
                  </div>

                  <button id="donate-button" class="w-full rounded-3xl bg-forest-600 px-6 py-4 text-white font-semibold shadow-medium transition-all duration-300 hover:bg-forest-700 donate-button">
                    Donate <?php echo get_acf_field('donation_tier_1', '$25'); ?>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php
    // Optional Gutenberg editor blocks added by administrator on homepage
    while (have_posts()) : the_post();
      if (!empty(trim(get_the_content()))): ?>
        <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-12 xl:px-20 bg-background border-t border-earth-200">
          <div class="max-w-6xl mx-auto prose prose-lg prose-forest max-w-none">
            <?php the_content(); ?>
          </div>
        </section>
      <?php endif;
    endwhile;
    ?>
</main>

<?php
get_footer();
