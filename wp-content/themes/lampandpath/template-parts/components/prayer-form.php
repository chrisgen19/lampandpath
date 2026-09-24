<?php
/**
 * Prayer request form.
 *
 * Posts to admin-post.php (action "lampandpath_prayer"); the handler and the
 * in-place JavaScript submission arrive in Phase 4. Until the handler exists
 * (or while lampandpath-core is inactive) the fields are disabled.
 *
 * @package Lampandpath
 */

$lampandpath_ready = lampandpath_form_ready( 'lampandpath_prayer' );
$lampandpath_note  = $lampandpath_ready ? lampandpath_home_option( 'prayer', 'form_note' ) : __( 'Prayer requests are paused right now.', 'lampandpath' );
$lampandpath_field = 'mt-2 rounded-xl border-[1.5px] border-field bg-white px-4 text-[17px] text-ink disabled:bg-surface-soft';
?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-lp-prayer-form class="flex flex-col rounded-[20px] border border-line bg-white p-6 sm:p-8">
	<input type="hidden" name="action" value="lampandpath_prayer">
	<?php wp_nonce_field( 'lampandpath_prayer', 'lampandpath_prayer_nonce' ); ?>

	<?php // display: contents keeps the fieldset out of the layout; disabling it disables every field and the button. ?>
	<fieldset class="contents"<?php disabled( ! $lampandpath_ready ); ?>>
		<label for="prayer-name" class="text-base leading-6 font-semibold text-ink">
			<?php esc_html_e( 'First name', 'lampandpath' ); ?>
			<span class="font-normal text-muted"><?php esc_html_e( '(optional)', 'lampandpath' ); ?></span>
		</label>
		<input id="prayer-name" name="first_name" type="text" autocomplete="given-name" maxlength="60" class="<?php echo esc_attr( $lampandpath_field ); ?> h-13">

		<label for="prayer-request" class="mt-5 text-base leading-6 font-semibold text-ink"><?php esc_html_e( 'How can we pray for you?', 'lampandpath' ); ?></label>
		<textarea id="prayer-request" name="request" rows="5" required maxlength="1000" placeholder="<?php esc_attr_e( 'Healing for a friend, wisdom for a decision, strength for this week…', 'lampandpath' ); ?>" class="<?php echo esc_attr( $lampandpath_field ); ?> h-34 resize-y py-3.5 leading-[26px]"></textarea>

		<label class="mt-5 flex items-start gap-3 text-base leading-6 text-ink-soft">
			<input type="checkbox" name="wall_consent" value="1" class="mt-0.5 size-5 shrink-0 accent-accent">
			<span><?php esc_html_e( 'Post my request on the prayer wall with my first name', 'lampandpath' ); ?></span>
		</label>

		<button type="submit" class="mt-6 h-13 rounded-full bg-accent text-[17px] leading-none font-semibold text-white hover:bg-accent-strong disabled:cursor-not-allowed disabled:opacity-60"><?php esc_html_e( 'Send prayer request', 'lampandpath' ); ?></button>
	</fieldset>

	<?php if ( $lampandpath_note ) : ?>
		<p class="mt-4 text-center text-sm leading-5 text-muted"><?php echo esc_html( $lampandpath_note ); ?></p>
	<?php endif; ?>
</form>
