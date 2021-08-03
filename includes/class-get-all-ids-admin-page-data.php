<?php

/**
 * Get data from DB.
 *
 * @link       https://github.com/mihailnesterov
 * @since      1.0.0
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 */

/**
 * Get plugin admin page data from DB.
 *
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 * @author     Mihail Nesterov <mhause@mail.ru>
 */
class Get_All_Ids_Admin_Page_Data {

	/**
	 * Get public post types.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_public_post_types() { 
		return get_post_types(array(
			'public'   => true,
			//'_builtin' => false
		));
	}

	/**
	 * Get posts by post_types.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param	 String ""|"post,page,attachment..."
	 */
	public function get_posts_by_post_types() { 
		
		$post_types 	= $this->get_public_post_types();
		$posts 			= array();
		$post_type_selected = "post";

		if( isset( $_REQUEST['search-by-id'] ) ) {
			
			$include = array_map(
				function( $item ) {
					return intval( $item );
				},
				explode(',', trim( $_REQUEST['search-by-id'] ))
			);

			foreach( $post_types as $post_type ) {

				$args = array(
					'numberposts' => -1,
					'include'     => $include,
					'orderby'     => 'ID',
					'order'       => 'ASC',
					'post_type'   => $post_type,
					'suppress_filters' => true,
				);			
	
				$posts_found = get_posts( $args );
	
				foreach( $posts_found as $post ) {
						
					setup_postdata($post);
	
					array_push($posts, array(
						'ID' => $post->ID,
						'post_title' => $post->post_title,
						'post_type' => $post->post_type
					));
					
				}
				wp_reset_postdata();
			}
			
		}
		
		if( !isset( $_REQUEST['search-by-id'] ) && ( isset($post_types) && !empty($post_types) ) ) {

			$post_type_selected = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : "post";
			$pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 0;
			$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : "";

			/*echo '<div style="float:right;"><pre>';
			var_dump(array(
				'post_type_selected' => $post_type_selected,
				'pagenum' => $pagenum,
				'limit' => $limit,
				'term' => $term,
			 ));
			 echo '</pre></div>';*/

			foreach( $post_types as $post_type ) {

				$args = array(
					//'numberposts' => -1,
					'posts_per_page' => $limit,
					'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ),
					'offset' => ($pagenum * $limit),
					'orderby'     => 'ID',
					'order'       => 'ASC',
					'post_type'   => $post_type,
					'suppress_filters' => true,
				);

				$posts_of_post_type = get_posts( $args );

				/*echo '<div style="float:right;"><pre>';
				var_dump($args);
				var_dump(count($posts_of_post_type));
				echo '</pre></div>';*/

				foreach( $posts_of_post_type as $post ) {
					
					setup_postdata($post);

					if( $post_type_selected !== "" && $post_type_selected === $post_type) {
						array_push($posts, array(
							'ID' => $post->ID,
							'post_title' => $post->post_title,
							'post_type' => $post->post_type
						));
					} elseif( $post_type_selected === "" ) {
						array_push($posts, array(
							'ID' => $post->ID,
							'post_title' => $post->post_title,
							'post_type' => $post->post_type
						));
					}
					
				}
				wp_reset_postdata();
			}
		}

		/*unset($post_types);

		echo '<div style="float:right;"><pre>';
		var_dump($posts);
		echo '</pre></div>';*/
		
		return $posts;
	}

	/**
	 * Get terms by public taxonomies.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_terms_by_public_taxonomies($taxonomy="") { 

		$terms_of_public_taxonomies = array();
		$terms = $this->get_terms_by_taxonomy($taxonomy);

		if( !empty($terms) ) {
			foreach( $terms as $term ){
				array_push($terms_of_public_taxonomies, array(
					'ID' => $term->term_id,
					'term_name' => $term->name,
					'taxonomy' => $term->taxonomy,
					'description' => $term->description
				));
			}
		}

		unset($terms);
		
		return $terms_of_public_taxonomies;
	}

	/**
	 * Get terms by taxonomy.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_terms_by_taxonomy($taxonomy="") {
		return get_terms( array(
			'taxonomy'      => $taxonomy === "" ? $this->get_public_taxonomies() : $taxonomy,
			'orderby'       => 'id', 
			'order'         => 'ASC',
			'hide_empty'    => true, 
			'object_ids'    => null,
			'include'       => array(),
			'exclude'       => array(), 
			'exclude_tree'  => array(), 
			'number'        => '', 
			'fields'        => 'all', 
			'count'         => false,
			'slug'          => '', 
			'parent'         => '',
			'hierarchical'  => true, 
			'child_of'      => 0, 
			'get'           => 'all', // ставим all чтобы получить все термины
			'name__like'    => '',
			'pad_counts'    => false, 
			'offset'        => '', 
			'search'        => '', 
			'cache_domain'  => 'core',
			'name'          => '',    // str/arr поле name для получения термина по нему. C 4.2.
			'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
			'update_term_meta_cache' => true, // подгружать метаданные в кэш
			'meta_query'    => '',
		) );
	}

	/**
	 * Get public taxonomies.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_public_taxonomies() { 
		$taxonomies = get_taxonomies( array(
			'public'   => true,
			//'_builtin' => false
		));

		$public_taxonomies = array();
		
		if( !empty($taxonomies) ) {
			foreach( $taxonomies as $taxonomy ) {
				array_push($public_taxonomies, $taxonomy);
			}
		}
		
		return $public_taxonomies;
	}

	/**
	 * Get all posts by type (or any if $type === null).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_all_posts_by_type($type=null) { 

		$args = array(
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'post_type' => $type ? $type : $this->get_public_post_types()
		);

		if ( $type === "attachment" ) {
			$args['post_status'] = 'inherit';
			$args['post_mime_type'] = 'image/jpeg,image/gif,image/jpg,image/png,image/webp';
		}

		return ( new WP_Query() )->query( $args );
	}

}
