<?php
/**
 * Nav menu styling: Tailwind classes for links in each menu location.
 *
 * @package Lampandpath
 */

/**
 * Returns the link classes for each menu style.
 *
 * The footer-1..3 locations share the "footer" style.
 *
 * @return array<string, string>
 */
function lampandpath_menu_link_classes() {
	return array(
		'primary' => 'flex h-11 items-center rounded-full px-3.5 text-base font-medium text-ink-soft hover:bg-surface-soft hover:text-ink aria-[current=page]:bg-accent-soft aria-[current=page]:text-accent',
		'footer'  => 'inline-flex min-h-11 items-center text-base text-ink-soft hover:text-accent hover:underline',
		'legal'   => 'inline-flex min-h-11 items-center text-muted underline hover:text-accent',
		'topics'  => 'flex h-11 items-center rounded-full bg-surface-soft px-4 text-[15px] font-medium text-ink hover:bg-accent-soft hover:text-accent',
	);
}

/**
 * Checks whether a menu location has a menu with at least one item.
 *
 * has_nav_menu() is also true for an assigned but empty menu, which would
 * leave empty wrappers on the page (a card heading, a menu button). The
 * menu's stored item count avoids an extra query.
 *
 * @param string $location Menu location.
 * @return bool
 */
function lampandpath_has_menu_items( $location ) {
	$locations = get_nav_menu_locations();
	$menu      = empty( $locations[ $location ] ) ? false : wp_get_nav_menu_object( $locations[ $location ] );

	return $menu && $menu->count > 0;
}

/**
 * Adds the location's Tailwind classes to each menu link.
 *
 * @param array    $atts HTML attributes for the link.
 * @param WP_Post  $item The menu item.
 * @param stdClass $args wp_nav_menu() arguments.
 * @return array
 */
function lampandpath_nav_menu_link_attributes( $atts, $item, $args ) {
	$location = isset( $args->theme_location ) ? (string) $args->theme_location : '';
	$style    = 0 === strpos( $location, 'footer-' ) ? 'footer' : $location;
	$classes  = lampandpath_menu_link_classes();

	if ( isset( $classes[ $style ] ) ) {
		$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] : '' ) . ' ' . $classes[ $style ] );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'lampandpath_nav_menu_link_attributes', 10, 3 );
