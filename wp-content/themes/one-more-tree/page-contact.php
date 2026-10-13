<?php
/**
 * Template Name: Contact Page
 *
 * The template for displaying the Contact page
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js ContactView -->
    <section class="relative min-h-[55vh] flex items-center justify-center overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
        <div class="max-w-5xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-50 border border-slate-200 mb-6">
            <svg class="w-4 h-4 text-forest-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="text-xs sm:text-sm text-foreground font-semibold">Contact OneMoreTree</span>
          </div>
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-4 leading-tight">
            <?php echo get_acf_field('contact_hero_title', 'Let us connect'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('contact_hero_subtitle', 'Send a message and we will get back to you with guidance on volunteering, partnerships, or donations.'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Form & Info Grid -->
    <section class="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20">
      <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-8 sm:gap-10 items-start">
          <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-8 shadow-medium">
            <div class="font-serif text-2xl font-bold text-foreground mb-2">Send a message</div>
            <div class="text-sm text-foreground/70 mb-6">
              We respond as soon as possible. Thanks for supporting forest restoration.
            </div>

            <form id="contact-form-main" class="space-y-4">
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
                <label class="text-sm text-foreground/70">Message</label>
                <textarea
                  name="message"
                  required
                  rows="5"
                  class="mt-1 w-full px-4 py-3 rounded-xl bg-earth-50 text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none transition-colors"
                  placeholder="Tell us how we can help…"
                ></textarea>
              </div>

              <button
                type="submit"
                class="w-full px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-elevated"
              >
                Send message
              </button>
            </form>

            <div id="contact-form-status" class="mt-4 rounded-2xl border border-earth-200 bg-earth-50 p-4 text-sm text-foreground/70 hidden"></div>
          </div>

          <div class="space-y-4">
            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <div class="font-serif text-xl font-bold text-foreground">Address</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">
                <?php echo get_acf_field('contact_address', 'Calle Gran Vía 28, Madrid, Spain'); ?>
              </div>
            </div>

            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <div class="font-serif text-xl font-bold text-foreground">Email</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">
                <a href="mailto:info@onemoretree.eu" class="hover:text-forest-600 transition-colors">
                  <?php echo get_acf_field('contact_email', 'info@onemoretree.eu'); ?>
                </a>
              </div>
            </div>

            <div class="bg-white border border-earth-200 rounded-3xl p-6 sm:p-7 shadow-medium">
              <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <div class="font-serif text-xl font-bold text-foreground">Phone</div>
              </div>
              <div class="text-sm text-foreground/70 leading-relaxed">
                <?php echo get_acf_field('contact_phone', '+34 912 345 678'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <?php if (trim(get_the_content()) !== ''): ?>
        <section class="omt-custom-content relative w-full py-12 sm:py-16 px-4 sm:px-6 lg:px-12 xl:px-20 bg-background">
          <div class="max-w-7xl mx-auto">
            <?php the_content(); ?>
          </div>
        </section>
      <?php endif; ?>
    <?php endwhile; endif; ?>
  </main>
</div>

<?php
get_footer();
