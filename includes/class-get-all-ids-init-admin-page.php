<?php

/**
 * Initialize plugin admin page.
 *
 * @link       https://github.com/mihailnesterov
 * @since      1.0.0
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 */

/**
 * Initialize admin page for the plugin.
 *
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 * @author     Mihail Nesterov <mhause@mail.ru>
 */
class Get_All_Ids_Init_Admin_Page {

	/**
	 * Initialize admin page.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		add_action('admin_menu', array( &$this, 'add_admin_menu_page' ) );
	}

	/**
	 * Add plugin admin menu page.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_admin_menu_page() {
		add_menu_page( 
			'Get all IDs / Get all by ID', 
			'Get all IDs', 
			'manage_options', 
			'get-all-ids-plugin', 
			[$this, 'render_admin_menu_page_html'] 
		);
	}

	/**
	 * Add plugin admin menu page html (render).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function render_admin_menu_page_html() { 
		$this->include_render_class();
		$html = new Get_All_Ids_Admin_Page_Html();
		$html->get_header_html();
		$html->get_posts_html();
	}

	/**
	 * Include render class.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function include_render_class() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-all-ids-admin-page-html.php';
	}
}
