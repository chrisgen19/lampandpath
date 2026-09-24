<?php
/**
 * Extra fields on the "Where are you today?" collection screens (Posts > Collections).
 *
 * Fields come from lampandpath_core_need_meta_schema().
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the fields on the "Add new collection" form.
 */
function lampandpath_core_need_add_fields() {
	wp_nonce_field( 'lampandpath_core_save_need', 'lampandpath_core_need_nonce' );

	foreach ( lampandpath_core_need_meta_schema() as $key => $field ) {
		echo '<div class="form-field">';
		printf( '<label for="%1$s">%2$s</label>', esc_attr( 'lampandpath-' . $key ), esc_html( $field['label'] ) );
		lampandpath_core_render_input( $key, $field, '' );
		if ( ! empty( $field['help'] ) ) {
			printf( '<p>%s</p>', esc_html( $field['help'] ) );
		}
		echo '</div>';
	}
}
add_action( 'lp_need_add_form_fields', 'lampandpath_core_need_add_fields' );

/**
 * Prints the fields on the "Edit collection" screen.
 *
 * @param WP_Term $term Collection being edited.
 */
function lampandpath_core_need_edit_fields( $term ) {
	wp_nonce_field( 'lampandpath_core_save_need', 'lampandpath_core_need_nonce' );

	foreach ( lampandpath_core_need_meta_schema() as $key => $field ) {
		echo '<tr class="form-field"><th scope="row">';
		printf( '<label for="%1$s">%2$s</label></th><td>', esc_attr( 'lampandpath-' . $key ), esc_html( $field['label'] ) );
		lampandpath_core_render_input( $key, $field, get_term_meta( $term->term_id, $key, true ) );
		if ( ! empty( $field['help'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['help'] ) );
		}
		echo '</td></tr>';
	}
}
add_action( 'lp_need_edit_form_fields', 'lampandpath_core_need_edit_fields' );

/**
 * Saves the collection fields when a collection is created or updated.
 *
 * @param int $term_id Term ID.
 */
function lampandpath_core_save_need_fields( $term_id ) {
	if ( ! lampandpath_core_verify_nonce( 'lampandpath_core_need_nonce', 'lampandpath_core_save_need' ) || ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	foreach ( lampandpath_core_need_meta_schema() as $key => $field ) {
		$value = lampandpath_core_submitted_value( $key, $field );
		if ( null === $value ) {
			delete_term_meta( $term_id, $key );
		} else {
			update_term_meta( $term_id, $key, $value );
		}
	}
}
add_action( 'created_lp_need', 'lampandpath_core_save_need_fields' );
add_action( 'edited_lp_need', 'lampandpath_core_save_need_fields' );
