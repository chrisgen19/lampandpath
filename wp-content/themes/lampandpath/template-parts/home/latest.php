<?php
/**
 * Homepage: latest articles with topic filter chips, plus the "More to read" sidebar.
 *
 * Without JavaScript the chips link to their category archives and "Load more"
 * links to the Articles page; Phase 4 makes them filter and load in place.
 *
 * @package Lampandpath
 */

if ( ! lampandpath_home_show( 'latest' ) ) {
	return;
}

// Only skip the featured article when it is actually shown above.
$lampandpath_hero      = lampandpath_home_show( 'hero' ) ? lampandpath_home_hero_post() : null;
$lampandpath_latest    = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'post__not_in'        => $lampandpath_hero ? array( $lampandpath_hero->ID ) : array(),
		'ignore_sticky_posts' => true,
	)
);
$lampandpath_posts_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
$lampandpath_filters   = lampandpath_menu_items( 'article-filters' );
$lampandpath_chip      = 'flex h-11 items-center rounded-full border px-[18px] text-[15px] leading-none no-underline';
?>
<section aria-labelledby="latest-heading" class="bg-white">
	<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
		<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
			<h2 id="latest-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'latest', 'heading' ) ); ?></h2>
			<a href="<?php echo esc_url( $lampandpath_posts_url ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'latest', 'all_label' ) ); ?></a>
		</div>

		<div class="mt-8 flex flex-col gap-14 lg:flex-row lg:items-start">
			<div class="min-w-0 flex-1">
				<?php // Without filter items the row would be a lone "All" chip, so it is left out. ?>
				<?php if ( $lampandpath_filters ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Filter articles by topic', 'lampandpath' ); ?>">
						<ul class="flex flex-wrap gap-2.5">
							<li><a href="<?php echo esc_url( $lampandpath_posts_url ); ?>" aria-current="true" data-lp-filter="0" class="<?php echo esc_attr( $lampandpath_chip ); ?> border-accent bg-accent font-semibold text-white"><?php esc_html_e( 'All', 'lampandpath' ); ?></a></li>
							<?php foreach ( $lampandpath_filters as $lampandpath_item ) : ?>
								<li><a href="<?php echo esc_url( $lampandpath_item->url ); ?>" data-lp-filter="<?php echo esc_attr( 'category' === $lampandpath_item->object ? $lampandpath_item->object_id : '' ); ?>" class="<?php echo esc_attr( $lampandpath_chip ); ?> border-line-strong bg-white font-medium text-ink hover:border-ink"><?php echo esc_html( $lampandpath_item->title ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>

				<?php if ( $lampandpath_latest->have_posts() ) : ?>
					<div class="mt-2 divide-y divide-line" data-lp-articles>
						<?php
						while ( $lampandpath_latest->have_posts() ) {
							$lampandpath_latest->the_post();
							get_template_part( 'template-parts/content/card-article', null, array( 'heading' => 'h3' ) );
						}
						wp_reset_postdata();
						?>
					</div>

					<div class="mt-9">
						<a href="<?php echo esc_url( $lampandpath_posts_url ); ?>" data-lp-load-more class="inline-flex h-13 items-center rounded-full border border-line-strong bg-white px-7 text-base leading-none font-semibold text-ink no-underline hover:border-ink"><?php echo esc_html( lampandpath_home_option( 'latest', 'more_label' ) ); ?></a>
					</div>
				<?php else : ?>
					<p class="mt-8 text-lg text-ink-soft"><?php esc_html_e( 'No articles yet. New articles will appear here.', 'lampandpath' ); ?></p>
				<?php endif; ?>
			</div>

			<?php get_template_part( 'template-parts/home/latest-sidebar' ); ?>
		</div>
	</div>
</section>
