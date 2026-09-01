<?php
/**
 * Template Name: Full Width (Block Canvas)
 * Description: Edge-to-edge canvas template optimized for Gutenberg full-width blocks, cover images, and landing page layouts.
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background">
  <main class="relative w-full">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('w-full'); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </main>
</div>

<?php
get_footer();
