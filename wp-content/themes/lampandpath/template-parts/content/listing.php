<?php
/**
 * Article listing below a page intro: filter chips, the main query's article
 * cards with "Load more", and the "More to read" sidebar from the homepage.
 *
 * Used by the Articles page, archives, writer pages, collections and search.
 *
 * Args:
 * - chips       (bool|array) true for the article filter chips (the "Homepage: article
 *                            filter chips" menu), or chip items (see filter-chips.php).
 * - chips_label (string)     Accessible name of the chips navigation.
 * - empty       (string)     Message when there are no articles.
 *
 * @package Lampandpath
 */

$lampandpath_chips = isset( $args['chips'] ) ? $args['chips'] : false;
if ( true === $lampandpath_chips ) {
	$lampandpath_chips = lampandpath_article_filter_items();
}
?>
<div class="bg-white">
	<div class="mx-auto flex max-w-site flex-col gap-14 px-4 pt-6 pb-16 sm:px-6 lg:flex-row lg:items-start lg:px-10 lg:pt-10 lg:pb-24">
		<div class="min-w-0 flex-1">
			<?php if ( $lampandpath_chips ) : ?>
				<div class="mt-4 mb-2">
					<?php
					get_template_part(
						'template-parts/components/filter-chips',
						null,
						array_filter(
							array(
								'items' => $lampandpath_chips,
								'label' => isset( $args['chips_label'] ) ? $args['chips_label'] : '',
							)
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/content/feed', null, isset( $args['empty'] ) ? array( 'empty' => $args['empty'] ) : array() ); ?>
		</div>

		<?php get_template_part( 'template-parts/home/latest-sidebar' ); ?>
	</div>
</div>
