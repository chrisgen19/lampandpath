<?php
/**
 * Pagination under a list: numbered page links plus a "Load more" button.
 *
 * Without JavaScript readers use the numbered links, and "Load more" stays
 * hidden. assets/js/feed.js reveals "Load more", which fetches the next page
 * and appends its items to the [data-lp-feed] list. On the first page it also
 * hides the numbered links; on later pages they stay, so readers can go back.
 *
 * Args:
 * - query (WP_Query) The paged query; defaults to the main query.
 * - label (string)   "Load more" label.
 *
 * @package Lampandpath
 */

$lampandpath_query = ( isset( $args['query'] ) && $args['query'] instanceof WP_Query ) ? $args['query'] : $GLOBALS['wp_query'];
$lampandpath_total = (int) $lampandpath_query->max_num_pages;
if ( $lampandpath_total < 2 ) {
	return;
}

$lampandpath_current = max( 1, (int) $lampandpath_query->get( 'paged' ) );
$lampandpath_label   = isset( $args['label'] ) ? $args['label'] : __( 'Load more articles', 'lampandpath' );
$lampandpath_links   = paginate_links(
	array(
		'total'     => $lampandpath_total,
		'current'   => $lampandpath_current,
		'mid_size'  => 1,
		'prev_text' => lampandpath_get_icon( 'previous', array( 'size' => 18 ) ) . esc_html__( 'Previous', 'lampandpath' ),
		'next_text' => esc_html__( 'Next', 'lampandpath' ) . lampandpath_get_icon( 'next', array( 'size' => 18 ) ),
	)
);
?>
<?php // feed.js announces the "loaded" and "end" texts after each load. ?>
<div class="mt-10 flex flex-col items-start gap-6" data-lp-feed-nav data-lp-feed-page="<?php echo esc_attr( $lampandpath_current ); ?>"
	data-lp-feed-loaded="<?php /* translators: %d: number of items just loaded. Keep %d, the script fills it in. */ esc_attr_e( '%d more loaded.', 'lampandpath' ); ?>"
	data-lp-feed-end="<?php esc_attr_e( 'That’s everything.', 'lampandpath' ); ?>">
	<?php if ( $lampandpath_current < $lampandpath_total ) : ?>
		<a href="<?php echo esc_url( get_pagenum_link( $lampandpath_current + 1, false ) ); ?>" data-lp-feed-more hidden class="inline-flex h-13 items-center rounded-full border border-line-strong bg-white px-7 text-base leading-none font-semibold text-ink no-underline hover:border-ink aria-disabled:cursor-wait aria-disabled:opacity-60"><?php echo esc_html( $lampandpath_label ); ?></a>
	<?php endif; ?>

	<nav class="pagination" aria-label="<?php esc_attr_e( 'Pages', 'lampandpath' ); ?>" data-lp-feed-pages>
		<div class="nav-links">
			<?php echo $lampandpath_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() escapes its URLs; the labels are escaped above. ?>
		</div>
	</nav>

	<p class="sr-only" role="status" data-lp-feed-status></p>
</div>
