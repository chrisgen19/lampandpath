<?php
/**
 * Collection ("Where are you today?"), e.g. "I'm anxious": icon, description,
 * its verse, the other collections as chips, and the collection's articles.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_need      = get_queried_object();
$lampandpath_icon      = (string) get_term_meta( $lampandpath_need->term_id, 'lp_icon', true );
$lampandpath_verse     = (string) get_term_meta( $lampandpath_need->term_id, 'lp_verse_ref', true );
$lampandpath_verse_url = (string) get_term_meta( $lampandpath_need->term_id, 'lp_verse_url', true );

$lampandpath_chips = array();
foreach ( function_exists( 'lampandpath_get_needs' ) ? lampandpath_get_needs() : array() as $lampandpath_term ) {
	$lampandpath_chips[] = array(
		'url'     => get_term_link( $lampandpath_term ),
		'label'   => $lampandpath_term->name,
		'current' => $lampandpath_term->term_id === $lampandpath_need->term_id,
	);
}
?>

<main id="primary" class="grow">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'eyebrow'     => __( 'Collection', 'lampandpath' ),
			'title'       => $lampandpath_need->name,
			'icon'        => $lampandpath_icon ? $lampandpath_icon : 'lamp-mark',
			'tone'        => 'accent',
			'description' => term_description(),
			// A verse without a link is still shown, as a fact under the title.
			'meta'        => $lampandpath_verse_url ? array() : array( $lampandpath_verse ),
			/* translators: %s: Bible reference, e.g. "Psalm 34:18". */
			'links'       => ( $lampandpath_verse && $lampandpath_verse_url ) ? array( array( $lampandpath_verse_url, sprintf( __( 'Read %s', 'lampandpath' ), $lampandpath_verse ), 'book-open' ) ) : array(),
		)
	);
	get_template_part(
		'template-parts/content/listing',
		null,
		array(
			// Only worth showing when there is somewhere else to go.
			'chips'       => count( $lampandpath_chips ) > 1 ? $lampandpath_chips : false,
			'chips_label' => __( 'Other collections', 'lampandpath' ),
			'empty'       => __( 'There are no articles in this collection yet.', 'lampandpath' ),
		)
	);
	?>
</main>

<?php
get_footer();
