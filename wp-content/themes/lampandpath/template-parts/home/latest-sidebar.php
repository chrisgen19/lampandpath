<?php
/**
 * Homepage: "More to read" sidebar with the most read articles and the topics menu.
 *
 * Each card hides itself when it has nothing to show.
 *
 * @package Lampandpath
 */

$lampandpath_popular    = lampandpath_core_active() ? lampandpath_get_most_read( 5 ) : array();
$lampandpath_has_topics = has_nav_menu( 'topics' );
if ( ! $lampandpath_popular && ! $lampandpath_has_topics ) {
	return;
}

$lampandpath_card_heading = 'font-serif text-[26px] leading-[31px] font-semibold text-ink';
?>
<aside aria-label="<?php esc_attr_e( 'More to read', 'lampandpath' ); ?>" class="flex shrink-0 flex-col gap-8 lg:w-96">
	<?php if ( $lampandpath_popular ) : ?>
		<section aria-labelledby="popular-heading" class="rounded-[20px] bg-surface-soft p-7">
			<h2 id="popular-heading" class="<?php echo esc_attr( $lampandpath_card_heading ); ?>"><?php echo esc_html( lampandpath_home_option( 'latest', 'popular_heading' ) ); ?></h2>
			<ol class="mt-2">
				<?php foreach ( $lampandpath_popular as $lampandpath_index => $lampandpath_post ) : ?>
					<li class="flex gap-4 border-t border-line py-3.5">
						<span aria-hidden="true" class="w-7 shrink-0 font-serif text-[30px] leading-7 font-bold text-accent"><?php echo esc_html( number_format_i18n( $lampandpath_index + 1 ) ); ?></span>
						<span class="flex flex-col gap-1">
							<a href="<?php echo esc_url( get_permalink( $lampandpath_post ) ); ?>" class="text-[17px] leading-6 font-semibold text-ink no-underline hover:text-accent"><?php echo esc_html( get_the_title( $lampandpath_post ) ); ?></a>
							<span class="text-sm leading-5 text-muted"><?php echo esc_html( lampandpath_reading_time_label( $lampandpath_post ) ); ?></span>
						</span>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>

	<?php if ( $lampandpath_has_topics ) : ?>
		<section aria-labelledby="topics-heading" class="rounded-[20px] border border-line p-7">
			<h2 id="topics-heading" class="<?php echo esc_attr( $lampandpath_card_heading ); ?>"><?php echo esc_html( lampandpath_home_option( 'latest', 'topics_heading' ) ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'topics',
					'container'      => false,
					'menu_class'     => 'mt-[18px] flex flex-wrap gap-2',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</section>
	<?php endif; ?>
</aside>
