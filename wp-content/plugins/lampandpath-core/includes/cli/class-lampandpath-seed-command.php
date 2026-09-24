<?php
/**
 * WP-CLI command that seeds the site with the structure from the homepage design.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Seeds settings, categories, pages and menus. Safe to run repeatedly.
 */
class Lampandpath_Seed_Command {

	/**
	 * Article categories from the design, keyed by slug.
	 */
	private const CATEGORIES = array(
		'devotionals'      => 'Devotionals',
		'bible-study'      => 'Bible study',
		'prayer'           => 'Prayer',
		'family'           => 'Family',
		'christian-living' => 'Christian living',
		'faith-and-doubt'  => 'Faith and doubt',
		'hope-and-healing' => 'Hope and healing',
		'faith-and-work'   => 'Faith and work',
	);

	/**
	 * Pages linked from the header and footer, keyed by slug.
	 */
	private const PAGES = array(
		'home'             => 'Home',
		'articles'         => 'Articles',
		'about'            => 'About',
		'what-we-believe'  => 'What we believe',
		'writers'          => 'Our writers',
		'support'          => 'Support our work',
		'contact'          => 'Contact us',
		'prayer-wall'      => 'Prayer wall',
		'share-your-story' => 'Share your story',
		'write-for-us'     => 'Write for us',
		'privacy-policy'   => 'Privacy policy',
		'terms'            => 'Terms of use',
		'accessibility'    => 'Accessibility',
	);

	/**
	 * Body copy for new pages until the final copy is written.
	 */
	private const PLACEHOLDER = '<!-- wp:paragraph --><p>This page is a placeholder. Replace it with the final copy.</p><!-- /wp:paragraph -->';

	/**
	 * Seeds site settings, categories, pages and menus from the homepage design.
	 *
	 * Existing categories and pages are reused, so the command is safe to run
	 * repeatedly. Menus only fill empty menu locations unless --reset-menus is
	 * passed, so menus assigned or edited in wp-admin are left alone.
	 *
	 * ## OPTIONS
	 *
	 * [--reset-menus]
	 * : Rebuild the seeded menus and reassign their locations, discarding menu edits made in wp-admin.
	 *
	 * ## EXAMPLES
	 *
	 *     wp lampandpath seed
	 *     wp lampandpath seed --reset-menus
	 *
	 * @param array $args       Positional arguments (unused).
	 * @param array $assoc_args Associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( 'lampandpath' !== get_stylesheet() ) {
			WP_CLI::error( 'Activate the theme first: wp theme activate lampandpath' );
		}

		$this->seed_settings();
		$categories = $this->seed_categories();
		$pages      = $this->seed_pages();
		$this->seed_reading_settings( $pages );
		$menus_ok = $this->seed_menus( $categories, $pages, (bool) WP_CLI\Utils\get_flag_value( $assoc_args, 'reset-menus', false ) );
		$this->seed_theme_mods();

		if ( ! $menus_ok ) {
			WP_CLI::error( 'Seed finished with errors; see the warnings above.' );
		}
		WP_CLI::success( 'Seed complete.' );
	}

	/**
	 * Sets the site title, tagline and permalink structure.
	 */
	private function seed_settings() {
		global $wp_rewrite;

		update_option( 'blogname', 'Lamp & Path' );
		update_option( 'blogdescription', 'Articles, devotionals and Bible reading plans for everyday faith.' );
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		flush_rewrite_rules( false );

		WP_CLI::log( 'Settings: site title, tagline and /%postname%/ permalinks.' );
	}

	/**
	 * Creates the article categories that do not exist yet.
	 *
	 * @return array<string, int> Term IDs keyed by slug.
	 */
	private function seed_categories() {
		$ids = array();
		foreach ( self::CATEGORIES as $slug => $name ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( $term ) {
				$ids[ $slug ] = (int) $term->term_id;
				continue;
			}

			$result = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
			if ( is_wp_error( $result ) ) {
				WP_CLI::warning( sprintf( 'Category "%s": %s', $name, $result->get_error_message() ) );
				continue;
			}
			$ids[ $slug ] = (int) $result['term_id'];
		}

		WP_CLI::log( sprintf( 'Categories: %d ready.', count( $ids ) ) );
		return $ids;
	}

