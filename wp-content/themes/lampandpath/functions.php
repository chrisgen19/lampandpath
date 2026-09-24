<?php
/**
 * Lamp and Path functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Lampandpath
 */

if ( ! defined( 'LAMPANDPATH_VERSION' ) ) {
	// Bump on each release so browsers fetch fresh theme assets.
	define( 'LAMPANDPATH_VERSION', '0.2.0' );
}

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/menus.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/template-tags.php';
