<?php
/**
 * Meta field schemas and registration for posts, collection terms and users.
 *
 * Each schema drives registration here, the admin fields in includes/admin/,
 * and saving, so a field is defined in exactly one place.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the editable post meta fields per post type.
 *
 * Field keys: type (string|integer|boolean), input (text|textarea|url|number|readonly),
 * label, help (optional), default (optional).
 *
 * @return array<string, array<string, array<string, mixed>>>
 */
function lampandpath_core_post_meta_schema() {
	return array(
		'lp_verse'  => array(
			'lp_translation' => array(
				'type'    => 'string',
				'input'   => 'text',
				'label'   => __( 'Translation', 'lampandpath-core' ),
				'default' => 'King James Version',
			),
			'lp_reflection'  => array(
				'type'  => 'string',
				'input' => 'textarea',
				'label' => __( 'Reflection question', 'lampandpath-core' ),
				'help'  => __( 'Shown under the verse, e.g. "A question to carry today: ..."', 'lampandpath-core' ),
			),
			'lp_chapter_url' => array(
				'type'  => 'string',
				'input' => 'url',
				'label' => __( 'Chapter link', 'lampandpath-core' ),
				'help'  => __( 'Where the "Read <chapter>" button goes, e.g. the chapter on Bible Gateway.', 'lampandpath-core' ),
			),
		),
		'lp_plan'   => array(
			'lp_minutes'  => array(
				'type'  => 'integer',
				'input' => 'number',
				'label' => __( 'Minutes per day', 'lampandpath-core' ),
			),
			'lp_readings' => array(
				'type'  => 'string',
				'input' => 'textarea',
				'label' => __( 'Daily readings', 'lampandpath-core' ),
				'help'  => __( 'One day per line, e.g. "Mark 1". The number of lines is the length of the plan.', 'lampandpath-core' ),
			),
		),
		'lp_prayer' => array(
			'lp_first_name'   => array(
				'type'  => 'string',
				'input' => 'text',
				'label' => __( 'First name', 'lampandpath-core' ),
				'help'  => __( 'Leave empty to show the request as "Shared anonymously".', 'lampandpath-core' ),
			),
			'lp_wall_consent' => array(
				'type'  => 'boolean',
				'input' => 'readonly',
				'label' => __( 'Agreed to show on the prayer wall', 'lampandpath-core' ),
			),
			'lp_prayed_count' => array(
				'type'  => 'integer',
				'input' => 'readonly',
				'label' => __( 'People who prayed', 'lampandpath-core' ),
			),
		),
		'post'      => array(
			'lp_reading_minutes' => array(
				'type'  => 'integer',
				'input' => 'number',
				'label' => __( 'Reading time (minutes)', 'lampandpath-core' ),
				'help'  => __( 'Leave empty to calculate it from the word count.', 'lampandpath-core' ),
			),
		),
	);
}

/**
 * Returns the fields of a "Where are you today?" collection term.
 *
 * @return array<string, array<string, mixed>>
 */
function lampandpath_core_need_meta_schema() {
	return array(
		'lp_icon'      => array(
			'type'  => 'string',
			'input' => 'icon',
			'label' => __( 'Icon', 'lampandpath-core' ),
		),
		'lp_verse_ref' => array(
			'type'  => 'string',
			'input' => 'text',
			'label' => __( 'Verse reference', 'lampandpath-core' ),
			'help'  => __( 'Shown at the bottom of the card, e.g. "Psalm 34:18".', 'lampandpath-core' ),
		),
		'lp_verse_url' => array(
			'type'  => 'string',
			'input' => 'url',
			'label' => __( 'Verse link', 'lampandpath-core' ),
		),
		'lp_order'     => array(
			'type'  => 'integer',
			'input' => 'number',
			'label' => __( 'Order', 'lampandpath-core' ),
			'help'  => __( 'Lower numbers come first on the homepage.', 'lampandpath-core' ),
		),
	);
}

/**
 * Returns the writer profile fields. "admin_only" fields need the edit_users capability.
 *
 * @return array<string, array<string, mixed>>
 */
function lampandpath_core_user_meta_schema() {
	return array(
		'lp_role_title'    => array(
			'type'  => 'string',
			'input' => 'text',
			'label' => __( 'Role title', 'lampandpath-core' ),
			'help'  => __( 'Shown under your name, e.g. "Pastor and writer".', 'lampandpath-core' ),
		),
		'lp_avatar_color'  => array(
			'type'  => 'string',
			'input' => 'avatar_color',
			'label' => __( 'Initials avatar color', 'lampandpath-core' ),
		),
		'lp_show_on_about' => array(
			'type'       => 'boolean',
			'input'      => 'checkbox',
			'label'      => __( 'Show in the homepage About section', 'lampandpath-core' ),
			'admin_only' => true,
		),
		'lp_about_order'   => array(
			'type'       => 'integer',
			'input'      => 'number',
			'label'      => __( 'About section order', 'lampandpath-core' ),
			'admin_only' => true,
		),
	);
}

