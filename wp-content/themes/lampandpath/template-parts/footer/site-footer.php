<?php
/**
 * Site footer: brand, verse, social links, menu columns and legal links.
 *
 * @package Lampandpath
 */

$lampandpath_verse     = lampandpath_get_option( 'lampandpath_footer_verse' );
$lampandpath_reference = lampandpath_get_option( 'lampandpath_footer_reference' );
?>
<footer class="bg-canvas">
	<div class="mx-auto max-w-site px-4 pt-16 pb-10 sm:px-6 lg:px-10 lg:pt-[72px]">
		<div class="flex flex-col gap-12 lg:flex-row lg:gap-[72px]">
			<div class="lg:w-96 lg:shrink-0">
				<?php lampandpath_site_brand( 'inline-flex' ); ?>

				<?php if ( $lampandpath_verse ) : ?>
					<p class="mt-5 font-serif text-[22px] leading-[1.4] text-ink italic"><?php echo esc_html( $lampandpath_verse ); ?></p>
				<?php endif; ?>

				<?php if ( $lampandpath_reference ) : ?>
					<p class="mt-2 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_reference ); ?></p>
				<?php endif; ?>

				<?php lampandpath_social_links(); ?>
			</div>

			<div class="grid grow grid-cols-2 gap-x-6 gap-y-8 sm:grid-cols-3">
				<?php
				foreach ( array( 'footer-1', 'footer-2', 'footer-3' ) as $lampandpath_location ) {
					lampandpath_footer_menu_column( $lampandpath_location );
				}
				?>
			</div>
		</div>

		<div class="mt-14 flex flex-col gap-2 border-t border-footer-line pt-6 text-[15px] leading-[22px] text-muted sm:flex-row sm:items-center sm:justify-between sm:gap-6">
			<p><?php echo esc_html( lampandpath_copyright_text() ); ?></p>

			<?php
			wp_nav_menu(
				array(
					'theme_location'       => 'legal',
					'container'            => 'nav',
					'container_aria_label' => __( 'Legal', 'lampandpath' ),
					'menu_class'           => 'flex flex-wrap gap-x-6',
					'depth'                => 1,
					'fallback_cb'          => false,
				)
			);
			?>
		</div>
	</div>
</footer>
