<?php
/**
 * Helpers for the inner templates: archive intros, filter chips, related
 * articles and featured image orientation.
 *
 * @package Lampandpath
 */

/*
 * Archive intros show the kind of archive ("Category", "Topic") as a separate
 * label above the title, so the title itself drops WordPress's "Category:" prefix.
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Keeps the form result flags (?lp_prayer=, ?lp_subscribe=) out of page links.
 *
 * The no-JavaScript form handlers add them to the page they return to, and
 * WordPress copies the current query string into pagination links, which
 * would show the form's message again on every page.
 *
 * @param string $url Page link.
 * @return string
 */
function lampandpath_clean_pagenum_link( $url ) {
	return remove_query_arg( array( 'lp_prayer', 'lp_subscribe' ), $url );
}
add_filter( 'get_pagenum_link', 'lampandpath_clean_pagenum_link' );

/**
 * Returns the intro of the current listing for template-parts/components/page-intro.php.
 *
 * Covers the Articles page, categories, tags, dates and other archives. The
 * author, collection, reading plan, verse and search templates build their own.
 *
 * @return array{eyebrow: string, title: string, description: string}
 */
function lampandpath_listing_intro() {
	if ( is_home() ) {
		// get_post( 0 ) would return the current post, so no posts page means no page.
		$page_id = (int) get_option( 'page_for_posts' );
		$page    = $page_id ? get_post( $page_id ) : null;

		// WordPress ignores the posts page's content, so it becomes the intro, falling back to the tagline.
		if ( $page && '' !== trim( $page->post_content ) ) {
			$description = apply_filters( 'the_content', $page->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
		} else {
			$description = wpautop( esc_html( get_bloginfo( 'description' ) ) );
		}

		return array(
			'eyebrow'     => '',
			'title'       => $page ? get_the_title( $page ) : __( 'Articles', 'lampandpath' ),
			'description' => $description,
		);
	}

	$eyebrow = '';
	if ( is_category() ) {
		$eyebrow = __( 'Category', 'lampandpath' );
	} elseif ( is_tag() ) {
		$eyebrow = __( 'Topic', 'lampandpath' );
	} elseif ( is_tax() ) {
		$taxonomy = get_taxonomy( get_queried_object()->taxonomy );
		$eyebrow  = $taxonomy ? $taxonomy->labels->singular_name : '';
	} elseif ( is_date() ) {
		$eyebrow = __( 'Archive', 'lampandpath' );
	}

	return array(
		'eyebrow'     => $eyebrow,
		'title'       => wp_strip_all_tags( get_the_archive_title() ),
		// Term descriptions are already formatted; post type descriptions are admin notes, so they are skipped.
		'description' => ( is_category() || is_tag() || is_tax() ) ? term_description() : '',
	);
}

/**
 * Returns the filter chips for article listings: "All" plus the article-filters menu.
 *
 * The chip for the current listing is marked current: "All" on the Articles
 * page, or the chip that links to the category (or other term) being viewed.
 *
 * @return array<int, array{url: string, label: string, current: bool}> Empty when the menu has no items.
 */
function lampandpath_article_filter_items() {
	$menu_items = lampandpath_menu_items( 'article-filters' );
	if ( ! $menu_items ) {
		return array();
	}

	$term  = get_queried_object();
	$items = array(
		array(
			'url'     => lampandpath_posts_page_url(),
			'label'   => __( 'All', 'lampandpath' ),
			'current' => is_home(),
		),
	);

	foreach ( $menu_items as $item ) {
		$items[] = array(
			'url'     => $item->url,
			'label'   => $item->title,
			'current' => $term instanceof WP_Term && 'taxonomy' === $item->type && $item->object === $term->taxonomy && (int) $item->object_id === $term->term_id,
		);
	}

	return $items;
}

/**
 * Returns articles to suggest after an article: the latest in its category, topped up with the latest articles.
 *
 * @param int|WP_Post $post  Article.
 * @param int         $limit Number of articles.
 * @return WP_Post[]
 */
function lampandpath_related_posts( $post, $limit = 3 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}

	$args     = array(
		'posts_per_page'      => $limit,
		'post__not_in'        => array( $post->ID ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$category = lampandpath_primary_category( $post );
	$related  = $category ? get_posts( $args + array( 'cat' => $category->term_id ) ) : array();

	if ( count( $related ) < $limit ) {
		$args['posts_per_page'] = $limit - count( $related );
		$args['post__not_in']   = array_merge( $args['post__not_in'], wp_list_pluck( $related, 'ID' ) );
		$related                = array_merge( $related, get_posts( $args ) );
	}

	return $related;
}

/**
 * Checks whether a post's featured image is taller than it is wide.
 *
 * Portrait images get the arched frame from the homepage hero; landscape
 * images get a rounded 3:2 frame.
 *
 * @param int|WP_Post|null $post Post; defaults to the current post.
 * @return bool
 */
function lampandpath_thumbnail_is_portrait( $post = null ) {
	$meta = wp_get_attachment_metadata( get_post_thumbnail_id( $post ) );

	return is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['height'] > $meta['width'];
}
