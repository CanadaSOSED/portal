<?php

/**
 * Extend Admin Reports with Refer a Friend Data
 *
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGens_RAF_Report extends WC_Admin_Reports {

	/**
	 * Hook in Reports tabs.
	 *
	 * @since      2.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_admin_reports', array( $this, 'report_tab') );
		add_filter( 'wc_admin_reports_path', array( $this, 'raf_report_path'), 10, 3);
	}

	/**
	 * Add new RAF Tab
	 *
	 * @since      2.0.0
	 */
	public function report_tab($reports) {
		$reports['gens_raf'] = array(
			'title'  => __( 'Refer a Friend', 'gens-raf' ),
			'reports' => array(
				"gens_raf" => array(
					'title'       => __( 'Sales by date', 'woocommerce' ),
					'description' => '',
					'hide_title'  => true,
					'callback'    => array( __CLASS__, 'get_report' ),
				),
			),
		);
		return $reports;
	}

	/**
	 * Include Main RAF Report Class
	 *
	 * @since      2.0.0
	 */
	public function raf_report_path($path, $report_name, $class) {
		if($report_name == "gens-raf") {
			$path = WPGENS_RAF_ABSPATH .'/includes/admin/reports/class-wpgens-admin-report-class.php';
		}
		return $path;
	}

}

return new WPGens_RAF_Report();