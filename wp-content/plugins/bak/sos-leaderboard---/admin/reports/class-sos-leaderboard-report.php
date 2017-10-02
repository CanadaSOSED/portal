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


class SOS_Leaderboard_Report {

	/**
	 * The start date of the report.
	 *
	 * @var int timestamp
	 */
	public $start_date;

	/**
	 * The end date of the report.
	 *
	 * @var int timestamp
	 */
	public $end_date;

	/**
	 * Get report totals such as order totals and discount amounts.
	 *
	 * @param  array $args
	 * @return mixed depending on query_type
	 */
	public function get_order_report_data( $args = array() ) {
		global $wpdb;

		$default_args = array(
			'data'                => array(),
			'where'               => array(),
			'where_meta'          => array(),
			'query_type'          => 'get_row',
			'group_by'            => '',
			'order_by'            => '',
			'limit'               => '',
			'filter_range'        => false,
			'nocache'             => false,
			'debug'               => false,
			'order_types'         => wc_get_order_types( 'reports' ),
			'order_status'        => array( 'completed', 'processing', 'on-hold' ),
			'parent_order_status' => false,
		);
		$args = apply_filters( 'woocommerce_reports_get_order_report_data_args', $args );
		$args = wp_parse_args( $args, $default_args );

		extract( $args );

		if ( empty( $data ) ) {
			return '';
		}

		$order_status = apply_filters( 'woocommerce_reports_order_statuses', $order_status );

		$query  = array();
		$select = array();

		foreach ( $data as $key => $value ) {
			$distinct = '';

			if ( isset( $value['distinct'] ) ) {
				$distinct = 'DISTINCT';
			}

			switch ( $value['type'] ) {
				case 'meta' :
					$get_key = "meta_{$key}.meta_value";
					break;
				case 'parent_meta' :
					$get_key = "parent_meta_{$key}.meta_value";
					break;
				case 'post_data' :
					$get_key = "posts.{$key}";
					break;
				case 'order_item_meta' :
					$get_key = "order_item_meta_{$key}.meta_value";
					break;
				case 'order_item' :
					$get_key = "order_items.{$key}";
					break;
				default :
					continue;
			}

			if ( $value['function'] ) {
				$get = "{$value['function']}({$distinct} {$get_key})";
			} else {
				$get = "{$distinct} {$get_key}";
			}

			$select[] = "{$get} as {$value['name']}";
		}

		$query['select'] = "SELECT " . implode( ',', $select );
		$query['from']   = "FROM {$wpdb->posts} AS posts";

		// Joins
		$joins = array();

		foreach ( ( $data + $where ) as $key => $value ) {
			$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
			$type      = isset( $value['type'] ) ? $value['type'] : false;

			switch ( $type ) {
				case 'meta' :
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON ( posts.ID = meta_{$key}.post_id AND meta_{$key}.meta_key = '{$key}' )";
					break;
				case 'parent_meta' :
					$joins[ "parent_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS parent_meta_{$key} ON (posts.post_parent = parent_meta_{$key}.post_id) AND (parent_meta_{$key}.meta_key = '{$key}')";
					break;
				case 'order_item_meta' :
					$joins["order_items"] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (posts.ID = order_items.order_id)";

					if ( ! empty( $value['order_item_type'] ) ) {
						$joins["order_items"] .= " AND (order_items.order_item_type = '{$value['order_item_type']}')";
					}

					$joins[ "order_item_meta_{$key}" ]  = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON " .
														"(order_items.order_item_id = order_item_meta_{$key}.order_item_id) " .
														" AND (order_item_meta_{$key}.meta_key = '{$key}')";
					break;
				case 'order_item' :
					$joins["order_items"] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					break;
			}
		}

		if ( ! empty( $where_meta ) ) {
			foreach ( $where_meta as $value ) {
				if ( ! is_array( $value ) ) {
					continue;
				}
				$join_type = isset( $value['join_type'] ) ? $value['join_type'] : 'INNER';
				$type      = isset( $value['type'] ) ? $value['type'] : false;
				$key       = is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'];

				if ( 'order_item_meta' === $type ) {

					$joins["order_items"] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
					$joins[ "order_item_meta_{$key}" ] = "{$join_type} JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

				} else {
					// If we have a where clause for meta, join the postmeta table
					$joins[ "meta_{$key}" ] = "{$join_type} JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
				}
			}
		}

		if ( ! empty( $parent_order_status ) ) {
			$joins["parent"] = "LEFT JOIN {$wpdb->posts} AS parent ON posts.post_parent = parent.ID";
		}

		$query['join'] = implode( ' ', $joins );

		$query['where']  = "
			WHERE 	posts.post_type 	IN ( '" . implode( "','", $order_types ) . "' )
			";

		if ( ! empty( $order_status ) ) {
			$query['where'] .= "
				AND 	posts.post_status 	IN ( 'wc-" . implode( "','wc-", $order_status ) . "')
			";
		}

		if ( ! empty( $parent_order_status ) ) {
			if ( ! empty( $order_status ) ) {
				$query['where'] .= " AND ( parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') OR parent.ID IS NULL ) ";
			} else {
				$query['where'] .= " AND parent.post_status IN ( 'wc-" . implode( "','wc-", $parent_order_status ) . "') ";
			}
		}

		if ( $filter_range ) {
			$query['where'] .= "
				AND 	posts.post_date >= '" . date( 'Y-m-d H:i:s', $this->start_date ) . "'
				AND 	posts.post_date < '" . date( 'Y-m-d H:i:s', strtotime( '+1 DAY', $this->end_date ) ) . "'
			";
		}

		if ( ! empty( $where_meta ) ) {

			$relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';

			$query['where'] .= " AND (";

			foreach ( $where_meta as $index => $value ) {

				if ( ! is_array( $value ) ) {
					continue;
				}

				$key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] . '_array' : $value['meta_key'];

				if ( strtolower( $value['operator'] ) == 'in' || strtolower( $value['operator'] ) == 'not in' ) {

					if ( is_array( $value['meta_value'] ) ) {
						$value['meta_value'] = implode( "','", $value['meta_value'] );
					}

					if ( ! empty( $value['meta_value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['meta_value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['meta_value']}'";
				}

				if ( ! empty( $where_value ) ) {
					if ( $index > 0 ) {
						$query['where'] .= ' ' . $relation;
					}

					if ( isset( $value['type'] ) && 'order_item_meta' === $value['type'] ) {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
					} else {

						if ( is_array( $value['meta_key'] ) ) {
							$query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
						} else {
							$query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";
						}

						$query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
					}
				}
			}

			$query['where'] .= ")";
		}

		if ( ! empty( $where ) ) {

			foreach ( $where as $value ) {

				if ( strtolower( $value['operator'] ) == 'in' || strtolower( $value['operator'] ) == 'not in' ) {

					if ( is_array( $value['value'] ) ) {
						$value['value'] = implode( "','", $value['value'] );
					}

					if ( ! empty( $value['value'] ) ) {
						$where_value = "{$value['operator']} ('{$value['value']}')";
					}
				} else {
					$where_value = "{$value['operator']} '{$value['value']}'";
				}

				if ( ! empty( $where_value ) ) {
					$query['where'] .= " AND {$value['key']} {$where_value}";
				}
			}
		}

		if ( $group_by ) {
			$query['group_by'] = "GROUP BY {$group_by}";
		}

		if ( $order_by ) {
			$query['order_by'] = "ORDER BY {$order_by}";
		}

		if ( $limit ) {
			$query['limit'] = "LIMIT {$limit}";
		}

		$query          = apply_filters( 'woocommerce_reports_get_order_report_query', $query );
		$query          = implode( ' ', $query );
		$query_hash     = md5( $query_type . $query );
		$cached_results = get_transient( strtolower( get_class( $this ) ) );

		if ( $debug ) {
			echo '<pre>';
			wc_print_r( $query );
			echo '</pre>';
		}

		if ( $debug || $nocache || false === $cached_results || ! isset( $cached_results[ $query_hash ] ) ) {
			// Enable big selects for reports
			$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
			$cached_results[ $query_hash ] = apply_filters( 'woocommerce_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );
			set_transient( strtolower( get_class( $this ) ), $cached_results, DAY_IN_SECONDS );
		}

		$result = $cached_results[ $query_hash ];

		return $result;
	}


	/**
	 * Get the current range and calculate the start and end dates.
	 *
	 * @param  string $current_range
	 */
	public function calculate_current_range( $current_range ) {

		switch ( $current_range ) {

			case 'custom' :

				$this->start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( $_GET['start_date'] ) ) );

				if ( empty( $_GET['end_date'] ) ) {
					$this->end_date = strtotime( 'midnight', current_time( 'timestamp' ) );
				} else {
					$this->end_date = strtotime( 'midnight', strtotime( sanitize_text_field( $_GET['end_date'] ) ) );
				}

				$interval = 0;
				$min_date = $this->start_date;

				while ( ( $min_date = strtotime( "+1 MONTH", $min_date ) ) <= $this->end_date ) {
					$interval ++;
				}

				// 3 months max for day view
				if ( $interval > 3 ) {
					$this->chart_groupby = 'month';
				} else {
					$this->chart_groupby = 'day';
				}
			break;

			case 'year' :
				$this->start_date    = strtotime( date( 'Y-01-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				
			break;

			case 'last_month' :
				$first_day_current_month = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->start_date        = strtotime( date( 'Y-m-01', strtotime( '-1 DAY', $first_day_current_month ) ) );
				$this->end_date          = strtotime( date( 'Y-m-t', strtotime( '-1 DAY', $first_day_current_month ) ) );
				
			break;

			case 'month' :
				$this->start_date    = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				
			break;

			case '7day' :
				$this->start_date    = strtotime( '-6 days', strtotime( 'midnight', current_time( 'timestamp' ) ) );
				$this->end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
				
			break;
		}
	}

	/**
	 * Output the report.
	 */
	public function output_report() {}

	/**
	 * Check nonce for current range.
	 *
	 * @since  3.0.4
	 * @param  string $current_range Current range.
	 */
	public function check_current_range_nonce( $current_range ) {
		if ( 'custom' !== $current_range ) {
			return;
		}

		if ( ! isset( $_GET['wc_reports_nonce'] ) || ! wp_verify_nonce( $_GET['wc_reports_nonce'], 'custom_range' ) ) {
			wp_safe_redirect( remove_query_arg( array( 'start_date', 'end_date', 'range', 'wc_reports_nonce' ) ) );
			exit;
		}
	}
}
