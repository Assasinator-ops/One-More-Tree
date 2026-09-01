<?php
/**
 * Template Name: Donate Page
 *
 * The template for displaying the Donate page
 *
 * @package One_More_Tree
 */

get_header();
?>

<?php
$admin_tiers_data = get_option('omt_admin_donation_tiers', []);
$donation_tiers = !empty($admin_tiers_data['tiers']) ? $admin_tiers_data['tiers'] : [
    ['amount' => 25, 'title' => '$25 support', 'description' => 'Supports tree planting, site care, and local communities.', 'trees' => 75, 'volunteers' => 3],
    ['amount' => 50, 'title' => '$50 support', 'description' => 'Expands native tree coverage and regional biodiversity.', 'trees' => 150, 'volunteers' => 5],
    ['amount' => 100, 'title' => '$100 support', 'description' => 'Funds full plot restoration, native saplings, and 5-year monitoring.', 'trees' => 300, 'volunteers' => 10],
];
?>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <!-- Header Section -->
    <section class="relative min-h-[50vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-5xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-100 border border-forest-200 mb-6">
            <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
              <?php echo get_acf_field('donate_hero_badge', 'Direct Donation'); ?>
            </span>
          </div>
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('donate_hero_title', 'Support Reforestation'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('donate_hero_subtitle', 'Every donation directly funds the planting, irrigation, and protection of native forests in Spain.'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Donation 3D Section - Exact mirror of Next.js Donation3DSection.tsx -->
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
              <?php foreach ($donation_tiers as $idx => $tier): 
                $is_active = $idx === 0;
                $t_amount = esc_attr($tier['amount'] ?? 25);
                $t_title  = esc_attr($tier['title'] ?? '$' . $t_amount . ' support');
                $t_trees  = esc_attr($tier['trees'] ?? ($t_amount * 3));
                $t_vols   = esc_attr($tier['volunteers'] ?? round($t_amount / 10));
                $t_desc   = esc_html($tier['description'] ?? 'Supports tree planting, site care, and local communities.');
              ?>
              <button
                class="tier-select-btn rounded-3xl border px-4 py-4 text-left transition-all duration-300 <?php echo $is_active ? 'border-forest-500 bg-forest-50 text-forest-900 shadow-medium' : 'border-slate-200 bg-white text-foreground hover:border-forest-300 hover:bg-forest-50'; ?>"
                data-amount="<?php echo $t_amount; ?>"
                data-title="<?php echo $t_title; ?>"
                data-trees="<?php echo $t_trees; ?>"
                data-volunteers="<?php echo $t_vols; ?>"
              >
                <p class="text-sm uppercase tracking-[0.18em] text-forest-600 mb-2">Tier</p>
                <p class="text-3xl font-semibold">$<?php echo $t_amount; ?></p>
                <p class="text-sm text-foreground/60 mt-2"><?php echo $t_desc; ?></p>
              </button>
              <?php endforeach; ?>
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
                      <h3 class="donation-card-amount mt-2 text-2xl font-semibold text-foreground"><?php echo esc_html($donation_tiers[0]['title'] ?? '$25 support'); ?></h3>
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
                      <p class="donation-trees-count mt-2 text-3xl font-semibold text-forest-900"><?php echo esc_html($donation_tiers[0]['trees'] ?? '75'); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4">
                      <p class="text-sm text-foreground/70">Community support</p>
                      <p class="donation-volunteers-count mt-2 text-3xl font-semibold text-foreground">+<?php echo esc_html($donation_tiers[0]['volunteers'] ?? '3'); ?> volunteers</p>
                    </div>
                  </div>

                  <div class="rounded-3xl bg-forest-600/10 p-4 text-sm text-forest-700">
                    <p class="font-medium">Impact snapshot</p>
                    <p class="mt-2 leading-relaxed text-foreground/75"><?php echo get_acf_field('donation_tax_note', 'Your donation helps plant trees, improve soil health, and support local reforestation teams in Spain.'); ?></p>
                  </div>

                  <button class="donation-submit-btn w-full rounded-3xl bg-forest-600 px-6 py-4 text-white font-semibold shadow-medium transition-all duration-300 hover:bg-forest-700">
                    Donate $<?php echo esc_html($donation_tiers[0]['amount'] ?? '25'); ?>
                  </button>
                </div>
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

