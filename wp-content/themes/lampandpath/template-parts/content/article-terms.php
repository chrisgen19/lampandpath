<?php
/**
 * The topics (tags) and collections of the current article, as pill links.
 *
 * Runs inside The Loop; prints nothing when the article has neither.
 *
 * @package Lampandpath
 */

$lampandpath_groups = array(
	'tags'        => array( __( 'Topics', 'lampandpath' ), get_the_terms( get_the_ID(), 'post_tag' ) ),
	'collections' => array( __( 'In these collections', 'lampandpath' ), taxonomy_exists( 'lp_need' ) ? get_the_terms( get_the_ID(), 'lp_need' ) : array() ),
);
$lampandpath_groups = array_filter(
	$lampandpath_groups,
	static function ( $group ) {
		return is_array( $group[1] ) && $group[1];
	}
);
if ( ! $lampandpath_groups ) {
	return;
}

$lampandpath_chip = lampandpath_menu_link_classes()['topics'];
?>
<div class="mt-12 flex flex-col gap-6 border-t border-line pt-8">
	<?php foreach ( $lampandpath_groups as $lampandpath_key => $lampandpath_group ) : ?>
		<div>
			<p id="<?php echo esc_attr( 'article-' . $lampandpath_key ); ?>" class="text-[15px] leading-[22px] font-semibold text-muted"><?php echo esc_html( $lampandpath_group[0] ); ?></p>
			<ul aria-labelledby="<?php echo esc_attr( 'article-' . $lampandpath_key ); ?>" class="mt-3 flex flex-wrap gap-2">
				<?php foreach ( $lampandpath_group[1] as $lampandpath_term ) : ?>
					<li><a href="<?php echo esc_url( get_term_link( $lampandpath_term ) ); ?>" class="<?php echo esc_attr( $lampandpath_chip ); ?>"><?php echo esc_html( $lampandpath_term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</div>
