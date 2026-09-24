<?php
/**
 * Front-end styles, scripts and font loading.
 *
 * @package Lampandpath
 */

/**
 * Returns a cache-busting version for a theme asset, based on its modified time.
 *
 * @param string $relative_path Path relative to the theme root.
 * @return string
 */
function lampandpath_asset_version( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	return file_exists( $file ) ? LAMPANDPATH_VERSION . '.' . filemtime( $file ) : LAMPANDPATH_VERSION;
}

/**
 * Returns the Google Fonts stylesheet URL for Alegreya and Hanken Grotesk.
 *
 * @return string
 */
function lampandpath_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Alegreya:ital,wght@0,400..900;1,400..900&family=Hanken+Grotesk:wght@400..700&display=swap';
}

/**
 * Enqueues theme styles and scripts.
 */
function lampandpath_scripts() {
	// Google Fonts rejects extra query args, so no version is added.
	wp_enqueue_style( 'lampandpath-fonts', lampandpath_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

	wp_enqueue_style(
		'lampandpath-app',
		get_theme_file_uri( 'assets/css/app.css' ),
		array( 'lampandpath-fonts' ),
		lampandpath_asset_version( 'assets/css/app.css' )
	);

	wp_enqueue_script(
		'lampandpath-navigation',
		get_theme_file_uri( 'assets/js/navigation.js' ),
		array(),
		lampandpath_asset_version( 'assets/js/navigation.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lampandpath_scripts' );

/**
 * Adds preconnect hints so the Google Fonts CSS and font files start downloading early.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function lampandpath_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'lampandpath_resource_hints', 10, 2 );
