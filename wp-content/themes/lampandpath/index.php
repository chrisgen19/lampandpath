<?php
/**
 * The main template file
 *
 * Fallback template used when no more specific template matches the query.
 * Dedicated templates for articles, pages and archives arrive in later phases.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
		<?php if ( ! is_singular() ) : ?>
			<h1 class="font-serif text-4xl leading-[1.1] font-semibold tracking-[-0.01em] text-ink lg:text-[44px]"><?php echo esc_html( lampandpath_listing_title() ); ?></h1>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="<?php echo esc_attr( is_singular() ? '' : 'mt-8 divide-y divide-line' ); ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'py-8 first:pt-0' ); ?>>
						<?php if ( is_singular() ) : ?>
							<?php the_title( '<h1 class="max-w-[690px] font-serif text-4xl leading-[1.06] font-semibold tracking-[-0.015em] text-ink lg:text-[64px]">', '</h1>' ); ?>
							<div class="prose prose-lg mt-8 max-w-[720px] prose-headings:font-serif prose-a:text-accent">
								<?php the_content(); ?>
							</div>
						<?php else : ?>
							<?php the_title( '<h2 class="font-serif text-[26px] leading-[1.22] font-semibold"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" class="text-ink hover:text-accent">', '</a></h2>' ); ?>
							<div class="mt-2.5 max-w-[720px] text-[17px] leading-[27px] text-ink-soft">
								<?php the_excerpt(); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( array( 'class' => 'pagination mt-12' ) ); ?>
		<?php else : ?>
			<p class="mt-6 text-[19px] leading-[30px] text-ink-soft"><?php esc_html_e( 'Nothing found. Try a different search.', 'lampandpath' ); ?></p>
			<div class="mt-6 max-w-xl"><?php get_search_form(); ?></div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
