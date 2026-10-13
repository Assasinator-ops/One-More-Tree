<?php
/**
 * Template Name: Projects Page
 *
 * The template for displaying the projects page
 *
 * @package One_More_Tree
 */

get_header();

// Default location data matching Next.js source data
$default_locations = [
    [
        'id' => 'sierra-nevada',
        'name' => 'Sierra Nevada Restoration',
        'region' => 'Granada, Andalusia',
        'description' => 'Mountain forest restoration project',
        'coords' => ['x' => '35%', 'y' => '75%'],
        'treesPlanted' => 42500,
        'volunteers' => 850,
        'progressImages' => [
            [
                'title' => 'Project Start - Bare Ground',
                'description' => 'Initial area assessment and preparation',
                'image' => get_template_directory_uri() . '/assets/images/seedling.jpg',
                'date' => 'Jan 2023',
            ],
            [
                'title' => '3 Months Progress',
                'description' => 'Seedlings planted and first growth visible',
                'image' => get_template_directory_uri() . '/assets/images/sapling.jpg',
                'date' => 'Apr 2023',
            ],
            [
                'title' => '6 Months Progress',
                'description' => 'Strong root development and leaf growth',
                'image' => get_template_directory_uri() . '/assets/images/mature-tree.jpg',
                'date' => 'Jul 2023',
            ],
            [
                'title' => 'Current Status',
                'description' => 'Thriving young forest with 95% survival rate',
                'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg',
                'date' => 'May 2026',
            ],
        ],
    ],
    [
        'id' => 'madrid-urban',
        'name' => 'Madrid Urban Greening',
        'region' => 'Madrid, Central Spain',
        'description' => 'Urban forest and green space initiative',
        'coords' => ['x' => '48%', 'y' => '50%'],
        'treesPlanted' => 18900,
        'volunteers' => 320,
        'progressImages' => [
            [
                'title' => 'Site Preparation',
                'description' => 'Urban park layout and soil preparation',
                'image' => get_template_directory_uri() . '/assets/images/seedling.jpg',
                'date' => 'Feb 2024',
            ],
            [
                'title' => '2 Months In',
                'description' => 'Young trees installed and staked',
                'image' => get_template_directory_uri() . '/assets/images/sapling.jpg',
                'date' => 'Apr 2024',
            ],
            [
                'title' => 'Current Growth',
                'description' => 'Canopy development and community engagement',
                'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg',
                'date' => 'May 2026',
            ],
        ],
    ],
    [
        'id' => 'wildfire-recovery',
        'name' => 'Wildfire Recovery Program',
        'region' => 'Various Regions',
        'description' => 'Restoring fire-damaged forest areas',
        'coords' => ['x' => '42%', 'y' => '60%'],
        'treesPlanted' => 65200,
        'volunteers' => 1250,
        'progressImages' => [
            [
                'title' => 'Fire Damage Site',
                'description' => 'Assessing burn damage and recovery needs',
                'image' => get_template_directory_uri() . '/assets/images/seedling.jpg',
                'date' => 'Mar 2023',
            ],
            [
                'title' => 'Clearing & Preparation',
                'description' => 'Removing charred wood and preparing soil',
                'image' => get_template_directory_uri() . '/assets/images/sapling.jpg',
                'date' => 'Jun 2023',
            ],
            [
                'title' => 'Planting Phase',
                'description' => 'Installing native tree species',
                'image' => get_template_directory_uri() . '/assets/images/mature-tree.jpg',
                'date' => 'Sep 2023',
            ],
            [
                'title' => 'Recovery Progress',
                'description' => 'Forest regeneration underway with excellent growth',
                'image' => get_template_directory_uri() . '/assets/images/hero-forest.jpg',
                'date' => 'May 2026',
            ],
        ],
    ],
];

