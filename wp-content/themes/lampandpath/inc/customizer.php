<?php
/**
 * Customizer settings: Season color, header and footer content.
 *
 * @package Lampandpath
 */

/**
 * Returns the Season color presets from the design, keyed by hex value.
 *
 * @return array<string, string>
 */
function lampandpath_season_colors() {
	return array(
		'#1E5B3A' => __( 'Green (Ordinary Time)', 'lampandpath' ),
		'#5A3D7A' => __( 'Purple (Advent and Lent)', 'lampandpath' ),
		'#9E2B25' => __( 'Red (Pentecost)', 'lampandpath' ),
		'#7A5A14' => __( 'Gold (Christmas and Easter)', 'lampandpath' ),
	);
}

/**
 * Returns the default value of every theme setting.
 *
 * @return array<string, string>
 */
function lampandpath_theme_mod_defaults() {
	return array(
		'lampandpath_accent'           => '#1E5B3A',
		'lampandpath_subscribe_label'  => __( 'Subscribe', 'lampandpath' ),
		'lampandpath_subscribe_url'    => '',
		'lampandpath_footer_verse'     => __( 'Thy word is a lamp unto my feet, and a light unto my path.', 'lampandpath' ),
		'lampandpath_footer_reference' => __( 'Psalm 119:105, King James Version', 'lampandpath' ),
		'lampandpath_social_instagram' => '',
		'lampandpath_social_facebook'  => '',
		'lampandpath_social_youtube'   => '',
		'lampandpath_social_podcast'   => '',
		/* translators: Footer copyright. {year} is the current year, {site} the site title. */
		'lampandpath_copyright'        => __( '© {year} {site}', 'lampandpath' ),
	);
}

/**
 * Returns a theme setting, falling back to its default.
 *
 * @param string $key Setting ID from lampandpath_theme_mod_defaults().
 * @return string
 */
function lampandpath_get_option( $key ) {
	$defaults = lampandpath_theme_mod_defaults();

	return (string) get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Keeps the Season color to one of the design presets.
 *
 * @param string $value Submitted hex color.
 * @return string
 */
function lampandpath_sanitize_accent( $value ) {
	$value = strtoupper( (string) $value );

	return array_key_exists( $value, lampandpath_season_colors() ) ? $value : lampandpath_theme_mod_defaults()['lampandpath_accent'];
}

/**
 * Returns the current Season color.
 *
 * @return string Hex color, one of lampandpath_season_colors().
 */
function lampandpath_accent() {
	return lampandpath_sanitize_accent( lampandpath_get_option( 'lampandpath_accent' ) );
}

/**
 * Adds a text, textarea or URL setting with its control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 * @param string               $id           Setting ID.
 * @param string               $label        Control label.
 * @param string               $section      Section ID.
 * @param string               $type         Control type: text, textarea or url.
 */
function lampandpath_customize_add_field( $wp_customize, $id, $label, $section, $type = 'text' ) {
	$sanitizers = array(
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'url'      => 'esc_url_raw',
	);

	$wp_customize->add_setting(
		$id,
		array(
			'default'           => lampandpath_theme_mod_defaults()[ $id ],
			'sanitize_callback' => $sanitizers[ $type ],
		)
	);
	$wp_customize->add_control(
		$id,
		array(
			'label'   => $label,
			'section' => $section,
			'type'    => $type,
		)
	);
}

/**
 * Registers the theme's Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function lampandpath_customize_register( $wp_customize ) {
	$wp_customize->add_setting(
		'lampandpath_accent',
		array(
			'default'           => lampandpath_theme_mod_defaults()['lampandpath_accent'],
			'sanitize_callback' => 'lampandpath_sanitize_accent',
		)
	);
	$wp_customize->add_control(
		'lampandpath_accent',
		array(
			'label'       => __( 'Season color', 'lampandpath' ),
			'description' => __( 'Accent used for buttons, links and highlights across the site.', 'lampandpath' ),
			'section'     => 'colors',
			'type'        => 'radio',
			'choices'     => lampandpath_season_colors(),
		)
	);

	$wp_customize->add_section( 'lampandpath_header', array( 'title' => __( 'Header', 'lampandpath' ) ) );
	lampandpath_customize_add_field( $wp_customize, 'lampandpath_subscribe_label', __( 'Subscribe button label', 'lampandpath' ), 'lampandpath_header' );
	lampandpath_customize_add_field( $wp_customize, 'lampandpath_subscribe_url', __( 'Subscribe button link (empty = homepage newsletter form)', 'lampandpath' ), 'lampandpath_header', 'url' );

	$wp_customize->add_section( 'lampandpath_footer', array( 'title' => __( 'Footer', 'lampandpath' ) ) );
	lampandpath_customize_add_field( $wp_customize, 'lampandpath_footer_verse', __( 'Verse', 'lampandpath' ), 'lampandpath_footer', 'textarea' );
	lampandpath_customize_add_field( $wp_customize, 'lampandpath_footer_reference', __( 'Verse reference', 'lampandpath' ), 'lampandpath_footer' );
	foreach ( lampandpath_social_networks() as $network => $network_label ) {
		/* translators: %s: social network name. */
		lampandpath_customize_add_field( $wp_customize, 'lampandpath_social_' . $network, sprintf( __( '%s URL', 'lampandpath' ), $network_label ), 'lampandpath_footer', 'url' );
	}
	lampandpath_customize_add_field( $wp_customize, 'lampandpath_copyright', __( 'Copyright text ({year} and {site} are replaced)', 'lampandpath' ), 'lampandpath_footer' );
}
add_action( 'customize_register', 'lampandpath_customize_register' );

/**
 * Outputs the Season color as a CSS variable when it differs from the default.
 *
 * Tailwind derives accent-strong, accent-soft and accent-tint from --color-accent.
 */
function lampandpath_accent_css() {
	$accent = lampandpath_accent();

	if ( lampandpath_theme_mod_defaults()['lampandpath_accent'] !== $accent ) {
		wp_add_inline_style( 'lampandpath-app', sprintf( ':root{--color-accent:%s}', $accent ) );
	}
}
add_action( 'wp_enqueue_scripts', 'lampandpath_accent_css', 20 );
