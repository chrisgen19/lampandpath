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
			<?php foreach ( $lampandpath_plans as $lampandpath_plan ) : ?>
				<?php
				$lampandpath_days    = lampandpath_get_plan_days( $lampandpath_plan );
				$lampandpath_minutes = (int) get_post_meta( $lampandpath_plan->ID, 'lp_minutes', true );
				?>
				<article class="flex flex-col rounded-[20px] border border-line bg-white p-7">
					<?php // One segment per day, with day one highlighted. ?>
					<div aria-hidden="true" class="flex h-9 gap-1">
						<?php for ( $lampandpath_day = 1; $lampandpath_day <= $lampandpath_days; $lampandpath_day++ ) : ?>
							<span class="flex-1 rounded-[3px] <?php echo 1 === $lampandpath_day ? 'bg-accent' : 'bg-accent-tint'; ?>"></span>
						<?php endfor; ?>
					</div>

					<h3 class="mt-6 font-serif text-[26px] leading-[31px] font-semibold text-ink"><?php echo esc_html( get_the_title( $lampandpath_plan ) ); ?></h3>
					<p class="mt-2 flex flex-wrap gap-x-4 text-[15px] leading-[22px] text-muted">
						<span><?php /* translators: %d: number of days in a reading plan. */ echo esc_html( sprintf( _n( '%d day', '%d days', $lampandpath_days, 'lampandpath' ), $lampandpath_days ) ); ?></span>
						<?php if ( $lampandpath_minutes ) : ?>
							<span><?php /* translators: %d: minutes of reading per day. */ echo esc_html( sprintf( __( 'About %d min a day', 'lampandpath' ), $lampandpath_minutes ) ); ?></span>
						<?php endif; ?>
					</p>
					<?php if ( has_excerpt( $lampandpath_plan ) ) : ?>
						<p class="mt-3.5 text-[17px] leading-[26px] text-ink-soft"><?php echo esc_html( get_the_excerpt( $lampandpath_plan ) ); ?></p>
					<?php endif; ?>

					<div class="mt-auto pt-6">
						<a href="<?php echo esc_url( get_permalink( $lampandpath_plan ) ); ?>" class="<?php echo esc_attr( lampandpath_button_class( 'accent-outline', 'md' ) ); ?>">
							<?php echo esc_html( lampandpath_home_option( 'plans', 'start_label' ) ); ?>
							<span class="sr-only"><?php echo esc_html( ': ' . get_the_title( $lampandpath_plan ) ); ?></span>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
