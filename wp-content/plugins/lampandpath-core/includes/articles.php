<?php
/**
 * Article data: reading time and monthly view counts.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Counts the words in post content, ignoring markup and shortcodes.
 *
 * @param string $content Post content.
 * @return int
 */
function lampandpath_core_count_words( $content ) {
	$text = trim( wp_strip_all_tags( strip_shortcodes( $content ) ) );

	return '' === $text ? 0 : count( preg_split( '/\s+/u', $text ) );
}

/**
 * Calculates reading time in minutes from post content (at least 1 minute).
 *
 * @param string $content Post content.
 * @return int
 */
function lampandpath_core_calculate_reading_time( $content ) {
	$words_per_minute = max( 1, (int) apply_filters( 'lampandpath_words_per_minute', 200 ) );

	return max( 1, (int) ceil( lampandpath_core_count_words( $content ) / $words_per_minute ) );
}

/**
 * Stores the calculated reading time whenever an article is saved.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function lampandpath_core_store_reading_time( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_lp_reading_time', lampandpath_core_calculate_reading_time( $post->post_content ) );
}
add_action( 'save_post_post', 'lampandpath_core_store_reading_time', 10, 2 );

/**
 * Returns the meta key holding an article's views for a month.
 *
 * Views are kept per month (e.g. "_lp_views_202609") so "Most read this month"
 * does not need a separate log table.
 *
 * @param DateTimeInterface|null $month Any date in the month; defaults to now in the site timezone.
 * @return string
 */
function lampandpath_core_views_key( $month = null ) {
	$month = $month ? $month : new DateTimeImmutable( 'now', wp_timezone() );

	return '_lp_views_' . $month->format( 'Ym' );
}

/**
 * Adds one to a numeric post meta value without losing concurrent updates.
 *
 * The count is incremented inside a single UPDATE, which the database runs
 * atomically (a read, add and write in PHP would let two requests both write
 * the same total). Used for article views and "I prayed" counts.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return int The new value.
 */
function lampandpath_core_increment_meta( $post_id, $key ) {
	global $wpdb;

	// LIMIT 1: if a race ever created two rows, only one keeps counting.
	$increment = $wpdb->prepare(
		"UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s LIMIT 1",
		$post_id,
		$key
	);

	// No row yet means this is the first count. If another request adds the row first, count on top of it.
	if ( ! $wpdb->query( $increment ) && ! add_post_meta( $post_id, $key, 1, true ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery -- Prepared above; a direct query is the point.
		$wpdb->query( $increment ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
	}

	// The UPDATE bypasses the meta API, so drop the cached meta before reading the new total.
	wp_cache_delete( $post_id, 'post_meta' );

	return (int) get_post_meta( $post_id, $key, true );
}

/**
 * Adds one view to an article for the current month.
 *
 * Called by the view beacon endpoint (includes/rest.php).
 *
 * @param int $post_id Post ID.
 * @return int The new view count for this month.
 */
function lampandpath_core_record_view( $post_id ) {
	return lampandpath_core_increment_meta( $post_id, lampandpath_core_views_key() );
}
