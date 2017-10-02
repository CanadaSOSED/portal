<?php
/**
 * Plugin Name: SOS Leaderboard
 * Plugin URI: 
 * Description: Don't Derp
 * Author: SOS Development Team
 * Author URI:
 * Version: 1.0.0
 * Text Domain: sos-leaderboard
 *
 *
 *
 * @package   SOS-Leaderboard-Reports
 * @author    SOS Development Team
 * @category  Admin
 *
 */

defined( 'ABSPATH' ) or exit;


class SOS_Leaderboard {

	// any frontend actions

	public function __construct() {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			add_action( 'plugins_loaded', 'sos_leaderboard' );

			// add styles
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu_item' ) );


			// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );

		}

	}

	public function admin_styles() {
		wp_register_style( 'sos_leaderboard_styles', plugin_dir_url( __FILE__ ) .'/assets/css/admin.css', array(), '' );
		wp_enqueue_style( 'sos_leaderboard_styles' );
	}

	public function add_admin_menu_item(){
		add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', array($this, 'output'), 'dashicons-chart-bar', 25 );
	}




	public function get_order_report_data( $current_range ) {

		$ranges = array(
			'year'         => __( 'Year', 'woocommerce' ),
			'last_month'   => __( 'Last month', 'woocommerce' ),
			'month'        => __( 'This month', 'woocommerce' ),
			'7day'         => __( 'Last 7 days', 'woocommerce' ),
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}


		$sites = get_sites();
		$sitesArray = array(); 
		$i = 0;

		foreach ( $sites as $site ) {

			switch_to_blog( $site->blog_id );

			$chapter_name = get_option( 'blogname' );


			switch ( $current_range ) {

				case 'custom' :

				$start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( $_GET['start_date'] ) ) );

				if ( empty( $_GET['end_date'] ) ) {
					$end_date = strtotime( 'midnight', current_time( 'timestamp' ) );
				} else {
					$end_date = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['end_date'] ) ) );
				}

				$interval = 0;
				$min_date = $start_date;

				while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $end_date ) {
					$interval ++;
				}

				break;

				case 'year' :
				$args = array(
					'post_type' => 'shop_order',
					'post_status' => 'complete',
					'date_query' => array(
						'column'  => 'post_date',
						'after' => strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) ),
						'before' => strtotime( 'midnight', current_time( 'timestamp' ) ), 
					),
				);
				break;

				case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$args = array(
					'post_type' => 'shop_order',
					'post_status' => 'complete',
					'date_query' => array(
						'column'  => 'post_date',
						'after' => strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) ),
						'before' => strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) ), 
					),
				);
				break;

				case 'month' :
				$args = array(
					'post_type' => 'shop_order',
					'post_status' => 'complete',
					'date_query' => array(
						'column'  => 'post_date',
						'after' => strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) ),
						'before' => strtotime( 'midnight', current_time( 'timestamp' ) ), 
					),
				);
				break;

				case '7day' :
				$args = array(
					'post_type' => 'shop_order',
					'post_status' => 'complete',
					'date_query' => array(
						'column'  => 'post_date',
						'after' => strtotime( '-6 days', strtotime( 'midnight', current_time( 'timestamp' ) ) ),
						'before' => strtotime( 'midnight', current_time( 'timestamp' ) ), 
					),
				);
				break;
			}


			$query = new WP_query();
			$orders = $query->found_posts; 
			$sitesArray[ $orders ] = $chapter_name;

			restore_current_blog();
		}

			krsort($sitesArray, SORT_NUMERIC);

			// Loop over Array items displaying values
			foreach ($sitesArray as $orders => $chapter) { 
			$i++;



			echo '<tr>;
				<th scope="row">'. $i .'</th>
				<td>' . $chapter . '</td>
				<td>' . $orders . '</td>
				</tr>';


			}

		}

	}

	$inst = new SOS_Leaderboard();
	echo $inst;

