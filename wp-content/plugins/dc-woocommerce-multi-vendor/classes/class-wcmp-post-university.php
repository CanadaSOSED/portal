<?php
/**
 * WCMp Email Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_University {
	private $post_type;
  public $dir;
  public $file;
  
  public function __construct() {
    $this->post_type = 'wcmp_university';
    $this->register_post_type();		
  }
  
  /**
	 * Register university post type
	 *
	 * @access public
	 * @return void
	*/
  function register_post_type() {
		global $WCMp;
		if ( post_type_exists($this->post_type) ) return;
		$labels = array(
			'name' => _x( 'Knowledgebase', 'post type general name' , $WCMp->text_domain ),
			'singular_name' => _x( 'Knowledgebase', 'post type singular name' , $WCMp->text_domain ),
			'add_new' => _x( 'Add New', $this->post_type , $WCMp->text_domain ),
			'add_new_item' => sprintf( __( 'Add New %s' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'edit_item' => sprintf( __( 'Edit %s' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain) ),
			'new_item' => sprintf( __( 'New %s' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain) ),
			'all_items' => sprintf( __( 'All %s' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'view_item' => sprintf( __( 'View %s' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'search_items' => sprintf( __( 'Search %a' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'not_found' =>  sprintf( __( 'No %s found' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash' , $WCMp->text_domain ), __( 'Knowledgebase' , $WCMp->text_domain ) ),
			'parent_item_colon' => '',
			'all_items' => __( 'Knowledgebase' , $WCMp->text_domain ),
			'menu_name' => __( 'Knowledgebase' , $WCMp->text_domain )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => current_user_can( 'manage_woocommerce' ) ? 'wcmp' : false,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor',  'comments' ),
			'menu_position' => 15,
			'menu_icon' => $WCMp->plugin_url.'/assets/images/dualcube.png'
		);		
		register_post_type( $this->post_type, $args );
	}  
	
	
}
