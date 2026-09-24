<?php
/**
 * Small UI helpers shared by the homepage sections and later templates.
 *
 * Class strings are written out in full so Tailwind can find them.
 *
 * @package Lampandpath
 */

/**
 * Checks whether the lampandpath-core plugin (content model and template API) is active.
 *
 * @return bool
 */
function lampandpath_core_active() {
	return function_exists( 'lampandpath_get_verse_of_the_day' );
}

/**
 * Returns the classes for a pill button or button-styled link.
 *
 * Buttons that start with an icon get a narrower left padding, as in the design.
 *
 * @param string $variant primary | outline | accent-outline.
 * @param string $size    sm (44px) | md (48px) | lg (52px).
 * @param bool   $icon    Whether the button starts with an icon.
 * @return string
 */
function lampandpath_button_class( $variant = 'primary', $size = 'md', $icon = false ) {
	$variants = array(
		'primary'        => 'bg-accent text-white hover:bg-accent-strong',
		'outline'        => 'border border-line-strong bg-white text-ink hover:border-ink',
		'accent-outline' => 'border border-accent text-accent hover:bg-accent-soft',
	);
	$sizes    = array(
		'sm' => array( 'h-11 px-[18px] text-[15px]', 'h-11 gap-2 pr-[18px] pl-3.5 text-[15px]' ),
		'md' => array( 'h-12 px-[22px] text-base', 'h-12 gap-2.5 pr-[22px] pl-[18px] text-base' ),
		'lg' => array( 'h-13 px-7 text-[17px]', 'h-13 gap-2 pr-[22px] pl-[18px] text-[17px]' ),
	);

	return 'inline-flex shrink-0 items-center justify-center rounded-full leading-none font-semibold no-underline ' . $variants[ $variant ] . ' ' . $sizes[ $size ][ $icon ? 1 : 0 ];
}

/**
 * Returns the classes for a section heading (h2) such as "Latest articles".
 *
 * @return string
 */
function lampandpath_section_heading_class() {
	return 'font-serif text-[34px] leading-[1.1] font-semibold tracking-[-0.01em] text-ink sm:text-[40px] lg:text-[44px]';
}

/**
 * Returns the classes for an underlined text link such as "View all articles".
 *
 * @return string
 */
function lampandpath_text_link_class() {
	return 'shrink-0 text-base leading-6 font-semibold text-accent underline decoration-[1.5px] hover:decoration-2';
}

/**
 * Returns a writer's initials, e.g. "GV" for Grace Villanueva.
 *
 * @param WP_User $user User.
 * @return string
 */
function lampandpath_initials( $user ) {
	$parts = array_filter( array( $user->first_name, $user->last_name ) );
	if ( ! $parts ) {
		$parts = preg_split( '/\s+/', trim( $user->display_name ) );
	}

	$initials = '';
	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		$initials .= mb_substr( $part, 0, 1 );
	}

	return mb_strtoupper( $initials );
}

/**
 * Returns an initials avatar in the writer's palette from the design.
 *
 * The palette comes from the "Initials avatar color" profile field, falling
 * back to one picked from the user ID. Avatars are decorative (the name is
 * always printed next to them), so they are hidden from assistive tech.
 *
 * @param int    $user_id User ID.
 * @param string $size    sm (28px) | md (48px) | lg (52px).
 * @return string
 */
function lampandpath_get_avatar( $user_id, $size = 'md' ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}

	$sizes    = array(
		'sm' => 'size-7 text-[11px]',
		'md' => 'size-12 text-base',
		'lg' => 'size-[52px] text-[17px]',
	);
	$palettes = array(
		'green'  => 'bg-[#e3ecdd] text-[#2f5a3c]',
		'blue'   => 'bg-[#dde7f0] text-[#2f4b66]',
		'amber'  => 'bg-[#f3e4d0] text-[#7a4f1f]',
		'violet' => 'bg-[#e8e1f0] text-[#4f3f73]',
		'rose'   => 'bg-[#f1deda] text-[#7a3a2e]',
	);

	$color = (string) get_user_meta( $user_id, 'lp_avatar_color', true );
	if ( ! isset( $palettes[ $color ] ) ) {
		$keys  = array_keys( $palettes );
		$color = $keys[ $user_id % count( $keys ) ];
	}

	return sprintf(
		'<span aria-hidden="true" class="flex shrink-0 items-center justify-center rounded-full font-bold %1$s %2$s">%3$s</span>',
		esc_attr( $sizes[ $size ] ),
		esc_attr( $palettes[ $color ] ),
		esc_html( lampandpath_initials( $user ) )
	);
}

