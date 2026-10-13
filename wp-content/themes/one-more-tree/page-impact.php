<?php
/**
 * Template Name: Impact Page
 *
 * The template for displaying the impact page
 *
 * @package One_More_Tree
 */

get_header();

// Pull ALL impact data from unified admin options
$admin_kpis        = get_option('omt_admin_impact_kpis', []);
$admin_goal        = get_option('omt_admin_impact_goal', []);
$admin_stories     = get_option('omt_admin_impact_stories', get_option('omt_admin_stories', []));
$admin_counters    = get_option('omt_admin_home_counters', []);

// Goal progress (from omt_admin_impact_goal)
$goal_title        = !empty($admin_goal['title'])          ? $admin_goal['title']          : '1,000,000 Trees by 2040';
$goal_target       = !empty($admin_goal['target'])         ? intval($admin_goal['target'])  : 1000000;
$goal_current      = !empty($admin_goal['current'])        ? intval($admin_goal['current']) : 847000;
$goal_remaining    = !empty($admin_goal['remaining_note']) ? $admin_goal['remaining_note']  : number_format($goal_target - $goal_current) . ' trees left to plant';
$goal_pct          = min(100, round(($goal_current / max(1, $goal_target)) * 100, 1));

// KPI stats
$kpis = !empty($admin_kpis['kpis']) ? $admin_kpis['kpis'] : [
    ['label' => 'Regions Covered',    'value' => '4',     'sub' => 'Granada, Madrid, Valencia, Galicia'],
    ['label' => 'Projects Active',    'value' => '12',    'sub' => 'Active forest & urban sites'],
    ['label' => 'Tons CO2 Absorbed',  'value' => '4,850', 'sub' => 'Estimated lifetime absorption'],
    ['label' => 'Volunteers Engaged', 'value' => '2,420', 'sub' => 'Community participants'],
];

// Story chapters
$story_seed   = !empty($admin_stories['seed'])   ? $admin_stories['seed']   : [];
$story_growth = !empty($admin_stories['growth']) ? $admin_stories['growth'] : [];
$story_mature = !empty($admin_stories['mature']) ? $admin_stories['mature'] : [];
?>



