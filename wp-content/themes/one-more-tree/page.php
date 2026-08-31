<?php
/**
 * The template for displaying all default WordPress pages
 *
 * Provides a modern, responsive layout with full Gutenberg block support
 * matching the Next.js visual design system.
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <?php while (have_posts()) : the_post(); ?>
      <!-- Header Banner Section -->
      <section class="relative min-h-[35vh] sm:min-h-[45vh] flex items-center justify-center py-16 sm:py-24 overflow-hidden page-header-texture">
        <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
        <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>
        
        <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 text-center">
          <div class="max-w-4xl mx-auto">
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-4 leading-tight">
              <?php the_title(); ?>
            </h1>
            <?php if (has_excerpt()): ?>
              <p class="text-base sm:text-lg text-foreground/70 max-w-2xl mx-auto leading-relaxed">
                <?php echo get_the_excerpt(); ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <?php if (has_post_thumbnail()): ?>
        <!-- Featured Image Banner -->
        <section class="px-4 sm:px-6 lg:px-12 xl:px-20 mb-12 sm:mb-16 -mt-8 relative z-20">
          <div class="max-w-5xl mx-auto">
            <div class="relative aspect-[16/9] rounded-3xl overflow-hidden shadow-elevated border border-earth-200">
              <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <!-- Gutenberg Content Canvas -->
      <section class="px-4 sm:px-6 lg:px-12 xl:px-20 pb-20 sm:pb-32">
        <div class="max-w-4xl mx-auto">
          <article id="post-<?php the_ID(); ?>" <?php post_class('prose prose-lg prose-forest max-w-none text-foreground/80 leading-relaxed font-sans'); ?>>
            <?php the_content(); ?>
          </article>
        </div>
      </section>
    <?php endwhile; ?>
  </main>
</div>

<?php
get_footer();
