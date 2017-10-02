<?php

/**
 * SOS Leaderboard
 *
 * Functions used for displaying leaderboard reports in admin.
 *
 * @author      SOS Development Team
 * @category    Admin
 * @package     SOS_Leaderboard/Admin/Reports
 * @version     2.0.0
 */

defined( 'ABSPATH' ) or exit;

class SOS_Leaderboard {


	const VERSION = '1.1.0';

	/** @var SOS_Leaderboard single instance of this plugin */
	protected static $instance;

		/**
		 * SOS_Leaderboard constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {


			// add styles
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );


			// any frontend actions

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

				add_action( 'admin_menu', array( $this, 'add_admin_menu_item' ) );

				// add plugin links
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );

			}

		}

		public function admin_styles(){
			wp_register_style( 'sos_leaderboard_styles', plugin_dir_url( __FILE__ ) .'/assets/css/admin.css', array(), '' );
			wp_enqueue_style( 'sos_leaderboard_styles' );
		}


		public function add_admin_menu_item() {
		    add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', array($this, 'output'), 'dashicons-chart-bar', 25 );
		}



		/**
		 * Handles output of the reports page in admin.
		 */
		public static function output() {

			echo 'derp';
			
			$reports        = self::get_reports();
			$first_tab      = array_keys( $reports );
			$current_tab    = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
			$current_report = isset( $_GET['report'] ) ? sanitize_title( $_GET['report'] ) : current( array_keys( $reports[ $current_tab ]['reports'] ) );

			include_once( dirname( __FILE__ ) . '/admin/reports/class-sos-leaderboard-report.php' );
			include_once( dirname( __FILE__ ) . '/admin/views/html-sos-leaderboard-reports.php' );
		}

		/**
		 * Returns the definitions for the reports to show in admin.
		 *
		 * @return array
		 */
		public static function get_reports() {
			$reports = array(
				'orders'     => array(
					'title'  => __( 'Orders', 'woocommerce' ),
					'reports' => array(
						"sales_by_date" => array(
							'title'       => __( 'Leaders by date', 'woocommerce' ),
							'description' => '',
							'hide_title'  => true,
							'callback'    => array( __CLASS__, 'get_report' ),
						),

						"sales_by_category" => array(
							'title'       => __( 'Leaders by type', 'woocommerce' ),
							'description' => '',
							'hide_title'  => true,
							'callback'    => array( __CLASS__, 'get_report' ),
						),
					),
				),
			);

			return $reports;
		}

		/**
		 * Get a report from our reports subfolder.
		 *
		 * @param string $name
		 */
		public static function get_report( $name ) {
			$name  = sanitize_title( str_replace( '_', '-', $name ) );
			$class = 'SOS_Report_' . str_replace( '-', '_', $name );

			include_once( apply_filters( 'wc_admin_reports_path', '/admin/reports/class-sos-leaderboard-report-' . $name . '.php', $name, $class ) );

			if ( ! class_exists( $class ) ) {
				return;
			}

			$report = new $class();
			$report->output_report();
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
				?>


				<tr>
					<th scope="row"><?php echo $i; ?></th>
					<td><?php echo $chapter; ?></td>
					<td><?php echo $orders; ?></td>
				</tr>


				<?php }

			}



		/** Helper methods ***************************************/


		/**
		 * Main SOS_Leaderboard Instance, ensures only one instance is/can be loaded
		 *
		 * @since 1.0.0
		 * @see sos_leaderboard_chart()
		 * @return SOS_Leaderboard
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}


		/**
		 * Cloning instances is forbidden due to singleton pattern.
		 *
		 * @since 1.1.0
		 */
		public function __clone() {
			/* translators: Placeholders: %s - plugin name */
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'sos-leaderboard-chart' ), 'WooCommerce New Customer Report' ), '1.1.0' );
		}


		/**
		 * Unserializing instances is forbidden due to singleton pattern.
		 *
		 * @since 1.1.0
		 */
		public function __wakeup() {
			/* translators: Placeholders: %s - plugin name */
			_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'sos-leaderboard-chart' ), 'WooCommerce New Customer Report' ), '1.1.0' );
		}

		/**
		 * Adds plugin page links
		 *
		 * @since 1.0.0
		 * @param array $links all plugin links
		 * @return array $links all plugin links + our custom links (i.e., "Settings")
		 */
		public function add_plugin_links( $links ) {

			$plugin_links = array(
				//'<a href="' . admin_url( 'admin.php?page=wc-reports&tab=customers&report=new_customers' ) . '">' . __( 'View Report', 'sos-leaderboard-chart' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}


	}