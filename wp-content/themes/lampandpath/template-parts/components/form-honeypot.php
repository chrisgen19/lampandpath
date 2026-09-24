<?php
/**
 * Honeypot field for the public forms.
 *
 * Hidden from people (and from assistive tech), so only bots fill it in;
 * lampandpath-core discards submissions where it has a value.
 *
 * Args: id (string) Unique element ID.
 *
 * @package Lampandpath
 */

$lampandpath_id = isset( $args['id'] ) ? $args['id'] : 'lp-website';
?>
<div class="hidden" aria-hidden="true">
	<label for="<?php echo esc_attr( $lampandpath_id ); ?>"><?php esc_html_e( 'Leave this field empty', 'lampandpath' ); ?></label>
	<input id="<?php echo esc_attr( $lampandpath_id ); ?>" name="website" type="text" tabindex="-1" autocomplete="off">
</div>
