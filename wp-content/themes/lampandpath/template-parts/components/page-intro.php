<?php
/**
 * Page intro band: the h1 of an inner page with its label, description and extras.
 *
 * Args:
 * - title       (string) The h1. Required.
 * - eyebrow     (string) Small label above the title, e.g. "Category".
 * - description (string) HTML, e.g. a term description or page content. Passed through wp_kses_post().
 * - tone        (string) "surface" (default) or "accent" background.
 * - avatar      (int)    User ID: shows the writer's initials avatar beside the text.
 * - icon        (string) Icon name: shows the icon in a circle beside the text.
 * - meta        (string[]) Short facts shown in a row, e.g. role and article count.
 * - links       (array[])  Buttons: each [url, label, icon (optional)].
 * - search      (bool)   Show the search form.
 *
 * @package Lampandpath
 */

$lampandpath_args = wp_parse_args(
	$args,
	array(
		'title'       => '',
		'eyebrow'     => '',
		'description' => '',
		'tone'        => 'surface',
		'avatar'      => 0,
		'icon'        => '',
		'meta'        => array(),
		'links'       => array(),
		'search'      => false,
	)
);
$lampandpath_meta = array_filter( (array) $lampandpath_args['meta'], 'strlen' );
?>
<header class="<?php echo esc_attr( 'accent' === $lampandpath_args['tone'] ? 'bg-accent-soft' : 'bg-surface-soft' ); ?>">
	<div class="mx-auto flex max-w-site flex-col gap-6 px-4 py-12 sm:flex-row sm:items-start sm:gap-8 sm:px-6 lg:px-10 lg:py-16">
		<?php if ( $lampandpath_args['avatar'] ) : ?>
			<?php lampandpath_avatar( (int) $lampandpath_args['avatar'], 'xl' ); ?>
		<?php elseif ( $lampandpath_args['icon'] ) : ?>
			<span aria-hidden="true" class="flex size-16 shrink-0 items-center justify-center rounded-full text-accent <?php echo esc_attr( 'accent' === $lampandpath_args['tone'] ? 'bg-white' : 'bg-accent-tint' ); ?>">
				<?php lampandpath_icon( $lampandpath_args['icon'], array( 'size' => 30 ) ); ?>
			</span>
		<?php endif; ?>

		<div class="min-w-0 max-w-[760px] flex-1">
			<?php if ( $lampandpath_args['eyebrow'] ) : ?>
				<p class="mb-3 text-[15px] leading-[22px] font-semibold text-accent"><?php echo esc_html( $lampandpath_args['eyebrow'] ); ?></p>
			<?php endif; ?>

			<h1 class="<?php echo esc_attr( lampandpath_page_title_class() ); ?>"><?php echo esc_html( $lampandpath_args['title'] ); ?></h1>

			<?php if ( $lampandpath_meta ) : ?>
				<p class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[15px] leading-[22px] text-muted">
					<?php foreach ( $lampandpath_meta as $lampandpath_fact ) : ?>
						<span><?php echo esc_html( $lampandpath_fact ); ?></span>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>

			<?php if ( $lampandpath_args['description'] ) : ?>
				<div class="mt-4 text-lg leading-[30px] text-ink-soft lg:text-[19px] [&_a]:font-semibold [&_a]:text-accent [&_a]:underline [&_p+p]:mt-3">
					<?php echo wp_kses_post( $lampandpath_args['description'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $lampandpath_args['links'] ) : ?>
				<div class="mt-6 flex flex-wrap gap-3">
					<?php foreach ( $lampandpath_args['links'] as $lampandpath_link ) : ?>
						<?php $lampandpath_link_icon = isset( $lampandpath_link[2] ) ? $lampandpath_link[2] : ''; ?>
						<a href="<?php echo esc_url( $lampandpath_link[0] ); ?>" class="<?php echo esc_attr( lampandpath_button_class( 'outline', 'md', (bool) $lampandpath_link_icon ) ); ?>">
							<?php
							if ( $lampandpath_link_icon ) {
								lampandpath_icon( $lampandpath_link_icon, array( 'size' => 18 ) );
							}
							echo esc_html( $lampandpath_link[1] );
							?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $lampandpath_args['search'] ) : ?>
				<div class="mt-7 max-w-xl"><?php get_search_form(); ?></div>
			<?php endif; ?>
		</div>
	</div>
</header>
