<?php
/**
 * Writer page: profile (avatar, name, role, bio) and their articles.
 *
 * @package Lampandpath
 */

get_header();

$lampandpath_writer = get_queried_object();
$lampandpath_count  = (int) count_user_posts( $lampandpath_writer->ID, 'post', true );
?>

<main id="primary" class="grow">
	<?php
	get_template_part(
		'template-parts/components/page-intro',
		null,
		array(
			'eyebrow'     => __( 'Writer', 'lampandpath' ),
			'title'       => $lampandpath_writer->display_name,
			'avatar'      => $lampandpath_writer->ID,
			'meta'        => array(
				(string) get_user_meta( $lampandpath_writer->ID, 'lp_role_title', true ),
				/* translators: %s: number of articles. */
				sprintf( _n( '%s article', '%s articles', $lampandpath_count, 'lampandpath' ), number_format_i18n( $lampandpath_count ) ),
			),
			'description' => wpautop( esc_html( $lampandpath_writer->description ) ),
		)
	);
	get_template_part(
		'template-parts/content/listing',
		null,
		array(
			/* translators: %s: writer's name. */
			'empty' => sprintf( __( '%s hasn’t published any articles yet.', 'lampandpath' ), $lampandpath_writer->display_name ),
		)
	);
	?>
</main>

<?php
get_footer();
