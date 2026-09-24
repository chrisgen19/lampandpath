<?php
/**
 * Plugin Name:       Lamp & Path Core
 * Description:       Content model, forms and developer tooling for the Lamp & Path site. The lampandpath theme handles presentation.
 * Version:           0.1.0
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

define( 'LAMPANDPATH_CORE_VERSION', '0.1.0' );
define( 'LAMPANDPATH_CORE_DIR', plugin_dir_path( __FILE__ ) );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once LAMPANDPATH_CORE_DIR . 'includes/cli/class-lampandpath-seed-command.php';
	WP_CLI::add_command( 'lampandpath seed', 'Lampandpath_Seed_Command' );
}
