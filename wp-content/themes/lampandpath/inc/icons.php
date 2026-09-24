<?php
/**
 * Inline SVG icons from the homepage design.
 *
 * Icons use currentColor, so they take the text color of their parent
 * (e.g. `text-accent`), which keeps them in sync with the Season color.
 *
 * @package Lampandpath
 */

/**
 * Returns the icon registry: SVG inner markup plus optional viewBox and stroke width.
 *
 * @return array<string, array{paths: string, viewbox?: string, stroke?: string}>
 */
function lampandpath_icons() {
	return array(
		// Brand.
		'lamp-mark' => array(
			'viewbox' => '0 0 32 40',
			'stroke'  => '2.4',
			'paths'   => '<path d="M4 38V16a12 12 0 0 1 24 0v22z"/><path d="M16 18.5c2.9 3 4 5.3 4 7.2a4 4 0 0 1-8 0c0-1.9 1.1-4.2 4-7.2z" fill="currentColor" stroke="none"/><path d="M10.5 33.5h11"/>',
		),

		// Interface.
		'search'    => array(
			'stroke' => '2',
			'paths'  => '<circle cx="11" cy="11" r="7"/><path d="M20.5 20.5l-4.2-4.2"/>',
		),
		'menu'      => array(
			'stroke' => '2',
			'paths'  => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		),
		'close'     => array(
			'stroke' => '2',
			'paths'  => '<path d="M6 6l12 12M18 6L6 18"/>',
		),
		'previous'  => array(
			'stroke' => '2',
			'paths'  => '<path d="M14.5 6l-6 6 6 6"/>',
		),
		'next'      => array(
			'stroke' => '2',
			'paths'  => '<path d="M9.5 6l6 6-6 6"/>',
		),
		'clock'     => array(
			'stroke' => '2',
			'paths'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>',
		),
		'bookmark'  => array(
			'stroke' => '2',
			'paths'  => '<path d="M6.5 3.5h11v17l-5.5-4-5.5 4z"/>',
		),
		'book-open' => array(
			'paths' => '<path d="M2.5 5.5c3-1.2 6.2-1 9.5 1 3.3-2 6.5-2.2 9.5-1v13c-3-1.2-6.2-1-9.5 1-3.3-2-6.5-2.2-9.5-1z"/><path d="M12 6.5v13"/>',
		),
		'copy'      => array(
			'paths' => '<rect x="8.5" y="8.5" width="12" height="12" rx="2.5"/><path d="M15.5 8.5V6A2.5 2.5 0 0 0 13 3.5H6A2.5 2.5 0 0 0 3.5 6v7A2.5 2.5 0 0 0 6 15.5h2.5"/>',
		),
		'share'     => array(
			'paths' => '<circle cx="18" cy="5.5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="18.5" r="2.5"/><path d="M8.2 10.8l7.6-4.1M8.2 13.2l7.6 4.1"/>',
		),
		'heart'     => array(
			'paths' => '<path d="M12 20s-7.5-4.6-9.2-9.4C1.6 7.2 3.8 4.5 7 4.5c2 0 3.5 1 5 2.9 1.5-1.9 3-2.9 5-2.9 3.2 0 5.4 2.7 4.2 6.1C19.5 15.4 12 20 12 20z"/>',
		),
		'check'     => array(
			'stroke' => '2.2',
			'paths'  => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		),
		'document'  => array(
			'paths' => '<path d="M14 3.5H7A2.5 2.5 0 0 0 4.5 6v12A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5V9z"/><path d="M14 3.5V9h5.5M8.5 13h7M8.5 16.5h5"/>',
		),
		'mail'      => array(
			'paths' => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.8 6.8l8.2 6 8.2-6"/>',
		),

		// Social.
		'instagram' => array(
			'paths' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/>',
		),
		'facebook'  => array(
			'paths' => '<path d="M14.5 21v-7.5h2.7l.4-3.3h-3.1V8.3c0-1 .3-1.6 1.7-1.6h1.6V3.8c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.1v2.4H8.7v3.3h2.7V21"/>',
		),
		'youtube'   => array(
			'paths' => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10.2 9.3v5.4l4.6-2.7z"/>',
		),
		'podcast'   => array(
			'paths' => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5.5 11a6.5 6.5 0 0 0 13 0M12 17.5V21M9 21h6"/>',
		),

		// "Where are you today?" collections.
		'waves'     => array(
			'paths' => '<path d="M2 7q2.5-3 5 0t5 0 5 0 5 0M2 12q2.5-3 5 0t5 0 5 0 5 0M2 17q2.5-3 5 0t5 0 5 0 5 0"/>',
		),
		'candle'    => array(
			'paths' => '<path d="M12 2.8c1.9 2.1 2.6 3.5 2.6 4.7a2.6 2.6 0 0 1-5.2 0c0-1.2.7-2.6 2.6-4.7z"/><rect x="8.5" y="11" width="7" height="10" rx="1.2"/><path d="M6 21h12"/>',
		),
		'sprout'    => array(
			'paths' => '<path d="M12 21V11.5"/><path d="M12 12.5c0-4-3-7-8-7 0 4 3 7 8 7z"/><path d="M12 15.5c0-3.5 2.5-6 7-6 0 3.5-2.5 6-7 6z"/><path d="M7.5 21h9"/>',
		),
		'sunrise'   => array(
			'paths' => '<path d="M2.5 19h19"/><path d="M6.5 19a5.5 5.5 0 0 1 11 0"/><path d="M12 4.5v3.5M5.3 9.3l2.1 2.1M18.7 9.3l-2.1 2.1"/>',
		),
		'unlock'    => array(
			'paths' => '<rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 7.6-1.8"/><path d="M12 14.5v2.5"/>',
		),
		'path'      => array(
			'paths' => '<path d="M4 21c4.5 0 4.5-5 8-5s3.5-5 8-5"/><circle cx="19.5" cy="5.5" r="2.5"/>',
		),
	);
}

