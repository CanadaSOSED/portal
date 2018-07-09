<?php
/**
 * Plugin Name:			Where Did You Hear About Us Checkout Field for WooCommerce
 * Plugin URI:			http://wooassist.com/
 * Description:			Adds a custom field in the checkout page to ask the custom where they've heard about your store.
 * Version:				1.0.1
 * Author:				Wooassist
 * Author URI:			http://wooassist.com/
 * Requires at least:	4.0.0
 * Tested up to:		4.9.2
 *
 * Text Domain: wc-customer-source
 * Domain Path: /languages/
 *
 * @package WC_Customer_Source
 * @category Core
 * @author Wooassist
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of WC_Customer_Source to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WC_Customer_Source
 */
function WC_Customer_Source() {
	return WC_Customer_Source::instance();
} // End WC_Customer_Source()

WC_Customer_Source();

/**
 * Main WC_Customer_Source Class
 *
 * @class WC_Customer_Source
 * @version	1.0.0
 * @since 1.0.0
 * @package	WC_Customer_Source
 */
final class WC_Customer_Source {
	/**
	 * WC_Customer_Source The single instance of WC_Customer_Source.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The settings array.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'wc-customer-source';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		$this->settings = get_option( 'wc_customer_source_settings' );

		add_action( 'init', array( $this, 'plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );

		$plugin_enabled = isset( $this->settings['plugin_enabled'] ) ? $this->settings['plugin_enabled'] : true;
		$checkout_display_action = isset( $this->settings['checkout_field_position'] ) ? $this->settings['checkout_field_position'] : 'woocommerce_after_order_notes';

		if ( $plugin_enabled ) {

			add_action( $checkout_display_action, array( $this, 'display_custom_field' ) );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_custom_field') );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_custom_field' ) );
		}

		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_fields_order_edit' ) );
		add_filter( 'woocommerce_admin_reports', array( $this, 'register_admin_pages' ) );
	}

	/**
	 * Main WC_Customer_Source Instance.
	 *
	 * Ensures only one instance of WC_Customer_Source is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC_Customer_Source()
	 * @return Main WC_Customer_Source instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain( 'wc-customer-source', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Plugin page links.
	 *
	 * @since  1.0.0
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url('admin.php?page=wc-reports&tab=customer_source&report=customer_source_report') . '">' . __( 'View Report', 'wc-customer-source' ) . '</a>',
			'<a href="' . admin_url('admin.php?page=wc-reports&tab=customer_source&report=customer_source_settings') . '">' . __( 'Settings', 'wc-customer-source' ) . '</a>',
			'<a href="https://wordpress.org/support/plugin/wc-customer-source">' . __( 'Support', 'wc-customer-source' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Installation.
	 *
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();

		if( ! get_option( 'wc_customer_source_settings' ) ) {

			$order_statuses = array();

			foreach ( wc_get_order_statuses() as $status => $label ) {

				if ( 'wc-processing' == $status || 'wc-completed' == $status ) {
					$order_statuses[ $status ] = true;
				} else {
					$order_statuses[ $status ] = false;
				}
			}

			update_option( 'wc_customer_source_settings', array(
				'plugin_enabled'			=>	true,
				'checkout_field_position'	=>	'woocommerce_checkout_after_order_review',
				'checkout_field_label'		=>	__( 'Where did you hear about us?', 'wc-customer-source' ),
				'checkout_field_options'	=>	array(
					'Google search'  =>  'Google search',
					'Facebook' 		 =>	 'Facebook',
					'Twitter'		 =>	 'Twitter',
				),
				'checkout_field_required'	=>	false,
				'other_field_disable'		=>	false,
				'other_field_label'			=>	__( 'Other', 'wc-customer-source' ),
				'report_orders_displayed'	=>	false,
				'report_orders_statuses'	=>	$order_statuses,
			) );
		}
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Add custom field on checkout.
	 *
	 * Adds the custom field on the checkout page below the "order notes" field
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function display_custom_field() {

		$source_options = array( '' => 'select...' );
		$source_options = array_merge( $source_options, (array) $this->settings['checkout_field_options'] );

		if ( ! $this->settings['other_field_disable'] ) {
			$source_options['other'] = __( 'Other', 'wc-customer-source' );
		}
		?>
		<div id="wc-customer-source">
			<?php
				woocommerce_form_field( 'wc_customer_source_checkout_field', array(
					'type'          => 'select',
					'class'         => array('wc-customer-source-select form-row-wide'),
					'label'         => $this->settings['checkout_field_label'],
					'options'		=> $source_options,
					'required'		=> array_key_exists( 'checkout_field_required', $this->settings ) && $this->settings['checkout_field_required'] ? true : false,
				) );
			?>
			<div class="wc-customer-source-other-field form-row form-row-wide" style="display: none">
				<label><?php echo $this->settings['other_field_label']; ?></label>
				<input class="input-text" type="text" name="wc_customer_source_checkout_other_field" value="" disabled>
			</div>
		</div>

		<script type="text/javascript">
		(jQuery( document ).ready( function($) {

			// reset field every page reload
			$('#wc_customer_source_checkout_field').val('');

			// display Other field when "other" is selected
			$('.woocommerce').on('change', '#wc_customer_source_checkout_field', function() {

				var otherField = $('.wc-customer-source-other-field');

				if ( $(this).val() == 'other' ) {
					otherField.find('.input-text').prop( 'disabled', false );
					otherField.show();
				} else {
					otherField.find('.input-text').prop( 'disabled', true );
					otherField.hide();
				}
			});
		}));

		</script>

		<?php
	 }

	 /**
	  * Validate custom checkout fields if it is set as required.
	  *
	  * @access public
 	  * @since 1.0.1
 	  * @return void
	  */
	 public function validate_custom_field() {

		 if ( ! $this->settings['checkout_field_required'] )
		 	return;

		if ( ! $_POST['wc_customer_source_checkout_field'] )
            wc_add_notice( __( '"' . $this->settings['checkout_field_label'] . '" field is required' ), 'error' );

		if ( $_POST['wc_customer_source_checkout_field'] == 'other' && ! $_POST['wc_customer_source_checkout_other_field'] )
			wc_add_notice( __( '"' . $this->settings['other_field_label'] . '" field is required' ), 'error' );
	 }

