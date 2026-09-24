<?php
/**
 * Public form handling: prayer requests, newsletter signups and "I prayed".
 *
 * The same functions serve the JavaScript path (includes/rest.php) and the
 * no-JavaScript path (admin-post.php handlers at the bottom of this file).
 *
 * Spam and abuse protection: a honeypot field, a time trap (submissions sent
 * less than 3 seconds after the page loaded are treated as bots), per-visitor
 * rate limits keyed by a salted hash of the IP address (the address itself is
 * never stored), and length and format validation.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the user-facing message for a form result code.
 *
 * The theme uses this too, to show results after a no-JavaScript submission.
 *
 * @param string $code Result code, e.g. "prayer_sent" or "rate_limited".
 * @return string
 */
function lampandpath_core_form_message( $code ) {
	$messages = array(
		'prayer_sent'     => __( 'Thank you. Our prayer team will pray for you by name this week.', 'lampandpath-core' ),
		'subscribed'      => __( 'Thank you for subscribing. Your first email arrives on Monday.', 'lampandpath-core' ),
		'prayed'          => __( 'Thank you for praying.', 'lampandpath-core' ),
		'invalid_request' => __( 'Please write your prayer request (3 to 1,000 characters).', 'lampandpath-core' ),
		'invalid_email'   => __( 'Please enter a valid email address.', 'lampandpath-core' ),
		'rate_limited'    => __( 'Too many requests from your connection. Please try again later.', 'lampandpath-core' ),
		'expired'         => __( 'This page has expired. Please reload it and try again.', 'lampandpath-core' ),
		'not_found'       => __( 'That prayer request is no longer on the prayer wall.', 'lampandpath-core' ),
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Something went wrong. Please try again.', 'lampandpath-core' );
}

/**
 * Returns a WP_Error for a form result code, with its message and HTTP status.
 *
 * @param string $code   Result code.
 * @param int    $status HTTP status.
 * @return WP_Error
 */
function lampandpath_core_form_error( $code, $status ) {
	return new WP_Error( 'lampandpath_' . $code, lampandpath_core_form_message( $code ), array( 'status' => $status ) );
}

/**
 * Returns the visitor's IP address as seen by the web server.
 *
 * Proxy headers such as X-Forwarded-For are not trusted by default because
 * anyone can send them; sites behind a trusted proxy can use the filter.
 *
 * @return string
 */
function lampandpath_core_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	return (string) apply_filters( 'lampandpath_client_ip', $ip );
}

/**
 * Runs a callback while holding a named database lock, so concurrent requests take turns.
 *
 * Uses MySQL/MariaDB GET_LOCK, which needs no special privileges. The lock name
 * includes the database and table prefix, so sites sharing a database server
 * never block each other.
 *
 * @param string   $name     What to lock, e.g. a rate-limit key.
 * @param callable $callback Work to do while holding the lock.
 * @param int      $timeout  Seconds to wait for the lock.
 * @return mixed|WP_Error The callback's return value, or an error when the lock was not obtained in time.
 */
