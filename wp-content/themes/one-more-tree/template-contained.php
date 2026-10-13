<?php
/**
 * Template Name: Contained / Editorial Canvas
 * Description: Centered contained format for legal documentation, articles, and structured content.
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background text-foreground">
  <main id="primary" class="site-main relative py-16 sm:py-24 px-4 sm:px-6 lg:px-12">
    <div class="max-w-4xl mx-auto">
      <?php while (have_posts()) : the_post(); ?>
        <header class="mb-10 text-center">
          <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
            <?php the_title(); ?>
          </h1>
          <?php if (has_excerpt()): ?>
            <p class="text-base sm:text-lg text-foreground/70 max-w-2xl mx-auto">
              <?php echo get_the_excerpt(); ?>
            </p>
          <?php endif; ?>
        </header>

        <article id="post-<?php the_ID(); ?>" <?php post_class('prose prose-lg prose-forest max-w-none bg-white p-8 sm:p-12 rounded-3xl border border-earth-200 shadow-soft omt-block-contained'); ?>>
          <?php the_content(); ?>
        </article>
      <?php endwhile; ?>
    </div>
  </main>
</div>

<?php
get_footer();
