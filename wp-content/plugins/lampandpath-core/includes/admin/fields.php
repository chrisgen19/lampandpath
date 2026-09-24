<?php
/**
 * Form inputs shared by the meta boxes, collection term screens and user profile.
 *
 * Submitted values arrive as $_POST['lampandpath_meta'][ <meta key> ].
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prints the input for a schema field.
 *
 * @param string               $key   Meta key.
 * @param array<string, mixed> $field Schema field.
 * @param mixed                $value Current value.
 */
function lampandpath_core_render_input( $key, array $field, $value ) {
	$id   = 'lampandpath-' . $key;
	$name = 'lampandpath_meta[' . $key . ']';

	switch ( $field['input'] ) {
		case 'textarea':
			printf( '<textarea id="%1$s" name="%2$s" rows="4" class="widefat">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( (string) $value ) );
			break;
		case 'number':
			printf( '<input type="number" id="%1$s" name="%2$s" value="%3$s" min="0" step="1" class="small-text">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ? (string) $value : '' ) );
			break;
		case 'checkbox':
			printf( '<input type="checkbox" id="%1$s" name="%2$s" value="1"%3$s>', esc_attr( $id ), esc_attr( $name ), checked( (bool) $value, true, false ) );
			break;
		case 'icon':
			lampandpath_core_render_select( $id, $name, lampandpath_core_need_icons(), (string) $value, __( 'Choose an icon', 'lampandpath-core' ) );
			break;
		case 'avatar_color':
			lampandpath_core_render_select( $id, $name, lampandpath_core_avatar_colors(), (string) $value, __( 'Automatic', 'lampandpath-core' ) );
			break;
		case 'readonly':
			echo esc_html( lampandpath_core_format_readonly( $field, $value ) );
			break;
		default:
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="widefat">',
				'url' === $field['input'] ? 'url' : 'text',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
	}
}

/**
 * Prints a <select> with an empty first option.
 *
 * @param string                $id      Element ID.
 * @param string                $name    Field name.
 * @param array<string, string> $options Value => label.
 * @param string                $value   Selected value.
 * @param string                $empty   Label of the empty option.
 */
function lampandpath_core_render_select( $id, $name, array $options, $value, $empty ) {
	printf( '<select id="%1$s" name="%2$s"><option value="">%3$s</option>', esc_attr( $id ), esc_attr( $name ), esc_html( $empty ) );
	foreach ( $options as $option => $label ) {
		printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $option ), selected( $value, $option, false ), esc_html( $label ) );
	}
	echo '</select>';
}

/**
 * Formats a read-only value for display.
 *
 * @param array<string, mixed> $field Schema field.
 * @param mixed                $value Stored value.
 * @return string
 */
function lampandpath_core_format_readonly( array $field, $value ) {
	if ( 'boolean' === $field['type'] ) {
		return $value ? __( 'Yes', 'lampandpath-core' ) : __( 'No', 'lampandpath-core' );
	}

	return (string) ( 'integer' === $field['type'] ? (int) $value : $value );
}

/**
 * Returns the sanitized submitted value for a schema field, or null when the field was left empty.
 *
 * Nonce and capability checks are the caller's job.
 *
 * @param string               $key   Meta key.
 * @param array<string, mixed> $field Schema field.
 * @return mixed|null
 */
function lampandpath_core_submitted_value( $key, array $field ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Callers verify the nonce; the value is sanitized below.
	$raw   = isset( $_POST['lampandpath_meta'][ $key ] ) ? wp_unslash( $_POST['lampandpath_meta'][ $key ] ) : '';
	$value = call_user_func( lampandpath_core_meta_sanitizer( $field ), is_string( $raw ) ? $raw : '' );

	return ( '' === $value || 0 === $value || false === $value ) ? null : $value;
}

/**
 * Checks a nonce submitted with one of the plugin's forms.
 *
 * @param string $field  Nonce field name.
 * @param string $action Nonce action.
 * @return bool
 */
function lampandpath_core_verify_nonce( $field, $action ) {
	return isset( $_POST[ $field ] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $field ] ) ), $action );
}
