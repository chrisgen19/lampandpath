<?php
/**
 * Homepage: "Read the Bible with us" reading plan cards.
 *
 * @package Lampandpath
 */

if ( ! lampandpath_home_show( 'plans' ) || ! lampandpath_core_active() ) {
	return;
}

$lampandpath_plans = lampandpath_get_featured_plans( 3 );
if ( ! $lampandpath_plans ) {
	return;
}

$lampandpath_intro       = lampandpath_home_option( 'plans', 'intro' );
$lampandpath_archive_url = get_post_type_archive_link( 'lp_plan' );
?>
<section aria-labelledby="plans-heading" class="bg-white">
	<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
		<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between lg:gap-6">
			<div class="max-w-[640px]">
				<h2 id="plans-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'plans', 'heading' ) ); ?></h2>
				<?php if ( $lampandpath_intro ) : ?>
					<p class="mt-4 text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( $lampandpath_intro ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $lampandpath_archive_url ) : ?>
				<a href="<?php echo esc_url( $lampandpath_archive_url ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'plans', 'all_label' ) ); ?></a>
			<?php endif; ?>
		</div>

		<div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			foreach ( $lampandpath_plans as $lampandpath_plan ) {
				get_template_part(
					'template-parts/content/card-plan',
					null,
					array(
						'post'    => $lampandpath_plan,
						'heading' => 'h3',
					)
				);
			}
			?>
		</div>
	</div>
</section>
