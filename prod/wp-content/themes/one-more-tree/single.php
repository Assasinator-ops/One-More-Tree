<?php
/**
 * The template for displaying all single posts
 *
 * @package One_More_Tree
 */

get_header();
?>

<!-- Reading Progress Bar -->
<div id="reading-progress-bar" class="fixed top-0 left-0 h-1 bg-forest-600 z-[70] transition-all duration-150 w-0"></div>

<div class="relative min-h-screen bg-background">
  <main class="relative">
    <?php while (have_posts()) : the_post(); 
      $cats = get_the_category();
      $cat_name = !empty($cats) ? $cats[0]->name : 'Ecology';
      $author_id = get_the_author_meta('ID');
    ?>
      <!-- Post Header Section -->
      <section class="relative pt-24 sm:pt-32 pb-12 sm:pb-16 px-4 sm:px-6 lg:px-12 xl:px-20 page-header-texture">
        <div class="max-w-4xl mx-auto">
          <!-- Back Link -->
          <a
            href="<?php echo esc_url(home_url('/blog')); ?>"
            class="inline-flex items-center gap-2 text-sm text-forest-700 hover:text-forest-800 font-semibold mb-8 group transition-colors"
          >
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to all stories
          </a>

          <div class="inline-block bg-forest-100 border border-forest-200 text-forest-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-6 uppercase tracking-wider">
            <?php echo esc_html($cat_name); ?>
          </div>

          <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-foreground mb-6 leading-tight">
            <?php the_title(); ?>
          </h1>

          <div class="flex flex-wrap items-center gap-6 pt-6 border-t border-earth-200 text-sm text-foreground/70">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-forest-100 flex items-center justify-center text-forest-700 font-serif font-bold text-lg">
                <?php echo substr(get_the_author(), 0, 1); ?>
              </div>
              <div>
                <div class="font-semibold text-foreground"><?php the_author(); ?></div>
                <div class="text-xs text-foreground/50">Field Research & Ecology</div>
              </div>
            </div>

            <div class="flex items-center gap-2 text-xs sm:text-sm text-foreground/60 ml-auto">
              <span><?php echo get_the_date('F j, Y'); ?></span>
              <span>•</span>
              <span>5 min read</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Image -->
      <section class="px-4 sm:px-6 lg:px-12 xl:px-20 mb-12 sm:mb-16">
        <div class="max-w-5xl mx-auto">
          <div class="relative aspect-[16/9] rounded-3xl overflow-hidden shadow-elevated">
            <?php if (has_post_thumbnail()): ?>
              <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
            <?php else: ?>
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hero-forest.jpg" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover" />
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- Article Body + Sidebar -->
      <section class="px-4 sm:px-6 lg:px-12 xl:px-20 pb-20 sm:pb-32">
        <div class="max-w-5xl mx-auto grid lg:grid-cols-[1fr_260px] gap-12 items-start">
          <!-- Main Content -->
          <article id="article-body" class="prose prose-lg prose-forest max-w-none text-foreground/80 leading-relaxed font-sans space-y-6">
            <?php the_content(); ?>
          </article>

          <!-- Sticky Sidebar / TOC & Share -->
          <aside class="sticky top-28 space-y-6 hidden lg:block">
            <div id="toc-box" class="glass-card rounded-2xl p-6 shadow-soft border border-earth-200">
              <h3 class="font-serif text-sm font-bold text-foreground mb-4 uppercase tracking-wider">In this article</h3>
              <nav id="toc-list" class="space-y-2 text-xs text-foreground/70"></nav>
            </div>

            <div class="glass-card rounded-2xl p-6 shadow-soft border border-earth-200">
              <h3 class="font-serif text-sm font-bold text-foreground mb-4 uppercase tracking-wider">Share Story</h3>
              <div class="flex gap-2">
                <a
                  href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-white hover:bg-forest-50 border border-earth-200 flex items-center justify-center text-foreground/70 hover:text-forest-600 transition-colors shadow-soft"
                  title="Share on Twitter"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
                <a
                  href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-white hover:bg-forest-50 border border-earth-200 flex items-center justify-center text-foreground/70 hover:text-forest-600 transition-colors shadow-soft"
                  title="Share on Facebook"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a
                  href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-10 h-10 rounded-xl bg-white hover:bg-forest-50 border border-earth-200 flex items-center justify-center text-foreground/70 hover:text-forest-600 transition-colors shadow-soft"
                  title="Share on LinkedIn"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </a>
                <button
                  id="copy-link-btn"
                  type="button"
                  class="w-10 h-10 rounded-xl bg-white hover:bg-forest-50 border border-earth-200 flex items-center justify-center text-foreground/70 hover:text-forest-600 transition-colors shadow-soft"
                  title="Copy link"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                </button>
              </div>
              <div id="copy-tooltip" class="text-xs text-forest-700 font-medium hidden mt-2">Link copied to clipboard!</div>
            </div>
          </aside>
        </div>

        <!-- Author Bio Box -->
        <div class="max-w-4xl mx-auto mt-16 glass-card rounded-3xl p-8 shadow-medium border border-earth-200 flex flex-col sm:flex-row items-center gap-6">
          <div class="w-20 h-20 rounded-2xl bg-forest-100 flex items-center justify-center text-forest-700 font-serif font-bold text-3xl flex-shrink-0">
            <?php echo substr(get_the_author(), 0, 1); ?>
          </div>
          <div>
            <div class="text-xs uppercase tracking-wider text-forest-600 font-semibold mb-1">Written by</div>
            <h3 class="font-serif text-xl font-bold text-foreground mb-2"><?php the_author(); ?></h3>
            <p class="text-sm text-foreground/70 leading-relaxed">
              <?php echo get_the_author_meta('description') ?: 'Ecologist and environmental researcher committed to native reforestation, biodiversity monitoring, and community-driven restoration projects across Spain.'; ?>
            </p>
          </div>
        </div>
      </section>
    <?php endwhile; ?>
  </main>
</div>

<?php
get_footer();
