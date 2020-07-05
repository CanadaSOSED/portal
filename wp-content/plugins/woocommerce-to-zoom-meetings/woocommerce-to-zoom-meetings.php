<?php
/*
*		Plugin Name: WooCommerce to Zoom Meetings
*		Plugin URI: https://www.northernbeacheswebsites.com.au
*		Description: WooCommerce to Zoom Meetings
*		Version: 1.20
*		Author: Martin Gibson
*		Text Domain: woocommerce-to-zoom-meetings-meetings
*		Domain Path: /inc/lang/
*		Support: https://www.northernbeacheswebsites.com.au/contact
*		Licence: GPL2
*/



/**
* 
*
*
* Get plugin version number
*/
function woocommerce_to_zoom_meetings_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}
/**
* 
*
*
* Make plugin translateable
*/
add_action('plugins_loaded', 'woocommerce_to_zoom_meetings_translations');
function woocommerce_to_zoom_meetings_translations() {
	load_plugin_textdomain( 'woocommerce-to-zoom-meetings', false, dirname( plugin_basename(__FILE__) ) . '/inc/lang/' );
}
/**
* 
*
*
* Load admin style and scripts
*/
function woocommerce_to_zoom_meetings_register_admin_styles($hook){

    global $pagenow;
    
    //apply styles/scripts to plugin settings page and also edit and post pages (order overview and detail pages)
    if('admin.php' == $pagenow && $_GET['page'] == 'wc-settings' || 'post.php' == $pagenow || 'edit.php' == $pagenow){
        //scripts
        wp_enqueue_script( 'admin-script-zoom-meetings', plugins_url( '/inc/js/adminscript.js', __FILE__ ), array( 'jquery' ),woocommerce_to_zoom_meetings_get_version());
        wp_enqueue_script( 'alertify-zoom-meetings', plugins_url( '/inc/js/alertify.js', __FILE__ ), array( 'jquery' ),woocommerce_to_zoom_meetings_get_version(),true);
        // //styles
        wp_enqueue_style( 'admin-style-zoom-meetings', plugins_url( '/inc/css/adminstyle.css', __FILE__ ), array(),woocommerce_to_zoom_meetings_get_version());
        // wp_enqueue_style( 'dashicons' );
    }
    
}
add_action( 'admin_enqueue_scripts', 'woocommerce_to_zoom_meetings_register_admin_styles' );
/**
* 
*
*
* Load frontend style and scripts
*/
function woocommerce_to_zoom_meetings_register_frontend_styles() { 

    //only output on checkout page
    //check if woocommerce is active
    if(class_exists('woocommerce')){
        if(is_checkout()){
            wp_enqueue_script( 'woocommerce-to-zoom-meetings-woocommerce-checkout', plugins_url( '/inc/js/woocommerce-checkout.js', __FILE__ ), array( 'jquery' ),woocommerce_to_zoom_meetings_get_version(),true);   
        }
    }
  
}
add_action( 'wp_enqueue_scripts', 'woocommerce_to_zoom_meetings_register_frontend_styles' );
/**
* 
*
*
* add a settings link on the plugin page
*/
function woocommerce_to_zoom_meetings_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=wc-settings&tab=zoommeetings">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'woocommerce_to_zoom_meetings_add_settings_link' );
/**
* 
*
*
* add files to plugin
*/
require('inc/settings.php');
require('inc/add-meeting-registrant.php');
require('inc/authentication.php');
require('inc/meta-boxes.php');
require('inc/woocommerce-product-settings.php');
require('inc/woocommerce-checkout-form.php');
require('inc/clear-transients.php');
require('inc/products-column.php');
require('inc/completed-order-email.php');
require('inc/old-meeting-products.php');
require('inc/my-account-order-view.php');




/**
* 
*
*
* When clicking the create registrants button on the order page process the order
*/
add_action( 'wp_ajax_zoom_meetings_process_shop_order', 'woocommerce_to_zoom_meetings_process_shop_order' );
function woocommerce_to_zoom_meetings_process_shop_order(){

    $order_id = $_POST['order_id'];  
    $order = new WC_Order( $order_id );

    //call the common order processing function
    woocommerce_to_zoom_meetings_add_registrant ( $order_id ); 

}
/**
* 
*
*
* Sanitize webinar id
*/
function woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id){

    $result = trim(str_replace('-','',$meeting_id));
    $result = str_replace(' ','',$result);

    return $result;

}