	/**
	 * Creates missing pages and publishes existing drafts (e.g. the default Privacy Policy).
	 *
	 * Existing page titles and content are left untouched, and private,
	 * pending or scheduled pages keep their status.
	 *
	 * @return array<string, int> Page IDs keyed by slug.
	 */
	private function seed_pages() {
		$ids = array();
		foreach ( self::PAGES as $slug => $title ) {
			$id = $this->seed_page( $slug, $title );
			if ( $id ) {
				$ids[ $slug ] = $id;
			}
		}

		WP_CLI::log( sprintf( 'Pages: %d of %d ready.', count( $ids ), count( self::PAGES ) ) );
		return $ids;
	}

	/**
	 * Returns the ID of the page with this slug, creating it when missing.
	 *
	 * @param string $slug  Page slug.
	 * @param string $title Title for a new page.
	 * @return int Page ID, or 0 when the page could not be created.
	 */
	private function seed_page( $slug, $title ) {
		// An array stops WordPress from also matching an attachment with the same slug.
		$page = get_page_by_path( $slug, OBJECT, array( 'page' ) );
		if ( $page ) {
			if ( 'draft' === $page->post_status ) {
				wp_update_post(
					array(
						'ID'          => $page->ID,
						'post_status' => 'publish',
					)
				);
			} elseif ( 'publish' !== $page->post_status ) {
				WP_CLI::warning( sprintf( 'Page "%s" is %s; status left unchanged.', $slug, $page->post_status ) );
			}
			return (int) $page->ID;
		}

		// Pages and unattached media share slugs. WordPress would rename the new page (e.g. "about-2")
		// and every re-run would create another copy, so stop and ask for the conflict to be fixed.
		$attachment = get_page_by_path( $slug, OBJECT, array( 'attachment' ) );
		if ( $attachment ) {
			WP_CLI::warning( sprintf( 'Page "%s" not created: media item #%d already uses that slug. Rename its slug, then re-run.', $slug, $attachment->ID ) );
			return 0;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_author'  => $this->default_author(),
				'post_content' => in_array( $slug, array( 'home', 'articles' ), true ) ? '' : self::PLACEHOLDER,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( sprintf( 'Page "%s": %s', $title, $id->get_error_message() ) );
			return 0;
		}

		return (int) $id;
	}

	/**
	 * Uses a static front page, "Articles" as the posts page, and sets the privacy page.
	 *
	 * Settings are only changed when the pages they point to exist.
	 *
	 * @param array<string, int> $pages Page IDs keyed by slug.
	 */
	private function seed_reading_settings( array $pages ) {
		if ( empty( $pages['home'] ) || empty( $pages['articles'] ) ) {
			WP_CLI::warning( 'Reading settings unchanged: the "home" or "articles" page is missing.' );
		} else {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $pages['home'] );
			update_option( 'page_for_posts', $pages['articles'] );
			WP_CLI::log( 'Reading: static front page "Home", posts page "Articles".' );
		}

