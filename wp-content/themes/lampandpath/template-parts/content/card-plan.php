<?php
/**
 * Reading plan card: day bar, title, length, minutes per day, summary and start button.
 *
 * Used by the homepage plans section, the plan archive and "More reading plans".
 *
 * Args:
 * - post    (WP_Post) The plan; defaults to the current post in The Loop.
 * - heading (string)  Heading tag for the title, "h2" by default.
 *
 * @package Lampandpath
 */

$lampandpath_plan = get_post( isset( $args['post'] ) ? $args['post'] : null );
if ( ! $lampandpath_plan ) {
	return;
}

$lampandpath_heading = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3' ), true ) ) ? $args['heading'] : 'h2';
$lampandpath_days    = lampandpath_get_plan_days( $lampandpath_plan );
$lampandpath_minutes = (int) get_post_meta( $lampandpath_plan->ID, 'lp_minutes', true );
?>
<article id="<?php echo esc_attr( 'post-' . $lampandpath_plan->ID ); ?>" class="flex flex-col rounded-[20px] border border-line bg-white p-7 wrap-anywhere">
	<?php lampandpath_plan_bar( $lampandpath_days, 'h-9' ); ?>

	<<?php echo esc_html( $lampandpath_heading ); ?> class="mt-6 font-serif text-[26px] leading-[31px] font-semibold text-ink"><?php echo esc_html( get_the_title( $lampandpath_plan ) ); ?></<?php echo esc_html( $lampandpath_heading ); ?>>
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
