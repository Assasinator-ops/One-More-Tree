<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package One_More_Tree
 */

get_header();
?>

<div class="relative min-h-[80vh] flex items-center justify-center bg-background py-20 sm:py-32 overflow-hidden page-header-texture">
  <div class="absolute inset-0 bg-gradient-to-b from-forest-50/95 to-background"></div>
  <div class="absolute inset-0 leaf-pattern opacity-20 pointer-events-none"></div>

  <div class="relative z-10 w-full px-4 sm:px-6 lg:px-12 xl:px-20 max-w-3xl mx-auto text-center">
    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-forest-100 border border-forest-200 mb-6">
      <svg class="w-4 h-4 text-forest-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
      </svg>
      <span class="text-xs sm:text-sm text-forest-700 font-semibold uppercase tracking-wider">Error 404</span>
    </div>

    <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-bold text-foreground mb-6 leading-tight">
      Lost in the Canopy
    </h1>

    <p class="text-base sm:text-lg text-foreground/70 max-w-xl mx-auto mb-10 leading-relaxed">
      The page or forest path you are searching for does not exist or has been relocated.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a
        href="<?php echo esc_url(home_url('/')); ?>"
        class="w-full sm:w-auto px-8 py-4 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated text-center"
      >
        Return to Homepage
      </a>
      <a
        href="<?php echo esc_url(home_url('/projects')); ?>"
        class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-forest-50 border border-forest-200 text-forest-700 font-medium rounded-full transition-all duration-300 shadow-medium text-center"
      >
        Explore Our Projects
      </a>
    </div>
  </div>
</div>

<?php
get_footer();
