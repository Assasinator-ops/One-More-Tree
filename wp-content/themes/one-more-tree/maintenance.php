<?php
/**
 * Maintenance Mode Template
 * Exact mirror of Next.js app/maintenance/page.tsx
 *
 * @package One_More_Tree
 */

$can_proceed_god_mode = is_user_logged_in() && (current_user_can('manage_options') || current_user_can('edit_posts'));
$omt_identity = get_option('omt_admin_site_identity', []);
$omt_favicon  = !empty($omt_identity['favicon_url']) ? esc_url($omt_identity['favicon_url']) : get_template_directory_uri() . '/assets/images/favicon.ico';

add_filter('pre_get_document_title', function() {
    return 'Maintenance Mode — OneMoreTree';
}, 99);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo $omt_favicon; ?>">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/main.css?v=<?php echo _S_VERSION; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body class="min-h-screen bg-background text-foreground antialiased selection:bg-forest-100 selection:text-forest-900">
    <div class="relative min-h-screen bg-background">
        <div class="noise-overlay" aria-hidden="true"></div>

        <div class="min-h-screen bg-background flex items-center justify-center px-4 py-16 sm:py-24">
            <div class="w-full max-w-4xl rounded-3xl border border-earth-200 bg-white/90 p-6 sm:p-10 text-center shadow-xl relative overflow-hidden">
                <!-- animated background ornaments (no dependency) -->
                <div class="pointer-events-none absolute inset-0 opacity-80">
                    <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-gradient-to-tr from-forest-500/25 via-forest-300/20 to-emerald-300/20 blur-2xl animate-pulse"></div>
                    <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-tr from-earth-200/40 via-slate-200/30 to-forest-400/25 blur-2xl animate-pulse"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(34,197,94,0.18),transparent_45%),radial-gradient(circle_at_80%_30%,rgba(16,185,129,0.12),transparent_40%),radial-gradient(circle_at_50%_90%,rgba(5,150,105,0.12),transparent_55%)]"></div>
                    <div class="absolute inset-0 leaf-pattern opacity-10 pointer-events-none"></div>
                </div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-earth-200 bg-white/70 shadow-sm">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-forest-600 animate-ping"></span>
                        <p class="text-xs sm:text-sm uppercase tracking-[0.25em] text-foreground/60 mb-0">
                            Maintenance mode
                        </p>
                    </div>

                    <h1 class="mt-6 font-serif text-4xl sm:text-6xl font-bold text-foreground">
                        We'll be back soon.
                    </h1>

                    <p class="mt-4 text-base sm:text-lg leading-8 text-foreground/70 max-w-2xl mx-auto">
                        The website is temporarily offline so we can improve performance and reliability.
                    </p>

                    <!-- animated info blocks -->
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-earth-200 bg-white/70 p-5 text-left">
                            <div class="text-foreground font-semibold">What's happening</div>
                            <div class="mt-1 text-foreground/70 text-sm leading-6">
                                Deploying updates and syncing content modules.
                            </div>
                            <div class="mt-3 h-1.5 w-full rounded-full bg-earth-200 overflow-hidden">
                                <div class="h-full w-2/3 bg-forest-600 rounded-full animate-[maintenance-slide_1.6s_ease-in-out_infinite]"></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-earth-200 bg-white/70 p-5 text-left">
                            <div class="text-foreground font-semibold">Expected time</div>
                            <div class="mt-1 text-foreground/70 text-sm leading-6">
                                Short maintenance window. We'll bring it back as soon as it's ready.
                            </div>
                            <div class="mt-3 text-xs uppercase tracking-[0.14em] text-foreground/50">
                                Thank you for your patience
                            </div>
                        </div>
                    </div>

                    <!-- actions -->
                    <div class="mt-10 flex flex-col sm:flex-row gap-3 sm:justify-center">
                        <?php if ($can_proceed_god_mode): ?>
                            <a
                                href="<?php echo esc_url(home_url('/administrator')); ?>"
                                class="
                                    rounded-2xl px-6 py-3 text-foreground
                                    shadow-[0_0_0_1px_rgba(20,83,45,0.15),0_12px_40px_rgba(0,0,0,0.12)]
                                    bg-gradient-to-b from-yellow-300 via-amber-400 to-yellow-200
                                    hover:brightness-110 transition
                                    font-semibold
                                "
                            >
                                Proceed as God Mode
                            </a>
                        <?php endif; ?>
                    </div>

                    <p class="mt-6 text-xs text-foreground/50">
                        If you're seeing this unexpectedly, please try refreshing once maintenance is complete.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>