/**
* 
*
*
* Run on plugin deactivate and also when ajax is run from the plugin settings
*/
// register_deactivation_hook( __FILE__, 'woocommerce_to_zoom_meetings_deactivate' );
add_action( 'wp_ajax_zoom_meetings_disconnect', 'woocommerce_to_zoom_meetings_deactivate' );
function woocommerce_to_zoom_meetings_deactivate(){

    $response = wp_remote_post( 'https://zoom.us/oauth/revoke', array(
        'headers' => array(
            'Authorization' => 'Basic '.woocommerce_to_zoom_meetings_get_authorisation(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
        'body' => 'token='.woocommerce_to_zoom_meetings_get_access_token(),
    ));

    delete_option('wc_settings_zoom_refresh_token');

    //also remove the cached data
    delete_transient('zoom_upcoming_meetings');

    // wp_die();

}

/**
* 
*
*
* Do pro update check
*/
require 'plugin-update-checker/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://northernbeacheswebsites.com.au/?update_action=get_metadata&update_slug=woocommerce-to-zoom-meetings', //Metadata URL.
    __FILE__, //Full path to the main plugin file.
    'woocommerce-to-zoom-meetings' //Plugin slug. Usually it's the same as the name of the directory.
);
//add queries to the update call
$updateChecker->addQueryArgFilter('filter_update_checks_woocommerce_to_zoom_meetings');
function filter_update_checks_woocommerce_to_zoom_meetings($queryArgs) {

    if (!empty(get_option('wc_settings_zoom_meetings_order_email')) &&  !empty(get_option('wc_settings_zoom_meetings_order_id'))) {

        $purchaseEmailAddress = get_option('wc_settings_zoom_meetings_order_email');
        $orderId = get_option('wc_settings_zoom_meetings_order_id');
        $siteUrl = get_site_url();

        if (!empty($purchaseEmailAddress) &&  !empty($orderId)) {
            $queryArgs['purchaseEmailAddress'] = $purchaseEmailAddress;
            $queryArgs['orderId'] = $orderId;
            $queryArgs['siteUrl'] = $siteUrl;
            $queryArgs['productId'] = '14470';
        }

    }

    return $queryArgs;   
}
// define the puc_request_info_result-<slug> callback 
function filter_puc_request_info_result_slug_woocommerce_to_zoom_meetings( $plugininfo, $result ) { 
    //get the message from the server and set as transient
    set_transient('woocommerce-to-zoom-meetings-update',$plugininfo->{'message'},YEAR_IN_SECONDS * 1);

    return $plugininfo; 
}; 
add_filter( "puc_request_info_result-woocommerce-to-zoom-meetings", 'filter_puc_request_info_result_slug_woocommerce_to_zoom_meetings', 10, 2 ); 

$path = plugin_basename( __FILE__ );

add_action("after_plugin_row_{$path}", function( $plugin_file, $plugin_data, $status ) {

    //get plugin settings

    if (!empty(get_option('wc_settings_zoom_meetings_order_email')) &&  !empty(get_option('wc_settings_zoom_meetings_order_id'))) {


        //get transient
        $message = get_transient('woocommerce-to-zoom-meetings-update');

        if($message !== 'Yes'){

            $purchaseLink = 'https://northernbeacheswebsites.com.au/zoom-meetings-for-woocommerce/';

            if($message == 'Incorrect Details'){
                $displayMessage = 'The Order ID and Purchase ID you entered is not correct. Please double check the details you entered to receive product updates.';    
            } elseif ($message == 'Licence Expired'){
                $displayMessage = 'Your licence has expired. Please <a href="'.$purchaseLink.'" target="_blank">purchase a new licence</a> to receive further updates for this plugin.';    
            } elseif ($message == 'Website Mismatch') {
                $displayMessage = 'This plugin has already been registered on another website using your details. Under the licence terms this plugin can only be used on one website. Please <a href="'.$purchaseLink.'" target="_blank">click here</a> to purchase an additional licence.';    
            } else {
                $displayMessage = '';    
            }

            echo '<tr class="plugin-update-tr active"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-error notice-alt"><p class="installer-q-icon">'.$displayMessage.'</p></div></td></tr>';

        }

    } else {

        echo '<tr class="plugin-update-tr active"><td colspan="3" class="plugin-update colspanchange"><div class="update-message notice inline notice-error notice-alt"><p class="installer-q-icon">Please enter your Order ID and Purchase ID in the plugin settings to receive automatics updates.</p></div></td></tr>';

    }


}, 10, 3 );






?>