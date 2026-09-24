<?php
/**
 * Homepage: featured article (the latest sticky post, or the latest post).
 *
 * @package Lampandpath
 */

$lampandpath_post = lampandpath_home_hero_post();
if ( ! lampandpath_home_show( 'hero' ) || ! $lampandpath_post ) {
	return;
}

$lampandpath_category = lampandpath_primary_category( $lampandpath_post );
$lampandpath_author   = (int) $lampandpath_post->post_author;
$lampandpath_minutes  = lampandpath_reading_time_label( $lampandpath_post );
?>
<section aria-labelledby="featured-title" class="bg-white">
	<div class="mx-auto flex max-w-site flex-col gap-12 px-4 pt-10 pb-16 sm:px-6 lg:flex-row lg:items-center lg:gap-[72px] lg:px-10 lg:pt-16 lg:pb-[88px]">
		<div class="flex min-w-0 flex-1 flex-col items-start">
			<?php if ( $lampandpath_category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $lampandpath_category ) ); ?>" class="text-[15px] leading-[22px] font-semibold text-accent no-underline hover:underline"><?php echo esc_html( $lampandpath_category->name ); ?></a>
			<?php endif; ?>

			<h1 id="featured-title" class="mt-5 max-w-[690px] font-serif text-[40px] leading-[1.06] font-semibold tracking-[-0.015em] text-ink sm:text-[52px] lg:text-[64px]"><?php echo esc_html( get_the_title( $lampandpath_post ) ); ?></h1>

			<p class="mt-6 max-w-[600px] text-lg leading-[30px] text-ink-soft lg:text-[20px] lg:leading-8"><?php echo esc_html( get_the_excerpt( $lampandpath_post ) ); ?></p>

			<div class="mt-8 flex items-center gap-3.5">
				<?php lampandpath_avatar( $lampandpath_author, 'md' ); ?>
				<div class="flex flex-col gap-0.5">
					<span class="text-base leading-[22px] font-semibold text-ink"><?php echo esc_html( get_the_author_meta( 'display_name', $lampandpath_author ) ); ?></span>
					<span class="flex flex-wrap items-center gap-x-4 text-[15px] leading-[22px] text-muted">
						<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $lampandpath_post ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y', $lampandpath_post ) ); ?></time>
						<?php if ( $lampandpath_minutes ) : ?>
							<span class="flex items-center gap-1.5"><?php lampandpath_icon( 'clock', array( 'size' => 16 ) ); ?><?php echo esc_html( $lampandpath_minutes ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			</div>

			<div class="mt-9 flex flex-wrap gap-3">
				<a href="<?php echo esc_url( get_permalink( $lampandpath_post ) ); ?>" class="<?php echo esc_attr( lampandpath_button_class( 'primary', 'lg' ) ); ?>"><?php echo esc_html( lampandpath_home_option( 'hero', 'read_label' ) ); ?></a>
				<button type="button" data-lp-save="<?php echo esc_attr( $lampandpath_post->ID ); ?>" aria-pressed="false" class="<?php echo esc_attr( lampandpath_button_class( 'outline', 'lg', true ) ); ?>">
					<?php lampandpath_icon( 'bookmark', array( 'size' => 18 ) ); ?>
					<?php echo esc_html( lampandpath_home_option( 'hero', 'save_label' ) ); ?>
				</button>
			</div>
		</div>

		<?php // Arched frame: corner radii are percentages tied to the aspect ratio, so the arch keeps its shape at any width. ?>
		<div aria-hidden="true" class="mx-auto w-full max-w-[400px] shrink-0 lg:mx-0 lg:w-[484px] lg:max-w-none">
			<div class="aspect-[484/604] rounded-t-[50%_40.07%] rounded-b-xl bg-canvas p-3">
				<div class="flex h-full w-full items-center justify-center overflow-hidden rounded-t-[50%_39.66%] rounded-b bg-accent-tint text-accent">
					<?php
					if ( has_post_thumbnail( $lampandpath_post ) ) {
						echo get_the_post_thumbnail(
							$lampandpath_post,
							'lampandpath-hero',
							array(
								'class'         => 'h-full w-full object-cover',
								'alt'           => '',
								'sizes'         => '(min-width: 1024px) 460px, 400px',
								'loading'       => 'eager',
								'fetchpriority' => 'high',
							)
						);
					} else {
						lampandpath_icon( 'lamp-mark', array( 'size' => 96 ) );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>
