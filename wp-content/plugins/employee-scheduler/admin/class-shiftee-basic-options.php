<?php

/**
 * The file that defines the plugin options class
 *
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin
 */

/**
 *
 * @since      2.0.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic_Options {

	private $options;

	private $currencies;

	private $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$shiftee_helper = new Shiftee_Helper();
		$this->helper = $shiftee_helper;
		$this->options = $shiftee_helper->shiftee_options();
		$this->currencies = $shiftee_helper->currency_list();

	}

	public function add_options_page() {
		add_menu_page(
			apply_filters( 'shiftee_name', __( 'Shiftee Basic', 'employee-scheduler' ) ),
			apply_filters( 'shiftee_name', __( 'Shiftee Basic', 'employee-scheduler' ) ),
			'manage_options',
			'shiftee',
			array( $this, 'render_options' ),
			plugins_url( 'employee-scheduler/admin/partials/images/shiftee-icon.png' ),
			87.2317
		);
		add_submenu_page( 'shiftee', __( 'Instructions', 'employee-scheduler' ), __( 'Instructions', 'employee-scheduler' ), 'manage_options', 'instructions', array( $this, 'show_instructions' ) );
		add_submenu_page( 'edit.php?post_type=shift', __( 'View Schedules', 'employee-scheduler' ), __( 'View Schedules', 'employee-scheduler' ), 'manage_options', 'view-schedules', array( $this, 'admin_view_schedules' ) );
		add_submenu_page( 'shiftee', __( 'About Shiftee', 'employee-scheduler' ), __( 'About Shiftee', 'employee-scheduler' ), 'manage_options', 'about-shiftee', array( $this, 'about_shiftee_page' ) );
		add_submenu_page( null, __( 'Shiftee Upgrades', 'employee-scheduler' ), __( 'Shiftee Upgrades', 'employee-scheduler' ), 'manage_options', 'shiftee-upgrades', array( $this, 'shiftee_upgrades_page' ) );

		do_action( 'shiftee_add_submenu_pages' );
	}

	/**
	 * Display the instructions page.
	 *
	 * @since 1.0.0
	 */
	public function show_instructions() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/instructions.php';
	}

	/**
	 * Display the "view schedules" page in the dashboard
	 *
	 * @since 1.5.0
	 */
	public function admin_view_schedules() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/dashboard-schedules.php';
	}

	/**
	 * Display the About Shiftee page, which explains that Employee Scheduler is now Shiftee
	 *
	 * @since 2.0.0
	 */
	public function about_shiftee_page() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/about-shiftee.php';
	}

	/**
	 * Display the Shiftee Upgrades page, which users see when they are doing upgrades
	 *
	 * @since 2.1.0
	 */
	public function shiftee_upgrades_page() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/shiftee-upgrades.php';
	}

	/**
	 * Register settings.
	 *
	 * Create the settings sections and fields.
	 *
	 * @since 1.0
	 */
	public function options_page_init() {

		register_setting( 'wpaesm_plugin_options', 'wpaesm_options', array( $this, 'validate_options' ) );

		add_settings_section(
			'shiftee_basic_staff_notifications',
			__( 'Staff Notification Settings', 'employee-scheduler' ),
			array( $this, 'options_section_callback' ),
			'wpaesm_plugin_options'
		);

		add_settings_field(
			'notification_from_name',
			__( 'Message Sender (Name)', 'employee-scheduler' ),
			array( $this, 'notification_from_name_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_staff_notifications',
			array(
				__( 'Email notifications sent to staff will come from this name', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'notification_from_email',
			__( 'Message Sender (Email Address)', 'employee-scheduler' ),
			array( $this, 'notification_from_email_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_staff_notifications',
			array(
				__( 'Email notifications sent to staff will come from this email address', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'notification_subject',
			__( 'Notification Subject', 'employee-scheduler' ),
			array( $this, 'notification_subject_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_staff_notifications',
			array(
				__( 'Email notifications sent to staff about scheduled shifts will have this subject', 'employee-scheduler' )
			)
		);

		add_settings_section(
			'shiftee_basic_admin_notifications',
			__( 'Admin Notification Settings', 'employee-scheduler' ),
			array( $this, 'options_section_callback' ),
			'wpaesm_plugin_options'
		);


		add_settings_field(
			'admin_notify_note',
			__( 'Shift Note Notification', 'employee-scheduler' ),
			array( $this, 'admin_notify_note_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_admin_notifications',
			array(
				__( 'Notify admin when a staff member adds a note to a shift', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'admin_notify_clockout',
			__( 'Clockout Notification', 'employee-scheduler' ),
			array( $this, 'admin_notify_clockout_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_admin_notifications',
			array(
				__( 'Notify admin when a staff member clocks out of a shift', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'admin_notification_email',
			__( 'Admin Notification Email', 'employee-scheduler' ),
			array( $this, 'admin_notification_email_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_admin_notifications',
			array(
				__( 'Enter the email address that will receive email notifications about staff activities', 'employee-scheduler' )
			)
		);

		add_settings_section(
			'shiftee_basic_settings',
			__( 'Shiftee Basic Settings', 'employee-scheduler' ),
			array( $this, 'options_section_callback' ),
			'wpaesm_plugin_options'
		);

		add_settings_field(
			'extra_shift_approval',
			__( 'Require Approval for Extra Shifts', 'employee-scheduler' ),
			array( $this, 'extra_shift_approval_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_settings',
			array(
				__( 'If this is checked, an administrator will receive an email when a staff member enters an extra shift, and the administrator can choose whether or not to approve the extra shift.', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'geolocation',
			__( 'Geolocation', 'employee-scheduler' ),
			array( $this, 'geolocation_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_settings',
			array(
				__( 'Check to record the location where staff clock in and out', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'week_starts_on',
			__( 'Week Starts On:', 'employee-scheduler' ),
			array( $this, 'week_starts_on_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_settings',
			array(
				__( 'For scheduling purposes, what day does the work-week start on?', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'currency',
			__( 'Currency:', 'employee-scheduler' ),
			array( $this, 'currency_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_settings',
			array(
				__( 'Select the currency for expenses and wages.', 'employee-scheduler' )
			)
		);

		add_settings_field(
			'currency_position',
			__( 'Currency Position:', 'employee-scheduler' ),
			array( $this, 'currency_position_render' ),
			'wpaesm_plugin_options',
			'shiftee_basic_settings',
			array(
				__( 'Display currency symbol before or after number?', 'employee-scheduler' )
			)
		);

	}

	/**
	 * Render "Notification From Name" setting.
	 *
	 * @since 1.0
	 */
	public function notification_from_name_render( $args ) { ?>

		<input type="text" size="57" name="wpaesm_options[notification_from_name]" value="<?php echo $this->options['notification_from_name']; ?>" />
		<br /><span class="description"><?php echo $args[0] ?></span>

	<?php }

	/**
	 * Render "Notification Email" setting.
	 *
	 * @since 1.0
	 */
	public function notification_from_email_render( $args ) { ?>

		<input type="text" size="57" name="wpaesm_options[notification_from_email]" value="<?php echo $this->options['notification_from_email']; ?>" />
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Notification Subject" setting.
	 *
	 * @since 1.0
	 */
	public function notification_subject_render( $args ) { ?>

		<input type="text" size="57" name="wpaesm_options[notification_subject]" value="<?php echo $this->options['notification_subject']; ?>" />
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Admin Notify Status" setting.
	 *
	 * @since 1.0
	 */
	public function admin_notify_status_render( $args ) {  ?>

		<label><input name="wpaesm_options[admin_notify_status]" type="checkbox" value="1" <?php if( isset( $this->options['admin_notify_status'] ) ) { checked('1', $this->options['admin_notify_status'] ); } ?> /> <?php _e('Turn on shift status notifications', 'employee-scheduler'); ?></label>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Admin Notify Note" setting.
	 *
	 * @since 1.0
	 */
	public function admin_notify_note_render( $args ) { ?>

		<label><input name="wpaesm_options[admin_notify_note]" type="checkbox" value="1" <?php if( isset( $this->options['admin_notify_note'] ) ) { checked( '1', $this->options['admin_notify_note'] ); } ?> /> <?php _e('Turn on shift note notifications', 'employee-scheduler'); ?></label>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Admin Notify Clockout" setting.
	 *
	 * @since 1.7.0
	 */
	public function admin_notify_clockout_render( $args ) {  ?>

		<label><input name="wpaesm_options[admin_notify_clockout]" type="checkbox" value="1" <?php if( isset( $this->options['admin_notify_clockout'])) { checked('1', $this->options['admin_notify_clockout']); } ?> /> <?php _e( 'Turn on clockout notifications', 'employee-scheduler' ); ?></label>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Admin Notification Email" setting.
	 *
	 * @since 1.0
	 */
	public function admin_notification_email_render( $args ) {  ?>

		<input type="text" size="57" name="wpaesm_options[admin_notification_email]" value="<?php echo $this->options['admin_notification_email']; ?>" />
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Extra Shift Approval" setting.
	 *
	 * @since 1.7.0
	 */
	public function extra_shift_approval_render( $args ) { ?>

		<label><input name="wpaesm_options[extra_shift_approval]" type="checkbox" value="1" <?php if( isset( $this->options['extra_shift_approval'] ) ) { checked('1', $this->options['extra_shift_approval'] ); } ?> /> <?php _e('Require approval for extra shifts', 'employee-scheduler'); ?></label><br />
		<br /><span class="description"><?php echo $args[0]; ?></span>

	<?php }

	/**
	 * Render "Geolocation" setting.
	 *
	 * @since 1.0
	 */
	public function geolocation_render( $args ) {  ?>

		<label><input name="wpaesm_options[geolocation]" type="checkbox" value="1" <?php if( isset( $this->options['geolocation'] ) ) { checked( '1', $this->options['geolocation']); } ?> /> <?php _e('Record location when staff clock in and clock out', 'employee-scheduler'); ?></label>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Week Starts On" setting.
	 *
	 * @since 1.0
	 */
	public function week_starts_on_render( $args ) { ?>

		<select name='wpaesm_options[week_starts_on]'>
			<option value='Sunday' <?php selected( 'Sunday', $this->options['week_starts_on'] ); ?>><?php _e( 'Sunday', 'employee-scheduler' ); ?></option>
			<option value='Monday' <?php selected( 'Monday', $this->options['week_starts_on'] ); ?>><?php _e( 'Monday', 'employee-scheduler' ); ?></option>
			<option value='Tuesday' <?php selected( 'Tuesday', $this->options['week_starts_on'] ); ?>><?php _e( 'Tuesday', 'employee-scheduler' ); ?></option>
			<option value='Wednesday' <?php selected( 'Wednesday', $this->options['week_starts_on'] ); ?>><?php _e( 'Wednesday', 'employee-scheduler' ); ?></option>
			<option value='Thursday' <?php selected( 'Thursday', $this->options['week_starts_on'] ); ?>><?php _e( 'Thursday', 'employee-scheduler' ); ?></option>
			<option value='Friday' <?php selected( 'Friday', $this->options['week_starts_on'] ); ?>><?php _e( 'Friday', 'employee-scheduler' ); ?></option>
			<option value='Saturday' <?php selected( 'Saturday', $this->options['week_starts_on'] ); ?>><?php _e( 'Saturday', 'employee-scheduler' ); ?></option>
		</select>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render "Currency" setting.
	 *
	 * @since 1.9
	 */
	public function currency_render( $args ) { ?>

		<select name='wpaesm_options[currency]'>
			<option value='' <?php selected('', $this->options['currency']); ?>></option>
			<?php foreach( $this->currencies as $symbol => $name ) { ?>
				<option value='<?php echo $symbol; ?>' <?php selected( $symbol, $this->options['currency'] ); ?>><?php echo esc_attr( $name ); ?></option>
			<?php } ?>

		</select>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }


	/**
	 * Render "Currency Position" setting.
	 *
	 * @since 1.9
	 */
	public function currency_position_render( $args ) { ?>

		<label><input name="wpaesm_options[currency_position]" type="radio" value="before" <?php if( isset( $this->options['currency_position'] ) ) { checked('before', $this->options['currency_position'] ); } ?> /> <?php _e( 'Before (such as $100.00)', 'employee-scheduler' ); ?></label><br />
		<label><input name="wpaesm_options[currency_position]" type="radio" value="after" <?php if( isset( $this->options['currency_position'] ) ) { checked('after', $this->options['currency_position'] ); } ?> /> <?php _e( 'After (such as 100.00 &euro;)', 'employee-scheduler' ); ?></label>
		<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php }

	/**
	 * Render the plugin settings form.
	 *
	 * @since 2.0.0
	 */
	public function render_options() {
		?>
		<div class="wrap">
			<h1><?php echo apply_filters( 'shiftee_name', __( 'Shiftee Basic', 'employee-scheduler' ) ); ?></h1>
			<?php settings_errors(); ?>

			<div id="shiftee-options-main">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'wpaesm_plugin_options' );
					do_settings_sections( 'wpaesm_plugin_options' );
					submit_button();
					?>
				</form>
			</div>

			<?php
			$admin = new Shiftee_Basic_Admin( 'Shiftee Basic', '2.0' );
			$admin->show_sidebar(); ?>

		</div>
		<?php
	}

	/**
	 * Settings section callback.
	 *
	 * Doesn't do anything.
	 *
	 * @since 2.0.0
	 */
	public function options_section_callback() {

	}

	/**
	 * Validate options.
	 *
	 * Validate and sanitize settings before saving.
	 *
	 * @since 1.0
	 *
	 * @param array  $input  Settings entered into the form.
	 * @return array $input  Settings to be saved in the database.
	 */
	public function validate_options( $input ) {

	    $options = $this->options; // make sure we don't over-write options from other Shiftee add-ons

		if( isset( $input['notification_from_name'] ) )
			$options['notification_from_name'] =  wp_filter_nohtml_kses( $input['notification_from_name'] );
		if( isset( $input['notification_from_email'] ) )
			$options['notification_from_email'] =  wp_filter_nohtml_kses( $input['notification_from_email'] );
		if( isset( $input['notification_subject'] ) )
			$options['notification_subject'] =  wp_filter_nohtml_kses( $input['notification_subject'] );
		if( isset( $input['admin_notification_email'] ) )
			$options['admin_notification_email'] =  wp_filter_nohtml_kses( $input['admin_notification_email'] );
		if( isset( $input['admin_notify_status'] ) )
			$options['admin_notify_status'] =  wp_filter_nohtml_kses( $input['admin_notify_status'] );

		if( isset( $input['admin_notify_note'] ) && '1' == $input['admin_notify_note'] ) {
			$options['admin_notify_note'] = '1';
		} else {
			$options['admin_notify_note'] = '0';
        }

		if( isset( $input['admin_notify_clockout'] ) && '1' == $input['admin_notify_clockout'] ) {
			$options['admin_notify_clockout'] = '1';
		} else {
			$options['admin_notify_clockout'] = '0';
		}

		if( isset( $input['geolocation'] ) && '1' == $input['geolocation'] ) {
			$options['geolocation'] = '1';
		} else {
			$options['geolocation'] = '0';
		}

		if( isset( $input['extra_shift_approval'] ) && '1' == $input['extra_shift_approval'] ) {
			$options['extra_shift_approval'] = '1';
		} else {
			$options['extra_shift_approval'] = '0';
		}

		if( isset( $input['week_starts_on'] ) )
			$options['week_starts_on'] =  wp_filter_nohtml_kses( $input['week_starts_on'] );
		if( isset( $input['currency'] ) )
			$options['currency'] =  wp_filter_nohtml_kses( $input['currency'] );
		if( isset( $input['currency_position'] ) )
			$options['currency_position'] =  wp_filter_nohtml_kses( $input['currency_position'] );
		if( isset( $input['shiftee_meta_update_last_step'] ) )
		    $options['shiftee_meta_update_last_step'] = intval( $input['shiftee_meta_update_last_step'] );
		if( isset( $input['db_version'] ) )
		    $options['db_version'] = wp_filter_nohtml_kses( $input['db_version'] );

		$options = apply_filters( 'shiftee_validation_options_filter', $options );

		return $options;
	}

}
if ( is_admin() )
	$options = new Shiftee_Basic_Options();