<?php
/**
 * Status message area for the public forms.
 *
 * Shows the result of a no-JavaScript submission, and is where
 * assets/js/interactions.js writes progress, success and error messages.
 * It is hidden while empty.
 *
 * Args: message (string) Text to show now; success (bool) Whether it is a success message.
 *
 * @package Lampandpath
 */

$lampandpath_message = isset( $args['message'] ) ? (string) $args['message'] : '';
$lampandpath_state   = $lampandpath_message ? ( empty( $args['success'] ) ? 'error' : 'success' ) : '';
?>
<p data-lp-form-status tabindex="-1" aria-live="polite" data-state="<?php echo esc_attr( $lampandpath_state ); ?>" class="mb-5 rounded-xl px-4 py-3 text-base leading-6 empty:hidden data-[state=error]:bg-[#f1deda] data-[state=error]:text-[#7a3a2e] data-[state=info]:bg-surface-soft data-[state=info]:text-ink-soft data-[state=success]:bg-accent-soft data-[state=success]:text-accent"><?php echo esc_html( $lampandpath_message ); ?></p>
