<?php
/**
 * The homepage.
 *
 * Renders the sections from docs/design/homepage.html in order. Each part
 * checks whether it is switched on in Appearance > Customize > Homepage and
 * hides itself when it has no content (e.g. no reading plans yet).
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow">
	<?php
	foreach ( array( 'hero', 'verse', 'latest', 'needs', 'plans', 'prayer', 'about' ) as $lampandpath_section ) {
		get_template_part( 'template-parts/home/' . $lampandpath_section );
	}
	?>
</main>

<?php
get_footer();
