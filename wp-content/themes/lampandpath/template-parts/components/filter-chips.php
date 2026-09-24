<?php
/**
 * A row of filter chips that link to other listings (categories, collections).
 *
 * Unlike the homepage chips, these are plain navigation: each chip is its own
 * archive page, and the current one is marked with aria-current.
 *
 * Args:
 * - items (array[]) Each [url, label, current] as returned by lampandpath_article_filter_items().
 * - label (string)  Accessible name of the navigation.
 *
 * @package Lampandpath
 */

if ( empty( $args['items'] ) ) {
	return;
}
?>
<nav aria-label="<?php echo esc_attr( isset( $args['label'] ) ? $args['label'] : __( 'Filter articles by topic', 'lampandpath' ) ); ?>">
	<ul class="flex flex-wrap gap-2.5">
		<?php foreach ( $args['items'] as $lampandpath_chip ) : ?>
			<li>
				<a href="<?php echo esc_url( $lampandpath_chip['url'] ); ?>"<?php echo $lampandpath_chip['current'] ? ' aria-current="page"' : ''; ?> class="<?php echo esc_attr( lampandpath_chip_class( $lampandpath_chip['current'] ) ); ?>"><?php echo esc_html( $lampandpath_chip['label'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
