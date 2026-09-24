<?php
/**
 * The main template file
 *
 * Fallback for views without a more specific template. Every view the site
 * links to has its own template, so this rarely renders: singular content is
 * shown like a page, anything else like an article archive.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php if ( is_singular() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part( 'template-parts/components/page-intro', null, array( 'title' => get_the_title() ) ); ?>
				<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
					<div class="max-w-[720px]">
						<div class="entry-content wrap-anywhere">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<?php
		get_template_part( 'template-parts/components/page-intro', null, lampandpath_listing_intro() );
		get_template_part( 'template-parts/content/listing' );
		?>
	<?php endif; ?>
</main>

<?php
get_footer();