/**
 * Returns the SVG markup for a theme icon.
 *
 * @param string $name Icon name from lampandpath_icons().
 * @param array  $args {
 *     Optional. Icon arguments.
 *
 *     @type int    $size  Rendered width in pixels; height follows the viewBox ratio. Default 24.
 *     @type string $class Extra CSS classes for the <svg>.
 *     @type string $label Accessible label. When empty the icon is hidden from assistive tech.
 * }
 * @return string SVG markup, or an empty string for an unknown icon.
 */
function lampandpath_get_icon( $name, $args = array() ) {
	$icons = lampandpath_icons();
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$args    = wp_parse_args(
		$args,
		array(
			'size'  => 24,
			'class' => '',
			'label' => '',
		)
	);
	$icon    = $icons[ $name ];
	$viewbox = isset( $icon['viewbox'] ) ? $icon['viewbox'] : '0 0 24 24';
	$box     = array_map( 'floatval', explode( ' ', $viewbox ) );
	$width   = (int) $args['size'];
	$height  = (int) round( $width * $box[3] / $box[2] );
	$a11y    = $args['label']
		? sprintf( 'role="img" aria-label="%s"', esc_attr( $args['label'] ) )
		: 'aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%3$d" viewBox="%4$s" fill="none" stroke="currentColor" stroke-width="%5$s" stroke-linecap="round" stroke-linejoin="round" %6$s>%7$s</svg>',
		esc_attr( trim( 'shrink-0 ' . $args['class'] ) ),
		$width,
		$height,
		esc_attr( $viewbox ),
		esc_attr( isset( $icon['stroke'] ) ? $icon['stroke'] : '1.9' ),
		$a11y,
		$icon['paths']
	);
}

/**
 * Prints a theme icon. See lampandpath_get_icon() for arguments.
 *
 * @param string $name Icon name.
 * @param array  $args Icon arguments.
 */
function lampandpath_icon( $name, $args = array() ) {
	echo lampandpath_get_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup from lampandpath_icons(); attributes are escaped above.
}
