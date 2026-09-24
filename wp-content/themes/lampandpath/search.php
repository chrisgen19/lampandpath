<?php
/**
 * Search results: articles as article cards, pages, plans and verses as simple results.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_query = get_search_query( false );
$lampandpath_found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main id="primary" class="grow">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'eyebrow' => '' !== $lampandpath_query ? __( 'Search', 'lampandpath' ) : '',
			/* translators: %s: search query. */
			'title'   => '' !== $lampandpath_query ? sprintf( __( 'Results for “%s”', 'lampandpath' ), $lampandpath_query ) : __( 'Search', 'lampandpath' ),
			/* translators: %s: number of search results. */
			'meta'    => '' !== $lampandpath_query ? array( sprintf( _n( '%s result', '%s results', $lampandpath_found, 'lampandpath' ), number_format_i18n( $lampandpath_found ) ) ) : array(),
			'search'  => true,
		)
	);
	get_template_part(
		'template-parts/content/listing',
		null,
		array(
			'empty' => __( 'Nothing matched your search. Try different or fewer words, or browse by topic.', 'lampandpath' ),
		)
	);
	?>
</main>

<?php
get_footer();
