<?php
/**
 * Search dialog opened by the header Search button (see assets/js/navigation.js).
 *
 * Uses the native <dialog> element, which handles focus trapping, the Escape
 * key and returning focus to the button that opened it.
 *
 * @package Lampandpath
 */

?>
<dialog id="search-dialog" aria-labelledby="search-dialog-title" class="m-auto mt-24 w-[calc(100%-2rem)] max-w-xl rounded-3xl bg-white p-0 text-ink shadow-2xl backdrop:bg-ink/50">
	<div class="p-6 sm:p-8">
		<div class="flex items-center justify-between gap-4">
			<h2 id="search-dialog-title" class="font-serif text-[26px] leading-[31px] font-semibold">
				<?php
				/* translators: %s: site title. */
				echo esc_html( sprintf( __( 'Search %s', 'lampandpath' ), get_bloginfo( 'name' ) ) );
				?>
			</h2>
			<button type="button" data-lp-search-close class="flex size-11 items-center justify-center rounded-full border border-line-strong bg-white text-ink hover:border-ink">
				<?php lampandpath_icon( 'close', array( 'size' => 20 ) ); ?>
				<span class="sr-only"><?php esc_html_e( 'Close search', 'lampandpath' ); ?></span>
			</button>
		</div>
		<div class="mt-5">
			<?php get_search_form(); ?>
		</div>
	</div>
</dialog>
