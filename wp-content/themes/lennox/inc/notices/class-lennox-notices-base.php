<?php
/**
 * Base class for Lennox admin notices
 *
 * @package Lennox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lennox_Notices_Base class.
 *
 * Base class for creating dismissible admin notices with optional expiration.
 */
abstract class Lennox_Notices_Base {

	/**
	 * Unique identifier for the notice
	 *
	 * @var string
	 */
	protected $notice_id;

	/**
	 * The notification message content
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Notice type (info, success, warning, error)
	 *
	 * @var string
	 */
	protected $type = 'info';

	/**
	 * Whether the notice can be dismissed
	 *
	 * @var bool
	 */
	protected $dismissible = true;

	/**
	 * Whether the notice supports "remind me later" functionality
	 *
	 * @var bool
	 */
	protected $remind_later = false;

	/**
	 * Expiration date in Y-m-d H:i:s format
	 *
	 * @var string|null
	 */
	protected $end_date = null;

	/**
	 * Required user capability to see the notice
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Primary button text
	 *
	 * @var string|null
	 */
	protected $button_text = null;

	/**
	 * Primary button URL
	 *
	 * @var string|null
	 */
	protected $button_url = null;

	/**
	 * Whether the button should open in a new tab
	 *
	 * @var bool
	 */
	protected $button_new_tab = false;

	/**
	 * Constructor
	 *
	 * @param array $args Configuration array.
	 */
	public function __construct( array $args ) {
		// Validate required properties.
		if ( empty( $args['notice_id'] ) ) {
			wp_die( esc_html__( 'Notice ID is required', 'lennox' ) );
		}
		if ( empty( $args['message'] ) ) {
			wp_die( esc_html__( 'Notice message is required', 'lennox' ) );
		}

		// Set required properties.
		$this->notice_id = sanitize_key( $args['notice_id'] );
		$this->message   = $args['message'];

		// Set optional properties with defaults.
		if ( isset( $args['type'] ) ) {
			$this->type = sanitize_key( $args['type'] );
		}
		if ( isset( $args['dismissible'] ) ) {
			$this->dismissible = (bool) $args['dismissible'];
		}
		if ( isset( $args['remind_later'] ) ) {
			$this->remind_later = (bool) $args['remind_later'];
		}
		if ( isset( $args['end_date'] ) ) {
			$this->end_date = $args['end_date'];
		}
		if ( isset( $args['capability'] ) ) {
			$this->capability = $args['capability'];
		}
		if ( isset( $args['button_text'] ) ) {
			$this->button_text = $args['button_text'];
		}
		if ( isset( $args['button_url'] ) ) {
			$this->button_url = $args['button_url'];
		}
		if ( isset( $args['button_new_tab'] ) ) {
			$this->button_new_tab = (bool) $args['button_new_tab'];
		}
	}

	/**
	 * Check if notice should be displayed
	 *
	 * @return bool
	 */
	public function should_display() {
		// Check user capability.
		if ( ! current_user_can( $this->capability ) ) {
			return false;
		}

		// Check dismissal status.
		if ( $this->is_dismissed() ) {
			return false;
		}

		// Check expiration.
		if ( $this->is_expired() ) {
			return false;
		}

		return true;
	}

