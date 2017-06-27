<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/public
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin settings.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    options    The settings for this plugin.
	 */
	private $options;

	/**
	 * The plugin helper.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    options    The settings for this plugin.
	 */
	private $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$helper        = new Shiftee_Helper();
		$this->helper  = $helper;
		$this->options = $helper->shiftee_options();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		if ( is_singular( 'shift' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shiftee-public.css', array(), $this->version, 'all' );
		}

		global $post;
		if( $post &&
        ( has_shortcode( $post->post_content, 'record_expense' ) ||
            has_shortcode( $post->post_content, 'extra_work' ) ||
            has_shortcode( $post->post_content, 'master_schedule' ) ||
            has_shortcode( $post->post_content, 'your_schedule' ) ||
            has_shortcode( $post->post_content, 'employee_profile' ) )
        ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shiftee-public.css', array(), $this->version, 'all' );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		if ( isset( $this->options['geolocation'] ) && 1 == $this->options['geolocation'] && is_singular( 'shift' ) ) {
			wp_enqueue_script( 'geolocation', plugin_dir_url( __FILE__ ) . 'js/geolocation.js' );
		}

		global $post;
		if( $post && has_shortcode( $post->post_content, 'record_expense' ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shiftee-public.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );
		}

		if( $post && ( has_shortcode( $post->post_content, 'extra_work' ) || has_shortcode( $post->post_content, 'employee_profile' ) ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
			wp_enqueue_script( 'timepicker-addon', WP_PLUGIN_URL . '/employee-scheduler/libraries/cmb2/js/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ) );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shiftee-public.js', array( 'jquery', 'jquery-ui-datepicker', 'timepicker-addon' ), $this->version, false );
		}

		if( $post && has_shortcode( $post->post_content, 'master_schedule' ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shiftee-public.js', array( 'jquery' ), $this->version, false );
		}

		$datetimepicker_options = $this->helper->get_datetimepicker_options();
		wp_localize_script( $this->plugin_name, 'datetimepicker_options', $datetimepicker_options );

	}

	/**
	 * Register shortcodes.
	 *
	 * @since 2.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'master_schedule', array( $this, 'master_schedule_shortcode' ) );
		add_shortcode( 'your_schedule', array( $this, 'your_schedule_shortcode' ) );
		add_shortcode( 'employee_profile', array( $this, 'employee_profile_shortcode' ) );
		add_shortcode( 'today', array( $this, 'today_shortcode' ) );
		add_shortcode( 'extra_work', array( $this, 'extra_work_shortcode' ) );
		add_shortcode( 'record_expense', array( $this, 'record_expense_shortcode' ) );
	}

	/**
	 * Display a login form
	 *
	 * @since 2.0.0
	 *
	 * @param $echo
	 *
	 * @return string
	 */
	public function show_login_form( $echo = false ) {

		$login_form = '<p>' . __( 'You must be logged in to view this page.', 'employee-scheduler' ) . '</p>';
		$args      = array(
			'echo' => false,
		);
		$login_form .= wp_login_form( $args );

		if( $echo ) {
			echo $login_form;
		} else {
			return $login_form;
		}

	}

	/**
	 * Single Shift Title.
	 *
	 * Change the title on the single shift view to "Shift Details."
	 *
	 * @since 1.0
	 *
	 * @param string $title The post title.
	 *
	 * @return string $title The filtered post title
	 */

	public function single_shift_title( $title ) {
		global $post;
		if ( is_singular( 'shift' ) && $title == $post->post_title && is_main_query() ) {
			$title = __( 'Shift Details', 'employee-scheduler' );
		}

		return $title;
	}

	/**
	 * Filter the single shift view
	 *
	 * @since 1.0
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function single_shift_view( $content ) {

		if ( is_singular( 'shift' ) && is_main_query() ) {

			if( !$this->helper->user_is_allowed() ) {
				return $this->show_login_form();
			}

			if ( !empty( $_POST ) ) {
				$this->process_single_shift_forms();
			}

			ob_start();
			include 'partials/single-shift.php';
			$shift_content = ob_get_clean();

			$content .= apply_filters( 'shiftee_single_shift', $shift_content, 10, get_the_id() );

		}

		return $content;

	}

	/**
	 * On the single shift view, process forms if needed
	 *
	 * @since 2.0.0
	 */
	public function process_single_shift_forms() {

		// if employee left a note
		if ( isset( $_POST['shiftee-employee-shift-note'] ) && 'Save Note' == ( $_POST['shiftee-employee-shift-note'] ) ) {
			$confirmation = $this->save_employee_note();
			if ( !empty( $confirmation ) ) {
				echo '<p class="' . $confirmation['status'] . '">' . $confirmation['message'] . '</p>';
			}
		}

		// If employee just pushed the clock in button
		if ( isset( $_POST['shiftee-clock-in-form'] ) && 'Clock In' == ( $_POST['shiftee-clock-in-form'] ) ) {
			$this->clock_in();
		}

		// If employee just pushed the clock out button
		if ( isset( $_POST['shiftee-clock-out-form'] ) && 'Clock Out' == ( $_POST['shiftee-clock-out-form'] ) ) {
			$this->clock_out();
		}

	}

	/**
	 * Display the clock-in form if needed.
	 *
	 * If the shift date is today, and if the current user is assigned to the shift and has not clocked in, show the clock in form
	 *
	 * @param $shift
	 *
	 * @return string HTML clock-in form
	 */
	public function maybe_clock_in( $shift ) {

		$assigned_employee = $this->helper->get_shift_connection( $shift, 'employee' );
		$current_user = wp_get_current_user();

		$start_date = get_post_meta( $shift, '_shiftee_shift_start', true );
		$end_date = get_post_meta( $shift, '_shiftee_shift_end', true );

		if ( $assigned_employee == $current_user->ID // employee assigned to the shift is viewing the shift
             && ( current_time( 'Ymd' ) == date( 'Ymd', $start_date ) || current_time( 'Ymd' ) == date( 'Ymd', $end_date ) ) // shift is scheduled for today
		     && '' == get_post_meta( $shift, '_shiftee_clock_in', true ) // employee has not clocked in already
		)
		{
			ob_start();
			include 'partials/clock-in.php';

			return ob_get_clean();

		}
	}

	/**
	 * Clock in.
	 *
	 * When employee clicks the "clock in" link, save the time and, if relevant, the location
	 *
	 * @since 2.0.0
	 */
	private function clock_in() {
		if ( ! wp_verify_nonce( $_POST['shiftee_clock_in_nonce'], 'shiftee_clock_in' ) ) {
			exit( "Permission error." );
		}

		$shift = get_post( intval( $_POST['shift-id'] ) );
		if ( ! isset( $shift->post_type ) || 'shift' !== $shift->post_type ) {
			$error = __( 'Could not clock in.  Please go back and try again.', 'employee-scheduler' );
			wp_die( $error );
		}

		// save clock in time
		update_post_meta( $shift->ID, '_shiftee_clock_in', current_time( 'timestamp' ) );

		$testing_meta = get_post_meta( $shift->ID, '_shiftee_clock_in', true );
		if ( ! isset( $testing_meta ) || '' == $testing_meta ) {
			wp_die( __( 'Something has gone wrong.  Please use the back button to try to clock in again.  If you continue to receive this error, contact the site administrator.', 'employee-scheduler' ) );
		}

		// save address
		if ( isset( $_POST['latitude'] ) && isset( $_POST['longitude'] ) ) {

			$address = $this->get_address( $_POST['latitude'], $_POST['longitude'] );
			update_post_meta( $shift->ID, '_shiftee_location_clock_in', sanitize_text_field( $address ) );

		}

		do_action( 'shiftee_clock_in_action' );

		unset( $_POST );
	}

	/**
	 * Given latitude and longitude, get a street address
	 *
	 * @param $lat
	 * @param $long
	 *
	 * @since 2.0.0
	 *
	 * @return string|void
	 */
	private function get_address( $lat, $long ) {

		$response = wp_remote_get( 'http://maps.google.com/maps/api/geocode/json?latlng=' . $lat . ',' . $long );

		if( is_wp_error( $response ) ) {
			$error = __( 'Unable to retrieve location data', 'employee-scheduler' );
			return $error;
		} else {
			$body = wp_remote_retrieve_body( $response );
			$json = json_decode( $body );
			if( isset( $json->status ) && 'OK' == $json->status ) {
				$address = $json->results[0]->formatted_address;
				return $address;
			} else {
				$error = __( 'Unable to retrieve location data', 'employee-scheduler' );
				return $error;
			}
		}

	}

	/**
	 * Show the clock out form if needed.
	 *
	 * If the shift date is today, and the current user is assigned to the shift and has already clocked in, show the clock out button.
	 *
	 * @param $shift
	 *
	 * @return string HTML clock-out form
	 */
	public function maybe_clock_out( $shift ) {

		$assigned_employee = $this->helper->get_shift_connection( $shift, 'employee' );
		$current_user = wp_get_current_user();

		$start_date = get_post_meta( $shift, '_shiftee_shift_start', true );
		$end_date = get_post_meta( $shift, '_shiftee_shift_end', true );

		if ( $assigned_employee == $current_user->ID // employee assigned to the shift is viewing the shift
		     && ( current_time( 'Ymd' ) == date( 'Ymd', $start_date ) || current_time( 'Ymd' ) == date( 'Ymd', $end_date ) ) // shift is scheduled for today
		     && '' !== get_post_meta( $shift, '_shiftee_clock_in', true ) // employee clocked in already
		     && '' == get_post_meta( $shift, '_shiftee_clock_out', true ) // employee has not clocked out
		)
		{
			ob_start();
			include 'partials/clock-out.php';

			return ob_get_clean();

		}

	}

	/**
	 * Clock out.
	 *
	 * When employee clicks the "clock out" link, save the time and, if relevant, the location
	 *
	 * @since 2.0.0
	 */
	private function clock_out() {
		if ( ! wp_verify_nonce( $_POST['shiftee_clock_out_nonce'], 'shiftee_clock_out' ) ) {
			exit( "Permission error." );
		}

		$shift = get_post( intval( $_POST['shift-id'] ) );
		if ( ! isset( $shift->post_type ) || 'shift' !== $shift->post_type ) {
			$error = __( 'Could not clock out.  Please go back and try again.', 'employee-scheduler' );
			wp_die( $error );
		}

		// save clock out time
		update_post_meta( $shift->ID, '_shiftee_clock_out', current_time( 'timestamp' ) );

		$testing_meta = get_post_meta( $shift->ID, '_shiftee_clock_out', true );
		if ( ! isset( $testing_meta ) || '' == $testing_meta ) {
			wp_die( __( 'Something has gone wrong.  Please use the back button to try to clock out again.  If you continue to receive this error, contact the site administrator.', 'employee-scheduler' ) );
		}

		// save worked duration
		$duration = $this->helper->get_shift_duration( $shift->ID, 'worked', 'hours' );
		update_post_meta( $shift->ID, '_shiftee_worked_duration', $duration );

		// save address
		if ( isset( $_POST['latitude'] ) && isset( $_POST['longitude'] ) ) {

			$address = $this->get_address( $_POST['latitude'], $_POST['longitude'] );
			update_post_meta( $shift->ID, '_shiftee_location_clock_out', sanitize_text_field( $address ) );

		}

		// change shift status to "worked"
		wp_set_object_terms( $shift->ID, 'worked', 'shift_status' );

		do_action( 'shiftee_clock_out_action', $shift );

		unset( $_POST );
	}

	/**
	 * Display a shift's date and time in a user-friendly way
	 *
	 * @since 2.0.0
	 *
	 * @param $shift_id
	 * @param $time_type 'scheduled' or 'worked'
	 *
	 * @return string
	 */
	public function show_shift_date_and_time( $shift_id, $time_type = 'scheduled' ) {

		$date = get_post_meta( $shift_id, '_wpaesm_date', true );
		if ( '' !== $date && '__-__-____' !== $date ) {
			$date = sanitize_text_field( $date );
		} else {
			$date = __( 'No date selected', 'employee-scheduler' );
		}

		if ( 'scheduled' == $time_type ) {
			$start = sanitize_text_field( get_post_meta( $shift_id, '_wpaesm_starttime', true ) );
		} elseif ( 'worked' == $time_type ) {
			$start = sanitize_text_field( get_post_meta( $shift_id, '_wpaesm_clockin', true ) );
		}
		if ( '' !== $start && '__:__' !== $start ) {
			$timestamp  = strtotime( $start );
			$start_time = date( "g:ia", $timestamp );
		} else {
			$start_time = __( 'No start time selected', 'employee-scheduler' );
		}

		if ( 'scheduled' == $time_type ) {
			$end = sanitize_text_field( get_post_meta( $shift_id, '_wpaesm_endtime', true ) );
		} elseif ( 'worked' == $time_type ) {
			$end = sanitize_text_field( get_post_meta( $shift_id, '_wpaesm_clockout', true ) );
		}
		if ( '' !== $end && '__:__' !== $end ) {
			$timestamp = strtotime( $end );
			$end_time  = date( "g:ia", $timestamp );
		} else {
			$end_time = __( 'No end time selected', 'employee-scheduler' );
		}

		$datetime = $date . ',&nbsp;' . $start_time . '&nbsp;&ndash;&nbsp;' . $end_time;

		return $datetime;
	}

	/**
     * Display the shift's notes
	 *
	 * @return string
	 */
	public function display_shift_notes() {

	    // don't show this to On Demand customers
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		if( is_array( $roles ) && !empty( $roles ) ) {
			if ( in_array( 'shiftee_customer', $roles ) ) {
				return;
			}
		}

		$notes = get_post_meta( get_the_id(), '_shiftee_shift_notes', true );
		if ( isset( $notes ) && is_array( $notes ) ) {
			$notes_display = '<p><strong>' . __( 'Notes', 'employee-scheduler' ) . '</strong><ul>';
			foreach ( $notes as $note ) {
				if ( isset( $note['notedate'] ) && isset( $note['notetext'] ) ) {
					$notes_display .= "<li><strong>" . $this->helper->display_datetime( $note['notedate'], 'date' ) . ":</strong> " . sanitize_text_field( $note['notetext'] ) . "</li>";
				}
			}

			$notes_display .= '</ul></p>';

			return $notes_display;
		}

	}

	/**
	 * Display employee note form on single shift.
	 *
	 * If the employee who is viewing the site is assigned to the shift, show them the note form.
	 *
	 * @since 2.0.0
	 */
	public function display_shift_note_form() {

		$assigned_employee = $this->helper->get_shift_connection( get_the_id(), 'employee' );

		$current_user = wp_get_current_user();

		if ( $assigned_employee == $current_user->ID ) {
			include 'partials/employee-note-form.php';
		}
	}

	/**
	 * Save employee note.
	 *
	 * If employee filled out the "leave shift note" form, save the data.
	 *
	 * @since 1.0
	 *
	 * @return array confirmation message
	 */
	public function save_employee_note() {

		if ( ! wp_verify_nonce( $_POST['shiftee_employee_note_nonce'], 'shiftee_employee_note' ) ) {
			exit( "Permission error." );
		}

		// Make sure we actually have a shift
		$shift = get_post( intval( $_POST['shift-id'] ) );
		if ( ! isset( $shift->post_type ) || 'shift' !== $shift->post_type ) {
			$error = array(
				'status'  => 'shiftee-failure',
				'message' => __( 'There was an error saving your note.  Please contact the site administrator.', 'employee-scheduler' )
			);

			unset( $_POST );

			return $error;
		}

		$old_notes = get_post_meta( $shift->ID, '_shiftee_shift_notes', true );

		if ( ! isset( $old_notes ) || ! is_array( $old_notes ) ) {
			$old_notes = array();
		}

		$new_note = array(
            'notedate' => time(),
            'notetext' => sanitize_text_field( $_POST['note'] )
        );

		array_push( $old_notes, $new_note );

		delete_post_meta( $shift->ID, '_shiftee_shift_notes' );
		$saved_note = add_post_meta( $shift->ID, '_shiftee_shift_notes', $old_notes );

		if ( ! $saved_note ) {
			$error = array(
				'status'  => 'shiftee-failure',
				'message' => __( 'There was an error saving your note.  Please contact the site administrator.', 'employee-scheduler' )
			);

			unset( $_POST );

			return $error;
		}

		do_action( 'shiftee_save_employee_note_action', $shift, $_POST['note'] );

		$confirmation = array(
			'status'  => 'shiftee-success',
			'message' => __( 'Your note has been saved.', 'employee-scheduler' )
		);

		unset( $_POST );

		return $confirmation;
	}

	/**
	 * Master Schedule Shortcode.
	 *
	 * [master_schedule] displays a weekly work schedule with all employees' shifts.
	 *
	 * @since 1.0
	 *
	 * @param array $atts {
	 *      begin date
	 *      end date
	 *      type
	 *      status
	 *      location
	 *      public
	 *      manager
	 * }
	 *
	 * @return string  HTML for master schedule.
	 */
	public function master_schedule_shortcode( $atts ) {
		// @todo - move manager stuff to Pro, use filters

		// Attributes
		extract( shortcode_atts(
				array(
					'begin'    => '',
					'end'      => '',
					'type'     => '',
					'status'   => '',
					'location' => '',
					'public'   => 'false',
					'manager'  => '',
					'nav'      => 'on'
				), $atts )
		);

		if( !$this->helper->user_is_allowed() && 'false' == $public ) {
			return $this->show_login_form();
		}

		$week = $this->get_week_days( $atts );

		if ( '' !== $begin && '' !== $end ) {
			$nav = 'off';
		}

		// collect all the shifts
		foreach ( $week as $day => $shifts ) {
			$args = $this->make_query_args( $atts, $day );
			$msquery = new WP_Query( $args );
			$i       = 0;
			if ( $msquery->have_posts() ) :
				while ( $msquery->have_posts() ) : $msquery->the_post();

					$id                              = get_the_id();
					$week[ $day ][ $i ]['id']        = $id;
					$week[ $day ][ $i ]['permalink'] = get_the_permalink();
					$week[ $day ][ $i ]['starttime'] = get_post_meta( $id, '_shiftee_shift_start', true );
					$week[ $day ][ $i ]['endtime']   = get_post_meta( $id, '_shiftee_shift_end', true );

					// status
					$statuses = get_the_terms( $id, 'shift_status' );
					if ( is_array( $statuses ) ) {
						foreach ( $statuses as $shift_status ) {
							$week[ $day ][ $i ]['status'] = $shift_status->slug;
							$color                        = get_tax_meta( $shift_status->term_id, 'status_color' );
							$week[ $day ][ $i ]['color']  = $color;
						}
					}

					// type
					$types = get_the_terms( $id, 'shift_type' );
					if ( is_array( $types ) ) {
						$typeclass = '';
						foreach ( $types as $shift_type ) {
							$typeclass .= $shift_type->slug . ' ';
						}
						$week[ $day ][ $i ]['type'] = $typeclass;
					}

					// location
					$locations = get_the_terms( $id, 'location' );
					if ( is_array( $locations ) ) {
						$locclass = '';
						foreach ( $locations as $this_location ) {
							$locclass .= $this_location->slug . ' ';
							$week[ $day ][ $i ]['location'] = $locclass;
							$week[ $day ][ $i ]['location_name'] = $this_location->name;
						}
					}

					// employee
					$employee_id = $this->helper->get_shift_connection( $id, 'employee', 'ID' );
					if ( $employee_id ) {
						$week[ $day ][ $i ]['employee'] = $employee_id;
					} else {
						$week[ $day ][ $i ]['employee'] = __( 'Unassigned', 'employee-scheduler' );
					}

					// job
					$job = $this->helper->get_shift_connection( $id, 'job', 'object' );
					if ( $job ) {
						$week[ $day ][ $i ]['job']     = $job->post_title;
						$week[ $day ][ $i ]['joblink'] = site_url() . "/job/" . $job->post_name;
						$week[ $day ][ $i ]['jobeditlink'] = get_edit_post_link( $job->ID );
					}

					$i ++;
				endwhile;
			endif;
			wp_reset_postdata();

		}

		// go through the shifts and collect all the employees
		$employeearray = array();
		foreach ( $week as $day => $shifts ) {
			foreach ( $shifts as $shift ) {
				if ( isset( $shift['employee'] ) ) {
					$employeearray[] = $shift['employee'];
				}
			}
		}

		// take out all the duplicate employees
		$employeearray = array_unique( $employeearray );

		if ( 'off' == $nav ) {
			$class = 'class="wp-list-table widefat fixed posts striped"';
		} else {
			$class = '';
		}

		ob_start();
		include 'partials/shortcode-master-schedule.php';

		return apply_filters( 'shiftee_master_schedule_shortcode', ob_get_clean() );

	}

	/**
	 * Get a visitor-friendly version of the first or last date of the schedule
	 *
	 * @param $week
	 * @param $which_end
	 *
	 * @return bool|string
	 */
	public function terminal_date( $week, $which_end ) {

		$dates = array_keys( $week );

		if ( 'start' == $which_end ) {
		    return $this->helper->display_datetime( $dates[0], 'date' );
		}

		if ( 'end' == $which_end ) {
			$last = count( $dates ) - 1;

			return $this->helper->display_datetime( $dates[$last], 'date' );
		}
	}

	/**
	 * Set up an array of all of the days on the schedule
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	public function get_week_days( $atts ) {

		$week = array();

		if ( is_array( $atts ) && isset( $atts['begin'] ) && '' !== $atts['begin'] && isset( $atts['end'] ) && '' !== $atts['end'] ) {
			$thisday = strtotime( $atts['begin'] );
			$lastday = strtotime( $atts['end'] );
			while ( $thisday <= $lastday ) {
				$thisday = strtotime( '+1 day', $thisday );
				$week[ $thisday ] = array();
			}

		} else {
			// we don't have shortcode attributes for the date, so we'll use default dates
			// get the appropriate date
			if ( isset( $_GET['week'] ) ) {
				$thisweek = $_GET['week'];
			} else {
				$thisweek = current_time( 'timestamp' );
			}

			// get the range of dates for this week

			// find out what day of the week today is
			$today = date( 'l', $thisweek );

			if ( $today == $this->options['week_starts_on'] ) { // today is first day of the week
				$weekstart = $thisweek;
			} else { // find the most recent first day of the week
				$sunday    = 'last ' . $this->options['week_starts_on'];
				$weekstart = strtotime( $sunday, $thisweek );
			}

			// from the first day of the week, add one day 7 times to get all the days of the week
			$i = 0;
			while ( $i < 7 ) {
				$week[ date( strtotime( '+ ' . $i . 'days', $weekstart ) ) ] = array();
				$i ++;
			}
		}

		return $week;

	}

	/**
	 * Generate the query args to find all the shifts for the master schedule and your schedule shortcodes.
	 *
	 * @param $atts
	 * @param $day
	 * @param bool $employee
	 *
	 * @return array
	 */
	private function make_query_args( $atts, $day, $employee = false ) {
		$args = array(
			'post_type'      => 'shift',
			'meta_query'     => array(
				$this->helper->date_meta_query( $day )
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'shift_type',
					'field'    => 'slug',
					'terms'    => array( 'extra', 'pto' ),
					'operator' => 'NOT IN',
				),
			),
			'posts_per_page' => - 1,
			'meta_key'       => '_shiftee_shift_start',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
		);

		if ( is_array( $atts ) ) {

			if ( isset( $atts['type'] ) || isset( $atts['status'] ) || isset( $atts['location'] ) ) {

				$args['tax_query'] = array(
					'relation' => 'AND',
				);
				if ( isset( $atts['type'] ) ) {
					$args['tax_query'][] =
						array(
							'taxonomy' => 'shift_type',
							'field'    => 'slug',
							'terms'    => $atts['type'],
						);
				}
				if ( isset( $atts['status'] ) ) {
					$args['tax_query'][] =
						array(
							'taxonomy' => 'shift_status',
							'field'    => 'slug',
							'terms'    => $atts['status'],
						);
				}
				if ( isset( $atts['location'] ) ) {
					$args['tax_query'][] =
						array(
							'taxonomy' => 'location',
							'field'    => 'slug',
							'terms'    => $atts['location'],
						);
				}

			}

			if ( isset( $atts['manager'] ) ) {
				// get manager's employees
				$manager_obj = get_user_by( 'login', $atts['manager'] );
				if ( $manager_obj ) {
					$managers_employees = new WP_User_Query( array(
						'connected_type'      => 'manager_to_employee',
						'connected_items'     => $manager_obj->ID,
						'connected_direction' => 'to',
					) );

					if ( ! empty( $managers_employees->results ) ) {
						$employee_ids = array();
						foreach ( $managers_employees->results as $employee ) {
							$employee_ids[] = $employee->ID;
						}
						if ( ! empty( $employee_ids ) ) {
							$args['connected_type']  = 'shifts_to_employees';
							$args['connected_items'] = $employee_ids;
						}
					}
				}
			}
		}

		if ( $employee ) {
			$args['connected_type']  = 'shifts_to_employees';
			$args['connected_items'] = $employee;
		}

		return $args;

	}


	/**
	 * Collect employee details to display in Master Schedule shortcode
	 *
	 * @param $employee
	 *
	 * @return string
	 */
	public function employee_information( $employee ) {

		if ( 'Unassigned' == $employee ) {
			$employee_cell = $employee;
		} else {
			$employeeinfo  = get_user_by( 'id', $employee );
			$employee_cell = $employeeinfo->display_name;
			if ( isset( $employeeinfo->user_email ) ) {
				$employee_cell .= '<br /><a class="shiftee-employee-email" title="' . sanitize_email( $employeeinfo->user_email ) . '" href="mailto:' . sanitize_email( $employeeinfo->user_email ) . '">' . sanitize_text_field( $employeeinfo->user_email ) . '</a>';
			}
			$phone = get_user_meta( $employee, 'phone', true );
			if ( isset( $phone ) ) {
				$employee_cell .= '<br /><a class="shiftee-employee-phone" href="tel:' . $phone . '">' . $phone . '</a>';
			}
		}

		return $employee_cell;
	}

	/**
	 * Count the number of unassigned shifts.
	 *
	 * If there are lots of unassigned shifts, the Master Schedule shortcode will only show 3 at a time.
	 *
	 * @param $shifts
	 *
	 * @return int
	 */
	private function count_unassigned( $shifts ) {

		$unassigned_count = 0;
		foreach ( $shifts as $shift ) {
			if ( 'Unassigned' == $shift['employee'] ) {
				$unassigned_count ++;
			}
		}

		return $unassigned_count;

	}

	/**
	 * Generate CSS classes for a shift in the Master Schedule.
	 *
	 * @param $shift
	 *
	 * @return string
	 */
	private function shift_classes( $shift ) {

		$shift_classes = 'shiftee-shift ';
		if ( isset( $shift['status'] ) ) {
			$shift_classes .= 'shiftee-' . sanitize_title( $shift['status'] ) . ' ';
		}
		if ( isset( $shift['type'] ) ) {
			$shift_classes .= 'shiftee-' . sanitize_title( $shift['type'] );
		}
		if ( isset( $shift['location'] ) ) {
			$shift_classes .= 'shiftee-' . sanitize_title( $shift['location'] );
		}
		if ( isset( $shift['job'] ) ) {
			$shift_classes .= 'shiftee-' . sanitize_title( $shift['job'] );
		}

		return $shift_classes;

	}

	/**
	 * Generate inline style for a shift in the Master Schedule.
	 *
	 * @param $shift
	 *
	 * @return string
	 */
	private function shift_style( $shift ) {
		if ( isset( $shift['color'] ) ) {
			$shift_style = 'style="border-top: 4px solid' . esc_attr( $shift['color'] ) . '"';

			return $shift_style;
		}
	}

	/**
     * If we are in the admin, link to the post edit link, otherwise, link to the public view
     *
     * @since 2.1.0
     *
	 * @param $shift
	 *
	 * @return false|null|string
	 */
	private function shift_link( $shift ) {
	    if( is_admin() ) {
	        $link = get_edit_post_link( $shift );
        } else {
	        $link = get_the_permalink( $shift );
        }

        return $link;
    }

	/**
     * If we are in the admin, link to the job edit page, otherwise, link to the public view
     *
     * @since 2.1.0
     *
	 * @param $shift
	 *
	 * @return mixed
	 */
	private function job_link( $shift ) {
	    if( is_admin() ) {
	        $link = $shift['jobeditlink'];
        } else {
	        $link = $shift['joblink'];
        }

        return $link;
    }

	/**
	 * Generate "previous" and "next" links for schedule shortcodes
	 *
	 * @since 2.0.0
	 */
	public function schedule_nav() {

		if( isset( $_GET['week'] ) ) {
			$thisweek = $_GET['week'];
			$nextweek = strtotime( '+1 week', $thisweek );
			$lastweek = strtotime( '-1 week', $thisweek );
		} else {
			$thisweek = current_time( 'timestamp' );
			$nextweek = strtotime( '+1 week' );
			$lastweek = strtotime( '-1 week' );
		}
		?>
		<nav class="shiftee-schedule">
			<ul>
				<li class="shiftee-previous-week">
					<a href="<?php the_permalink(); ?>?week=<?php echo $lastweek; ?>">
						<?php _e( 'Previous Week', 'employee-scheduler' ); ?>
					</a>
				</li>
				<li class="shiftee-this-week">
					<a href="<?php the_permalink(); ?>">
						<?php _e( 'This Week', 'employee-scheduler' ); ?>
					</a>
				</li>
				<li class="shiftee-next-week">
					<a href="<?php the_permalink(); ?>?week=<?php echo $nextweek; ?>">
						<?php _e( 'Next Week', 'employee-scheduler' ); ?>
					</a>
				</li>
			</ul>
		</nav>
	<?php }

	/**
	 * Your Schedule Shortcode.
	 *
	 * [your_schedule] displays a weekly work schedule for the currently logged-in user.
	 *
	 * @since 1.0
	 *
	 * @param array $atts {
	 *     Shortcode attributes: begin date, end date, employee ID
	 *
	 * }
	 * @return string  HTML for your schedule.
	 */
	public function your_schedule_shortcode( $atts ) {

		if( !$this->helper->user_is_allowed() ) {
			return $this->show_login_form();
		}

		// Attributes
		extract( shortcode_atts(
				array(
					'employee' => '',
					'begin'    => '',
                    'end'      => '',
					'type'     => '',
					'status'   => '',
					'location' => '',
					'nav'      => 'on'
				), $atts )
		);

		$week = $this->get_week_days( $atts );

		if ( '' !== $begin ) {
			$nav = 'off';
		}

		if ( '' == $employee ) {
			$employee = get_current_user_id();
		}

		// collect all the shifts
		foreach ( $week as $day => $shifts ) {
			$args = $this->make_query_args( $atts, $day, $employee );

			$your_schedule_query = new WP_Query( $args );
			$i                   = 0;
			if ( $your_schedule_query->have_posts() ) :
				while ( $your_schedule_query->have_posts() ) : $your_schedule_query->the_post();

					$id                              = get_the_id();
					$week[ $day ][ $i ]['id']        = $id;
					$week[ $day ][ $i ]['permalink'] = get_the_permalink();
					$statuses                        = get_the_terms( $id, 'shift_status' );
					if ( is_array( $statuses ) ) {
						foreach ( $statuses as $shift_status ) {
							$week[ $day ][ $i ]['status'] = $shift_status->slug;
							$color                        = get_tax_meta( $shift_status->term_id, 'status_color' );
							$week[ $day ][ $i ]['color']  = $color;
						}
					}
					$jobs = get_posts( array(
						'connected_type'   => 'shifts_to_jobs',
						'post-type'        => 'job',
						'connected_items'  => $id,
						'nopaging'         => true,
						'suppress_filters' => false
					) );
					if ( empty( $jobs ) ) {
						$week[ $day ][ $i ]['job']     = __( 'No job assigned', 'employee-scheduler' );
						$week[ $day ][ $i ]['joblink'] = '#';
					} else {
						foreach ( $jobs as $job ) {
							$week[ $day ][ $i ]['job']     = $job->post_title;
							$week[ $day ][ $i ]['joblink'] = site_url() . "/job/" . $job->post_name;
							$week[ $day ][ $i ]['jobeditlink'] = get_edit_post_link( $job->ID );
						}
					}
					$i ++;
				endwhile;
			endif;
			wp_reset_postdata();

		}

		// collect all the jobs
		$job_array = array();
		foreach ( $week as $day => $shifts ) {
			foreach ( $shifts as $shift ) {
				if ( isset( $shift['job'] ) ) {
					$job_array[] = $shift['job'];
				}
			}
		}
		// take out all the duplicates
		$job_array = array_unique( $job_array );

		// display table
		if ( 'off' == $nav ) {
			$class = 'class="wp-list-table widefat fixed posts striped"';
		} else {
			$class = '';
		}

		ob_start();
		include 'partials/shortcode-your-schedule.php';

		return ob_get_clean();

		$your_schedule = apply_filters( 'shiftee_filter_your_schedule', $your_schedule );

		return $your_schedule;
	}

	/**
	 * Output buffer.
	 *
	 * Add output buffer so that when an employee saves their profile, we can redirect to show them their updated profile.
	 *
	 * @since 1.3
	 */
	function output_buffer() {
		// @todo - there has got to be a better way to do this
		ob_start();
	}

	/**
	 * Employee Profile Shortcode.
	 *
	 * [employee_profile] lets employees edit some of their profile information.
	 *
	 * @see http://wordpress.stackexchange.com/questions/9775/how-to-edit-a-user-profile-on-the-front-end
	 *
	 * @since 1.0
	 * @return string HTML to display profile form.
	 */
	public function employee_profile_shortcode() {

		if( !$this->helper->user_is_allowed() ) {
			return $this->show_login_form();
		}

		global $current_user;

		$error = array();
		/* If profile was saved, update profile. */
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

			$this->save_user_profile();

			/* Redirect so the page will show updated info.*/
			if ( count( $error ) == 0 ) {
				//action hook for plugins and extra fields saving
				do_action( 'edit_user_profile_update', $current_user->ID );
				wp_redirect( get_permalink() );
				exit;
			}
		}

		ob_start();
		include 'partials/shortcode-employee-profile.php';

		return ob_get_clean();

		return $profile;

	}

	/**
	 * When an employee edits their profile, save the information
	 *
	 * @since 2.0.0
	 */
	private function save_user_profile() {

		global $current_user;

		/* Update user password. */
		if ( ! empty( $_POST['pass1'] ) && ! empty( $_POST['pass2'] ) ) {
			if ( $_POST['pass1'] == $_POST['pass2'] ) {
				wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
			} else {
				$error[] = __( 'The passwords you entered do not match.  Your password was not updated.', 'profile' );
			}
		}

		/* Update user information. */
		if ( ! empty( $_POST['url'] ) ) {
			update_user_meta( $current_user->ID, 'user_url', esc_url( $_POST['url'] ) );
		}
		if ( ! empty( $_POST['email'] ) ) {
			if ( ! is_email( esc_attr( $_POST['email'] ) ) ) {
				$error[] = __( 'The Email you entered is not valid.  please try again.', 'profile' );
			} elseif ( email_exists( esc_attr( $_POST['email'] ) ) != $current_user->id ) {
				$error[] = __( 'This email is already used by another user.  try a different one.', 'profile' );
			} else {
				wp_update_user( array( 'ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] ) ) );
			}
		}

		if ( ! empty( $_POST['first-name'] ) ) {
			update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
		}
		if ( ! empty( $_POST['last-name'] ) ) {
			update_user_meta( $current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
		}
		if ( ! empty( $_POST['description'] ) ) {
			update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );
		}
		if ( ! empty( $_POST['address'] ) ) {
			update_user_meta( $current_user->ID, 'address', esc_attr( $_POST['address'] ) );
		}
		if ( ! empty( $_POST['city'] ) ) {
			update_user_meta( $current_user->ID, 'city', esc_attr( $_POST['city'] ) );
		}
		if ( ! empty( $_POST['state'] ) ) {
			update_user_meta( $current_user->ID, 'state', esc_attr( $_POST['state'] ) );
		}
		if ( ! empty( $_POST['zip'] ) ) {
			update_user_meta( $current_user->ID, 'zip', esc_attr( $_POST['zip'] ) );
		}
		if ( ! empty( $_POST['phone'] ) ) {
			update_user_meta( $current_user->ID, 'phone', esc_attr( $_POST['phone'] ) );
		}

		do_action( 'shiftee_save_additional_user_profile_fields', $current_user->ID );
	}

	/**
	 * Today shortcode.
	 *
	 * [today] shows the currently logged-in employee the shift(s) they are scheduled to work today.
	 *
	 * @since 1.0
	 *
	 * @return string HTML to display today's shifts.
	 */
	function today_shortcode() {

		if( !$this->helper->user_is_allowed() ) {
			return $this->show_login_form();
		}

		$viewer = wp_get_current_user();
        $now = current_time( 'timestamp' );
		$args = array(
			'post_type'       => 'shift',
			'posts_per_page'  => -1,
			'order'           => 'DESC',
			'meta_key'        => '_shiftee_shift_start',
			'orderby'         => 'meta-value',
			'meta_query'      => array(
				$this->helper->date_meta_query( current_time( $now, false ) )
			),
			'connected_type'  => 'shifts_to_employees',
			'connected_items' => $viewer->ID,

		);

		$todayquery = new WP_Query( $args );

		ob_start();
		include 'partials/shortcode-today.php';

		return apply_filters( 'shiftee_today_shortcode', ob_get_clean() );

		wp_reset_postdata();

	}

	/**
	 * Extra Work shortcode.
	 *
	 * [extra_work] shortcode displays a form where employees can record work they did that was not a scheduled shift.
	 *
	 * @since 1.0
	 *
	 * @return string HTML to display extra work form.
	 */
	public function extra_work_shortcode() {

		if( !$this->helper->user_is_allowed() ) {
			return $this->show_login_form();
		}

		$message = '';

		if ( isset( $_POST['shiftee-extra-work'] ) && 'Record Work' == ( $_POST['shiftee-extra-work'] ) ) {
			$message = $this->save_extra_work();
		}

		ob_start();

		echo $message;

		include 'partials/shortcode-extra-work.php';

		return ob_get_clean();

	}

	/**
	 * If the "Extra" shift type has children, display a drop-down menu of those children.
	 *
	 * @return string|void
	 */
	public function extra_type_dropdown() {

		$extratype = get_term_by( 'slug', 'extra', 'shift_type' );
		// if extra has children, show dropdown of children
		$extra_children = get_term_children( $extratype->term_id, 'shift_type' );
		if ( ! empty( $extra_children ) ) {
			$extra_dropdown =
				'<p>
					<label>' . __( 'Type of Work', 'employee-scheduler' ) . '</label>
					<select name="shiftee-shift-type" id="shiftee-shift-type">
						<option value=""> </option>';
			foreach ( $extra_children as $child ) {
				$childterm = get_term_by( 'id', $child, 'shift_type' );
				$extra_dropdown .=
					'<option value="' . esc_attr( $childterm->slug ) . '">' . esc_attr( $childterm->name ) . '</option>';
			}
			$extra_dropdown .= '</select></p>';

			return $extra_dropdown;
		}

		return;
	}

	/**
	 * If there are jobs, display a dropdown field of jobs on the extra work shortcode or expense report shortcode.
	 *
	 * @return string|void
	 */
	public function job_dropdown() {

		$args = array( 'post_type' => 'job', 'posts_per_page' => - 1, 'order' => 'ASC', 'orderby' => 'title' );
		$jobs = get_posts( $args );

		if ( $jobs ) {
			$job_dropdown = '<p id="shiftee-job">
				<label>' . __( 'Job', 'employee-scheduler' ) . '</label>
				<select name="shiftee-job" id="shiftee-job">
					<option value=""> </option>';
			foreach ( $jobs as $job ) {
				$job_dropdown .= '<option value="' . intval( $job->ID ) . '">' . esc_attr( $job->post_title ) . '</option>';
			}
			$job_dropdown .= '</select></p>';

			return $job_dropdown;
		}
	}

	/**
	 * If there are locations, display a dropdown list of locations on the extra work shortcode.
	 *
	 * @return string
	 */
	public function location_dropdown() {

		$locations = get_terms( 'location' );
		if( $locations ) {
			$locations_dropdown = '<p id="shiftee-location">
				<label>' . __( 'Location', 'employee-scheduler' ) . '</label>
				<select name="shiftee-location" id="shiftee-location">
					<option value=""> </option>';
					foreach( $locations as $location ) {
						$locations_dropdown .= '<option value="' . intval( $location->term_id ) . '">' . esc_attr( $location->name ) . '</option>';
					}
				$locations_dropdown .= '</select></p>';


			return $locations_dropdown;
		}

	}

	/**
	 * When an employee fills out the extra work form, save their entry
	 *
	 * @return string
	 */
	private function save_extra_work() {
		if ( !wp_verify_nonce( $_POST['shiftee_extra_work_nonce'], 'shiftee_extra_work' ) ) {
			$message = '<p class="shiftee-failure">' . __( 'Permission Error', 'employee-scheduler' ) . '</p>';
			return $message;
		}

		$current_user = wp_get_current_user();

		if( !$current_user ) {
			$message = '<p class="shiftee-failure">' . __( 'Could not find user account.', 'employee-scheduler' ) . '</p>';
			return $message;
		}

		$username = $current_user->display_name;
		$extrawork = array(
			'post_type'     => 'shift',
			'post_title'    => sprintf( __( 'Extra shift by %s', 'employee-scheduler' ),  $username ),
			'post_status'   => 'publish',
			'post_content'	=> sanitize_text_field( $_POST['shiftee-description'] ),
		);
		$extrashift = wp_insert_post( $extrawork );

		// check whether admins need to approve extra shifts
		if( '1' == $this->options['extra_shift_approval'] ) {
			// mark the shift as pending approval
			wp_set_object_terms( $extrashift, 'pending-approval', 'shift_status' );

		} else {
			// we don't need admin approval, so mark the shift as worked
			wp_set_object_terms( $extrashift, 'worked', 'shift_status' );
		}

		wp_set_object_terms( $extrashift, 'extra', 'shift_type' );

		// also add subcategory, if they selected one from the drop-down
		if( isset( $_POST['shiftee-shift-type'] ) ) {
			wp_set_object_terms( $extrashift, sanitize_text_field( $_POST['shiftee-shift-type'] ), 'shift_type' );
		}
		if( isset( $_POST['shiftee-location'] ) ) {
			wp_set_object_terms( $extrashift, intval( $_POST['shiftee-location'] ), 'location' );
		}
		wp_set_object_terms( $extrashift, 'worked', 'shift_status' );

		add_post_meta( $extrashift, '_shiftee_shift_start', strtotime( $_POST['shiftee-start'] ) );
		add_post_meta( $extrashift, '_shiftee_shift_end', strtotime( $_POST['shiftee-end'] ) );
		add_post_meta( $extrashift, '_shiftee_clock_in', strtotime( $_POST['shiftee-start'] ) );
		add_post_meta( $extrashift, '_shiftee_clock_out', strtotime( $_POST['shiftee-end'] ) );
		add_post_meta( $extrashift, '_shiftee_scheduled_duration', $this->helper->get_shift_duration( $extrashift, 'scheduled', 'hours' ) );
        add_post_meta( $extrashift, '_shiftee_worked_duration', $this->helper->get_shift_duration( $extrashift, 'worked', 'hours' ) );
        add_post_meta( $extrashift, '_shiftee_wage', $this->helper->calculate_shift_wage( $extrashift ) );

		// connect shift to employee
		p2p_type( 'shifts_to_employees' )->connect( $extrashift, $current_user->ID, array(
			'date' => current_time('mysql')
		) );
		// connect shift to job
		if( isset( $_POST['shiftee-job'] ) && $_POST['shiftee-job'] !== ' ' ) {
			p2p_type( 'shifts_to_jobs' )->connect( $extrashift, $_POST['shiftee-job'], array(
				'date' => current_time('mysql')
			) );
		}

		if( $extrashift ) {
			$message = '<p class="shiftee-success">' . __( 'Your extra work has been recorded.  ', 'employee-scheduler' ) . '<a href="' . get_the_permalink( $extrashift ) . '">' . __('View extra work shift', 'employee-scheduler') . '</a></p>';
		} else {
			$message = '<p class="shiftee-failure">' . __( 'Sorry, there was an error recording your work.', 'employee-scheduler' ) . '</p>';
		}

		do_action( 'shiftee_add_extra_work_action', $extrashift, $current_user );

		return $message;
	}

	/**
	 * Record Expense shortcode.
	 *
	 * [record_expense] displays a form where employees can record mileage and expenses.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML to display form.
	 */
	function record_expense_shortcode() {

		if( !$this->helper->user_is_allowed() ) {
			return $this->show_login_form();
		}

		$message = '';

		if( isset( $_POST['shiftee-expense-form'] ) && 'Record Expense' == ( $_POST['shiftee-expense-form'] ) ) {
			$message = $this->add_expense();
		}

		ob_start();

		echo $message;

		include 'partials/shortcode-record-expense.php';

		return ob_get_clean();

	}


	/**
	 * Expense category dropdown.
	 *
	 * Expense category is a hierarchical taxonomy: this displays the top-level expense categories.
	 *
	 * @since 1.0
	 *
	 * @see record_expense_shortcode()
	 *
	 * @return string HTML for dropdown.
	 */
	public function expense_category_dropdown() {
		$dropdown = '';

		// Get all taxonomy terms
		$terms = get_terms('expense_category', array(
				"hide_empty" => false,
				"parent" => 0
			)
		);

		if( isset( $terms ) ) {
			foreach( $terms as $term ) {
				$dropdown .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
				$dropdown .= $this->get_term_children( $term->term_id, 1 );
			}
		}

		return $dropdown;
	}

	/**
	 * Expense category dropdown: child terms.
	 *
	 * Display the children and grandchildren in the expense category dropdown.
	 *
	 * @since 1.0
	 *
	 * @see expense_category_dropdown()
	 *
	 * @param $termid  int  ID of taxonomy term
	 * @param $depth  int  how deep in the hierarchy we are
	 *
	 * @return string HTML for dropdown.
	 */
	public function get_term_children( $termid, $depth ) {

		$children = '';
		$childterms = get_terms('expense_category', array(
				"hide_empty" => false,
				"parent" => $termid
			)
		);

		if( isset( $childterms ) ) {
			$depth++;
			foreach( $childterms as $childterm ) {


				$children .= '<option value="' . $childterm->slug . '"> ';
				for ($i=0; $i < $depth; $i++) {
					$children .= '--';
				}
				$children .= ' ' . $childterm->name . '</option>';
				$children .= $this->get_term_children( $childterm->term_id, $depth );
			}
		}

		return $children;
	}

	/**
	 * Record expense.
	 *
	 * When employee fills out the "record expense" form, save the expense.
	 *
	 * @since 1.0
	 *
	 * @see record_expense_shortcode()
	 *
	 * @return string  Success or failure message.
	 */
	private function add_expense() {

		if ( !wp_verify_nonce( $_POST['shiftee_record_expense_nonce'], 'shiftee_record_expense' ) ) {
			$message = '<p class="shiftee-failure">' . __( 'Permission Error', 'employee-scheduler' ) . '</p>';
			return $message;
		}

		$current_user = wp_get_current_user();

		if( !$current_user ) {
			$message = '<p class="shiftee-failure">' . __( 'Could not find user account.', 'employee-scheduler' ) . '</p>';
			return $message;
		}

		$username = $current_user->display_name;

		$thisexpense = array(
			'post_type'     => 'expense',
			'post_title'    => sprintf( __( 'Expense reported by %s', 'employee-scheduler' ),  $username ),
			'post_status'   => 'publish',
			'post_content'	=> sanitize_text_field( $_POST['shiftee-expense-description'] ),
		);
		$newexpense = wp_insert_post( $thisexpense );

		if( isset( $_POST['shiftee-expense-date'] ) ) {
			add_post_meta( $newexpense, '_shiftee_date', strtotime( $_POST['shiftee-expense-date'] ) );
		}

		if( isset( $_POST['shiftee-expense-amount'] ) ) {
			add_post_meta( $newexpense, '_shiftee_amount', floatval( $_POST['shiftee-expense-amount'] ) );
		}

		if( isset( $_POST['shiftee-expense-type'] ) ) {
			wp_set_object_terms( $newexpense, sanitize_text_field( $_POST['shiftee-expense-type'] ), 'expense_category' );
		}

		//attach image
		if( isset( $_FILES['shiftee-expense-receipt'] ) && is_array( $_FILES['shiftee-expense-receipt'] ) && '' !== $_FILES['shiftee-expense-receipt']['name'] ) {
			$upload = wp_upload_bits( $_FILES['shiftee-expense-receipt']['name'], null, file_get_contents( $_FILES['shiftee-expense-receipt']['tmp_name'] ) );

			$wp_filetype = wp_check_filetype( basename( $upload['file'] ), null );

			$wp_upload_dir = wp_upload_dir();

			$attachment = array(
				'guid'           => $wp_upload_dir['baseurl'] . _wp_relative_upload_path( $upload['file'] ),
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $upload['file'], $newexpense );

			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			update_post_meta( $newexpense, '_thumbnail_id', $attach_id );
		}


		// connect shift to employee
		p2p_type( 'expenses_to_employees' )->connect( $newexpense, $current_user->ID, array(
			'date' => current_time('mysql')
		) );
		// connect shift to job
		if( isset( $_POST['shiftee-job'] ) && $_POST['shiftee-job'] !== ' ') {
			p2p_type( 'expenses_to_jobs' )->connect( $newexpense, intval( $_POST['shiftee-job'] ), array(
				'date' => current_time('mysql')
			) );
		}

		if( $newexpense ) {
			$message = '<p class="shiftee-success">' . __( 'Your expense has been recorded.', 'employee-scheduler' ) . '</p>';
		} else {
			$message = '<p class="shiftee-failure">' . __( 'Sorry, there was an error recording your expense.', 'employee-scheduler') . '</p>';
		}

		do_action( 'shiftee_add_expense_action' );

		return $message;
	}
}

