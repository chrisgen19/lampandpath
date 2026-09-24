<?php
/**
 * Comments are switched off site-wide (issue #1, open question 2).
 *
 * No content accepts comments or pingbacks, existing comments stay hidden
 * (in templates, feeds and the REST API), and the Comments screens leave wp-admin. The prayer wall is the site's
 * place for readers to respond. Remove this file's require in
 * lampandpath-core.php to turn comments back on.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Removes comment and trackback support from every post type, which also
 * removes the Discussion panel and the comment columns in wp-admin.
 */
function lampandpath_core_remove_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'lampandpath_core_remove_comment_support', 100 );

// Closes comments and pings everywhere, including posts that were saved with them open.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

// Hides comments made before they were switched off, and the comments feed link.
add_filter( 'comments_array', '__return_empty_array', 20 );
add_filter( 'get_comments_number', '__return_zero', 20 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/**
 * Answers comment feeds (/comments/feed/ and each article's /feed/) with a 404,
 * so earlier comments are not served there either.
 */
function lampandpath_core_block_comment_feeds() {
	if ( is_comment_feed() ) {
		wp_die( esc_html__( 'Comments are switched off on this site.', 'lampandpath-core' ), '', array( 'response' => 404 ) );
	}
}
add_action( 'template_redirect', 'lampandpath_core_block_comment_feeds', 1 );

/**
 * Answers REST API requests for comments with a 404, except block editor notes.
 *
 * Notes (editors' comments on blocks) are stored as comments of type "note"
 * and use the same /wp/v2/comments routes, so the routes stay and core's own
 * permission checks still apply to notes.
 *
 * @param WP_REST_Response|WP_Error|mixed $response Result so far; usually empty.
 * @param array                           $handler  Route handler (unused).
 * @param WP_REST_Request                 $request  Request.
 * @return WP_REST_Response|WP_Error|mixed
 */
function lampandpath_core_block_rest_comments( $response, $handler, $request ) {
	if ( 0 !== strpos( $request->get_route(), '/wp/v2/comments' ) ) {
		return $response;
	}

	// A single comment's type comes from the comment; a list's from the "type" parameter (default "comment").
	$comment = $request['id'] ? get_comment( (int) $request['id'] ) : null;
	$type    = $comment ? $comment->comment_type : $request['type'];
	if ( 'note' === $type || ( $request['id'] && ! $comment ) ) {
		return $response;
	}

	return new WP_Error( 'rest_comments_disabled', __( 'Comments are switched off on this site.', 'lampandpath-core' ), array( 'status' => 404 ) );
}
add_filter( 'rest_request_before_callbacks', 'lampandpath_core_block_rest_comments', 10, 3 );

/**
 * Removes the Comments menu from wp-admin.
 */
function lampandpath_core_remove_comments_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'lampandpath_core_remove_comments_menu' );

/**
 * Sends visits to the Comments screens back to the dashboard.
 */
function lampandpath_core_redirect_comments_screens() {
	global $pagenow;

	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'lampandpath_core_redirect_comments_screens' );

/**
 * Removes the comments counter from the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar.
 */
function lampandpath_core_remove_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'lampandpath_core_remove_comments_admin_bar', 100 );
