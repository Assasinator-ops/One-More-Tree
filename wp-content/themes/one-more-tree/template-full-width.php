<?php
/**
 * Template Name: Full Width (Block Canvas)
 * Description: Edge-to-edge canvas template optimized for Gutenberg full-width blocks, hero patterns, and drag-and-drop page building.
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background text-foreground">
  <main id="primary" class="site-main relative w-full overflow-hidden">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('w-full omt-block-canvas'); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </main>
</div>

<?php
get_footer();