function lampandpath_core_with_lock( $name, callable $callback, $timeout = 5 ) {
	global $wpdb;

	$lock = 'lp_' . md5( DB_NAME . $wpdb->prefix . $name );
	if ( 1 !== (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK( %s, %d )', $lock, $timeout ) ) ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return lampandpath_core_form_error( 'error', 503 );
	}

	try {
		return $callback();
	} finally {
		$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK( %s )', $lock ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}
}

/**
 * Counts one attempt for this visitor and reports whether they are over the limit.
 *
 * The check and the count happen under a lock, so parallel requests cannot all
 * read the same count and slip past the limit together.
 *
 * @param string $bucket What is being limited, e.g. "prayer".
 * @param int    $limit  Allowed attempts per window.
 * @param int    $window Window length in seconds.
 * @return bool True when the visitor is over the limit.
 */
function lampandpath_core_rate_limited( $bucket, $limit, $window = HOUR_IN_SECONDS ) {
	/**
	 * Filters a rate limit, e.g. to raise it for a church network where many visitors share one IP address.
	 *
	 * @param int    $limit  Allowed attempts per window.
	 * @param string $bucket What is being limited: prayer, subscribe, prayed or view.
	 */
	$limit   = (int) apply_filters( 'lampandpath_rate_limit', $limit, $bucket );
	$visitor = substr( hash_hmac( 'sha256', lampandpath_core_client_ip(), wp_salt( 'nonce' ) ), 0, 20 );
	$key     = 'lp_rl_' . $bucket . '_' . $visitor;

	$over = lampandpath_core_with_lock(
		$key,
		static function () use ( $key, $limit, $window ) {
			$count = (int) get_transient( $key );
			if ( $count >= $limit ) {
				return true;
			}
			set_transient( $key, $count + 1, $window );
			return false;
		}
	);

	// Fail closed: if the lock could not be taken, treat the visitor as over the limit.
	return is_wp_error( $over ) || $over;
}

/**
 * Checks the honeypot and time trap fields that every public form sends.
 *
 * With JavaScript the browser reports how long the form was open (lp_elapsed,
 * in milliseconds), which stays accurate when the page came from a full-page
 * cache. Without JavaScript the check falls back to the time the page was
 * generated (lp_started), which is only meaningful on uncached pages.
 *
 * @param array $input Submitted fields: "website" (honeypot, must be empty), "lp_elapsed" and "lp_started".
 * @return bool True when the submission looks automated.
 */
function lampandpath_core_is_spam( array $input ) {
	if ( ! empty( $input['website'] ) ) {
		return true;
	}

	if ( isset( $input['lp_elapsed'] ) && '' !== $input['lp_elapsed'] ) {
		return (int) $input['lp_elapsed'] < 3000;
	}

	$started = isset( $input['lp_started'] ) ? (int) $input['lp_started'] : 0;

	return ! $started || time() - $started < 3;
}

/**
 * Saves a prayer request as pending and notifies the moderators.
 *
 * Spam gets a normal-looking success so bots learn nothing, but nothing is saved.
 *
 * @param array $input Fields: first_name, request, wall_consent, website, lp_started.
 * @return int|WP_Error New prayer request ID (0 for filtered spam), or an error.
 */
function lampandpath_core_submit_prayer( array $input ) {
	if ( lampandpath_core_is_spam( $input ) ) {
		return 0;
	}
	if ( lampandpath_core_rate_limited( 'prayer', 5 ) ) {
		return lampandpath_core_form_error( 'rate_limited', 429 );
	}

	$name    = mb_substr( trim( sanitize_text_field( isset( $input['first_name'] ) ? $input['first_name'] : '' ) ), 0, 60 );
	$request = trim( sanitize_textarea_field( isset( $input['request'] ) ? $input['request'] : '' ) );
	$length  = mb_strlen( $request );
	if ( $length < 3 || $length > 1000 ) {
		return lampandpath_core_form_error( 'invalid_request', 400 );
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'lp_prayer',
			'post_status'  => 'pending',
			/* translators: %s: first name of the person asking for prayer. */
			'post_title'   => '' !== $name ? sprintf( __( 'Request from %s', 'lampandpath-core' ), $name ) : __( 'Anonymous request', 'lampandpath-core' ),
			'post_content' => $request,
			'meta_input'   => array(
				'lp_first_name'   => $name,
				'lp_wall_consent' => ! empty( $input['wall_consent'] ),
				'lp_prayed_count' => 0,
			),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return lampandpath_core_form_error( 'error', 500 );
	}

	lampandpath_core_notify_prayer( $id );

	return $id;
}

/**
 * Emails the moderators about a new prayer request.
 *
 * @param int $id Prayer request ID.
 */
function lampandpath_core_notify_prayer( $id ) {
	$to = apply_filters( 'lampandpath_prayer_notify_email', get_option( 'admin_email' ) );
	if ( ! $to ) {
		return;
	}

	$post = get_post( $id );
	/* translators: %s: site title. */
	$subject = sprintf( __( '[%s] New prayer request awaiting approval', 'lampandpath-core' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$lines   = array(
		$post->post_title,
		'',
		$post->post_content,
		'',
		get_post_meta( $id, 'lp_wall_consent', true ) ? __( 'They agreed to show it on the prayer wall.', 'lampandpath-core' ) : __( 'They did not agree to show it on the prayer wall.', 'lampandpath-core' ),
		/* translators: %s: link to review the prayer request in wp-admin. */
		sprintf( __( 'Review it: %s', 'lampandpath-core' ), admin_url( 'post.php?post=' . $id . '&action=edit' ) ),
	);

	wp_mail( $to, $subject, implode( "\n", $lines ) );
}

/**
 * Adds a newsletter subscriber.
 *
 * Existing subscribers get the same success response, so the form does not
 * reveal who is subscribed. Spam also gets a normal-looking success.
 *
 * @param array $input Fields: email, website, lp_started.
 * @return int|WP_Error Subscriber ID (0 for spam or an existing subscriber), or an error.
 */
function lampandpath_core_subscribe( array $input ) {
	if ( lampandpath_core_is_spam( $input ) ) {
		return 0;
	}
	if ( lampandpath_core_rate_limited( 'subscribe', 5 ) ) {
		return lampandpath_core_form_error( 'rate_limited', 429 );
	}

	$email = strtolower( sanitize_email( isset( $input['email'] ) ? $input['email'] : '' ) );
	if ( ! is_email( $email ) || strlen( $email ) > 254 ) {
		return lampandpath_core_form_error( 'invalid_email', 400 );
	}

	// The lookup and insert run under a per-email lock, so parallel signups cannot both create a subscriber.
	$id = lampandpath_core_with_lock(
		'subscriber_' . $email,
		static function () use ( $email ) {
			return lampandpath_core_insert_subscriber( $email );
		}
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return $id;
	}

	/**
	 * Fires after a new newsletter subscriber is saved, e.g. to send them to a mailing provider.
	 *
	 * Runs once per email, outside the lock, so a slow provider call does not hold up other signups.
	 *
	 * @param string $email Subscriber email.
	 * @param int    $id    Subscriber post ID.
	 */
	do_action( 'lampandpath_newsletter_subscribed', $email, $id );

	return $id;
}

/**
 * Saves a subscriber unless the email is already subscribed. Call it under the email's lock.
 *
 * @param string $email Sanitized, lowercased email.
 * @return int|WP_Error New subscriber ID, 0 when the email already exists, or an error.
 */
function lampandpath_core_insert_subscriber( $email ) {
	$existing = get_posts(
		array(
			'post_type'   => 'lp_subscriber',
			'post_status' => 'any',
			'title'       => $email,
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);
	if ( $existing ) {
		return 0;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'lp_subscriber',
			'post_status' => 'publish',
			'post_title'  => $email,
		),
		true
	);

	return is_wp_error( $id ) ? lampandpath_core_form_error( 'error', 500 ) : (int) $id;
}

/**
 * Counts one "I prayed" for a request on the prayer wall.
 *
 * @param int $id Prayer request ID.
 * @return int|WP_Error New prayed count, or an error.
 */
function lampandpath_core_record_prayed( $id ) {
	$post = get_post( $id );
	if ( ! $post || 'lp_prayer' !== $post->post_type || 'publish' !== $post->post_status || ! get_post_meta( $id, 'lp_wall_consent', true ) ) {
		return lampandpath_core_form_error( 'not_found', 404 );
	}
	if ( lampandpath_core_rate_limited( 'prayed', 60 ) ) {
		return lampandpath_core_form_error( 'rate_limited', 429 );
	}

	return lampandpath_core_increment_meta( $id, 'lp_prayed_count' );
}

/**
 * Returns the result code of a form result, e.g. "invalid_email" for a WP_Error.
 *
 * @param int|WP_Error $result  Result of a form function.
 * @param string       $success Code to use on success.
 * @return string
 */
function lampandpath_core_result_code( $result, $success ) {
	return is_wp_error( $result ) ? preg_replace( '/^lampandpath_/', '', $result->get_error_code() ) : $success;
}

/**
 * Handles a form posted without JavaScript and redirects back with a result code.
 *
 * @param string $form     Form key: "prayer" or "subscribe".
 * @param string $fragment Anchor to return to, e.g. "prayer".
 */
function lampandpath_core_handle_form_post( $form, $fragment ) {
	$nonce = isset( $_POST[ 'lampandpath_' . $form . '_nonce' ] ) ? sanitize_key( wp_unslash( $_POST[ 'lampandpath_' . $form . '_nonce' ] ) ) : '';
	$input = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each field is sanitized by the form function.
	// The nonce is only checked for logged-in users, the only visitors a forged cross-site
	// submission could act for; their pages also bypass full-page caches. Logged-out visitors
	// all share one public nonce, which protects nothing and would go stale inside cached pages.
	$valid  = ! is_user_logged_in() || wp_verify_nonce( $nonce, 'lampandpath_' . $form );
	$result = ! $valid
		? lampandpath_core_form_error( 'expired', 403 )
		: ( 'prayer' === $form ? lampandpath_core_submit_prayer( $input ) : lampandpath_core_subscribe( $input ) );

	$code     = lampandpath_core_result_code( $result, 'prayer' === $form ? 'prayer_sent' : 'subscribed' );
	$referer  = wp_get_referer();
	$redirect = remove_query_arg( 'lp_' . $form, $referer ? $referer : home_url( '/' ) );

	wp_safe_redirect( add_query_arg( 'lp_' . $form, $code, $redirect ) . '#' . $fragment );
	exit;
}

/**
 * Handles the prayer form posted without JavaScript.
 */
function lampandpath_core_handle_prayer_post() {
	lampandpath_core_handle_form_post( 'prayer', 'prayer' );
}
add_action( 'admin_post_nopriv_lampandpath_prayer', 'lampandpath_core_handle_prayer_post' );
add_action( 'admin_post_lampandpath_prayer', 'lampandpath_core_handle_prayer_post' );

/**
 * Handles the newsletter form posted without JavaScript.
 */
function lampandpath_core_handle_subscribe_post() {
	lampandpath_core_handle_form_post( 'subscribe', 'newsletter' );
}
add_action( 'admin_post_nopriv_lampandpath_subscribe', 'lampandpath_core_handle_subscribe_post' );
add_action( 'admin_post_lampandpath_subscribe', 'lampandpath_core_handle_subscribe_post' );
