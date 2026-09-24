<?php
/**
 * "Written by" box under an article: avatar, name, role, bio and a link to the writer's articles.
 *
 * Args: author (int) User ID.
 *
 * @package Lampandpath
 */

$lampandpath_author = isset( $args['author'] ) ? get_userdata( (int) $args['author'] ) : false;
if ( ! $lampandpath_author ) {
	return;
}

$lampandpath_role = (string) get_user_meta( $lampandpath_author->ID, 'lp_role_title', true );
?>
<section aria-labelledby="author-heading" class="mt-12 flex flex-col gap-5 rounded-[20px] bg-surface-soft p-7 wrap-anywhere sm:flex-row sm:p-8">
	<?php lampandpath_avatar( $lampandpath_author->ID, 'lg' ); ?>
	<div class="min-w-0">
		<p class="text-sm leading-5 font-semibold text-muted"><?php esc_html_e( 'Written by', 'lampandpath' ); ?></p>
		<h2 id="author-heading" class="mt-1 font-serif text-[26px] leading-[31px] font-semibold text-ink"><?php echo esc_html( $lampandpath_author->display_name ); ?></h2>
		<?php if ( $lampandpath_role ) : ?>
			<p class="mt-1 text-[15px] leading-[22px] text-muted"><?php echo esc_html( $lampandpath_role ); ?></p>
		<?php endif; ?>
		<?php if ( $lampandpath_author->description ) : ?>
			<p class="mt-3 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( $lampandpath_author->description ); ?></p>
		<?php endif; ?>
		<p class="mt-4">
			<a href="<?php echo esc_url( get_author_posts_url( $lampandpath_author->ID ) ); ?>" class="<?php echo esc_attr( lampandpath_text_link_class() ); ?>">
				<?php
				/* translators: %s: writer's first name, or display name when the first name is empty. */
				echo esc_html( sprintf( __( 'More from %s', 'lampandpath' ), $lampandpath_author->first_name ? $lampandpath_author->first_name : $lampandpath_author->display_name ) );
				?>
			</a>
		</p>
	</div>
</section>
