<?php
/**
 * Homepage: "Can we pray for you?" form and the latest requests from the prayer wall.
 *
 * Wall items come from template-parts/content/card-prayer.php, shared with the
 * Prayer wall page template.
 *
 * @package Lampandpath
 */

if ( ! lampandpath_home_show( 'prayer' ) ) {
	return;
}

$lampandpath_wall     = lampandpath_core_active() ? lampandpath_get_prayer_wall( 3 ) : array();
$lampandpath_wall_url = lampandpath_home_page_url( 'prayer', 'wall_page' );
$lampandpath_intro    = lampandpath_home_option( 'prayer', 'intro' );
?>
<section id="prayer" aria-labelledby="prayer-heading" class="bg-accent-soft">
	<div class="mx-auto flex max-w-site flex-col gap-14 px-4 py-16 sm:px-6 lg:flex-row lg:items-start lg:gap-[72px] lg:px-10 lg:py-24">
		<div class="shrink-0 lg:w-[486px]">
			<h2 id="prayer-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'prayer', 'heading' ) ); ?></h2>
			<?php if ( $lampandpath_intro ) : ?>
				<p class="mt-4 text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( $lampandpath_intro ); ?></p>
			<?php endif; ?>
			<div class="mt-8">
				<?php get_template_part( 'template-parts/components/prayer-form' ); ?>
			</div>
		</div>

		<div class="min-w-0 flex-1">
			<div class="flex flex-wrap items-end justify-between gap-x-4 gap-y-2">
				<h3 class="font-serif text-[30px] leading-9 font-semibold text-ink"><?php echo esc_html( lampandpath_home_option( 'prayer', 'wall_heading' ) ); ?></h3>
				<?php if ( $lampandpath_wall_url ) : ?>
					<a href="<?php echo esc_url( $lampandpath_wall_url ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'prayer', 'wall_label' ) ); ?></a>
				<?php endif; ?>
			</div>
			<p class="mt-2 text-[15px] leading-[22px] text-muted"><?php echo esc_html( lampandpath_home_option( 'prayer', 'wall_intro' ) ); ?></p>

			<?php if ( $lampandpath_wall ) : ?>
				<ul class="mt-5 flex flex-col gap-4">
					<?php
					foreach ( $lampandpath_wall as $lampandpath_request ) {
						get_template_part( 'template-parts/content/card-prayer', null, array( 'post' => $lampandpath_request ) );
					}
					?>
				</ul>
			<?php else : ?>
				<p class="mt-5 rounded-[18px] border border-dashed border-line-strong p-6 text-[17px] leading-[27px] text-ink-soft"><?php esc_html_e( 'No requests have been shared on the prayer wall yet.', 'lampandpath' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
