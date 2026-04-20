<?php

/**
 * Define the internationalization functionality
 *
 * @link       https://jpdnc.org
 * @since      1.0.0
 *
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jpdnc_Plugin
 * @subpackage Jpdnc_Plugin/includes
 * @author     JPDNC <https://jpdnc.org>
 */
class Jpdnc_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jpdnc-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
