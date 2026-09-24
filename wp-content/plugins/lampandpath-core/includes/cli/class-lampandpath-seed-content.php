<?php
/**
 * Seeds the demo content (writers, articles, verses, plans, prayers, images).
 *
 * Data lives in seed-data.php. Existing items are matched by slug or login
 * and left untouched, so running the seed again never duplicates or overwrites.
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Creates the design's demo content. Used by Lampandpath_Seed_Command.
 */
class Lampandpath_Seed_Content {

	/**
	 * Demo data from seed-data.php.
	 *
	 * @var array<string, array>
	 */
	private $data;

	/**
	 * False once any item fails to seed.
	 *
	 * @var bool
	 */
	private $ok = true;

	/**
	 * User IDs keyed by login.
	 *
	 * @var array<string, int>
	 */
	private $writers = array();

	/**
	 * Attachment IDs keyed by image key.
	 *
	 * @var array<string, int>
	 */
	private $images = array();

	/**
	 * Collection term IDs keyed by slug.
	 *
	 * @var array<string, int>
	 */
	private $needs = array();

	/**
	 * Loads the demo data.
	 */
	public function __construct() {
		$this->data = require __DIR__ . '/seed-data.php';
	}

	/**
	 * Seeds all demo content.
	 *
	 * @param array<string, int> $categories Category IDs keyed by slug.
	 * @param array<string, int> $tags       Tag IDs keyed by slug.
	 * @return bool False when any item failed.
	 */
	public function run( array $categories, array $tags ) {
		$this->seed_writers();
		$this->seed_needs();
		$this->seed_images();
		$this->seed_posts( $categories, $tags );
		$this->seed_verses();
		$this->seed_plans();
		$this->seed_prayers();
		$this->remove_sample_content();

		return $this->ok;
	}

	/**
	 * Creates the writers with their profile fields. Passwords are random; reset them in wp-admin to log in.
	 */
	private function seed_writers() {
		$created = 0;
		foreach ( $this->data['writers'] as $login => $writer ) {
			$user = get_user_by( 'login', $login );
			if ( $user ) {
				$this->writers[ $login ] = (int) $user->ID;
				continue;
			}

			$name = $writer['first'] . ' ' . $writer['last'];
			$id   = wp_insert_user(
				array(
					'user_login'   => $login,
					'user_pass'    => wp_generate_password( 24 ),
					'user_email'   => $login . '@example.com',
					'first_name'   => $writer['first'],
					'last_name'    => $writer['last'],
					'display_name' => $name,
					'nickname'     => $name,
					'role'         => $writer['role'],
					'description'  => $writer['bio'],
				)
			);
			if ( is_wp_error( $id ) ) {
				$this->fail( sprintf( 'Writer "%s": %s', $login, $id->get_error_message() ) );
				continue;
			}

			update_user_meta( $id, 'lp_role_title', $writer['title'] );
			update_user_meta( $id, 'lp_avatar_color', $writer['color'] );
			if ( $writer['about'] ) {
				update_user_meta( $id, 'lp_show_on_about', true );
				update_user_meta( $id, 'lp_about_order', $writer['about'] );
			}
			$this->writers[ $login ] = (int) $id;
			++$created;
		}

		$this->report( 'Writers', count( $this->writers ), $created );
	}

	/**
	 * Creates the "Where are you today?" collections in design order.
	 */
	private function seed_needs() {
		$created = 0;
		$order   = 0;
		foreach ( $this->data['needs'] as $slug => $need ) {
			++$order;
			$term = get_term_by( 'slug', $slug, 'lp_need' );
			if ( $term ) {
				$this->needs[ $slug ] = (int) $term->term_id;
				continue;
			}

			$result = wp_insert_term(
				$need['name'],
				'lp_need',
				array(
					'slug'        => $slug,
					'description' => $need['text'],
				)
			);
			if ( is_wp_error( $result ) ) {
				$this->fail( sprintf( 'Collection "%s": %s', $slug, $result->get_error_message() ) );
				continue;
			}

			$id = (int) $result['term_id'];
			update_term_meta( $id, 'lp_icon', $need['icon'] );
			update_term_meta( $id, 'lp_verse_ref', $need['verse'] );
			update_term_meta( $id, 'lp_verse_url', lampandpath_get_bible_url( $need['verse'] ) );
			update_term_meta( $id, 'lp_order', $order );
			$this->needs[ $slug ] = $id;
			++$created;
		}

		$this->report( 'Collections', count( $this->needs ), $created );
	}

