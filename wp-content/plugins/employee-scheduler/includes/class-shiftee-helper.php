<?php

/**
 * Functions and variables used throughout the plugin and its add-ons.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 */

/**
 * Functions and variables used throughout the plugin and its add-ons.
 *
 * @since      2.0.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Helper {

	/**
	 * Retrieve the plugin options, with default options
	 *
	 * This includes defaults for Shiftee Basic and Shiftee
	 *
	 * @since   2.0.0
	 * @return  array
	 */
	public function shiftee_options() {

		$defaultfromname = get_bloginfo( 'name' );
		$defaultfromemail = get_bloginfo( 'admin_email' );

		$defaults = array(
			'notification_from_name' => $defaultfromname,
			'notification_from_email' => $defaultfromemail,
			'notification_subject' => __( 'You have been scheduled for a work shift', 'employee-scheduler' ),
			'admin_notification_email' => $defaultfromemail,
			'admin_notify_note' => '0',
			'admin_notify_clockout' => '0',
			'extra_shift_approval' => '0',
			'geolocation' => '0',
			'week_starts_on' => 'Monday',
			'hours' => 40,
			'shift_hours' => 8,
			'otrate' => 1.5,
			'mileage' => .56,
			'calculate' => 'actual',
			'currency' => 'USD',
			'currency_position' => 'before',
			'pro_license' => '',
			'avoid_conflicts' => 1,
			'notification_email' => '',
			'drop_pick_notification_email' => $defaultfromemail,
			'drop_lock' => 48,
			'self_assign' => 1,
			'allow_drop' => 1,
			'drop_notification' => '',
			'pick_up_notification' => '',
			'pick_up_confirmation' => '',
			'db_version' => '0',
			'shiftee_meta_update_last_step' => 0,
		);
		$options = wp_parse_args( get_option( 'wpaesm_options' ), $defaults );

		return $options;
	}

	/**
	 * Check a user's role.
	 *
	 * Check if a user has a particular role.
	 *
	 * @since 1.0.0
	 *
	 * @link http://docs.appthemes.com/tutorials/wordpress-check-user-role-function/
	 *
	 * @param string  Name of user role.
	 * @param int  ID of user
	 * @return bool True if user has role, false if not.
	 */
	public function check_user_role( $role, $user_id = null ) {

		if ( is_numeric( $user_id ) ) {
			$user = get_userdata( $user_id );
		} else {
			$user = wp_get_current_user();
		}

		if ( empty( $user ) )
			return false;

		return in_array( $role, (array) $user->roles );
	}

	/**
	 * Retrieve information connected to a shift.
	 *
	 * @since 2.0.0
	 *
	 * @param $shift_id
	 * @param $connected_item
	 * @param string $return
	 *
	 * @return bool|false|int
	 */
	public function get_shift_connection( $shift_id, $connected_item, $return = 'ID' ) {

		if( 'employee' == $connected_item ) {
			$users = get_users( array(
				'connected_type' => 'shifts_to_employees',
				'connected_items' => $shift_id,
			) );
			if( empty( $users ) ) {
				return false;
			}
			foreach( $users as $user ) {
				if( 'ID' == $return ) {
					return $user->ID;
				} elseif( 'name' == $return ) {
					return $user->display_name;
				} elseif( 'email' == $return ) {
					return $user->user_email;
				}
			}
		} elseif( 'job' == $connected_item ) {
			$jobs = get_posts( array(
				'connected_type' => 'shifts_to_jobs',
				'connected_items' => $shift_id,
			) );
			if( !empty( $jobs ) ) {
				foreach ( $jobs as $job ) {
					if( 'ID' == $return ) {
						return $job->ID;
					} elseif( 'name' == $return ) {
						return $job->post_title;
					} elseif( 'object' == $return ) {
						return $job;
					}
				}
			}
			wp_reset_postdata();
		}

		return false;

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

		if( 'scheduled' == $time_type ) {
			$start = get_post_meta( $shift_id, '_shiftee_shift_start', true );
			$end = get_post_meta( $shift_id, '_shiftee_shift_end', true );
		} else {
			$start = get_post_meta( $shift_id, '_shiftee_clock_in', true );
			$end = get_post_meta( $shift_id, '_shiftee_clock_out', true );
		}

		if( '' == $start || '' == $end ) {
			return;
		}

		if( date( 'd/m/Y', $start ) == date( 'd/m/Y', $end ) ) {
			$datetime = $this->display_datetime( $start ) . ' - ' . $this->display_datetime( $end, 'time' );
		} else {
			$datetime = $this->display_datetime( $start ) . ' - ' . $this->display_datetime( $end );
		}

		return $datetime;
	}

	/**
	 * Format a timestamp according to user preferences
	 *
	 * @since 2.1.0
	 *
	 * @param $timestamp
	 * @param string $return
	 *
	 * @return false|string
	 */
	public function display_datetime( $timestamp, $return = 'both' ) {

		if( 'date' == $return ) {
			$format = get_option( 'date_format' );
		} elseif( 'time' == $return ) {
			$format = get_option( 'time_format' );
		} else {
			$format = get_option( 'date_format' ) . ', ' . get_option( 'time_format' );
		}

		return date( $format, $timestamp );
	}

	/**
	 * Figure out if the difference between two time intervals is positive or negative
	 *
	 * @see http://stackoverflow.com/questions/8724710/php-datetimediff-results-comparison
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function time_interval_compare( $a, $b ) {

		foreach ($a as $key => $value) {
			// after seconds 's' comes 'invert' and other stuff we do not care about
			// and it means that the date intervals are the same
			if ($key == 'invert') {
				return 0;
			}

			// when the values are the same we can move on
			if ($a->$key == $b->$key) {
				continue;
			}

			// finally a level where we see a difference, return accordingly
			if ($a->$key < $b->$key) {
				return '+ ';
			} else {
				return '- ';
			}
		}
	}

	/**
	 * Calculate a shift duration in hours
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 * @param $scheduled
	 *
	 * @return float|void
	 */
	public function get_shift_duration( $shift_id, $scheduled ) {

		if( 'scheduled' == $scheduled ) {
			$start = get_post_meta( $shift_id, '_shiftee_shift_start', true );
			$stop = get_post_meta( $shift_id, '_shiftee_shift_end', true );
		} elseif( 'worked' == $scheduled ) {
			$start = get_post_meta( $shift_id, '_shiftee_clock_in', true );
			$stop = get_post_meta( $shift_id, '_shiftee_clock_out', true );
		}

		if( !isset( $start ) || '' == $start || !isset( $stop ) || '' == $stop ) {
			return;
		}

		$difference = $stop - $start;

		return ( round( ( $difference / 3600 ), 2 ) );

	}

	/**
	 * Calculate the employee's payment for a shift
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 *
	 * @return mixed|void
	 */
	public function calculate_shift_wage( $shift_id ) {
		// get the number of hours
		$options = $this->shiftee_options();
		if( 'scheduled' == $options['calculate'] ) {
			$hours = get_post_meta( $shift_id, '_shiftee_scheduled_duration' );
		} else {
			$hours = get_post_meta( $shift_id, '_shiftee_worked_duration' );
		}

		if( !$hours || '' == $hours ) {
			return;
		}

		// get the employee's pay rate
		$employee = $this->get_shift_connection( $shift_id, 'employee', 'id' );

		$wage = floatval( get_user_meta( $employee, 'wage', true ) );
		if( '' == $wage ) {
			return;
		}

		$ot_limit = floatval( get_user_meta( $employee, 'shift_hours', true ) );
		if( '' == $ot_limit ) {
			$ot_limit = $options['shift_hours'];
		}

		if( $ot_limit > $hours ) {
			$ot_hours = $ot_limit - $hours;
			$reg_hours = $hours - $ot_hours;
			$shift_pay = ( $reg_hours * $wage ) + ( $reg_hours * $wage * $this->shiftee_options['otrate'] );
		} else {
			$shift_pay = $hours * $wage;
		}

		return $shift_pay;

	}



	/**
	 * Buid meta_query args to search for shifts by date
	 *
	 * @param $timestamp
	 * @param $offset bool whether or not we need to offset the timezone
	 *
	 * @return array
	 */
	public function date_meta_query( $timestamp, $offset = true ) {

		if( $offset ) {
			$day_start = strtotime( 'midnight', $timestamp );
		} else {
			$timezone  = strtotime( get_option( 'gmt_offset' ), $timestamp );
			$day_start = strtotime( 'midnight', $timezone );
		}

		$day_end = strtotime( 'tomorrow', $day_start ) - 1;

		$meta_query = array(
			'key' => '_shiftee_shift_start',
			'value'   => array( $day_start, $day_end ),
			'type'    => 'numeric',
			'compare' => 'BETWEEN',
		);

		return $meta_query;
	}

	/**
	 * Alphabetize a list of names
	 *
	 * @since 2.0.0
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function alphabetize( $a, $b ) {
		return strcmp( $a->user_nicename, $b->user_nicename );
	}


	/**
	 * Check if a user can view this content, and if not, show a login form.
	 *
	 * @since 2.0.0
	 *
	 * @param string $extra_roles
	 * @param bool $echo true to echo, false to return
	 *
	 * @return bool
	 */
	public function user_is_allowed( $extra_roles = '', $echo = false ) {

		if( !is_user_logged_in() ) {
			return false;
		}

		$allowed_roles = apply_filters( 'shiftee_allowed_user_roles', array(
			'employee',
			'administrator',
			'shiftee_manager'
		) );

		if( is_array( $extra_roles ) ) {
			$allowed_roles = array_merge( $allowed_roles, $extra_roles );
		}

		foreach( $allowed_roles as $role ) {
			$allowed = $this->check_user_role( $role );
			if( $allowed ) {
				return true;
			}
		}

		// check if the current user is a customer associated with this post and return true
		if( $this->customer_owns_shift() ) {
			return true;
		}

		return false;

	}

	/**
	 * Check whether a shift belongs to a customer
	 *
	 * @return bool
	 */
	public function customer_owns_shift() {
		// get current user
		$current_user = wp_get_current_user();

		$roles = $current_user->roles;
		if( is_array( $roles ) && !empty( $roles ) ) {
			if( in_array( 'shiftee_customer', $roles ) ) {
				global $post;
				$users = get_users( array(
					'connected_type' => 'shifts_to_customers',
					'connected_items' => $post->ID,
				) );
				if( empty( $users ) ) {
					return false;
				}
				foreach( $users as $user ) {
					if( $user->ID == $current_user->ID ) {
						return true;
					}
				}

			}
		}

		return false;

	}

	/**
	 * Generate an alphabetized drop-down list of employees
	 *
	 * @since 2.0.0
	 *
	 * @return  string
	 */
	public function make_employee_dropdown_options() {

		$employees = apply_filters( 'shiftee_employee_dropdown_list', array_merge( get_users( 'role=employee' ), get_users( 'role=administrator' ) ) );

		$employee_list = array();

		foreach( $employees as $employee ) {
			$employee_list[$employee->ID] = $employee->display_name;
		}

		asort( $employee_list );

		$options = '<option value=""></option>';

		$selected = '';

		foreach( $employee_list as $id => $name ) {

			if( isset( $_POST['employee'] ) ) {
				$selected = selected( $id, $_POST['employee'], false );
			}

			$options .= '<option value="' . $id . '"' . $selected . '>' . $name . '</option>';
		}

		return $options;
	}

	/**
	 * Generate drop-down options for a taxonomy
	 *
	 * @param $taxonomy
	 *
	 * @return string
	 */
	public function make_taxonomy_dropdown_options( $taxonomy ) {

		$options = '<option value=""> </option>';

		$terms = get_terms( $taxonomy, 'hide_empty=0&orderby=name' );

		if( !empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$options .= '<option value="' . $term->slug . '">' . $term->name . '</option>';
			}
		}

		return $options;

	}

	/**
	 *  Get an employee's manager
	 *
	 * @since 2.0.0
	 *
	 * @param $employee  ID of employee
	 * @param $return
	 * @return string
	 */
	public function get_employees_manager( $employee, $return = 'ID' ) {

		$users_manager = new WP_User_Query( array(
			'connected_type' => 'manager_to_employee',
			'connected_items' => $employee,
		) );

		if ( ! empty( $users_manager->results ) ) {
			foreach ( $users_manager->results as $manager ) {
				switch( $return ) {
					case 'ID':
						return $manager->ID;
						break;
					case 'name':
						return $manager->user_nicename;
						break;
					case 'email':
						return $manager->user_email;
						break;
				}
			}
		} else {
			return false;
		}

	}

	/**
	 * Generate a drop-down list of jobs
	 *
	 * @since 2.0.0
	 */
	public function make_job_dropdown_options() {

		$options = '<option value=""></option>';

		$args = array(
			'post_type' => 'job',
			'posts_per_page' => -1,
			'orderby' => 'name',
			'order' => 'asc',
		);

		$job_query = new WP_Query( $args );
		if ( $job_query->have_posts() ) :
			while ( $job_query->have_posts() ) : $job_query->the_post();
				$options .= '<option value="' . get_the_id() . '">' . get_the_title() . '</option>';
			endwhile;
		endif;
		wp_reset_postdata();

		return $options;
	}

	/**
	 * Create a list of a shift's taxonomy terms
	 *
	 * @since 2.0.0
	 *
	 * @param $taxonomy
	 *
	 * @return string
	 */
	public function display_shift_terms( $taxonomy ) {

		$termlist = wp_get_post_terms( get_the_id(), $taxonomy );

		if ( is_wp_error( $termlist ) || ! is_array( $termlist ) || empty( $termlist ) ) {
			return;
		}

		$shift_terms = '<p>';

		$term = get_taxonomy( $taxonomy );

		$shift_terms .= '<strong>' . $term->labels->name . '</strong>: ';

		$term_names = array();
		foreach ( $termlist as $term ) {
			$this_term = $term->name;

			$address = get_tax_meta( $term->term_id, 'location_address' );
			if ( isset( $address ) && '' !== $address ) {
				$this_term .= '<br />' . nl2br( $address ) . '<br />';
			}

			$term_names[] = $this_term;
		}

		$shift_terms .= implode( ', ', $term_names ) . '</p>';

		return $shift_terms;
	}

	/**
	 * Get datetimepicker options to use in localizing scripts
	 *
	 * @return array
	 */
	public function get_datetimepicker_options() {

		$options = $this->shiftee_options();

		$date_format = $this->convert_php_datetime_to_js_datetime( get_option( 'date_format' ) ); // need to convert this to JS-friendly format
		$time_format = $this->convert_php_datetime_to_js_datetime( get_option( 'time_format' ) ); // convert to JS
		$first_day_of_week = $this->convert_weekday_to_number( $options['week_starts_on'] ); // convert to number

		$datetimepicker_options = array(
			'date_format' => $date_format,
			'time_format' => $time_format,
			'first_day_of_week' => $first_day_of_week
		);

		return $datetimepicker_options;
	}

	/**
	 * Convert from PHP's datetime format to JS's datetime format
	 *
	 * @since 2.1.0
	 *
	 * @see http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 *
	 * @param $datetime_format
	 *
	 * @return string
	 */
	public function convert_php_datetime_to_js_datetime( $datetime_format ) {
		$symbols = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			// Year
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => 'tt',
			'A' => 'TT',
			'g' => 'h',
			'G' => 'H',
			'h' => 'hh',
			'H' => 'HH',
			'i' => 'mm',
			's' => 'ss',
			'u' => 'c'
		);

		$jqueryui_format = '';
		$escaping = false;

		for( $i = 0; $i < strlen($datetime_format); $i++ ) {
			$char = $datetime_format[$i];
			if($char === '\\') { // PHP date format escaping character
				$i++;
				if( $escaping ) {
					$jqueryui_format .= $datetime_format[$i];
				} else {
					$jqueryui_format .= '\'' . $datetime_format[$i];
				}
				$escaping = true;
			} else {
				if( $escaping ) {
					$jqueryui_format .= "'"; $escaping = false;
				}
				if( isset( $symbols[$char] ) ) {
					$jqueryui_format .= $symbols[ $char ];
				} else {
					$jqueryui_format .= $char;
				}
			}
		}

		return $jqueryui_format;

	}

	/**
	 * Convert a day of the week to a number
	 *
	 * @param $weekday
	 *
	 * @return int
	 */
	public function convert_weekday_to_number( $weekday ) {
		switch( strtolower( $weekday ) ) {
			case 'sunday':
				$weekday_number = 0;
				break;
			case 'monday':
				$weekday_number = 1;
				break;
			case 'tuesday':
				$weekday_number = 2;
				break;
			case 'wednesday':
				$weekday_number = 3;
				break;
			case 'thursday':
				$weekday_number = 4;
				break;
			case 'friday':
				$weekday_number = 5;
				break;
			case 'saturday':
				$weekday_number = 6;
				break;

		}

		return $weekday_number;
	}

	/**
	 * Display currency, formatted with the correct symbol
	 *
	 * @since 1.9.0
	 *
	 * @param int $number
	 *
	 * @return string
	 */
	public function display_currency( $number ) {

		$symbol = $this->currency_symbol();

		$formatted_currency = '';

		if( $this->currency_symbol_before() ) {
			$formatted_currency .= $symbol . '&nbsp;';
		}

		$formatted_currency .= number_format( ( float )$number, 2, '.', '' );

		if( !$this->currency_symbol_before() ) {
			$formatted_currency .= '&nbsp;' . $symbol;
		}

		return $formatted_currency;

	}

	/**
	 * Determine if the currency symbol goes before or after the number
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function currency_symbol_before() {

		// if option is before = before
		// if option is after = after
		// if option is anything else = before

		$options = $this->shiftee_options();

		if( 'after' == $options['currency_position'] ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get a list of currencies and their codes.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public function currency_list() {
		$currencies = array(
			'AED' => __( 'United Arab Emirates dirham', 'employee-scheduler' ),
			'AFN' => __( 'Afghan afghani', 'employee-scheduler' ),
			'ALL' => __( 'Albanian lek', 'employee-scheduler' ),
			'AMD' => __( 'Armenian dram', 'employee-scheduler' ),
			'ANG' => __( 'Netherlands Antillean guilder', 'employee-scheduler' ),
			'AOA' => __( 'Angolan kwanza', 'employee-scheduler' ),
			'ARS' => __( 'Argentine peso', 'employee-scheduler' ),
			'AUD' => __( 'Australian dollar', 'employee-scheduler' ),
			'AWG' => __( 'Aruban florin', 'employee-scheduler' ),
			'AZN' => __( 'Azerbaijani manat', 'employee-scheduler' ),
			'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'employee-scheduler' ),
			'BBD' => __( 'Barbadian dollar', 'employee-scheduler' ),
			'BDT' => __( 'Bangladeshi taka', 'employee-scheduler' ),
			'BGN' => __( 'Bulgarian lev', 'employee-scheduler' ),
			'BHD' => __( 'Bahraini dinar', 'employee-scheduler' ),
			'BIF' => __( 'Burundian franc', 'employee-scheduler' ),
			'BMD' => __( 'Bermudian dollar', 'employee-scheduler' ),
			'BND' => __( 'Brunei dollar', 'employee-scheduler' ),
			'BOB' => __( 'Bolivian boliviano', 'employee-scheduler' ),
			'BRL' => __( 'Brazilian real', 'employee-scheduler' ),
			'BSD' => __( 'Bahamian dollar', 'employee-scheduler' ),
			'BTC' => __( 'Bitcoin', 'employee-scheduler' ),
			'BTN' => __( 'Bhutanese ngultrum', 'employee-scheduler' ),
			'BWP' => __( 'Botswana pula', 'employee-scheduler' ),
			'BYR' => __( 'Belarusian ruble', 'employee-scheduler' ),
			'BZD' => __( 'Belize dollar', 'employee-scheduler' ),
			'CAD' => __( 'Canadian dollar', 'employee-scheduler' ),
			'CDF' => __( 'Congolese franc', 'employee-scheduler' ),
			'CHF' => __( 'Swiss franc', 'employee-scheduler' ),
			'CLP' => __( 'Chilean peso', 'employee-scheduler' ),
			'CNY' => __( 'Chinese yuan', 'employee-scheduler' ),
			'COP' => __( 'Colombian peso', 'employee-scheduler' ),
			'CRC' => __( 'Costa Rican col&oacute;n', 'employee-scheduler' ),
			'CUC' => __( 'Cuban convertible peso', 'employee-scheduler' ),
			'CUP' => __( 'Cuban peso', 'employee-scheduler' ),
			'CVE' => __( 'Cape Verdean escudo', 'employee-scheduler' ),
			'CZK' => __( 'Czech koruna', 'employee-scheduler' ),
			'DJF' => __( 'Djiboutian franc', 'employee-scheduler' ),
			'DKK' => __( 'Danish krone', 'employee-scheduler' ),
			'DOP' => __( 'Dominican peso', 'employee-scheduler' ),
			'DZD' => __( 'Algerian dinar', 'employee-scheduler' ),
			'EGP' => __( 'Egyptian pound', 'employee-scheduler' ),
			'ERN' => __( 'Eritrean nakfa', 'employee-scheduler' ),
			'ETB' => __( 'Ethiopian birr', 'employee-scheduler' ),
			'EUR' => __( 'Euro', 'employee-scheduler' ),
			'FJD' => __( 'Fijian dollar', 'employee-scheduler' ),
			'FKP' => __( 'Falkland Islands pound', 'employee-scheduler' ),
			'GBP' => __( 'Pound sterling', 'employee-scheduler' ),
			'GEL' => __( 'Georgian lari', 'employee-scheduler' ),
			'GGP' => __( 'Guernsey pound', 'employee-scheduler' ),
			'GHS' => __( 'Ghana cedi', 'employee-scheduler' ),
			'GIP' => __( 'Gibraltar pound', 'employee-scheduler' ),
			'GMD' => __( 'Gambian dalasi', 'employee-scheduler' ),
			'GNF' => __( 'Guinean franc', 'employee-scheduler' ),
			'GTQ' => __( 'Guatemalan quetzal', 'employee-scheduler' ),
			'GYD' => __( 'Guyanese dollar', 'employee-scheduler' ),
			'HKD' => __( 'Hong Kong dollar', 'employee-scheduler' ),
			'HNL' => __( 'Honduran lempira', 'employee-scheduler' ),
			'HRK' => __( 'Croatian kuna', 'employee-scheduler' ),
			'HTG' => __( 'Haitian gourde', 'employee-scheduler' ),
			'HUF' => __( 'Hungarian forint', 'employee-scheduler' ),
			'IDR' => __( 'Indonesian rupiah', 'employee-scheduler' ),
			'ILS' => __( 'Israeli new shekel', 'employee-scheduler' ),
			'IMP' => __( 'Manx pound', 'employee-scheduler' ),
			'INR' => __( 'Indian rupee', 'employee-scheduler' ),
			'IQD' => __( 'Iraqi dinar', 'employee-scheduler' ),
			'IRR' => __( 'Iranian rial', 'employee-scheduler' ),
			'ISK' => __( 'Icelandic kr&oacute;na', 'employee-scheduler' ),
			'JEP' => __( 'Jersey pound', 'employee-scheduler' ),
			'JMD' => __( 'Jamaican dollar', 'employee-scheduler' ),
			'JOD' => __( 'Jordanian dinar', 'employee-scheduler' ),
			'JPY' => __( 'Japanese yen', 'employee-scheduler' ),
			'KES' => __( 'Kenyan shilling', 'employee-scheduler' ),
			'KGS' => __( 'Kyrgyzstani som', 'employee-scheduler' ),
			'KHR' => __( 'Cambodian riel', 'employee-scheduler' ),
			'KMF' => __( 'Comorian franc', 'employee-scheduler' ),
			'KPW' => __( 'North Korean won', 'employee-scheduler' ),
			'KRW' => __( 'South Korean won', 'employee-scheduler' ),
			'KWD' => __( 'Kuwaiti dinar', 'employee-scheduler' ),
			'KYD' => __( 'Cayman Islands dollar', 'employee-scheduler' ),
			'KZT' => __( 'Kazakhstani tenge', 'employee-scheduler' ),
			'LAK' => __( 'Lao kip', 'employee-scheduler' ),
			'LBP' => __( 'Lebanese pound', 'employee-scheduler' ),
			'LKR' => __( 'Sri Lankan rupee', 'employee-scheduler' ),
			'LRD' => __( 'Liberian dollar', 'employee-scheduler' ),
			'LSL' => __( 'Lesotho loti', 'employee-scheduler' ),
			'LYD' => __( 'Libyan dinar', 'employee-scheduler' ),
			'MAD' => __( 'Moroccan dirham', 'employee-scheduler' ),
			'MDL' => __( 'Moldovan leu', 'employee-scheduler' ),
			'MGA' => __( 'Malagasy ariary', 'employee-scheduler' ),
			'MKD' => __( 'Macedonian denar', 'employee-scheduler' ),
			'MMK' => __( 'Burmese kyat', 'employee-scheduler' ),
			'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'employee-scheduler' ),
			'MOP' => __( 'Macanese pataca', 'employee-scheduler' ),
			'MRO' => __( 'Mauritanian ouguiya', 'employee-scheduler' ),
			'MUR' => __( 'Mauritian rupee', 'employee-scheduler' ),
			'MVR' => __( 'Maldivian rufiyaa', 'employee-scheduler' ),
			'MWK' => __( 'Malawian kwacha', 'employee-scheduler' ),
			'MXN' => __( 'Mexican peso', 'employee-scheduler' ),
			'MYR' => __( 'Malaysian ringgit', 'employee-scheduler' ),
			'MZN' => __( 'Mozambican metical', 'employee-scheduler' ),
			'NAD' => __( 'Namibian dollar', 'employee-scheduler' ),
			'NGN' => __( 'Nigerian naira', 'employee-scheduler' ),
			'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'employee-scheduler' ),
			'NOK' => __( 'Norwegian krone', 'employee-scheduler' ),
			'NPR' => __( 'Nepalese rupee', 'employee-scheduler' ),
			'NZD' => __( 'New Zealand dollar', 'employee-scheduler' ),
			'OMR' => __( 'Omani rial', 'employee-scheduler' ),
			'PAB' => __( 'Panamanian balboa', 'employee-scheduler' ),
			'PEN' => __( 'Peruvian nuevo sol', 'employee-scheduler' ),
			'PGK' => __( 'Papua New Guinean kina', 'employee-scheduler' ),
			'PHP' => __( 'Philippine peso', 'employee-scheduler' ),
			'PKR' => __( 'Pakistani rupee', 'employee-scheduler' ),
			'PLN' => __( 'Polish z&#x142;oty', 'employee-scheduler' ),
			'PRB' => __( 'Transnistrian ruble', 'employee-scheduler' ),
			'PYG' => __( 'Paraguayan guaran&iacute;', 'employee-scheduler' ),
			'QAR' => __( 'Qatari riyal', 'employee-scheduler' ),
			'RON' => __( 'Romanian leu', 'employee-scheduler' ),
			'RSD' => __( 'Serbian dinar', 'employee-scheduler' ),
			'RUB' => __( 'Russian ruble', 'employee-scheduler' ),
			'RWF' => __( 'Rwandan franc', 'employee-scheduler' ),
			'SAR' => __( 'Saudi riyal', 'employee-scheduler' ),
			'SBD' => __( 'Solomon Islands dollar', 'employee-scheduler' ),
			'SCR' => __( 'Seychellois rupee', 'employee-scheduler' ),
			'SDG' => __( 'Sudanese pound', 'employee-scheduler' ),
			'SEK' => __( 'Swedish krona', 'employee-scheduler' ),
			'SGD' => __( 'Singapore dollar', 'employee-scheduler' ),
			'SHP' => __( 'Saint Helena pound', 'employee-scheduler' ),
			'SLL' => __( 'Sierra Leonean leone', 'employee-scheduler' ),
			'SOS' => __( 'Somali shilling', 'employee-scheduler' ),
			'SRD' => __( 'Surinamese dollar', 'employee-scheduler' ),
			'SSP' => __( 'South Sudanese pound', 'employee-scheduler' ),
			'STD' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'employee-scheduler' ),
			'SYP' => __( 'Syrian pound', 'employee-scheduler' ),
			'SZL' => __( 'Swazi lilangeni', 'employee-scheduler' ),
			'THB' => __( 'Thai baht', 'employee-scheduler' ),
			'TJS' => __( 'Tajikistani somoni', 'employee-scheduler' ),
			'TMT' => __( 'Turkmenistan manat', 'employee-scheduler' ),
			'TND' => __( 'Tunisian dinar', 'employee-scheduler' ),
			'TOP' => __( 'Tongan pa&#x2bb;anga', 'employee-scheduler' ),
			'TRY' => __( 'Turkish lira', 'employee-scheduler' ),
			'TTD' => __( 'Trinidad and Tobago dollar', 'employee-scheduler' ),
			'TWD' => __( 'New Taiwan dollar', 'employee-scheduler' ),
			'TZS' => __( 'Tanzanian shilling', 'employee-scheduler' ),
			'UAH' => __( 'Ukrainian hryvnia', 'employee-scheduler' ),
			'UGX' => __( 'Ugandan shilling', 'employee-scheduler' ),
			'USD' => __( 'United States dollar', 'employee-scheduler' ),
			'UYU' => __( 'Uruguayan peso', 'employee-scheduler' ),
			'UZS' => __( 'Uzbekistani som', 'employee-scheduler' ),
			'VEF' => __( 'Venezuelan bol&iacute;var', 'employee-scheduler' ),
			'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'employee-scheduler' ),
			'VUV' => __( 'Vanuatu vatu', 'employee-scheduler' ),
			'WST' => __( 'Samoan t&#x101;l&#x101;', 'employee-scheduler' ),
			'XAF' => __( 'Central African CFA franc', 'employee-scheduler' ),
			'XCD' => __( 'East Caribbean dollar', 'employee-scheduler' ),
			'XOF' => __( 'West African CFA franc', 'employee-scheduler' ),
			'XPF' => __( 'CFP franc', 'employee-scheduler' ),
			'YER' => __( 'Yemeni rial', 'employee-scheduler' ),
			'ZAR' => __( 'South African rand', 'employee-scheduler' ),
			'ZMW' => __( 'Zambian kwacha', 'employee-scheduler' ),
		);

		return $currencies;
	}

	/**
	 * Get a list of currency codes and their symbols.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function currency_symbol() {

		$options = $this->shiftee_options();
		$currency = $options['currency'];
		if( !isset( $currency ) || '' == $currency ) {
			$currency = 'USD';
		}

		$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => '&fnof;',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x10da;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'ISK' => 'Kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x43b;&#x432;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;. &#x645;.',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'L',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => '&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'Fr',
			'XCD' => '&#36;',
			'XOF' => 'Fr',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';
		return $currency_symbol;
	}

}
