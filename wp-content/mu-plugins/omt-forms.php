<?php
/**
 * Plugin Name: One More Tree — Forms
 * Description: Dependency-free newsletter signup and contact form handling. Validates submissions, stores them as private leads, and renders status notices. Forms are authored as ordinary HTML blocks; the CSRF nonce and request metadata are injected at render time so they are never stale.
 * Version: 1.0.0
 * Author: One More Tree
 *
 * Loaded as a must-use plugin so it cannot be deactivated by accident.
 *
 * Design notes
 * ------------
 * Forms live in page content as plain HTML (a core/html block), which keeps them
 * fully editable in the block editor and avoids registering block types that a
 * given WordPress version might render as "unsupported".
 *
 * Two markers drive the dynamic behaviour, both resolved on the front end:
 *   <span data-omt-fields="KIND"></span>   -> action, nonce, timestamp, redirect
 *   <div  data-omt-notice="KIND"></div>    -> success / error message, if any
 *
 * @package OneMoreTree
 */

defined( 'ABSPATH' ) || exit;

const OMT_FORM_ACTION       = 'omt_form';
const OMT_LEAD_POST_TYPE    = 'omt_lead';
const OMT_FIELD_MARKER      = 'data-omt-fields';
const OMT_NOTICE_MARKER     = 'data-omt-notice';
const OMT_RATE_LIMIT_MAX    = 6;    // submissions allowed…
const OMT_RATE_LIMIT_WINDOW = 3600; // …per hour, per IP.
const OMT_MIN_SECONDS       = 2;    // fastest a human can realistically submit.

/* -------------------------------------------------------------------------
 * 1. Storage — private lead post type
 * ---------------------------------------------------------------------- */

/**
 * Register the post type every submission is stored as.
 */
