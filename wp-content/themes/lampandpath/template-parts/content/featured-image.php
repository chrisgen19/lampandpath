<?php
/**
 * The featured image beside the article title.
 *
 * Portrait images sit in the arched frame from the homepage featured article;
 * landscape images in a rounded 3:2 frame. Runs inside The Loop; prints
 * nothing when the article has no featured image.
 *
 * @package Lampandpath
 */

if ( ! has_post_thumbnail() ) {
	return;
}

$lampandpath_portrait = lampandpath_thumbnail_is_portrait();
$lampandpath_caption  = get_the_post_thumbnail_caption();
$lampandpath_image    = array(
	'class'         => 'h-full w-full object-cover',
	'sizes'         => $lampandpath_portrait ? '(min-width: 1280px) 360px, (min-width: 1024px) 296px, 400px' : '(min-width: 1280px) 360px, (min-width: 1024px) 296px, 100vw',
	'loading'       => 'eager',
	'fetchpriority' => 'high',
);
?>
<?php
/*
 * WordPress core prints :where(figure) { margin: 0 0 1em } outside any CSS layer, which
 * beats Tailwind's layered margin utilities on a figure. So the layout sits on this div
 * and the figure only cancels that margin, with an important utility.
 */
?>
<div class="w-full shrink-0 lg:w-80 xl:w-96 <?php echo esc_attr( $lampandpath_portrait ? 'mx-auto max-w-[400px] lg:mx-0 lg:max-w-none' : '' ); ?>">
<figure class="m-0!">
	<?php if ( $lampandpath_portrait ) : ?>
		<?php // Corner radii are percentages tied to the aspect ratio, so the arch keeps its shape at any width. ?>
		<div class="aspect-[484/604] rounded-t-[50%_40.07%] rounded-b-xl bg-canvas p-3">
			<div class="h-full w-full overflow-hidden rounded-t-[50%_39.66%] rounded-b bg-accent-tint">
				<?php the_post_thumbnail( 'lampandpath-hero', $lampandpath_image ); ?>
			</div>
		</div>
	<?php else : ?>
		<div class="rounded-3xl bg-canvas p-3">
			<div class="aspect-[3/2] overflow-hidden rounded-[14px] bg-accent-tint">
				<?php the_post_thumbnail( 'large', $lampandpath_image ); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $lampandpath_caption ) : ?>
		<figcaption class="mt-3 text-sm leading-5 text-muted"><?php echo wp_kses_post( $lampandpath_caption ); ?></figcaption>
	<?php endif; ?>
</figure>
</div>
