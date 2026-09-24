<?php
/**
 * Page not found: a search form, links back into the site and popular articles.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_popular = function_exists( 'lampandpath_get_most_read' )
	? lampandpath_get_most_read( 3 )
	: get_posts(
		array(
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
		)
	);
?>

<main id="primary" class="grow bg-white">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'eyebrow'     => __( 'Page not found', 'lampandpath' ),
			'title'       => __( 'We couldn’t find that page', 'lampandpath' ),
			'description' => '<p>' . esc_html__( 'It may have moved, or the link may be wrong. Try a search, or start from one of the pages below.', 'lampandpath' ) . '</p>',
			'icon'        => 'lamp-mark',
			'search'      => true,
			'links'       => array(
				array( home_url( '/' ), __( 'Go to the homepage', 'lampandpath' ) ),
				array( lampandpath_posts_page_url(), __( 'Browse articles', 'lampandpath' ) ),
			),
		)
	);
	?>

	<?php if ( $lampandpath_popular ) : ?>
		<section aria-labelledby="popular-heading" class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
			<h2 id="popular-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'latest', 'popular_heading' ) ); ?></h2>
			<div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
				<?php
				global $post;
				// A 404 has no post, which wp_reset_postdata() cannot restore, so the original is kept here.
				$lampandpath_original_post = $post;
				foreach ( $lampandpath_popular as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
					setup_postdata( $post );
					get_template_part(
						'template-parts/content/card-article',
						null,
						array(
							'heading' => 'h3',
							'layout'  => 'stack',
						)
					);
				}
				wp_reset_postdata();
				$post = $lampandpath_original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restores the value from before the loop.
				?>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php
get_footer();
