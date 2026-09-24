<?php
/**
 * REST API routes (namespace lampandpath/v1) for the public forms and counters.
 *
 * - GET  /token                 Fresh token for the write routes (never cached).
 * - POST /prayers               Prayer request (saved as pending).
 * - POST /prayers/<id>/prayed   "I prayed" on an approved, shared request.
 * - POST /subscribers           Newsletter signup.
 * - POST /views/<id>            Article view beacon (bots and repeats are ignored).
 *
 * Write routes need the token in an X-LP-Token header. The theme fetches it
 * right before submitting, so a page served from a cache never sends an
 * expired one. The articles listing route lives in the theme because it
 * returns rendered cards.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the plugin's REST routes.
 */
function lampandpath_core_register_rest_routes() {
	$namespace = 'lampandpath/v1';
	$spam_args = array(
		'website'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'lp_started' => array(
			'type'    => 'integer',
			'default' => 0,
		),
	);

	register_rest_route(
		$namespace,
		'/token',
		array(
			'methods'             => 'GET',
			'callback'            => 'lampandpath_core_rest_token',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		$namespace,
		'/prayers',
		array(
			'methods'             => 'POST',
			'callback'            => 'lampandpath_core_rest_prayer',
			'permission_callback' => 'lampandpath_core_rest_check_token',
			'args'                => $spam_args + array(
				'first_name'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'request'      => array(
					'type'    => 'string',
					'default' => '',
				),
				'wall_consent' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
		)
	);

	register_rest_route(
		$namespace,
		'/prayers/(?P<id>\d+)/prayed',
		array(
			'methods'             => 'POST',
			'callback'            => 'lampandpath_core_rest_prayed',
			'permission_callback' => 'lampandpath_core_rest_check_token',
		)
	);

	register_rest_route(
		$namespace,
		'/subscribers',
		array(
			'methods'             => 'POST',
			'callback'            => 'lampandpath_core_rest_subscribe',
			'permission_callback' => 'lampandpath_core_rest_check_token',
			'args'                => $spam_args + array(
				'email' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		)
	);

	register_rest_route(
		$namespace,
		'/views/(?P<id>\d+)',
		array(
			'methods'             => 'POST',
			'callback'            => 'lampandpath_core_rest_view',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'lampandpath_core_register_rest_routes' );

/**
 * Returns a fresh token for the write routes.
 *
 * @return WP_REST_Response
 */
function lampandpath_core_rest_token() {
	$response = rest_ensure_response( array( 'token' => wp_create_nonce( 'lampandpath_public' ) ) );
	$response->header( 'Cache-Control', 'no-store, max-age=0' );

	return $response;
}

/**
 * Checks the X-LP-Token header on write routes.
 *
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function lampandpath_core_rest_check_token( WP_REST_Request $request ) {
	$token = (string) $request->get_header( 'x-lp-token' );

	return ( '' !== $token && wp_verify_nonce( $token, 'lampandpath_public' ) ) ? true : lampandpath_core_form_error( 'expired', 403 );
}

/**
 * Builds a success response with a result code and its message.
 *
 * @param string $code   Result code.
 * @param int    $status HTTP status.
 * @param array  $extra  Extra response fields.
 * @return WP_REST_Response
 */
function lampandpath_core_rest_message( $code, $status = 200, array $extra = array() ) {
	return new WP_REST_Response(
		array(
			'code'    => $code,
			'message' => lampandpath_core_form_message( $code ),
		) + $extra,
		$status
	);
}

/**
 * Handles POST /prayers.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function lampandpath_core_rest_prayer( WP_REST_Request $request ) {
	$result = lampandpath_core_submit_prayer( $request->get_params() );

	return is_wp_error( $result ) ? $result : lampandpath_core_rest_message( 'prayer_sent', 201 );
}

/**
 * Handles POST /prayers/<id>/prayed.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function lampandpath_core_rest_prayed( WP_REST_Request $request ) {
	$result = lampandpath_core_record_prayed( (int) $request['id'] );

	return is_wp_error( $result ) ? $result : lampandpath_core_rest_message( 'prayed', 200, array( 'count' => $result ) );
}

/**
 * Handles POST /subscribers.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function lampandpath_core_rest_subscribe( WP_REST_Request $request ) {
	$result = lampandpath_core_subscribe( $request->get_params() );

	return is_wp_error( $result ) ? $result : lampandpath_core_rest_message( 'subscribed', 201 );
}

/**
 * Handles POST /views/<id>: counts a view of a published article.
 *
 * Bots and visitors over the rate limit get the same empty response, but are not counted.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function lampandpath_core_rest_view( WP_REST_Request $request ) {
	$post = get_post( (int) $request['id'] );
	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return new WP_Error( 'lampandpath_invalid_article', __( 'Views are only counted for published articles.', 'lampandpath-core' ), array( 'status' => 404 ) );
	}

	$agent  = (string) $request->get_header( 'user-agent' );
	$is_bot = '' === $agent || preg_match( '/bot|crawl|spider|slurp|preview|facebookexternalhit|headless/i', $agent );
	if ( ! $is_bot && ! lampandpath_core_rate_limited( 'view', 300 ) ) {
		lampandpath_core_record_view( $post->ID );
	}

	return new WP_REST_Response( null, 204 );
}
