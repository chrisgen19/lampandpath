<?php
/**
 * Plugin Name:       Lamp & Path Core
 * Description:       Content model, forms and developer tooling for the Lamp & Path site. The lampandpath theme handles presentation.
 * Version:           0.3.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Chris Diomampo
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lampandpath-core
 *
 * @package Lampandpath_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'LAMPANDPATH_CORE_VERSION', '0.3.0' );
define( 'LAMPANDPATH_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once LAMPANDPATH_CORE_DIR . 'includes/post-types.php';
require_once LAMPANDPATH_CORE_DIR . 'includes/meta.php';
require_once LAMPANDPATH_CORE_DIR . 'includes/articles.php';
require_once LAMPANDPATH_CORE_DIR . 'includes/template-api.php';
require_once LAMPANDPATH_CORE_DIR . 'includes/forms.php';
require_once LAMPANDPATH_CORE_DIR . 'includes/rest.php';

if ( is_admin() ) {
	require_once LAMPANDPATH_CORE_DIR . 'includes/admin/fields.php';
	require_once LAMPANDPATH_CORE_DIR . 'includes/admin/meta-boxes.php';
	require_once LAMPANDPATH_CORE_DIR . 'includes/admin/term-fields.php';
	require_once LAMPANDPATH_CORE_DIR . 'includes/admin/user-fields.php';
	require_once LAMPANDPATH_CORE_DIR . 'includes/admin/columns.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once LAMPANDPATH_CORE_DIR . 'includes/cli/class-lampandpath-seed-content.php';
	require_once LAMPANDPATH_CORE_DIR . 'includes/cli/class-lampandpath-seed-command.php';
	WP_CLI::add_command( 'lampandpath seed', 'Lampandpath_Seed_Command' );
}

/**
 * Registers the post types before flushing, so their URLs work right after activation.
 */
function lampandpath_core_activate() {
	lampandpath_core_register_post_types();
	lampandpath_core_register_taxonomies();
	flush_rewrite_rules();
	update_option( 'lampandpath_core_version', LAMPANDPATH_CORE_VERSION );
}
register_activation_hook( __FILE__, 'lampandpath_core_activate' );

/**
 * Flushes rewrite rules once after the plugin is updated.
 *
 * Deploys update the plugin in place (git pull), so the activation hook never
 * runs again and new post type or taxonomy URLs would 404 until the next flush.
 * Runs after the post types register on init (priority 10).
 */
function lampandpath_core_maybe_upgrade() {
	if ( get_option( 'lampandpath_core_version' ) === LAMPANDPATH_CORE_VERSION ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'lampandpath_core_version', LAMPANDPATH_CORE_VERSION );
}
add_action( 'init', 'lampandpath_core_maybe_upgrade', 99 );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
