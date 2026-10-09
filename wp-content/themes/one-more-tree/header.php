<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package One_More_Tree
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php
    // Site Identity – pull from admin options with fallbacks
    $omt_identity   = get_option('omt_admin_site_identity', []);
    $omt_nav_labels = get_option('omt_admin_nav_labels', []);
    $omt_favicon    = !empty($omt_identity['favicon_url']) ? esc_url($omt_identity['favicon_url']) : get_template_directory_uri() . '/assets/images/favicon.ico';
    $omt_logo_url   = !empty($omt_identity['logo_url'])   ? esc_url($omt_identity['logo_url'])   : get_template_directory_uri() . '/assets/images/logo.png';
    ?>
    <link rel="icon" href="<?php echo $omt_favicon; ?>">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    <?php wp_head(); ?>
</head>

<body <?php body_class('min-h-screen bg-background text-foreground antialiased'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site relative min-h-screen bg-background">
    <div class="noise-overlay" aria-hidden="true"></div>

    <a class="skip-link screen-reader-text sr-only" href="#primary">
        <?php _e('Skip to content', 'one-more-tree'); ?>
    </a>

    <!-- Disclaimer Ribbon - Exact mirror of Next.js PublicChrome / DisclaimerRibbon -->
    <?php
    $omt_disclaimer = get_option('omt_admin_disclaimer');
    $disclaimer_show = isset($omt_disclaimer['enabled']) ? (bool)$omt_disclaimer['enabled'] : true;
    $disclaimer_msg  = !empty($omt_disclaimer['message'])
        ? $omt_disclaimer['message']
        : '🌳 OneMoreTree NGO is a volunteer-led initiative. All donations go directly to tree planting, site restoration, and community programs. We are a registered non-profit — receipts are issued on request.';
    $disclaimer_color = get_option('omt_disclaimer_color', 'green');
    $color_classes = [
        'green' => ['bg' => 'bg-forest-900', 'fg' => 'text-forest-50', 'hover' => 'hover:bg-forest-700/60'],
        'red' => ['bg' => 'bg-red-950', 'fg' => 'text-red-50', 'hover' => 'hover:bg-red-700/60'],
        'yellow' => ['bg' => 'bg-amber-950', 'fg' => 'text-amber-50', 'hover' => 'hover:bg-amber-800/60'],
        'blue' => ['bg' => 'bg-blue-950', 'fg' => 'text-blue-50', 'hover' => 'hover:bg-blue-800/60'],
        'pink' => ['bg' => 'bg-fuchsia-950', 'fg' => 'text-fuchsia-50', 'hover' => 'hover:bg-fuchsia-800/60'],
    ];
    $color = $color_classes[$disclaimer_color] ?? $color_classes['green'];
    if ($disclaimer_show):
    ?>
    <div
      id="disclaimer-ribbon"
      role="status"
      aria-label="Site disclaimer"
      class="fixed top-0 left-0 right-0 z-[60] w-full <?php echo esc_attr($color['bg']); ?> <?php echo esc_attr($color['fg']); ?> transition-all duration-300"
    >

      <div class="w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="flex items-center justify-between gap-3 py-1.5 text-[11px] sm:text-xs">
          <div class="flex items-center gap-2 min-w-0 flex-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="leading-snug break-words line-clamp-2 sm:line-clamp-1">
              <span class="sm:hidden">OneMoreTree NGO — all donations fund tree planting.</span>
              <span class="hidden sm:inline"><?php echo esc_html($disclaimer_msg); ?></span>
            </p>
          </div>

          <button
            id="dismiss-disclaimer-btn"
            type="button"
            aria-label="Dismiss disclaimer"
            class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full <?php echo esc_attr($color['hover']); ?> transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Navigation - Exact mirror of Next.js Navigation.tsx -->
    <nav
      id="site-navigation"
      style="top: var(--disclaimer-h, 0px)"
      class="fixed left-0 right-0 z-50 transition-all duration-500 bg-white md:bg-transparent"
    >
      <div class="w-full px-4 sm:px-6 lg:px-12 xl:px-20">
        <div class="flex items-center justify-between h-16 sm:h-20">
          <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-2 group">
            <img
              src="<?php echo $omt_logo_url; ?>"
              alt="<?php echo esc_attr(!empty($omt_identity['site_title']) ? $omt_identity['site_title'] : 'OneMoreTree'); ?>"
              class="h-8 w-auto sm:h-10 transition-transform duration-300 group-hover:scale-105"
            />
            <span class="font-serif text-lg sm:text-xl font-bold text-foreground">
              <?php
                $site_title = !empty($omt_identity['site_title']) ? $omt_identity['site_title'] : 'OneMoreTree';
                // Split "OneMoreTree" into prefix + colored suffix for styling
                if (strtolower($site_title) === 'onemortree' || $site_title === 'OneMoreTree') {
                    echo 'OneMore<span class="text-forest-600">Tree</span>';
                } else {
                    echo esc_html($site_title);
                }
              ?>
            </span>
          </a>

          <div class="hidden md:flex items-center gap-8">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/">
              <?php echo esc_html($omt_nav_labels['home'] ?? 'Home'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/about')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/about">
              <?php echo esc_html($omt_nav_labels['about'] ?? 'About'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/projects">
              <?php echo esc_html($omt_nav_labels['projects'] ?? 'Projects'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/impact')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/impact">
              <?php echo esc_html($omt_nav_labels['impact'] ?? 'Impact'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/faq')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/faq">
              <?php echo esc_html($omt_nav_labels['faq'] ?? 'FAQ'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/get-involved')); ?>" class="nav-link text-sm transition-colors duration-300 animated-underline font-medium text-foreground/70 hover:text-forest-600" data-path="/get-involved">
              <?php echo esc_html($omt_nav_labels['get_involved'] ?? 'Get Involved'); ?>
            </a>
            <a
              href="<?php echo esc_url(!empty($omt_nav_labels['cta_url']) ? home_url($omt_nav_labels['cta_url']) : home_url('/donate')); ?>"
              class="px-5 py-2.5 bg-forest-600 hover:bg-forest-700 text-white text-sm font-semibold rounded-full transition-all duration-300 shadow-medium hover:shadow-elevated"
            >
              <?php echo esc_html($omt_nav_labels['cta_text'] ?? 'Donate Now'); ?>
            </a>
          </div>


          <button
            id="mobile-menu-btn"
            class="md:hidden p-2 text-foreground/70 hover:text-forest-600 transition-colors"
            aria-label="Toggle menu"
          >
            <svg id="mobile-menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path class="menu-icon-hamburger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path class="menu-icon-x hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div id="mobile-menu" class="md:hidden absolute top-full left-0 right-0 bg-white border-b border-earth-200 shadow-medium hidden">
          <div class="px-4 py-6 space-y-4">
            <a
              href="<?php echo esc_url(home_url('/')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              Home
            </a>
            <a
              href="<?php echo esc_url(home_url('/about')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              <?php echo esc_html($omt_nav_labels['about'] ?? 'About'); ?>
            </a>
            <a
              href="<?php echo esc_url(home_url('/projects')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              <?php echo esc_html($omt_nav_labels['projects'] ?? 'Projects'); ?>
            </a>
            <a
              href="<?php echo esc_url(home_url('/impact')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              <?php echo esc_html($omt_nav_labels['impact'] ?? 'Impact'); ?>
            </a>
            <a
              href="<?php echo esc_url(home_url('/faq')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              <?php echo esc_html($omt_nav_labels['faq'] ?? 'FAQ'); ?>
            </a>
            <a
              href="<?php echo esc_url(home_url('/get-involved')); ?>"
              class="block w-full text-left text-lg transition-colors duration-300 py-2 font-medium text-foreground/70 hover:text-forest-600"
            >
              <?php echo esc_html($omt_nav_labels['get_involved'] ?? 'Get Involved'); ?>
            </a>
            <a
              href="<?php echo esc_url(!empty($omt_nav_labels['cta_url']) ? home_url($omt_nav_labels['cta_url']) : home_url('/donate')); ?>"
              class="block w-full mt-4 px-5 py-3 bg-forest-600 hover:bg-forest-700 text-white font-semibold rounded-full transition-all duration-300 shadow-medium text-center"
            >
              <?php echo esc_html($omt_nav_labels['cta_text'] ?? 'Donate Now'); ?>
            </a>
          </div>
        </div>
      </div>

      <!-- Floating admin chip — shown for authenticated admins in dev mode only. -->
      <?php if (current_user_can('manage_options') && wp_get_environment_type() === 'development'): ?>
        <a
          href="<?php echo esc_url(admin_url()); ?>"
          aria-label="Admin"
          title="Admin console"
          class="fixed bottom-4 right-4 z-[70] inline-flex items-center gap-2 px-3 py-2 rounded-full bg-forest-700/95 hover:bg-forest-600 text-white text-xs font-semibold shadow-elevated backdrop-blur-md ring-1 ring-white/20 transition-all"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
          </svg>
          <span class="hidden sm:inline">Admin</span>
        </a>
      <?php endif; ?>
    </nav>

    <div id="content" class="site-content">
