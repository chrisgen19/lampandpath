<?php
/**
 * Single verse of the day, where shared verse links land.
 *
 * Laid out like the homepage verse section, dated with the verse's own day,
 * followed by recent verses. assets/js/interactions.js handles Copy and
 * Share (data-lp-copy, data-lp-share).
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php
	while ( have_posts() ) :
		the_post();

		$lampandpath_reference   = wp_strip_all_tags( get_the_title() );
		$lampandpath_translation = (string) get_post_meta( get_the_ID(), 'lp_translation', true );
		$lampandpath_reflection  = (string) get_post_meta( get_the_ID(), 'lp_reflection', true );
		$lampandpath_chapter_url = (string) get_post_meta( get_the_ID(), 'lp_chapter_url', true );
		$lampandpath_share_text  = sprintf( '%1$s (%2$s)', lampandpath_plain_verse( get_the_content() ), $lampandpath_reference );
		$lampandpath_outline     = lampandpath_button_class( 'outline', 'md', true );
		$lampandpath_recent      = new WP_Query(
			array(
				'post_type'      => 'lp_verse',
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'no_found_rows'  => true,
			)
		);
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-accent-soft' ); ?>>
			<div class="mx-auto flex max-w-site flex-col gap-8 px-4 py-16 sm:px-6 lg:flex-row lg:gap-[72px] lg:px-10 lg:py-[88px]">
				<header class="flex shrink-0 flex-col lg:w-[282px]">
					<p class="text-base leading-6 font-semibold text-accent"><?php echo esc_html( lampandpath_home_option( 'verse', 'heading' ) ); ?></p>
					<p class="mt-2 text-base leading-6 text-muted"><time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'l, F j, Y' ) ); ?></time></p>
					<h1 class="mt-6 font-serif text-[30px] leading-9 font-semibold text-ink"><?php echo esc_html( $lampandpath_reference ); ?></h1>
					<?php if ( $lampandpath_translation ) : ?>
						<p class="mt-1 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_translation ); ?></p>
					<?php endif; ?>
				</header>

				<div class="flex min-w-0 flex-1 flex-col items-start wrap-anywhere">
					<blockquote class="max-w-[820px] font-serif text-[28px] leading-[1.4] text-ink sm:text-[34px] lg:text-[40px] [&_sup]:top-0 [&_sup]:mr-1.5 [&_sup]:align-[0.9em] [&_sup]:font-sans [&_sup]:text-[17px] [&_sup]:leading-[0] [&_sup]:font-bold [&_sup]:text-accent">
						<?php echo lampandpath_format_verse( get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Passed through wp_kses() in lampandpath_format_verse(). ?>
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
								echo esc_html( sprintf( __( 'Read %s', 'lampandpath' ), lampandpath_reference_chapter( $lampandpath_reference ) ) );
								?>
							</a>
						<?php endif; ?>
						<button type="button" data-lp-copy="<?php echo esc_attr( $lampandpath_share_text ); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
							<?php lampandpath_icon( 'copy', array( 'size' => 18 ) ); ?>
							<span data-lp-label><?php esc_html_e( 'Copy verse', 'lampandpath' ); ?></span>
						</button>
						<button type="button" data-lp-share="<?php echo esc_attr( $lampandpath_share_text ); ?>" data-lp-share-url="<?php the_permalink(); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
							<?php lampandpath_icon( 'share', array( 'size' => 18 ) ); ?>
							<span data-lp-label><?php esc_html_e( 'Share', 'lampandpath' ); ?></span>
						</button>
					</div>
				</div>
			</div>
		</article>

		<?php if ( $lampandpath_recent->have_posts() ) : ?>
			<section aria-labelledby="recent-verses-heading">
				<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
					<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
						<h2 id="recent-verses-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php esc_html_e( 'Recent verses', 'lampandpath' ); ?></h2>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'lp_verse' ) ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php esc_html_e( 'See all verses', 'lampandpath' ); ?></a>
					</div>
					<div class="mt-4 max-w-[1040px] divide-y divide-line">
						<?php
						while ( $lampandpath_recent->have_posts() ) {
							$lampandpath_recent->the_post();
							get_template_part( 'template-parts/content/card-verse', null, array( 'heading' => 'h3' ) );
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>

<?php
get_footer();
