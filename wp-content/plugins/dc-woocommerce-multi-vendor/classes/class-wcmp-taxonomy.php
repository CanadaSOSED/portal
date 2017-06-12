<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCMp Taxonomy Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Taxonomy {
  
  public $taxonomy_name;
	
	public $taxonomy_slug;
  
	public function __construct() {
		global $WCMp;
		$permalinks = get_option( 'dc_vendors_permalinks' );
		$this->taxonomy_name = 'dc_vendor_shop';
		$this->taxonomy_slug = empty( $permalinks['vendor_shop_base'] ) ? _x( 'vendor', 'slug', $WCMp->text_domain ) : $permalinks['vendor_shop_base'];
		$this->register_post_taxonomy();
		add_action('created_term', array( $this, 'created_term' ), 10, 3);
	}
  
  /**
  * Register WCMp taxonomy
  *
	* @author WC Marketplace
	* @access private
	* @package WCMp
  */
  function register_post_taxonomy() {
    global $WCMp;
		$labels = array(
		  'name' => __( 'WCMp Vendors' , $WCMp->text_domain ),
		  'singular_name' => __( 'Vendor', $WCMp->text_domain ),
		  'menu_name' => __( 'Vendors' , $WCMp->text_domain ),
		  'search_items' =>  __( 'Search Vendors' , $WCMp->text_domain),
		  'all_items' => __( 'All Vendors' , $WCMp->text_domain ),
		  'parent_item' => __( 'Parent Vendor' , $WCMp->text_domain ),
		  'parent_item_colon' => __( 'Parent Vendor:' , $WCMp->text_domain ),
		  'view_item' => __( 'View Vendor' , $WCMp->text_domain ),
		  'edit_item' => __( 'Edit Vendor' , $WCMp->text_domain ),
		  'update_item' => __( 'Update Vendor' , $WCMp->text_domain ),
		  'add_new_item' => __( 'Add New Vendor' , $WCMp->text_domain ),
		  'new_item_name' => __( 'New Vendor Name' , $WCMp->text_domain ),
		  'popular_items' => __( 'Popular Vendors' , $WCMp->text_domain ),
		  'separate_items_with_commas' => __( 'Separate vendors with commas' , $WCMp->text_domain ),
		  'add_or_remove_items' => __( 'Add or remove vendors' , $WCMp->text_domain ),
		  'choose_from_most_used' => __( 'Choose from most used vendors' , $WCMp->text_domain ),
		  'not_found' => __( 'No vendors found' , $WCMp->text_domain ),
		);

		$vendor_slug = apply_filters( 'wcmp_vendor_slug', $this->taxonomy_slug );
		
		$args = array(
			'public' => true,
			'hierarchical' => false,
			'rewrite' => array( 'slug' => $vendor_slug ),
			'show_admin_column' => true,
			'show_ui' 	=> false,
			'labels' => $labels
		);
    register_taxonomy( $this->taxonomy_name, 'product', $args );
  }
  
  /**
  * Function created_term
  */
  function created_term( $term_id, $tt_id, $taxonomy ) {
  	if( $taxonomy == $this->taxonomy_name ) {
  		$term = get_term_by('id', $term_id, $this->taxonomy_name, 'ARRAY_A');
			$random_password = wp_generate_password( 12 );
			$unique_username = $this->generate_unique_username( $term['name'] );
			$user_id = wp_create_user( $unique_username , $random_password );
			if( ! is_wp_error( $user_id ) ) {
				$user = new WP_User( $user_id );
				$user->set_role( 'dc_vendor' );
			}
		}
  }
	
  /**
  * Function generate_unique_username
  */
	function generate_unique_username( $term_name, $count = '' ) {
		if ( ! username_exists( $term_name . $count ) ) {
			return $term_name . $count;
		}
		
		$count = ( $count == '' ) ? 1 : absint( $count ) + 1;
		
		$this->generate_unique_username( $term_name, $count ); 		
	}
  
}