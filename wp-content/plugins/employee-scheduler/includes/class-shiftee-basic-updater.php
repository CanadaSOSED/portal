<?php

/**
 * Define the update functionality
 *
 * Make updates to the database when the user updates the plugin
 *
 * @link       http://ran.ge
 * @since      2.1.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 */

/**
 * Define the update functionality.
 *
 * Make updates to the database when the user updates the plugin
 *
 * @since      2.1.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic_Updater {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
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
	 * @var      string    options    The helper class.
	 */
	private $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$helper = new Shiftee_Helper();
		$this->helper = $helper;
		$this->options = $helper->shiftee_options();

	}


	/**
	 * Check whether we need to update
	 */
	public function check_for_updates() {

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'shiftee-upgrades' )
			return; // Don't show notices on the upgrades page

		$db_version = $this->options['db_version'];

		// check if we need the 2.1.0 update
		if( version_compare( $db_version, '2.1.0', '<' ) ) { ?>
			<div class="error">
				<p>
					<?php _e( 'Shiftee needs to update your database so you can use the latest features!  Shiftee will not work properly without this update.', 'employee-scheduler' ); ?>
				</p>
				<p>
					<strong><a href="index.php?page=shiftee-upgrades&shiftee-upgrade=upgrade_shift_meta"><?php _e( 'Update Shiftee now!', 'employee-scheduler' ); ?></a></strong>
				</p>
			</div>
		<?php }

	}

	/**
	 * AJAX function to upgrade meta data from old WP Alchemy meta fields to new CMB2 meta fields
     *
     * @since 2.1.0
	 */
	public function upgrade_shift_meta() {

		if ( !wp_verify_nonce( $_POST['nonce'], 'shiftee_upgrade_shift_meta' ) ) {
			$results = 'error';
			die( $results );
		}

		$args = array(
		    'post_type' => 'shift',
		    'posts_per_page' => 100,
		);

		$current_step = $this->options['shiftee_meta_update_last_step'];
		if( isset( $current_step ) && '0' !== $current_step ) {
			$args['offset'] = $current_step;
		}

		$options = get_option( 'wpaesm_options' );

		$options['shiftee_meta_update_last_step'] = 'wtf';
		update_option( 'wpaesm_options', $options );

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) : $the_query->the_post();
				$this->convert_start( get_the_id() );
				$this->convert_end( get_the_id() );
				$this->convert_clockin( get_the_id() );
				$this->convert_clockout( get_the_id() );
				$this->convert_location_in( get_the_id() );
				$this->convert_location_out( get_the_id() );
				$this->convert_notify( get_the_id() );
				$this->convert_shiftnote( get_the_id() );
				$this->convert_employeenotes( get_the_id() );
				$this->add_hidden_fields( get_the_id() );
				$this->convert_shift_history( get_the_id() );
				$this->convert_invoice( get_the_id() );
				$this->delete_shift_meta_fields( get_the_id() );
			endwhile;

			// update the database to record what step we're on
			$options = get_option( 'wpaesm_options' );

			$options['shiftee_meta_update_last_step'] = intval( $current_step ) + 100;
			update_option( 'wpaesm_options', $options );

			$results = ( string )( intval( $current_step ) + 100 );
			die( $results );

		} else {
		    $this->update_expenses();
			$this->update_on_demand_options( get_the_id() );

			$options = get_option( 'wpaesm_options' );
			unset( $options['shiftee_meta_update_last_step'] );
			$options['db_version'] = '2.1.0';
			update_option( 'wpaesm_options', $options );

			$results = 'finished';
			die( $results );
		}

		wp_reset_postdata();

		$results = 'error';

		die( $results );
	}

	/**
     * Convert the start date meta field to the new format
     *
     * @since 2.1.0
     *
	 * @param $shift_id
	 */
	public function convert_start( $shift_id ) {
		$date = get_post_meta( $shift_id, '_wpaesm_date', true );
		$start = get_post_meta( $shift_id, '_wpaesm_starttime', true );

		if( $date && '' !== $date && $start && '' !== $start ) {
			$time = strtotime( $start . $date );
			add_post_meta( $shift_id, '_shiftee_shift_start', $time );
		}
	}

	/**
	 * Convert the end date meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_end( $shift_id ) {
		$date = get_post_meta( $shift_id, '_wpaesm_date', true );
		$start = get_post_meta( $shift_id, '_wpaesm_endtime', true );

		if( $date && '' !== $date && $start && '' !== $start ) {
			$time = strtotime( $start . $date );
			add_post_meta( $shift_id, '_shiftee_shift_end', $time );
		}
	}

	/**
	 * Convert the clock in meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_clockin( $shift_id ) {
		$date = get_post_meta( $shift_id, '_wpaesm_date', true );
		$start = get_post_meta( $shift_id, '_wpaesm_clockin', true );

		if( $date && '' !== $date && $start && '' !== $start ) {
			$time = strtotime( $start . $date );
			add_post_meta( $shift_id, '_shiftee_clock_in', $time );
		}
	}

	/**
	 * Convert the clock out meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_clockout( $shift_id ) {
		$date = get_post_meta( $shift_id, '_wpaesm_date', true );
		$start = get_post_meta( $shift_id, '_wpaesm_clockout', true );

		if( $date && '' !== $date && $start && '' !== $start ) {
			$time = strtotime( $start . $date );
			add_post_meta( $shift_id, '_shiftee_clock_out', $time );
		}
	}

	/**
	 * Convert the location in meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
    public function convert_location_in( $shift_id ) {
	    $location_in = get_post_meta( $shift_id, '_wpaesm_location_in', true );
	    if( isset( $location_in ) && '' !== $location_in ) {
		    add_post_meta( $shift_id, '_shiftee_location_clock_in', $location_in );
	    }
    }

	/**
	 * Convert the location out meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_location_out( $shift_id ) {
		$location_out = get_post_meta( $shift_id, '_wpaesm_location_out', true );
		if( isset( $location_out ) && '' !== $location_out ) {
			add_post_meta( $shift_id, '_shiftee_location_clock_out', $location_out );
		}
	}

	/**
	 * Convert the employee notification meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_notify( $shift_id ) {
		$notify = get_post_meta( $shift_id, '_wpaesm_notify', true );
		if( $notify && '1' == $notify ) {
			add_post_meta( $shift_id, '_shiftee_notify_employee', 'on' );
		}
	}

	/**
	 * Convert the shift admin note meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_shiftnote( $shift_id ) {
		$note = get_post_meta( $shift_id, '_wpaesm_shiftnotes', true );
		if( $note && '' !== $note ) {
			add_post_meta( $shift_id, '_shiftee_admin_note', $note );
		}
	}

	/**
	 * Convert the employee notes meta field to the new format
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function convert_employeenotes( $shift_id ) {
		$notes = get_post_meta( $shift_id, '_wpaesm_employeenote', true );
		if( is_array( $notes ) && !empty( $notes ) ) {
			foreach( $notes as $note ) {
				$note['notedate'] = strtotime( $note['notedate'] );
			}
			add_post_meta( $shift_id, '_shiftee_shift_notes', $notes );
		}
		delete_post_meta( $shift_id, '_shiftee_shift_notes');
		add_post_meta( $shift_id, '_shiftee_shift_notes', $notes );
	}

	/**
	 * Add in some hidden fields: worked and scheduled duration, wage
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function add_hidden_fields( $shift_id ) {
		$worked_duration = $this->helper->get_shift_duration( $shift_id, 'worked', 'hours' );
		if( $worked_duration && '' !== $worked_duration ) {
			add_post_meta( $shift_id, '_shiftee_worked_duration', $worked_duration );
		}

		$scheduled_duration = $this->helper->get_shift_duration( $shift_id, 'scheduled', 'hours' );
		if( $scheduled_duration && '' !== $scheduled_duration ) {
			add_post_meta( $shift_id, '_shiftee_scheduled_duration', $scheduled_duration );
		}

		$wage = $this->helper->calculate_shift_wage( $shift_id );
		if( $wage && '' !== $wage ) {
			add_post_meta( $shift_id, '_shiftee_wage', $wage );
		}
	}

	/**
     * Convert the shift history field to the new format
     *
     * @since 2.1.0
     *
	 * @param $shift_id
	 */
	public function convert_shift_history( $shift_id ) {
		$events = get_post_meta( $shift_id, '_wpaesp_history', true );
		if( is_array( $events ) && !empty( $events ) ) {
			foreach( $events as $event ) {
			    if( isset( $event['notedate'] ) ) {
				    $event['date'] = strtotime( $event['notedate'] );
			    }
			}
			add_post_meta( $shift_id, '_shiftee_shift_assignment_history', $events );
		}
		delete_post_meta( $shift_id, '_shiftee_shift_assignment_history');
		add_post_meta( $shift_id, '_shiftee_shift_assignment_history', $events );
    }

    public function convert_invoice( $shift_id ) {
	    $invoice = get_post_meta( $shift_id, '_wpaesm_invoice', true );
	    if( isset( $invoice ) && '' !== $invoice ) {
		    add_post_meta( $shift_id, '_shiftee_od_invoice', $invoice );
        }
    }

	/**
	 * Delete the old meta data
	 *
	 * @since 2.1.0
	 *
	 * @param $shift_id
	 */
	public function delete_shift_meta_fields( $shift_id ) {
		delete_post_meta( $shift_id, '_wpaesm_starttime' );
		delete_post_meta( $shift_id, '_wpaesm_endtime' );
		delete_post_meta( $shift_id, '_wpaesm_clockin' );
		delete_post_meta( $shift_id, '_wpaesm_clockout' );
		delete_post_meta( $shift_id, '_wpaesm_notify' );
		delete_post_meta( $shift_id, '_wpaesm_shiftnotes' );
		delete_post_meta( $shift_id, '_wpaesm_employeenote' );
		delete_post_meta( $shift_id, 'shift_meta_fields' );
		delete_post_meta( $shift_id, '_wpaesm_invoice' );
	}

    public function update_expenses() {
	    $args = array(
		    'post_type' => 'expense',
		    'posts_per_page' => -1,
	    );

	    $the_query = new WP_Query( $args );

	    if ( $the_query->have_posts() ) {
		    while ( $the_query->have_posts() ) : $the_query->the_post();
			    $date = get_post_meta( get_the_id(), '_wpaesm_date', true );
			    add_post_meta( get_the_id(), '_shiftee_date', strtotime( $date ) );
			    delete_post_meta( get_the_id(), '_wpaesm_date' );
			    $amount = get_post_meta( get_the_id(), '_wpaesm_amount', true );
			    add_post_meta( get_the_id(), '_shiftee_amount', $amount );
			    delete_post_meta( get_the_id(), '_wpaesm_amount' );
		    endwhile;
	    }
    }

    public function update_on_demand_options() {
	    $args = array(
            'post_type' => 'qualification_form',
            'posts_per_page' => -1,
        );

	    $the_query = new WP_Query( $args );

	    if ( $the_query->have_posts() ) {
		    while ( $the_query->have_posts() ) : $the_query->the_post();
			    $questions = get_post_meta( get_the_id(), '_shiftee_od_question', true );
			    if( is_array( $questions ) && !empty( $questions ) ) {
			        $i = 0;
				    foreach( $questions as $question ) {
					    if( isset( $question['option'] ) && !empty( $question['option'] ) ) {
						    $new_options = array();
						    foreach( $question['option'] as $option ) {
							    $new_options[] = $option['option'];
						    }
						    $questions[$i]['option'] = $new_options;
						    $i++;
					    }
				    }
			    }
                delete_post_meta( get_the_id(), '_shiftee_od_question' );
                add_post_meta( get_the_id(), '_shiftee_od_question', $questions );

		    endwhile;
	    }
    }

}
