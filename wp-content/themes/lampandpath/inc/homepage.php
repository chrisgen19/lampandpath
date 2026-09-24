<?php
/**
 * Homepage settings: the Customizer "Homepage" panel and the functions the
 * homepage sections use to read their text, links and visibility.
 *
 * Every field is defined once in lampandpath_homepage_sections(); defaults are
 * the copy from docs/design/homepage.html. "{site}" is replaced with the site title.
 *
 * @package Lampandpath
 */

/**
 * Returns the homepage sections and their editable fields.
 *
 * Each field is [label, default, type]; type is text (default), textarea or
 * page (a page picker whose default is a page slug).
 *
 * @return array<string, array{title: string, description?: string, fields: array<string, array>}>
 */
function lampandpath_homepage_sections() {
	static $sections = null;
	if ( null !== $sections ) {
		return $sections;
	}

	$sections = array(
		'hero'       => array(
			'title'       => __( 'Featured article', 'lampandpath' ),
			'description' => __( 'Shows the latest sticky post (tick "Stick to the top of the blog" on a post), or the latest post if none is sticky.', 'lampandpath' ),
			'fields'      => array(
				'read_label' => array( __( 'Read button label', 'lampandpath' ), __( 'Read the article', 'lampandpath' ) ),
				'save_label' => array( __( 'Save button label', 'lampandpath' ), __( 'Save for later', 'lampandpath' ) ),
			),
		),
		'verse'      => array(
			'title'       => __( 'Verse of the day', 'lampandpath' ),
			'description' => __( 'Shows the latest verse from Verses of the day whose date has arrived.', 'lampandpath' ),
			'fields'      => array(
				'heading' => array( __( 'Heading', 'lampandpath' ), __( 'Verse of the day', 'lampandpath' ) ),
			),
		),
		'latest'     => array(
			'title'       => __( 'Latest articles', 'lampandpath' ),
			'description' => __( 'Filter chips come from the "Homepage: article filter chips" menu, topics from the "Homepage: browse by topic" menu.', 'lampandpath' ),
			'fields'      => array(
				'heading'         => array( __( 'Heading', 'lampandpath' ), __( 'Latest articles', 'lampandpath' ) ),
				'all_label'       => array( __( '"View all" link label', 'lampandpath' ), __( 'View all articles', 'lampandpath' ) ),
				'more_label'      => array( __( '"Load more" button label', 'lampandpath' ), __( 'Load more articles', 'lampandpath' ) ),
				'popular_heading' => array( __( 'Most read heading', 'lampandpath' ), __( 'Most read this month', 'lampandpath' ) ),
				'topics_heading'  => array( __( 'Topics heading', 'lampandpath' ), __( 'Browse by topic', 'lampandpath' ) ),
			),
		),
		'needs'      => array(
			'title'       => __( 'Where are you today?', 'lampandpath' ),
			'description' => __( 'Cards come from Posts > Collections.', 'lampandpath' ),
			'fields'      => array(
				'heading' => array( __( 'Heading', 'lampandpath' ), __( 'Where are you today?', 'lampandpath' ) ),
				'intro'   => array( __( 'Intro', 'lampandpath' ), __( 'Start with what you’re carrying. Each collection gathers scripture, prayers, and articles for that season of life.', 'lampandpath' ), 'textarea' ),
			),
		),
		'plans'      => array(
			'title'       => __( 'Reading plans', 'lampandpath' ),
			'description' => __( 'Shows the first 3 reading plans by their "Order".', 'lampandpath' ),
			'fields'      => array(
				'heading'     => array( __( 'Heading', 'lampandpath' ), __( 'Read the Bible with us', 'lampandpath' ) ),
				'intro'       => array( __( 'Intro', 'lampandpath' ), __( 'Free plans you can start on any day. Each morning we’ll send the passage and a short reflection to your inbox.', 'lampandpath' ), 'textarea' ),
				'all_label'   => array( __( '"See all" link label', 'lampandpath' ), __( 'See all reading plans', 'lampandpath' ) ),
				'start_label' => array( __( 'Plan button label', 'lampandpath' ), __( 'Start the plan', 'lampandpath' ) ),
			),
		),
		'prayer'     => array(
			'title'  => __( 'Prayer requests', 'lampandpath' ),
			'fields' => array(
				'heading'      => array( __( 'Heading', 'lampandpath' ), __( 'Can we pray for you?', 'lampandpath' ) ),
				'intro'        => array( __( 'Intro', 'lampandpath' ), __( 'Our prayer team reads every request and prays for you by name this week. Share as much or as little as you like.', 'lampandpath' ), 'textarea' ),
				'form_note'    => array( __( 'Note under the form', 'lampandpath' ), __( 'Nothing is posted without your permission.', 'lampandpath' ) ),
				'wall_heading' => array( __( 'Prayer wall heading', 'lampandpath' ), __( 'From the prayer wall', 'lampandpath' ) ),
				'wall_intro'   => array( __( 'Prayer wall intro', 'lampandpath' ), __( 'Shared with permission. Let someone know you prayed.', 'lampandpath' ) ),
				'wall_label'   => array( __( '"See all" link label', 'lampandpath' ), __( 'See all requests', 'lampandpath' ) ),
				'wall_page'    => array( __( '"See all" page', 'lampandpath' ), 'prayer-wall', 'page' ),
			),
		),
		'about'      => array(
			'title'       => __( 'About', 'lampandpath' ),
			'description' => __( 'Team members are the writers ticked "Show in the homepage About section" in Users.', 'lampandpath' ),
			'fields'      => array(
				'heading'       => array( __( 'Heading', 'lampandpath' ), __( 'About {site}', 'lampandpath' ) ),
				'text'          => array( __( 'Text', 'lampandpath' ), __( 'We’re a small team of writers, pastors, and everyday believers writing about ordinary life with Jesus. Every article aims to send you back to Scripture a little more hopeful than when you arrived.', 'lampandpath' ), 'textarea' ),
				'believe_label' => array( __( 'Button label', 'lampandpath' ), __( 'Read what we believe', 'lampandpath' ) ),
				'believe_page'  => array( __( 'Button page', 'lampandpath' ), 'what-we-believe', 'page' ),
				'writers_label' => array( __( 'First link label', 'lampandpath' ), __( 'Meet the writers', 'lampandpath' ) ),
				'writers_page'  => array( __( 'First link page', 'lampandpath' ), 'writers', 'page' ),
				'write_label'   => array( __( 'Second link label', 'lampandpath' ), __( 'Write for us', 'lampandpath' ) ),
				'write_page'    => array( __( 'Second link page', 'lampandpath' ), 'write-for-us', 'page' ),
			),
		),
		'newsletter' => array(
			'title'       => __( 'Newsletter', 'lampandpath' ),
			'description' => __( 'Shown next to the About section. The header Subscribe button links here, so keep it visible.', 'lampandpath' ),
			'fields'      => array(
				'heading' => array( __( 'Heading', 'lampandpath' ), __( 'A quiet word for your Monday', 'lampandpath' ) ),
				'text'    => array( __( 'Text', 'lampandpath' ), __( 'One short devotional, a prayer to carry into your week, and our best new articles. Free, with one-click unsubscribe.', 'lampandpath' ), 'textarea' ),
				'note'    => array( __( 'Note under the form', 'lampandpath' ), __( 'Sent every Monday morning.', 'lampandpath' ) ),
			),
		),
	);

	return $sections;
}

