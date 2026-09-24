<?php
/**
 * Newsletter signup card, the target of the header Subscribe button (#newsletter).
 *
 * Posts to admin-post.php (action "lampandpath_subscribe"); the handler and the
 * in-place JavaScript submission arrive in Phase 4.
 *
 * Args: class (string) Extra classes for the card, e.g. its width in a layout.
 *
 * @package Lampandpath
 */

$lampandpath_class = isset( $args['class'] ) ? $args['class'] : '';
$lampandpath_note  = lampandpath_home_option( 'newsletter', 'note' );
?>
<div id="newsletter" class="rounded-3xl bg-accent-soft p-7 sm:p-10 <?php echo esc_attr( $lampandpath_class ); ?>">
	<span aria-hidden="true" class="flex size-11 items-center justify-center rounded-full bg-white text-accent">
		<?php lampandpath_icon( 'mail', array( 'size' => 22 ) ); ?>
	</span>
	<h2 id="newsletter-heading" class="mt-5 font-serif text-[28px] leading-[1.15] font-semibold text-ink sm:text-[32px]"><?php echo esc_html( lampandpath_home_option( 'newsletter', 'heading' ) ); ?></h2>
	<p class="mt-3 text-[17px] leading-[27px] text-ink-soft"><?php echo esc_html( lampandpath_home_option( 'newsletter', 'text' ) ); ?></p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-lp-newsletter-form class="mt-6">
		<input type="hidden" name="action" value="lampandpath_subscribe">
		<?php wp_nonce_field( 'lampandpath_subscribe', 'lampandpath_subscribe_nonce' ); ?>
		<label for="newsletter-email" class="block text-base leading-6 font-semibold text-ink"><?php esc_html_e( 'Email address', 'lampandpath' ); ?></label>
		<input id="newsletter-email" name="email" type="email" autocomplete="email" required placeholder="<?php esc_attr_e( 'you@example.com', 'lampandpath' ); ?>" class="mt-2 h-13 w-full rounded-xl border-[1.5px] border-field bg-white px-4 text-[17px] text-ink">
		<button type="submit" class="mt-3 h-13 w-full rounded-full bg-accent text-[17px] leading-none font-semibold text-white hover:bg-accent-strong"><?php esc_html_e( 'Subscribe', 'lampandpath' ); ?></button>
	</form>

	<?php if ( $lampandpath_note ) : ?>
		<p class="mt-3.5 text-sm leading-5 text-muted"><?php echo esc_html( $lampandpath_note ); ?></p>
	<?php endif; ?>
</div>
