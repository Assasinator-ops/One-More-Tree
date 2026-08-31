<?php
/**
 * Template Name: Volunteer Page
 *
 * The template for displaying the Volunteer page
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js VolunteerView -->
    <section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
        <div class="max-w-5xl mx-auto">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border border-slate-200 mb-6">
            <svg class="w-4 h-4 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
            </svg>
            <span class="text-xs sm:text-sm text-foreground font-semibold">Volunteer with OneMoreTree</span>
          </div>
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-4 leading-tight">
            <?php echo get_acf_field('volunteer_hero_title', 'Help restore forests, together'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl">
            <?php echo get_acf_field('volunteer_hero_subtitle', 'Sign up to receive local volunteering opportunities and updates.'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Volunteer Section -->
    <section class="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
      <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-10 items-start">
          <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-8 shadow-medium">
            <div class="font-serif text-2xl font-bold text-foreground mb-2">Your details</div>
            <div class="text-sm text-foreground/70 mb-6">
              We will only use your information to contact you about volunteering.
            </div>
            <form id="volunteer-form" class="space-y-4">
              <div>
                <label class="text-sm text-foreground/70">Name</label>
                <input
                  name="name"
                  required
                  class="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                  placeholder="Your name"
                />
              </div>
              <div>
                <label class="text-sm text-foreground/70">Email</label>
                <input
                  name="email"
                  type="email"
                  required
                  class="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                  placeholder="you@example.com"
                />
              </div>
              <div>
                <label class="text-sm text-foreground/70">Interest</label>
                <select
                  name="interest"
                  aria-label="Select volunteering interest"
                  class="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                >
                  <option value="Tree planting">Tree planting</option>
                  <option value="Community stewardship">Community stewardship</option>
                  <option value="Monitoring & data">Monitoring & data</option>
                  <option value="Education & outreach">Education & outreach</option>
                </select>
              </div>
              <button
                type="submit"
                class="w-full px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-elevated"
              >
                Request volunteering info
              </button>
            </form>
            <div id="volunteer-form-status" class="mt-4 rounded-2xl border border-earth-200 bg-earth-50 p-4 text-sm text-foreground/70 hidden"></div>
          </div>

          <div class="space-y-4">
            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <div class="font-serif text-xl font-bold text-foreground">Planting days</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">Join local teams for native tree planting events.</div>
            </div>

            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </div>
                <div class="font-serif text-xl font-bold text-foreground">Community stewardship</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">Help care for young forests and strengthen ownership.</div>
            </div>

            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <div class="font-serif text-xl font-bold text-foreground">Local coordination</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">We share nearby opportunities based on your interests.</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php
    // Optional Gutenberg editor blocks added by administrator on Volunteer page
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
