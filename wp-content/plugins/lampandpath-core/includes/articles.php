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
 * Adds one view to an article for the current month.
 *
 * Called by the view beacon endpoint in Phase 4.
 *
 * @param int $post_id Post ID.
 * @return int The new view count for this month.
 */
function lampandpath_core_record_view( $post_id ) {
	$key   = lampandpath_core_views_key();
	$views = (int) get_post_meta( $post_id, $key, true ) + 1;
	update_post_meta( $post_id, $key, $views );

	return $views;
}