	 /**
	  * Update the order meta with the custom field values
	  *
	  * @access public
 	  * @since 1.0.0
 	  * @return void
	  */
	 public function save_custom_field( $order_id ) {
		if ( ! empty( $_POST['wc_customer_source_checkout_field'] ) ) {
			update_post_meta( $order_id, 'wc_customer_source_checkout_field', sanitize_text_field( $_POST['wc_customer_source_checkout_field'] ) );
		}
		if ( ! empty( $_POST['wc_customer_source_checkout_other_field'] ) ) {
			update_post_meta( $order_id, 'wc_customer_source_checkout_other_field', sanitize_text_field( $_POST['wc_customer_source_checkout_other_field'] ) );
		}
	 }

	 /**
	  * Display values on order edit page
	  *
	  * @access public
 	  * @since 1.0.0
 	  * @return void
	  */
	 public function display_fields_order_edit( $order ) {

		 $source = get_post_meta( $order->id, 'wc_customer_source_checkout_field', true );
		 $other  = get_post_meta( $order->id, 'wc_customer_source_checkout_other_field', true );

		 ?>
		 	<p>
				<strong><?php echo $this->settings['checkout_field_label']; ?></strong><br>
				<?php echo $source; ?>
				<?php echo ( $source == 'other' && $other ) ? ' - ' . $other : ''; ?>
			</p>
		 <?php
	 }

	 public function register_admin_pages( $reports ) {

         $reports['customer_source'] = array(
                 'title'         =>  __( 'Customer Source', 'wc-customer-source' ),
                 'reports'       =>  array(
                         'customer_source_report'  => array(
                             'title'         => __( 'Report', 'wc-customer-source' ),
                             'description'   => '',
                             'hide_title'    => true,
                             'callback'      => array( __CLASS__, 'display_report' )
                         ),
                         'customer_source_settings'  => array(
                             'title'         => __( 'Settings', 'wc-customer-source' ),
                             'description'   => '',
                             'hide_title'    => true,
                             'callback'      => array( __CLASS__, 'display_settings' )
                         ),
                 ),

         );

         return $reports;
     }

	 /**
	  * Display report
	  *
	  * @access public
 	  * @since 1.0.0
 	  * @return void
	  */
     public static function display_report() {

		include_once( 'includes/wccs-source-report-table.php' );

		$report = new WC_Customer_Source_Report();
		$report->output_report();
     }

	 /**
	  * Display settings
	  *
	  * @access public
 	  * @since 1.0.0
 	  * @return void
	  */
     public static function display_settings() {

		include_once( 'includes/wccs-admin-settings.php' );

		$settings = new WC_Customer_Source_Settings();
		$settings->save_settings();
		$settings->display_settings_page();
     }
} // End Class
