<?php
/**
 * Extra list table columns for verses, reading plans and prayer requests.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the extra columns per post type, inserted after the title column.
 *
 * @return array<string, array<string, string>>
 */
function lampandpath_core_list_columns() {
	return array(
		'lp_verse'  => array(
			'lp_translation' => __( 'Translation', 'lampandpath-core' ),
		),
		'lp_plan'   => array(
			'lp_days'    => __( 'Days', 'lampandpath-core' ),
			'lp_minutes' => __( 'Minutes a day', 'lampandpath-core' ),
		),
		'lp_prayer' => array(
			'lp_request' => __( 'Request', 'lampandpath-core' ),
			'lp_name'    => __( 'Name', 'lampandpath-core' ),
			'lp_wall'    => __( 'Prayer wall', 'lampandpath-core' ),
			'lp_prayed'  => __( 'Prayed', 'lampandpath-core' ),
			'lp_status'  => __( 'Status', 'lampandpath-core' ),
		),
	);
}

/**
 * Inserts a post type's extra columns after the title column.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function lampandpath_core_add_list_columns( $columns ) {
	$screen = get_current_screen();
	$extra  = lampandpath_core_list_columns();
	if ( ! $screen || ! isset( $extra[ $screen->post_type ] ) ) {
		return $columns;
	}

	$result = array();
	foreach ( $columns as $key => $label ) {
		$result[ $key ] = $label;
		if ( 'title' === $key ) {
			$result += $extra[ $screen->post_type ];
		}
	}

	return $result;
}
add_filter( 'manage_lp_verse_posts_columns', 'lampandpath_core_add_list_columns' );
add_filter( 'manage_lp_plan_posts_columns', 'lampandpath_core_add_list_columns' );
add_filter( 'manage_lp_prayer_posts_columns', 'lampandpath_core_add_list_columns' );

/**
 * Prints the value of an extra column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function lampandpath_core_render_list_column( $column, $post_id ) {
	echo esc_html( lampandpath_core_list_column_value( $column, $post_id ) );
}
add_action( 'manage_lp_verse_posts_custom_column', 'lampandpath_core_render_list_column', 10, 2 );
add_action( 'manage_lp_plan_posts_custom_column', 'lampandpath_core_render_list_column', 10, 2 );
add_action( 'manage_lp_prayer_posts_custom_column', 'lampandpath_core_render_list_column', 10, 2 );

/**
 * Returns the plain-text value of an extra column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 * @return string
 */
function lampandpath_core_list_column_value( $column, $post_id ) {
	switch ( $column ) {
		case 'lp_translation':
			return (string) get_post_meta( $post_id, 'lp_translation', true );
		case 'lp_days':
			return (string) lampandpath_get_plan_days( $post_id );
		case 'lp_minutes':
			$minutes = (int) get_post_meta( $post_id, 'lp_minutes', true );
			/* translators: %d: minutes. */
			return $minutes ? sprintf( __( '%d min', 'lampandpath-core' ), $minutes ) : '';
		case 'lp_request':
			return wp_trim_words( get_post_field( 'post_content', $post_id ), 16 );
		case 'lp_name':
			$name = (string) get_post_meta( $post_id, 'lp_first_name', true );
			return '' !== $name ? $name : __( 'Anonymous', 'lampandpath-core' );
		case 'lp_wall':
			return get_post_meta( $post_id, 'lp_wall_consent', true ) ? __( 'Agreed', 'lampandpath-core' ) : __( 'Private', 'lampandpath-core' );
		case 'lp_prayed':
			return (string) (int) get_post_meta( $post_id, 'lp_prayed_count', true );
		case 'lp_status':
			return lampandpath_core_prayer_status_label( get_post_status( $post_id ) );
	}

	return '';
}

/**
 * Returns a moderation label for a prayer request status.
 *
 * @param string|false $status Post status.
 * @return string
 */
function lampandpath_core_prayer_status_label( $status ) {
	if ( 'publish' === $status ) {
		return __( 'Approved', 'lampandpath-core' );
	}
	if ( 'pending' === $status ) {
		return __( 'Awaiting approval', 'lampandpath-core' );
	}

	$object = $status ? get_post_status_object( $status ) : null;

	return $object ? $object->label : '';
}
