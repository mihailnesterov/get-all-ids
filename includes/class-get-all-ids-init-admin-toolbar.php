<?php

/**
 * Initialize plugin admin toolbar.
 *
 * @link       https://github.com/mihailnesterov
 * @since      1.0.0
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 */

/**
 * Initialize admin toolbar for the plugin.
 *
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 * @author     Mihail Nesterov <mhause@mail.ru>
 */
class Get_All_Ids_Init_Admin_Toolbar {

	/**
	 * The object of WP_Query.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $data    The object WP_Query.
	 */
	private $data;

	/**
	 * Initialize admin page.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		
		$this->include_data_class();
		$this->data = new Get_All_Ids_Admin_Page_Data();
		add_action('admin_bar_menu', array( &$this, 'admin_add_toolbar_items' ), 99);
	}

	/**
	 * Include data class.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function include_data_class() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-get-all-ids-admin-page-data.php';
	}

	/**
	 * Add admin toolbar menu item.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	 Object admin_bar
	 */
	public function admin_add_toolbar_items( $admin_bar ){

		if ( !is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			
			$object = $this->get_current_page_id_and_type();
			
			$admin_bar->add_menu( array(
				'id'    => 'get-all-ids-admin-bar-item',
				'title' => ucfirst( $object['type'] ) . ' ID = <b>' . $object['ID'] . '</b>',
				'href'  => get_dashboard_url() . 'admin.php?page=get-all-ids-plugin',
				'meta'  => array(
					'title' => __( 'Go to plugin page', 'get_all_ids' ),
					'html' => $this->get_toolbar_html( $object ),
				),
			));
		}

	}

