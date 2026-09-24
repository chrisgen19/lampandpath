<?php
/**
 * Reading plans archive (/reading-plans/): every plan as a card, in their "Order".
 *
 * The intro is the homepage plans intro (Appearance > Customize > Homepage > Reading plans).
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_intro = lampandpath_home_option( 'plans', 'intro' );
?>

<main id="primary" class="grow bg-white">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'title'       => post_type_archive_title( '', false ),
			'description' => $lampandpath_intro ? wpautop( esc_html( $lampandpath_intro ) ) : '',
		)
	);
	?>

	<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
		<?php
		get_template_part(
			'template-parts/content/feed',
			null,
			array(
				'card'  => 'template-parts/content/card-plan',
				'class' => 'grid gap-6 sm:grid-cols-2 lg:grid-cols-3',
				'empty' => __( 'There are no reading plans yet.', 'lampandpath' ),
				'more'  => __( 'Load more plans', 'lampandpath' ),
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
