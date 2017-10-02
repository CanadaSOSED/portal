<?php
/**
 * WooCommerce New Customer Report
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce New Customer Report to newer
 * versions in the future.
 *
 * @package     WC-New-Customer-Report/Includes/
 * @author      SkyVerge
 * @copyright   Copyright (c) 2016-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * New Customer Report Admin Report Class
 *
 * Handles generating and rendering the New vs Returning Customers by Date report
 *
 * @since 1.0.0
 */

if(!class_exists('WC_Admin_Report')) {
    require_once( ABSPATH . '/wp-content/plugins/woocommerce/includes/admin/reports/class-wc-admin-report.php');
}

class SOS_Leaderbord_Chart extends WC_Admin_Report {

	/** @var stdClass for caching multiple calls to get_report_data() */
	protected $report_data;

	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}
		return $this->report_data;
	}

	/**
	 * Get all data needed for this report and store in the class.
	 */
	private function query_report_data() {
		$this->report_data = new stdClass;

		$this->report_data->order_counts = (array) $this->get_order_report_data( array(
			'data' => array(
				'ID' => array(
					'type'     => 'post_data',
					'function' => 'COUNT',
					'name'     => 'count',
					'distinct' => true,
				),
				'post_date' => array(
					'type'     => 'post_data',
					'function' => '',
					'name'     => 'post_date',
				),
			),
			'group_by'            => $this->group_by_query,
			'order_by'            => 'post_date ASC',
			'query_type'          => 'get_results',
			'filter_range'        => true,
			'order_types'         => wc_get_order_types( 'order-count' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold', 'refunded' ),
		) );
	}
	/**
	 * Render the report data
	 *
	 * @since 1.0.0
	 */
	public function output_report() {

		$current_range = $this->get_current_range();

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		// used in view
		$ranges = array(
			'year'       => __( 'Year', 'woocommerce-cost-of-goods' ),
			'last_month' => __( 'Last Month', 'woocommerce-cost-of-goods' ),
			'month'      => __( 'This Month', 'woocommerce-cost-of-goods' ),
			'7day'       => __( 'Last 7 Days', 'woocommerce-cost-of-goods' )
		);

		//include( ABSPATH . '/wp-content/plugins/woocommerce/includes/admin/views/html-report-by-date.php' );

		//include( ABSPATH . '/wp-content/plugins/woocommerce/includes/admin/views/html-report-by-date.php' );
		include_once( ABSPATH . '/wp-content/plugins/sos-leaderboard/partials/sos-leaderboard-admin-display.php' );

	}


	/**
	 * Return the currently selected date range for the report
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_current_range() {
		return ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
	}





}


return sos_leaderboard_chart()->report = new SOS_Leaderbord_Chart();
