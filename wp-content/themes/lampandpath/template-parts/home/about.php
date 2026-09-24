<?php
/**
 * Homepage: About section (text, team, links) and the newsletter card.
 *
 * The two halves are switched on separately in the Customizer, because the
 * header Subscribe button links to the newsletter card.
 *
 * @package Lampandpath
 */

$lampandpath_show_about = lampandpath_home_show( 'about' );
$lampandpath_show_news  = lampandpath_home_show( 'newsletter' );
if ( ! $lampandpath_show_about && ! $lampandpath_show_news ) {
	return;
}

$lampandpath_team  = ( $lampandpath_show_about && lampandpath_core_active() ) ? lampandpath_get_team() : array();
$lampandpath_links = array(
	'believe' => lampandpath_home_page_url( 'about', 'believe_page' ),
	'writers' => lampandpath_home_page_url( 'about', 'writers_page' ),
	'write'   => lampandpath_home_page_url( 'about', 'write_page' ),
);
?>
<section aria-labelledby="<?php echo esc_attr( $lampandpath_show_about ? 'about-heading' : 'newsletter-heading' ); ?>" class="bg-white">
	<div class="mx-auto flex max-w-site flex-col gap-14 px-4 py-16 sm:px-6 lg:flex-row lg:items-center lg:gap-[72px] lg:px-10 lg:py-24">
		<?php if ( $lampandpath_show_about ) : ?>
			<div class="min-w-0 flex-1">
				<h2 id="about-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'about', 'heading' ) ); ?></h2>
				<p class="mt-5 max-w-[600px] text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( lampandpath_home_option( 'about', 'text' ) ); ?></p>

				<?php if ( $lampandpath_team ) : ?>
					<ul class="mt-9 grid max-w-[600px] grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
						<?php foreach ( $lampandpath_team as $lampandpath_member ) : ?>
							<li class="flex items-center gap-3.5">
								<?php lampandpath_avatar( $lampandpath_member->ID, 'lg' ); ?>
								<span class="flex flex-col">
									<span class="text-base leading-[22px] font-semibold text-ink"><?php echo esc_html( $lampandpath_member->display_name ); ?></span>
									<span class="text-[15px] leading-[22px] text-muted"><?php echo esc_html( (string) get_user_meta( $lampandpath_member->ID, 'lp_role_title', true ) ); ?></span>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="mt-9 flex flex-wrap items-center gap-x-7 gap-y-3">
					<?php if ( $lampandpath_links['believe'] ) : ?>
						<a href="<?php echo esc_url( $lampandpath_links['believe'] ); ?>" class="<?php echo esc_attr( lampandpath_button_class( 'outline', 'md', true ) ); ?>">
							<?php lampandpath_icon( 'document', array( 'size' => 18 ) ); ?>
							<?php echo esc_html( lampandpath_home_option( 'about', 'believe_label' ) ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $lampandpath_links['writers'] ) : ?>
						<a href="<?php echo esc_url( $lampandpath_links['writers'] ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'about', 'writers_label' ) ); ?></a>
					<?php endif; ?>
					<?php if ( $lampandpath_links['write'] ) : ?>
						<a href="<?php echo esc_url( $lampandpath_links['write'] ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'about', 'write_label' ) ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		if ( $lampandpath_show_news ) {
			get_template_part( 'template-parts/components/newsletter-card', null, array( 'class' => 'shrink-0 lg:w-[486px]' ) );
		}
		?>
	</div>
</section>
