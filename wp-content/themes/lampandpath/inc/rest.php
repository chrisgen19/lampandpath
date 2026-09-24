<?php
/**
 * REST route for the article list (GET lampandpath/v1/articles).
 *
 * It returns rendered article cards, so it lives in the theme next to the card
 * template. The homepage topic filters and "Load more" use it; the data routes
 * (forms and counters) live in lampandpath-core.
 *
 * @package Lampandpath
 */

/**
 * Registers the article list route.
 */
function lampandpath_register_rest_routes() {
	register_rest_route(
		'lampandpath/v1',
		'/articles',
		array(
			'methods'             => 'GET',
			'callback'            => 'lampandpath_rest_articles',
			'permission_callback' => '__return_true',
			'args'                => array(
				'category' => array(
					'type'    => 'integer',
					'default' => 0,
					'minimum' => 0,
				),
				'page'     => array(
					'type'    => 'integer',
					'default' => 1,
					'minimum' => 1,
				),
				'per_page' => array(
					'type'    => 'integer',
					'default' => 5,
					'minimum' => 1,
					'maximum' => 12,
				),
				'exclude'  => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'integer' ),
					'default' => array(),
				),
				'heading'  => array(
					'type'    => 'string',
					'enum'    => array( 'h2', 'h3' ),
					'default' => 'h2',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'lampandpath_register_rest_routes' );

/**
 * Returns a page of rendered article cards.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function lampandpath_rest_articles( WP_REST_Request $request ) {
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'cat'                 => $request['category'],
			'paged'               => $request['page'],
			'posts_per_page'      => $request['per_page'],
			'post__not_in'        => array_map( 'absint', $request['exclude'] ),
			'ignore_sticky_posts' => true,
		)
	);

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/content/card-article', null, array( 'heading' => $request['heading'] ) );
	}
	wp_reset_postdata();

	return rest_ensure_response(
		array(
			'html'     => ob_get_clean(),
			'count'    => $query->post_count,
			'has_more' => $request['page'] < $query->max_num_pages,
		)
	);
}
