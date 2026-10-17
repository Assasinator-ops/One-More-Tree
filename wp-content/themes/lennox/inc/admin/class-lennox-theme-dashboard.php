<?php
/**
 * Theme dashboard admin page.
 *
 * @package lennox
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme dashboard class.
 *
 * @since 1.0.0
 */
class Lennox_Theme_Dashboard {

	/**
	 * Allowed dashboard tabs.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	const ALLOWED_TABS = array( 'welcome', 'starter-sites', 'free-vs-pro' );

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_head', array( $this, 'render_menu_icon_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_lennox_import_starter_site', array( $this, 'import_starter_site' ) );
	}

	/**
	 * Import a bundled starter site.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function import_starter_site() {
		if ( false === check_ajax_referer( 'lennox_dashboard_modules', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request nonce.', 'lennox' ),
				),
				403
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to perform this action.', 'lennox' ),
				),
				403
			);
		}

		$starter_slug = isset( $_POST['starter'] ) && is_string( $_POST['starter'] ) ? sanitize_key( wp_unslash( $_POST['starter'] ) ) : '';

		if ( '' === $starter_slug || ! class_exists( 'Lennox_Starter_Sites' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid starter site.', 'lennox' ),
				),
				400
			);
		}

		$result = Lennox_Starter_Sites::import( $starter_slug );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
				),
				400
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Starter site imported.', 'lennox' ),
				'siteUrl' => home_url( '/' ),
				'import'  => $result,
			),
			200
		);
	}

	/**
	 * Enqueue dashboard assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( 'toplevel_page_lennox-theme-dashboard' !== $hook_suffix ) {
			return;
		}

		$stylesheet_path = get_theme_file_path( 'assets/css/admin-theme-dashboard.css' );

		wp_enqueue_style( 'wp-components' );

		if ( file_exists( $stylesheet_path ) ) {
			wp_enqueue_style(
				'lennox-admin-theme-dashboard',
				get_theme_file_uri( 'assets/css/admin-theme-dashboard.css' ),
				array( 'wp-components' ),
				(string) filemtime( $stylesheet_path )
			);

			if ( $this->is_pro_active() ) {
				wp_add_inline_style(
					'lennox-admin-theme-dashboard',
					'.lennox-theme-dashboard__upgrade-link { display: none !important; }'
				);
			}
		}

		if ( ! $this->has_dashboard_build_assets() ) {
			return;
		}

		$asset_path   = get_theme_file_path( 'build/admin/dashboard/index.asset.php' );
		$script_asset = require $asset_path;

		if ( ! is_array( $script_asset ) ) {
			return;
		}

		$dependencies = isset( $script_asset['dependencies'] ) && is_array( $script_asset['dependencies'] ) ? array_filter( $script_asset['dependencies'], 'is_string' ) : array();

		wp_enqueue_script(
			'lennox-admin-theme-dashboard',
			get_theme_file_uri( 'build/admin/dashboard/index.js' ),
			$dependencies,
			isset( $script_asset['version'] ) ? (string) $script_asset['version'] : false,
			true
		);

		wp_set_script_translations( 'lennox-admin-theme-dashboard', 'lennox' );

		wp_add_inline_script(
			'lennox-admin-theme-dashboard',
			'window.lennoxThemeDashboard = ' . wp_json_encode( $this->get_dashboard_data() ) . ';',
			'before'
		);
	}

	/**
	 * Register the theme dashboard page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_page() {
		add_menu_page(
			__( 'Lennox', 'lennox' ),
			__( 'Lennox', 'lennox' ),
			'manage_options',
			'lennox-theme-dashboard',
			array( $this, 'render_page' ),
			$this->get_menu_icon(),
			61
		);
	}

	/**
	 * Get the dashboard admin menu icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_menu_icon() {
		return 'none';
	}

	/**
	 * Render admin menu icon styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_menu_icon_styles() {
		?>
		<style>
			#adminmenu #toplevel_page_lennox-theme-dashboard .wp-menu-image::before {
				box-sizing: border-box;
				width: 20px;
				height: 20px;
				margin: 7px 8px;
				padding: 0;
				border-radius: 50%;
				background: #f0f0f1;
				color: #1d2327;
				content: "L";
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
				font-size: 12px;
				font-weight: 600;
				line-height: 20px;
				text-align: center;
			}

			#adminmenu #toplevel_page_lennox-theme-dashboard.current .wp-menu-image::before,
			#adminmenu #toplevel_page_lennox-theme-dashboard.wp-has-current-submenu .wp-menu-image::before {
				background: #fff;
				color: var(--wp-admin-theme-color, #3858e9);
			}
		</style>
		<?php
	}

	/**
	 * Render the dashboard page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="lennox-theme-dashboard">
			<?php if ( ! $this->has_dashboard_build_assets() ) : ?>
				<div class="notice notice-warning inline">
					<p>
						<?php esc_html_e( 'The Lennox theme dashboard app is not available because the React build files are missing. Run the dashboard build to generate the admin assets.', 'lennox' ); ?>
					</p>
				</div>
			<?php endif; ?>
			<div id="lennox-theme-dashboard-root"></div>
			<noscript>
				<p><?php esc_html_e( 'The Lennox theme dashboard requires JavaScript to render.', 'lennox' ); ?></p>
			</noscript>
		</div>
		<?php
	}

	/**
	 * Get dashboard bootstrap data.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	private function get_dashboard_data() {
		$heading = __( 'Lennox', 'lennox' );
		$intro   = __( 'Welcome to the Lennox theme dashboard. Use the tabs below to explore the welcome resources and compare the free and pro editions.', 'lennox' );

		if ( $this->is_pro_active() ) {
			$intro = __( 'Welcome to the Lennox theme dashboard. Use the tabs below to explore the available resources.', 'lennox' );
		}

		return array(
			'activeTab'         => $this->get_active_tab(),
			'heading'           => $heading,
			'intro'             => $intro,
			'version'           => (string) LENNOX_VERSION,
			'isProActive'       => $this->is_pro_active(),
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'ajaxNonce'         => wp_create_nonce( 'lennox_dashboard_modules' ),
			'upgradeUrl'        => $this->get_upgrade_url( 'dashboard-header' ),
			'moduleEditOptions' => $this->get_module_edit_options(),
			'tabs'              => $this->get_tabs(),
			'welcome'           => array(
				'title'       => sprintf(
					/* translators: %s: Theme title. */
					__( 'Welcome to %s', 'lennox' ),
					$heading
				),
				'description' => __( 'Use these resources to jump into the main block theme editing areas and keep the core documentation close by.', 'lennox' ),
				'resources'   => $this->get_documentation_links(),
				'starterSite' => array(
					'title'       => __( 'Starter Sites', 'lennox' ),
					'description' => __( 'Import bundled starter content, navigation, and styles.', 'lennox' ),
					'buttonLabel' => __( 'Browse Starter Sites', 'lennox' ),
					'tab'         => 'starter-sites',
				),
			),
			'freeVsPro'         => array(
				'title'       => __( 'Free vs Pro', 'lennox' ),
				'description' => __( 'Compare the key differences between Lennox Free and Lennox Pro.', 'lennox' ),
				'buttonLabel' => __( 'Upgrade to Pro', 'lennox' ),
				'buttonUrl'   => $this->get_upgrade_url( 'free-vs-pro' ),
				'comparison'  => $this->get_comparison_rows(),
			),
			'starterSites'      => array(
				'title'       => __( 'Starter Sites', 'lennox' ),
				'description' => __( 'Import a bundled starter site with pages, navigation, and global styles.', 'lennox' ),
				'upgradeUrl'  => 'https://emberwp.com/themes/lennox',
				'starters'    => class_exists( 'Lennox_Starter_Sites' ) ? Lennox_Starter_Sites::get_starters() : array(),
			),
			'modulesSection'    => $this->get_modules_section_data(),
		);
	}

	/**
	 * Get module edit options schema from integrations.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_module_edit_options() {
		$edit_options = apply_filters( 'lennox_dashboard_module_edit_options', array() );

		if ( ! is_array( $edit_options ) ) {
			return array();
		}

		$normalized_options = array();

		foreach ( $edit_options as $module_slug => $module_options ) {
			if ( ! is_string( $module_slug ) || ! is_array( $module_options ) ) {
				continue;
			}

			$normalized_options[ $module_slug ] = $module_options;
		}

		return $normalized_options;
	}

	/**
	 * Get module cards and allow Lennox Pro to supply their state.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	private function get_modules_section_data() {
		$modules = $this->get_modules_catalog();

		foreach ( $modules as $index => $module ) {
			$modules[ $index ]['enabled']  = 'false';
			$modules[ $index ]['settings'] = array();
		}

		$section = apply_filters(
			'lennox_dashboard_modules_section_data',
			array(
				'title'       => __( 'Modules', 'lennox' ),
				'description' => __( 'Unlock optional feature modules with Lennox Pro.', 'lennox' ),
				'modules'     => $modules,
			)
		);

		return is_array( $section ) ? $section : array();
	}

	/**
	 * Get the display-only catalog of Lennox Pro modules.
	 *
	 * Module state, settings, and validation are owned by Lennox Pro. Keeping
	 * this catalog in the free theme lets the dashboard advertise the locked
	 * Pro features when the plugin is inactive.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, string|bool>>
	 */
	private function get_modules_catalog() {
		return array(
			array(
				'slug'        => 'back-to-top',
				'title'       => __( 'Back to top', 'lennox' ),
				'description' => __( 'Show a floating button that scrolls visitors back to the top.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'floating-contact-buttons',
				'title'       => __( 'Floating Contact Buttons', 'lennox' ),
				'description' => __( 'Display quick contact actions in a floating frontend stack.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'footer-reveal-effect',
				'title'       => __( 'Footer Reveal Effect', 'lennox' ),
				'description' => __( 'Reveal the footer with a layered scroll effect as visitors reach the bottom of the page.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'reading-progress-bar',
				'title'       => __( 'Reading Progress Bar', 'lennox' ),
				'description' => __( 'Show a progress indicator that tracks how much of the post the visitor has read.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'smart-sticky-header',
				'title'       => __( 'Smart Sticky Header', 'lennox' ),
				'description' => __( 'Auto-hide the sticky header while scrolling down and reveal it when scrolling up.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'animations',
				'title'       => __( 'Animations', 'lennox' ),
				'description' => __( 'Animate elements when they enter the viewport for subtle motion across page sections.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'custom-cursor',
				'title'       => __( 'Custom Cursor', 'lennox' ),
				'description' => __( 'Replace the native cursor with an accent dot and trailing ring on pointer devices.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'hover-colors',
				'title'       => __( 'Hover Colors', 'lennox' ),
				'description' => __( 'Add hover state colors for text, background, and borders to blocks, with transition controls.', 'lennox' ),
				'isPro'       => true,
			),
			array(
				'slug'        => 'maintenance-mode',
				'title'       => __( 'Maintenance Mode', 'lennox' ),
				'description' => __( 'Show a branded maintenance or coming-soon screen while you keep building the site.', 'lennox' ),
				'isPro'       => true,
			),
		);
	}

	/**
	 * Check whether the built dashboard assets are available.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function has_dashboard_build_assets() {
		$script_path = get_theme_file_path( 'build/admin/dashboard/index.js' );
		$asset_path  = get_theme_file_path( 'build/admin/dashboard/index.asset.php' );

		return file_exists( $script_path ) && file_exists( $asset_path );
	}

	/**
	 * Get the dashboard tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, string>>
	 */
	private function get_tabs() {
		$tabs = array(
			array(
				'name'  => 'welcome',
				'title' => __( 'Welcome', 'lennox' ),
			),
			array(
				'name'  => 'starter-sites',
				'title' => __( 'Starter Sites', 'lennox' ),
			),
		);

		if ( ! $this->is_pro_active() ) {
			$tabs[] = array(
				'name'  => 'free-vs-pro',
				'title' => __( 'Free vs Pro', 'lennox' ),
			);
		}

		return $tabs;
	}

	/**
	 * Get the Lennox Pro upgrade URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content UTM content value.
	 * @return string
	 */
	private function get_upgrade_url( $content ) {
		return add_query_arg(
			array(
				'utm_source'   => 'lennox-theme',
				'utm_medium'   => 'dashboard',
				'utm_campaign' => 'upgrade-to-pro',
				'utm_content'  => sanitize_key( $content ),
			),
			'https://emberwp.com/themes/lennox'
		);
	}

	/**
	 * Determine whether Lennox Pro is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_pro_active() {
		return defined( 'LENNOX_PRO_VERSION' );
	}

	/**
	 * Get the active dashboard tab.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_active_tab() {
		$active_tab = 'welcome';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab routing.
		if ( isset( $_GET['tab'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab routing.
			$active_tab = is_string( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'welcome';
		}

		if ( ! in_array( $active_tab, self::ALLOWED_TABS, true ) ) {
			return 'welcome';
		}

		return $active_tab;
	}

	/**
	 * Get documentation links.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_documentation_links() {
		return array(
			array(
				'title'       => __( 'Quick Actions', 'lennox' ),
				'description' => '',
				'links'       => $this->get_quick_links(),
			),
			array(
				'title'       => __( 'Resources & Documentation', 'lennox' ),
				'description' => '',
				'links'       => $this->get_resource_links(),
			),
		);
	}

	/**
	 * Get support and documentation resource links.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_resource_links() {
		return array(
			array(
				'label'       => __( 'Support forum', 'lennox' ),
				'description' => __( 'Ask questions and find answers from the WordPress community.', 'lennox' ),
				'icon'        => 'comment',
				'url'         => 'https://wordpress.org/support/theme/lennox/',
			),
			array(
				'label'       => __( 'Email support', 'lennox' ),
				'description' => __( 'Contact the support team directly for account or technical help.', 'lennox' ),
				'icon'        => 'envelope',
				'url'         => 'mailto:hello@emberwp.com',
				'isProLocked' => ! defined( 'LENNOX_PRO_VERSION' ),
			),
			array(
				'label'       => __( 'Documentation', 'lennox' ),
				'description' => __( 'Browse setup guides and theme development references.', 'lennox' ),
				'icon'        => 'pages',
				'url'         => 'https://emberwp.com/documentation/lennox',
			),
		);
	}

	/**
	 * Get quick links for key block theme editing screens.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_quick_links() {
		$site_editor_url = admin_url( 'site-editor.php' );

		return array(
			array(
				'label'       => __( 'Global styles', 'lennox' ),
				'description' => __( 'Customize colors and design tokens.', 'lennox' ),
				'icon'        => 'styles',
				'url'         => add_query_arg( 'p', rawurlencode( '/styles' ), $site_editor_url ),
				'children'    => array(
					array(
						'label'       => __( 'Colors', 'lennox' ),
						'description' => __( 'Edit your color palette.', 'lennox' ),
						'icon'        => 'color',
						'url'         => add_query_arg(
							array(
								'p'       => rawurlencode( '/styles' ),
								'section' => rawurlencode( '/colors' ),
							),
							$site_editor_url
						),
					),
					array(
						'label'       => __( 'Typography', 'lennox' ),
						'description' => __( 'Adjust font settings and sizes.', 'lennox' ),
						'icon'        => 'typography',
						'url'         => add_query_arg(
							array(
								'p'       => rawurlencode( '/styles' ),
								'section' => rawurlencode( '/typography' ),
							),
							$site_editor_url
						),
					),
				),
			),
			array(
				'label'       => __( 'Templates', 'lennox' ),
				'description' => __( 'Browse page and post templates.', 'lennox' ),
				'icon'        => 'layout',
				'url'         => add_query_arg( 'p', rawurlencode( '/template' ), $site_editor_url ),
			),
			array(
				'label'       => __( 'Header', 'lennox' ),
				'description' => __( 'Edit the site header template part.', 'lennox' ),
				'icon'        => 'header',
				'url'         => add_query_arg(
					array(
						'p'          => rawurlencode( '/pattern' ),
						'postType'   => 'wp_template_part',
						'categoryId' => 'header',
					),
					$site_editor_url
				),
			),
			array(
				'label'       => __( 'Footer', 'lennox' ),
				'description' => __( 'Edit the site footer template part.', 'lennox' ),
				'icon'        => 'footer',
				'url'         => add_query_arg(
					array(
						'p'          => rawurlencode( '/pattern' ),
						'postType'   => 'wp_template_part',
						'categoryId' => 'footer',
					),
					$site_editor_url
				),
			),
		);
	}

	/**
	 * Get the free vs pro comparison rows.
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array<string, string>>
	 */
	private function get_comparison_rows() {
		return array(
			array(
				'feature' => __( 'Site templates', 'lennox' ),
				'free'    => __( '17', 'lennox' ),
				'pro'     => __( '17', 'lennox' ),
			),
			array(
				'feature' => __( 'Starter sites', 'lennox' ),
				'free'    => __( '2', 'lennox' ),
				'pro'     => __( '4', 'lennox' ),
			),
			array(
				'feature' => __( 'Patterns', 'lennox' ),
				'free'    => __( '20 total', 'lennox' ),
				'pro'     => __( '75 total', 'lennox' ),
			),
			array(
				'feature' => __( 'Six visual styles', 'lennox' ),
				'free'    => __( 'Yes', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Global Styles and Site Editor support', 'lennox' ),
				'free'    => __( 'Yes', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'WooCommerce templates', 'lennox' ),
				'free'    => __( 'Yes', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Complete page layouts (About, Contact, etc.)', 'lennox' ),
				'free'    => __( 'None', 'lennox' ),
				'pro'     => __( '6', 'lennox' ),
			),
			array(
				'feature' => __( 'Entrance animations (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Smart sticky header (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Hover colors (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Floating contact buttons (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Reading progress bar (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Back to top (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Custom cursor (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Footer reveal effect (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
			array(
				'feature' => __( 'Maintenance and coming soon mode (Module)', 'lennox' ),
				'free'    => __( 'No', 'lennox' ),
				'pro'     => __( 'Yes', 'lennox' ),
			),
		);
	}
}
