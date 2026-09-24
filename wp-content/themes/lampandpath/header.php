<?php
/**
 * The header for the theme
 *
 * Displays the <head> section, the skip link, the sticky site header and the
 * search dialog, and opens the page wrapper that footer.php closes.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Lampandpath
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-canvas font-sans text-ink antialiased' ); ?>>
<?php wp_body_open(); ?>
<a class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[60] focus:rounded-full focus:bg-white focus:px-5 focus:py-3 focus:font-semibold focus:text-ink focus:shadow-lg" href="#primary"><?php esc_html_e( 'Skip to content', 'lampandpath' ); ?></a>

<div class="flex min-h-screen flex-col">
	<?php
	get_template_part( 'template-parts/header/site-header' );
	get_template_part( 'template-parts/header/search-dialog' );
