<?php
/**
 * Sticky site header: brand, primary navigation, search and subscribe.
 *
 * Below the xl breakpoint (1280px) the primary menu becomes a dropdown panel
 * toggled by the menu button (see assets/js/navigation.js). The full menu plus
 * Search and Subscribe needs about 1100px, so it does not fit at lg (1024px).
 *
 * @package Lampandpath
 */

$lampandpath_has_menu        = lampandpath_has_menu_items( 'primary' );
$lampandpath_subscribe_url   = lampandpath_subscribe_url();
$lampandpath_subscribe_label = lampandpath_get_option( 'lampandpath_subscribe_label' );
?>
<header class="sticky top-[var(--wp-admin--admin-bar--height,0px)] z-40 border-b border-line bg-white max-[600px]:top-0">
	<div class="relative mx-auto flex h-[72px] max-w-site items-center gap-4 px-4 sm:px-6 lg:h-[84px] lg:gap-10 lg:px-10">
		<?php lampandpath_site_brand( 'flex' ); ?>

		<?php if ( $lampandpath_has_menu ) : ?>
			<nav id="site-navigation" aria-label="<?php esc_attr_e( 'Main', 'lampandpath' ); ?>" class="absolute inset-x-0 top-full hidden border-b border-line bg-white px-4 pt-2 pb-4 shadow-lg sm:px-6 lg:px-10 [&.is-open]:block xl:static xl:block xl:grow xl:border-0 xl:p-0 xl:shadow-none">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'flex flex-col gap-1 xl:flex-row xl:items-center',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
				<a href="<?php echo esc_url( $lampandpath_subscribe_url ); ?>" class="mt-3 flex h-11 items-center justify-center rounded-full bg-accent px-[22px] text-base font-semibold text-white hover:bg-accent-strong sm:hidden"><?php echo esc_html( $lampandpath_subscribe_label ); ?></a>
			</nav>
		<?php endif; ?>

		<div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3 xl:ml-0">
			<button type="button" data-lp-search-open aria-haspopup="dialog" aria-controls="search-dialog" class="flex h-11 items-center gap-2 rounded-full border border-line-strong bg-white px-3 text-base leading-none font-medium text-ink hover:border-ink sm:pr-[18px] sm:pl-3.5">
				<?php lampandpath_icon( 'search', array( 'size' => 18 ) ); ?>
				<span class="sr-only sm:not-sr-only"><?php esc_html_e( 'Search', 'lampandpath' ); ?></span>
			</button>

			<a href="<?php echo esc_url( $lampandpath_subscribe_url ); ?>" class="hidden h-11 items-center rounded-full bg-accent px-[22px] text-base font-semibold text-white hover:bg-accent-strong sm:flex"><?php echo esc_html( $lampandpath_subscribe_label ); ?></a>

			<?php if ( $lampandpath_has_menu ) : ?>
				<button type="button" data-lp-nav-toggle aria-controls="site-navigation" aria-expanded="false" class="group flex size-11 items-center justify-center rounded-full border border-line-strong bg-white text-ink hover:border-ink xl:hidden">
					<?php lampandpath_icon( 'menu', array( 'size' => 20, 'class' => 'group-aria-expanded:hidden' ) ); ?>
					<?php lampandpath_icon( 'close', array( 'size' => 20, 'class' => 'hidden group-aria-expanded:block' ) ); ?>
					<span class="sr-only"><?php esc_html_e( 'Menu', 'lampandpath' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</header>
