<?php
/**
 * Front-end styles, scripts and font preloading.
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
 * Enqueues theme styles and scripts.
 *
 * The fonts are self-hosted: app.css declares them (src/css/fonts.css).
 */
function lampandpath_scripts() {
	wp_enqueue_style(
		'lampandpath-app',
		get_theme_file_uri( 'assets/css/app.css' ),
		array(),
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

	wp_enqueue_script(
		'lampandpath-interactions',
		get_theme_file_uri( 'assets/js/interactions.js' ),
		array(),
		lampandpath_asset_version( 'assets/js/interactions.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
	wp_add_inline_script( 'lampandpath-interactions', 'window.lampandpath = ' . wp_json_encode( lampandpath_interactions_config() ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'lampandpath_scripts' );

/**
 * Returns the settings and messages for assets/js/interactions.js.
 *
 * @return array
 */
function lampandpath_interactions_config() {
	// Editors' own visits are not counted, so "Most read" reflects readers.
	$view_post = ( is_singular( 'post' ) && ! current_user_can( 'edit_posts' ) ) ? get_queried_object_id() : 0;

	return array(
		'rest'     => esc_url_raw( rest_url( 'lampandpath/v1/' ) ),
		'viewPost' => $view_post,
		'strings'  => array(
			'copied'      => __( 'Verse copied', 'lampandpath' ),
			'copiedShort' => __( 'Copied', 'lampandpath' ),
			'linkCopied'  => __( 'Link copied', 'lampandpath' ),
			'copyFailed'  => __( 'Copying is not available in this browser.', 'lampandpath' ),
			'saved'       => __( 'Saved for later', 'lampandpath' ),
			'unsaved'     => __( 'Removed from saved articles', 'lampandpath' ),
			/* translators: %d: number of articles added to the list. */
			'loaded'      => __( '%d more articles loaded', 'lampandpath' ),
			/* translators: %s: topic name, e.g. "Prayer". */
			'filtered'    => __( 'Showing articles in %s', 'lampandpath' ),
			'filteredAll' => __( 'Showing all articles', 'lampandpath' ),
			'noArticles'  => __( 'No articles in this topic yet.', 'lampandpath' ),
			'sending'     => __( 'Sending…', 'lampandpath' ),
			'error'       => __( 'Something went wrong. Please try again.', 'lampandpath' ),
		),
	);
}

/**
 * Preloads the two fonts every page needs for its first paint.
 *
 * Without this the browser only finds them after downloading and parsing
 * app.css. The URLs must match the ones in src/css/fonts.css exactly (no
 * version query), or the browser downloads each font twice. The italic and
 * the latin-ext files are left to load on demand.
 *
 * @param array $resources Resources to preload.
 * @return array
 */
function lampandpath_preload_fonts( $resources ) {
	foreach ( array( 'hanken-grotesk-latin', 'alegreya-latin' ) as $font ) {
		$resources[] = array(
			'href'        => get_theme_file_uri( 'assets/fonts/' . $font . '.woff2' ),
			'as'          => 'font',
			'type'        => 'font/woff2',
			'crossorigin' => 'anonymous',
		);
	}

	return $resources;
}
add_filter( 'wp_preload_resources', 'lampandpath_preload_fonts' );

/**
 * Enqueues the "Load more" script on paged listings (assets/js/feed.js).
 */
function lampandpath_feed_script() {
	if ( ! ( is_home() || is_archive() || is_search() || is_page_template( 'page-templates/prayer-wall.php' ) ) ) {
		return;
	}

	wp_enqueue_script(
		'lampandpath-feed',
		get_theme_file_uri( 'assets/js/feed.js' ),
		array(),
		lampandpath_asset_version( 'assets/js/feed.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lampandpath_feed_script' );
