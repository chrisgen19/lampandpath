<?php
/**
 * Template tags used by the header and footer.
 *
 * @package Lampandpath
 */

/**
 * Returns the social networks supported in the footer, keyed by icon name.
 *
 * @return array<string, string>
 */
function lampandpath_social_networks() {
	return array(
		'instagram' => __( 'Instagram', 'lampandpath' ),
		'facebook'  => __( 'Facebook', 'lampandpath' ),
		'youtube'   => __( 'YouTube', 'lampandpath' ),
		'podcast'   => __( 'Podcast', 'lampandpath' ),
	);
}

/**
 * Prints the site brand: the custom logo when set, otherwise the lamp mark and site title.
 *
 * @param string $display Tailwind display class for the link, e.g. "flex" or "inline-flex".
 */
function lampandpath_site_brand( $display = 'flex' ) {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$name = get_bloginfo( 'name' );
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="<?php echo esc_attr( $display ); ?> shrink-0 items-center gap-3 text-ink no-underline"
		aria-label="<?php /* translators: %s: site title. */ echo esc_attr( sprintf( __( '%s, home', 'lampandpath' ), $name ) ); ?>">
		<?php
		lampandpath_icon(
			'lamp-mark',
			array(
				'size'  => 30,
				'class' => 'text-accent',
			)
		);
		?>
		<span class="font-serif text-[27px] leading-none font-bold tracking-[-0.01em]"><?php echo esc_html( $name ); ?></span>
	</a>
	<?php
}

/**
 * Prints a favicon of the lamp mark in the Season color until a Site Icon is set.
 *
 * Without any icon, browsers request /favicon.ico and get a 404 on every page.
 * An inline SVG needs no extra request and follows the Season color.
 */
function lampandpath_default_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	$svg = str_replace(
		array( '<svg ', 'currentColor' ),
		array( '<svg xmlns="http://www.w3.org/2000/svg" ', lampandpath_accent() ),
		lampandpath_get_icon( 'lamp-mark', array( 'size' => 32 ) )
	);
	printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_attr( 'data:image/svg+xml,' . rawurlencode( $svg ) ) );
}
add_action( 'wp_head', 'lampandpath_default_favicon' );

/**
 * Keeps an uploaded logo at the header's height.
 *
 * @param array $attributes Custom logo <img> attributes.
 * @return array
 */
function lampandpath_custom_logo_attributes( $attributes ) {
	$attributes['class'] = trim( ( isset( $attributes['class'] ) ? $attributes['class'] : '' ) . ' h-11 w-auto' );

	return $attributes;
}
add_filter( 'get_custom_logo_image_attributes', 'lampandpath_custom_logo_attributes' );

/**
 * Returns the Subscribe button URL, defaulting to the homepage newsletter form.
 *
 * Returns an empty string when no custom link is set and the newsletter card is
 * hidden on the homepage, because the default #newsletter target would not exist.
 *
 * @return string
 */
function lampandpath_subscribe_url() {
	$url = lampandpath_get_option( 'lampandpath_subscribe_url' );
	if ( $url ) {
		return $url;
	}

	return lampandpath_home_show( 'newsletter' ) ? home_url( '/#newsletter' ) : '';
}

/**
 * Prints the footer social links for every network with a URL set in the Customizer.
 */
function lampandpath_social_links() {
	$links = array();
	foreach ( lampandpath_social_networks() as $network => $label ) {
		$url = lampandpath_get_option( 'lampandpath_social_' . $network );
		if ( $url ) {
			$links[ $network ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}

	if ( ! $links ) {
		return;
	}

	/* translators: %s: site title. */
	printf( '<ul class="mt-6 flex gap-2.5" aria-label="%s">', esc_attr( sprintf( __( 'Follow %s', 'lampandpath' ), get_bloginfo( 'name' ) ) ) );
	foreach ( $links as $network => $link ) {
		printf(
			'<li><a href="%1$s" aria-label="%2$s" class="flex size-11 items-center justify-center rounded-full border border-line-strong bg-white text-ink hover:border-accent hover:text-accent">%3$s</a></li>',
			esc_url( $link['url'] ),
			esc_attr( $link['label'] ),
			lampandpath_get_icon( $network, array( 'size' => 20 ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup.
		);
	}
	echo '</ul>';
}

/**
 * Prints a footer menu column. The column heading is the assigned menu's name.
 *
 * @param string $location Menu location, e.g. "footer-1".
 */
function lampandpath_footer_menu_column( $location ) {
	if ( ! lampandpath_has_menu_items( $location ) ) {
		return;
	}

	$heading_id = $location . '-heading';
	?>
	<nav aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="text-base leading-6 font-bold text-ink"><?php echo esc_html( wp_get_nav_menu_name( $location ) ); ?></h2>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'mt-2',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
	<?php
}

/**
 * Returns the footer copyright line with {year} and {site} replaced.
 *
 * @return string
 */
function lampandpath_copyright_text() {
	return strtr(
		lampandpath_get_option( 'lampandpath_copyright' ),
		array(
			'{year}' => wp_date( 'Y' ),
			'{site}' => get_bloginfo( 'name' ),
		)
	);
}
