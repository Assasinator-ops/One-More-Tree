<?php
/**
 * Review notice class for Lennox
 *
 * @package Lennox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lennox_Review_Notice class.
 *
 * Displays a review request notice after the theme has been active for 14 days.
 */
class Lennox_Review_Notice extends Lennox_Notices_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$review_url = 'https://wordpress.org/support/theme/lennox/reviews/#new-post';

		parent::__construct(
			array(
				'notice_id'      => 'lennox_review_request',
				'message'        => sprintf(
					/* translators: %s: Theme name. */
					__( 'You\'ve been using %s for a while now! We truly hope it helps! If you\'re enjoying the theme, would you mind leaving us a review?', 'lennox' ),
					'<strong>Lennox</strong>'
				),
				'type'           => 'info',
				'dismissible'    => true,
				'remind_later'   => true,
				'end_date'       => null,
				'capability'     => 'manage_options',
				'button_text'    => __( 'Leave a Review', 'lennox' ),
				'button_url'     => $review_url,
				'button_new_tab' => true,
			)
		);
	}

	/**
	 * Check if notice should be displayed
	 *
	 * Overrides parent method to add activation date check.
	 *
	 * @return bool
	 */
	public function should_display() {
		// First check parent conditions.
		if ( ! parent::should_display() ) {
			return false;
		}

		// Check if theme has been active for at least 14 days.
		$activation_date = get_option( 'lennox_installed_time' );

		if ( ! $activation_date ) {
			return false;
		}

		$days_active = ( current_time( 'timestamp' ) - $activation_date ) / DAY_IN_SECONDS;

		return $days_active >= 14;
	}
}
