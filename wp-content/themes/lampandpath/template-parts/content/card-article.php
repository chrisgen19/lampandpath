<?php
/**
 * Article card: thumbnail, category, title, excerpt and byline.
 *
 * Runs inside The Loop. Used by the homepage latest articles list, archives,
 * search results and the related articles under an article.
 *
 * Args:
 * - heading (string) Heading tag for the title, "h2" by default.
 * - layout  (string) "row" (default): image beside the text, for lists.
 *                    "stack": a boxed card with the image on top and no excerpt, for grids.
 *
 * @package Lampandpath
 */

$lampandpath_heading  = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3' ), true ) ) ? $args['heading'] : 'h2';
$lampandpath_stacked  = isset( $args['layout'] ) && 'stack' === $args['layout'];
$lampandpath_category = lampandpath_primary_category();
$lampandpath_author   = (int) get_the_author_meta( 'ID' );
$lampandpath_minutes  = lampandpath_reading_time_label();
$lampandpath_link     = get_permalink();

if ( $lampandpath_stacked ) {
	$lampandpath_class = array(
		'article' => 'flex flex-col rounded-[20px] border border-line bg-white p-5',
		'image'   => 'flex aspect-[3/2] w-full items-center justify-center overflow-hidden rounded-[14px] bg-surface-soft text-accent',
		'sizes'   => '(min-width: 1024px) 360px, (min-width: 640px) 50vw, 100vw',
		'body'    => 'mt-5 flex min-w-0 flex-1 flex-col items-start wrap-anywhere',
		'title'   => 'mt-2 font-serif text-2xl leading-[1.25] font-semibold',
		'byline'  => 'mt-auto flex flex-wrap items-center gap-x-3.5 gap-y-1 pt-4 text-sm leading-5 text-muted',
	);
} else {
	$lampandpath_class = array(
		'article' => 'flex flex-col gap-5 py-7 sm:flex-row sm:gap-7',
		// self-start stops the row from stretching the image to the text height, which would override the 3:2 ratio.
		'image'   => 'flex aspect-[3/2] w-full shrink-0 items-center justify-center self-start overflow-hidden rounded-[14px] bg-surface-soft text-accent sm:w-60',
		'sizes'   => '(min-width: 640px) 240px, 100vw',
		'body'    => 'flex min-w-0 flex-1 flex-col items-start wrap-anywhere',
		'title'   => 'mt-2 font-serif text-[26px] leading-[1.22] font-semibold',
		'byline'  => 'mt-4 flex flex-wrap items-center gap-x-3.5 gap-y-1 text-sm leading-5 text-muted',
	);
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $lampandpath_class['article'] ); ?>>
	<?php // The image repeats the title link, so it is skipped by keyboard and screen readers. ?>
	<a href="<?php echo esc_url( $lampandpath_link ); ?>" tabindex="-1" aria-hidden="true" class="<?php echo esc_attr( $lampandpath_class['image'] ); ?>">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail(
				'lampandpath-card',
				array(
					'class' => 'h-full w-full object-cover',
					'alt'   => '',
					'sizes' => $lampandpath_class['sizes'],
				)
			);
		} else {
			lampandpath_icon( 'lamp-mark', array( 'size' => 40 ) );
		}
		?>
	</a>

	<div class="<?php echo esc_attr( $lampandpath_class['body'] ); ?>">
		<?php if ( $lampandpath_category ) : ?>
			<a href="<?php echo esc_url( get_category_link( $lampandpath_category ) ); ?>" class="text-sm leading-5 font-semibold text-accent no-underline hover:underline"><?php echo esc_html( $lampandpath_category->name ); ?></a>
		<?php endif; ?>

		<<?php echo esc_html( $lampandpath_heading ); ?> class="<?php echo esc_attr( $lampandpath_class['title'] ); ?>">
			<a href="<?php echo esc_url( $lampandpath_link ); ?>" class="text-ink no-underline hover:text-accent"><?php the_title(); ?></a>
		</<?php echo esc_html( $lampandpath_heading ); ?>>

		<?php if ( ! $lampandpath_stacked ) : ?>
			<p class="mt-2.5 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>

		<div class="<?php echo esc_attr( $lampandpath_class['byline'] ); ?>">
			<?php lampandpath_avatar( $lampandpath_author, 'sm' ); ?>
			<span class="font-semibold text-ink-soft"><?php the_author(); ?></span>
			<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
			<?php if ( $lampandpath_minutes ) : ?>
				<span><?php echo esc_html( $lampandpath_minutes ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
