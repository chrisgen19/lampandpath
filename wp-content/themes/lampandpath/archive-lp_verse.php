<?php
/**
 * Verses of the day archive (/verses/): every published verse, newest first.
 *
 * Scheduled verses stay hidden until their day.
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'title'       => post_type_archive_title( '', false ),
			'description' => '<p>' . esc_html__( 'A verse to carry through each day, with a question to reflect on. Here is every verse we have shared, newest first.', 'lampandpath' ) . '</p>',
			'tone'        => 'accent',
		)
	);
	?>

	<div class="mx-auto max-w-site px-4 pt-4 pb-16 sm:px-6 lg:px-10 lg:pt-8 lg:pb-24">
		<div class="max-w-[1040px]">
			<?php
			get_template_part(
				'template-parts/content/feed',
				null,
				array(
					'card'  => 'template-parts/content/card-verse',
					'empty' => __( 'No verses have been shared yet.', 'lampandpath' ),
					'more'  => __( 'Load more verses', 'lampandpath' ),
				)
			);
			?>
		</div>
	</div>
</main>

<?php
get_footer();