	/**
	 * Imports the design illustrations into the media library, once.
	 */
	private function seed_images() {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$created = 0;
		foreach ( $this->data['images'] as $key => $alt ) {
			$existing = get_posts(
				array(
					'post_type'   => 'attachment',
					'post_status' => 'inherit',
					'meta_key'    => '_lp_seed_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'fields'      => 'ids',
					'numberposts' => 1,
				)
			);
			if ( $existing ) {
				$this->images[ $key ] = (int) $existing[0];
				continue;
			}

			$id = $this->import_image( $key, $alt );
			if ( $id ) {
				$this->images[ $key ] = $id;
				++$created;
			}
		}

		$this->report( 'Images', count( $this->images ), $created );
	}

	/**
	 * Copies one illustration into uploads and creates its attachment and image sizes.
	 *
	 * @param string $key Image key (file name without .png).
	 * @param string $alt Alt text.
	 * @return int Attachment ID, or 0 on failure.
	 */
	private function import_image( $key, $alt ) {
		$source = LAMPANDPATH_CORE_DIR . 'seed/images/' . $key . '.png';
		$tmp    = wp_tempnam( $key . '.png' );
		if ( ! $tmp || ! copy( $source, $tmp ) ) {
			$this->fail( sprintf( 'Image "%s": could not copy %s', $key, $source ) );
			return 0;
		}

		// A "lampandpath-" file name keeps attachment slugs clear of the seeded page slugs.
		$id = media_handle_sideload(
			array(
				'name'     => 'lampandpath-' . $key . '.png',
				'tmp_name' => $tmp,
			),
			0,
			null,
			array( 'post_title' => $alt )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$this->fail( sprintf( 'Image "%s": %s', $key, $id->get_error_message() ) );
			return 0;
		}

		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		update_post_meta( $id, '_lp_seed_image', $key );

		return (int) $id;
	}

	/**
	 * Creates the design's articles with terms, featured images, reading times and views.
	 *
	 * @param array<string, int> $categories Category IDs keyed by slug.
	 * @param array<string, int> $tags       Tag IDs keyed by slug.
	 */
	private function seed_posts( array $categories, array $tags ) {
		$ready   = 0;
		$created = 0;
		foreach ( $this->data['posts'] as $post ) {
			if ( $this->find_post( $post['slug'], 'post' ) ) {
				++$ready;
				continue;
			}

			$id = $this->insert_post(
				array(
					'post_type'    => 'post',
					'post_status'  => 'publish',
					'post_name'    => $post['slug'],
					'post_title'   => $post['title'],
					'post_excerpt' => $post['excerpt'],
					'post_content' => $this->article_body(),
					'post_author'  => $this->writers[ $post['author'] ] ?? 0,
					'post_date'    => $this->local_date( '-' . $post['days_ago'] . ' days', '08:00' ),
				)
			);
			if ( ! $id ) {
				continue;
			}

			wp_set_post_categories( $id, $this->ids( array( $post['category'] ), $categories ) );
			wp_set_object_terms( $id, $this->ids( $post['tags'], $tags ), 'post_tag' );
			wp_set_object_terms( $id, $this->ids( $post['needs'], $this->needs ), 'lp_need' );
			update_post_meta( $id, 'lp_reading_minutes', $post['minutes'] );
			// Views are only set on creation, so re-running the seed never resets real counts.
			update_post_meta( $id, lampandpath_core_views_key(), $post['views'] );
			if ( ! empty( $post['image'] ) && isset( $this->images[ $post['image'] ] ) ) {
				set_post_thumbnail( $id, $this->images[ $post['image'] ] );
			}
			if ( ! empty( $post['sticky'] ) ) {
				stick_post( $id );
			}
			++$ready;
			++$created;
		}

		$this->report( 'Articles', $ready, $created );
	}

	/**
	 * Creates today's verse and schedules the following days' verses.
	 */
	private function seed_verses() {
		$ready   = 0;
		$created = 0;
		foreach ( $this->data['verses'] as $verse ) {
			if ( $this->find_post( $verse['slug'], 'lp_verse' ) ) {
				++$ready;
				continue;
			}

			// A future date makes WordPress schedule the verse instead of publishing it now.
			$id = $this->insert_post(
				array(
					'post_type'    => 'lp_verse',
					'post_status'  => 'publish',
					'post_name'    => $verse['slug'],
					'post_title'   => $verse['title'],
					'post_content' => '<!-- wp:paragraph --><p>' . wp_kses( $verse['text'], array( 'sup' => array() ) ) . '</p><!-- /wp:paragraph -->',
					'post_author'  => $this->writers['grace.villanueva'] ?? 0,
					'post_date'    => $this->local_date( '+' . $verse['day'] . ' days', '00:00' ),
				)
			);
			if ( ! $id ) {
				continue;
			}

			update_post_meta( $id, 'lp_translation', 'King James Version' );
			update_post_meta( $id, 'lp_reflection', $verse['reflection'] );
			update_post_meta( $id, 'lp_chapter_url', lampandpath_get_bible_url( $verse['chapter'] ) );
			++$ready;
			++$created;
		}

		$this->report( 'Verses', $ready, $created );
	}

