<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/mihailnesterov
 * @since      1.0.0
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 * @author     Mihail Nesterov <mhause@mail.ru>
 */
class Get_All_Ids_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'get-all-ids',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
