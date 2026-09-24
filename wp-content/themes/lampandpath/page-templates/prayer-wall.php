<?php
/**
 * Template Name: Prayer wall
 *
 * The prayer request form beside every approved request that its author
 * agreed to share, newest first and paged. The page content is the intro.
 *
 * @package Lampandpath
 */

$lampandpath_paged = max( 1, (int) get_query_var( 'paged' ) );
$lampandpath_wall  = function_exists( 'lampandpath_query_prayer_wall' )
	? lampandpath_query_prayer_wall(
		array(
			'paged'          => $lampandpath_paged,
			'posts_per_page' => 10,
		)
	)
	: null;

// A page past the last one (e.g. an old link) is not found, rather than an empty wall.
if ( $lampandpath_paged > 1 && ( ! $lampandpath_wall || $lampandpath_paged > $lampandpath_wall->max_num_pages ) ) {
	$GLOBALS['wp_query']->set_404();
	status_header( 404 );
	nocache_headers();
	get_template_part( '404' );
	return;
}

get_header();

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
		<?php // #prayer is where the form returns to after a submission without JavaScript. ?>
		<section id="prayer" aria-labelledby="prayer-heading" class="shrink-0 lg:w-[486px]">
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

			<div class="mt-5">
				<?php
				get_template_part(
					'template-parts/content/feed',
					null,
					array(
						// Without lampandpath-core an empty query shows the empty message.
						'query' => $lampandpath_wall ? $lampandpath_wall : new WP_Query(),
						'card'  => 'template-parts/content/card-prayer',
						'tag'   => 'ul',
						'class' => 'flex flex-col gap-4',
						'empty' => __( 'No requests have been shared on the prayer wall yet.', 'lampandpath' ),
						'more'  => __( 'Load more requests', 'lampandpath' ),
					)
				);
				?>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();
