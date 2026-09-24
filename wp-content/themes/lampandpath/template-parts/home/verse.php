<?php
/**
 * Homepage: verse of the day.
 *
 * The Copy and Share buttons get their behavior in Phase 4; their data
 * attributes carry the text to copy and share.
 *
 * @package Lampandpath
 */

if ( ! lampandpath_home_show( 'verse' ) || ! lampandpath_core_active() ) {
	return;
}

$lampandpath_verse = lampandpath_get_verse_of_the_day();
if ( ! $lampandpath_verse ) {
	return;
}

$lampandpath_reference   = get_the_title( $lampandpath_verse );
$lampandpath_translation = (string) get_post_meta( $lampandpath_verse->ID, 'lp_translation', true );
$lampandpath_reflection  = (string) get_post_meta( $lampandpath_verse->ID, 'lp_reflection', true );
$lampandpath_chapter_url = (string) get_post_meta( $lampandpath_verse->ID, 'lp_chapter_url', true );
$lampandpath_share_text  = sprintf( '%1$s (%2$s)', lampandpath_plain_verse( $lampandpath_verse->post_content ), wp_strip_all_tags( $lampandpath_reference ) );
$lampandpath_outline     = lampandpath_button_class( 'outline', 'md', true );
?>
<section aria-labelledby="verse-heading" class="bg-accent-soft">
	<div class="mx-auto flex max-w-site flex-col gap-8 px-4 py-16 sm:px-6 lg:flex-row lg:gap-[72px] lg:px-10 lg:py-[88px]">
		<div class="flex shrink-0 flex-col lg:w-[282px]">
			<h2 id="verse-heading" class="text-base leading-6 font-semibold text-accent"><?php echo esc_html( lampandpath_home_option( 'verse', 'heading' ) ); ?></h2>
			<p class="mt-2 text-base leading-6 text-muted"><time datetime="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( wp_date( 'l, F j' ) ); ?></time></p>
			<p class="mt-6 font-serif text-2xl leading-[30px] font-semibold text-ink"><?php echo esc_html( $lampandpath_reference ); ?></p>
			<?php if ( $lampandpath_translation ) : ?>
				<p class="mt-1 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_translation ); ?></p>
			<?php endif; ?>
		</div>

		<div class="flex min-w-0 flex-1 flex-col items-start">
			<blockquote class="max-w-[820px] font-serif text-[28px] leading-[1.4] text-ink sm:text-[34px] lg:text-[40px] [&_sup]:top-0 [&_sup]:mr-1.5 [&_sup]:align-[0.9em] [&_sup]:font-sans [&_sup]:text-[17px] [&_sup]:leading-[0] [&_sup]:font-bold [&_sup]:text-accent">
				<?php echo lampandpath_format_verse( $lampandpath_verse->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Passed through wp_kses() in lampandpath_format_verse(). ?>
			</blockquote>

			<?php if ( $lampandpath_reflection ) : ?>
				<p class="mt-7 max-w-[640px] text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( $lampandpath_reflection ); ?></p>
			<?php endif; ?>

			<div class="mt-9 flex flex-wrap gap-3">
				<?php if ( $lampandpath_chapter_url ) : ?>
					<a href="<?php echo esc_url( $lampandpath_chapter_url ); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
						<?php lampandpath_icon( 'book-open', array( 'size' => 18 ) ); ?>
						<?php
						/* translators: %s: Bible chapter, e.g. "Psalm 46". */
						echo esc_html( sprintf( __( 'Read %s', 'lampandpath' ), lampandpath_reference_chapter( wp_strip_all_tags( $lampandpath_reference ) ) ) );
						?>
					</a>
				<?php endif; ?>
				<button type="button" data-lp-copy="<?php echo esc_attr( $lampandpath_share_text ); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
					<?php lampandpath_icon( 'copy', array( 'size' => 18 ) ); ?>
					<?php esc_html_e( 'Copy verse', 'lampandpath' ); ?>
				</button>
				<button type="button" data-lp-share="<?php echo esc_attr( $lampandpath_share_text ); ?>" data-lp-share-url="<?php echo esc_url( get_permalink( $lampandpath_verse ) ); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
					<?php lampandpath_icon( 'share', array( 'size' => 18 ) ); ?>
					<?php esc_html_e( 'Share', 'lampandpath' ); ?>
				</button>
			</div>
		</div>
	</div>
</section>