/**
 * Returns the default value of a field: its text, or for a page field the ID of the page with that slug.
 *
 * @param array $spec Field spec [label, default, type].
 * @return string|int
 */
function lampandpath_homepage_default( array $spec ) {
	if ( isset( $spec[2] ) && 'page' === $spec[2] ) {
		$page = get_page_by_path( $spec[1], OBJECT, array( 'page' ) );
		return $page ? (int) $page->ID : 0;
	}

	return $spec[1];
}

/**
 * Returns a homepage field value, with "{site}" replaced by the site title.
 *
 * @param string $section Section key, e.g. "latest".
 * @param string $field   Field key, e.g. "heading".
 * @return string|int
 */
function lampandpath_home_option( $section, $field ) {
	$sections = lampandpath_homepage_sections();
	if ( ! isset( $sections[ $section ]['fields'][ $field ] ) ) {
		return '';
	}

	$value = get_theme_mod( 'lampandpath_home_' . $section . '_' . $field, null );
	if ( null === $value ) {
		$value = lampandpath_homepage_default( $sections[ $section ]['fields'][ $field ] );
	}

	return is_string( $value ) ? str_replace( '{site}', get_bloginfo( 'name' ), $value ) : $value;
}

/**
 * Returns the URL of a page field, or an empty string when no page is set.
 *
 * @param string $section Section key.
 * @param string $field   Page field key.
 * @return string
 */