	/**
	 * Get current page ID and type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return	 Array [ID, type]
	 */
	private function get_current_page_id_and_type() {
		
		global $wp_query;
		$object_id = 0;
		$object_type = '';

		switch (true) {
			case is_front_page():
				$object_id = get_the_ID();
				$object_type = __('Home page', 'get_all_ids');
				break;
			case is_category():
				$object_id = $wp_query->get_queried_object_id();
				$object_type = __('Category', 'get_all_ids');
				break;
			case is_tag():
				$object_id = $wp_query->get_queried_object_id();
				$object_type = __('Tag', 'get_all_ids');
				break;
			case is_home():
				$object_id = get_option( 'page_for_posts' );
				$object_type = __('Posts archive', 'get_all_ids');
				break;
			case is_date():
				$object_id = get_the_ID();
				if( is_year() ) {
					$object_type = __('Posts archive by year', 'get_all_ids');
				} elseif( is_month() ) {
					$object_type = __('Posts archive by month', 'get_all_ids');
				} elseif( is_day() ) {
					$object_type = __('Posts archive by day', 'get_all_ids');
				}
				break;
			case is_search():
				$object_id = 'no ID';
				$object_type = __('Search page', 'get_all_ids');
				break;
			case is_404():
				$object_type = __('404 page', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_product():
				$object_id = get_the_ID();
				$object_type = __('WooCommerce Product', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_shop():
				$object_id = get_option( 'woocommerce_shop_page_id' );
				$object_type = __('WooCommerce Shop Page', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_product_category():
				$object_id = $wp_query->get_queried_object_id();
				$object_type = __('WooCommerce Category', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_product_tag():
				$object_id = $wp_query->get_queried_object_id();
				$object_type = __('WooCommerce Product Tag', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_cart():
				$object_id = get_option( 'woocommerce_cart_page_id' );
				$object_type = __('WooCommerce Cart Page', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_checkout():
				$object_id = get_option( 'woocommerce_checkout_page_id' );
				$object_type = __('WooCommerce Checkout Page', 'get_all_ids');
				break;
			case class_exists('woocommerce') && is_account_page():
				$object_id = get_option( 'woocommerce_myaccount_page_id' );
				$object_type = __('WooCommerce My Account Page', 'get_all_ids');
				break;
			case is_tax():
				$object_id = $wp_query->get_queried_object_id();
				$object_type = __('Taxonomy', 'get_all_ids');
				break;
			default:
				$object_id = get_the_ID();
				$object_type = __(get_post_type(), 'get_all_ids');
				break;
		}

		return array(
			'ID' => $object_id, 
			'type' => $object_type
		);
	}

	/**
	 * Get toolbar html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_toolbar_html( $object ) { 

		$page_parents 		= $this->get_block_html($this->get_page_parents( $object ), 'Parents');
		$page_childrens 	= $this->get_block_html($this->get_page_childrens( $object ), 'Children');
		$categories 		= $this->get_block_html($this->get_categories( $object ), 'In categories');
		$comments 			= $this->get_comments_html($this->get_comments( $object ), 'Comments');
		$tags 				= $this->get_block_html($this->get_posts_with_tag( $object ), 'With tag');
		$post_tags 			= $this->get_block_html($this->get_post_tags( $object ), 'Tags');
		$cart 				= $this->get_cart_html($this->get_cart( $object ), 'Products in cart');
		$enabled_gateways 	= $this->get_enabled_gateways_html($this->get_enabled_gateways( $object ), 'Enabled Gateways');
		$customer_id 		= $this->get_customer_id_html($this->get_customer_id(), 'User ID');
		$orders 			= $this->get_orders_html($this->get_orders(), 'User orders');
		$cat_posts			= $this->get_block_html($this->get_category_posts( $object ), 'In category');
		$wc_cat_products	= $this->get_block_html($this->get_wc_category_products( $object ), 'Products');
		

		$isAttached = $page_parents !== '' || 
			$page_childrens !== '' || 
			$categories !== '' ||
			($comments && $comments[0]) || 
			$tags ||
			$post_tags ||
			$cart || 
			$enabled_gateways || 
			$customer_id || 
			$orders || 
			$cat_posts || 
			$wc_cat_products
			? true 
			: false;
	
		return '<div class="get-all-ids-toolbar-container">'
			. '<div class="attach-pannel">' 
				. $this->get_attach_pannel_checkbox($isAttached) 
			. '</div>'
			. '<div class="overflow-y">'
				. $page_parents
				. $page_childrens
				. $categories
				. $comments
				. $tags 
				. $post_tags 
				. $cart
				. $enabled_gateways
				. $customer_id
				. $orders
				. $cat_posts
				. $wc_cat_products
			. '</div>'
		. '</div>'
		;
	}

	/**
	 * Get page childrens.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_page_childrens( $object ) { 
		
		$all_pages 		= $this->data->get_all_posts_by_type();
		$page_childrens = get_page_children( $object['ID'], $all_pages );
		
		$page_childrens = count($page_childrens) > 0 && $object['type'] === 'page' 
			? 
			array_reduce( 
				$page_childrens, 
				function($res,$item) {
					return $res 
					. '<a href="' 
					. get_permalink( $item->ID )
					. '" title="' . $item->post_title . '">' 
					. $item->ID 
					. '</a>';
				}
			) : '0';
		
		return $page_childrens;
	}
	
	/**
	 * Get page parents.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_page_parents( $object ) {
		return count(get_post_ancestors( $object['ID'])) > 0 && $object['type'] === 'page' 
			? 
			array_reduce( 
				get_post_ancestors( $object['ID']), 
				function($res,$item) {
					return $res . '<a href="' . get_permalink( $item ). '" title="' . get_the_title($item) . '">' .$item . '</a>';
				}
			) : '0';
	}

	/**
	 * Get categories.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_categories( $object ) {
		
		$categories = count( wp_get_post_categories( $object['ID'] )) > 0  && 
			!is_category() && 
			!is_tax() && 
			!is_date()
			? 
			array_reduce( 
				wp_get_post_categories( $object['ID'] ), 
				function($res,$item) {
					return $res 
					. '<a href="' . get_category_link( $item )
					. '" title="' . get_the_category_by_ID($item) 
					. '">' 
					. $item 
					. '</a>';
				}
			) : '0';
	
		if( get_the_terms( $object['ID'], 'product_cat' ) && 
			!is_category() && 
			!is_tax() && 
			!is_date() ) {
			
			$categories = array_reduce( 
				get_the_terms( $object['ID'], 'product_cat' ), 
				function($res,$item) {
					return $res 
					. '<a href="' . get_category_link( $item->term_id )
					. '" title="' . $item->name 
					. '">' 
					. $item->term_id 
					. '</a>';
				}
			);
		}

		return $categories;
	}

	/**
	 * Get category posts.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_category_posts( $object ) {

		if ( ( is_category() || is_tax() ) && 
			( !is_product_category() && !is_tag() && !is_product_tag() ) ) {

			return array_reduce( 
				get_posts( array(
					'numberposts' => -1,
					'category'    => $object['ID'],
					'orderby'     => 'date',
					'order'       => 'DESC',
					'post_status'   => 'publish',
					'suppress_filters' => true,
				) ), 
				function($res,$item) {
					return $res 
					. '<a href="' . get_permalink($item->ID) 
					. '" title="' . esc_html( $item->post_title )
					. '">' 
					. $item->ID
					. '</a>';
				}
			);
		}
		
		return '0';
	}

	/**
	 * Get comments.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_comments( $object ) {
		
		$comments = array();
		
		if( !is_search() && 
			!is_tax() && 
			!is_product_category() && 
			!is_category() && 
			!is_archive() && 
			!is_tag() && 
			!is_404() && 
			get_comments(array(
					'post_id' => $object['ID']
				)) 
			) {
			
			$comments = get_comments(array(
				'post_id' => $object['ID']
			));
			
			$comments = array_reduce( 
				$comments, 
				function($res,$item) {
					return $res 
					. '<a href="' . get_comment_link($item->comment_ID) 
					. '" title="' . esc_html( $item->comment_content )
					. '">' 
					. $item->comment_ID
					. '</a>';
				}
			);
		}
		
		return $comments;
	}

	/**
	 * Get comments html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array comments
	 * @param	 String title
	 */
	private function get_comments_html( $comments, $comments_title ) {
		return (
			$comments && $comments[0]  ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $comments_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $comments
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get post tags.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_post_tags( $object ) {
		
		$tags 			= array();
		$post_tags 		= get_the_terms( $object['ID'], 'post_tag'  );
		$product_tags 	= get_the_terms( $object['ID'], 'product_tag' );

		if ( isset( $post_tags ) && !empty( $post_tags ) ) $tags = $post_tags;
		if ( isset( $product_tags ) && !empty( $product_tags ) ) $tags = $product_tags;

		if ( isset( $tags ) && !empty( $tags ) &&
			( is_single() || ( class_exists('woocommerce') && is_product() ) ) ) {

			return array_reduce( 
				$tags, 
				function($res,$item) {
					return $res 
					. '<a href="' . get_tag_link($item->term_id) 
					. '" title="' . esc_html( $item->slug ) . ' (' . esc_html( $item->name ) . ')'
					. '">' 
					. $item->term_id
					. '</a>';
				}
			);
		}
		
		return '0';
	}

	/**
	 * Get posts with tag.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_posts_with_tag( $object ) {
		
		$posts 		 = array();
		$post_tag 	 = is_tag() ? get_term( $object['ID'], 'post_tag') : null;
		$product_tag = class_exists('woocommerce') && is_product_tag() ? get_term( $object['ID'], 'product_tag') : null;

		if ( isset( $post_tag ) ) {
			$posts = get_posts( array(
				'numberposts' => -1,
				'tag'    => $post_tag->slug,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_status'   => 'publish',
				'post_type'   => 'post',
				'suppress_filters' => true,
			) );
		}
			
		if ( isset( $product_tag ) ) {
			$posts = get_posts( array(
				'numberposts' => -1,
				'product_tag'    => $product_tag->slug,
				'orderby'     => 'date',
				'order'       => 'DESC',
				'post_status'   => 'publish',
				'post_type'   => 'product',
				'suppress_filters' => true,
			) );
		}

		if ( isset( $posts ) && !empty( $posts ) ) {

			return array_reduce( 
				$posts, 
				function($res,$item) {
					return $res 
					. '<a href="' . get_permalink($item->ID) 
					. '" title="' . esc_html( $item->post_title )
					. '">' 
					. $item->ID
					. '</a>';
				}
			);
		}
		
		return '0';
	}

	/**
	 * Get WooCommerce cart.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_cart( $object ) {
		
		$cart = array();
		
		if( class_exists('woocommerce') && is_cart() ) {
			
			foreach ( WC()->cart->get_cart() as $key => $value ){
				array_push( $cart, $value['data']->get_id() );
			}
			
			$cart = array_reduce( 
				$cart, 
				function($res,$item) {
					return $res 
					. '<a href="' . get_permalink($item) 
					. '" title="' . esc_html( wc_get_product( $item )->get_title() )
					. '">' 
					. $item
					. '</a>';
				}
			);

		}

		return $cart;
	}
	
	/**
	 * Get WooCommerce cart html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array cart
	 * @param	 String title
	 */
	private function get_cart_html( $cart, $cart_title ) {
		return (
			$cart ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $cart_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $cart
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get WooCommerce enabled payment gateways.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_enabled_gateways( $object ) {
		
		$enabled_gateways = array();

		if( class_exists('woocommerce') && is_checkout() ) {
			
			$gateways = WC()->payment_gateways->get_available_payment_gateways();
	
			if( $gateways ) {
				foreach( $gateways as $gateway ) {
					if( $gateway->enabled == 'yes' ) {
						$enabled_gateways[] = $gateway;
					}
				}
			}
	
			$enabled_gateways = array_reduce( 
				$enabled_gateways, 
				function($res,$item) {
					return $res 
					. '<a title="' . $item->method_title
					. '">' 
					. $item->id
					. '</a>';
				}
			);
		}

		return $enabled_gateways;
	}

	/**
	 * Get WooCommerce enabled payment gateways html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 String gateways
	 */
	private function get_enabled_gateways_html( $enabled_gateways, $gateways_title ) {
		return (
			$enabled_gateways ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $gateways_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $enabled_gateways
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get WooCommerce customer ID.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_customer_id() {
		$customer_id = '';

		if( class_exists('woocommerce') && is_account_page() ) {

			$customer_id = get_current_user_id();

			if( $customer_id !== '' && $customer_id > 0 ) {
				$customer_id = '<a href="' . wc_customer_edit_account_url() 
				. '" title="' . wp_get_current_user()->user_login
				. '">' 
				. $customer_id
				. '</a>';
			}
		}

		return $customer_id;
	}

	/**
	 * Get WooCommerce customer ID html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Integer user ID
	 * @param	 String title
	 */
	private function get_customer_id_html( $customer_id, $customer_title ) {
		return (
			$customer_id && $customer_id !== '' ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $customer_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $customer_id
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get WooCommerce orders.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_orders() {
		
		$orders = array();

		if( class_exists('woocommerce') && is_account_page() ) {

			$orders = get_posts( array(
				'numberposts' => -1,
				'meta_key'    => '_customer_user',
				'meta_value'  => get_current_user_id(),
				'post_type'   => wc_get_order_types(),
				'post_status' => array_keys( wc_get_order_statuses() ),
			) );

			if( count($orders) > 0 ) {
				$orders = array_reduce( 
					$orders, 
					function($res,$item) {
						return $res 
						. '<a href="' 
						. wc_get_order($item->ID)->get_view_order_url() 
						. '" title="' 
						. $item->post_title 
						. ' (' 
						. $item->post_status
						. ')">' 
						. $item->ID
						. '</a>';
					}
				);
			}
		}

		return $orders;
	}

	/**
	 * Get WooCommerce orders html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array orders
	 * @param	 String title
	 */
	private function get_orders_html( $orders, $orders_title ) {
		return (
			$orders ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $orders_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $orders
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get WooCommerce category products.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array [ID, type]
	 */
	private function get_wc_category_products( $object ) {
		
		if ( class_exists('woocommerce') && is_product_category() ) {

			return array_reduce(
				wc_get_products( array(
					'orderby' => 'date',  
        			'order' => 'DESC', 
					'status' => 'publish',
					'category' => get_term( $object['ID'] )->slug
				) ), 
				function($res,$item) {
					return $res 
					. '<a href="' . get_permalink($item->get_ID()) 
					. '" title="' . esc_html( $item->get_name() )
					. '">' 
					. $item->get_ID()
					. '</a>';
				}
			);
		}
		
		return '0';
	}


	/**
	 * Get common block html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Array
	 * @param	 String
	 */
	private function get_block_html( $block_data, $block_title ) {
		return (
			$block_data !== '0'  ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( $block_title, 'get-all-ids' )
			. '<span>&#10230;</span></p>'
			.'<p><b>'
			. $block_data
			. '</b></p>'
			. '</div>'
			: ''
		);
	}

	/**
	 * Get fixed html.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param	 Boolean true | false
	 */
	private function get_attach_pannel_checkbox( $isAttached ) {
		return (
			$isAttached ?
			'<div class="get-all-ids-toolbar-row">'
			. '<p>'
			. __( 'Attach' , 'get-all-ids' )
			. '</p>'
			.'<p class="text-right">'
			. '<input type="checkbox" id="get-all-ids-toolbar-attach" name="get-all-ids-toolbar-attach" value="0" title="'. __( 'Attach widget over the window' , 'get-all-ids' ) .'">'
			. '</p>'
			. '</div>'
			: ''
		);
	}
}