/**
 * Returns the icons a collection can use (rendered by the theme), keyed by icon name.
 *
 * @return array<string, string>
 */
function lampandpath_core_need_icons() {
	return array(
		'waves'   => __( 'Waves', 'lampandpath-core' ),
		'candle'  => __( 'Candle', 'lampandpath-core' ),
		'sprout'  => __( 'Sprout', 'lampandpath-core' ),
		'sunrise' => __( 'Sunrise', 'lampandpath-core' ),
		'unlock'  => __( 'Open lock', 'lampandpath-core' ),
		'path'    => __( 'Path', 'lampandpath-core' ),
	);
}

/**
 * Returns the initials avatar color palettes (the theme maps them to design colors).
 *
 * @return array<string, string>
 */
function lampandpath_core_avatar_colors() {
	return array(
		'green'  => __( 'Green', 'lampandpath-core' ),
		'blue'   => __( 'Blue', 'lampandpath-core' ),
		'amber'  => __( 'Amber', 'lampandpath-core' ),
		'violet' => __( 'Violet', 'lampandpath-core' ),
		'rose'   => __( 'Rose', 'lampandpath-core' ),
	);
}

/**
 * Returns the sanitize callback for a schema field.
 *
 * @param array<string, mixed> $field Schema field.
 * @return callable
 */
function lampandpath_core_meta_sanitizer( array $field ) {
	if ( 'boolean' === $field['type'] ) {
		return 'rest_sanitize_boolean';
	}
	if ( 'integer' === $field['type'] ) {
		return 'absint';
	}
	if ( 'url' === $field['input'] ) {
		return 'esc_url_raw';
	}
	if ( 'textarea' === $field['input'] ) {
		return 'sanitize_textarea_field';
	}
	if ( 'icon' === $field['input'] ) {
		return 'lampandpath_core_sanitize_need_icon';
	}
	if ( 'avatar_color' === $field['input'] ) {
		return 'lampandpath_core_sanitize_avatar_color';
	}

	return 'sanitize_text_field';
}

/**
 * Keeps a collection icon to the supported list.
 *
 * @param string $value Submitted icon name.
 * @return string
 */
function lampandpath_core_sanitize_need_icon( $value ) {
	return array_key_exists( $value, lampandpath_core_need_icons() ) ? $value : '';
}

/**
 * Keeps an avatar color to the supported list.
 *
 * @param string $value Submitted color key.
 * @return string
 */
function lampandpath_core_sanitize_avatar_color( $value ) {
	return array_key_exists( $value, lampandpath_core_avatar_colors() ) ? $value : '';
}

/**
 * Allows editing a post's meta through the REST API only to users who can edit that post.
 *
 * @param bool   $allowed  Whether the user can edit the meta (unused).
 * @param string $meta_key Meta key (unused).
 * @param int    $post_id  Post ID.
 * @return bool
 */
function lampandpath_core_can_edit_post_meta( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Registers every schema field with WordPress.
 *
 * Public post types expose their fields in the REST API; prayer requests do not.
 */
function lampandpath_core_register_meta() {
	foreach ( lampandpath_core_post_meta_schema() as $post_type => $fields ) {
		foreach ( $fields as $key => $field ) {
			$args = array(
				'type'              => $field['type'],
				'single'            => true,
				'show_in_rest'      => 'lp_prayer' !== $post_type,
				'sanitize_callback' => lampandpath_core_meta_sanitizer( $field ),
				'auth_callback'     => 'lampandpath_core_can_edit_post_meta',
			);
			// WordPress rejects a default that does not match the field type, so only pass real defaults.
			if ( isset( $field['default'] ) ) {
				$args['default'] = $field['default'];
			}
			register_post_meta( $post_type, $key, $args );
		}
	}

	foreach ( lampandpath_core_need_meta_schema() as $key => $field ) {
		register_term_meta(
			'lp_need',
			$key,
			array(
				'type'              => $field['type'],
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => lampandpath_core_meta_sanitizer( $field ),
			)
		);
	}

	foreach ( lampandpath_core_user_meta_schema() as $key => $field ) {
		register_meta(
			'user',
			$key,
			array(
				'type'              => $field['type'],
				'single'            => true,
				'sanitize_callback' => lampandpath_core_meta_sanitizer( $field ),
			)
		);
	}
}
add_action( 'init', 'lampandpath_core_register_meta' );
