<?php
/**
 * Writer profile fields on the user profile screen.
 *
 * Writers can set their own role title and avatar color. Only users who can
 * edit other users (administrators) decide who appears in the homepage About section.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the profile fields the current user may edit.
 *
 * @return array<string, array<string, mixed>>
 */
function lampandpath_core_editable_user_fields() {
	$can_manage = current_user_can( 'edit_users' );

	return array_filter(
		lampandpath_core_user_meta_schema(),
		static function ( $field ) use ( $can_manage ) {
			return $can_manage || empty( $field['admin_only'] );
		}
	);
}

/**
 * Prints the writer profile fields.
 *
 * @param WP_User $user User being edited.
 */
function lampandpath_core_render_user_fields( $user ) {
	printf( '<h2>%s</h2>', esc_html__( 'Writer profile', 'lampandpath-core' ) );
	wp_nonce_field( 'lampandpath_core_save_user', 'lampandpath_core_user_nonce' );
	echo '<table class="form-table" role="presentation">';

	foreach ( lampandpath_core_editable_user_fields() as $key => $field ) {
		echo '<tr><th scope="row">';
		printf( '<label for="%1$s">%2$s</label></th><td>', esc_attr( 'lampandpath-' . $key ), esc_html( $field['label'] ) );
		lampandpath_core_render_input( $key, $field, get_user_meta( $user->ID, $key, true ) );
		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
		echo '</td></tr>';
	}

	echo '</table>';
}
add_action( 'show_user_profile', 'lampandpath_core_render_user_fields' );
add_action( 'edit_user_profile', 'lampandpath_core_render_user_fields' );

/**
 * Saves the writer profile fields.
 *
 * @param int $user_id User being saved.
 */
function lampandpath_core_save_user_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! lampandpath_core_verify_nonce( 'lampandpath_core_user_nonce', 'lampandpath_core_save_user' ) ) {
		return;
	}

	foreach ( lampandpath_core_editable_user_fields() as $key => $field ) {
		$value = lampandpath_core_submitted_value( $key, $field );
		if ( null === $value ) {
			delete_user_meta( $user_id, $key );
		} else {
			update_user_meta( $user_id, $key, $value );
		}
	}
}
add_action( 'personal_options_update', 'lampandpath_core_save_user_fields' );
add_action( 'edit_user_profile_update', 'lampandpath_core_save_user_fields' );