<div class="relative bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js ImpactView -->
    <section class="relative min-h-[60vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-5xl mx-auto text-center">
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('impact_hero_title', 'Our Impact'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('impact_hero_subtitle', 'See how our trees grow and the forests we\'re restoring across Spain'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Impact KPI Section - Exact mirror of Next.js Impact.tsx -->
    <section id="impact-stats" class="relative w-full py-20 sm:py-32 overflow-hidden bg-background">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute top-0 right-0 w-[520px] h-[520px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('impact_section_badge', 'Spain Reforestation Progress'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 sm:mb-6 leading-tight">
              <?php echo get_acf_field('impact_headline', 'The Numbers Tell Our Story'); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/60 max-w-2xl mx-auto">
              <?php echo get_acf_field('impact_description', 'Every tree planted is tracked, verified, and nurtured for long-term survival across ecosystems in Spain.'); ?>
            </p>
          </div>

          <!-- Progress Bar Card -->
          <div class="glass-card rounded-3xl p-6 sm:p-10 mb-12 shadow-medium border border-earth-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
              <div>
                <h3 class="font-serif text-xl sm:text-2xl font-bold text-foreground">
                  <?php echo esc_html($goal_title); ?>
                </h3>
                <p class="text-sm text-foreground/60">
                  <?php echo esc_html($goal_remaining); ?>
                </p>
              </div>
              <span class="text-2xl sm:text-3xl font-bold text-forest-600 font-serif">
                <?php echo esc_html($goal_pct); ?>% Complete
              </span>
            </div>
            <div class="w-full bg-slate-200 h-4 rounded-full overflow-hidden">
              <div class="bg-gradient-to-r from-forest-500 to-forest-600 h-full rounded-full transition-all duration-1000" style="width: <?php echo esc_attr($goal_pct); ?>%"></div>
            </div>
          </div>

          <!-- 4 KPI Cards -->
          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php 
            $icons = [
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>',
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
            ];
            foreach ($kpis as $idx => $kpi): 
                $icon_svg = $icons[$idx % count($icons)];
            ?>
            <div class="glass-card rounded-3xl p-6 sm:p-8 text-center shadow-soft hover:shadow-medium transition-all">
              <div class="w-12 h-12 rounded-2xl bg-forest-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <?php echo $icon_svg; ?>
                </svg>
              </div>
              <div class="font-serif text-3xl sm:text-4xl font-bold text-forest-600 mb-2">
                <?php echo esc_html($kpi['value'] ?? ''); ?>
              </div>
              <div class="text-sm font-semibold text-foreground mb-1">
                <?php echo esc_html($kpi['label'] ?? ''); ?>
              </div>
              <div class="text-xs text-foreground/50">
                <?php echo esc_html($kpi['sub'] ?? ''); ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

        </div>
      </div>
    </section>

    <!-- Story Chapter One: Seed - Exact mirror of Next.js StorySeed.tsx -->
    <section id="chapter-seed" class="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-30"></div>
      <div class="absolute top-1/4 -left-32 w-64 h-64 bg-forest-300/20 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center max-w-7xl mx-auto">
          <div class="relative order-2 lg:order-1">
            <div class="relative aspect-[3/4] rounded-3xl overflow-hidden shadow-elevated">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/seedling.jpg" alt="A seedling sprouting from soil" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent"></div>
            </div>
            <div class="absolute -bottom-8 -right-4 sm:-bottom-12 sm:-right-6 p-6 sm:p-8 rounded-2xl glass-card group hover:scale-105 transition-all duration-300 shadow-2xl z-10">
              <div class="flex items-center gap-4 mb-3">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-colorful.png" alt="NGO Logo" class="h-16 w-16 sm:h-20 sm:w-20 flex-shrink-0 drop-shadow-2xl" />
                <div>
                  <div class="text-3xl sm:text-4xl font-serif font-bold text-forest-600">Day 1</div>
                </div>
              </div>
              <div class="text-base font-medium text-foreground/80">The beginning</div>
            </div>
          </div>

          <div class="order-1 lg:order-2">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('story_seed_chapter_label', 'Chapter One'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 sm:mb-8 leading-tight">
              <?php echo get_acf_field('story_seed_headline', 'It Starts With a'); ?>
              <span class="text-forest-700 italic block py-3"><?php echo get_acf_field('story_seed_emphasis', 'Single Seed'); ?></span>
            </h2>
            <div class="space-y-4 sm:space-y-6 text-foreground/70 mb-8">
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_seed_desc_1', 'Beneath the soil, magic happens. A tiny seed, no larger than a grain of rice, holds within it the blueprint for a towering forest giant.'); ?>
              </p>
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_seed_desc_2', 'With patience, care, and the right conditions, life emerges. This is where our journey begins.'); ?>
              </p>
            </div>
            <div class="flex flex-wrap gap-4">
              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Water</div>
                  <div class="text-xs text-foreground/50">Life source</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Sunlight</div>
                  <div class="text-xs text-foreground/50">Energy</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Care</div>
                  <div class="text-xs text-foreground/50">Nurturing</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Story Chapter Two: Growth - Exact mirror of Next.js StoryGrowth.tsx -->
    <section id="chapter-growth" class="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-30"></div>
      <div class="absolute top-1/4 -right-32 w-64 h-64 bg-forest-300/20 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center max-w-7xl mx-auto">
          <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('story_growth_chapter_label', 'Chapter Two'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 sm:mb-8 leading-tight">
              <?php echo get_acf_field('story_growth_headline', 'Growing Through'); ?>
              <span class="text-forest-700 italic block py-3"><?php echo get_acf_field('story_growth_emphasis', 'Adversity'); ?></span>
            </h2>
            <div class="space-y-4 sm:space-y-6 text-foreground/70 mb-8">
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_growth_desc_1', 'Young saplings brave harsh winds, scorching summers, and winter frost. In community, roots intertwine beneath the surface, sharing nutrients and strength.'); ?>
              </p>
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_growth_desc_2', 'Careful irrigation and local stewardship protect vulnerable seedlings during critical growth years.'); ?>
              </p>
            </div>
            <div class="flex flex-wrap gap-4">
              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Wind</div>
                  <div class="text-xs text-foreground/50">Building strength</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Soil</div>
                  <div class="text-xs text-foreground/50">Root foundation</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Resilience</div>
                  <div class="text-xs text-foreground/50">Community care</div>
                </div>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="relative aspect-[3/4] rounded-3xl overflow-hidden shadow-elevated">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/sapling.jpg" alt="A young sapling growing strong" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent"></div>
            </div>
            <div class="absolute -bottom-8 -left-4 sm:-bottom-12 sm:-left-6 p-6 sm:p-8 rounded-2xl glass-card group hover:scale-105 transition-all duration-300 shadow-2xl z-10">
              <div class="flex items-center gap-4 mb-3">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-colorful.png" alt="NGO Logo" class="h-16 w-16 sm:h-20 sm:w-20 flex-shrink-0 drop-shadow-2xl" />
                <div>
                  <div class="text-3xl sm:text-4xl font-serif font-bold text-forest-600">Year 2</div>
                </div>
              </div>
              <div class="text-base font-medium text-foreground/80">Taking root</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Story Chapter Three: Mature - Exact mirror of Next.js StoryMature.tsx -->
    <section id="chapter-mature" class="relative min-h-screen w-full py-20 sm:py-32 overflow-hidden gradient-bg">
      <div class="absolute inset-0 leaf-pattern opacity-30"></div>
      <div class="absolute top-1/4 -left-32 w-64 h-64 bg-forest-300/20 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center max-w-7xl mx-auto">
          <div class="relative order-2 lg:order-1">
            <div class="relative aspect-[3/4] rounded-3xl overflow-hidden shadow-elevated">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mature-tree.jpg" alt="A towering mature oak tree" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent"></div>
            </div>
            <div class="absolute -bottom-8 -right-4 sm:-bottom-12 sm:-right-6 p-6 sm:p-8 rounded-2xl glass-card group hover:scale-105 transition-all duration-300 shadow-2xl z-10">
              <div class="flex items-center gap-4 mb-3">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-colorful.png" alt="NGO Logo" class="h-16 w-16 sm:h-20 sm:w-20 flex-shrink-0 drop-shadow-2xl" />
                <div>
                  <div class="text-3xl sm:text-4xl font-serif font-bold text-forest-600">Year 10+</div>
                </div>
              </div>
              <div class="text-base font-medium text-foreground/80">Thriving Forest</div>
            </div>
          </div>

          <div class="order-1 lg:order-2">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('story_mature_chapter_label', 'Chapter Three'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 sm:mb-8 leading-tight">
              <?php echo get_acf_field('story_mature_headline', 'A Legacy That'); ?>
              <span class="text-forest-700 italic block py-3"><?php echo get_acf_field('story_mature_emphasis', 'Lives On'); ?></span>
            </h2>
            <div class="space-y-4 sm:space-y-6 text-foreground/70 mb-8">
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_mature_desc_1', 'Decades later, a thriving canopy shelters diverse wildlife, enriches soil, and cools local microclimates.'); ?>
              </p>
              <p class="text-base sm:text-lg leading-relaxed">
                <?php echo get_acf_field('story_mature_desc_2', 'Restored woodlands become gathering grounds, teaching centers, and carbon sinks for generations to come.'); ?>
              </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Wildlife</div>
                  <div class="text-xs text-foreground/50">Sanctuary</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Canopy</div>
                  <div class="text-xs text-foreground/50">Cooling</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Community</div>
                  <div class="text-xs text-foreground/50">Heritage</div>
                </div>
              </div>

              <div class="flex items-center gap-3 px-4 py-3 rounded-xl glass-card">
                <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-semibold text-foreground">Future</div>
                  <div class="text-xs text-foreground/50">Carbon capture</div>
                </div>
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
