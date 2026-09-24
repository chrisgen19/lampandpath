<?php
/**
 * Post types and taxonomies.
 *
 * - lp_verse:      Verse of the day. Title is the reference; the latest published verse is today's.
 * - lp_plan:       Bible reading plans (archive: /reading-plans/).
 * - lp_prayer:     Prayer requests from the site form. Private; "publish" means approved.
 * - lp_subscriber: Newsletter subscribers. Private.
 * - lp_need:       "Where are you today?" collections, attached to posts (archive: /collections/<slug>/).
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the capability map for private post types.
 *
 * Prayer requests hold personal data, so only users who can moderate comments
 * (editors and administrators) can see or manage them. Authors cannot.
 *
 * @return array<string, string>
 */
function lampandpath_core_moderator_caps() {
	$caps = array( 'edit_posts', 'edit_others_posts', 'edit_private_posts', 'edit_published_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'delete_others_posts', 'delete_private_posts', 'delete_published_posts', 'create_posts' );

	return array_fill_keys( $caps, 'moderate_comments' );
}

/**
 * Registers the plugin's post types.
 */
function lampandpath_core_register_post_types() {
	register_post_type(
		'lp_verse',
		array(
			'labels'        => lampandpath_core_post_type_labels( __( 'Verse', 'lampandpath-core' ), __( 'Verses of the day', 'lampandpath-core' ) ),
			'description'   => __( 'Verse of the day. The latest published verse is shown as today\'s verse; schedule future verses by date.', 'lampandpath-core' ),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'verses',
			'has_archive'   => 'verses',
			'rewrite'       => array(
				'slug'       => 'verse',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-book-alt',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'revisions', 'custom-fields' ),
		)
	);

	register_post_type(
		'lp_plan',
		array(
			'labels'        => lampandpath_core_post_type_labels( __( 'Reading plan', 'lampandpath-core' ), __( 'Reading plans', 'lampandpath-core' ) ),
			'public'        => true,
			'show_in_rest'  => true,
			'rest_base'     => 'plans',
			'has_archive'   => 'reading-plans',
			'rewrite'       => array(
				'slug'       => 'reading-plans',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 22,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'custom-fields' ),
		)
	);

	lampandpath_core_register_private_post_type( 'lp_prayer', __( 'Prayer request', 'lampandpath-core' ), __( 'Prayer requests', 'lampandpath-core' ), 'dashicons-heart', 23 );
	lampandpath_core_register_private_post_type( 'lp_subscriber', __( 'Subscriber', 'lampandpath-core' ), __( 'Subscribers', 'lampandpath-core' ), 'dashicons-email-alt', 24 );
}
add_action( 'init', 'lampandpath_core_register_post_types' );

/**
 * Registers a post type that is managed in wp-admin but never exposed on the front end or REST API.
 *
 * @param string $type      Post type name.
 * @param string $singular  Singular label.
 * @param string $plural    Plural label.
 * @param string $icon      Dashicon class.
 * @param int    $position  Admin menu position.
 */
function lampandpath_core_register_private_post_type( $type, $singular, $plural, $icon, $position ) {
	register_post_type(
		$type,
		array(
			'labels'          => lampandpath_core_post_type_labels( $singular, $plural ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'capability_type' => 'post',
			'capabilities'    => lampandpath_core_moderator_caps(),
			'map_meta_cap'    => true,
			'menu_icon'       => $icon,
			'menu_position'   => $position,
			'supports'        => 'lp_prayer' === $type ? array( 'title', 'editor' ) : array( 'title' ),
		)
	);
}

/**
 * Registers the "Where are you today?" collections taxonomy.
 */
function lampandpath_core_register_taxonomies() {
	register_taxonomy(
		'lp_need',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'          => __( 'Collections', 'lampandpath-core' ),
				'singular_name' => __( 'Collection', 'lampandpath-core' ),
				'menu_name'     => __( 'Collections', 'lampandpath-core' ),
				'all_items'     => __( 'All collections', 'lampandpath-core' ),
				'edit_item'     => __( 'Edit collection', 'lampandpath-core' ),
				'add_new_item'  => __( 'Add new collection', 'lampandpath-core' ),
				'search_items'  => __( 'Search collections', 'lampandpath-core' ),
				'not_found'     => __( 'No collections found.', 'lampandpath-core' ),
			),
			'description'       => __( 'The "Where are you today?" collections on the homepage.', 'lampandpath-core' ),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rest_base'         => 'needs',
			'rewrite'           => array(
				'slug'       => 'collections',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'lampandpath_core_register_taxonomies' );

/**
 * Lists reading plans in their "Order", then by title, on the plan archive, as on the homepage.
 *
 * @param WP_Query $query Query.
 */
function lampandpath_core_order_plan_archive( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'lp_plan' ) ) {
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'lampandpath_core_order_plan_archive' );

/**
 * Publishes verses whose scheduled day has arrived but that WP-Cron has not published yet.
 *
 * WP-Cron only runs when the site gets visits, and some hosts block it, so a
 * verse can stay "Scheduled" after its day starts. The homepage already shows
 * it as today's verse (lampandpath_get_verse_of_the_day()), but WordPress keeps
 * scheduled posts private: it would be missing from the verse archive and its
 * page and share link would 404. Publishing it, as WP-Cron would have, fixes
 * all of them.
 *
 * Runs on front-end page requests only, before the main query so the current
 * page already sees the verse (REST requests end at parse_request priority 10).
 * Publishing fires save_post, so it must never run inside a wp-admin save,
 * whose form data belongs to another post. A lock stops two visitors from
 * publishing the same verse, and its hooks, twice.
 */
function lampandpath_core_publish_missed_verses() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_key( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
	if ( ! in_array( $method, array( 'GET', 'HEAD' ), true ) ) {
		return;
	}

	$missed = get_posts(
		array(
			'post_type'      => 'lp_verse',
			'post_status'    => 'future',
			'fields'         => 'ids',
			// Newest first, so today's verse is published on the first visit even after a long outage;
			// a larger backlog is worked through 10 at a time on the following visits.
			'posts_per_page' => 10,
			'orderby'        => 'date',
			'order'          => 'DESC',
			// GMT, like check_and_publish_future_post(), which would otherwise reschedule the verse.
			'date_query'     => array(
				array(
					'column'    => 'post_date_gmt',
					'before'    => current_time( 'mysql', true ),
					'inclusive' => true,
				),
			),
		)
	);
	if ( ! $missed || ! lampandpath_core_lock( 'lampandpath_publishing_verses' ) ) {
		return;
	}

	foreach ( $missed as $verse_id ) {
		// Another request may have published it since the query above.
		clean_post_cache( $verse_id );
		check_and_publish_future_post( $verse_id );
	}

	lampandpath_core_unlock( 'lampandpath_publishing_verses' );
}
add_action( 'parse_request', 'lampandpath_core_publish_missed_verses', 11 );

/**
 * Takes a named lock, stored as an option.
 *
 * INSERT IGNORE is atomic, unlike add_option(), so only one request gets the
 * lock. A lock older than a minute was left by a request that failed, and is taken over.
 *
 * @param string $name Lock (option) name.
 * @return bool Whether this request holds the lock.
 */
function lampandpath_core_lock( $name ) {
	global $wpdb;

	// phpcs:disable WordPress.DB.DirectDatabaseQuery -- The options API cannot insert atomically.
	if ( $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'off')", $name, time() ) ) ) {
		return true;
	}

	$since = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );
	$taken = $since && $since < time() - MINUTE_IN_SECONDS
		&& $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s", time(), $name, $since ) );
	// phpcs:enable

	return (bool) $taken;
}

/**
 * Releases a lock taken with lampandpath_core_lock().
 *
 * @param string $name Lock (option) name.
 */
function lampandpath_core_unlock( $name ) {
	global $wpdb;

	$wpdb->delete( $wpdb->options, array( 'option_name' => $name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Pairs with lampandpath_core_lock().
}

/**
 * Builds the common post type labels from a singular and plural name.
 *
 * @param string $singular Singular label, e.g. "Reading plan".
 * @param string $plural   Plural label, e.g. "Reading plans".
 * @return array<string, string>
 */
function lampandpath_core_post_type_labels( $singular, $plural ) {
	$lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $singular ) : strtolower( $singular );

	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'all_items'          => $plural,
		/* translators: %s: post type singular name, lowercase. */
		'add_new_item'       => sprintf( __( 'Add new %s', 'lampandpath-core' ), $lower ),
		/* translators: %s: post type singular name, lowercase. */
		'edit_item'          => sprintf( __( 'Edit %s', 'lampandpath-core' ), $lower ),
		/* translators: %s: post type singular name, lowercase. */
		'new_item'           => sprintf( __( 'New %s', 'lampandpath-core' ), $lower ),
		/* translators: %s: post type singular name, lowercase. */
		'view_item'          => sprintf( __( 'View %s', 'lampandpath-core' ), $lower ),
		/* translators: %s: post type plural name. */
		'search_items'       => sprintf( __( 'Search %s', 'lampandpath-core' ), $plural ),
		'not_found'          => __( 'Nothing found.', 'lampandpath-core' ),
		'not_found_in_trash' => __( 'Nothing found in Trash.', 'lampandpath-core' ),
	);
}
