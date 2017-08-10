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
		$this->taxonomy_slug = empty( $permalinks['vendor_shop_base'] ) ? _x( 'vendor', 'slug', 'dc-woocommerce-multi-vendor' ) : $permalinks['vendor_shop_base'];
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
		  'name' => apply_filters('wcmp_vendor_taxonomy_name', __( 'WCMp Vendors' , 'dc-woocommerce-multi-vendor' )),
		  'singular_name' => __( 'Vendor', 'dc-woocommerce-multi-vendor' ),
		  'menu_name' => __( 'Vendors' , 'dc-woocommerce-multi-vendor' ),
		  'search_items' =>  __( 'Search Vendors' , 'dc-woocommerce-multi-vendor'),
		  'all_items' => __( 'All Vendors' , 'dc-woocommerce-multi-vendor' ),
		  'parent_item' => __( 'Parent Vendor' , 'dc-woocommerce-multi-vendor' ),
		  'parent_item_colon' => __( 'Parent Vendor:' , 'dc-woocommerce-multi-vendor' ),
		  'view_item' => __( 'View Vendor' , 'dc-woocommerce-multi-vendor' ),
		  'edit_item' => __( 'Edit Vendor' , 'dc-woocommerce-multi-vendor' ),
		  'update_item' => __( 'Update Vendor' , 'dc-woocommerce-multi-vendor' ),
		  'add_new_item' => __( 'Add New Vendor' , 'dc-woocommerce-multi-vendor' ),
		  'new_item_name' => __( 'New Vendor Name' , 'dc-woocommerce-multi-vendor' ),
		  'popular_items' => __( 'Popular Vendors' , 'dc-woocommerce-multi-vendor' ),
		  'separate_items_with_commas' => __( 'Separate vendors with commas' , 'dc-woocommerce-multi-vendor' ),
		  'add_or_remove_items' => __( 'Add or remove vendors' , 'dc-woocommerce-multi-vendor' ),
		  'choose_from_most_used' => __( 'Choose from most used vendors' , 'dc-woocommerce-multi-vendor' ),
		  'not_found' => __( 'No vendors found' , 'dc-woocommerce-multi-vendor' ),
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