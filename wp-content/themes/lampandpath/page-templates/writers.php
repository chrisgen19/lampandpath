<?php
/**
 * Template Name: Our writers
 *
 * The page content, then a card for every writer with published articles:
 * the About section team first (in their About order), then everyone else.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_writers = function_exists( 'lampandpath_get_writers' )
	? lampandpath_get_writers()
	: get_users(
		array(
			'has_published_posts' => array( 'post' ),
			'orderby'             => 'display_name',
		)
	);
// Published article counts for every writer in one query.
$lampandpath_counts = $lampandpath_writers ? count_many_users_posts( wp_list_pluck( $lampandpath_writers, 'ID' ), 'post', true ) : array();
?>

<main id="primary" class="grow bg-white">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php get_template_part( 'template-parts/components/page-intro', null, array( 'title' => get_the_title() ) ); ?>

			<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="mb-12 max-w-[720px]">
						<div class="entry-content wrap-anywhere">
							<?php the_content(); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $lampandpath_writers ) : ?>
					<ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $lampandpath_writers as $lampandpath_writer ) : ?>
							<?php
							$lampandpath_role  = (string) get_user_meta( $lampandpath_writer->ID, 'lp_role_title', true );
							$lampandpath_count = isset( $lampandpath_counts[ $lampandpath_writer->ID ] ) ? (int) $lampandpath_counts[ $lampandpath_writer->ID ] : 0;
							?>
							<li class="flex flex-col rounded-[20px] border border-line bg-white p-7 wrap-anywhere">
								<div class="flex items-center gap-4">
									<?php lampandpath_avatar( $lampandpath_writer->ID, 'lg' ); ?>
									<div class="min-w-0">
										<h2 class="font-serif text-[26px] leading-[31px] font-semibold">
											<a href="<?php echo esc_url( get_author_posts_url( $lampandpath_writer->ID ) ); ?>" class="text-ink no-underline hover:text-accent"><?php echo esc_html( $lampandpath_writer->display_name ); ?></a>
										</h2>
										<?php if ( $lampandpath_role ) : ?>
											<p class="mt-0.5 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_role ); ?></p>
										<?php endif; ?>
									</div>
								</div>
								<?php if ( $lampandpath_writer->description ) : ?>
									<p class="mt-5 text-[17px] leading-[26px] text-ink-soft"><?php echo esc_html( $lampandpath_writer->description ); ?></p>
								<?php endif; ?>
								<p class="mt-auto pt-5 text-[15px] leading-[22px] font-semibold text-accent">
									<?php
									/* translators: %s: number of articles. */
									echo esc_html( sprintf( _n( '%s article', '%s articles', $lampandpath_count, 'lampandpath' ), number_format_i18n( $lampandpath_count ) ) );
									?>
								</p>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="rounded-[18px] border border-dashed border-line-strong p-6 text-[17px] leading-[27px] text-ink-soft sm:p-8"><?php esc_html_e( 'Writers appear here once they publish their first article.', 'lampandpath' ); ?></p>
				<?php endif; ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
