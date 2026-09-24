<?php
/**
 * Article archives: categories, topics (tags) and dates.
 *
 * Collections, writers, reading plans and verses have their own templates.
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
			'empty' => __( 'There are no articles here yet. Try another topic, or browse all articles.', 'lampandpath' ),
		)
	);
	?>
</main>

<?php
get_footer();
