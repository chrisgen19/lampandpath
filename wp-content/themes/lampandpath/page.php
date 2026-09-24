<?php
/**
 * Pages such as About, What we believe and Contact.
 *
 * The title sits in the intro band; the featured image, if any, opens the content.
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php get_template_part( 'template-parts/components/page-intro', null, array( 'title' => get_the_title() ) ); ?>

			<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
				<div class="max-w-[720px]">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="mb-10">
							<?php the_post_thumbnail( 'large', array( 'class' => 'h-auto w-full rounded-[20px]' ) ); ?>
						</figure>
					<?php endif; ?>

					<div class="entry-content wrap-anywhere">
						<?php the_content(); ?>
					</div>

					<?php
					wp_link_pages(
						array(
							'before'      => '<nav class="mt-10 flex flex-wrap items-center gap-2 font-semibold text-ink" aria-label="' . esc_attr__( 'Pages of this page', 'lampandpath' ) . '"><span class="mr-2 text-muted">' . esc_html__( 'Pages:', 'lampandpath' ) . '</span>',
							'after'       => '</nav>',
							'link_before' => '<span class="flex size-11 items-center justify-center rounded-full border border-line-strong">',
							'link_after'  => '</span>',
						)
					);
					?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
