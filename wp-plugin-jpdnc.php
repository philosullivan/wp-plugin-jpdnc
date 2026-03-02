<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jpdnc.org
 * @since             1.0.0
 * @package           Jpdnc_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       JPDNC Redesign Compatibility Plugin
 * Plugin URI:        https://jpdnc.org
 * Description:       A custom WordPress plugin for JPDNC.
 * Version:           1.0.0
 * Author:            JPDNC
 * Author URI:        https://jpdnc.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jpdnc-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'JPDNC_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jpdnc-plugin-activator.php
 */
function activate_jpdnc_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jpdnc-plugin-activator.php';
	Jpdnc_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jpdnc-plugin-deactivator.php
 */
function deactivate_jpdnc_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jpdnc-plugin-deactivator.php';
	Jpdnc_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jpdnc_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_jpdnc_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jpdnc-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off
 * the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jpdnc_plugin() {

	$plugin = new Jpdnc_Plugin();
	$plugin->run();

}
run_jpdnc_plugin();
