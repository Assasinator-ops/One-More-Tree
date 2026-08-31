<?php
/**
 * Standalone Administrator Console View
 * Exact clone of Next.js AdminShell & LoginView with unified WordPress authentication
 *
 * @package One_More_Tree
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in() && (current_user_can('manage_options') || current_user_can('edit_posts'));
$current_user = wp_get_current_user();
$username = $is_logged_in ? $current_user->user_login : '';
$display_name = $is_logged_in ? $current_user->display_name : '';
$rest_nonce = wp_create_nonce('wp_rest');
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OneMoreTree — Admin Console</title>
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/main.css?v=<?php echo _S_VERSION; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Scoped styles for Glorious Modern Admin Console */
        #admin-login-screen {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #020617;
            color: #f8fafc;
            padding: 24px 16px;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .admin-login-container {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
            box-sizing: border-box;
        }

        .admin-glow-orb-1 {
            position: absolute;
            top: -15%;
            left: -10%;
            width: 55vw;
            height: 55vw;
            max-width: 600px;
            max-height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(22, 163, 74, 0.22) 0%, rgba(22, 163, 74, 0) 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: pulseGlow 10s ease-in-out infinite alternate;
        }

        .admin-glow-orb-2 {
            position: absolute;
            bottom: -15%;
            right: -10%;
            width: 55vw;
            height: 55vw;
            max-width: 600px;
            max-height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(16, 185, 129, 0) 70%);
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
            animation: pulseGlow 12s ease-in-out infinite alternate-reverse;
        }

        .admin-grid-texture {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.6;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes pulseGlow {
            0% { transform: scale(0.95); opacity: 0.7; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        .admin-glass-card {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        .admin-glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(52, 211, 153, 0.6), transparent);
        }

        .admin-logo-emblem {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
            border: 1px solid rgba(52, 211, 153, 0.4);
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            position: relative;
        }

        .admin-input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .admin-input-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 6px;
            margin-left: 2px;
        }

        .admin-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #64748b;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .admin-input-wrapper:focus-within .admin-input-icon {
            color: #34d399;
        }

        .admin-text-input {
            width: 100% !important;
            height: 48px !important;
            box-sizing: border-box !important;
            background: rgba(2, 6, 23, 0.75) !important;
            border: 1px solid rgba(51, 65, 85, 0.9) !important;
            border-radius: 14px !important;
            color: #ffffff !important;
            padding: 0 16px 0 44px !important;
            font-size: 14px !important;
            font-family: inherit !important;
            outline: none !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .admin-text-input.has-toggle {
            padding-right: 46px !important;
        }

        .admin-text-input:focus {
            background: rgba(2, 6, 23, 0.95) !important;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25) !important;
        }

        .admin-text-input::placeholder {
            color: #475569 !important;
        }

        .admin-toggle-pwd {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #64748b;
            padding: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: color 0.2s ease;
        }

        .admin-toggle-pwd:hover {
            color: #34d399;
        }

        #login-submit-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 52px !important;
            background: linear-gradient(135deg, #15803d 0%, #16a34a 50%, #059669 100%) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 16px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            letter-spacing: 0.01em !important;
            box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.45) !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            margin-top: 10px;
        }

        #login-submit-btn:hover {
            background: linear-gradient(135deg, #166534 0%, #15803d 50%, #047857 100%) !important;
            box-shadow: 0 14px 28px -4px rgba(22, 163, 74, 0.55) !important;
            transform: translateY(-1px);
        }

        #login-submit-btn:active {
            transform: scale(0.99) translateY(0);
        }

        #login-submit-btn:disabled {
            opacity: 0.65 !important;
            cursor: not-allowed !important;
        }

        #login-error-box {
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(239, 68, 68, 0.35);
            background: rgba(69, 10, 10, 0.6);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        #login-error-box.hidden {
            display: none !important;
        }

        #login-error-box.flex {
            display: flex !important;
        }

        .admin-badge-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(51, 65, 85, 0.6);
            text-align: center;
        }

        .admin-badge-item {
            background: rgba(2, 6, 23, 0.4);
            border: 1px solid rgba(51, 65, 85, 0.6);
            border-radius: 12px;
            padding: 8px 4px;
            font-size: 10px;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .admin-badge-item strong {
            color: #34d399;
            font-size: 11px;
            font-weight: 700;
        }
    </style>
    <script>
        window.OMT_ADMIN_CONFIG = {
            isLoggedIn: <?php echo $is_logged_in ? 'true' : 'false'; ?>,
            username: <?php echo json_encode($username); ?>,
            displayName: <?php echo json_encode($display_name); ?>,
            restUrl: <?php echo json_encode(esc_url_raw(rest_url('omt/v1/'))); ?>,
            restNonce: <?php echo json_encode($rest_nonce); ?>,
            siteUrl: <?php echo json_encode(esc_url_raw(home_url('/'))); ?>,
            themeUrl: <?php echo json_encode(esc_url_raw(get_template_directory_uri())); ?>
        };
    </script>
</head>
<body class="min-h-screen bg-background text-foreground antialiased selection:bg-forest-100 selection:text-forest-900">

<?php if (!$is_logged_in): ?>
    <!-- ========================================== -->
    <!-- 1. LOGIN VIEW (Glorious & Modern Administrator Console) -->
    <!-- ========================================== -->
    <div id="admin-login-screen">
        <!-- Ambient Glowing Aura Orbs -->
        <div class="admin-glow-orb-1"></div>
        <div class="admin-glow-orb-2"></div>
        <div class="admin-grid-texture"></div>

        <div class="admin-login-container">
            <!-- Brand Badge & Header -->
            <div style="text-align: center; margin-bottom: 28px;">
                <!-- Glowing Leaf Insignia Emblem -->
                <div class="admin-logo-emblem">
                    <svg style="width: 36px; height: 36px; color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM12 8v8m-3-3l3-3 3 3"></path>
                    </svg>
                </div>

                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 14px; border-radius: 9999px; background: rgba(6, 78, 59, 0.6); border: 1px solid rgba(52, 211, 153, 0.3); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #6ee7b7; margin-bottom: 12px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #34d399; display: inline-block;"></span>
                    <span>Admin Console • Governance Core</span>
                </div>

                <h1 style="font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; color: #ffffff; margin: 0 0 6px 0; letter-spacing: -0.02em;">OneMoreTree</h1>
                <p style="color: #94a3b8; font-size: 13px; margin: 0; font-weight: 400;">Environmental Restoration & Platform Management</p>
            </div>

            <!-- Glassmorphic Login Card -->
            <div class="admin-glass-card">
                <form id="admin-login-form">
                    <!-- Username Field -->
                    <div class="admin-input-group">
                        <label for="login-username">Administrator ID</label>
                        <div class="admin-input-wrapper">
                            <svg class="admin-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <input
                                id="login-username"
                                type="text"
                                placeholder="Username or email"
                                required
                                autocomplete="username"
                                class="admin-text-input"
                            />
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="admin-input-group">
                        <label for="login-password">Password</label>
                        <div class="admin-input-wrapper">
                            <svg class="admin-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <input
                                id="login-password"
                                type="password"
                                placeholder="••••••••••••"
                                required
                                autocomplete="current-password"
                                class="admin-text-input has-toggle"
                            />
                            <button
                                type="button"
                                id="toggle-password-btn"
                                class="admin-toggle-pwd"
                                aria-label="Toggle password visibility"
                            >
                                <svg id="eye-icon" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Error Notification Box -->
                    <div id="login-error-box" class="hidden">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0; color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span id="login-error-msg">Invalid credentials.</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        id="login-submit-btn"
                    >
                        <span id="login-btn-text" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <span>Access Admin Console</span>
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                    </button>
                </form>

                <!-- Security Verification Badges -->
                <div class="admin-badge-grid">
                    <div class="admin-badge-item">
                        <strong>256-BIT</strong>
                        <span>SSL Encrypted</span>
                    </div>
                    <div class="admin-badge-item">
                        <strong>WP REST</strong>
                        <span>Nonce Guard</span>
                    </div>
                    <div class="admin-badge-item">
                        <strong>AUDITED</strong>
                        <span>Session Auth</span>
                    </div>
                </div>
            </div>

            <!-- Footer Navigation -->
            <div style="margin-top: 28px; text-align: center; font-size: 13px; color: #64748b; display: flex; align-items: center; justify-content: center; gap: 14px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #94a3b8; text-decoration: none; display: flex; align-items: center; gap: 6px; transition: color 0.2s ease;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Back to OneMoreTree</span>
                </a>
                <span style="color: #334155;">•</span>
                <a href="<?php echo esc_url(admin_url()); ?>" style="color: #94a3b8; text-decoration: none; transition: color 0.2s ease;">
                    WordPress Standard Login
                </a>
            </div>
        </div>
    </div>

<?php else: ?>


    <!-- ========================================== -->
    <!-- 2. ADMIN SHELL (Exact mirror of AdminShell.tsx) -->
    <!-- ========================================== -->
    <div id="admin-shell" class="min-h-screen bg-background">
        <main class="relative w-full px-4 sm:px-6 lg:px-12 xl:px-20 py-12 sm:py-20">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between gap-4 mb-2">
                    <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-foreground">OneMoreTree Admin</h1>
                    <a href="<?php echo esc_url(admin_url()); ?>" class="text-xs font-semibold text-forest-700 bg-forest-50 border border-forest-200 px-3 py-1.5 rounded-full hover:bg-forest-100 transition-colors flex items-center gap-1.5">
                        <span>WordPress Dashboard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
                <p class="text-foreground/70 max-w-3xl text-sm sm:text-base">
                    Admin modules are independent working areas. Changes are persisted directly to the WordPress database and all public views update immediately.
                </p>

                <!-- Navigation Toolbar -->
                <div class="mt-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between md:gap-6">
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2" id="admin-nav-groups-container">
                            <!-- Populated dynamically via administrator.js -->
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="rounded-2xl border border-earth-200 bg-slate-50 px-4 py-3 text-xs sm:text-sm text-foreground/70">
                            Signed in as <strong><?php echo esc_html($username); ?></strong>
                        </div>
                        <a
                            href="<?php echo esc_url(home_url('/')); ?>"
                            target="_blank"
                            class="rounded-2xl bg-slate-800 hover:bg-slate-900 px-4 py-3 text-white transition font-medium text-xs sm:text-sm text-center"
                        >
                            View Site ↗
                        </a>
                        <button
                            type="button"
                            id="admin-logout-btn"
                            class="rounded-2xl bg-forest-600 px-4 py-3 text-white transition hover:bg-forest-700 font-medium text-xs sm:text-sm"
                        >
                            Logout
                        </button>
                    </div>
                </div>

                <!-- Main Module Container -->
                <div id="admin-module-container" class="mt-6 rounded-3xl glass-card border border-earth-200 p-4 sm:p-8 shadow-soft">
                    <div id="admin-module-content">
                        <!-- Module content injected here dynamically -->
                    </div>
                </div>
            </div>
        </main>
    </div>

<?php endif; ?>

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/administrator.js?v=<?php echo _S_VERSION; ?>"></script>
</body>
</html>
