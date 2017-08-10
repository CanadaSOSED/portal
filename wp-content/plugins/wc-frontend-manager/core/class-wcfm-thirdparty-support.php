<?php
/**
 * WCFM plugin core
 *
 * Third Party Plugin Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.2.2
 */
 
class WCFM_ThirdParty_Support {

	public function __construct() {
		global $WCFM;
		
    // WCFM Query Var Filter
		add_filter( 'wcfm_query_vars', array( &$this, 'wcfm_thirdparty_query_vars' ), 20 );
		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcfm_thirdparty_endpoint_title' ), 20, 2 );
		add_action( 'init', array( &$this, 'wcfm_thirdparty_init' ), 20 );
		
		// WCFM Third Party Endpoint Edit
		add_filter( 'wcfm_endpoints_slug', array( $this, 'wcfm_thirdparty_endpoints_slug' ) );
    
    // WCFM Menu Filter
    add_filter( 'wcfm_menus', array( &$this, 'wcfm_thirdparty_menus' ), 100 );
    
    // Third Party Product Type Capability
		add_filter( 'wcfm_settings_fields_product_types', array( &$this, 'wcfmcap_product_types' ), 50, 3 );
    
    // WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				// WC Paid Listing Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcfm_wcpl_product_types' ), 50 );
				
				// WC Paid Listing Product options
				add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcfm_wcpl_product_manage_fields_pricing' ), 50, 2 );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				// WC Rental Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcfm_wcrental_product_types' ), 80 );
				
				// WC Rental Product options
				add_filter( 'after_wcfm_products_manage_general', array( &$this, 'wcfm_wcrental_product_manage_fields' ), 80, 2 );
			}
		}
		
		// Product Manage Third Party Plugins View
    add_action( 'end_wcfm_products_manage', array( &$this, 'wcfm_thirdparty_products_manage_views' ), 100 );
	}
	
	
	/**
   * WCFM Third Party Query Var
   */
  function wcfm_thirdparty_query_vars( $query_vars ) {
  	
  	// WP Job Manager Support
  	if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
				$query_listing_vars = array(
					'wcfm-listings'       => ! empty( $wcfm_modified_endpoints['wcfm-listings'] ) ? $wcfm_modified_endpoints['wcfm-listings'] : 'wcfm-listings',
				);
		
				$query_vars = array_merge( $query_vars, $query_listing_vars );
			} else {
				delete_option( 'wcfm_updated_end_point_wc_listings' );
			}
		}
		
		return $query_vars;
  }
  
  /**
   * WCFM Third Party End Point Title
   */
  function wcfm_thirdparty_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
  		case 'wcfm-listings' :
				$title = __( 'Listings Dashboard', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCFM Third Party Endpoint Intialize
   */
  function wcfm_thirdparty_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_listings' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_listings', 1 );
		}
  }
  
  /**
	 * Thirdparty Endpoiint Edit
	 */
	function wcfm_thirdparty_endpoints_slug( $endpoints ) {
		
		// Listings
		if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$listings_endpoints = array(
															'wcfm-listings'  		   => 'wcfm-listings',
															);
				$endpoints = array_merge( $endpoints, $listings_endpoints );
			}
		}
		
		return $endpoints;
	}
	
	/**
	 * WCFM Third Party Plugins Menus
	 */
	function wcfm_thirdparty_menus( $menus ) {
  	global $WCFM;
  	
  	// WP Job Manager Support
  	if( $wcfm_allow_listings = apply_filters( 'wcfm_is_allow_listings', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$jobs_dashboard = get_permalink( get_option( 'job_manager_job_dashboard_page_id' ) );
				$post_a_job = get_permalink ( get_option( 'job_manager_submit_job_form_page_id' ) );
				if( $jobs_dashboard && $post_a_job ) {
					$menus = array_slice($menus, 0, 3, true) +
															array( 'wcfm-listings' => array(  'label'      => __( 'Listings', 'wc-frontend-manager' ),
																													 'url'        => get_wcfm_listings_url(),
																													 'icon'       => 'briefcase',
																													) )	 +
																array_slice($menus, 3, count($menus) - 3, true) ;
				}
			}
		}
		
  	return $menus;
  }
  
  /**
	 * WCFM Capability Product Types
	 */
	function wcfmcap_product_types( $product_types, $handler = 'wcfm_capability_options', $wcfm_capability_options = array() ) {
		global $WCFM, $WCFMu;
		
		if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
			$job_package = ( isset( $wcfm_capability_options['job_package'] ) ) ? $wcfm_capability_options['job_package'] : 'no';
		
			$product_types["job_package"] = array('label' => __('Job Package', 'wc-frontend-manager') , 'name' => $handler . '[job_package]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $job_package);
		}
		
		if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
			$rental = ( isset( $wcfm_capability_options['rental'] ) ) ? $wcfm_capability_options['rental'] : 'no';
			
			$product_types["rental"] = array('label' => __('Rental', 'wc-frontend-manager') , 'name' => $handler . '[rental]','type' => 'checkboxoffon', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $rental);
		}
		
		return $product_types;
	}
	
  /**
   * WC Paid Listing Product Type
   */
  function wcfm_wcpl_product_types( $pro_types ) {
  	global $WCFM;
  	
  	$pro_types['job_package'] = __( 'Job Package', 'wp-job-manager-wc-paid-listings' );
  	
  	return $pro_types;
  }
  
  /**
	 * WC Paid Listing Product General options
	 */
	function wcfm_wcpl_product_manage_fields_pricing( $general_fields, $product_id ) {
		global $WCFM;
		
		$_job_listing_package_subscription_type        = '';
		$_job_listing_limit     = '';
		$_job_listing_duration       = '';
		$_job_listing_featured = 'no';
		
		if( $product_id ) {
			$_job_listing_package_subscription_type        = get_post_meta( $product_id, '_job_listing_package_subscription_type', true );
			$_job_listing_limit     = get_post_meta( $product_id, '_job_listing_limit', true );
			$_job_listing_duration       = get_post_meta( $product_id, '_job_listing_duration', true );
			$_job_listing_featured = get_post_meta( $product_id, '_job_listing_featured', true );
		}
		
		$pos_counter = 4;
		if( WCFM_Dependencies::wcfmu_plugin_active_check() ) $pos_counter = 6;
		
		$general_fields = array_slice($general_fields, 0, $pos_counter, true) +
																	array( 
																				"_job_listing_package_subscription_type" => array( 'label' => __('Subscription Type', 'wp-job-manager-wc-paid-listings' ), 'type' => 'select', 'options' => array( 'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-job-manager-wc-paid-listings' ), 'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-job-manager-wc-paid-listings' ) ), 'class' => 'wcfm-select wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'hints' => __( 'Choose how subscriptions affect this package', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_package_subscription_type ),
																				"_job_listing_limit" => array( 'label' => __('Job listing limit', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => __( 'Unlimited', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of job listings a user can post with this package.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_limit ),
																				"_job_listing_duration" => array( 'label' => __('Job listing duration', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => 0, 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of days that the job listing will be active.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_duration ),
																				"_job_listing_featured" => array( 'label' => __('Feature Listings?', 'wp-job-manager-wc-paid-listings' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title checkbox_title wcfm_ele job_package', 'hints' => __( 'Feature this job listing - it will be styled differently and sticky.', 'wp-job-manager-wc-paid-listings' ), 'value' => 'yes', 'dfvalue' => $_job_listing_featured ),
																				) +
																	array_slice($general_fields, $pos_counter, count($general_fields) - 1, true) ;
		return $general_fields;
	}
	
	/**
   * WC Rental Product Type
   */
  function wcfm_wcrental_product_types( $pro_types ) {
  	global $WCFM;
  	
  	$pro_types['redq_rental'] = __( 'Rental Product', 'wc-frontend-manager' );
  	
  	return $pro_types;
  }
  
  /**
	 * WC Rental Product General options
	 */
	function wcfm_wcrental_product_manage_fields( $product_id = 0, $product_type ) {
		global $WCFM;
		
		$pricing_type = '';
		$hourly_price = '';
		$general_price = '';
		
		$redq_rental_availability = array();
		
		if( $product_id ) {
			$pricing_type = get_post_meta( $product_id, 'pricing_type', true );
			$hourly_price = get_post_meta( $product_id, 'hourly_price', true );
			$general_price = get_post_meta( $product_id, 'general_price', true );
			
			$redq_rental_availability = (array) get_post_meta( $product_id, 'redq_rental_availability', true );
		}
		
		
		?>
		
		<div class="page_collapsible products_manage_redq_rental redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_head"><label class="fa fa-cab"></label><?php _e('Rental', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields', array( 
					"pricing_type" => array( 'label' => __('Set Price Type', 'wc-frontend-manager') , 'type' => 'select', 'options' => apply_filters( 'wcfm_redq_rental_pricing_options', array( 'general_pricing' => __( 'General Pricing', 'wc-frontend-manager' ) ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $pricing_type, 'hints' => __( 'Choose a price type - this controls the schema.', 'wc-frontend-manager' ) ),
					"hourly_price" => array( 'label' => __('Hourly Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $hourly_price, 'hints' => __( 'Hourly price will be applicabe if booking or rental days min 1day', 'wc-frontend-manager' ), 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					"general_price" => array( 'label' => __('General Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele rentel_pricing rental_general_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_general_pricing redq_rental', 'value' => $general_price, 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					) ) );
				?>
			</div>
		</div>
		
		<div class="page_collapsible products_manage_redq_rental_availabillity redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_availabillity_head"><label class="fa fa-clock-o"></label><?php _e('Availability', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_availabillity_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
				"redq_rental_availability" =>   array('label' => __('Product Availabilities', 'wc-frontend-manager') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'desc' => __( 'Please select the date range to be disabled for the product.', 'wc-frontend-manager' ), 'desc_class' => 'avail_rules_desc', 'value' => $redq_rental_availability, 'options' => array(
											"type" => array('label' => __('Type', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'custom_date' => __( 'Custom Date', 'wc-frontend-manager' )), 'class' => 'wcfm-select wcfm_ele avail_range_type redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label redq_rental' ),
											"from" => array('label' => __('From', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"to" => array('label' => __('To', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"rentable" => array('label' => __('Bookable', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'no' => __('NO', 'wc-frontend-manager') ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label' ),
											)	)
				) );
			?>
		</div>
	</div>
	<?php	
	}
	
	/**
   * Product Manage Third Party Plugins views
   */
  function wcfm_thirdparty_products_manage_views( ) {
		global $WCFM;
	  
	 require_once( $WCFM->library->views_path . 'wcfm-view-thirdparty-products-manage.php' );
	}
}