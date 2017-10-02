<?php
/**
* Plugin Name: SOS Leaderboard -- derp de derp
* Plugin URI: 
* Description: Don't Derp -- derped again
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



/*
* SOS_Leaderboard_Reports Version Check.
*/
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || version_compare( get_option( 'woocommerce_db_version' ), '2.4.0', '<' ) ) {

	function sos_leaderboard_reports_outdated_version_notice() {

		$message = sprintf(
			/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
			esc_html__( '%1$sDisplays the tops performing schools by revenue and orders%2$s This plugin requires WooCommerce 2.4 or newer. Please %3$supdate WooCommerce to version 2.4 or newer%4$s.', 'sos-leaderboard-chart' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);

		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
	}

	add_action( 'admin_notices', 'sos_leaderboard_reports_outdated_version_notice' );
	return;
}


class SOS_Main_Leaderboard_Reports {


	public function __construct() {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

		// any admin actions
			add_action( 'admin_menu', 'add_admin_menu_item');

		// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'add_plugin_links');

		}

		wp_register_style( 'sos_leaderboard_styles', plugin_dir_url( __FILE__ ) .'/assets/css/admin.css', array(), '' );
		wp_enqueue_style( 'sos_leaderboard_styles' );

		add_action( 'admin_enqueue_scripts', 'admin_styles');
		add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', 'get_report', 'dashicons-chart-bar', 25 );

	}

	public function setup_reports(){
		$reports = array(
			'orders'     => array(
				'title'  => __( 'Orders', 'woocommerce' ),
				'reports' => array(
					"orders_by_date" => array(
						'title'       => __( 'Leaders by date', 'woocommerce' ),
						'description' => 'Gotta Love Descriptions',
						'hide_title'  => true,
						'callback'    => 'get_report',
					),

					"orders_by_category" => array(
						'title'       => __( 'Leaders by type', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => 'get_report',
					),
				),
			),
		);
	}

	public function display_report(){
		$first_tab      = array_keys( $this->setup_reports->$reports );
		$current_tab    = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
		$current_report = isset( $_GET['report'] ) ? sanitize_title( $_GET['report'] ) : current( array_keys( $reports[ $current_tab ]['reports'] ) );
	
		include_once( dirname( __FILE__ ) . '/views/html-sos-leaderboard-page-reports.php' );

	}

	public function get_report_data(){

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
			?>


			<tr>
				<th scope="row"><?php echo $i; ?></th>
				<td><?php echo $chapter; ?></td>
				<td><?php echo $orders; ?></td>
			</tr>


			<?php }

		}

}


/**
* Returns the One True Instance of SOS_Leaderboard_Reports
*
* @since 1.0.0
* @return SOS_Leaderboard_Reports
*/
function sos_leaderboard_reports() {
	return SOS_Main_Leaderboard_Reports::instance();
}
