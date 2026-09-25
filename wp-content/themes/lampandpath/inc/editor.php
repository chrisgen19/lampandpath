<?php
/**
 * Block editor: styles, color palette and font sizes that match the front end.
 *
 * The editor stylesheet (assets/css/editor.css) is built from src/css/editor.css
 * and shares the design tokens and article typography with the front end
 * (src/css/content.css).
 *
 * @package Lampandpath
 */

/**
 * Mixes a hex color with white, like CSS color-mix(in srgb, color N%, white).
 *
 * @param string $hex     Color, e.g. "#1E5B3A".
 * @param int    $percent Share of the color, 0 to 100.
 * @return string Hex color.
 */
function lampandpath_mix_with_white( $hex, $percent ) {
	$mixed = '#';
	foreach ( str_split( ltrim( $hex, '#' ), 2 ) as $channel ) {
		$mixed .= sprintf( '%02x', (int) round( ( hexdec( $channel ) * $percent + 255 * ( 100 - $percent ) ) / 100 ) );
	}

	return $mixed;
}

/**
 * Registers editor styles and limits colors and font sizes to the design's tokens.
 */
function lampandpath_editor_setup() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// The Season color entries follow the Customizer, like --color-accent on the front end.
	$accent = lampandpath_accent();
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Ink', 'lampandpath' ),
				'slug'  => 'ink',
				'color' => '#17231c',
			),
			array(
				'name'  => __( 'Ink soft', 'lampandpath' ),
				'slug'  => 'ink-soft',
				'color' => '#36433b',
			),
			array(
				'name'  => __( 'Muted', 'lampandpath' ),
				'slug'  => 'muted',
				'color' => '#5c6a61',
			),
			array(
				'name'  => __( 'Season color', 'lampandpath' ),
				'slug'  => 'accent',
				'color' => $accent,
			),
			array(
				'name'  => __( 'Season color, tint', 'lampandpath' ),
				'slug'  => 'accent-tint',
				'color' => lampandpath_mix_with_white( $accent, 18 ),
			),
			array(
				'name'  => __( 'Season color, soft', 'lampandpath' ),
				'slug'  => 'accent-soft',
				'color' => lampandpath_mix_with_white( $accent, 8 ),
			),
			array(
				'name'  => __( 'Canvas', 'lampandpath' ),
				'slug'  => 'canvas',
				'color' => '#edf1eb',
			),
			array(
				'name'  => __( 'Light surface', 'lampandpath' ),
				'slug'  => 'surface-soft',
				'color' => '#f5f7f4',
			),
			array(
				'name'  => __( 'White', 'lampandpath' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
		)
	);
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-gradient-presets', array() );
	add_theme_support( 'disable-custom-gradients' );

	// Article body copy is 18px (src/css/content.css); the rest follow the design's type scale.
	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => __( 'Small', 'lampandpath' ),
				'slug' => 'small',
				'size' => 15,
			),
			array(
				'name' => __( 'Normal', 'lampandpath' ),
				'slug' => 'normal',
				'size' => 18,
			),
			array(
				'name' => __( 'Large', 'lampandpath' ),
				'slug' => 'large',
				'size' => 22,
			),
			array(
				'name' => __( 'Extra large', 'lampandpath' ),
				'slug' => 'x-large',
				'size' => 28,
			),
		)
	);
	add_theme_support( 'disable-custom-font-sizes' );
}
add_action( 'after_setup_theme', 'lampandpath_editor_setup' );

/**
 * Applies the Season color to the editor canvas when it differs from the default.
 *
 * @param array $settings Block editor settings.
 * @return array
 */
function lampandpath_editor_accent( $settings ) {
	$accent = lampandpath_accent();
	if ( lampandpath_theme_mod_defaults()['lampandpath_accent'] !== $accent ) {
		$settings['styles'][] = array(
			'css'            => sprintf( ':root{--color-accent:%s}', $accent ),
			'__unstableType' => 'theme',
		);
	}

	return $settings;
}
add_filter( 'block_editor_settings_all', 'lampandpath_editor_accent' );
