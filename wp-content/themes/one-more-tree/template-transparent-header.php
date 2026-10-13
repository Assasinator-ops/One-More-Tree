<?php
/**
 * Template Name: Full Width (Transparent Header Canvas)
 * Description: Edge-to-edge canvas where the site header floats transparently over the first Gutenberg block (ideal for hero sections with background images/videos).
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-screen bg-background text-foreground -mt-24 sm:-mt-28">
  <main id="primary" class="site-main relative w-full overflow-hidden">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('w-full omt-block-canvas omt-transparent-header-canvas'); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </main>
</div>

<?php
get_footer();