function lampandpath_home_page_url( $section, $field ) {
	$page_id = (int) lampandpath_home_option( $section, $field );

	return $page_id ? (string) get_permalink( $page_id ) : '';
}

/**
 * Checks whether a homepage section is switched on in the Customizer.
 *
 * @param string $section Section key.
 * @return bool
 */
function lampandpath_home_show( $section ) {
	return (bool) get_theme_mod( 'lampandpath_home_' . $section . '_show', true );
}

/**
 * Sanitizes a checkbox setting.
 *
 * @param mixed $value Submitted value.
 * @return bool
 */
function lampandpath_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Adds one homepage setting and its control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 * @param string               $section      Section key.
 * @param string               $field        Field key.
 * @param array                $spec         Field spec [label, default, type].
 */
function lampandpath_homepage_add_control( $wp_customize, $section, $field, array $spec ) {
	$type        = isset( $spec[2] ) ? $spec[2] : 'text';
	$sanitizers  = array(
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'page'     => 'absint',
		'checkbox' => 'lampandpath_sanitize_checkbox',
	);
	$setting_id  = 'lampandpath_home_' . $section . '_' . $field;
	$control_ops = array(
		'label'   => $spec[0],
		'section' => 'lampandpath_home_' . $section,
		'type'    => 'page' === $type ? 'dropdown-pages' : $type,
	);

	$wp_customize->add_setting(
		$setting_id,
		array(
			'default'           => lampandpath_homepage_default( $spec ),
			'sanitize_callback' => $sanitizers[ $type ],
		)
	);
	$wp_customize->add_control( $setting_id, $control_ops );
}

/**
 * Registers the Customizer "Homepage" panel with a section per homepage section.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function lampandpath_homepage_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'lampandpath_homepage',
		array(
			'title'    => __( 'Homepage', 'lampandpath' ),
			'priority' => 100,
		)
	);

	foreach ( lampandpath_homepage_sections() as $section => $config ) {
		$wp_customize->add_section(
			'lampandpath_home_' . $section,
			array(
				'title'       => $config['title'],
				'description' => isset( $config['description'] ) ? $config['description'] : '',
				'panel'       => 'lampandpath_homepage',
			)
		);

		lampandpath_homepage_add_control( $wp_customize, $section, 'show', array( __( 'Show this section', 'lampandpath' ), true, 'checkbox' ) );
		foreach ( $config['fields'] as $field => $spec ) {
			lampandpath_homepage_add_control( $wp_customize, $section, $field, $spec );
		}
	}
}
add_action( 'customize_register', 'lampandpath_homepage_customize_register' );

/**
 * Returns the featured article: the latest sticky post, or the latest post.
 *
 * Cached for the request, because the latest articles list excludes it.
 *
 * @return WP_Post|null
 */
function lampandpath_home_hero_post() {
	static $hero = false;
	if ( false !== $hero ) {
		return $hero;
	}

	$sticky = array_filter( array_map( 'absint', (array) get_option( 'sticky_posts', array() ) ) );
	$posts  = $sticky ? get_posts(
		array(
			'post__in'       => $sticky,
			'posts_per_page' => 1,
		)
	) : array();
	if ( ! $posts ) {
		$posts = get_posts( array( 'posts_per_page' => 1 ) );
	}

	$hero = $posts ? $posts[0] : null;

	return $hero;
}
