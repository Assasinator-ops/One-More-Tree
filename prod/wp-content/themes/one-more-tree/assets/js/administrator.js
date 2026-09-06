/**
 * OneMoreTree Administrator Console Application - Modular Entry Point
 * Loads admin modules dynamically for better performance and maintainability
 */

(function() {
    'use strict';

    const config = window.OMT_ADMIN_CONFIG || {};

    // Module registry for manual dependency management
    const AdminModules = {
        Auth: null,
        API: null,
        Navigation: null,
        Dashboard: null,
        Forms: null,
        Settings: null
    };

    // Dynamic module loader using script tags for broader compatibility
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    async function loadAdminModules() {
        try {
            // Load module scripts dynamically
            await Promise.all([
                loadScript(config.themeUrl + '/assets/js/admin/api.js'),
                loadScript(config.themeUrl + '/assets/js/admin/auth.js'),
                loadScript(config.themeUrl + '/assets/js/admin/navigation.js'),
                loadScript(config.themeUrl + '/assets/js/admin/dashboard.js'),
                loadScript(config.themeUrl + '/assets/js/admin/forms.js'),
                loadScript(config.themeUrl + '/assets/js/admin/settings.js')
            ]);

            // Wait a moment for modules to register themselves
            await new Promise(resolve => setTimeout(resolve, 100));

            // Access modules from global scope (simplified approach)
            const api = window.AdminAPI ? new window.AdminAPI(config) : null;
            const auth = window.AdminAuth ? new window.AdminAuth(config) : null;
            const navigation = window.AdminNavigation ? new window.AdminNavigation(config) : null;
            const dashboard = window.AdminDashboard ? new window.AdminDashboard(config) : null;
            const forms = window.AdminForms ? new window.AdminForms() : null;
            const settings = window.AdminSettings ? new window.AdminSettings(config) : null;

            // Set API references for modules that need it
            if (dashboard && api) {
                dashboard.api = api;
            }
            if (settings && api) {
                settings.api = api;
            }

            // Initialize modules that have their dependencies
            if (dashboard && dashboard.api) {
                dashboard.init();
            }
            if (settings && settings.api) {
                settings.init();
            }

            // Handle authentication (if not logged in, stop here)
            if (!config.isLoggedIn) {
                return; // Auth module handles login form
            }

            // Connect navigation to dashboard
            if (navigation && dashboard) {
                navigation.setOnPageChange((pageKey) => {
                    dashboard.loadModule(pageKey);
                });
            }

            // Expose to window for debugging if needed
            window.OMTAdmin = {
                api,
                auth,
                navigation,
                dashboard,
                forms,
                settings,
                config
            };

        } catch (error) {
            console.error('Failed to load admin modules:', error);
            
            // Fallback to basic functionality if modules fail to load
            const errorDiv = document.getElementById('admin-module-content');
            if (errorDiv) {
                errorDiv.innerHTML = `
                    <div class="p-8 bg-red-50 border border-red-200 rounded-2xl text-center">
                        <h3 class="text-red-700 font-semibold mb-2">Admin Console Error</h3>
                        <p class="text-red-600 text-sm">Failed to load admin modules. Please refresh the page.</p>
                        <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                            Refresh Page
                        </button>
                    </div>
                `;
            }
        }
    }

    // Start the application
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAdminModules);
    } else {
        loadAdminModules();
    }

})();