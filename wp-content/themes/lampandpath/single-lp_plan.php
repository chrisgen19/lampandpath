<?php
/**
 * Single reading plan: overview, the day bar, a tile per day's reading and other plans.
 *
 * Each reading links to the passage on Bible Gateway (lampandpath_get_bible_url()).
 *
 * @package Lampandpath
 */

get_header();
?>

<main id="primary" class="grow bg-white">
	<?php
	while ( have_posts() ) :
		the_post();

		$lampandpath_readings = lampandpath_get_plan_readings();
		$lampandpath_days     = count( $lampandpath_readings );
		$lampandpath_minutes  = (int) get_post_meta( get_the_ID(), 'lp_minutes', true );
		$lampandpath_plan_id  = get_the_ID();
		$lampandpath_others   = array_filter(
			lampandpath_get_featured_plans( 4 ),
			static function ( $plan ) use ( $lampandpath_plan_id ) {
				return $plan->ID !== $lampandpath_plan_id;
			}
		);
		$lampandpath_others   = array_slice( $lampandpath_others, 0, 3 );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="bg-accent-soft">
				<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
					<div class="max-w-[760px] wrap-anywhere">
						<p class="mb-3 text-[15px] leading-[22px] font-semibold text-accent"><?php echo esc_html( get_post_type_object( 'lp_plan' )->labels->singular_name ); ?></p>
						<?php the_title( '<h1 class="' . esc_attr( lampandpath_page_title_class() ) . '">', '</h1>' ); ?>
						<p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[15px] leading-[22px] text-muted">
							<span><?php /* translators: %d: number of days in a reading plan. */ echo esc_html( sprintf( _n( '%d day', '%d days', $lampandpath_days, 'lampandpath' ), $lampandpath_days ) ); ?></span>
							<?php if ( $lampandpath_minutes ) : ?>
								<span><?php /* translators: %d: minutes of reading per day. */ echo esc_html( sprintf( __( 'About %d min a day', 'lampandpath' ), $lampandpath_minutes ) ); ?></span>
							<?php endif; ?>
						</p>
						<?php if ( has_excerpt() ) : ?>
							<p class="mt-4 text-lg leading-[30px] text-ink-soft lg:text-[19px]"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( $lampandpath_days ) : ?>
						<div class="mt-8 max-w-[520px]">
							<?php lampandpath_plan_bar( $lampandpath_days, 'h-10' ); ?>
						</div>
						<a href="#day-1" class="mt-8 <?php echo esc_attr( lampandpath_button_class( 'primary', 'lg' ) ); ?>"><?php echo esc_html( lampandpath_home_option( 'plans', 'start_label' ) ); ?></a>
					<?php endif; ?>
				</div>
			</header>

			<div class="mx-auto max-w-site px-4 py-12 sm:px-6 lg:px-10 lg:py-16">
				<?php if ( '' !== trim( get_the_content() ) ) : ?>
					<div class="mb-14 max-w-[720px]">
						<div class="entry-content wrap-anywhere">
							<?php the_content(); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $lampandpath_readings ) : ?>
					<section aria-labelledby="readings-heading">
						<h2 id="readings-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php esc_html_e( 'Day by day', 'lampandpath' ); ?></h2>
						<p class="mt-4 max-w-[640px] text-lg leading-[30px] text-ink-soft"><?php esc_html_e( 'Read one passage a day. Each one opens on Bible Gateway.', 'lampandpath' ); ?></p>

						<ol class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
							<?php foreach ( $lampandpath_readings as $lampandpath_index => $lampandpath_reading ) : ?>
								<li id="<?php echo esc_attr( 'day-' . ( $lampandpath_index + 1 ) ); ?>">
									<a href="<?php echo esc_url( lampandpath_get_bible_url( $lampandpath_reading ) ); ?>" class="flex items-center gap-4 rounded-[14px] border border-line bg-white p-4 text-ink no-underline hover:border-accent">
										<span aria-hidden="true" class="flex size-11 shrink-0 items-center justify-center rounded-full bg-accent-tint font-serif text-lg font-bold text-accent"><?php echo esc_html( number_format_i18n( $lampandpath_index + 1 ) ); ?></span>
										<span class="flex min-w-0 flex-col wrap-anywhere">
											<span class="text-sm leading-5 text-muted">
												<?php
												/* translators: %s: day number in a reading plan. */
												echo esc_html( sprintf( __( 'Day %s', 'lampandpath' ), number_format_i18n( $lampandpath_index + 1 ) ) );
												?>
											</span>
											<span class="text-[17px] leading-6 font-semibold"><?php echo esc_html( $lampandpath_reading ); ?></span>
										</span>
										<?php lampandpath_icon( 'book-open', array( 'size' => 20, 'class' => 'ml-auto text-accent' ) ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ol>
					</section>
				<?php endif; ?>
			</div>
		</article>

		<?php if ( $lampandpath_others ) : ?>
			<section aria-labelledby="more-plans-heading" class="bg-surface-soft">
				<div class="mx-auto max-w-site px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
					<div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
						<h2 id="more-plans-heading" class="<?php echo esc_attr( lampandpath_section_heading_class() ); ?>"><?php esc_html_e( 'More reading plans', 'lampandpath' ); ?></h2>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'lp_plan' ) ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>"><?php echo esc_html( lampandpath_home_option( 'plans', 'all_label' ) ); ?></a>
					</div>
					<div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
						<?php
						foreach ( $lampandpath_others as $lampandpath_other ) {
							get_template_part(
								'template-parts/content/card-plan',
								null,
								array(
									'post'    => $lampandpath_other,
									'heading' => 'h3',
								)
							);
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>

<?php
get_footer();
