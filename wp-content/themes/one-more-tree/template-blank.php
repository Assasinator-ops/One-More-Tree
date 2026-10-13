<?php
/**
 * Template Name: Blank / Landing Canvas (No Header or Footer)
 * Description: Clean, distraction-free edge-to-edge canvas without site header or footer. Perfect for landing pages, conversion campaigns, and custom full-page designs.
 *
 * @package One_More_Tree
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="h-full scroll-smooth">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class('min-h-full bg-background text-foreground font-sans antialiased selection:bg-forest-500 selection:text-white'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site relative min-h-screen overflow-x-hidden flex flex-col justify-between">
  <main id="primary" class="site-main relative w-full flex-grow">
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('w-full omt-block-canvas omt-blank-canvas'); ?>>
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </main>
</div>

<?php wp_footer(); ?>
</body>
</html>
