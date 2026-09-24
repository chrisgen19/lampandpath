<?php
/**
 * Verse of the day in a list: date, reference, translation and the verse.
 *
 * Runs inside The Loop. Today's verse (the one on the homepage) is labelled.
 *
 * Args: heading (string) Heading tag for the reference, "h2" by default.
 *
 * @package Lampandpath
 */

$lampandpath_heading     = ( isset( $args['heading'] ) && in_array( $args['heading'], array( 'h2', 'h3' ), true ) ) ? $args['heading'] : 'h2';
$lampandpath_translation = (string) get_post_meta( get_the_ID(), 'lp_translation', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'flex flex-col gap-4 py-8 wrap-anywhere lg:flex-row lg:gap-12' ); ?>>
	<div class="shrink-0 lg:w-60">
		<p class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[15px] leading-[22px] text-muted">
			<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'l, F j' ) ); ?></time>
			<?php if ( lampandpath_todays_verse_id() === get_the_ID() ) : ?>
				<span class="rounded-full bg-accent-soft px-2.5 py-0.5 text-sm font-semibold text-accent"><?php esc_html_e( 'Today', 'lampandpath' ); ?></span>
			<?php endif; ?>
		</p>
		<<?php echo esc_html( $lampandpath_heading ); ?> class="mt-1.5 font-serif text-2xl leading-[30px] font-semibold">
			<a href="<?php the_permalink(); ?>" class="text-ink no-underline hover:text-accent"><?php the_title(); ?></a>
		</<?php echo esc_html( $lampandpath_heading ); ?>>
		<?php if ( $lampandpath_translation ) : ?>
			<p class="mt-1 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_translation ); ?></p>
		<?php endif; ?>
	</div>

	<blockquote class="min-w-0 flex-1 font-serif text-[22px] leading-[1.45] text-ink sm:text-2xl [&_sup]:top-0 [&_sup]:mr-1 [&_sup]:align-[0.8em] [&_sup]:font-sans [&_sup]:text-[13px] [&_sup]:leading-[0] [&_sup]:font-bold [&_sup]:text-accent">
		<?php echo lampandpath_format_verse( get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Passed through wp_kses() in lampandpath_format_verse(). ?>
	</blockquote>
</article>