		if ( ! empty( $pages['privacy-policy'] ) ) {
			update_option( 'wp_page_for_privacy_policy', $pages['privacy-policy'] );
		}
	}

	/**
	 * Returns the menus to create: name => theme location and items.
	 *
	 * Items are [type, target, label]: type "page" and "category" target a slug,
	 * type "custom" targets a site-relative URL.
	 *
	 * @return array<string, array{location: string, items: array<int, array{0: string, 1: string, 2: string}>}>
	 */
	private function menu_definitions() {
		return array(
			'Main'      => array(
				'location' => 'primary',
				'items'    => array(
					array( 'page', 'articles', 'Articles' ),
					array( 'category', 'devotionals', 'Devotionals' ),
					array( 'category', 'bible-study', 'Bible study' ),
					array( 'category', 'prayer', 'Prayer' ),
					array( 'custom', '/reading-plans/', 'Reading plans' ),
					array( 'page', 'about', 'About' ),
				),
			),
			'Read'      => array(
				'location' => 'footer-1',
				'items'    => array(
					array( 'page', 'articles', 'Latest articles' ),
					array( 'category', 'devotionals', 'Devotionals' ),
					array( 'category', 'bible-study', 'Bible study' ),
					array( 'custom', '/reading-plans/', 'Reading plans' ),
					array( 'category', 'prayer', 'Prayers' ),
				),
			),
			'Community' => array(
				'location' => 'footer-2',
				'items'    => array(
					array( 'page', 'prayer-wall', 'Prayer wall' ),
					array( 'custom', '/#prayer', 'Send a prayer request' ),
					array( 'page', 'share-your-story', 'Share your story' ),
					array( 'page', 'write-for-us', 'Write for us' ),
					array( 'custom', '/#newsletter', 'Newsletter' ),
				),
			),
			'About'     => array(
				'location' => 'footer-3',
				'items'    => array(
					array( 'page', 'about', 'Our story' ),
					array( 'page', 'what-we-believe', 'What we believe' ),
					array( 'page', 'writers', 'Our writers' ),
					array( 'page', 'support', 'Support our work' ),
					array( 'page', 'contact', 'Contact us' ),
				),
			),
			'Legal'     => array(
				'location' => 'legal',
				'items'    => array(
					array( 'page', 'privacy-policy', 'Privacy policy' ),
					array( 'page', 'terms', 'Terms of use' ),
					array( 'page', 'accessibility', 'Accessibility' ),
				),
			),
		);
	}

	/**
	 * Creates the menus and assigns them to their theme locations.
	 *
	 * Without $reset only empty locations are filled, so a menu an editor
	 * assigned in wp-admin is never swapped back.
	 *
	 * @param array<string, int> $categories Category IDs keyed by slug.
	 * @param array<string, int> $pages      Page IDs keyed by slug.
	 * @param bool               $reset      Whether to rebuild menus and reassign their locations.
	 * @return bool False when a menu could not be built.
	 */
	private function seed_menus( array $categories, array $pages, $reset ) {
		$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
		$ok        = true;

		foreach ( $this->menu_definitions() as $name => $menu ) {
			$location = $menu['location'];
			if ( ! $reset && $this->location_has_menu( $locations, $location ) ) {
				WP_CLI::log( sprintf( 'Menu location "%s": already assigned, kept (use --reset-menus to rebuild).', $location ) );
				continue;
			}

			$menu_id = $this->seed_menu( $name, $menu['items'], $categories, $pages, $reset );
			if ( $menu_id ) {
				$locations[ $location ] = $menu_id;
			} else {
				$ok = false;
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
		return $ok;
	}

	/**
	 * Creates or rebuilds one menu. An existing menu is reused as-is unless $reset is set.
	 *
	 * @param string                                             $name       Menu name.
	 * @param array<int, array{0: string, 1: string, 2: string}> $items      Menu definition items.
	 * @param array<string, int>                                 $categories Category IDs keyed by slug.
	 * @param array<string, int>                                 $pages      Page IDs keyed by slug.
	 * @param bool                                               $reset      Whether to replace the items of an existing menu.
	 * @return int Menu ID, or 0 on failure.
	 */
	private function seed_menu( $name, array $items, array $categories, array $pages, $reset ) {
		$existing = wp_get_nav_menu_object( $name );
		if ( $existing && ! $reset ) {
			WP_CLI::log( sprintf( 'Menu "%s": exists, assigned without changing its items.', $name ) );
			return (int) $existing->term_id;
		}

		// Resolve every link target first, so a missing page never leaves a half-built menu.
		$item_data = $this->build_menu_items( $name, $items, $categories, $pages );
		if ( null === $item_data ) {
			return 0;
		}

		$menu_id = $existing ? (int) $existing->term_id : wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::warning( sprintf( 'Menu "%s": %s', $name, $menu_id->get_error_message() ) );
			return 0;
		}

		foreach ( (array) wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) ) as $old_item ) {
			wp_delete_post( $old_item->ID, true );
		}
		foreach ( $item_data as $data ) {
			$result = wp_update_nav_menu_item( $menu_id, 0, $data );
			if ( is_wp_error( $result ) ) {
				WP_CLI::warning( sprintf( 'Menu "%s": item "%s" failed: %s', $name, $data['menu-item-title'], $result->get_error_message() ) );
				return 0;
			}
		}

		WP_CLI::log( sprintf( 'Menu "%s": %d items.', $name, count( $item_data ) ) );
		return (int) $menu_id;
	}

	/**
	 * Converts menu definition items into wp_update_nav_menu_item() data.
	 *
	 * @param string                                             $name       Menu name, for warnings.
	 * @param array<int, array{0: string, 1: string, 2: string}> $items      Menu definition items.
	 * @param array<string, int>                                 $categories Category IDs keyed by slug.
	 * @param array<string, int>                                 $pages      Page IDs keyed by slug.
	 * @return array|null Item data, or null when a linked page or category does not exist.
	 */
	private function build_menu_items( $name, array $items, array $categories, array $pages ) {
		$data = array();
		foreach ( $items as $index => $item ) {
			$item_data = $this->menu_item_data( $item, $index + 1, $categories, $pages );
			if ( null === $item_data ) {
				WP_CLI::warning( sprintf( 'Menu "%s": %s "%s" not found, menu left unchanged.', $name, $item[0], $item[1] ) );
				return null;
			}
			$data[] = $item_data;
		}

		return $data;
	}

	/**
	 * Checks whether a theme location is assigned to a menu that still exists.
	 *
	 * @param array  $locations Theme location assignments (location => menu ID).
	 * @param string $location  Theme location.
	 * @return bool
	 */
	private function location_has_menu( array $locations, $location ) {
		return ! empty( $locations[ $location ] ) && (bool) wp_get_nav_menu_object( (int) $locations[ $location ] );
	}

	/**
	 * Converts a menu definition item into wp_update_nav_menu_item() data.
	 *
	 * @param array{0: string, 1: string, 2: string} $item       [type, target, label].
	 * @param int                                    $position   Menu order.
	 * @param array<string, int>                     $categories Category IDs keyed by slug.
	 * @param array<string, int>                     $pages      Page IDs keyed by slug.
	 * @return array|null Item data, or null when the linked page or category does not exist.
	 */
	private function menu_item_data( array $item, $position, array $categories, array $pages ) {
		list( $type, $target, $label ) = $item;

		$data = array(
			'menu-item-title'    => $label,
			'menu-item-status'   => 'publish',
			'menu-item-position' => $position,
		);

		if ( 'custom' === $type ) {
			return $data + array(
				'menu-item-type' => 'custom',
				'menu-item-url'  => $target,
			);
		}

		// WordPress accepts an item with object ID 0 but then silently drops it from the menu.
		$ids = 'page' === $type ? $pages : $categories;
		if ( empty( $ids[ $target ] ) ) {
			return null;
		}

		return $data + array(
			'menu-item-type'      => 'page' === $type ? 'post_type' : 'taxonomy',
			'menu-item-object'    => $type,
			'menu-item-object-id' => $ids[ $target ],
		);
	}

	/**
	 * Fills empty social link settings with "#" placeholders so the footer matches the design.
	 */
	private function seed_theme_mods() {
		foreach ( array( 'instagram', 'facebook', 'youtube', 'podcast' ) as $network ) {
			$key = 'lampandpath_social_' . $network;
			if ( ! get_theme_mod( $key ) ) {
				set_theme_mod( $key, '#' );
			}
		}

		WP_CLI::log( 'Customizer: social link placeholders set where empty.' );
	}

	/**
	 * Returns the ID of the first administrator, used as the author of seeded content.
	 *
	 * @return int
	 */
	private function default_author() {
		$admins = get_users(
			array(
				'role'   => 'administrator',
				'number' => 1,
				'fields' => 'ID',
			)
		);

		return $admins ? (int) $admins[0] : 0;
	}
}
