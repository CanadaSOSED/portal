<?php
/**
 * WCMp Email Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_Notices {
	private $post_type;
  public $dir;
  public $file;
  
  public function __construct() {
    $this->post_type = 'wcmp_vendor_notice';
    $this->register_post_type();
		add_action( 'add_meta_boxes', array($this,'vendor_notices_add_meta_box_addtional_field') );
		add_action( 'save_post', array( $this, 'vendor_notices_save_addtional_field' ), 10, 3 );		
  }
  
  
  public function vendor_notices_add_meta_box_addtional_field() {
  	global $WCMp;
		$screens = array( 'wcmp_vendor_notice' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wcmp_vendor_notice_addtional_field',
				__( 'Addtional Fields', $WCMp->text_domain ),
				array($this,'wcmp_vendor_notice_addtional_field_callback'),
				$screen,
				'normal',
				'high'
			);
		}  	
  }
  
  public function wcmp_vendor_notice_addtional_field_callback() {
  	global $WCMp, $post;
  	$url = get_post_meta($post->ID,'_wcmp_vendor_notices_url', true);
  	?>
  	<div id="_wcmp_vendor_notices_url_div" class="_wcmp_vendor_notices_url_div" >
  		<label>Enter Url</label>
  		<input type="text" name="_wcmp_vendor_notices_url" value="<?php echo $url; ?>" class="widefat" style="margin:10px; border:1px solid #888; width:90%;" >			
		</div>			
		<?php
  }
  
  public function vendor_notices_save_addtional_field($post_id, $post, $update) {
  	global $WCMp;
  	if (  $this->post_type != $post->post_type ) {
        return;
    }
    if(isset($_POST['_wcmp_vendor_notices_url'])) {
    	update_post_meta($post_id, '_wcmp_vendor_notices_url', $_POST['_wcmp_vendor_notices_url']);    	
    } 	
  }
  
  /**
	 * Register vendor_notices post type
	 *
	 * @access public
	 * @return void
	*/
  function register_post_type() {
		global $WCMp;
		if ( post_type_exists($this->post_type) ) return;
		$labels = array(
			'name' => _x( 'Announcements', 'post type general name' , $WCMp->text_domain ),
			'singular_name' => _x( 'Announcements', 'post type singular name' , $WCMp->text_domain ),
			'add_new' => _x( 'Add New', $this->post_type , $WCMp->text_domain ),
			'add_new_item' => sprintf( __( 'Add New %s' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'edit_item' => sprintf( __( 'Edit %s' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain) ),
			'new_item' => sprintf( __( 'New %s' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain) ),
			'all_items' => sprintf( __( 'All %s' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'view_item' => sprintf( __( 'View %s' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'search_items' => sprintf( __( 'Search %a' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'not_found' =>  sprintf( __( 'No %s found' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash' , $WCMp->text_domain ), __( 'Announcements' , $WCMp->text_domain ) ),
			'parent_item_colon' => '',
			'all_items' => __( 'Announcements' , $WCMp->text_domain ),
			'menu_name' => __( 'Announcements' , $WCMp->text_domain )
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
			'supports' => array( 'title', 'editor', 'excerpt' ),
			'menu_position' => 10,
			'menu_icon' => $WCMp->plugin_url.'/assets/images/dualcube.png'
		);		
		register_post_type( $this->post_type, $args );
	}  
	
	
}
