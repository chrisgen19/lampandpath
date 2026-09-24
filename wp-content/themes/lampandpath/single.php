<?php
/**
 * Single article.
 *
 * The header follows the homepage featured article: category, title, excerpt,
 * byline, Save and Share, with the featured image beside it. Below come the
 * content, topics and collections, the author box, the newsletter card and
 * related articles. assets/js/interactions.js handles Save and Share
 * (data-lp-save, data-lp-share). Comments are switched off site-wide in lampandpath-core.
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php
	while ( have_posts() ) :
		the_post();

		$lampandpath_category = lampandpath_primary_category();
		$lampandpath_author   = (int) get_the_author_meta( 'ID' );
		$lampandpath_minutes  = lampandpath_reading_time_label();
		$lampandpath_outline  = lampandpath_button_class( 'outline', 'md', true );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="mx-auto flex max-w-site flex-col gap-10 px-4 pt-10 pb-12 sm:px-6 lg:flex-row lg:items-center lg:gap-16 lg:px-10 lg:pt-16 lg:pb-16">
				<div class="flex min-w-0 max-w-[720px] flex-1 flex-col items-start wrap-anywhere">
					<?php if ( $lampandpath_category ) : ?>
						<a href="<?php echo esc_url( get_category_link( $lampandpath_category ) ); ?>" class="mb-5 text-[15px] leading-[22px] font-semibold text-accent no-underline hover:underline"><?php echo esc_html( $lampandpath_category->name ); ?></a>
					<?php endif; ?>

					<?php the_title( '<h1 class="font-serif text-[40px] leading-[1.06] font-semibold tracking-[-0.015em] text-balance text-ink sm:text-[52px] lg:text-[60px]">', '</h1>' ); ?>

					<?php // Only a written excerpt: an automatic one would repeat the first paragraph. ?>
					<?php if ( has_excerpt() ) : ?>
						<p class="mt-6 text-lg leading-[30px] text-ink-soft lg:text-[20px] lg:leading-8"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>

					<div class="mt-8 flex items-center gap-3.5">
						<?php lampandpath_avatar( $lampandpath_author, 'md' ); ?>
						<div class="flex flex-col gap-0.5">
							<a href="<?php echo esc_url( get_author_posts_url( $lampandpath_author ) ); ?>" class="text-base leading-[22px] font-semibold text-ink no-underline hover:text-accent hover:underline"><?php the_author(); ?></a>
							<span class="flex flex-wrap items-center gap-x-4 text-[15px] leading-[22px] text-muted">
								<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></time>
								<?php if ( $lampandpath_minutes ) : ?>
									<span class="flex items-center gap-1.5"><?php lampandpath_icon( 'clock', array( 'size' => 16 ) ); ?><?php echo esc_html( $lampandpath_minutes ); ?></span>
								<?php endif; ?>
							</span>
						</div>
					</div>

					<div class="mt-8 flex flex-wrap gap-3">
						<?php // A toggle: pressed means saved, and the bookmark fills in. ?>
						<button type="button" data-lp-save="<?php the_ID(); ?>" aria-pressed="false" class="group <?php echo esc_attr( $lampandpath_outline ); ?> aria-pressed:border-accent aria-pressed:bg-accent-soft aria-pressed:text-accent">
							<?php lampandpath_icon( 'bookmark', array( 'size' => 18, 'class' => 'group-aria-pressed:fill-current' ) ); ?>
							<?php echo esc_html( lampandpath_home_option( 'hero', 'save_label' ) ); ?>
						</button>
						<button type="button" data-lp-share="<?php echo esc_attr( wp_strip_all_tags( get_the_title() ) ); ?>" data-lp-share-url="<?php the_permalink(); ?>" class="<?php echo esc_attr( $lampandpath_outline ); ?>">
							<?php lampandpath_icon( 'share', array( 'size' => 18 ) ); ?>
							<span data-lp-label><?php esc_html_e( 'Share', 'lampandpath' ); ?></span>
						</button>
					</div>
				</div>

				<?php get_template_part( 'template-parts/content/featured-image' ); ?>
			</header>

			<div class="mx-auto max-w-site px-4 pb-16 sm:px-6 lg:px-10 lg:pb-24">
				<div class="max-w-[720px]">
					<div class="entry-content wrap-anywhere">
						<?php the_content(); ?>
					</div>

					<?php
					wp_link_pages(
						array(
							'before'      => '<nav class="mt-10 flex flex-wrap items-center gap-2 font-semibold text-ink" aria-label="' . esc_attr__( 'Pages of this article', 'lampandpath' ) . '"><span class="mr-2 text-muted">' . esc_html__( 'Pages:', 'lampandpath' ) . '</span>',
							'after'       => '</nav>',
							'link_before' => '<span class="flex size-11 items-center justify-center rounded-full border border-line-strong">',
							'link_after'  => '</span>',
						)
					);

					get_template_part( 'template-parts/content/article-terms' );
					get_template_part( 'template-parts/content/author-box', null, array( 'author' => $lampandpath_author ) );

					if ( lampandpath_home_show( 'newsletter' ) ) {
						get_template_part( 'template-parts/components/newsletter-card', null, array( 'class' => 'mt-12' ) );
					}
					?>
				</div>
			</div>
		</article>

		<?php get_template_part( 'template-parts/content/related', null, array( 'post' => get_post() ) ); ?>
	<?php endwhile; ?>
</main>

<?php
get_footer();
