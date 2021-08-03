<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/mihailnesterov
 * @since             1.0.0
 * @package           Get_All_Ids
 *
 * @wordpress-plugin
 * Plugin Name:       Get All IDs
 * Plugin URI:        https://github.com/mihailnesterov/get-all-ids
 * Description:       Wordpress Plugin helps to get ID of any WP post, page, taxonomy, tag, category, WooCommerce product, any custom type.
 * Version:           1.0.0
 * Author:            Mihail Nesterov
 * Author URI:        https://github.com/mihailnesterov
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       get-all-ids
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GET_ALL_IDS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-get-all-ids-activator.php
 */
function activate_get_all_ids() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-get-all-ids-activator.php';
	Get_All_Ids_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-get-all-ids-deactivator.php
 */
function deactivate_get_all_ids() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-get-all-ids-deactivator.php';
	Get_All_Ids_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_get_all_ids' );
register_deactivation_hook( __FILE__, 'deactivate_get_all_ids' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-get-all-ids.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_get_all_ids() {

	$plugin = new Get_All_Ids();
	$plugin->run();

}
run_get_all_ids();
