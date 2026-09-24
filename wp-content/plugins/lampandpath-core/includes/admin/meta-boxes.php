<?php
/**
 * Meta boxes for verses, reading plans, prayer requests and articles.
 *
 * Fields come from lampandpath_core_post_meta_schema().
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the meta box title per post type.
 *
 * @return array<string, string>
 */
function lampandpath_core_meta_box_titles() {
	return array(
		'lp_verse'  => __( 'Verse details', 'lampandpath-core' ),
		'lp_plan'   => __( 'Plan details', 'lampandpath-core' ),
		'lp_prayer' => __( 'Request details', 'lampandpath-core' ),
		'post'      => __( 'Reading time', 'lampandpath-core' ),
	);
}

/**
 * Adds the details meta box to post types that have schema fields.
 *
 * @param string $post_type Current post type.
 */
function lampandpath_core_add_meta_boxes( $post_type ) {
	$titles = lampandpath_core_meta_box_titles();
	if ( ! isset( $titles[ $post_type ] ) ) {
		return;
	}

	$context = in_array( $post_type, array( 'post', 'lp_prayer' ), true ) ? 'side' : 'normal';
	add_meta_box( 'lampandpath-details', $titles[ $post_type ], 'lampandpath_core_render_meta_box', $post_type, $context, 'high' );
}
add_action( 'add_meta_boxes', 'lampandpath_core_add_meta_boxes' );

/**
 * Prints the details meta box.
 *
 * @param WP_Post $post Post being edited.
 */
function lampandpath_core_render_meta_box( $post ) {
	$schema = lampandpath_core_post_meta_schema();
	wp_nonce_field( 'lampandpath_core_save_meta', 'lampandpath_core_meta_nonce' );

	foreach ( $schema[ $post->post_type ] as $key => $field ) {
		echo '<p>';
		if ( 'readonly' === $field['input'] ) {
			printf( '<strong>%s:</strong> ', esc_html( $field['label'] ) );
		} else {
			printf( '<label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( 'lampandpath-' . $key ), esc_html( $field['label'] ) );
		}
		lampandpath_core_render_input( $key, $field, get_post_meta( $post->ID, $key, true ) );
		if ( ! empty( $field['help'] ) ) {
			printf( '<br><span class="description">%s</span>', esc_html( $field['help'] ) );
		}
		echo '</p>';
	}

	$note = lampandpath_core_meta_box_note( $post );
	if ( $note ) {
		printf( '<p class="description">%s</p>', esc_html( $note ) );
	}
}

/**
 * Returns a short explanation shown under the fields, per post type.
 *
 * @param WP_Post $post Post being edited.
 * @return string
 */
function lampandpath_core_meta_box_note( $post ) {
	switch ( $post->post_type ) {
		case 'lp_verse':
			return __( 'The homepage shows the latest verse whose publish date has arrived. Schedule verses ahead by setting their publish date.', 'lampandpath-core' );
		case 'lp_plan':
			/* translators: %d: number of days in the plan. */
			return sprintf( _n( 'This plan is %d day long.', 'This plan is %d days long.', lampandpath_get_plan_days( $post ), 'lampandpath-core' ), lampandpath_get_plan_days( $post ) );
		case 'lp_prayer':
			return __( 'Publishing approves the request. It only appears on the prayer wall if the person agreed.', 'lampandpath-core' );
		case 'post':
			/* translators: %d: calculated reading time in minutes. */
			return sprintf( __( 'Calculated from the word count: %d min.', 'lampandpath-core' ), lampandpath_core_calculate_reading_time( $post->post_content ) );
	}

	return '';
}

/**
 * Saves the details meta box fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function lampandpath_core_save_meta_box( $post_id, $post ) {
	$schema = lampandpath_core_post_meta_schema();
	if ( ! isset( $schema[ $post->post_type ] ) || ! lampandpath_core_verify_nonce( 'lampandpath_core_meta_nonce', 'lampandpath_core_save_meta' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( $schema[ $post->post_type ] as $key => $field ) {
		if ( 'readonly' === $field['input'] ) {
			continue;
		}

		$value = lampandpath_core_submitted_value( $key, $field );
		if ( null === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'lampandpath_core_save_meta_box', 10, 2 );
