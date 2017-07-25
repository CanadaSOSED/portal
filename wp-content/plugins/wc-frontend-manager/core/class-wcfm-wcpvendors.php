<?php

/**
 * WCFM plugin core
 *
 * Marketplace WC Product Vendors Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.1.0
 */
 
class WCFM_WCPVendors {
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	// Store Identity
    	add_filter( 'wcfm_store_logo', array( &$this, 'wcpvendors_store_logo' ) );
    	add_filter( 'wcfm_store_name', array( &$this, 'wcpvendors_store_name' ) );
    	
    	// Myaccount Vendor Dashboard URL
    	add_action( 'woocommerce_before_my_account', array( &$this, 'wcpvendors_add_section' ), 0 );
    	
    	// WP Admin View
    	add_filter( 'wcfm_allow_wp_admin_view', array( &$this, 'wcpvendors_allow_wp_admin_view' ) );
    	
			// Allow Vendor user to manage product from catalog
			add_filter( 'wcfm_allwoed_user_rols', array( &$this, 'allow_wcpvendors_vendor_role' ) );
			
			// Filter Vendor Products
			add_filter( 'wcfm_products_args', array( &$this, 'wcpvendors_products_args' ) );
			
			// Manage Vendor Product Permissions
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcpvendors_product_manage_vendor_association' ), 10, 2 );
			
			// Manage Vendor Product Export Permissions - 2.4.2
			add_filter( 'woocommerce_product_export_row_data', array( &$this, 'wcpvendors_product_export_row_data' ), 100, 2 );
			
			// Filter Vendor Coupons
			add_filter( 'wcfm_coupons_args', array( &$this, 'wcpvendors_coupons_args' ) );
			
			// Manage Vendor Coupon Permission
			add_filter( 'wcfm_coupon_types', array( &$this, 'wcpvendors_coupon_types' ) );
			
			// Manage Order Details Permission
			add_filter( 'wcfm_allow_order_details', array( &$this, 'wcpvendors_is_allow_order_details' ) );
			add_filter( 'wcfm_valid_line_items', array( &$this, 'wcpvendors_valid_line_items' ), 10, 3 );
			add_filter( 'wcfm_order_details_shipping_line_item', array( &$this, 'wcpvendors_is_allow_order_details_shipping_line_item' ) );
			add_filter( 'wcfm_order_details_tax_line_item', array( &$this, 'wcpvendors_is_allow_order_details_tax_line_item' ) );
			add_filter( 'wcfm_order_details_line_total_head', array( &$this, 'wcpvendors_is_allow_order_details_line_total_head' ) );
			add_filter( 'wcfm_order_details_line_total', array( &$this, 'wcpvendors_is_allow_order_details_line_total' ) );
			add_filter( 'wcfm_order_details_tax_total', array( &$this, 'wcpvendors_is_allow_order_details_tax_total' ) );
			add_filter( 'wcfm_order_details_fee_line_item', array( &$this, 'wcpvendors_is_allow_order_details_fee_line_item' ) );
			add_filter( 'wcfm_order_details_refund_line_item', array( &$this, 'wcpvendors_is_allow_order_details_refund_line_item' ) );
			add_filter( 'wcfm_order_details_coupon_line_item', array( &$this, 'wcpvendors_is_allow_order_details_coupon_line_item' ) );
			add_filter( 'wcfm_order_details_total', array( &$this, 'wcpvendors_is_allow_wcfm_order_details_total' ) );
			add_action ( 'wcfm_order_details_after_line_total_head', array( &$this, 'wcpvendors_after_line_total_head' ) );
			add_action ( 'wcfm_after_order_details_line_total', array( &$this, 'wcpvendors_after_line_total' ), 10, 2 );
			add_action ( 'wcfm_order_totals_after_total', array( &$this, 'wcpvendors_order_total_commission' ) );
			
			// Report Filter
			add_filter( 'wcfm_report_out_of_stock_query_from', array( &$this, 'wcpvendors_report_out_of_stock_query_from' ), 100, 2 );
			add_filter( 'woocommerce_reports_order_statuses', array( &$this, 'wcpvendors_reports_order_statuses' ) );
			add_filter( 'woocommerce_dashboard_status_widget_top_seller_query', array( &$this, 'wcpvendors_dashboard_status_widget_top_seller_query'), 100 );
			
