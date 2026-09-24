<?php
/**
 * The Articles page (the posts page set in Settings > Reading).
 *
 * The page's title is the heading and its content, if any, is the intro.
 * Filter chips link to the category archives.
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow">
	<?php
	get_template_part( 'template-parts/components/page-intro', null, lampandpath_listing_intro() );
	get_template_part(
		'template-parts/content/listing',
		null,
		array(
			'chips' => true,
			'empty' => __( 'No articles yet. New articles will appear here.', 'lampandpath' ),
		)
	);
	?>
</main>

<?php
get_footer();
