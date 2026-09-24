<?php
/**
 * Template Name: Prayer wall
 *
 * The prayer request form beside every approved request that its author
 * agreed to share, newest first and paged. The page content is the intro.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_wall = function_exists( 'lampandpath_query_prayer_wall' )
	? lampandpath_query_prayer_wall(
		array(
			'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
			'posts_per_page' => 10,
		)
	)
	: null;
$lampandpath_intro = lampandpath_home_option( 'prayer', 'intro' );
?>

<main id="primary" class="grow bg-accent-soft">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part(
			'template-parts/components/page-intro',
			null,
			array(
				'title'       => get_the_title(),
				'description' => '' !== trim( get_the_content() ) ? apply_filters( 'the_content', get_the_content() ) : wpautop( esc_html( lampandpath_home_option( 'prayer', 'wall_intro' ) ) ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
				'tone'        => 'accent',
			)
		);
	endwhile;
	?>

	<div class="mx-auto flex max-w-site flex-col gap-14 px-4 pb-16 sm:px-6 lg:flex-row lg:items-start lg:gap-[72px] lg:px-10 lg:pb-24">
		<section aria-labelledby="prayer-heading" class="shrink-0 lg:w-[486px]">
			<h2 id="prayer-heading" class="font-serif text-[30px] leading-9 font-semibold text-ink"><?php echo esc_html( lampandpath_home_option( 'prayer', 'heading' ) ); ?></h2>
			<?php if ( $lampandpath_intro ) : ?>
				<p class="mt-3 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( $lampandpath_intro ); ?></p>
			<?php endif; ?>
			<div class="mt-6">
				<?php get_template_part( 'template-parts/components/prayer-form' ); ?>
			</div>
		</section>

		<section aria-labelledby="wall-heading" class="min-w-0 flex-1">
			<h2 id="wall-heading" class="font-serif text-[30px] leading-9 font-semibold text-ink"><?php esc_html_e( 'Recent requests', 'lampandpath' ); ?></h2>

			<?php if ( $lampandpath_wall && $lampandpath_wall->have_posts() ) : ?>
				<ul class="mt-5 flex flex-col gap-4" data-lp-feed>
					<?php
					foreach ( $lampandpath_wall->posts as $lampandpath_request ) {
						get_template_part( 'template-parts/content/card-prayer', null, array( 'post' => $lampandpath_request ) );
					}
					?>
				</ul>
				<?php
				get_template_part(
					'template-parts/components/feed-more',
					null,
					array(
						'query' => $lampandpath_wall,
						'label' => __( 'Load more requests', 'lampandpath' ),
					)
				);
				?>
			<?php else : ?>
				<p class="mt-5 rounded-[18px] border border-dashed border-line-strong p-6 text-[17px] leading-[27px] text-ink-soft"><?php esc_html_e( 'No requests have been shared on the prayer wall yet.', 'lampandpath' ); ?></p>
			<?php endif; ?>
		</section>
	</div>
</main>

<?php
get_footer();