	/**
	 * Creates the reading plans with one reading per day.
	 */
	private function seed_plans() {
		$ready   = 0;
		$created = 0;
		foreach ( $this->data['plans'] as $index => $plan ) {
			if ( $this->find_post( $plan['slug'], 'lp_plan' ) ) {
				++$ready;
				continue;
			}

			$id = $this->insert_post(
				array(
					'post_type'    => 'lp_plan',
					'post_status'  => 'publish',
					'post_name'    => $plan['slug'],
					'post_title'   => $plan['title'],
					'post_excerpt' => $plan['excerpt'],
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $plan['overview'] ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $index + 1,
				)
			);
			if ( ! $id ) {
				continue;
			}

			$readings = array();
			foreach ( $plan['chapters'] as $chapter ) {
				$readings[] = $plan['book'] . ' ' . $chapter;
			}
			update_post_meta( $id, 'lp_minutes', $plan['minutes'] );
			update_post_meta( $id, 'lp_readings', implode( "\n", $readings ) );
			++$ready;
			++$created;
		}

		$this->report( 'Reading plans', $ready, $created );
	}

	/**
	 * Creates the prayer requests: approved ones for the wall and one awaiting approval.
	 */
	private function seed_prayers() {
		$ready   = 0;
		$created = 0;
		foreach ( $this->data['prayers'] as $prayer ) {
			if ( $this->find_post( $prayer['slug'], 'lp_prayer' ) ) {
				++$ready;
				continue;
			}

			$date = new DateTimeImmutable( '-' . $prayer['hours_ago'] . ' hours', wp_timezone() );
			$id   = $this->insert_post(
				array(
					'post_type'    => 'lp_prayer',
					'post_status'  => $prayer['status'],
					'post_name'    => $prayer['slug'],
					/* translators: %s: first name of the person asking for prayer. */
					'post_title'   => '' !== $prayer['name'] ? sprintf( __( 'Request from %s', 'lampandpath-core' ), $prayer['name'] ) : __( 'Anonymous request', 'lampandpath-core' ),
					'post_content' => $prayer['text'],
					'post_date'    => $date->format( 'Y-m-d H:i:s' ),
				)
			);
			if ( ! $id ) {
				continue;
			}

			if ( '' !== $prayer['name'] ) {
				update_post_meta( $id, 'lp_first_name', $prayer['name'] );
			}
			update_post_meta( $id, 'lp_wall_consent', $prayer['consent'] );
			update_post_meta( $id, 'lp_prayed_count', $prayer['prayed'] );
			++$ready;
			++$created;
		}

		$this->report( 'Prayer requests', $ready, $created );
	}

	/**
	 * Moves WordPress's "Hello world!" post and "Sample Page" to the trash while they are untouched.
	 *
	 * Only the posts the installer created qualify: it always gives them IDs 1
	 * and 2, and IDs are never reused, so a post someone later creates with the
	 * same slug is never matched. WordPress gives a new post a modified date equal
	 * to its publish date and changes it on every edit, so a mismatch means the
	 * sample was reused. Trashing (not deleting) keeps any mistake recoverable.
	 */
	private function remove_sample_content() {
		$defaults = array(
			1 => array( 'hello-world', 'post' ),
			2 => array( 'sample-page', 'page' ),
		);

		foreach ( $defaults as $id => $sample ) {
			list( $slug, $post_type ) = $sample;
			$post                     = get_post( $id );
			if ( ! $post || $post->post_type !== $post_type || $post->post_name !== $slug || 'trash' === $post->post_status ) {
				continue;
			}

			if ( $post->post_modified_gmt !== $post->post_date_gmt ) {
				WP_CLI::log( sprintf( 'Kept the "%s" %s: it has been edited.', $slug, $post_type ) );
				continue;
			}
			if ( wp_trash_post( $post->ID ) ) {
				WP_CLI::log( sprintf( 'Moved the default "%s" %s to the trash.', $slug, $post_type ) );
			}
		}
	}

