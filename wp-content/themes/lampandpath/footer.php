<?php
/**
 * The footer for the theme
 *
 * Prints the site footer and closes the page wrapper opened in header.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Lampandpath
 */

get_template_part( 'template-parts/footer/site-footer' );
?>
</div>

<?php // Screen reader announcements from assets/js/interactions.js (e.g. "Verse copied"). ?>
<div id="lp-live" class="sr-only" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>
</html>
