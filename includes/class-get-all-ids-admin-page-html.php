<?php

/**
 * Get (render) plugin admin page html.
 *
 * @link       https://github.com/mihailnesterov
 * @since      1.0.0
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 */

/**
 * Generate html for the plugin admin page.
 *
 *
 * @package    Get_All_Ids
 * @subpackage Get_All_Ids/includes
 * @author     Mihail Nesterov <mhause@mail.ru>
 */
class Get_All_Ids_Admin_Page_Html {

	/**
	 * The object of WP_Query.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $data    The object WP_Query.
	 */
	private $data;

	/**
	 * The array of all public WordPress post types.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $post_types    The registered public post types.
	 */
	private $post_types;

	/**
	 * The array of all posts by post types.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $posts    The posts by post types.
	 */
	private $posts;

	/**
	 * The array of all public taxonomies.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $taxonomies    The public taxonomies.
	 */
	private $taxonomies;

	/**
	 * The taxonomies filtered by term.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      String    $taxonomies_by_term    The taxonomies filtered by term.
	 */
	private $taxonomies_by_term;

	/**
	 * Initialize data for html.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		
		$this->include_data_class();
		
		$this->data 		= new Get_All_Ids_Admin_Page_Data();
		$this->post_types 	= $this->data->get_public_post_types();
		$this->posts 		= $this->data->get_posts_by_post_types();
		$this->taxonomies 	= $this->data->get_terms_by_public_taxonomies();
		$this->taxonomies_by_term	= 	isset($_REQUEST['term']) ? 
										$this->data->get_terms_by_public_taxonomies($_REQUEST['term']) : 
										array();
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
	 * Get admin page header html.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_header_html() { ?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>
		</div>
	<?php
	}

	/**
	 * Get plugin page url.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_plugin_page_url() {
		return esc_html(menu_page_url( 'get-all-ids-plugin'));
	}

	/**
	 * Get admin page posts html.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_posts_html() { 

		// posts (all or selected type)
		$all_pages = $this->data->get_all_posts_by_type();
		
		if( isset($_REQUEST['post_type']) && !empty($_REQUEST['post_type']) ) {
			$all_pages = $this->data->get_all_posts_by_type($_REQUEST['post_type']);
		}

		// limits (for pagination)
		$all_limits = array(10, 20, 50, 100);

		// posts count
		$posts_count = count($this->posts);

		?>

		<div class="container">
			
			<div class="row">
				<?php $this->get_content_nav_html() ?>
				<?php $this->get_taxonomies_nav_html() ?>
				<?php $this->get_search_nav_html() ?>
			</div>

			<!-- блок с пагинацией и выбором limit -->
			<?php if( $posts_count > 0 && !isset( $_REQUEST['search-by-id'] ) ): var_dump($posts_count); ?>
			<div class="row">
				<div class="pagination">
					<ul>
						<?php 
						$page_count = intval(count($all_pages) / (isset( $_REQUEST['limit'] ) ? intval($_REQUEST['limit']) : 1)) - 1;
						
						for( $i = 0; $i < $page_count; $i++ ) {
							$current_page = isset( $_REQUEST['pagenum'] ) ? intval($_REQUEST['pagenum']) : 0;
							$active_page = $current_page === $i ? ' active' : '';
						?>
							<li>
								<a 
									href="<?php $this->get_plugin_page_url(); ?>&post_type<?php echo esc_html( isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '' ); ?>&pagenum=<?php echo esc_html( $i ); ?>&limit=<?php echo esc_html( isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 20 ); ?>" 
									class="page-item<?php echo esc_html( $active_page ); ?>"
								>
									<?php echo esc_html( intval($i) + 1 ); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
					<ul>
						<?php for( $i = 0; $i < count($all_limits); $i++ ) {
							$current_limit = isset( $_REQUEST['limit'] ) ? intval($_REQUEST['limit']) : $all_limits[0];
							$active_limit = $current_limit === $all_limits[$i] ? ' active' : '';
						?>
							<li>
								<a href="#" class="page-item<?php echo esc_html( $active_limit ); ?>">
									<?php echo esc_html( $all_limits[$i] ); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php elseif( isset( $_REQUEST['search-by-id'] ) ): ?>
				<div class="row">
					<div class="nav-container">
						<h4><?php echo __('Found:', 'get-all-ids'); ?> <?php echo esc_html( $posts_count ); ?></h4>
					</div>
				</div>
			<?php endif; ?>
		
			<div class="row">
				<div class="content">
					<?php if( isset($_REQUEST['post_type']) || 
						( !isset($_REQUEST['post_type']) && !isset($_REQUEST['term']) ) ): ?>
						<?php foreach($this->posts as $post):?>
							<div class="line">
								<p style="flex:1;text-align:center;background-color:rgba(0,0,0,0.08)">
									<?php echo esc_html( $post['ID'] ); ?>
								</p>
								<p style="flex:6;padding-left:25px">
									<?php echo esc_html( $post['post_type'] ); ?>
								</p>
								<p style="display:flex;flex:16">
									<?php if( $post['post_type'] === 'attachment' ):
										$attachment_url = wp_get_attachment_image_url( $post['ID'], 'thumbnail', true );
									?>
										<img src="<?php echo esc_html( $attachment_url ); ?>" style="width:26px;margin-right:10px;" />
									<?php endif; ?>

									<a href="<?php echo get_admin_url(); ?>post.php?post=<?php echo esc_html($post['ID']); ?>&action=edit">
										<?php echo esc_html( $post['post_title'] ); ?>
									</a>
									<?php if( $post['post_type'] === 'page' ): 
										
										$page_childrens = get_page_children( $post['ID'], $all_pages );
										$page_parents 	= get_post_ancestors( $post['ID']);
										
									?>
										<?php if( !empty($page_parents) ): ?>
											<b style="padding: 0 10px 0 30px;"><?php echo __( 'Parents: ', 'get-all-ids' ); ?></b>
											<?php foreach( $page_parents as $parent_id ): 
												$parent = get_post( $parent_id ); ?>
												<a 
													style="background-color:rgba(0,0,0,0.08); padding: 0 10px;" 
													href="<?php echo get_admin_url(); ?>post.php?post=<?php echo esc_html($parent->ID); ?>&action=edit"
													title="<?php echo esc_html( $parent->post_title ); ?>"
												>
													<?php echo esc_html( $parent->ID ); ?>
												</a>
											<?php endforeach; ?>
										<?php endif; ?>

										<?php if( !empty( $page_childrens ) ): ?>
											<b style="padding: 0 10px 0 30px;"><?php echo __( 'Children: ', 'get-all-ids' ); ?></b>
											<?php foreach( $page_childrens as $child ): ?>
												<a 
													style="background-color:rgba(0,0,0,0.08); padding: 0 10px;" 
													href="<?php echo get_admin_url(); ?>post.php?post=<?php echo esc_html($child->ID); ?>&action=edit"
													title="<?php echo esc_html( $child->post_title ); ?>"
												>
													<?php echo esc_html( $child->ID ); ?>
												</a>
											<?php endforeach; ?>
										<?php endif; ?>
									
									<?php endif; ?>
								</p>
								
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if( isset($_REQUEST['term'])): 
						$taxonomies = array();
						foreach($this->taxonomies_by_term as $term) {
							array_push($taxonomies, $term['taxonomy']);
						}
						$taxonomies = array_unique($taxonomies);
					?>
						<?php foreach($this->taxonomies_by_term as $term):?>
							<div class="line">
								<p style="flex:1;text-align:center;background-color:rgba(0,0,0,0.08)">
								<?php echo esc_html($term['ID']); ?></p>
								<p style="flex:6;padding-left:25px"><?php echo esc_html($term['taxonomy']); ?></p>
								<p style="flex:8">
									<a href="<?php echo get_admin_url(); ?>term.php?taxonomy=<?php echo esc_html($term['taxonomy']); ?>&tag_ID=<?php echo esc_html($term['ID']); ?>&action=edit">
										<?php echo esc_html( $term['term_name'] ); ?>
									</a>
								</p>
								<p style="flex:8;padding-left:25px"><?php echo esc_html($term['description']); ?></p>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
			
		</div>
	<?php
	}	


	/**
	 * Get content nav html.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_content_nav_html() {?>
		<div class="nav-container">
			<h4><?php echo __('Content:', 'get-all-ids'); ?></h4>
			<ul class="nav-list">
				<li class="nav-list-item<?= isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === $post_type ? " active" : "" ?>">
					<a 
						href="<?php $this->get_plugin_page_url(); ?>&post_type=<?php echo esc_html(''); ?>&pagenum=0&limit=20"
						style="<?= (!isset($_REQUEST['post_type']) && !isset($_REQUEST['term'])) || (empty($_REQUEST['post_type']) && !isset($_REQUEST['term'])) ? "color:red;" : "" ?>"
					>
						<?php echo __('all', 'get-all-ids'); ?>
					</a>
				</li>
				<?php foreach($this->post_types as $post_type): ?>
					<li class="nav-list-item<?= isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === $post_type || (!isset($_REQUEST['post_type']) && $post_type === '') && !isset($_REQUEST['term']) ? " active" : "" ?>">
						<a href="<?php $this->get_plugin_page_url(); ?>&post_type=<?php echo esc_html($post_type); ?>&pagenum=<?php echo esc_html( isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 0); ?>&limit=<?php echo esc_html( isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 20); ?>">
							<?php echo esc_html( $post_type ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php
	}	

	/**
	 * Get taxonomies nav html.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_taxonomies_nav_html() {
		$taxonomies = array();
		foreach($this->taxonomies as $term) {
			array_push($taxonomies, $term['taxonomy']);
		}
		$taxonomies = array_unique($taxonomies);
		?>
		<div class="nav-container">
			<h4><?php echo __('Taxonomy:', 'get-all-ids'); ?></h4>
			<ul class="nav-list">
				<li class="nav-list-item<?= isset($_REQUEST['term']) && $_REQUEST['term'] === "" ? " active" : "" ?>">
					<a href="<?php $this->get_plugin_page_url(); ?>&term=<?php echo esc_html(''); ?>">
						<?php echo __('all', 'get-all-ids'); ?>
					</a>
				</li>
				<?php foreach($taxonomies as $term):?>
					<li class="nav-list-item<?= isset($_REQUEST['term']) && $_REQUEST['term'] === $term ? " active" : "" ?>">
						<a href="<?php $this->get_plugin_page_url(); ?>&term=<?php echo esc_html( $term ); ?>">
							<?php echo esc_html( $term ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php
	}	
	

	/**
	 * Get search nav html.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_search_nav_html() { ?>
		<div class="nav-container">
			<form class="search-form" action="<?php $this->get_plugin_page_url(); ?>" method="POST">
				<input 
					type="text" 
					name="search-by-id" 
					id="search-by-id" 
					autocomplete="off" 
					placeholder="<?php echo __('Search by ID', 'get-all-ids'); ?>" 
					value="<?php echo esc_html( isset( $_REQUEST['search-by-id'] ) ? $_REQUEST['search-by-id'] : '' ); ?>"
				/>
				<button><?php echo __('Find', 'get-all-ids'); ?></button>
			</form>
		</div>
	<?php
	}	
}
