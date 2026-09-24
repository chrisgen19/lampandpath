<?php
/**
 * Read-only functions the theme uses to get content.
 *
 * The theme must check function_exists() before calling these, so it still
 * renders when this plugin is inactive.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns an article's reading time in minutes.
 *
 * A manual value (lp_reading_minutes) wins; otherwise the value calculated on save is used.
 *
 * @param int|WP_Post|null $post Post ID or object; defaults to the current post.
 * @return int Minutes, or 0 when there is no post.
 */
function lampandpath_get_reading_time( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 0;
	}

	$manual = (int) get_post_meta( $post->ID, 'lp_reading_minutes', true );
	if ( $manual > 0 ) {
		return $manual;
	}

	$stored = (int) get_post_meta( $post->ID, '_lp_reading_time', true );

	return $stored > 0 ? $stored : lampandpath_core_calculate_reading_time( $post->post_content );
}

/**
 * Returns the most read articles of the current month.
 *
 * Early in a month there are few views, so the list is topped up from last
 * month's views and then from the latest articles.
 *
 * @param int $limit Number of articles.
 * @return WP_Post[]
 */
function lampandpath_get_most_read( $limit = 5 ) {
	$this_month = new DateTimeImmutable( 'first day of this month', wp_timezone() );
	$ids        = array();

	foreach ( array( $this_month, $this_month->modify( '-1 month' ) ) as $month ) {
		if ( count( $ids ) >= $limit ) {
			break;
		}
		$ids = array_merge(
			$ids,
			get_posts(
				array(
					'fields'         => 'ids',
					'posts_per_page' => $limit - count( $ids ),
					'post__not_in'   => $ids,
					'meta_key'       => lampandpath_core_views_key( $month ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'orderby'        => 'meta_value_num',
					'order'          => 'DESC',
				)
			)
		);
	}

	if ( count( $ids ) < $limit ) {
		$ids = array_merge(
			$ids,
			get_posts(
				array(
					'fields'         => 'ids',
					'posts_per_page' => $limit - count( $ids ),
					'post__not_in'   => $ids,
				)
			)
		);
	}

	return $ids ? get_posts(
		array(
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $ids ),
		)
	) : array();
}

/**
 * Returns today's verse: the latest verse whose date has arrived.
 *
 * Scheduled ("future") verses are included once their date passes, so the
 * verse still changes if WP-Cron misses publishing it on time.
 *
 * @return WP_Post|null
 */
function lampandpath_get_verse_of_the_day() {
	$verses = get_posts(
		array(
			'post_type'      => 'lp_verse',
			'post_status'    => array( 'publish', 'future' ),
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'date_query'     => array(
				array(
					'before'    => current_time( 'mysql' ),
					'inclusive' => true,
				),
			),
		)
	);

	return $verses ? $verses[0] : null;
}

/**
 * Returns a reading plan's daily readings, one entry per day.
 *
 * @param int|WP_Post|null $post Plan ID or object; defaults to the current post.
 * @return string[]
 */
function lampandpath_get_plan_readings( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}

	$lines = preg_split( '/\R/', (string) get_post_meta( $post->ID, 'lp_readings', true ) );

	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Returns the number of days in a reading plan.
 *
 * @param int|WP_Post|null $post Plan ID or object; defaults to the current post.
 * @return int
 */
function lampandpath_get_plan_days( $post = null ) {
	return count( lampandpath_get_plan_readings( $post ) );
}

/**
 * Returns the reading plans shown on the homepage, in their "Order" (menu_order).
 *
 * @param int $limit Number of plans.
 * @return WP_Post[]
 */
function lampandpath_get_featured_plans( $limit = 3 ) {
	return get_posts(
		array(
			'post_type'      => 'lp_plan',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

/**
 * Returns approved prayer requests whose authors agreed to show them on the wall.
 *
 * @param int $limit Number of requests.
 * @return WP_Post[]
 */
function lampandpath_get_prayer_wall( $limit = 3 ) {
	return get_posts(
		array(
			'post_type'      => 'lp_prayer',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'lp_wall_consent',
					'value' => '1',
				),
			),
		)
	);
}

/**
 * Returns the writers flagged for the homepage About section, in their About order.
 *
 * @return WP_User[]
 */
function lampandpath_get_team() {
	$users = get_users(
		array(
			'meta_key'   => 'lp_show_on_about', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	usort(
		$users,
		static function ( $a, $b ) {
			$order = (int) get_user_meta( $a->ID, 'lp_about_order', true ) <=> (int) get_user_meta( $b->ID, 'lp_about_order', true );

			return 0 !== $order ? $order : strcasecmp( $a->display_name, $b->display_name );
		}
	);

	return $users;
}

/**
 * Returns the "Where are you today?" collections in their display order.
 *
 * @return WP_Term[]
 */
function lampandpath_get_needs() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'lp_need',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		static function ( $a, $b ) {
			$order = (int) get_term_meta( $a->term_id, 'lp_order', true ) <=> (int) get_term_meta( $b->term_id, 'lp_order', true );

			return 0 !== $order ? $order : strcasecmp( $a->name, $b->name );
		}
	);

	return $terms;
}