	/**
	 * Returns the body copy for a demo article: placeholder text with sample formatting.
	 *
	 * The excerpt is not repeated, because the article template shows it under the title.
	 *
	 * @return string Block markup.
	 */
	private function article_body() {
		$blocks = array(
			'<!-- wp:paragraph --><p>' . esc_html__( 'This article is placeholder copy for the Lamp & Path demo site. The final text will replace it.', 'lampandpath-core' ) . '</p><!-- /wp:paragraph -->',
			'<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'A place to begin', 'lampandpath-core' ) . '</h2><!-- /wp:heading -->',
			'<!-- wp:paragraph --><p>' . esc_html__( 'Articles can mix paragraphs, headings, quotes and lists. This sample shows how each one looks in the theme.', 'lampandpath-core' ) . '</p><!-- /wp:paragraph -->',
			'<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>Thy word is a lamp unto my feet, and a light unto my path.</p><!-- /wp:paragraph --><cite>Psalm 119:105</cite></blockquote><!-- /wp:quote -->',
			'<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>' . esc_html__( 'Read the passage slowly.', 'lampandpath-core' ) . '</li><!-- /wp:list-item --><!-- wp:list-item --><li>' . esc_html__( 'Notice one word or phrase that stands out.', 'lampandpath-core' ) . '</li><!-- /wp:list-item --><!-- wp:list-item --><li>' . esc_html__( 'Turn it into a short prayer.', 'lampandpath-core' ) . '</li><!-- /wp:list-item --></ul><!-- /wp:list -->',
		);

		return implode( "\n\n", $blocks );
	}

	/**
	 * Returns the ID of a seeded post in any status except trash, or 0.
	 *
	 * Matches the slug first, then the _lp_seed_slug meta: WordPress drops the
	 * slug of a pending post created without publish rights (as WP-CLI is), so
	 * the meta is the only reliable key for pending prayer requests.
	 *
	 * @param string $slug      Seed slug.
	 * @param string $post_type Post type.
	 * @return int
	 */
	private function find_post( $slug, $post_type ) {
		$query = array(
			'post_type'   => $post_type,
			'post_status' => 'any',
			'fields'      => 'ids',
			'numberposts' => 1,
		);

		$ids = get_posts( $query + array( 'name' => $slug ) );
		if ( ! $ids ) {
			$ids = get_posts(
				$query + array(
					'meta_key'   => '_lp_seed_slug', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value' => $slug, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			);
		}

		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Inserts a post, tagging it with its seed slug, and reports failures.
	 *
	 * @param array $args wp_insert_post() arguments, including post_name.
	 * @return int Post ID, or 0 on failure.
	 */
	private function insert_post( array $args ) {
		$args['meta_input'] = array( '_lp_seed_slug' => $args['post_name'] );

		$id = wp_insert_post( $args, true );
		if ( is_wp_error( $id ) ) {
			$this->fail( sprintf( '%s "%s": %s', $args['post_type'], $args['post_name'], $id->get_error_message() ) );
			return 0;
		}

		return (int) $id;
	}

	/**
	 * Returns a date relative to today in the site timezone, as a post_date string.
	 *
	 * @param string $offset Relative offset, e.g. "-3 days".
	 * @param string $time   Time of day, e.g. "08:00".
	 * @return string
	 */
	private function local_date( $offset, $time ) {
		$date = new DateTimeImmutable( 'today ' . $time, wp_timezone() );

		return $date->modify( $offset )->format( 'Y-m-d H:i:s' );
	}

	/**
	 * Maps slugs to IDs, dropping slugs that were not seeded.
	 *
	 * @param string[]           $slugs Slugs.
	 * @param array<string, int> $map   IDs keyed by slug.
	 * @return int[]
	 */
	private function ids( array $slugs, array $map ) {
		$ids = array();
		foreach ( $slugs as $slug ) {
			if ( ! empty( $map[ $slug ] ) ) {
				$ids[] = $map[ $slug ];
			}
		}

		return $ids;
	}

	/**
	 * Logs how many items of a kind are ready and how many were created.
	 *
	 * @param string $label   Item kind, e.g. "Writers".
	 * @param int    $ready   Items that now exist.
	 * @param int    $created Items created by this run.
	 */
	private function report( $label, $ready, $created ) {
		WP_CLI::log( sprintf( '%s: %d ready (%d created).', $label, $ready, $created ) );
	}

	/**
	 * Logs a failure and marks the run as failed.
	 *
	 * @param string $message What went wrong.
	 */
	private function fail( $message ) {
		WP_CLI::warning( $message );
		$this->ok = false;
	}
}
