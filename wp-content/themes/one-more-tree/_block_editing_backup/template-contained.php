<?php
/**
 * Template Name: Contained / Editorial Document
 * Description: Centered contained format for legal documentation, privacy policies, terms, and structured editorial articles.
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background">
  <main class="relative py-16 sm:py-24 px-4 sm:px-6 lg:px-12">
    <div class="max-w-3xl mx-auto">
      <?php while (have_posts()) : the_post(); ?>
        <header class="mb-10 text-center">
          <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground mb-4 leading-tight">
            <?php the_title(); ?>
          </h1>
          <p class="text-xs sm:text-sm text-foreground/50">
            Last updated: <?php echo get_the_modified_date('F j, Y'); ?>
          </p>
        </header>

        <article id="post-<?php the_ID(); ?>" <?php post_class('prose prose-lg prose-forest max-w-none bg-white p-8 sm:p-12 rounded-3xl border border-earth-200 shadow-soft'); ?>>
          <?php the_content(); ?>
        </article>
      <?php endwhile; ?>
    </div>
  </main>
</div>

<?php
get_footer();