// Pull locations from admin options (set via wp-admin or /administrator console)
$admin_map = get_option('omt_admin_live_map');
$raw_locs = !empty($admin_map['locations']) ? $admin_map['locations'] : ($admin_map ?: []);
if (!empty($raw_locs) && is_array($raw_locs)) {
    // Merge admin data (name, region, description, treesPlanted, volunteers) over defaults
    $locations = array_map(function($admin_loc, $index) use ($default_locations) {
      $admin_loc = is_array($admin_loc) ? $admin_loc : [];
        $default = null;
        foreach ($default_locations as $d) {
        if (($d['id'] ?? '') === ($admin_loc['id'] ?? '') || ($d['name'] ?? '') === ($admin_loc['name'] ?? '')) {
                $default = $d;
                break;
            }
        }
      $location = array_merge($default ?? [], $admin_loc);
      $location['id'] = !empty($admin_loc['id'])
        ? sanitize_title($admin_loc['id'])
        : (sanitize_title($admin_loc['name'] ?? '') ?: 'location-' . ($index + 1));
      $location['coords'] = is_array($location['coords'] ?? null) ? $location['coords'] : ['x' => '50%', 'y' => '50%'];
      $location['coords']['x'] = $location['coords']['x'] ?? '50%';
      $location['coords']['y'] = $location['coords']['y'] ?? '50%';
      $location['progressImages'] = is_array($location['progressImages'] ?? null) ? $location['progressImages'] : [];
      return $location;
    }, $raw_locs, array_keys($raw_locs));
} else {
    $locations = $default_locations;
}

$totalTrees = array_reduce($locations, function($sum, $loc) { return $sum + ($loc['treesPlanted'] ?? 0); }, 0);
$totalVolunteers = array_reduce($locations, function($sum, $loc) { return $sum + ($loc['volunteers'] ?? 0); }, 0);
?>


