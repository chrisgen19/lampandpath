<?php
/**
 * Article card: thumbnail, category, title, excerpt and byline.
 *
 * Runs inside The Loop. Used by the homepage latest articles list, and later
 * by archives and search.
 *
 * Args: heading (string) Heading tag for the title, "h2" by default.
 *
 * @package Lampandpath
 */

$lampandpath_heading  = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3' ), true ) ) ? $args['heading'] : 'h2';
$lampandpath_category = lampandpath_primary_category();
$lampandpath_author   = (int) get_the_author_meta( 'ID' );
$lampandpath_minutes  = lampandpath_reading_time_label();
$lampandpath_link     = get_permalink();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'flex flex-col gap-5 py-7 sm:flex-row sm:gap-7' ); ?>>
	<?php // The image repeats the title link, so it is skipped by keyboard and screen readers. ?>
	<?php // self-start stops the row from stretching the image to the text height, which would override the 3:2 ratio. ?>
	<a href="<?php echo esc_url( $lampandpath_link ); ?>" tabindex="-1" aria-hidden="true" class="flex aspect-[3/2] w-full shrink-0 items-center justify-center self-start overflow-hidden rounded-[14px] bg-surface-soft text-accent sm:w-60">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail(
				'lampandpath-card',
				array(
					'class' => 'h-full w-full object-cover',
					'alt'   => '',
					'sizes' => '(min-width: 640px) 240px, 100vw',
				)
			);
		} else {
			lampandpath_icon( 'lamp-mark', array( 'size' => 40 ) );
		}
		?>
	</a>

	<div class="flex min-w-0 flex-1 flex-col items-start">
		<?php if ( $lampandpath_category ) : ?>
			<a href="<?php echo esc_url( get_category_link( $lampandpath_category ) ); ?>" class="text-sm leading-5 font-semibold text-accent no-underline hover:underline"><?php echo esc_html( $lampandpath_category->name ); ?></a>
		<?php endif; ?>

		<<?php echo esc_html( $lampandpath_heading ); ?> class="mt-2 font-serif text-[26px] leading-[1.22] font-semibold">
			<a href="<?php echo esc_url( $lampandpath_link ); ?>" class="text-ink no-underline hover:text-accent"><?php the_title(); ?></a>
		</<?php echo esc_html( $lampandpath_heading ); ?>>

		<p class="mt-2.5 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( get_the_excerpt() ); ?></p>

		<div class="mt-4 flex flex-wrap items-center gap-x-3.5 gap-y-1 text-sm leading-5 text-muted">
			<?php lampandpath_avatar( $lampandpath_author, 'sm' ); ?>
			<span class="font-semibold text-ink-soft"><?php the_author(); ?></span>
			<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
			<?php if ( $lampandpath_minutes ) : ?>
				<span><?php echo esc_html( $lampandpath_minutes ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
