<?php
/**
 * Prayer request form.
 *
 * With JavaScript it posts to the lampandpath/v1/prayers REST route (see
 * assets/js/interactions.js); without it, it posts to admin-post.php and the
 * result comes back as ?lp_prayer=<code>. Until lampandpath-core registers the
 * handler (or while it is inactive) the fields are disabled.
 *
 * @package Lampandpath
 */

$lampandpath_ready = lampandpath_form_ready( 'lampandpath_prayer' );
$lampandpath_note  = $lampandpath_ready ? lampandpath_home_option( 'prayer', 'form_note' ) : __( 'Prayer requests are paused right now.', 'lampandpath' );
$lampandpath_field = 'mt-2 rounded-xl border-[1.5px] border-field bg-white px-4 text-[17px] text-ink disabled:bg-surface-soft';

// Display only: the result code comes from our own redirect after a no-JavaScript submission.
$lampandpath_result  = isset( $_GET['lp_prayer'] ) ? sanitize_key( wp_unslash( $_GET['lp_prayer'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$lampandpath_message = ( $lampandpath_result && function_exists( 'lampandpath_core_form_message' ) ) ? lampandpath_core_form_message( $lampandpath_result ) : '';
?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-lp-prayer-form class="flex flex-col rounded-[20px] border border-line bg-white p-6 sm:p-8">
	<input type="hidden" name="action" value="lampandpath_prayer">
	<input type="hidden" name="lp_started" value="<?php echo esc_attr( time() ); ?>">
	<?php wp_nonce_field( 'lampandpath_prayer', 'lampandpath_prayer_nonce' ); ?>

	<?php get_template_part( 'template-parts/components/form-status', null, array( 'message' => $lampandpath_message, 'success' => 'prayer_sent' === $lampandpath_result ) ); ?>

	<?php // display: contents keeps the fieldset out of the layout; disabling it disables every field and the button. ?>
	<fieldset class="contents"<?php disabled( ! $lampandpath_ready ); ?>>
		<label for="prayer-name" class="text-base leading-6 font-semibold text-ink">
			<?php esc_html_e( 'First name', 'lampandpath' ); ?>
			<span class="font-normal text-muted"><?php esc_html_e( '(optional)', 'lampandpath' ); ?></span>
		</label>
		<input id="prayer-name" name="first_name" type="text" autocomplete="given-name" maxlength="60" class="<?php echo esc_attr( $lampandpath_field ); ?> h-13">

		<label for="prayer-request" class="mt-5 text-base leading-6 font-semibold text-ink"><?php esc_html_e( 'How can we pray for you?', 'lampandpath' ); ?></label>
		<textarea id="prayer-request" name="request" rows="5" required minlength="3" maxlength="1000" placeholder="<?php esc_attr_e( 'Healing for a friend, wisdom for a decision, strength for this week…', 'lampandpath' ); ?>" class="<?php echo esc_attr( $lampandpath_field ); ?> h-34 resize-y py-3.5 leading-[26px]"></textarea>

		<label class="mt-5 flex items-start gap-3 text-base leading-6 text-ink-soft">
			<input type="checkbox" name="wall_consent" value="1" class="mt-0.5 size-5 shrink-0 accent-accent">
			<span><?php esc_html_e( 'Post my request on the prayer wall with my first name', 'lampandpath' ); ?></span>
		</label>

		<?php get_template_part( 'template-parts/components/form-honeypot', null, array( 'id' => 'prayer-website' ) ); ?>

		<button type="submit" class="mt-6 h-13 rounded-full bg-accent text-[17px] leading-none font-semibold text-white hover:bg-accent-strong disabled:cursor-not-allowed disabled:opacity-60"><?php esc_html_e( 'Send prayer request', 'lampandpath' ); ?></button>
	</fieldset>

	<?php if ( $lampandpath_note ) : ?>
		<p class="mt-4 text-center text-sm leading-5 text-muted"><?php echo esc_html( $lampandpath_note ); ?></p>
	<?php endif; ?>
</form>
