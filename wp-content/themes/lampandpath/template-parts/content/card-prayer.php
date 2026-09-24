<?php
/**
 * Prayer wall item: the request, first name (or "Shared anonymously"), when it was shared and "I prayed".
 *
 * Used by the homepage prayer section and the Prayer wall page template.
 * assets/js/interactions.js handles "I prayed" (data-lp-prayed).
 *
 * Args: post (WP_Post) An approved prayer request; defaults to the current post in The Loop.
 *
 * @package Lampandpath
 */

$lampandpath_request = get_post( isset( $args['post'] ) ? $args['post'] : null );
if ( ! $lampandpath_request ) {
	return;
}

$lampandpath_name = (string) get_post_meta( $lampandpath_request->ID, 'lp_first_name', true );
?>
<li id="<?php echo esc_attr( 'post-' . $lampandpath_request->ID ); ?>" class="rounded-[18px] border border-line bg-white p-6 wrap-anywhere">
	<p class="text-[17px] leading-[27px] text-ink"><?php echo esc_html( wp_strip_all_tags( $lampandpath_request->post_content ) ); ?></p>
	<div class="mt-4 flex flex-wrap items-center justify-between gap-4">
		<span class="flex flex-wrap gap-x-2.5 text-sm leading-5 text-muted">
			<span class="font-semibold text-ink-soft"><?php echo esc_html( '' !== $lampandpath_name ? $lampandpath_name : __( 'Shared anonymously', 'lampandpath' ) ); ?></span>
			<span><?php echo esc_html( lampandpath_relative_time( (int) get_post_time( 'U', true, $lampandpath_request ) ) ); ?></span>
		</span>
		<?php // Pressed shows "Prayed" with a check; the accessible name "I prayed" contains the visible text in both states. ?>
		<button type="button" data-lp-prayed="<?php echo esc_attr( $lampandpath_request->ID ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'I prayed', 'lampandpath' ); ?>" class="group <?php echo esc_attr( lampandpath_button_class( 'outline', 'sm', true ) ); ?> aria-pressed:border-accent aria-pressed:bg-accent-soft aria-pressed:text-accent">
			<?php lampandpath_icon( 'heart', array( 'size' => 18, 'class' => 'group-aria-pressed:hidden' ) ); ?>
			<?php lampandpath_icon( 'check', array( 'size' => 18, 'class' => 'hidden group-aria-pressed:block' ) ); ?>
			<span class="group-aria-pressed:hidden"><?php esc_html_e( 'I prayed', 'lampandpath' ); ?></span>
			<span class="hidden group-aria-pressed:inline"><?php esc_html_e( 'Prayed', 'lampandpath' ); ?></span>
		</button>
	</div>
</li>