/**
 * Prints an initials avatar. See lampandpath_get_avatar().
 *
 * @param int    $user_id User ID.
 * @param string $size    Avatar size.
 */
function lampandpath_avatar( $user_id, $size = 'md' ) {
	echo lampandpath_get_avatar( $user_id, $size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in lampandpath_get_avatar().
}

/**
 * Returns the category shown on an article card (the first category).
 *
 * @param int|WP_Post|null $post Post; defaults to the current post.
 * @return WP_Term|null
 */
function lampandpath_primary_category( $post = null ) {
	$categories = get_the_category( get_post( $post ) ? get_post( $post )->ID : 0 );

	return $categories ? $categories[0] : null;
}

/**
 * Returns "8 min read" for an article, or an empty string without the core plugin.
 *
 * @param int|WP_Post|null $post Post; defaults to the current post.
 * @return string
 */
function lampandpath_reading_time_label( $post = null ) {
	if ( ! lampandpath_core_active() ) {
		return '';
	}

	/* translators: %d: reading time in minutes. */
	return sprintf( __( '%d min read', 'lampandpath' ), lampandpath_get_reading_time( $post ) );
}

/**
 * Returns a short relative time such as "2 hours ago", "Yesterday" or "Sep 21".
 *
 * @param int $timestamp Unix timestamp.
 * @return string
 */
function lampandpath_relative_time( $timestamp ) {
	$now = time();
	if ( $now - $timestamp < DAY_IN_SECONDS ) {
		/* translators: %s: time difference, e.g. "2 hours". */
		return sprintf( __( '%s ago', 'lampandpath' ), human_time_diff( $timestamp, $now ) );
	}

	if ( wp_date( 'Y-m-d', $timestamp ) === wp_date( 'Y-m-d', $now - DAY_IN_SECONDS ) ) {
		return __( 'Yesterday', 'lampandpath' );
	}

	return wp_date( 'M j', $timestamp );
}

/**
 * Returns the items of the menu assigned to a location.
 *
 * @param string $location Menu location.
 * @return WP_Post[] Menu item objects (with url, title, object and object_id).
 */
function lampandpath_menu_items( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}

	$items = wp_get_nav_menu_items( $locations[ $location ] );

	return $items ? $items : array();
}

/**
 * Formats verse text for display: keeps verse-number superscripts and sets LORD in small caps.
 *
 * @param string $content Verse post content.
 * @return string Safe HTML.
 */
function lampandpath_format_verse( $content ) {
	$text = trim( wp_kses( preg_replace( '/<!--.*?-->/s', '', $content ), array( 'sup' => array() ) ) );
	// The KJV prints the divine name as LORD; the design sets it in small caps.
	$text = preg_replace( '/\bLORD\b/', '<span class="tracking-[0.03em] [font-variant-caps:small-caps]">Lord</span>', $text );

	return wp_kses(
		$text,
		array(
			'sup'  => array(),
			'span' => array( 'class' => true ),
		)
	);
}

/**
 * Returns plain verse text without verse numbers, for copying and sharing.
 *
 * @param string $content Verse post content.
 * @return string
 */
function lampandpath_plain_verse( $content ) {
	$text = preg_replace( '#<sup>.*?</sup>#s', '', preg_replace( '/<!--.*?-->/s', '', $content ) );

	return trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
}

/**
 * Returns the chapter of a verse reference, e.g. "Psalm 46" for "Psalm 46:1".
 *
 * @param string $reference Verse reference.
 * @return string
 */
function lampandpath_reference_chapter( $reference ) {
	return trim( preg_replace( '/:\S*$/u', '', $reference ) );
}
