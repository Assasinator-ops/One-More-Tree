<?php
/**
 * Admin Debug Page
 *
 * Displays comprehensive analysis of site availability, security, data metadata, counts, and concurrency
 */

// Add admin menu
add_action('admin_menu', 'one_more_tree_add_debug_menu');

function one_more_tree_add_debug_menu() {
    add_submenu_page(
        'one-more-tree-admin',
        'Debug & Analysis',
        'Debug & Analysis',
        'manage_options',
        'one-more-tree-debug',
        'one_more_tree_debug_page'
    );
}

function one_more_tree_debug_page() {
    ?>
    <div class="wrap">
        <h1>One More Tree - Debug & Analysis</h1>
        <p>Comprehensive site analysis and debugging information</p>

        <div id="omt-debug-container">
            <div class="omt-loading">Loading analysis data...</div>
        </div>
    </div>

    <style>
        #omt-debug-container {
            max-width: 1400px;
            margin-top: 20px;
        }
        .omt-loading {
            padding: 40px;
            text-align: center;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .omt-debug-section {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            padding: 20px;
        }
        .omt-debug-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #2271b1;
            color: #2271b1;
        }
        .omt-debug-section h3 {
            margin-top: 20px;
            color: #333;
        }
        .omt-debug-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .omt-debug-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #2271b1;
        }
        .omt-debug-card.warning {
            border-left-color: #f59e0b;
        }
        .omt-debug-card.error {
            border-left-color: #ef4444;
        }
        .omt-debug-card.success {
            border-left-color: #10b981;
        }
        .omt-debug-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }
        .omt-debug-value {
            font-size: 14px;
            color: #333;
        }
        .omt-debug-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .omt-debug-table th,
        .omt-debug-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .omt-debug-table th {
            background: #f0f0f1;
            font-weight: 600;
        }
        .omt-debug-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .omt-debug-actions button {
            margin-right: 10px;
            padding: 8px 16px;
            background: #2271b1;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .omt-debug-actions button:hover {
            background: #135e96;
        }
        .omt-debug-actions button.danger {
            background: #ef4444;
        }
        .omt-debug-actions button.danger:hover {
            background: #dc2626;
        }
        .omt-health-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .omt-health-badge.critical {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .omt-health-badge.warning {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .omt-health-badge.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('omt-debug-container');
            const nonce = '<?php echo wp_create_nonce('wp_rest'); ?>';

            fetch('/wp-json/omt/v1/admin/debug', {
                headers: {
                    'X-WP-Nonce': nonce
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    renderDebugData(data.analysis);
                } else {
                    container.innerHTML = '<div class="omt-debug-card error">Error loading debug data</div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<div class="omt-debug-card error">Failed to load debug data: ' + error.message + '</div>';
            });

            function renderDebugData(analysis) {
                let html = '';

                // Health Check Section
                html += renderHealthCheck(analysis.health_check);

                // Availability Section
                html += renderSection('Availability', analysis.availability);

                // Security Section
                html += renderSection('Security', analysis.security);

                // Data Metadata Section
                html += renderSection('Data & Metadata', analysis.data_metadata);

                // Counts Section
                html += renderSection('Counts', analysis.counts);

                // Concurrency Section
                html += renderSection('Concurrency', analysis.concurrency);

                // CRUD Actions
                html += renderCRUDActions();

                container.innerHTML = html;
            }

            function renderHealthCheck(health) {
                let html = '<div class="omt-debug-section">';
                html += '<h2>Health Check</h2>';

                if (health.critical_issues.length === 0 && health.warnings.length === 0) {
                    html += '<div class="omt-debug-card success">';
                    html += '<div class="omt-debug-label">Overall Status</div>';
                    html += '<div class="omt-debug-value">All systems operational</div>';
                    html += '</div>';
                }

                if (health.critical_issues.length > 0) {
                    html += '<h3>Critical Issues</h3>';
                    health.critical_issues.forEach(issue => {
                        html += '<div class="omt-debug-card error">';
                        html += '<div class="omt-debug-value">' + issue + '</div>';
                        html += '</div>';
                    });
                }

                if (health.warnings.length > 0) {
                    html += '<h3>Warnings</h3>';
                    health.warnings.forEach(warning => {
                        html += '<div class="omt-debug-card warning">';
                        html += '<div class="omt-debug-value">' + warning + '</div>';
                        html += '</div>';
                    });
                }

                html += '</div>';
                return html;
            }

            function renderSection(title, data) {
                let html = '<div class="omt-debug-section">';
                html += '<h2>' + title + '</h2>';

                for (const [key, value] of Object.entries(data)) {
                    html += '<h3>' + formatKey(key) + '</h3>';

                    if (typeof value === 'object' && value !== null) {
                        if (Array.isArray(value)) {
                            html += '<div class="omt-debug-value">' + value.join(', ') + '</div>';
                        } else {
                            html += '<div class="omt-debug-grid">';
                            for (const [subKey, subValue] of Object.entries(value)) {
                                html += '<div class="omt-debug-card">';
                                html += '<div class="omt-debug-label">' + formatKey(subKey) + '</div>';
                                html += '<div class="omt-debug-value">' + formatValue(subValue) + '</div>';
                                html += '</div>';
                            }
                            html += '</div>';
                        }
                    } else {
                        html += '<div class="omt-debug-card">';
                        html += '<div class="omt-debug-value">' + formatValue(value) + '</div>';
                        html += '</div>';
                    }
                }

                html += '</div>';
                return html;
            }

            function renderCRUDActions() {
                let html = '<div class="omt-debug-section">';
                html += '<h2>CRUD Actions</h2>';
                html += '<p>Manage admin data options. Be careful with delete operations.</p>';

                html += '<div class="omt-debug-grid">';
                const options = [
                    'home_counters', 'hero', 'live_map', 'impact_kpis',
                    'mission', 'about_hero', 'testimonials', 'stories',
                    'donation_tiers', 'disclaimer', 'social', 'team'
                ];

                options.forEach(option => {
                    html += '<div class="omt-debug-card">';
                    html += '<div class="omt-debug-label">' + formatKey(option) + '</div>';
                    html += '<div class="omt-debug-actions">';
                    html += '<button onclick="deleteOption(\'' + option + '\')" class="danger">Delete</button>';
                    html += '</div>';
                    html += '</div>';
                });

                html += '</div>';
                html += '</div>';
                return html;
            }

            function formatKey(key) {
                return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }

            function formatValue(value) {
                if (value === true) return '✓ Yes';
                if (value === false) return '✗ No';
                if (value === null) return 'N/A';
                if (typeof value === 'object') return JSON.stringify(value, null, 2);
                return String(value);
            }

            window.deleteOption = function(key) {
                if (!confirm('Are you sure you want to delete ' + key + '? This action cannot be undone.')) {
                    return;
                }

                fetch('/wp-json/omt/v1/admin/data/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': nonce
                    },
                    body: JSON.stringify({ key: key })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        alert('Deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Failed to delete: ' + error.message);
                });
            };
        });
    </script>
    <?php
}