function omt_register_lead_type() {
	register_post_type(
		OMT_LEAD_POST_TYPE,
		array(
			'labels'          => array(
				'name'          => __( 'Form submissions', 'omt' ),
				'singular_name' => __( 'Form submission', 'omt' ),
				'menu_name'     => __( 'Submissions', 'omt' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}
add_action( 'init', 'omt_register_lead_type', 5 );

/**
 * Add readable admin columns.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function omt_lead_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['omt_kind']    = __( 'Form', 'omt' );
			$new['omt_email']   = __( 'Email', 'omt' );
			$new['omt_topic']   = __( 'Topic', 'omt' );
			$new['omt_message'] = __( 'Message', 'omt' );
		}
	}
	return $new;
}
add_filter( 'manage_' . OMT_LEAD_POST_TYPE . '_posts_columns', 'omt_lead_columns' );

/**
 * Render the custom admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function omt_lead_column_content( $column, $post_id ) {
	$meta_key = '_omt_' . str_replace( 'omt_', '', $column );
	$value    = get_post_meta( $post_id, $meta_key, true );

	if ( 'message' === $column ) {
		$value = wp_trim_words( (string) $value, 14 );
	}

	echo esc_html( (string) $value );
}
add_action( 'manage_' . OMT_LEAD_POST_TYPE . '_posts_custom_column', 'omt_lead_column_content', 10, 2 );

/* -------------------------------------------------------------------------
 * 2. Allow the form markup through kses
 * ---------------------------------------------------------------------- */

/**
 * Permit form elements in post content.
 *
 * WordPress does not allow <form>/<input> in post content by default, which
 * would strip the forms whenever a user without unfiltered_html saves a page.
 * Only the tags and attributes the forms actually need are added.
 *
 * @param array  $tags    Allowed tags.
 * @param string $context Sanitisation context.
 * @return array
 */
function omt_allow_form_markup( $tags, $context ) {
	if ( 'post' !== $context ) {
		return $tags;
	}

	$global = array(
		'class'       => true,
		'id'          => true,
		'name'        => true,
		'style'       => true,
		'data-omt-fields' => true,
		'data-omt-notice' => true,
	);

	$tags['form'] = array_merge(
		$global,
		array(
			'action'  => true,
			'method'  => true,
			'novalidate' => true,
			'target'  => true,
			'enctype' => true,
		)
	);

	$tags['input'] = array_merge(
		$global,
		array(
			'type'         => true,
			'value'        => true,
			'placeholder'  => true,
			'required'     => true,
			'checked'      => true,
			'readonly'     => true,
			'disabled'     => true,
			'min'          => true,
			'max'          => true,
			'step'         => true,
			'maxlength'    => true,
			'autocomplete' => true,
			'tabindex'     => true,
			'aria-label'   => true,
			'aria-hidden'  => true,
			'size'         => true,
			'pattern'      => true,
		)
	);

	$tags['select'] = array_merge(
		$global,
		array(
			'required'   => true,
			'multiple'   => true,
			'autocomplete' => true,
			'aria-label' => true,
		)
	);

	$tags['option'] = array(
		'value'    => true,
		'selected' => true,
		'label'    => true,
	);

	$tags['textarea'] = array_merge(
		$global,
		array(
			'rows'         => true,
			'cols'         => true,
			'placeholder'  => true,
			'required'     => true,
			'maxlength'    => true,
			'autocomplete' => true,
			'aria-label'   => true,
		)
	);

	$tags['label'] = array_merge(
		$global,
		array(
			'for' => true,
		)
	);

	$tags['fieldset'] = $global;
	$tags['legend']   = $global;

	// <button> is already allowed, but make sure the type attribute is.
	if ( isset( $tags['button'] ) && is_array( $tags['button'] ) ) {
		$tags['button']['type'] = true;
	} else {
		$tags['button'] = array_merge( $global, array( 'type' => true, 'value' => true ) );
	}

	$tags['span'] = isset( $tags['span'] ) && is_array( $tags['span'] )
		? array_merge( $tags['span'], array( 'data-omt-fields' => true, 'data-omt-notice' => true ) )
		: $global;

	$tags['div'] = isset( $tags['div'] ) && is_array( $tags['div'] )
		? array_merge( $tags['div'], array( 'data-omt-fields' => true, 'data-omt-notice' => true ) )
		: $global;

	return $tags;
}
add_filter( 'wp_kses_allowed_html', 'omt_allow_form_markup', 10, 2 );

/* -------------------------------------------------------------------------
 * 3. Shared helpers
 * ---------------------------------------------------------------------- */

/**
 * Current request IP, hashed — raw addresses are never persisted.
 *
 * @return string
 */
function omt_client_hash() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return substr( wp_hash( $ip . '|omt' ), 0, 20 );
}

/**
 * Where a submission should send the visitor back to.
 *
 * Only same-host URLs are honoured, so this cannot be used as an open redirect.
 *
 * @return string
 */
function omt_resolve_redirect() {
	$raw = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
	$url = wp_validate_redirect( $raw, home_url( '/' ) );

	// Drop previous status flags so they cannot accumulate.
	return remove_query_arg( array( 'omt', 'omt_f', 'omt_m' ), $url );
}

/**
 * Redirect back to the originating page with a machine-readable status.
 *
 * @param string $status ok|error.
 * @param string $form   newsletter|contact.
 * @param string $code   Error code used to pick the message.
 */
function omt_finish( $status, $form, $code = '' ) {
	$args = array(
		'omt'   => $status,
		'omt_f' => $form,
	);
	if ( $code ) {
		$args['omt_m'] = $code;
	}

	wp_safe_redirect( add_query_arg( $args, omt_resolve_redirect() ) . '#omt-form-' . $form );
	exit;
}

/* -------------------------------------------------------------------------
 * 4. Submission handling
 * ---------------------------------------------------------------------- */

/**
 * Validate a posted form, store it, and redirect back with the outcome.
 */
function omt_handle_submission() {
	$kind = isset( $_POST['omt_form'] ) ? sanitize_key( wp_unslash( $_POST['omt_form'] ) ) : '';

	if ( ! in_array( $kind, array( 'newsletter', 'contact' ), true ) ) {
		omt_finish( 'error', 'newsletter', 'unknown' );
	}

	// CSRF — injected at render time, so it can never be stale.
	$nonce = isset( $_POST['_omt_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_omt_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'omt_form_' . $kind ) ) {
		omt_finish( 'error', $kind, 'expired' );
	}

	// Honeypot — hidden field that only automated submissions fill in.
	if ( ! empty( $_POST['omt_hp'] ) ) {
		omt_finish( 'error', $kind, 'rejected' );
	}

	// Time trap — faster than a human can type means automated.
	$ts = isset( $_POST['omt_ts'] ) ? absint( $_POST['omt_ts'] ) : 0;
	if ( $ts && ( time() - $ts ) < OMT_MIN_SECONDS ) {
		omt_finish( 'error', $kind, 'rejected' );
	}

	// Rate limit per IP.
	$rate_key = 'omt_rl_' . omt_client_hash();
	$hits     = (int) get_transient( $rate_key );
	if ( $hits >= OMT_RATE_LIMIT_MAX ) {
		omt_finish( 'error', $kind, 'rate' );
	}
	set_transient( $rate_key, $hits + 1, OMT_RATE_LIMIT_WINDOW );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		omt_finish( 'error', $kind, 'email' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$topic   = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( 'contact' === $kind ) {
		if ( '' === trim( $name ) ) {
			omt_finish( 'error', $kind, 'name' );
		}
		if ( strlen( trim( $message ) ) < 10 ) {
			omt_finish( 'error', $kind, 'message' );
		}
	}

	$referer = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
	$source  = $referer ? (string) wp_parse_url( $referer, PHP_URL_PATH ) : '';

	$title = 'newsletter' === $kind
		? sprintf( 'Newsletter — %s', $email )
		: sprintf( 'Contact — %s', $name );

	$lead_id = wp_insert_post(
		array(
			'post_type'   => OMT_LEAD_POST_TYPE,
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);

	if ( is_wp_error( $lead_id ) ) {
		omt_finish( 'error', $kind, 'store' );
	}

	update_post_meta( $lead_id, '_omt_kind', $kind );
	update_post_meta( $lead_id, '_omt_email', $email );
	update_post_meta( $lead_id, '_omt_name', $name );
	update_post_meta( $lead_id, '_omt_topic', $topic );
	update_post_meta( $lead_id, '_omt_message', $message );
	update_post_meta( $lead_id, '_omt_source', $source );
	update_post_meta( $lead_id, '_omt_ip_hash', omt_client_hash() );
	update_post_meta(
		$lead_id,
		'_omt_ua',
		isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 250 ) : ''
	);

	/**
	 * Fires after a submission is stored.
	 *
	 * Hook here to forward leads to a CRM or a transactional email provider. This
	 * install has no mail transport configured, so nothing is emailed by default.
	 *
	 * @param int    $lead_id Lead post ID.
	 * @param string $kind    newsletter|contact.
	 * @param array  $data    Sanitised submission data.
	 */
	do_action( 'omt_lead_stored', $lead_id, $kind, compact( 'email', 'name', 'topic', 'message', 'source' ) );

	omt_finish( 'ok', $kind );
}
add_action( 'admin_post_' . OMT_FORM_ACTION, 'omt_handle_submission' );
add_action( 'admin_post_nopriv_' . OMT_FORM_ACTION, 'omt_handle_submission' );

/* -------------------------------------------------------------------------
 * 5. Render-time injection
 * ---------------------------------------------------------------------- */

/**
 * Human-readable message for an outcome.
 *
 * @param string $status ok|error.
 * @param string $code   Error code.
 * @return string
 */
function omt_notice_text( $status, $code ) {
	if ( 'ok' === $status ) {
		return __( 'Thank you — your message is with us. We reply within two working days.', 'omt' );
	}

	$messages = array(
		'expired'  => __( 'That form had timed out. Please try again.', 'omt' ),
		'rejected' => __( 'That submission looked automated, so we did not accept it. Please try again.', 'omt' ),
		'rate'     => __( 'Too many submissions from this connection. Please try again a little later.', 'omt' ),
		'email'    => __( 'Please enter a valid email address.', 'omt' ),
		'name'     => __( 'Please tell us your name.', 'omt' ),
		'message'  => __( 'Please add a few more details — at least 10 characters.', 'omt' ),
		'store'    => __( 'Something went wrong saving your message. Please email us directly.', 'omt' ),
		'unknown'  => __( 'We could not recognise that form. Please try again.', 'omt' ),
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['unknown'];
}

/**
 * Notice markup for a form, or an empty string when there is nothing to say.
 *
 * @param string $kind newsletter|contact.
 * @return string
 */
function omt_notice_markup( $kind ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display of a status flag.
	if ( ! isset( $_GET['omt'], $_GET['omt_f'] ) ) {
		return '';
	}

	$status = sanitize_key( wp_unslash( $_GET['omt'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$form   = sanitize_key( wp_unslash( $_GET['omt_f'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$code   = isset( $_GET['omt_m'] ) ? sanitize_key( wp_unslash( $_GET['omt_m'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $form !== $kind || ! in_array( $status, array( 'ok', 'error' ), true ) ) {
		return '';
	}

	return sprintf(
		'<p class="omt-form__notice omt-form__notice--%1$s" role="status">%2$s</p>',
		esc_attr( $status ),
		esc_html( omt_notice_text( $status, $code ) )
	);
}

/**
 * Hidden inputs that make the form submittable and CSRF-safe.
 *
 * @param string $kind newsletter|contact.
 * @return string
 */
function omt_hidden_fields_markup( $kind ) {
	$redirect = is_singular() ? get_permalink() : home_url( '/' );

	return sprintf(
		'<input type="hidden" name="action" value="%1$s" />' .
		'<input type="hidden" name="omt_form" value="%2$s" />' .
		'<input type="hidden" name="redirect_to" value="%3$s" />' .
		'<input type="hidden" name="omt_ts" value="%4$s" />' .
		'%5$s',
		esc_attr( OMT_FORM_ACTION ),
		esc_attr( $kind ),
		esc_url( $redirect ),
		esc_attr( (string) time() ),
		wp_nonce_field( 'omt_form_' . $kind, '_omt_nonce', true, false )
	);
}

/**
 * Swap the authoring placeholders for live values.
 *
 * Both placeholders are plain elements in the page content:
 *   <span data-omt-fields="kind"></span>  and  <div data-omt-notice="kind"></div>
 *
 * @param string $html Block output.
 * @return string
 */
function omt_inject_form_parts( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return $html;
	}
	if ( false === strpos( $html, OMT_FIELD_MARKER ) && false === strpos( $html, OMT_NOTICE_MARKER ) ) {
		return $html;
	}

	// Live hidden fields.
	$html = preg_replace_callback(
		'#<(span|div)\b[^>]*\b' . preg_quote( OMT_FIELD_MARKER, '#' ) . '=(["\'])(?<kind>[a-z_\-]+)\2[^>]*>\s*</\1>#i',
		static function ( $matches ) {
			$kind = sanitize_key( $matches['kind'] );
			if ( ! in_array( $kind, array( 'newsletter', 'contact' ), true ) ) {
				return '';
			}
			return omt_hidden_fields_markup( $kind );
		},
		$html
	);

	// Status notice, if this request produced one.
	$html = preg_replace_callback(
		'#<(span|div)\b[^>]*\b' . preg_quote( OMT_NOTICE_MARKER, '#' ) . '=(["\'])(?<kind>[a-z_\-]+)\2[^>]*>\s*</\1>#i',
		static function ( $matches ) {
			$kind = sanitize_key( $matches['kind'] );
			if ( ! in_array( $kind, array( 'newsletter', 'contact' ), true ) ) {
				return '';
			}
			return omt_notice_markup( $kind );
		},
		$html
	);

	return $html;
}
add_filter( 'render_block', 'omt_inject_form_parts', 20 );

/* -------------------------------------------------------------------------
 * 6. Front-end styling
 * ---------------------------------------------------------------------- */

/**
 * Enqueue the form stylesheet on the front end.
 *
 * Forms can live in page content, in the footer template part or in the
 * front-page template, so there is no reliable single place to detect them
 * before rendering. The stylesheet is small and cacheable, so it is served
 * whenever a front-end page is built (but never for feeds or embeds).
 */
function omt_form_styles() {
	if ( is_feed() || is_embed() || is_robots() ) {
		return;
	}

	$css = WPMU_PLUGIN_DIR . '/omt-forms.css';

	wp_enqueue_style(
		'omt-forms',
		WPMU_PLUGIN_URL . '/omt-forms.css',
		array(),
		file_exists( $css ) ? (string) filemtime( $css ) : '1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'omt_form_styles' );
