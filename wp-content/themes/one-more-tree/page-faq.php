<?php
/**
 * Template Name: FAQ Page
 *
 * The template for displaying the FAQ page
 *
 * @package One_More_Tree
 */

get_header();

// Pull FAQ items from admin settings (saved via wp-admin or /administrator console)
$admin_faq = get_option('omt_admin_faq');
$raw_faqs = !empty($admin_faq['faqs']) ? $admin_faq['faqs'] : ($admin_faq ?: []);
$faqs = (!empty($raw_faqs) && is_array($raw_faqs))
    ? $raw_faqs
    : [
        [
            'q' => 'Do you plant native species only?',
            'a' => 'Yes. We focus on native species suited to the local ecosystem to support biodiversity and long-term survival.',
        ],
        [
            'q' => 'How do you measure impact?',
            'a' => 'We track survival rates and development over time, and we publish updates so partners and supporters can see progress.',
        ],
        [
            'q' => 'Can communities participate?',
            'a' => 'Absolutely. We work with local leaders and training partners to support planting, maintenance, and stewardship.',
        ],
        [
            'q' => 'How can I get involved?',
            'a' => 'Donate, volunteer, or partner with us. Use the "Join the Movement" section to take your next step.',
        ],
        [
            'q' => 'Are donations tax-deductible?',
            'a' => 'Yes, OneMoreTree is a registered non-profit organization. Tax certificates and receipts are available upon request.',
        ],
        [
            'q' => 'Where are your planting locations located in Spain?',
            'a' => 'We have active planting and restoration sites across Sierra Nevada in Granada, Madrid Urban greening corridors, wildfire recovery zones in Valencia, and along the Camino de Santiago route.',
        ],
    ];
?>


<div class="relative bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js FAQView -->
    <section class="relative min-h-[50vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-5xl mx-auto text-center">
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('faq_hero_title', 'Frequently Asked Questions'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('faq_hero_subtitle', 'Find answers to common questions about our mission and how to get involved'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- FAQ Section - Exact mirror of Next.js FAQSection -->
    <section id="faq" class="relative min-h-[60vh] w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute top-0 left-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('faq_section_badge', 'FAQ'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              <?php echo get_acf_field('faq_section_headline', 'Questions, answered'); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              <?php echo get_acf_field('faq_section_description', 'Clear answers about how we plant, measure impact, and partner with communities.'); ?>
            </p>
          </div>

          <div class="grid lg:grid-cols-2 gap-6 sm:gap-8 items-start">
            <!-- Accordion Box -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-medium divide-y divide-earth-200">
              <?php foreach ($faqs as $index => $faq): ?>
                <div class="faq-accordion-item py-4 first:pt-0 last:pb-0">
                  <button
                    type="button"
                    class="faq-trigger w-full flex items-center justify-between gap-4 text-left font-serif text-lg font-semibold text-foreground hover:text-forest-600 transition-colors py-2"
                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                  >
                    <span><?php echo esc_html($faq['q']); ?></span>
                    <svg class="faq-icon w-5 h-5 text-forest-600 transform transition-transform duration-300 flex-shrink-0 <?php echo $index === 0 ? 'rotate-180' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>
                  <div class="faq-content text-foreground/70 text-sm sm:text-base leading-relaxed pt-2 <?php echo $index === 0 ? '' : 'hidden'; ?>">
                    <p><?php echo esc_html($faq['a']); ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Still Have Questions Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-medium">
              <div class="font-serif text-2xl font-bold text-foreground mb-2">Still have questions?</div>
              <p class="text-sm text-foreground/60 mb-6">
                Reach out and we will help you find the best way to support restoration.
              </p>
              <a
                href="<?php echo esc_url(home_url('/contact')); ?>"
                class="inline-flex items-center justify-center w-full px-6 py-3.5 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-medium hover:shadow-elevated text-center"
              >
                Contact us via Join Us
              </a>
              <div class="mt-5 text-xs text-foreground/40 text-center">
                Tip: donate, volunteer, or partner to make an immediate impact.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<?php
get_footer();
