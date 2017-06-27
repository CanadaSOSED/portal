<?php
/**
 * WCFM plugin core
 *
 * Plugin Vendor Capability Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.3.1
 */
 
class WCFM_Vendor_Capability {
	
	private $wcfm_capability_options = array();

	public function __construct() {
		global $WCFM;
		
		if( wcfm_is_vendor() ) {
			$this->wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );
			
			// Menu Filter
			add_filter( 'wcfm_menus', array( &$this, 'wcfmcap_wcfm_menus' ), 50 );
			add_filter( 'wcfm_add_new_product_sub_menu', array( &$this, 'wcfmcap_add_new_product_sub_menu' ), 500 );
    	add_filter( 'wcfm_add_new_coupon_sub_menu', array( &$this, 'wcfmcap_add_new_coupon_sub_menu' ),500 );
			
			// Manage Product Permission
			add_filter( 'wcfm_product_types', array( &$this, 'wcfmcap_is_allow_product_types'), 500 );
			add_filter( 'wcfm_is_allow_job_package', array( &$this, 'wcfmcap_is_allow_job_package'), 500 );
			add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcfmcap_is_allow_fields_general' ), 500 );
			add_filter( 'wcfm_is_allow_inventory', array( &$this, 'wcfmcap_is_allow_inventory' ), 500 );
			add_filter( 'wcfm_is_allow_shipping', array( &$this, 'wcfmcap_is_allow_shipping' ), 500 );
			add_filter( 'wcfm_is_allow_tax', array( &$this, 'wcfmcap_is_allow_tax' ), 500 );
			add_filter( 'wcfm_is_allow_attribute', array( &$this, 'wcfmcap_is_allow_attribute' ), 500 );
			add_filter( 'wcfm_is_allow_variable', array( &$this, 'wcfmcap_is_allow_variable' ), 500 );
			add_filter( 'wcfm_is_allow_linked', array( &$this, 'wcfmcap_is_allow_linked' ), 500 );
			
			// Manage Order Permission
			add_filter( 'wcfm_is_allow_orders', array( &$this, 'wcfmcap_is_allow_orders' ), 500 );
			add_filter( 'wcfm_is_allow_order_details', array( &$this, 'wcfm_is_allow_order_details' ), 500 );
			add_filter( 'wcfm_allow_order_customer_details', array( &$this, 'wcfmcap_is_allow_order_customer_details' ), 500 );
			add_filter( 'wcfm_is_allow_export_csv', array( &$this, 'wcfmcap_is_allow_export_csv' ), 500 );
			add_filter( 'wcfm_is_allow_pdf_invoice', array( &$this, 'wcfmcap_is_allow_pdf_invoice' ), 500 );
			
			// Manage Reports Permission
			add_filter( 'wcfm_is_allow_reports', array( &$this, 'wcfmcap_is_allow_reports' ), 500 );
		}
	}
	
	// WCFM wcfmcap Menu
  function wcfmcap_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	$view_orders  = ( isset( $this->wcfm_capability_options['view_orders'] ) ) ? $this->wcfm_capability_options['view_orders'] : 'no';
  	$view_reports  = ( isset( $this->wcfm_capability_options['view_reports'] ) ) ? $this->wcfm_capability_options['view_reports'] : 'no';
  	$manage_booking = ( isset( $this->wcfm_capability_options['manage_booking'] ) ) ? $this->wcfm_capability_options['manage_booking'] : 'no';
  	
  	if( !current_user_can( 'edit_products' ) ) unset( $menus['wcfm-products'] );
  	if( !current_user_can( 'edit_shop_coupons' ) ) unset( $menus['wcfm-coupons'] );
  	if( $view_orders == 'yes' ) unset( $menus['wcfm-orders'] );
  	if( $view_reports == 'yes' ) unset( $menus['wcfm-reports'] );
  	if( $manage_booking == 'yes' ) unset( $menus['wcfm-bookings-dashboard'] );
  	
  	return $menus;
  }
  
  // WCV Add New Product Sub menu
  function wcfmcap_add_new_product_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_products' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCV Add New Coupon Sub menu
  function wcfmcap_add_new_coupon_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_shop_coupons' ) ) $has_new = false;
  	return $has_new;
  }
	
  // Product Types
  function wcfmcap_is_allow_product_types( $product_types ) {
  	
  	$simple = ( isset( $this->wcfm_capability_options['simple'] ) ) ? $this->wcfm_capability_options['simple'] : 'no';
		$variable = ( isset( $this->wcfm_capability_options['variable'] ) ) ? $this->wcfm_capability_options['variable'] : 'no';
		$grouped = ( isset( $this->wcfm_capability_options['grouped'] ) ) ? $this->wcfm_capability_options['grouped'] : 'no';
		$external = ( isset( $this->wcfm_capability_options['external'] ) ) ? $this->wcfm_capability_options['external'] : 'no';
		$booking = ( isset( $this->wcfm_capability_options['booking'] ) ) ? $this->wcfm_capability_options['booking'] : 'no';
		$job_package = ( isset( $this->wcfm_capability_options['job_package'] ) ) ? $this->wcfm_capability_options['job_package'] : 'no';
		$resume_package = ( isset( $this->wcfm_capability_options['resume_package'] ) ) ? $this->wcfm_capability_options['resume_package'] : 'no';
		$auction = ( isset( $this->wcfm_capability_options['auction'] ) ) ? $this->wcfm_capability_options['auction'] : 'no';
		$rental = ( isset( $this->wcfm_capability_options['rental'] ) ) ? $this->wcfm_capability_options['rental'] : 'no';
		$subscription = ( isset( $this->wcfm_capability_options['subscription'] ) ) ? $this->wcfm_capability_options['subscription'] : 'no';
		$variable_subscription = ( isset( $this->wcfm_capability_options['variable-subscription'] ) ) ? $this->wcfm_capability_options['variable-subscription'] : 'no';
		$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	
  	if( $simple == 'yes' ) unset( $product_types[ 'simple' ] );
		if( $variable == 'yes' ) unset( $product_types[ 'variable' ] );
		if( $grouped == 'yes' ) unset( $product_types[ 'grouped' ] );
		if( $external == 'yes' ) unset( $product_types[ 'external' ] );
		if( $booking == 'yes' ) unset( $product_types[ 'booking' ] );
		if( $job_package == 'yes' ) unset( $product_types[ 'job_package' ] );
		if( $resume_package == 'yes' ) unset( $product_types[ 'resume_package' ] );
		if( $auction == 'yes' ) unset( $product_types[ 'auction' ] );
		if( $rental == 'yes' ) unset( $product_types[ 'redq_rental' ] );
		if( $subscription == 'yes' ) unset( $product_types[ 'subscription' ] );
  	if( $variable_subscription == 'yes' ) unset( $product_types[ 'variable-subscription' ] );
  	if( $attributes == 'yes' ) unset( $product_types[ 'variable' ] );
  	if( $attributes == 'yes' ) unset( $product_types[ 'variable-subscription' ] );
		
		return $product_types;
  }
  
  // Job Package
  function wcfmcap_is_allow_job_package( $allow ) {
  	$job_package = ( isset( $this->wcfm_capability_options['job_package'] ) ) ? $this->wcfm_capability_options['job_package'] : 'no';
  	if( $job_package == 'yes' ) return false;
  	return $allow;
  }
  
  // General Fields
  function wcfmcap_is_allow_fields_general( $general_fields ) {
  	//$product_misc = (array) WC_Vendors::$pv_options->get_option( 'hide_product_misc' );
  	//if( !empty( $product_misc['sku'] ) ) unset( $general_fields['sku'] );
  		
  	return $general_fields;
  }
  
  // Inventory
  function wcfmcap_is_allow_inventory( $allow ) {
  	$inventory = ( isset( $this->wcfm_capability_options['inventory'] ) ) ? $this->wcfm_capability_options['inventory'] : 'no';
  	if( $inventory == 'yes' ) return false;
  	return $allow;
  }
  
  // Shipping
  function wcfmcap_is_allow_shipping( $allow ) {
  	$shipping = ( isset( $this->wcfm_capability_options['shipping'] ) ) ? $this->wcfm_capability_options['shipping'] : 'no';
  	if( $shipping == 'yes' ) return false;
  	return $allow;
  }
  
  // Tax
  function wcfmcap_is_allow_tax( $allow ) {
  	$taxes = ( isset( $this->wcfm_capability_options['taxes'] ) ) ? $this->wcfm_capability_options['taxes'] : 'no';
  	if( $taxes == 'yes' ) return false;
  	return $allow;
  }
  
  // Attributes
  function wcfmcap_is_allow_attribute( $allow ) {
  	$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	if( $attributes == 'yes' ) return false;
  	return $allow;
  }
  
  // Variable
  function wcfmcap_is_allow_variable( $allow ) {
  	$attributes = ( isset( $this->wcfm_capability_options['attributes'] ) ) ? $this->wcfm_capability_options['attributes'] : 'no';
  	$variable = ( isset( $this->wcfm_capability_options['variable'] ) ) ? $this->wcfm_capability_options['variable'] : 'no';
  	$variable_subscription = ( isset( $this->wcfm_capability_options['variable-subscription'] ) ) ? $this->wcfm_capability_options['variable-subscription'] : 'no';
  	
  	if( ( $attributes == 'yes' ) && ( $variable == 'yes' ) && ( $variable_subscription == 'yes' ) ) return false;
  	return $allow;
  }
  
  // Linked
  function wcfmcap_is_allow_linked( $allow ) {
  	$linked = ( isset( $this->wcfm_capability_options['linked'] ) ) ? $this->wcfm_capability_options['linked'] : 'no';
  	if( $linked == 'yes' ) return false;
  	return $allow;
  }
  
  // Allow View Orders
  function wcfmcap_is_allow_orders( $allow ) {
  	$view_orders = ( isset( $this->wcfm_capability_options['view_orders'] ) ) ? $this->wcfm_capability_options['view_orders'] : 'no';
  	if( $view_orders == 'yes' ) return false;
  	return $allow;
  }
  
  // Allow View Order Details
  function wcfm_is_allow_order_details( $allow ) {
  	$view_order_details = ( isset( $this->wcfm_capability_options['view_order_details'] ) ) ? $this->wcfm_capability_options['view_order_details'] : 'no';
  	if( $view_order_details == 'yes' ) return false;
  	return $allow;
  }
  
  // Order Customer Details
  function wcfmcap_is_allow_order_customer_details( $allow ) {
  	$view_email = ( isset( $this->wcfm_capability_options['view_email'] ) ) ? $this->wcfm_capability_options['view_email'] : 'no';
  	if( $view_email == 'yes' ) return false;
  	return $allow;
  }
  
  // Order EXport CSV
  function wcfmcap_is_allow_export_csv( $allow ) {
  	$export_csv = ( isset( $this->wcfm_capability_options['export_csv'] ) ) ? $this->wcfm_capability_options['export_csv'] : 'no';
  	if( $export_csv == 'yes' ) return false;
  	return $allow;
  }
  
  // Order PDF Invoice
  function wcfmcap_is_allow_pdf_invoice( $allow ) {
  	$pdf_invoice = ( isset( $this->wcfm_capability_options['pdf_invoice'] ) ) ? $this->wcfm_capability_options['pdf_invoice'] : 'no';
  	if( $pdf_invoice == 'yes' ) return false;
  	return $allow;
  }
  
  // Allow View Reports
  function wcfmcap_is_allow_reports( $allow ) {
  	$view_reports = ( isset( $this->wcfm_capability_options['view_reports'] ) ) ? $this->wcfm_capability_options['view_reports'] : 'no';
  	if( $view_reports == 'yes' ) return false;
  	return $allow;
  }
}