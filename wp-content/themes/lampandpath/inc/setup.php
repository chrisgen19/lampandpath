<?php
/**
 * Theme setup: supported features, menu locations and image sizes.
 *
 * @package Lampandpath
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function lampandpath_setup() {
	load_theme_textdomain( 'lampandpath', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 76,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// The design shows hero art at 460x580 and article thumbnails at 240x160; these are 2x for high-density screens.
	add_image_size( 'lampandpath-hero', 920, 1160, true );
	add_image_size( 'lampandpath-card', 480, 320, true );

	register_nav_menus(
		array(
			'primary'  => __( 'Primary menu', 'lampandpath' ),
			'footer-1' => __( 'Footer column 1', 'lampandpath' ),
			'footer-2' => __( 'Footer column 2', 'lampandpath' ),
			'footer-3' => __( 'Footer column 3', 'lampandpath' ),
			'legal'    => __( 'Legal links', 'lampandpath' ),
		)
	);
}
add_action( 'after_setup_theme', 'lampandpath_setup' );

/**
 * Sets the content width used for oEmbeds and large images in article content.
 */
function lampandpath_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'lampandpath_content_width', 720 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'lampandpath_content_width', 0 );
