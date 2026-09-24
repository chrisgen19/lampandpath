<?php
/**
 * Search result for anything that is not an article: a page, reading plan or verse.
 *
 * Runs inside The Loop. Articles use card-article.php instead.
 *
 * @package Lampandpath
 */

$lampandpath_type = get_post_type_object( get_post_type() );
// A verse's excerpt would run its verse numbers into the words, so verses show their plain text.
$lampandpath_summary = 'lp_verse' === get_post_type() ? wp_trim_words( lampandpath_plain_verse( get_the_content() ), 40 ) : get_the_excerpt();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'py-7 wrap-anywhere' ); ?>>
	<?php if ( $lampandpath_type ) : ?>
		<p class="text-sm leading-5 font-semibold text-accent"><?php echo esc_html( $lampandpath_type->labels->singular_name ); ?></p>
	<?php endif; ?>

	<h2 class="mt-2 font-serif text-[26px] leading-[1.22] font-semibold">
		<a href="<?php the_permalink(); ?>" class="text-ink no-underline hover:text-accent"><?php the_title(); ?></a>
	</h2>

	<?php if ( $lampandpath_summary ) : ?>
		<p class="mt-2.5 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( $lampandpath_summary ); ?></p>
	<?php endif; ?>
</article>
