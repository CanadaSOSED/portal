<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Admin {
	var $settings_name_now = 'woocommerce-order-export-now';
	var $settings_name_cron = 'woocommerce-order-export-cron';
	var $settings_name_profiles = 'woocommerce-order-export-profiles';
	var $settings_name_actions = 'woocommerce-order-export-actions';
	var $cron_process_option = 'woocommerce-order-export-cron-do';
	var $activation_notice_option = 'woocommerce-order-export-activation-notice-shown';
	var $tempfile_prefix = 'woocommerce-order-file-';
	var $step = 30;
	public static $formats = array( 'XLS', 'CSV', 'XML', 'JSON' );
	public static $export_types = array( 'EMAIL', 'FTP', 'HTTP', 'FOLDER' );
	public $url_plugin;
	public $path_plugin;

	const EXPORT_NOW      = 'now';
	const EXPORT_PROFILE  = 'profiles';
	const EXPORT_SCHEDULE = 'cron';
	const EXPORT_ORDER_ACTION = 'order-action';

	var $methods_allowed_for_guests;

	public function __construct() {
		$this->url_plugin         = dirname( plugin_dir_url( __FILE__ ) ) . '/';
		$this->path_plugin        = dirname( plugin_dir_path( __FILE__ ) ) . '/';
		$this->path_views_default = dirname( plugin_dir_path( __FILE__ ) ) . "/view/";

		if ( is_admin() ) { // admin actions
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-order-export' ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'thematic_enqueue_scripts' ) );
				add_filter( 'script_loader_src', array( $this, 'script_loader_src' ), 10, 2 );
			}
			add_action( 'wp_ajax_order_exporter', array( $this, 'ajax_gate' ) );

			//Add custom bulk export action in Woocomerce orders Table
			add_action('admin_footer-edit.php',  array( $this,'export_orders_bulk_action'));
			add_action('load-edit.php', array( $this,'export_orders_bulk_action_process'));
			add_action('admin_notices', array( $this,'export_orders_bulk_action_notices'));
			//do once
			if( !get_option( $this->activation_notice_option ) )
				add_action('admin_notices', array( $this,'display_plugin_activated_message'));
		}
		add_filter( 'cron_schedules', array( $this, 'create_custom_schedules' ), 10, 1 );
		add_action( 'wc_export_cron_global', array( $this, 'wc_export_cron_global_f' ) );


		//for direct calls
		add_action( 'wp_ajax_order_exporter_run', array( $this, 'ajax_gate_guest' ) );
		add_action( 'wp_ajax_nopriv_order_exporter_run', array( $this, 'ajax_gate_guest' ) );
		$this->methods_allowed_for_guests = array('run_cron_jobs','run_one_job','run_one_scheduled_job');

		// order actions
		add_action( 'woocommerce_order_status_changed', array( $this, 'wc_order_status_changed' ), 10, 3);
	}


	public function install() {
		//wp_clear_scheduled_hook( "wc_export_cron_global" ); //debug
		$this->install_job();
	}

	private function install_job() {
		if ( ! wp_get_schedule( 'wc_export_cron_global' ) ) {
			wp_schedule_event( time(), 'wc_export_1min_global', 'wc_export_cron_global' );
		}
	}

	public function display_plugin_activated_message() {
		?>
		<div class="notice notice-success is-dismissible">
        <p><?php _e( 'Advanced Orders Export For WooCommerce  is available <a href="admin.php?page=wc-order-export">on this page</a>.', 'woocommerce-order-export' ); ?></p>
		</div>
		<?php
		update_option( $this->activation_notice_option, true );
	}

	public function add_action_links( $links ) {
		$mylinks  =  array(
			'<a href="admin.php?page=wc-order-export">'. __('Settings', 'woocommerce-order-export'). '</a>',
			'<a href="https://algolplus.com/plugins/documentation-order-export-woocommerce/" target="_blank">'. __('Docs', 'woocommerce-order-export'). '</a>',
			'<a href="https://algolplus.freshdesk.com" target="_blank">'. __('Support', 'woocommerce-order-export'). '</a>'
		);
		return array_merge( $mylinks, $links);
	}

	public function uninstall() {
		wp_clear_scheduled_hook( "wc_export_cron_global" );
		delete_option( $this->activation_notice_option );
	}

	function load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-order-export' );
		load_textdomain( 'woocommerce-order-export', WP_LANG_DIR . '/woocommerce-order-export/woocommerce-order-export-' . $locale . '.mo' );

		load_plugin_textdomain( 'woocommerce-order-export', false,
			plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/i18n/languages' );
	}

	public function add_menu() {
		if( apply_filters('woe_current_user_can_export', true) ) {
			if ( current_user_can( 'manage_woocommerce' ) )
				add_submenu_page( 'woocommerce', __( 'Export Orders', 'woocommerce-order-export' ),__( 'Export Orders', 'woocommerce-order-export' ), 'view_woocommerce_reports', 'wc-order-export', array( $this, 'render_menu' ) );
			else // add after Sales Report!
				add_menu_page( __( 'Export Orders', 'woocommerce-order-export' ),__( 'Export Orders', 'woocommerce-order-export' ), 'view_woocommerce_reports', 'wc-order-export', array( $this, 'render_menu' ) , null, '55.7');
		}
	}

	public function render_menu() {
		$this->render( 'main', array( 'WC_Order_Export' => $this, 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		$active_tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'export';
		if ( method_exists( $this, 'render_tab_' . $active_tab ) ) {
			$this->{'render_tab_' . $active_tab}();
		}
	}

	public function render_tab_export() {
		$this->render( 'export', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

    public function render_tab_tools() {
		$this->render( 'tools', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

    public function render_tab_help() {
		$this->render( 'help', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'WC_Order_Export' => $this ) );
	}

	public function render_tab_order_actions() {
		$wc_oe     = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';
		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$all_items = get_option( $this->settings_name_actions, array() );
		$show      = array(
			'date_filter'      => false,
			'export_button'    => false,
			'preview_actions'  => false,
			'destinations'     => true,
			'schedule'         => false,
			'sort_orders'      => false,
			'order_filters'    => true,
			'product_filters'  => true,
			'customer_filters' => true,
			'payment_filters'  => true,
			'shipping_filters' => true,
		);
		switch ( $wc_oe ) {
			case 'add_action':
				end( $all_items );
				$next_id = key( $all_items ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => self::EXPORT_ORDER_ACTION,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show
				) );

				return;
				break;
			case 'edit_action':
				$item_id = isset( $_REQUEST[ 'action_id' ] ) ? $_REQUEST[ 'action_id' ] : '';
				if ($item_id) {
					$this->render( 'settings-form', array(
						'mode'            => self::EXPORT_ORDER_ACTION,
						'id'              => $item_id,
						'WC_Order_Export' => $this,
						'ajaxurl'         => $ajaxurl,
						'show'            => $show
					) );
				}

				return;
				break;
			case 'clone':
				$item_id = isset( $_REQUEST[ 'action_id' ] ) ? $_REQUEST[ 'action_id' ] : '';
				$item_id = $this->clone_export_settings( self::EXPORT_ORDER_ACTION, $item_id );

				$url     = add_query_arg( array( 'wc_oe' => 'edit', 'action_id' => $item_id ) );
				wp_redirect( $url );
				break;
			case 'delete':
				$item_id = isset( $_REQUEST['action_id'] ) ? $_REQUEST['action_id'] : '';
				if ( $item_id ) {
					unset( $all_items[ $item_id ] );
					update_option( $this->settings_name_actions, $all_items );
				}
				break;
			case 'change_status':
				$item_id = isset( $_REQUEST['action_id'] ) ? $_REQUEST['action_id'] : '';

				if ( $item_id ) {
					$all_items[ $item_id ]['active'] = $_REQUEST['status'];
					update_option( $this->settings_name_actions, $all_items );
				}

				$url = remove_query_arg( array( 'wc_oe', 'action_id', 'status' ) );

				wp_redirect( $url );
				break;
		}
		$this->render( 'order-actions', array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this, 'tab' => 'order_actions' ) );
	}

	public function render_tab_schedules() {
		$wc_oe    = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';
		$ajaxurl  = admin_url( 'admin-ajax.php' );
		$all_jobs = get_option( $this->settings_name_cron, array() );
		switch ( $wc_oe ) {
			case 'add_schedule':
				$show = array(
					'date_filter'   => true,
					'export_button' => true,
					'destinations'  => true,
					'schedule'      => true,
				);
				end( $all_jobs );
				$next_id = key( $all_jobs ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => self::EXPORT_SCHEDULE,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show
				) );

				return;
				break;

			case 'edit_schedule':
				$schedule_id = isset( $_REQUEST[ 'schedule_id' ] ) ? $_REQUEST[ 'schedule_id' ] : '';

				$clone = isset( $_REQUEST[ 'clone' ] ) ? $_REQUEST[ 'clone' ] : '';

				if ( $clone ) {
					$schedule_id = $this->clone_export_settings( self::EXPORT_SCHEDULE, $schedule_id );
				}

				if ( $schedule_id ) {
					$show = array(
						'date_filter'   => true,
						'export_button' => true,
						'destinations'  => true,
						'schedule'      => true,
					);
					$this->render( 'settings-form', array(
						'mode'            => self::EXPORT_SCHEDULE,
						'id'              => $schedule_id,
						'WC_Order_Export' => $this,
						'ajaxurl'         => $ajaxurl,
						'show'            => $show
					) );
				}

				return;
				break;
			case 'delete_schedule':
				$schedule_id = isset( $_REQUEST['schedule_id'] ) ? $_REQUEST['schedule_id'] : '';
				if ( $schedule_id ) {
					unset( $all_jobs[ $schedule_id ] );
					update_option( $this->settings_name_cron, $all_jobs );
				}
				break;
			case 'change_status_schedule':
				$schedule_id = isset( $_REQUEST['schedule_id'] ) ? $_REQUEST['schedule_id'] : '';

				if ( $schedule_id ) {
					$all_jobs[ $schedule_id ]['active'] = $_REQUEST['status'];
					update_option( $this->settings_name_cron, $all_jobs );
				}

				$url = remove_query_arg( array( 'wc_oe', 'schedule_id', 'status' ) );

				wp_redirect( $url );
				break;
		}
		$this->render( 'schedules', array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this ) );
	}

	public function clone_export_settings( $mode, $id ) {
		return $this->advanced_clone_export_settings( $id, $mode, $mode );
	}

	public function advanced_clone_export_settings( $id, $mode_in = self::EXPORT_SCHEDULE, $mode_out = self::EXPORT_SCHEDULE ) {
		$name_in = "";
		if ( $mode_in == self::EXPORT_SCHEDULE ) {
			$name_in = $this->settings_name_cron;
		} elseif ( $mode_in == self::EXPORT_PROFILE ) {
			$name_in = $this->settings_name_profiles;
		} elseif ( $mode_in == self::EXPORT_ORDER_ACTION ) {
			$name_in = $this->settings_name_actions;
		}
		$all_jobs_in = get_option( $name_in, array() );

		//new settings
		$settings           = $all_jobs_in[ $id ];
		$settings['mode']   = $mode_out;

		if ( $mode_in !== $mode_out ) {
			$name_out = "";
			if ( $mode_out == self::EXPORT_SCHEDULE ) {
				$name_out = $this->settings_name_cron;
			} elseif ( $mode_out == self::EXPORT_PROFILE ) {
				$name_out = $this->settings_name_profiles;
			} elseif ( $mode_out == self::EXPORT_ORDER_ACTION ) {
				$name_out = $this->settings_name_actions;
			}

			$all_jobs_out = get_option( $name_out, array() );
		}
		else {
			$name_out     = $name_in;
			$all_jobs_out = $all_jobs_in;
			$settings['title'] .= " [cloned]"; //add note
		}


		if ( $mode_in === self::EXPORT_PROFILE && $mode_out === self::EXPORT_SCHEDULE) {
			if ( ! isset( $settings['destination'] ) ) {
				$settings['destination'] = array(
					'type' => 'folder',
					'path' => get_home_path(),
				);
			}

			if ( ! isset( $settings['export_rule'] ) ) {
				$settings['export_rule'] = 'last_run';
			}

			if ( ! isset( $settings['export_rule_field'] ) ) {
				$settings['export_rule_field'] = 'date';
			}

			if ( ! isset( $settings['schedule'] ) ) {
				$settings['schedule'] = array(
						'type'   => 'schedule-1',
						'run_at' => '00:00',
				);
			}

			unset( $settings['use_as_bulk'] );
		}

		end( $all_jobs_out );
		$next_id				  = key( $all_jobs_out ) + 1;
		$all_jobs_out[ $next_id ] = $settings;

		update_option( $name_out, $all_jobs_out );
		return $next_id;
	}

	public function render_tab_profiles() {
		$wc_oe    = isset( $_REQUEST['wc_oe'] ) ? $_REQUEST['wc_oe'] : '';
		$ajaxurl  = admin_url( 'admin-ajax.php' );
		$all_jobs = get_option( $this->settings_name_profiles, array() );
		switch ( $wc_oe ) {
			case 'add_profile':
				$show = array(
					'date_filter'   => true,
					'export_button' => true,
					'destinations'  => true,
					'schedule'      => false,
				);
				end( $all_jobs );
				$next_id = key( $all_jobs ) + 1;
				$this->render( 'settings-form', array(
					'mode'            => self::EXPORT_PROFILE,
					'id'              => $next_id,
					'WC_Order_Export' => $this,
					'ajaxurl'         => $ajaxurl,
					'show'            => $show
				) );

				return;
				break;

			case 'edit_profile':
				$profile_id = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';

				$clone = isset( $_REQUEST[ 'clone' ] ) ? $_REQUEST[ 'clone' ] : '';
				if ( $clone ) {
					$profile_id = $this->clone_export_settings( self::EXPORT_PROFILE, $profile_id );
				}
				if ( $profile_id ) {
					$show = array(
						'date_filter'   => true,
						'export_button' => true,
						'destinations'  => true,
						'schedule'      => false,
					);
					$this->render( 'settings-form', array(
						'mode'            => self::EXPORT_PROFILE,
						'id'              => $profile_id,
						'WC_Order_Export' => $this,
						'ajaxurl'         => $ajaxurl,
						'show'            => $show
					) );
				}

				return;
				break;
			case 'copy_profile_to_scheduled':
				$profile_id  = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';

				$schedule_id = $this->advanced_clone_export_settings( $profile_id, self::EXPORT_PROFILE, self::EXPORT_SCHEDULE );

				$url = remove_query_arg( 'profile_id' );
				$url = add_query_arg( 'tab', 'schedules', $url );
				$url = add_query_arg( 'wc_oe', 'edit_schedule', $url );
				$url = add_query_arg( 'schedule_id', $schedule_id, $url );

				wp_redirect( $url );
				break;
			case 'copy_profile_to_actions':
				$profile_id  = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';

				$schedule_id = $this->advanced_clone_export_settings( $profile_id, self::EXPORT_PROFILE, self::EXPORT_ORDER_ACTION );

				$url = remove_query_arg( 'profile_id' );
				$url = add_query_arg( 'tab', 'order_actions', $url );
				$url = add_query_arg( 'wc_oe', 'edit_action', $url );
				$url = add_query_arg( 'action_id', $schedule_id, $url );

				wp_redirect( $url );
				break;
			case 'delete_profile':
				$profile_id = isset( $_REQUEST['profile_id'] ) ? $_REQUEST['profile_id'] : '';
				if ( $profile_id ) {
					unset( $all_jobs[ $profile_id ] );
					update_option( $this->settings_name_profiles, $all_jobs );
				}
				break;
		}

		//code to copy default settings as profile
		$profiles = get_option( $this->settings_name_profiles, array() );
		$free_job = get_option( $this->settings_name_now, array() );
		if(empty( $profiles )  AND !empty( $free_job ) ) {
			$free_job['title'] = __('Copied from "Export Now"', 'woocommerce-order-export' );
			$profiles[1] = $free_job;
			update_option( $this->settings_name_profiles, $profiles);
		}

		$this->render( 'profiles', array( 'ajaxurl' => $ajaxurl, 'WC_Order_Export' => $this ) );
	}

	public function get_export_settings( $mode, $id = 0 ) {
		if ( $mode == self::EXPORT_NOW OR ! $id ) {
			$settings = get_option( $this->settings_name_now, array() );
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			$all_jobs = get_option( $this->settings_name_cron, array() );
			if ( isset( $all_jobs[ $id ] ) ) {
				$settings = $all_jobs[ $id ];
			} else {
				$settings = array();
			}
		}
		elseif ( $mode == self::EXPORT_PROFILE ) {
			$all_jobs = get_option( $this->settings_name_profiles, array() );
			if ( isset( $all_jobs[ $id ] ) ) {
				$settings = $all_jobs[ $id ];
			} else {
				$settings = array();
			}
		}
		elseif ( $mode == self::EXPORT_ORDER_ACTION ) {
			$all_jobs = get_option( $this->settings_name_actions, array() );
			if ( isset( $all_jobs[ $id ] ) ) {
				$settings = $all_jobs[ $id ];
			} else {
				$settings = array();
			}
		}

		$defaults = array(
			'mode'                                           => $mode,
			'from_status'                                    => array(),
			'to_status'                                      => array(),
			'statuses'                                       => array(),
			'from_date'                                      => '',
			'to_date'                                        => '',
			'shipping_locations'                             => array(),
			'shipping_methods'                               => array(),
			'user_roles'                                     => array(),
			'user_names'                                     => array(),
			'payment_methods'                                => array(),
			'coupons'                                        => array(),
			'order_custom_fields'                            => array(),
			'product_categories'                             => array(),
			'product_vendors'                                => array(),
			'products'                                       => array(),
			'product_taxonomies'                             => array(),
			'product_custom_fields'                          => array(),
			'product_attributes'                             => array(),
            'product_itemmeta'                               => array(),
			'format'                                         => 'XLS',
			'format_xls_use_xls_format'		       			 => 0,
			'format_xls_display_column_names'                => 1,
			'format_xls_auto_width'				             => 1,
			'format_xls_populate_other_columns_product_rows' => 1,
			'format_csv_enclosure'                           => '"',
			'format_csv_delimiter'                           => ',',
			'format_csv_linebreak'                           => '\r\n',
			'format_csv_display_column_names'                => 1,
			'format_csv_add_utf8_bom'                        => 0,
			'format_csv_populate_other_columns_product_rows' => 1,
			'format_xml_root_tag'                            => 'Orders',
			'format_xml_order_tag'                           => 'Order',
			'format_xml_product_tag'                         => 'Product',
			'format_xml_coupon_tag'                          => 'Coupon',
			'format_xml_prepend_raw_xml'                     => '',
			'format_xml_append_raw_xml'                      => '',
			'all_products_from_order'                        => 1,
			'skip_suborders' 	              		         => 0,
			'export_refunds' 	              		         => 0,
			'date_format' 									 => 'Y-m-d',
			'time_format' 									 => 'H:i',
			'sort_direction'                                 => 'DESC',
		);

		if ( ! isset( $settings['format'] ) ) {
			$settings['format'] = 'XLS';
		}

		if ( ! isset( $settings['order_fields'] ) ) {
			$settings['order_fields'] = array();
		}
		$this->merge_settings_and_default( $settings['order_fields'], WC_Order_Export_Data_Extractor::get_order_fields( $settings['format'] ) );

		if ( ! isset( $settings['order_product_fields'] ) ) {
			$settings['order_product_fields'] = array();
		}
		$this->merge_settings_and_default( $settings['order_product_fields'], WC_Order_Export_Data_Extractor::get_order_product_fields( $settings['format'] ) );

		if ( ! isset( $settings['order_coupon_fields'] ) ) {
			$settings['order_coupon_fields'] = array();
		}
		$this->merge_settings_and_default( $settings['order_coupon_fields'], WC_Order_Export_Data_Extractor::get_order_coupon_fields( $settings['format'] ) );

		return array_merge( $defaults, $settings );
	}

	private function merge_settings_and_default(&$opt, $defaults) {
		foreach( $defaults as $k=>$v ) {
			//set default attribute OR add to option
			if( isset($opt[$k]) ) {
				if( isset($v['default']) )
					$opt[$k]['default'] = $v['default'];
			}
			else
				$opt[$k] = $v;
		}
	}

	public function save_export_settings( $mode, $id, $options ) {
		if ( $mode == self::EXPORT_NOW ) {
			update_option( $this->settings_name_now, $options );
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			$all_jobs = get_option( $this->settings_name_cron, array() );
			if ( $id ) {
				$options['schedule']['last_run'] = current_time("timestamp",0);//$all_jobs[ $id ]['schedule']['last_run'];
				$options['schedule']['next_run'] = self::next_event_timestamp_for_schedule( $options['schedule'] );
				$all_jobs[ $id ]                 = $options;
			} else {
				$options['schedule']['last_run'] = current_time("timestamp",0);
				$options['schedule']['next_run'] = self::next_event_timestamp_for_schedule( $options['schedule'] );
				$all_jobs[]                      = $options; // new job
			}

			update_option( $this->settings_name_cron, $all_jobs );
			$this->install_job();
		} elseif ( $mode == self::EXPORT_PROFILE ) {
			$all_jobs = get_option( $this->settings_name_profiles, array() );
			if ( $id ) {
				$all_jobs[ $id ]				 = $options;
			} else {
				$all_jobs[]						 = $options; // new job
			}

			update_option( $this->settings_name_profiles, $all_jobs );
		} elseif ( $mode == self::EXPORT_ORDER_ACTION ) {
			$all_jobs = get_option( $this->settings_name_actions, array() );
			if ( $id ) {
				$all_jobs[ $id ]				 = $options;
			} else {
				$all_jobs[]						 = $options; // new job
			}

			update_option( $this->settings_name_actions, $all_jobs );
		}

		return $id;
	}

	public function thematic_enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'jquery-style',
			'//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
		//wp_enqueue_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.min.js', array( 'jquery' ), '3.5.2' );
		wp_enqueue_script( 'select22', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js',
			array( 'jquery' ), '4.0.3' );
		//wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
			array(), WC_VERSION );
		wp_enqueue_script( 'export', $this->url_plugin . 'assets/js/export.js' );
		wp_enqueue_style( 'export', $this->url_plugin . 'assets/css/export.css' );
	}

	public function script_loader_src($src, $handle) {
		// don't load ANY select2.js / select2.min.js  and OUTDATED select2.full.js
		if (!preg_match('/\/select2\.full\.js\?ver=[1-3]/', $src) && !preg_match('/\/select2\.min\.js/', $src) && !preg_match('/\/select2\.js/', $src) ) {
			return $src;
		}
	}

	public function render( $view, $params = array(), $path_views = null ) {

		extract( $params );
		if ( $path_views ) {
			include $path_views . "$view.php";
		} else {
			include $this->path_views_default . "$view.php";
		}
	}

	public function get_value( $arr, $name ) {
		$arr_name = explode( ']', $name );
		$arr_name = array_map( function ( $name ) {
			if ( substr( $name, 0, 1 ) == '[' ) {
				$name = substr( $name, 1 );
			}

			return trim( $name );
		}, $arr_name );
		$arr_name = array_filter( $arr_name );

		foreach ( $arr_name as $value ) {
			$arr = isset( $arr[ $value ] ) ? $arr[ $value ] : "";
		}

		return $arr;
	}

	// AJAX part
	// calls ajax_action_XXXX
	public function ajax_gate() {
		if ( isset( $_REQUEST['method'] ) ) {
			$method = 'ajax_action_' . $_REQUEST['method'];
			if ( method_exists( $this, $method ) ) {
				$this->$method();
			}
		}
		die();
	}

	public function ajax_gate_guest() {
		if ( isset( $_REQUEST['method'] )  AND in_array($_REQUEST['method'],$this->methods_allowed_for_guests) ) {
			$method = $_REQUEST['method'];
			if ( method_exists( $this, $method ) ) {
				$this->$method();
			}
		}
		die();
	}
	public function run_cron_jobs() {
		$this->wc_export_cron_global_f();
		echo "ALL jobs completed";
	}
	public function run_one_job() {
		if( !empty( $_REQUEST[ 'schedule' ] ) )
			$settings = $this->get_export_settings( self::EXPORT_SCHEDULE, $_REQUEST[ 'schedule' ]);
		elseif($_REQUEST[ 'profile' ] == 'now')
			$settings	 = get_option( $this->settings_name_now, array() );
		else
			$settings = $this->get_export_settings( self::EXPORT_PROFILE, $_REQUEST[ 'profile' ]);
		$filename = WC_Order_Export_Engine::build_file_full( $settings );
		if( $settings[ 'format' ] == 'XLS' AND !$settings[ 'format_xls_use_xls_format' ] )
			$settings[ 'format' ] = 'XLSX';
		$this->send_headers( $settings[ 'format' ], WC_Order_Export_Engine::make_filename( $settings['export_filename'] ) );
		readfile( $filename );
		unlink( $filename );
	}

	//force scheduled job!
	public function run_one_scheduled_job() {
		$job_id = @$_REQUEST[ 'schedule' ];
		if( empty( $job_id  ) )
			die( 'schedule missed' );

		$item = $this->get_export_settings( self::EXPORT_SCHEDULE, $job_id );
		$item  = apply_filters( 'woe_adjust_cron_job_settings_before_run', $item );
		$active =  ( ! isset( $item['active'] ) || $item['active'] );
		if( !$active )
			die( 'job is inactive' );

		// do cron job
		$result = WC_Order_Export_Engine::build_files_and_export( $item );

		// write last_run time back!
		$item['schedule']['last_run'] = current_time("timestamp",0);
		$this->save_export_settings( self::EXPORT_SCHEDULE, $job_id, $item );
		echo "Job #{$job_id} completed";
	}



	private function make_new_settings( $in ) {
		$in           = stripslashes_deep( $in );
		$new_settings = $in['settings'];

		// UI don't pass empty multiselects
		$multiselects = array(
			'from_status',
			'to_status',
			'statuses',
			'order_custom_fields',
			'product_custom_fields',
			'product_categories',
			'product_vendors',
			'products',
			'shipping_locations',
			'shipping_methods',
			'user_roles',
			'user_names',
			'coupons',
			'payment_methods',
			'product_attributes',
            'product_itemmeta',
			'product_taxonomies'
		);
		foreach ( $multiselects as $m_select ) {
			if ( ! isset( $new_settings[ $m_select ] ) ) {
				$new_settings[ $m_select ] = array();
			}
		}

		$settings = $this->get_export_settings( $in['mode'], $in['id'] );
		// setup new values for same keys
		foreach ( $new_settings as $key => $val ) {
			$settings[ $key ] = $val;
		}

		$sections = array(
			'orders'   => 'order_fields',
			'products' => 'order_product_fields',
			'coupons'  => 'order_coupon_fields'
		);
		foreach ( $sections as $section => $fieldset ) {
			$new_order_fields = array();
			$in_sec           = $in[ $section ];
//var_dump($in_sec['colname']);
			if ( $in_sec['colname'] ) {
				foreach ( $in_sec['colname'] as $field => $colname ) {
					$opts = array(
						"checked" => $in_sec['exported'][ $field ],
						"colname" => $colname,
						"label"   => $in_sec['label'][ $field ]
					);
					// for products & coupons
					if ( isset( $in_sec['repeat'][ $field ] ) ) {
						$opts["repeat"] = $in_sec['repeat'][ $field ];
					}
					if ( isset( $in_sec['max_cols'][ $field ] ) ) {
						$opts["max_cols"] = $in_sec['max_cols'][ $field ];
					}
					//for orders
					if ( isset( $in_sec['segment'][ $field ] ) ) {
						$opts["segment"] = $in_sec['segment'][ $field ];
					}
					//for static fields
					if ( isset( $in_sec['value'][ $field ] ) ) {
						$opts["value"] = $in_sec['value'][ $field ];
					}
					$new_order_fields[ $field ] = $opts;
				}
			}

			$settings[ $fieldset ] = $new_order_fields;
		}

		return $settings;
	}

	//hook for TESTs
	public function ajax_action_test_all_crons() {
		set_time_limit(0);
		do_action( 'woe_start_cron_jobs' );
		$items = get_option( 'woocommerce-order-export-cron', array() );
		foreach ( $items as $key => &$item ) {
				// do cron job
				do_action( 'woe_start_cron_job', $key, $item );
				$item  = apply_filters( 'woe_adjust_cron_job_settings', $item );
				$item  = apply_filters( 'woe_adjust_cron_job_settings_before_run', $item );
				$result = WC_Order_Export_Engine::build_files_and_export( $item );
				echo $result;
		}
	}


	public function ajax_action_save_settings() {
		$settings = $this->make_new_settings( $_POST );
		//print_r(array($_POST['mode'], $_POST['id'], $settings));
		$this->save_export_settings( $_POST['mode'], $_POST['id'], $settings );
		//_e("Settings Updated", 'woocommerce-order-export');
	}

    public function ajax_action_save_tools() {
		$tools = json_decode(stripslashes($_POST['tools-import']), true);
        if ($tools) {
			$allowed_options  =  array(
				$this->settings_name_now,
				$this->settings_name_cron,
				$this->settings_name_profiles,
				$this->settings_name_actions,
			);
            foreach ($allowed_options as $key) {
                if ( isset($tools[$key]) ) {
                    update_option( $key, $tools[$key] );
                }
            }
        }
	}

	public function ajax_action_get_products() {
		global $wpdb;
		$like     = $wpdb->esc_like( $_REQUEST['q'] );
		$query    = "
                SELECT      post.ID as id,post.post_title as text,att.ID as photo_id,att.guid as photo_url
                FROM        " . $wpdb->posts . " as post
                LEFT JOIN  " . $wpdb->posts . " AS att ON post.ID=att.post_parent AND att.post_type='attachment'
                WHERE       post.post_title LIKE '%{$like}%'
                AND         post.post_type = 'product'
                AND         post.post_status <> 'trash'
                GROUP BY    post.ID
                ORDER BY    post.post_title
                LIMIT 0,5
                ";
		$products = $wpdb->get_results( $query );
		foreach ( $products as $key => $product ) {
			if ( $product->photo_id ) {
				$photo                       = wp_get_attachment_image_src( $product->photo_id, 'thumbnail' );
				$products[ $key ]->photo_url = $photo[0];
			}
			else
				unset( $products[ $key ]->photo_url );
		}
		echo json_encode( $products );
	}

	public function ajax_action_get_users() {
		global $wpdb;
		$ret = array();

		$like  = '*' . $wpdb->esc_like( $_REQUEST['q'] ) . '*';
		$users = get_users( array( 'search' => $like ) );

		foreach ( $users as $key => $user ) {
			$ret[] = array(
					'id'   => $user->ID,
					'text' => $user->display_name
			);
		}
		echo json_encode( $ret );
	}

	public function ajax_action_get_coupons() {
		global $wpdb;

		$like  = $wpdb->esc_like( $_REQUEST['q'] );
		$query = "
                SELECT      post.post_title as id, post.post_title as text
                FROM        " . $wpdb->posts . " as post
                WHERE       post.post_title LIKE '%{$like}%'
                AND         post.post_type = 'shop_coupon'
                AND         post.post_status <> 'trash'
                ORDER BY    post.post_title
                LIMIT 0,10
        ";
		$ret = $wpdb->get_results( $query );

		echo json_encode( $ret );
	}

	public function ajax_action_get_used_custom_order_meta() {
		$settings = $this->make_new_settings( $_POST );
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );

		$ret = WC_Order_Export_Data_Extractor::get_all_order_custom_meta_fields( $sql );
		echo json_encode( $ret );
	}

	public function ajax_action_get_used_custom_products_meta() {
		$settings = $this->make_new_settings( $_POST );
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );

		$ret = WC_Order_Export_Data_Extractor::get_all_product_custom_meta_fields_for_orders( $sql );
		echo json_encode( $ret );
	}

	public function ajax_action_get_used_custom_coupons_meta() {
		$ret = array();

		echo json_encode( $ret );
	}

	public function ajax_action_get_categories() {
		$cat = array();
		foreach (
			get_terms( 'product_cat',
				'hide_empty=0&hierarchical=1&name__like=' . $_REQUEST['q'] . '&number=10' ) as $term
		) {
			$cat[] = array( "id" => $term->term_id, "text" => $term->name );
		}
		echo json_encode( $cat );
	}

	public function ajax_action_get_vendors() {
		$this->ajax_action_get_users();
	}

	public function ajax_action_test_destination() {
		$settings = $this->make_new_settings( $_POST );
		// use unsaved settings

		do_action( 'woe_start_test_job', $_POST['id'], $settings );

		$result = WC_Order_Export_Engine::build_files_and_export( $settings, '', 1 );
		echo $result;
	}

	public function ajax_action_preview() {
		$settings = $this->make_new_settings( $_POST );
		// use unsaved settings

		do_action( 'woe_start_preview_job', $_POST['id'], $settings );

		WC_Order_Export_Engine::build_file( $settings, 'preview', 'browser', 0, $_POST['limit'] );
	}

	public function ajax_action_estimate() {
		$settings = $this->make_new_settings( $_POST );
		// use unsaved settings

		$total = WC_Order_Export_Engine::build_file( $settings, 'estimate', 'file', 0, 0, 'test' );

		echo json_encode( array( 'total' => $total ) );
	}

	public function ajax_action_get_order_custom_fields_values() {
		global $wpdb;
		$values  = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s    AND post_id IN (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'shop_order' )" , $_POST['cf_name'] ) );
		sort( $values );
		echo json_encode( $values );
	}

	public function ajax_action_get_product_custom_fields_values() {
		global $wpdb;
		$values  = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s    AND post_id IN (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'product_variation' OR post_type = 'product')" , $_POST['cf_name'] ) );
		sort( $values );
		echo json_encode( $values );
	}

	public function ajax_action_get_products_attributes_values() {

		$data = false;

		$attrs = wc_get_attribute_taxonomies();

		foreach ( $attrs as $item ) {
			if ( $item->attribute_label == $_POST['attr'] && $item->attribute_type != 'select' ) {
				break;
			} elseif ( $item->attribute_label == $_POST['attr'] ) {

				$name = wc_attribute_taxonomy_name( $item->attribute_name );

				$values = get_terms( $name, array( 'hide_empty' => false ) );
				if ( is_array( $values ) ) {
					$data = array_map( function ( $elem ) {
						return $elem->slug;
					}, $values );
				} else {
					$data = array();
				}
				break;
			}
		}
		echo json_encode( $data );
	}

    public function ajax_action_get_products_itemmeta_values() {
        global $wpdb;

        $meta_key_ent = htmlentities($_POST['item']);
		$metas = $wpdb->get_col( $wpdb->prepare("SELECT DISTINCT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta where meta_key = '%s' OR meta_key='%s'", $_POST['item'], $meta_key_ent ));

		echo json_encode( $metas );
	}

	public function ajax_action_get_products_shipping_values() {

		global $wpdb;

		$data = false;

		$query   = $wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s',
			array( '_shipping_' . strtolower( $_POST['item'] ) ) );
		$results = $wpdb->get_results( $query );
		$data    = array_filter( array_unique( array_map( function ( $elem ) {
			return $elem->meta_value;
		}, $results ) ), function ( $elem ) {
			return ! empty( $elem );
		} );

		echo json_encode( $data );
	}

	public function send_headers( $format, $download_name = '') {
		while ( @ob_end_clean() ) {
		}; // remove ob_xx
		switch ( $format ) {
			case 'XLSX':
				if( empty( $download_name ) )
				  $download_name = "orders.xlsx";
				header( 'Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				break;
			case 'XLS':
				if( empty( $download_name ) )
				  $download_name = "orders.xls";
				header( 'Content-type: application/vnd.ms-excel; charset=utf-8' );
				break;
			case 'CSV':
				if( empty( $download_name ) )
				  $download_name = "orders.csv";
				header( 'Content-type: text/csv' );
				break;
			case 'JSON':
				if( empty( $download_name ) )
				  $download_name = "orders.json";
				header( 'Content-type: application/json' );
				break;
			case 'XML':
				if( empty( $download_name ) )
				  $download_name = "orders.xml";
				header( 'Content-type: text/xml' );
				break;
		}
		header( 'Content-Disposition: attachment; filename="' . $download_name  .'"' );
	}

	public function start_prevent_object_cache() {
		global $_wp_using_ext_object_cache;

		$this->_wp_using_ext_object_cache_previous	 = $_wp_using_ext_object_cache;
		$_wp_using_ext_object_cache					 = false;
	}

	public function stop_prevent_object_cache() {
		global $_wp_using_ext_object_cache;

		$_wp_using_ext_object_cache = $this->_wp_using_ext_object_cache_previous;
	}

	public function ajax_action_export_start() {
		$this->start_prevent_object_cache();
		$settings = $this->make_new_settings( $_POST );

		$filename = WC_Order_Export_Engine::tempnam( sys_get_temp_dir(), "orders" );
		if( !$filename ) {
			die( __( 'Can not create temporary file', 'woocommerce-order-export' ) ) ;
		}

		file_put_contents( $filename, '' );

		do_action( 'woe_start_export_job', $_POST['id'], $settings );

		$total = WC_Order_Export_Engine::build_file( $settings, 'estimate', 'file', 0, 0, $filename );
		$file_id = current_time( 'timestamp' );
		set_transient( $this->tempfile_prefix . $file_id, $filename, 60 );
		$this->stop_prevent_object_cache();
		echo json_encode( array( 'total' => $total, 'file_id' => $file_id ) );
	}

	private function get_temp_file_name() {
		$this->start_prevent_object_cache();
		$filename = get_transient( $this->tempfile_prefix . $_REQUEST['file_id'] );
		if ( $filename === false ) {
			echo json_encode( array( 'error' => __( 'Can not find exported file', 'woocommerce-order-export' ) ) );
			die();
		}
		set_transient( $this->tempfile_prefix . $_REQUEST['file_id'], $filename, 60 );
		$this->stop_prevent_object_cache();
		return $filename;
	}

	private function delete_temp_file() {
		$this->start_prevent_object_cache();
		$filename = get_transient( $this->tempfile_prefix . $_REQUEST['file_id'] );
		if ( $filename !== false ) {
			delete_transient( $this->tempfile_prefix . $_REQUEST['file_id'] );
			unlink($filename);
		}
		$this->stop_prevent_object_cache();
	}

	public function ajax_action_cancel_export() {
		$this->delete_temp_file();

		echo json_encode( array() );
	}

	public function ajax_action_export_part() {
		$settings = $this->make_new_settings( $_POST );

		WC_Order_Export_Engine::build_file( $settings, 'partial', 'file', intval( $_POST['start'] ), $this->step,
			$this->get_temp_file_name() );
		echo json_encode( array( 'start' => $_POST['start'] + $this->step ) );
	}

	public function ajax_action_export_finish() {
		$settings = $this->make_new_settings( $_POST );
		WC_Order_Export_Engine::build_file( $settings, 'finish', 'file', 0, 0, $this->get_temp_file_name() );

		$filename = WC_Order_Export_Engine::make_filename( $settings['export_filename'] );
		set_transient( $this->tempfile_prefix . 'download_filename', $filename, 60 );
		echo json_encode( array( 'done' => true ) );
	}

	public function ajax_action_export_download() {
		$this->start_prevent_object_cache();
		$format   = basename( $_GET['format'] );
		$filename = $this->get_temp_file_name();
		delete_transient( $this->tempfile_prefix . $_GET['file_id'] );

		$download_name = get_transient( $this->tempfile_prefix . 'download_filename');
		$this->send_headers( $format ,$download_name);
		readfile( $filename );
		unlink( $filename );
		$this->stop_prevent_object_cache();
	}

	public function ajax_action_plain_export() {
		parse_str($_POST['settings'], $_POST['settings']);
		$_POST['settings']['mode'] = $_POST['mode'];
		$_POST['settings']['id']   = $_POST['id'];
		$settings = $this->make_new_settings( $_POST['settings'] );
		// use unsaved settings

		do_action( 'woe_start_export_job', $_POST['id'], $settings );

		// custom export worked for plain
		if( apply_filters( 'woe_plain_export_custom_func', false, $_POST['id'], $settings ) )
			return ;

		$file = WC_Order_Export_Engine::build_file_full( $settings );

		if ( $file !== false ) {
			$file_id = current_time( 'timestamp' );
			$this->start_prevent_object_cache();
			set_transient( $this->tempfile_prefix . $file_id, $file, 600 );
			$this->stop_prevent_object_cache();

			if( $settings[ 'format' ] == 'XLS' AND !$settings[ 'format_xls_use_xls_format' ] )
				$settings[ 'format' ] = 'XLSX';

			$_GET['format']  = $settings[ 'format' ];
			$_GET['file_id'] = $_REQUEST['file_id'] = $file_id;

			$filename = WC_Order_Export_Engine::make_filename( $settings['export_filename'] );
			set_transient( $this->tempfile_prefix . 'download_filename', $filename, 60 );

			$this->ajax_action_export_download();
		}
	}

	public function wc_order_status_changed( $order_id, $old_status, $new_status ) {
		$all_items = get_option( $this->settings_name_actions, array() );
		if ( empty( $all_items ) ) {
			return;
		}
		$old_status = "wc-{$old_status}";
		$new_status = "wc-{$new_status}";

		$callback = function( $where, $settings ) use( $order_id ) {
			$where[] = "orders.ID = {$order_id}";
			return $where;
		};
		add_filter( 'woe_sql_get_order_ids_where', $callback, 10, 2 );
		
		$logger = function_exists( "wc_get_logger" ) ? wc_get_logger() : false; //new logger in 3.0+
		$logger_context = array( 'source' => 'woocommerce-order-export' );

		foreach ( $all_items as $key=>$item ) {
			if ( isset( $item['active'] ) && ! $item['active'] ) {
				continue;
			}
			// use empty for ANY status
			if ( ( empty( $item['from_status'] ) OR  in_array( $old_status, $item['from_status'] ) )
			     AND
			     ( empty( $item['to_status'] ) OR in_array( $new_status, $item['to_status'] ) )
				) {
				do_action('woe_order_action_started', $order_id, $item );
				$result = WC_Order_Export_Engine::build_files_and_export( $item );
				$output = sprintf( __('Order change rule # %s. Result: %s', 'woocommerce-order-export' ), $key, $result);
				// log if required
				if( $logger AND !empty($item['log_results']) ) 
					$logger->info( $output, $logger_context );
				
				do_action('woe_order_action_completed', $order_id,  $item , $result );
			}
		}
		remove_filter( 'woe_sql_get_order_ids_where', $callback, 10 );
	}

	public function create_custom_schedules( $schedules ) {

		$schedules['wc_export_5min_global'] = array(
			'interval' => 300,
			'display'  => 'Every 5 Minutes[exporter]'
		);
		$schedules['wc_export_1min_global'] = array(
			'interval' => 60,
			'display'  => 'Every 1 Minute[exporter]'
		);

		return $schedules;
	}

	public function wc_export_cron_global_f() {
		$export_now = get_transient( $this->cron_process_option );
		if ( $export_now ) {
			return;
		} else {
			set_transient( $this->cron_process_option, 1, 60 );
		}
		$time = current_time("timestamp",0);

		set_time_limit(0);
		do_action( 'woe_start_cron_jobs' );
		
		$logger = function_exists( "wc_get_logger" ) ? wc_get_logger() : false; //new logger in 3.0+
		$logger_context = array( 'source' => 'woocommerce-order-export' );

		$items = get_option( 'woocommerce-order-export-cron', array() );
		foreach ( $items as $key => &$item ) {
			if ( isset( $item['active'] ) && ! $item['active'] ) {
				continue;
			}
			if ( ! isset( $item['mode'] ) ) {
				$item['mode'] = self::EXPORT_SCHEDULE;
			}

			do_action( 'woe_start_cron_job', $key, $item );
			$item  = apply_filters( 'woe_adjust_cron_job_settings', $item );
			if ( !empty( $item['schedule']['next_run'] ) && $item['schedule']['next_run'] <= $time ) {
			//if ( true) {
				$item  = apply_filters( 'woe_adjust_cron_job_settings_before_run', $item );
				// do cron job
				$result = WC_Order_Export_Engine::build_files_and_export( $item );
				$output = sprintf( __('Scheduled job # %s. Result: %s', 'woocommerce-order-export' ), $key, $result);
				echo $output."<br>\n";
				// log if required
				if( $logger AND !empty($item['log_results']) ) 
					$logger->info( $output, $logger_context );
					
				$item['schedule']['last_run'] = $time;
				$item['schedule']['next_run'] = self::next_event_timestamp_for_schedule( $item['schedule'] );
			}
		}
		unset( $item );

		update_option( 'woocommerce-order-export-cron', $items );
		delete_transient( $this->cron_process_option );
	}

	public static function next_event_timestamp_for_schedule( $schedule ) {
		if ( $schedule['type'] == 'schedule-1' ) {
			if( !isset( $schedule['weekday'] ) ) // nothing selected!
				$schedule['weekday'] = array();
			return self::next_event_for_schedule_weekday( array_keys( $schedule['weekday'] ), $schedule['run_at'],
				true );
		} else {
			return self::next_event_for_schedule2( $schedule['interval'], $schedule['custom_interval'], true,
				$schedule['last_run'] );
		}
	}

	public static function next_event_for_schedule_weekday( $weekdays, $runat, $timestamp = false ) {
		$now = current_time("timestamp");
		$diff_utc = current_time("timestamp") - current_time("timestamp",1);
		$times = array();
		for ( $index = 0; $index <= 7; $index ++ ) {
			if ( in_array( date( "D", strtotime( "+{$index} day" , $now ) ), $weekdays ) ) {
				$time = strtotime( date( "M j Y", strtotime( "+{$index} day" , $now ) ) . " " . $runat );
				if ( $time >= $now ) {
					$times[] = $time;
				}
			}
		}
		$time = $times ? min( $times ) : 0;

		if ( $timestamp ) {
			return $time;
		} else {
			return date( "D M j Y", $time ) . " at " . $runat;
		}
	}

	public static function next_event_for_schedule2( $interval, $custom_interval, $timestamp = false, $now = null ) {
		$now = empty( $now ) ? current_time( "timestamp", 0 ) : $now;
		if ( $interval == 'first_day_month' ) {
			$next_month	 = strtotime(" +1 month" ,$now);
			$month_start = date( 'Y-m-01', $next_month );
			$time		 = strtotime( $month_start );
		} elseif ( $interval == 'first_day_quarter' ) {
			$next_quarter    = strtotime("+3 month",$now);
			$quarter_start = date( 'Y-'. WC_Order_Export_Data_Extractor::get_quarter_month($next_quarter).'-01', $next_quarter );
			$time		 = strtotime( $quarter_start );
		} elseif ( $interval != 'custom' ) {
			$schedules = wp_get_schedules();
			foreach ( $schedules as $k => $v ) {
				if ( $interval == $k ) {
					if( isset( $v[ 'calc_method' ] ) ) {
						$v[ 'interval' ] = call_user_func($v[ 'calc_method' ], $v[ 'interval' ]);
					}
					$time = strtotime( '+' . $v[ 'interval' ] . ' seconds', $now );
					break;
				}
			}
		} else {
			$time = strtotime( '+' . $custom_interval * 60 . ' seconds', $now );
		}

		if ( $timestamp ) {
			return $time;
		} else {
			return date( "M j Y", $time ) . ' at ' . date( "G:i", $time );
		}
	}

	function export_orders_bulk_action() {

		global $post_type;

		$settings = get_option( $this->settings_name_now, array( 'format' => '') );

		if ( $post_type == 'shop_order' ) {
			if ( $settings['format'] == 'XLS' AND ! $settings['format_xls_use_xls_format'] ) {
				$settings['format'] = 'XLSX';
			}

			$items   = array();

			if( ! empty($settings['format']) ) {
				$items[] = array(
						'label' => sprintf( __( 'Export as %s', 'woocommerce-order-export' ), $settings['format'] ),
						'value' => 'woe_export_selected_orders'
				);
			}

			$all_jobs = get_option( $this->settings_name_profiles, array() );
			foreach ( $all_jobs as $job_id => $job ) {
				if ( isset( $job['use_as_bulk'] ) ) {
					$items[] = array(
							'label' => sprintf( __( "Export as profile '%s'", 'woocommerce-order-export' ), $job['title'] ),
							'value' => 'woe_export_selected_orders_profile_' . $job_id
					);
				}
			}
			?>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					<?php foreach($items as $item): ?>
					jQuery('<option>').val('<?php echo $item['value'] ?>').text("<?php echo $item['label'] ?>").appendTo("select[name='action']");
					jQuery('<option>').val('<?php echo $item['value'] ?>').text("<?php echo $item['label'] ?>").appendTo("select[name='action2']");
					<?php endforeach ?>
				});
			</script>
			<?php
		}
	}

	function export_orders_bulk_action_process() {
		global $sendback;

		$wp_list_table	 = _get_list_table( 'WP_Posts_List_Table' );
		$action			 = $wp_list_table->current_action();




		switch ( $action ) {
			case 'woe_export_selected_orders':

				if (!isset($_REQUEST[ 'post' ]))
					return;

				$post_ids	 = $_REQUEST[ 'post' ];
				$sendback	 = $_REQUEST[ '_wp_http_referer' ];
				$sendback = add_query_arg( array( 'export_bulk_profile' => 'now', 'ids' => join( ',', $post_ids ) ), $sendback );
				break;
			default:
				if ( preg_match( '/woe_export_selected_orders_profile_(\d+)/', $action, $matches ) ) {

					if ( isset( $matches[1] ) ) {
						$id = $matches[1];
					}
					else {
						return;
					}

					if ( ! isset($_REQUEST[ 'post' ] ) )
						return;


					$post_ids	 = $_REQUEST[ 'post' ];
					$sendback	 = $_REQUEST[ '_wp_http_referer' ];
					$sendback = add_query_arg( array( 'export_bulk_profile' => $id, 'ids' => join( ',', $post_ids ) ), $sendback );
					break;
				}
				return;
		}

		wp_redirect( $sendback );

		exit();
	}

	function ajax_action_export_download_bulk_file() {
		if($_REQUEST[ 'export_bulk_profile' ] == 'now')
			$settings	 = get_option( $this->settings_name_now, array() );
		else
			$settings = $this->get_export_settings( self::EXPORT_PROFILE, $_REQUEST[ 'export_bulk_profile' ]);
		$filename = WC_Order_Export_Engine::build_file_full( $settings, '', 0, explode(",",$_REQUEST[ 'ids' ]) );
		if( $settings[ 'format' ] == 'XLS' AND !$settings[ 'format_xls_use_xls_format' ] )
			$settings[ 'format' ] = 'XLSX';
		$this->send_headers( $settings[ 'format' ], WC_Order_Export_Engine::make_filename( $settings['export_filename'] ) );
		readfile( $filename );
		unlink( $filename );
	}

	function export_orders_bulk_action_notices() {

		global $post_type, $pagenow;

		if ( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset( $_REQUEST[ 'export_bulk_profile' ] ) ) {
			$url = admin_url( 'admin-ajax.php' ) . "?action=order_exporter&method=export_download_bulk_file&export_bulk_profile=" . $_REQUEST[ 'export_bulk_profile' ] . "&ids=" . $_REQUEST[ 'ids' ];
			//$message = sprintf( __( 'Orders exported. <a href="%s">Download report.</a>' ,'woocommerce-order-export'), $url );
			$message = __( 'Orders exported.','woocommerce-order-export');

			echo "<div class='updated'><p>{$message}</p></div><iframe width=0 height=0 style='display:none' src='$url'></iframe>";

			// must remove this arg from pagination url
			add_filter('removable_query_args', array($this, 'fix_table_links') );
		}
	}

	function fix_table_links( $args ) {
		$args[] = 'export_bulk_profile';
		$args[] = 'ids';
		return $args;
	}

	function must_run_ajax_methods() {
		// wait admin ajax!
		if ( basename($_SERVER['SCRIPT_NAME']) != "admin-ajax.php" )
				return false;
		// our method MUST BE called
		return isset($_REQUEST['action'])  AND ($_REQUEST['action'] == "order_exporter"  OR $_REQUEST['action'] == "order_exporter_run" );
	}
}
