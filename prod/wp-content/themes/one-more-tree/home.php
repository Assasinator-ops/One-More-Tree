<?php
/**
 * The main template file / Blog Archive
 *
 * @package One_More_Tree
 */

get_header();

$categories = ['All', 'Ecology', 'Field Report', 'Urban Ecology', 'Community', 'Technology', 'Climate Science'];
?>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <!-- Header Section - Exact mirror of Next.js BlogView -->
    <section class="relative min-h-[50vh] flex items-center justify-center py-20 sm:py-32 overflow-hidden page-header-texture">
      <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
      <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
      <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="max-w-5xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-forest-100 border border-forest-200 mb-6">
            <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">Stories & Science</span>
          </div>
          <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
            Our Blog & Stories
          </h1>
          <p class="text-lg sm:text-xl text-foreground/70 max-w-3xl mx-auto">
            Insights, field reports, and updates on forest restoration across Spain
          </p>
        </div>
      </div>
    </section>

    <!-- Search & Category Filters -->
    <section class="py-12 px-4 sm:px-6 lg:px-12 xl:px-20 bg-background">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12">
          <!-- Search input -->
          <div class="relative w-full md:w-80">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-foreground/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              id="blog-search-input"
              type="text"
              placeholder="Search articles…"
              class="w-full pl-11 pr-4 py-3 rounded-full bg-white text-foreground placeholder-foreground/40 border border-earth-200 focus:border-forest-500 focus:outline-none focus:ring-2 focus:ring-forest-200 transition-colors shadow-soft text-sm"
            />
          </div>

          <!-- Category filter buttons -->
          <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            <?php foreach ($categories as $index => $cat): ?>
              <button
                type="button"
                class="blog-cat-filter px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-300 <?php echo $index === 0 ? 'bg-forest-600 text-white shadow-medium' : 'bg-white text-foreground/70 hover:bg-forest-50 hover:text-forest-700 border border-earth-200'; ?>"
                data-category="<?php echo esc_attr($cat); ?>"
              >
                <?php echo esc_html($cat); ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Blog Posts Grid -->
        <div id="blog-posts-grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); 
              $cats = get_the_category();
              $cat_name = !empty($cats) ? $cats[0]->name : 'Ecology';
              $read_time = '4 min read';
            ?>
              <article class="blog-post-card bg-white border border-earth-200 rounded-3xl overflow-hidden hover:shadow-elevated transition-all duration-300 flex flex-col group" data-category="<?php echo esc_attr($cat_name); ?>">
                <a href="<?php the_permalink(); ?>" class="relative aspect-[16/10] overflow-hidden block">
                  <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', ['class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105']); ?>
                  <?php else: ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-forest.jpg" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                  <?php endif; ?>
                  <div class="absolute top-4 left-4 bg-forest-900/80 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">
                    <?php echo esc_html($cat_name); ?>
                  </div>
                </a>

                <div class="p-6 sm:p-7 flex flex-col flex-1">
                  <div class="flex items-center gap-3 text-xs text-foreground/50 mb-3">
                    <span><?php echo get_the_date('M j, Y'); ?></span>
                    <span>•</span>
                    <span><?php echo esc_html($read_time); ?></span>
                  </div>

                  <h2 class="font-serif text-xl sm:text-2xl font-bold text-foreground mb-3 leading-snug group-hover:text-forest-600 transition-colors">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>

                  <div class="text-sm text-foreground/70 leading-relaxed mb-6 flex-1">
                    <?php the_excerpt(); ?>
                  </div>

                  <div class="flex items-center justify-between pt-4 border-t border-earth-200 text-xs">
                    <div class="flex items-center gap-2 text-foreground/70 font-medium">
                      <div class="w-6 h-6 rounded-full bg-forest-100 flex items-center justify-center text-forest-700 font-bold">
                        <?php echo substr(get_the_author(), 0, 1); ?>
                      </div>
                      <span><?php the_author(); ?></span>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="text-forest-600 font-semibold group-hover:text-forest-700 transition-colors flex items-center gap-1">
                      Read Article
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                      </svg>
                    </a>
                  </div>
                </div>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <!-- Fallback Mock Post if no WP posts exist yet -->
            <article class="blog-post-card bg-white border border-earth-200 rounded-3xl overflow-hidden hover:shadow-elevated transition-all duration-300 flex flex-col group" data-category="Ecology">
              <a href="#" class="relative aspect-[16/10] overflow-hidden block">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-forest.jpg" alt="How Native Forests Resist Wildfires" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div class="absolute top-4 left-4 bg-forest-900/80 text-white text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">
                  Ecology
                </div>
              </a>

              <div class="p-6 sm:p-7 flex flex-col flex-1">
                <div class="flex items-center gap-3 text-xs text-foreground/50 mb-3">
                  <span>Jul 1, 2026</span>
                  <span>•</span>
                  <span>5 min read</span>
                </div>

                <h2 class="font-serif text-xl sm:text-2xl font-bold text-foreground mb-3 leading-snug group-hover:text-forest-600 transition-colors">
                  How Native Oak and Pine Forests Resist Wildfires in Southern Spain
                </h2>

                <p class="text-sm text-foreground/70 leading-relaxed mb-6 flex-1">
                  Discover how biodiversity and native tree spacing create resilient natural firebreaks while restoring depleted soils.
                </p>

                <div class="flex items-center justify-between pt-4 border-t border-earth-200 text-xs">
                  <div class="flex items-center gap-2 text-foreground/70 font-medium">
                    <div class="w-6 h-6 rounded-full bg-forest-100 flex items-center justify-center text-forest-700 font-bold">
                      E
                    </div>
                    <span>Elena Morales</span>
                  </div>

                  <span class="text-forest-600 font-semibold flex items-center gap-1">
                    Read Article →
                  </span>
                </div>
              </div>
            </article>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-16 flex justify-center">
          <?php
          the_posts_pagination([
              'prev_text' => '← Previous',
              'next_text' => 'Next →',
              'class'     => 'flex gap-2 items-center text-sm font-semibold',
          ]);
          ?>
        </div>
      </div>
    </section>
  </main>
</div>

<?php
get_footer();
