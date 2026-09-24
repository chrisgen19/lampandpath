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
	define( 'LAMPANDPATH_VERSION', '0.1.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function lampandpath_setup() {
	load_theme_textdomain( 'lampandpath', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary-menu' => __( 'Primary Menu', 'lampandpath' ),
		)
	);
}
add_action( 'after_setup_theme', 'lampandpath_setup' );

/**
 * Enqueues theme styles and scripts.
 */
function lampandpath_scripts() {
	wp_enqueue_style( 'lampandpath-style', get_stylesheet_uri(), array(), LAMPANDPATH_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lampandpath_scripts' );