<div class="relative bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js ProjectsView -->
    <section class="relative min-h-[60vh] flex items-center justify-center page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-100/90 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-20 sm:py-32">
        <div class="max-w-5xl mx-auto text-center">
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php echo get_acf_field('projects_hero_title', 'Our Projects Across Spain'); ?>
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            <?php echo get_acf_field('projects_hero_subtitle', 'Explore our active initiatives and see the impact we\'re making across Spain'); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- Projects Section - Live Map & Stats -->
    <section id="projects" class="relative w-full py-20 sm:py-32 overflow-hidden bg-background">
      <div class="absolute inset-0 leaf-pattern opacity-20"></div>
      <div class="absolute bottom-0 left-0 w-[620px] h-[620px] bg-forest-300/15 rounded-full blur-3xl"></div>

      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-14 sm:mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
              <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
              </svg>
              <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">
                <?php echo get_acf_field('projects_section_badge', 'Live Tree Map'); ?>
              </span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
              <?php echo get_acf_field('projects_section_headline', 'Track Trees in Real Time'); ?>
            </h2>
            <p class="text-base sm:text-lg text-foreground/70 max-w-2xl mx-auto">
              <?php echo get_acf_field('projects_section_description', 'Click markers to explore progress images, planting milestones, and real planting stories across Europe.'); ?>
            </p>
          </div>

          <div class="grid sm:grid-cols-2 gap-4 mb-10">
            <div class="rounded-3xl bg-white/90 border border-slate-200 p-6 text-center shadow-soft">
              <p class="text-sm text-foreground/60 uppercase tracking-[0.24em] mb-2">Trees tracked</p>
              <p class="text-4xl font-semibold text-forest-500"><?php echo number_format($totalTrees); ?></p>
            </div>
            <div class="rounded-3xl bg-white/90 border border-slate-200 p-6 text-center shadow-soft">
              <p class="text-sm text-foreground/60 uppercase tracking-[0.24em] mb-2">Local volunteers</p>
              <p class="text-4xl font-semibold text-forest-500"><?php echo number_format($totalVolunteers); ?></p>
            </div>
          </div>

          <!-- Interactive Spain Map component -->
          <div id="interactive-spain-map" class="relative w-full bg-gradient-to-br from-forest-50/90 to-background rounded-2xl overflow-hidden border border-slate-200/80 shadow-soft">
            <div
              class="relative w-full h-96 sm:h-[520px] bg-cover bg-center"
              style="background-image: linear-gradient(135deg, rgba(237, 249, 237, 0.85) 0%, rgba(229, 245, 232, 0.8) 100%), url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23f0f9ff%22 width=%22100%22 height=%22100%22/%3E%3Cpath fill=%22%231e5631%22 d=%22M25 20 L30 25 L28 35 L22 32 Z%22/%3E%3Cpath fill=%22%23e8f5e9%22 d=%22M35 30 L45 25 L50 40 L40 42 Z%22/%3E%3Cpath fill=%22%231e5631%22 d=%22M55 15 L70 20 L65 40 L50 35 Z%22/%3E%3C/svg%3E'); background-size: cover;"
            >
              <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice">
                <path d="M 20 25 L 35 20 L 40 18 L 45 20 L 50 18 L 55 22 L 60 20 L 65 25 L 68 30 L 70 35 L 68 40 L 70 45 L 65 48 L 60 50 L 55 52 L 50 55 L 45 54 L 40 56 L 35 58 L 30 60 L 25 58 L 22 55 L 20 50 L 18 45 L 20 40 L 18 35 L 20 30 Z" fill="none" stroke="rgba(255, 255, 255, 0.2)" stroke-width="0.5" />
              </svg>

              <div class="absolute inset-0 w-full h-full">
                <?php foreach ($locations as $loc): ?>
                  <button
                    type="button"
                    class="map-marker-btn absolute transform -translate-x-1/2 -translate-y-1/2 group transition-all duration-300 hover:scale-110"
                    style="left: <?php echo esc_attr($loc['coords']['x']); ?>; top: <?php echo esc_attr($loc['coords']['y']); ?>;"
                    data-location-id="<?php echo esc_attr($loc['id']); ?>"
                    title="<?php echo esc_attr($loc['name']); ?>"
                  >
                    <div class="absolute inset-0 w-8 h-8 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-forest-400 opacity-20 group-hover:opacity-40 transition-all duration-300 animate-pulse"></div>
                    <div class="relative w-8 h-8 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center">
                      <div class="absolute inset-0 bg-gradient-to-br from-forest-400 to-forest-600 rounded-full shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:from-forest-300 group-hover:to-forest-500"></div>
                      <svg class="w-4 h-4 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-20">
                      <div class="bg-white/95 border border-slate-200 rounded-lg p-2 sm:p-3 whitespace-nowrap text-xs sm:text-sm text-foreground shadow-soft">
                        <p class="font-semibold text-forest-700"><?php echo esc_html($loc['name']); ?></p>
                        <p class="text-foreground/70"><?php echo number_format($loc['treesPlanted']); ?> trees</p>
                      </div>
                      <div class="absolute top-full left-1/2 -translate-x-1/2 translate-y-px border-8 border-transparent border-t-white/95"></div>
                    </div>
                  </button>
                <?php endforeach; ?>
              </div>

              <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm border border-slate-200 rounded-lg p-3 sm:p-4">
                <p class="text-xs sm:text-sm text-foreground/85">
                  <span class="text-forest-700 font-semibold">💡 Tip:</span> Click on any location marker to see planting progress and image galleries
                </p>
              </div>
            </div>
          </div>

          <!-- Locations list below map -->
          <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <?php foreach ($locations as $loc): ?>
              <button
                type="button"
                class="location-card-btn bg-white/90 border border-slate-200 rounded-xl p-4 text-left hover:bg-white hover:border-forest-300/30 transition-all duration-300 group shadow-soft"
                data-location-id="<?php echo esc_attr($loc['id']); ?>"
              >
                <div class="flex items-start justify-between mb-2">
                  <div>
                    <h3 class="font-semibold text-foreground group-hover:text-forest-700 transition-colors duration-300">
                      <?php echo esc_html($loc['name']); ?>
                    </h3>
                    <p class="text-xs text-forest-600"><?php echo esc_html($loc['region']); ?></p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-forest-700"><?php echo number_format($loc['treesPlanted']); ?></p>
                    <p class="text-xs text-foreground/60">trees</p>
                  </div>
                </div>
                <p class="text-sm text-foreground/75"><?php echo esc_html($loc['description']); ?></p>
                <div class="mt-3 inline-block px-3 py-1 bg-forest-100 rounded-full text-xs text-forest-700 group-hover:bg-forest-200 transition-all duration-300">
                  View Progress →
                </div>
              </button>
            <?php endforeach; ?>
          </div>

          <div class="mt-10 text-center">
            <a
              href="<?php echo esc_url(home_url('/get-involved')); ?>"
              class="inline-block px-8 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated"
            >
              Join these projects
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Active Projects Grid -->
    <section class="py-20 sm:py-32 px-4 sm:px-6 lg:px-12 xl:px-20 bg-white/60">
      <div class="max-w-7xl mx-auto">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-foreground mb-12">Active Projects</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($locations as $project): ?>
            <div class="bg-white/95 border border-slate-200 rounded-2xl p-8 hover:bg-white transition-all duration-300 shadow-soft hover:shadow-medium">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h3 class="font-serif text-xl font-semibold text-foreground mb-1"><?php echo esc_html($project['name']); ?></h3>
                  <p class="text-sm text-forest-600"><?php echo esc_html($project['region']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-forest-100 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m0-18l-4 6h8l-4-6zm0 6l-6 8h12l-6-8z"></path>
                  </svg>
                </div>
              </div>
              <p class="text-foreground/70 text-sm mb-6"><?php echo esc_html($project['description']); ?></p>
              <div class="flex gap-6 pt-6 border-t border-slate-200">
                <div>
                  <p class="text-xs text-foreground/60 mb-1">Trees Planted</p>
                  <p class="font-serif text-2xl font-bold text-forest-600"><?php echo number_format($project['treesPlanted']); ?></p>
                </div>
                <div>
                  <p class="text-xs text-foreground/60 mb-1">Volunteers</p>
                  <p class="font-serif text-2xl font-bold text-forest-600"><?php echo number_format($project['volunteers']); ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Modal Image Gallery -->
    <div id="map-image-gallery-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
      <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-elevated relative">
        <div class="sticky top-0 bg-white/95 backdrop-blur-md p-6 border-b border-slate-200 flex items-start justify-between z-10">
          <div>
            <h2 id="modal-project-name" class="font-serif text-2xl font-bold text-foreground mb-1">Project Name</h2>
            <p id="modal-project-region" class="text-sm text-forest-700">Region</p>
          </div>
          <button id="modal-close-btn" class="p-2 hover:bg-slate-100 rounded-full transition-colors duration-300" aria-label="Close modal">
            <svg class="w-6 h-6 text-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-100 shadow-medium">
            <img id="modal-slide-image" src="" alt="Project progress" class="w-full h-full object-cover" />
            <button id="modal-prev-btn" class="absolute left-4 top-1/2 -translate-y-1/2 p-3 bg-white/90 hover:bg-white rounded-full shadow-lg transition-all" aria-label="Previous image">
              <svg class="w-5 h-5 text-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <button id="modal-next-btn" class="absolute right-4 top-1/2 -translate-y-1/2 p-3 bg-white/90 hover:bg-white rounded-full shadow-lg transition-all" aria-label="Next image">
              <svg class="w-5 h-5 text-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <div id="modal-slide-date" class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm">
              Date
            </div>
          </div>

          <div>
            <h3 id="modal-slide-title" class="font-serif text-xl font-bold text-foreground mb-2">Slide Title</h3>
            <p id="modal-slide-desc" class="text-foreground/70 text-sm leading-relaxed">Slide Description</p>
          </div>

          <!-- Thumbnails -->
          <div id="modal-thumbnails" class="grid grid-cols-4 gap-3"></div>
        </div>
      </div>
    </div>

    <script>
      window.ONE_MORE_TREE_LOCATIONS = <?php echo json_encode($locations); ?>;
    </script>

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
