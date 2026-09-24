<?php
/**
 * Search form, used by get_search_form() in the search dialog and templates.
 *
 * @package Lampandpath
 */

$lampandpath_field_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="flex gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $lampandpath_field_id ); ?>" class="sr-only"><?php esc_html_e( 'Search for:', 'lampandpath' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $lampandpath_field_id ); ?>" name="s" value="<?php echo get_search_query(); ?>" required
		placeholder="<?php esc_attr_e( 'Search articles, devotionals, plans…', 'lampandpath' ); ?>"
		class="h-13 min-w-0 grow rounded-xl border-[1.5px] border-field bg-white px-4 text-[17px] text-ink">
	<button type="submit" class="flex h-13 shrink-0 items-center gap-2 rounded-full bg-accent px-4 text-[17px] font-semibold text-white hover:bg-accent-strong sm:px-6">
		<?php lampandpath_icon( 'search', array( 'size' => 18 ) ); ?>
		<span class="sr-only sm:not-sr-only"><?php esc_html_e( 'Search', 'lampandpath' ); ?></span>
	</button>
</form>
