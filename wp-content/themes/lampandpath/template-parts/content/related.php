<?php
/**
 * "Keep reading": three articles to read next, from the same category first.
 *
 * Args: post (WP_Post) The article being read.
 *
 * @package Lampandpath
 */

$lampandpath_related = isset( $args['post'] ) ? lampandpath_related_posts( $args['post'], 3 ) : array();
if ( ! $lampandpath_related ) {
	return;
}
?>
<section aria-labelledby="related-heading" class="bg-surface-soft">
	<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
		<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
			<h2 id="related-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php esc_html_e( 'Keep reading', 'lampandpath' ); ?></h2>
			<a href="<?php echo esc_url( lampandpath_posts_page_url() ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php esc_html_e( 'View all articles', 'lampandpath' ); ?></a>
		</div>

		<div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			global $post;
			foreach ( $lampandpath_related as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored by wp_reset_postdata() below.
				setup_postdata( $post );
				get_template_part(
					'template-parts/content/card-article',
					null,
					array(
						'heading' => 'h3',
						'layout'  => 'stack',
						// Rendered inside the article's loop, but always below the article.
						'lazy'    => true,
					)
				);
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