	/**
	 * Render the admin notice
	 *
	 * @return void
	 */
	public function render() {
		if ( ! $this->should_display() ) {
			return;
		}

		$classes = $this->get_notice_classes();
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<p><?php echo wp_kses_post( $this->message ); ?></p>
			<?php if ( $this->button_text && $this->button_url ) : ?>
				<p>
					<a href="<?php echo esc_url( $this->button_url ); ?>" class="button button-primary"<?php echo $this->button_new_tab ? ' target="_blank"' : ''; ?>>
						<?php echo esc_html( $this->button_text ); ?>
					</a>
				</p>
			<?php endif; ?>
			<?php if ( $this->dismissible ) : ?>
				<p>
					<a href="<?php echo esc_url( $this->get_dismiss_url( 'dismiss' ) ); ?>">
						<?php esc_html_e( 'Dismiss', 'lennox' ); ?>
					</a>
					<?php if ( $this->remind_later ) : ?>
						| <a href="<?php echo esc_url( $this->get_dismiss_url( 'remind' ) ); ?>">
							<?php esc_html_e( 'Remind me later', 'lennox' ); ?>
						</a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Check if notice has been dismissed
	 *
	 * @return bool
	 */
	public function is_dismissed() {
		$user_id   = get_current_user_id();
		$meta_key  = $this->notice_id . '_dismiss';
		$dismissed = get_user_meta( $user_id, $meta_key, true );

		// Check for permanent dismissal.
		if ( 'true' === $dismissed ) {
			return true;
		}

		// Check for remind later (stored as timestamp).
		if ( is_numeric( $dismissed ) ) {
			$remind_until = (int) $dismissed;
			if ( current_time( 'timestamp' ) < $remind_until ) {
				return true;
			}
			// Remind period has passed, delete the meta.
			delete_user_meta( $user_id, $meta_key );
		}

		return false;
	}

	/**
	 * Dismiss the notice
	 *
	 * @param string $action Either 'dismiss' or 'remind'.
	 * @return void
	 */
	public function dismiss( $action = 'dismiss' ) {
		$user_id  = get_current_user_id();
		$meta_key = $this->notice_id . '_dismiss';

		if ( 'remind' === $action ) {
			// Store timestamp for 1 week from now.
			$remind_until = current_time( 'timestamp' ) + ( 7 * DAY_IN_SECONDS );
			update_user_meta( $user_id, $meta_key, $remind_until );
		} else {
			// Permanent dismissal.
			update_user_meta( $user_id, $meta_key, 'true' );
		}
	}

	/**
	 * Check if notice has expired
	 *
	 * @return bool
	 */
	public function is_expired() {
		if ( null === $this->end_date ) {
			return false;
		}

		$end_timestamp     = strtotime( $this->end_date );
		$current_timestamp = current_time( 'timestamp' );

		return $current_timestamp > $end_timestamp;
	}

	/**
	 * Register WordPress hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_notices', array( $this, 'render' ) );
		add_action( 'admin_init', array( $this, 'handle_dismiss' ) );
	}

	/**
	 * Handle dismiss action via GET parameters
	 *
	 * @return void
	 */
	public function handle_dismiss() {
		// Check for dismiss parameter.
		$dismiss_param = $this->notice_id . '_dismiss';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET[ $dismiss_param ] ) ) {
			return;
		}

		// Verify nonce.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $dismiss_param ) ) {
			return;
		}

		// Verify user permissions.
		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		// Get action type (dismiss or remind).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = isset( $_GET[ $dismiss_param ] ) ? sanitize_text_field( wp_unslash( $_GET[ $dismiss_param ] ) ) : 'dismiss';

		// Dismiss the notice.
		$this->dismiss( $action );

		// Redirect to clean URL.
		$redirect_url = remove_query_arg( array( $dismiss_param, '_wpnonce' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Get CSS classes for the notice
	 *
	 * @return string
	 */
	protected function get_notice_classes() {
		$classes = array( 'notice' );

		// Add type class.
		$classes[] = 'notice-' . $this->type;

		// Add dismissible class if applicable.
		if ( $this->dismissible ) {
			$classes[] = 'is-dismissible';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get dismiss URL with nonce
	 *
	 * @param string $action Either 'dismiss' or 'remind'.
	 * @return string
	 */
	protected function get_dismiss_url( $action = 'dismiss' ) {
		$dismiss_param = $this->notice_id . '_dismiss';
		$url           = add_query_arg( $dismiss_param, $action );
		$url           = wp_nonce_url( $url, $dismiss_param );

		return $url;
	}
}
