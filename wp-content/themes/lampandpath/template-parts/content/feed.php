<?php
/**
 * A paged list of posts with "Load more", numbered pagination and an empty state.
 *
 * Articles use card-article.php and anything else card-result.php (search
 * results), unless the card is set. assets/js/feed.js appends the next page's
 * items to the [data-lp-feed] element, so it must hold only the items.
 *
 * Args:
 * - query (WP_Query) Defaults to the main query.
 * - card  (string)   Template part for every item, e.g. "template-parts/content/card-verse".
 * - class (string)   Classes for the list. Default "divide-y divide-line".
 * - empty (string)   Message when there is nothing to list.
 * - more  (string)   "Load more" label.
 *
 * @package Lampandpath
 */

$lampandpath_args  = wp_parse_args(
	$args,
	array(
		'query' => null,
		'card'  => '',
		'class' => 'divide-y divide-line',
		'empty' => __( 'There are no articles here yet.', 'lampandpath' ),
		'more'  => __( 'Load more articles', 'lampandpath' ),
	)
);
$lampandpath_query = $lampandpath_args['query'] instanceof WP_Query ? $lampandpath_args['query'] : $GLOBALS['wp_query'];

if ( ! $lampandpath_query->have_posts() ) {
	?>
	<p class="rounded-[18px] border border-dashed border-line-strong p-6 text-[17px] leading-[27px] text-ink-soft sm:p-8"><?php echo esc_html( $lampandpath_args['empty'] ); ?></p>
	<?php
	return;
}
?>
<div data-lp-feed class="<?php echo esc_attr( $lampandpath_args['class'] ); ?>">
	<?php
	while ( $lampandpath_query->have_posts() ) {
		$lampandpath_query->the_post();

		$lampandpath_card = $lampandpath_args['card'];
		if ( ! $lampandpath_card ) {
			$lampandpath_card = 'post' === get_post_type() ? 'template-parts/content/card-article' : 'template-parts/content/card-result';
		}
		get_template_part( $lampandpath_card );
	}
	wp_reset_postdata();
	?>
</div>

<?php
get_template_part(
	'template-parts/components/feed-more',
	null,
	array(
		'query' => $lampandpath_query,
		'label' => $lampandpath_args['more'],
	)
);
