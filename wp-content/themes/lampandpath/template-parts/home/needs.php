<?php
/**
 * Homepage: "Where are you today?" collection cards (Posts > Collections).
 *
 * @package Lampandpath
 */

if ( ! lampandpath_home_show( 'needs' ) || ! lampandpath_core_active() ) {
	return;
}

$lampandpath_needs = lampandpath_get_needs();
if ( ! $lampandpath_needs ) {
	return;
}

$lampandpath_intro = lampandpath_home_option( 'needs', 'intro' );
?>
<section aria-labelledby="needs-heading" class="bg-surface-soft">
	<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
		<h2 id="needs-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'needs', 'heading' ) ); ?></h2>
		<?php if ( $lampandpath_intro ) : ?>
			<p class="mt-4 max-w-[620px] text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( $lampandpath_intro ); ?></p>
		<?php endif; ?>

		<div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $lampandpath_needs as $lampandpath_need ) : ?>
				<?php
				$lampandpath_icon  = (string) get_term_meta( $lampandpath_need->term_id, 'lp_icon', true );
				$lampandpath_verse = (string) get_term_meta( $lampandpath_need->term_id, 'lp_verse_ref', true );
				?>
				<a href="<?php echo esc_url( get_term_link( $lampandpath_need ) ); ?>" class="flex flex-col rounded-[18px] border border-line bg-white p-7 text-ink no-underline hover:border-accent">
					<span class="flex items-center gap-3.5">
						<span aria-hidden="true" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-accent-tint text-accent">
							<?php lampandpath_icon( $lampandpath_icon ? $lampandpath_icon : 'lamp-mark', array( 'size' => 22 ) ); ?>
						</span>
						<span class="font-serif text-[26px] leading-[31px] font-semibold"><?php echo esc_html( $lampandpath_need->name ); ?></span>
					</span>
					<?php if ( $lampandpath_need->description ) : ?>
						<span class="mt-4 text-[17px] leading-[26px] text-ink-soft"><?php echo esc_html( $lampandpath_need->description ); ?></span>
					<?php endif; ?>
					<?php if ( $lampandpath_verse ) : ?>
						<span class="mt-auto pt-[18px] text-[15px] leading-[22px] font-semibold text-accent"><?php echo esc_html( $lampandpath_verse ); ?></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
