<?php
/**
 * Plugin Name: SOS Leaderboard Page
 * Plugin URI: 
 * Description: Functions to display the leaderboard data
 * Version: 1.0 
 * Author: SOS Development Team
 * Author URI: 
 * License: GPL2
 *
 * Text Domain: sos-leaderboard
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



// function sos_full_leaderboard(){ 


// 	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
// 		global $woocommerce, $wpdb, $product;
// 	    include_once($woocommerce->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php');
	    
// 	    // WooCommerce Admin Report
// 	    $wc_report = new WC_Admin_Report();
	    
// 	    // Set date parameters for the current month
// 	    $start_date = strtotime(date('Y-m', current_time('timestamp')) . '-01 midnight');
// 	    $end_date = strtotime('+1month', $start_date) - 86400;
// 	    $wc_report->start_date = $start_date;
// 	    $wc_report->end_date = $end_date;
	    
// 	    // Avoid max join size error
// 	    $wpdb->query('SET SQL_BIG_SELECTS=1');
// 	    $sites = get_sites();


// 	    foreach ( $sites as $site ) {

// 	    	switch_to_blog( $site->blog_id );

// 	    // Get data for current month sold products
// 	    $sold_products = $wc_report->get_order_report_data(array(
// 	        'data' => array(
// 	            '_product_id' => array(
// 	                'type' => 'order_item_meta',
// 	                'order_item_type' => 'line_item',
// 	                'function' => '',
// 	                'name' => 'product_id'
// 	            ),
// 	            '_qty' => array(
// 	                'type' => 'order_item_meta',
// 	                'order_item_type' => 'line_item',
// 	                'function' => 'SUM',
// 	                'name' => 'quantity'
// 	            ),
// 	            '_line_subtotal' => array(
// 	                'type' => 'order_item_meta',
// 	                'order_item_type' => 'line_item',
// 	                'function' => 'SUM',
// 	                'name' => 'gross'
// 	            ),
// 	            '_line_total' => array(
// 	                'type' => 'order_item_meta',
// 	                'order_item_type' => 'line_item',
// 	                'function' => 'SUM',
// 	                'name' => 'gross_after_discount'
// 	            )
// 	        ),
// 	        'query_type' => 'get_results',
// 	        'group_by' => 'product_id',
// 	        'where_meta' => '',
// 	        'order_by' => 'quantity DESC',
// 	        'order_types' => wc_get_order_types('order_count'),
// 	        'filter_range' => TRUE,
// 	        'order_status' => array('completed'),
// 	    ));

// 	    restore_current_blog();

// 	    // List Sales Items
// 	    if (!empty($sold_products)) {echo 'derp';}
// 		}

// 		return;
// 	}

// }

// add_shortcode('sos-full-leaderboard','sos_full_leaderboard' );
		?>