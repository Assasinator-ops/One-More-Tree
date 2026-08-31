<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container mx-auto px-4 py-8">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="text-4xl font-serif mb-4"><?php the_title(); ?></h1>
                    <div class="prose max-w-none">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <p><?php _e('Sorry, no posts matched your criteria.', 'one-more-tree'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