			// Message Author Filter
			add_filter( 'wcfm_message_author', array( &$this, 'wcpvendors_message_author' ) );
		}
  }
  
  // WCFM wcpvendors Store Logo
  function wcpvendors_store_logo( $store_logo ) {
  	$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();
		$logo = ! empty( $vendor_data['logo'] ) ? $vendor_data['logo'] : '';
		$logo_image_url = wp_get_attachment_image_src( $logo, 'thumbnail' );
		
		if ( !empty( $logo_image_url ) ) {
			$store_logo = $logo_image_url[0];
		}
  	return $store_logo;
  }
  
  // WCFM wcpvendors Store Name
  function wcpvendors_store_name( $store_name ) {
  	$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();
  	$store_name = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
  	$shop_link = get_term_link( WC_Product_Vendors_Utils::get_logged_in_vendor(), WC_PRODUCT_VENDORS_TAXONOMY );
  	if( $store_name ) { $store_name = '<a target="_blank" href="' . apply_filters('wcpv_vendor_shop_permalink', $shop_link) . '">' . $store_name . '</a>'; }
  	else { $store_name = '<a target="_blank" href="' . apply_filters('wcpv_vendor_shop_permalink', $shop_link) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
  	return $store_name;
  }
  
  /**
	 * Add vendor specific section to my accounts page
	 *
	 */
	public function wcpvendors_add_section() {
		
		if ( WC_Product_Vendors_Utils::auth_vendor_user() ) {

			printf( '<a href="%s" title="%s" class="button wcfm-vendor-dashboard-link">%s</a>', esc_url( get_wcfm_page() ), esc_attr( __( 'Vendor Dashboard', 'woocommerce-product-vendors' ) ), __( 'Vendor Dashboard', 'woocommerce-product-vendors' ) );
			
			?>
			<style> .vendor-dashboard-link { display: none; } </style>
			<?php
		}

		return true;
	}
  
  // WCPV Wp-admin view
  function wcpvendors_allow_wp_admin_view( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  function allow_wcpvendors_vendor_role( $allowed_roles ) {
  	if( wcfm_is_vendor() ) {
  		$allowed_roles[] = 'wc_product_vendors_manager_vendor';
  		$allowed_roles[] = 'wc_product_vendors_admin_vendor';
  	}
  	return $allowed_roles;
  }
  
  function wcpvendors_products_args( $args ) {
  	if( wcfm_is_vendor() ) {
  		//$args['author'] = get_current_user_id();
  		$args['tax_query'][] = array(
																		'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
																		'field' => 'id',
																		'terms' => array(WC_Product_Vendors_Utils::get_logged_in_vendor()),
																		'operator' => 'IN'
																	);
		}
  	return $args;
  }
  
  // Product Vendor association on Product save
  function wcpvendors_product_manage_vendor_association( $new_product_id, $wcfm_products_manage_form_data ) {
  	global $WCFM;
  	
  	if ( WC_Product_Vendors_Utils::auth_vendor_user() ) {

			// check post type to be product
			if ( 'product' === get_post_type( $new_product_id ) ) {

				// automatically set the vendor term for this product
				wp_set_object_terms( $new_product_id, WC_Product_Vendors_Utils::get_logged_in_vendor(), WC_PRODUCT_VENDORS_TAXONOMY );

				// set visibility to catalog/search
				update_post_meta( $new_product_id, '_visibility', 'visible' );
			}
		}
  }
  
  // Product Export Data Filter - 2.4.2
  function wcpvendors_product_export_row_data( $row, $product ) {
  	global $WCFM;
  	
  	$user_id = $this->vendor_id;
  	
  	$products = $this->WCPV_get_vendor_products();
		
		if( !in_array( $product->get_ID(), $products ) ) return array();
		
		return $row;
  }
  
  // Coupons Args
  function wcpvendors_coupons_args( $args ) {
  	if( wcfm_is_vendor() ) $args['author'] = get_current_user_id();
  	return $args;
  }
  
  // Coupon Types
  function wcpvendors_coupon_types( $types ) {
  	$wcmp_coupon_types = array( 'percent', 'fixed_product' );
  	foreach( $types as $type => $label ) 
  		if( !in_array( $type, $wcmp_coupon_types ) ) unset( $types[$type] );
  	return $types;
  } 
  
  // Order Status details
  function wcpvendors_is_allow_order_details( $allow ) {
  	return false;
  }
  
  // Filter Order Details Line Items as Per Vendor
  function wcpvendors_valid_line_items( $items, $order_id ) {
  	global $WCFM, $wpdb;
  	
  	$vendor_id   = WC_Product_Vendors_Utils::get_logged_in_vendor();
		$valid_items = array();
  	
  	$sql = 'SELECT * FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . ' AS commission';
		$sql .= ' WHERE 1=1';
		$sql .= " AND `vendor_id` = {$vendor_id}";
		$sql .= " AND `order_id` = {$order_id}";
		$data_items = $wpdb->get_results( $sql );
		
		if( !empty($data_items) ) {
			foreach( $data_items as $data_item ) {
				if ( ! empty( $data_item->variation_id ) ) $valid_items[] = $data_item->variation_id;
				$valid_items[] = $data_item->product_id;
			}
		}
  	
  	$valid = array();
  	foreach ($items as $key => $value) {
			if ( in_array( $value['variation_id'], $valid_items) || in_array( $value['product_id'], $valid_items ) ) {
				$valid[] = $value;
			}
		}
  	return $valid;
  }
  
  // Order Details Shipping Line Item
  function wcpvendors_is_allow_order_details_shipping_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item
  function wcpvendors_is_allow_order_details_tax_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item Head
  function wcpvendors_is_allow_order_details_line_total_head( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item
  function wcpvendors_is_allow_order_details_line_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Total
  function wcpvendors_is_allow_order_details_tax_total( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Fee Line Item
  function wcpvendors_is_allow_order_details_fee_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Coupon Line Item
  function wcpvendors_is_allow_order_details_coupon_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Refunded Line Item
  function wcpvendors_is_allow_order_details_refund_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Total
  function wcpvendors_is_allow_wcfm_order_details_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // wcpvendors After Order Total Line Head
  function wcpvendors_after_line_total_head( $order ) {
  	global $WCFM;
  	?>
		<th class="line_cost sortable" data-sort="float"><?php _e( 'Commission', 'wc-frontend-manager' ); ?></th>
  	<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></th>
  	<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Tax', 'wc-frontend-manager' ); ?></th>
  	<?php
  }
  
  // wcpvendors after Order total Line item
  function wcpvendors_after_line_total( $item, $order ) {
  	global $WCFM, $wpdb;
  	$vendor_id   = WC_Product_Vendors_Utils::get_logged_in_vendor();
  	$qty = ( isset( $item['qty'] ) ? esc_html( $item['qty'] ) : '1' );
		
		$sql = "
			SELECT product_commission_amount, product_shipping_amount, product_shipping_tax_amount, product_tax_amount 
			FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " 
			WHERE   (product_id = " . $item['product_id'] . ")
			AND     order_id = " . $order->get_id() . "
			AND     `vendor_id` = " . $vendor_id;
		$order_line_due = $wpdb->get_results( $sql );
		if( !empty( $order_line_due ) ) {
		?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->product_commission_amount ); ?></div>
			</td>
			<td class="line_cost no_ipad no_mob" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->product_shipping_amount + $order_line_due[0]->product_shipping_tax_amount ); ?></div>
			</td>
			<td class="line_cost no_ipad no_mob" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->product_tax_amount ); ?></div>
			</td>
		<?php
		} else {
			?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<?php
		}
  }
  
  // wcpvendors Order Total Commission
  function wcpvendors_order_total_commission( $order_id ) {
  	global $WCFM, $wpdb;
  	$vendor_id   = WC_Product_Vendors_Utils::get_logged_in_vendor();
  	$sql = "
  	SELECT SUM(total_commission_amount) as line_total,
  	   SUM(	product_commission_amount) as product_commission_amount,
	     SUM(product_shipping_amount) as shipping,
	     SUM(product_shipping_tax_amount) as shipping_tax,
       SUM(product_tax_amount) as tax
       FROM " .WC_PRODUCT_VENDORS_COMMISSION_TABLE . "
       WHERE `vendor_id` = " . $vendor_id . " 
       AND order_id = " . $order_id;
    $order_due = $wpdb->get_results( $sql );
		?>
		<tr>
			<td class="label"><?php _e( 'Line Commission', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->product_commission_amount ); ?></div>
			</td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->shipping + $order_due[0]->shipping_tax ); ?></div>
			</td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Tax', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->tax ); ?></div>
			</td>
		</tr>
		<tr>
			<td class="label"><?php _e( 'Total Commission', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->line_total ); ?></div>
			</td>
		</tr>
		<?php
  }
  
  // Report Vendor Filter
  function wcpvendors_report_out_of_stock_query_from( $query_from, $stock ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = get_current_user_id();
  	
  	$query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND posts.post_author = {$user_id}
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
		";
		
		return $query_from;
  }
  
  // Report Order Data Status
  function wcpvendors_reports_order_statuses( $order_status ) {
  	$order_status = array( 'completed', 'processing' );
  	return $order_status;
  }
  
  // WCPVendor dashboard top seller query
  function wcpvendors_dashboard_status_widget_top_seller_query( $query ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = get_current_user_id();
  	
  	$products = $this->WCPV_get_vendor_products();
		
		if( !empty($products) )
		  $query['where'] .= "AND order_item_meta_2.meta_value in (" . implode( ',', $products ) . ")";
  	
  	return $query;
  }
  
  /**
   * WC Vendors current venndor products
   */
  function WCPV_get_vendor_products( $vendor_id = 0 ) {
  	if( !$vendor_id ) $vendor_id = get_current_user_id();
  	
  	$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'product',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => true 
						);
		
		$args = apply_filters( 'wcfm_products_args', $args );
		$products = get_posts( $args );
		$products_arr = array(0);
		if(!empty($products)) {
			foreach($products as $product) {
				$products_arr[] = $product->ID;
			}
		}
		
		return $products_arr;
  }
  
  // Message authoe filter
  function wcpvendors_message_author( $author ) {
  	$author = WC_Product_Vendors_Utils::get_logged_in_vendor();
  	 return $author;
  }
}