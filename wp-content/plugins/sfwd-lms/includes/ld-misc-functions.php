<?php
/**
 * Misc functions
 * 
 * @since 2.1.0
 * 
 * @package LearnDash\Misc
 */



/**
 * Add post thumbnail theme support for customn post types
 *
 * @since 2.1.0
 */
function learndash_add_theme_support() {
	if ( ! current_theme_supports( 'post-thumbnails' ) ) {
		add_theme_support( 'post-thumbnails', array( 'sfwd-certificates', 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-assignment', 'sfwd-essays' ) );
	}
}

add_action( 'after_setup_theme', 'learndash_add_theme_support' );



/**
 * Get a Quiz Pro's quiz ID
 *
 * @todo   purpose of this function and how quiz pro id's relate to quizzes
 *
 * @since 2.1.0
 *
 * @param  int $quiz_id  quiz pro id
 * @return int           quiz id
 */

function learndash_get_quiz_id_by_pro_quiz_id( $quiz_id ) {
	global $wpdb;

	//$sql_str = $wpdb->prepare( "SELECT post_id FROM ". $wpdb->postmeta . " WHERE meta_key=%s ", 'quiz_pro_id_' . intval( $quiz_id ) );
	$sql_str = $wpdb->prepare("SELECT post_id FROM ". $wpdb->postmeta ." as postmeta INNER JOIN ". $wpdb->posts ." as posts ON posts.ID=postmeta.post_id
		WHERE posts.post_type = %s AND posts.post_status = %s AND postmeta.meta_key = %s", 'sfwd-quiz', 'publish', 'quiz_pro_id_' . intval( $quiz_id ));
	$quiz_post_id = $wpdb->get_var( $sql_str );
	if ( $quiz_post_id != '' ) {
		return intval($quiz_post_id);
	}
	
	
	//$sql_str = $wpdb->prepare( "SELECT post_id FROM ". $wpdb->postmeta . " WHERE meta_key=%s AND meta_value=%d", 'quiz_pro_id', intval( $quiz_id ) );
	$sql_str = $wpdb->prepare("SELECT post_id FROM ". $wpdb->postmeta ." as postmeta INNER JOIN ". $wpdb->posts ." as posts ON posts.ID=postmeta.post_id
		WHERE posts.post_type = %s AND posts.post_status = %s AND meta_key = %s AND meta_value = %d", 'sfwd-quiz', 'publish', 'quiz_pro_id', intval( $quiz_id ));
	$quiz_post_id = $wpdb->get_var( $sql_str );
	if ( $quiz_post_id != '' ) {
		update_post_meta( intval($quiz_post_id), 'quiz_pro_id_' . intval($quiz_id), intval($quiz_id) );
		return intval($quiz_post_id);
	} 

	// Because we seem to have a mix of int and string values when these are serialized the format to look for end up being somewhat kludge-y. 
	$quiz_id_str = sprintf('%s', intval($quiz_id));
	$quiz_id_len = strlen($quiz_id_str);

	$like_i = 'sfwd-quiz_quiz_pro";i:'. intval($quiz_id) .';';
	$like_s = '"sfwd-quiz_quiz_pro";s:'. $quiz_id_len .':"'. $quiz_id_str .'"';

	//$sql_str = $wpdb->prepare( "SELECT post_id FROM ". $wpdb->postmeta . " WHERE meta_key=%s AND meta_value LIKE '". $like_i ."' OR meta_value LIKE '". $like_s ."'", '_sfwd-quiz' );
	
	// Using REGEX because it is slightly faster then OR on text fields pattern search
	$sql_str = $wpdb->prepare( "SELECT post_id FROM ". $wpdb->postmeta . " as postmeta INNER JOIN ". $wpdb->posts ." as posts ON posts.ID=postmeta.post_id WHERE posts.post_type = %s AND posts.post_status = %s AND postmeta.meta_key=%s AND postmeta.meta_value REGEXP '". $like_i ."|". $like_s ."'", 'sfwd-quiz', 'publish', '_sfwd-quiz' );
	$quiz_post_id = $wpdb->get_var( $sql_str );
	if ( $quiz_post_id != '' ) {
		update_post_meta( intval($quiz_post_id), 'quiz_pro_id_' . intval($quiz_id), intval($quiz_id) );
		update_post_meta( intval($quiz_post_id), 'quiz_pro_id', intval($quiz_id) );
		return $quiz_post_id;
	} 
}


/**
 * Get LearnDash setting for a post
 * 
 * @since 2.1.0
 * 
 * @param  id|obj $post    
 * @param  string $setting 
 * @return string value for requested setting
 */
function learndash_get_setting( $post, $setting = null ) {

	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	} else {
		if ( empty( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			return null;
		}
	}
	
	if ($post instanceof WP_Post) {

		if ( $setting == 'lesson' ) {
			return learndash_get_lesson_id( $post->ID ); 
		}

		if ( $setting == 'course' ) {
			//return get_post_meta( $post->ID, 'course_id', true ); 
			return learndash_get_course_id( $post->ID ); 
		}

		$meta = get_post_meta( $post->ID, '_' . $post->post_type, true );
		if ( ( !empty( $meta ) ) && ( is_array( $meta ) ) ) {
			if ( empty( $setting ) ) {
				$settings = array();
				foreach ( $meta as $k => $v ) {
					$settings[ str_replace( $post->post_type.'_', '', $k ) ] = $v;
				}
				return $settings;
			} else {
				if ( isset( $meta[ $post->post_type.'_'.$setting ] ) ) {
					return $meta[ $post->post_type.'_'.$setting ]; 
				} else {
					return ''; 
				}
			}
		} else {
			return ''; 
		}
	}
}



/**
 * Get options for a particular post type and setting
 * 
 * @since 2.1.0
 * 
 * @param  string $post_type
 * @param  string $setting
 * @return array|string 	options requested
 */
function learndash_get_option( $post_type, $setting = '' ) {
	$return = array();
	
	$options = get_option( 'sfwd_cpt_options' );

	// In LD v2.4 we moved all the settings to the new Settings API. Because of this we need to merge the value(s)
	// into the legacy values but keep in mind other add-ons might be extending the $post_args sections
	if ( $post_type == 'sfwd-lessons' ) {
		if ( $options === false ) $options = array();
		if ( !isset( $options['modules'] ) ) $options['modules'] = array();
		if ( !isset( $options['modules'][ $post_type.'_options'] ) ) $options['modules'][ $post_type.'_options'] = array();
		
		$settings_fields = LearnDash_Settings_Section::get_section_settings_all('LearnDash_Settings_Section_Lessons_Display_Order');
		if ( ( !empty( $settings_fields ) ) && ( is_array( $settings_fields ) ) ) {
			foreach( $settings_fields as $key => $val ) {
				$options['modules'][ $post_type . '_options'][$post_type .'_'. $key ] = $val;
			}
		}
	}

	if ( ( empty( $setting ) )  && ( !empty( $options['modules'][ $post_type.'_options'] ) ) ) {
		foreach ( $options['modules'][ $post_type.'_options'] as $key => $val ) {
			$return[str_replace( $post_type.'_', '', $key )] = $val;
		}

		return $return;
	}

	if ( ! empty( $options['modules'][ $post_type.'_options'][ $post_type.'_'.$setting] ) ) {
		return $options['modules'][ $post_type.'_options'][ $post_type.'_'.$setting];
	} else {
		return '';
	}	
}



/**
 * Update LearnDash setting for a post
 *
 * @since 2.1.0
 * 
 * @param  id|obj $post    
 * @param  string $setting 
 * @param  string $value
 * @return bool   if update was successful         
 */
function learndash_update_setting( $post, $setting, $value ) {
	$return = false;

	if ( empty( $setting) ) {
		return $return;
	}

	// Were we sent a post ID?
	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	} 
	
	// Ensure we have a post object or type WP_Post!
	if ($post instanceof WP_Post) {  
	
		$meta = get_post_meta( $post->ID, '_'.$post->post_type, true );
		if ( !is_array( $meta )) $meta = array( $meta );
		$meta[ $post->post_type.'_'.$setting] = $value;

		if ( $setting == 'course' ) {
			$value = intval( $value );
			$meta[ $post->post_type.'_'.$setting] = $value;
			if ( !empty( $value ) ) 
				update_post_meta( $post->ID, 'course_id', $value );
			else
				delete_post_meta( $post->ID, 'course_id' );
		} else if ( $setting == 'lesson' ) {
			$value = intval( $value );
			$meta[ $post->post_type.'_'.$setting] = $value;
			if ( !empty( $value ) ) 
				update_post_meta( $post->ID, 'lesson_id', $value );
			else
				delete_post_meta( $post->ID, 'lesson_id' );
		}

		$return = update_post_meta( $post->ID, '_'.$post->post_type, $meta );
	}
	
	return $return;
}



if ( ! function_exists( 'sfwd_lms_get_post_options' ) ) {

	/**
	 * Set up wp query args for the post type that are saved in options
	 * 
	 * @param  string $post_type
	 * @return array  wp query arguments
	 */
	function sfwd_lms_get_post_options( $post_type ) {
		global $sfwd_lms;
	
		// Set our default options

		$ret = array( 
			'order' 			=> 	'ASC', 
			'orderby' 			=> 	'date', 
			'posts_per_page' 	=> 	get_option('posts_per_page')
		);

		if ( ( !empty( $post_type ) ) && ( isset( $sfwd_lms->post_types[ $post_type ] ) ) ) {
			$cpt = $sfwd_lms->post_types[ $post_type ];
			if ( ( $cpt ) && ( $cpt instanceof SFWD_CPT_Instance ) ) {
				$prefix = $cpt->get_prefix();
				$options = $cpt->get_current_options();

				if ((!empty($prefix)) && (!empty($options))) {
					foreach ( $ret as $k => $v ) {
						if ( ! empty( $options["{$prefix}{$k}"] ) ) {
							$ret[ $k ] = $options["{$prefix}{$k}"];
						}
					}
				}
				
				if ( $post_type == 'sfwd-lessons' ) {
					$settings_fields = LearnDash_Settings_Section::get_section_settings_all('LearnDash_Settings_Section_Lessons_Display_Order');
					if ( ( !empty( $settings_fields ) ) && ( is_array( $settings_fields ) ) ) {
						$ret = wp_parse_args( $settings_fields, $ret );
					}
				}
				
			}
		}

		return $ret;
	}
}



/**
 * Output LearnDash Payment buttons
 * 
 * @since 2.1.0
 *
 * @uses learndash_get_function()
 * @uses sfwd_lms_has_access()
 * 
 * @param  id|obj 	$course course id or WP_Post course object
 * @return string   output of payment buttons
 */
function learndash_payment_buttons( $course ) {

	if ( is_numeric( $course ) ) {
		$course_id = $course;
		$course = get_post( $course_id );
	} else if ( ! empty( $course->ID ) ) {
		$course_id = $course->ID;
	} else {
		return '';
	}

	$user_id = get_current_user_id();

	if ( $course->post_type != 'sfwd-courses' ) {
		return '';
	}

	$meta = get_post_meta( $course_id, '_sfwd-courses', true );
	$course_price_type = @$meta['sfwd-courses_course_price_type'];
	$course_price = @$meta['sfwd-courses_course_price'];
	$course_no_of_cycles = @$meta['sfwd-courses_course_no_of_cycles'];
	$course_price = @$meta['sfwd-courses_course_price'];
	$custom_button_url = @$meta['sfwd-courses_custom_button_url'];

	// format the Course price to be proper XXX.YY no leading dollar signs or other values. 
	if (( $course_price_type == 'paynow' ) || ( $course_price_type == 'subscribe' )) {
		if ( $course_price != '' ) {
			$course_price = preg_replace( "/[^0-9.]/", '', $course_price );
			$course_price = number_format( floatval( $course_price ), 2, '.', '' );
		}
	}

	//$courses_options = learndash_get_option( 'sfwd-courses' );

	//if ( ! empty( $courses_options ) ) {
	//	extract( $courses_options );
	//}

	$paypal_settings = LearnDash_Settings_Section::get_section_settings_all( 'LearnDash_Settings_Section_PayPal' );
	if ( ! empty( $paypal_settings ) ) {
		$paypal_settings['paypal_sandbox'] = $paypal_settings['paypal_sandbox'] == 'yes' ? 1 : 0;
	}

	if ( sfwd_lms_has_access( $course->ID, $user_id ) ) {
		return '';
	}

	$button_text = LearnDash_Custom_Label::get_label( 'button_take_this_course' );

	if ( ! empty( $course_price_type ) && $course_price_type == 'closed' ) {

		if ( empty( $custom_button_url) ) {
			$custom_button = '';
		} else {
			if ( ! strpos( $custom_button_url, '://' ) ) {
				$custom_button_url = 'http://'.$custom_button_url;
			}

			$custom_button = '<a class="btn-join" href="'.$custom_button_url.'" id="btn-join">'. $button_text .'</a>';
		}

		$payment_params = array(
			'custom_button_url' => $custom_button_url,
			'post' => $course
		);

		/**
		 * Filter a closed course payment button
		 * 
		 * @since 2.1.0
		 * 
		 * @param  string  $custom_button       
		 */
		return 	apply_filters( 'learndash_payment_closed_button', $custom_button, $payment_params );

	} else if ( ! empty( $course_price ) ) {
		include_once( 'vendor/paypal/enhanced-paypal-shortcodes.php' );

		$paypal_button = '';

		if ( ! empty( $paypal_settings['paypal_email'] ) ) {

			$post_title = str_replace(array('[', ']'), array('', ''), $course->post_title);
			
			if ( empty( $course_price_type ) || $course_price_type == 'paynow' ) {
				$shortcode_content = do_shortcode( '[paypal type="paynow" amount="'. $course_price .'" sandbox="'. $paypal_settings['paypal_sandbox'] .'" email="'. $paypal_settings['paypal_email'] .'" itemno="'. $course->ID .'" name="'. $post_title .'" noshipping="1" nonote="1" qty="1" currencycode="'. $paypal_settings['paypal_currency'] .'" rm="2" notifyurl="'. $paypal_settings['paypal_notifyurl'] .'" returnurl="'. $paypal_settings['paypal_returnurl'] .'" cancelurl="'. $paypal_settings['paypal_cancelurl'] .'" imagewidth="100px" pagestyle="paypal" lc="'. $paypal_settings['paypal_country'] .'" cbt="'. esc_html__( 'Complete Your Purchase', 'learndash' ) . '" custom="'. $user_id. '"]' );
				if (!empty( $shortcode_content ) ) {
					$paypal_button = wptexturize( '<div class="learndash_checkout_button learndash_paypal_button">'. $shortcode_content .'</div>');
				}
				
			} else if ( $course_price_type == 'subscribe' ) {
				$course_price_billing_p3 = get_post_meta( $course_id, 'course_price_billing_p3',  true );
				$course_price_billing_t3 = get_post_meta( $course_id, 'course_price_billing_t3',  true );
				$srt = intval( $course_no_of_cycles );
				
				$shortcode_content = do_shortcode( '[paypal type="subscribe" a3="'. $course_price .'" p3="'. $course_price_billing_p3 .'" t3="'. $course_price_billing_t3 .'" sandbox="'. $paypal_settings['paypal_sandbox'] .'" email="'. $paypal_settings['paypal_email'] .'" itemno="'. $course->ID .'" name="'. $post_title .'" noshipping="1" nonote="1" qty="1" currencycode="'. $paypal_settings['paypal_currency'] .'" rm="2" notifyurl="'. $paypal_settings['paypal_notifyurl'] .'" cancelurl="'. $paypal_settings['paypal_cancelurl'] .'" returnurl="'. $paypal_settings['paypal_returnurl'] .'" imagewidth="100px" pagestyle="paypal" lc="'. $paypal_settings['paypal_country'] .'" cbt="'. esc_html__( 'Complete Your Purchase', 'learndash' ) .'" custom="'. $user_id .'" srt="'. $srt .'"]' );
				
				if (!empty( $shortcode_content ) ) {
					$paypal_button = wptexturize( '<div class="learndash_checkout_button learndash_paypal_button">'. $shortcode_content .'</div>' );
				}
			}
		}

		$payment_params = array(
			'price' => $course_price,
			'post' => $course,
		);

		/**
		 * Filter PayPal payment button
		 * 
		 * @since 2.1.0
		 * 
		 * @param  string  $paypal_button
		 */
		$payment_buttons = apply_filters( 'learndash_payment_button', $paypal_button, $payment_params );
		
		if ( ! empty( $payment_buttons ) ) {
		
			if ( ( !empty( $paypal_button ) ) && ( $payment_buttons != $paypal_button ) ) {

				$button = 	'';
				$button .= 	'<div id="learndash_checkout_buttons_course_'. $course->ID .'" class="learndash_checkout_buttons">';
				$button .= 		'<input id="btn-join-'. $course->ID .'" class="btn-join btn-join-'. $course->ID .' button learndash_checkout_button" data-jq-dropdown="#jq-dropdown-'. $course->ID .'" type="button" value="'. $button_text .'" />';
				$button .= 	'</div>';
			
				global $dropdown_button;
				$dropdown_button .= 	'<div id="jq-dropdown-'. $course->ID .'" class="jq-dropdown jq-dropdown-tip checkout-dropdown-button">';
				$dropdown_button .= 		'<ul class="jq-dropdown-menu">';
				$dropdown_button .= 		'<li>';
				$dropdown_button .= 			str_replace($button_text, esc_html__('Use Paypal', 'learndash'), $payment_buttons);
				$dropdown_button .= 		'</li>';
				$dropdown_button .= 		'</ul>';
				$dropdown_button .= 	'</div>';
			
				return apply_filters( 'learndash_dropdown_payment_button', $button );
				
			} else {
				return	'<div id="learndash_checkout_buttons_course_'. $course->ID .'" class="learndash_checkout_buttons">'. $payment_buttons .'</div>';
			}
		}
	} else {
		$join_button = '<div class="learndash_join_button"><form method="post">
							<input type="hidden" value="'. $course->ID .'" name="course_id" />
							<input type="hidden" name="course_join" value="'. wp_create_nonce( 'course_join_'. get_current_user_id() .'_'. $course->ID ) .'" />
							<input type="submit" value="'.$button_text.'" class="btn-join" id="btn-join" />
						</form></div>';

		$payment_params = array( 
			'price' => '0', 
			'post' => $course, 
			'course_price_type' => $course_price_type 
		);

		/**
		 * Filter Join payment button
		 * 
		 * @since 2.1.0
		 * 
		 * @param  string  $join_button
		 */
		$payment_buttons = apply_filters( 'learndash_payment_button', $join_button, $payment_params );
		return $payment_buttons;
	}

}

// Yes, global var here. This var is set within the payment button processing. The var will contain HTML for a fancy dropdown
$dropdown_button = '';
add_action("wp_footer", 'ld_footer_payment_buttons');
function ld_footer_payment_buttons() {
	global $dropdown_button;
	
	if (!empty($dropdown_button)) {
		echo $dropdown_button;
	}
}

add_action('get_footer', 'learndash_get_footer');
function learndash_get_footer() {
	if (is_admin()) return;

	global $dropdown_button;
	if (empty($dropdown_button)) {
		wp_dequeue_script('jquery-dropdown-js');
	}
}



/**
 * Payment buttons shortcode
 *
 * @since 2.1.0
 * 
 * @param  array $attr shortcode attributes
 * @return string      output of payment buttons
 */
function learndash_payment_buttons_shortcode( $attr ) {
	global $learndash_shortcode_used;
	$learndash_shortcode_used = true;

	$shortcode_atts = shortcode_atts( array( 'course_id' => 0 ), $attr );

	extract( $shortcode_atts );

	if ( empty( $course_id ) ) {
		return '';
	} else {
		return learndash_payment_buttons( $course_id );
	}
}

add_shortcode( 'learndash_payment_buttons', 'learndash_payment_buttons_shortcode' );



/**
 * Check if lesson, topic, or quiz is a sample
 *
 * @since 2.1.0
 * 
 * @param  id|obj $post id of post or WP_Post object
 * @return bool
 */
function learndash_is_sample( $post ) {
	if ( empty( $post) ) {
		return false;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->ID ) ) {
		return false;
	}

	if ( $post->post_type == 'sfwd-lessons' ) {
		if ( learndash_get_setting( $post->ID, 'sample_lesson' ) ) {
			return true;
		}
	}

	if ( $post->post_type == 'sfwd-topic' ) {
		//if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) {
			$course_id = learndash_get_course_id( $post );
			$lesson_id = learndash_course_get_single_parent_step( $course_id, $post->ID );
			
		//} else {
		//	$lesson_id = learndash_get_setting( $post->ID, 'lesson' );
		//}
		
		if ( learndash_get_setting( $lesson_id, 'sample_lesson' ) ) {
			return true;
		}
	}

	if ( $post->post_type == 'sfwd-quiz' ) {
		//if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) == 'yes' ) {
			$course_id = learndash_get_course_id( $post );
			$lesson_id = learndash_course_get_single_parent_step( $course_id, $post->ID );
		//} else {
			$lesson_id = learndash_get_setting( $post->ID, 'lesson' );
		//}
		return learndash_is_sample( $lesson_id );
	}

	return false;
}



/**
 * Helper function for php output buffering
 * 
 * @todo not sure what this is preventing with a while looping
 *       counting to 10 and checking current buffer level
 *
 * @since 2.1.0
 * 
 * @param  integer $level
 * @return string
 */
function learndash_ob_get_clean( $level = 0 ) {
	$content = '';
	$i = 1;

	while ( $i <= 10 && ob_get_level() > $level ) {
		$i++;
		$content = ob_get_clean();
	}

	return $content;
}



/**
 * Redirect to home if user lands on archive pages for lesson or quiz post types
 * 
 * @since 2.1.0
 * 
 * @param  object $wp WP object
 */
function ld_remove_lessons_and_quizzes_page( $wp ) {

	if ( is_archive() && ! is_admin() )  {
		$post_type = get_post_type();
		if ( ( is_post_type_archive( $post_type ) ) && ( in_array( $post_type, array('sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ) ) ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}

}

add_action( 'wp', 'ld_remove_lessons_and_quizzes_page' );



/**
 * Removes comments
 * Filter callback for 'comments_array' (wp core hook)
 *
 * @since 2.1.0
 * 
 * @param  array $comments
 * @param  array $array
 * @return array empty array
 */
function learndash_remove_comments( $comments, $array ) {
	return array();
}


/**
 * Include auto updater file and instantiate nss_plugin_updater_sfwd_lms class
 *
 * @since 2.1.0
 */
function nss_plugin_updater_activate_sfwd_lms() {
	
	//if(!class_exists('nss_plugin_updater'))
	require_once ( dirname( __FILE__ ).'/ld-autoupdate.php' );

	$nss_plugin_updater_plugin_remote_path = 'http://support.learndash.com/';
	$nss_plugin_updater_plugin_slug = basename( dirname( dirname( __FILE__ ) ) ) . '/sfwd_lms.php';

	new nss_plugin_updater_sfwd_lms( $nss_plugin_updater_plugin_remote_path, $nss_plugin_updater_plugin_slug );
}

// Load the auto-update class
add_action( 'init', 'nss_plugin_updater_activate_sfwd_lms' );



if ( ! function_exists( 'ld_debug' ) ) {

	/**
	 * Log debug messages to file
	 * 
	 * @param  int|str|arr|obj|bool 	$msg 	data to log
	 */
	function ld_debug( $msg ) {
		$original_log_errors = ini_get( 'log_errors' );
		$original_error_log = ini_get( 'error_log' );
		ini_set( 'log_errors', true );
		ini_set( 'error_log', dirname( dirname( __FILE__ ) ).DIRECTORY_SEPARATOR.'debug.log' );

		global $processing_id;

		if ( empty( $processing_id ) ) {
			$processing_id	= time();
		}

		if ( isset( $_GET['debug'] ) ) {
			error_log( "[ $processing_id] ".print_r( $msg, true ) ); //Comment This line to stop logging debug messages.
		}

		ini_set( 'log_errors', $original_log_errors );
		ini_set( 'error_log', $original_error_log );
	}

}



/**
 * Convert seconds to time
 *
 * @since 2.1.0
 * 
 * @param  int 		$inputSeconds
 * @return string   time output
 */
function learndash_seconds_to_time( $inputSeconds ) {
	$secondsInAMinute = 60;
	$secondsInAnHour  = 60 * $secondsInAMinute;
	$secondsInADay    = 24 * $secondsInAnHour;

	$return = '';
	// extract days
	$days = floor( $inputSeconds / $secondsInADay );
	$return .= empty( $days ) ? '' : $days.'day';

	// extract hours
	$hourSeconds = $inputSeconds % $secondsInADay;
	$hours = floor( $hourSeconds / $secondsInAnHour );
	$return .= ( empty( $hours ) && empty( $days ) )? '':' '.$hours.'hr';

	// extract minutes
	$minuteSeconds = $hourSeconds % $secondsInAnHour;
	$minutes = floor( $minuteSeconds / $secondsInAMinute );
	$return .= ( empty( $hours ) && empty( $days ) && empty( $minutes ) ) ? '' : ' '.$minutes.'min';

	// extract the remaining seconds
	$remainingSeconds = $minuteSeconds % $secondsInAMinute;
	$seconds = ceil( $remainingSeconds );
	$return .= ' '.$seconds.'sec';

	return trim( $return );
}

/**
 * Convert a timestamp to locally timezone adjusted output display
 *
 * @since 2.2.0
 *
 * @param int		$timestamp timestamp to display
 * @param string	$display_format optional display format
 * @return string	offset adjusted displayed date/time
 */
function learndash_adjust_date_time_display($timestamp = 0, $display_format = '' ) {
	$date_time_display = '';

	if ($timestamp != 0) {
		if ( empty( $display_format ) ) {
			$display_format = apply_filters('learndash_date_time_formats', get_option('date_format') .' '. get_option('time_format'));
		}

		// First we convert the timestamp to local Y-m-d H:i:s format
		$date_time_display = get_date_from_gmt( date('Y-m-d H:i:s', $timestamp), 'Y-m-d H:i:s' );
			
		// Then we take that value and reconvert it to a timestamp and call date_i18n to translate the month, date name etc. 	
		$date_time_display = date_i18n( $display_format, strtotime( $date_time_display ) );
	}
	return $date_time_display;	
} 

function learndash_get_timestamp_from_date_string( $date_string = '', $adjust_to_gmt = true ) {
	$value_timestamp = 0;
	
	if ( !empty( $date_string ) ) {
		$value_timestamp = strtotime( $date_string );
		if ( ( !empty( $value_timestamp ) ) && ( $adjust_to_gmt ) ) {
			$value_ymd = get_gmt_from_date( date( 'Y-m-d H:i:s', $value_timestamp ), 'Y-m-d H:i:s' );
			if ( !empty( $value_ymd ) ) {
				$value_timestamp = strtotime($value_ymd);
			} else {
				$value_timestamp = 0;
			}
		}
	}
	
	return $value_timestamp;
}

/**
 * Check if server is on Microsoft IIS
 *
 * @since 2.1.0
 * 
 * @return bool
 */
function learndash_on_iis() {
	$sSoftware = strtolower( $_SERVER['SERVER_SOFTWARE'] );
	if ( strpos( $sSoftware, 'microsoft-iis' ) !== false ) {
		return true;
	} else {
		return false;
	}
}



/**
 * Sql "Default NULL check" in version 5(strict mode)
 * Function to disable null checks
 * Refer to bug http://core.trac.wordpress.org/ticket/2115
 *
 * @since 2.1.0
 */
function mysql_5_hack() {
	if ( learndash_on_iis() ) {
		global $wpdb;
		$sqlVersion = $wpdb->get_var( 'select @@version' );

		if ( $sqlVersion{0} == 5 ) { 
			$wpdb->query( 'set sql_mode="";' ); //set "Strict" mode off
		}		
	}
}

add_action( 'init', 'mysql_5_hack' );



/**
 * Helper function to print_r() in preformatted text 
 * 
 * @since 2.1.0
 * 
 * @param  string $msg
 */
function ldp( $msg ) {
	echo '<pre>';
	print_r( $msg );
	echo '</pre>';
}

/**
 * Utility function to traverse multidimensional array and apply user function 
 * 
 * @since 2.1.2
 * 
 * @param function $func callable user defined or system function. This 
 *			should be 'esc_attr', or some similar function. 
 * @param array $arr This is the array to traverse and cleanup. 
 *
 * @return array $arr cleaned array
 */
function array_map_r( $func, $arr) {
    foreach( $arr as $key => $value ) {
		if (is_array( $value ) ) {
			$arr[ $key ] = array_map_r( $func, $value );
		} else if (is_array($func)) {
			$arr[ $key ] = call_user_func_array($func, $value);
		} else {
			$arr[ $key ] = call_user_func( $func, $value );
		}
    }

    return $arr;
}


/**
 * Utility function to interface with the WordPress get_transient function
 * There have been resent issue where the transients loose the expire setting
 * So this function was created and replaces the direct calls to get_transient
 *
 * This function also allow checking disregard transients all together (see 
 * LEARNDASH_TRANSIENTS_DISABLED define). Or selectively disregard via filter	
 * 
 * @since 2.3.3
 * 
 * @param string $transient_key The transient key to retreive.
 *
 * @return mixed $transient_data the retreived transient data or false if expired. 
 */
function learndash_get_valid_transient( $transient_key = '' ) {
	
	$transient_data = false;
	
	if ( !empty( $transient_key ) ) {
		if ( !apply_filters( 'learndash_transients_disabled', LEARNDASH_TRANSIENTS_DISABLED, $transient_key ) ) { 
			
			$transient_data = get_transient( $transient_key );
			/*
			if ( $transient_data !== false ) {

				// Added in v2.4 to check if the site is running object cache we don't validate
				if ( !wp_using_ext_object_cache() ) {

					// If the data return is NOT false we double check it has a valid expired data. 
					$transient_expire_time = get_option( '_transient_timeout_' . $transient_key );
			
					// If the expired time is empty then something is not right in the system. So we 
					// set the return data to false so it will be regenerated. And just to be sure
					// we also delete the options for the transient and tranient expire. 
					if ( ( empty( $transient_expire_time ) ) || ( $transient_expire_time < time() ) ) {
						$transient_data = false;
						delete_option('_transient_'. $transient_key );
						delete_option( '_transient_timeout_' . $transient_key );
					} 
				}
			}
			*/
		}
	}
	
	return $transient_data;
}

function learndash_purge_transients() {
	if ( !apply_filters( 'learndash_transients_disabled', LEARNDASH_TRANSIENTS_DISABLED, 'learndash_all_purge' ) ) { 
		global $wpdb;
		
		$sql_str = "DELETE FROM ". $wpdb->options." WHERE option_name LIKE '_transient_learndash_%' OR option_name LIKE '_transient_timeout_learndash_%'";
		//error_log('sql_str['. $sql_str .']');
		$wpdb->query( $sql_str );
	}
}

function learndash_format_course_points( $points ) {

	$points = preg_replace("/[^0-9.]/", '', $points );
	$points = round( floatval( $points ), apply_filters( 'learndash_course_points_format_round', 1 ) );

	return floatval( $points );
}

/**
 * Utility function to accept a file path and swap it out for a URL
 * This function is used in combination with get_template() to take
 * a local file system path and filename and replace the beginning part 
 * matching ABSPATH with the home URL. 
 *
 * @since 2.4.2
 * 
 * @param string $filepath The file path and filename 
 *
 * @return string $$fileurl The URL to the template file
 */
function learndash_template_url_from_path( $filepath = '' ) {
	if ( !empty( $filepath ) ) {
		// Ensure we are handling Windows separators. 
		$WP_CONTENT_DIR_tmp = str_replace('\\', '/', WP_CONTENT_DIR );
		$filepath = str_replace('\\', '/', $filepath );
		$filepath = str_replace( $WP_CONTENT_DIR_tmp, WP_CONTENT_URL, $filepath );
		$filepath = str_replace( array('https://', 'http://' ), array('//', '//' ), $filepath );
	}

	return $filepath;
}

/**
 * Normally Course, Lesson, Topic and Quiz settings are stored into a single postmeta array. This 
 * function runs after after that save and will save the array elements into individual postmeta
 * fields. 
 * @param $course_id int required post_meta course_id
 * @param $settings array array of settings to be stored 
 *
 * @return none
 *
 * @since 2.4.3
 */
function learndash_convert_settings_to_single( $post_id = 0, $settings = array(), $prefix = '' ) {
	return;
	
	// Disabled for now. 
	if ( ( !empty( $post_id  ) ) && ( !empty( $settings ) ) && ( is_array( $settings ) ) ) {
		foreach( $settings as $setting_key => $setting_value ) {

			if ( ( !empty( $prefix ) ) && ( !empty( $setting_key ) ) ) {
				$setting_key = str_replace( $prefix.'_', '', $setting_key );
			}

			if ( ( is_array( $setting_value ) ) && ( empty( $setting_value ) ) ) {
				$setting_value = '';
			}
			
			update_post_meta( $post_id, $setting_key, $setting_value );
		}
		// Create a queryable marker so we know this settings has been converted. 
		update_post_meta( $post_id, '_settings_to_single', true );
	}
}

function learndash_check_convert_settings_to_single( $post_id = 0, $prefix = '' ) {
	return;
	
	// Disabled for now. 
	if ( !empty( $post_id ) ) {
		if ( !get_post_meta( $post_id, '_settings_to_single', true ) ) {
			
			$settings = get_post_meta( $post_id, '_'. $prefix, true );
			learndash_convert_settings_to_single( $post_id, $settings, $prefix );
		}
	}
}

// Used when saving a single setting. This will then trigger an update to the array setting
function learndash_setting_update_post_meta( $meta_id = 0, $object_id = '', $meta_key = '', $meta_value = '' ) {
	global $learndash_post_types;
	
	if ( ( !empty( $meta_key ) ) && ( substr( $meta_key, 0, strlen( '_ld_setting_' ) ) == '_ld_setting_' ) ) {
		$object_post_type = get_post_type( $object_id );
		if ( ( !empty( $object_post_type ) ) && ( in_array( $object_post_type, $learndash_post_types ) ) ) {
			$settings = get_post_meta( $object_id, '_'. $object_post_type, true );
			$meta_key = substr_replace( $meta_key, $object_post_type.'_', 0, strlen('_ld_setting_') );
			if ( ( isset( $settings[$meta_key] ) ) && ( $settings[$meta_key] != $meta_value ) ) {
				$settings[$meta_key] = $meta_value;
				
				remove_action( 'update_post_meta', 'learndash_setting_update_post_meta', 20, 4 );
				update_post_meta( $object_id, '_'. $object_post_type, $settings );
				add_action( 'update_post_meta', 'learndash_setting_update_post_meta', 20, 4 );
			}
		}
	} else if ( in_array( substr( $meta_key, 1 ), $learndash_post_types ) )  {
		learndash_convert_settings_to_single( $object_id, $meta_value, substr( $meta_key, 1 ) );
	}
}
//add_action( 'update_post_meta', 'learndash_setting_update_post_meta', 20, 4 );


/**
 * Used for the Support panel to get the MySQL priveleges for the DB_USER defined in the wp-config
 *
 * @since 2.4.7
 *
 * @returns array of grants
 */
function learndash_get_db_user_grants() {
	global $wpdb;
	
	$grants = array();

	if ( ( defined( 'DB_USER' ) ) && ( defined( 'DB_HOST' ) ) && ( DB_HOST === 'localhost' ) ) {
		$grants_sql_str = "SHOW GRANTS FOR '". DB_USER ."'@'". DB_HOST ."';";

		$level = ob_get_level();
		ob_start();
		
		$grants_results = $wpdb->query($grants_sql_str);
		if ( !empty( $grants_results ) ) {
			foreach( $wpdb->last_result as $result_object ) {
				foreach( $result_object as $result_key => $result_string ) {
					preg_match('/GRANT (.*?) ON /', $result_string, $result_perms);
					if ( ( isset( $result_perms[1] ) ) && ( !empty( $result_perms[1] ) ) ) {
						$perms = explode(',', $result_perms[1] );
						$perms = array_map( 'trim', $perms );
						$grants = array_merge( $grants, $perms );
					}
				}
			}
		}
		$contents = learndash_ob_get_clean( $level );		
		
		if ( !empty( $grants ) ) {
			$grants = array_unique( $grants );
		}
	}
	
	return $grants;
}

/**
 * Utility function to recursively remove a directory. 
 *
 * @since 1.0.3
 * @see 
 *
 * @param $dir directory path to remove
 * @return none
 */
function learndash_recursive_rmdir( $dir  = '' ) {
	if ( ( !empty( $dir ) ) && ( is_dir( $dir ) ) ) {
		$objects = scandir($dir);
		
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") 
					learndash_recursive_rmdir($dir."/".$object); 
				else unlink($dir."/".$object);
			}
		}
     	reset($objects);
		rmdir($dir);
	}
}


/**
 * Utility function to parse and validate the Assignment upload extensions allowed. 
 * This utility function will trim, convert to lowercase and rmeove '.', ans unique
 *
 * @since 2.5
 * @see 
 *
 * @param $exts array of extensions: zip, doc, pdf
 * @return $exts array or corrected values. 
 */
function learndash_validate_extensions( $exts = array() ) {
	if ( ( is_string( $exts ) ) && ( !empty( $exts ) ) ) {
		$exts = explode(',', $exts );
		$exts = array_map( 'trim', $exts );
		$exts = array_map( function( $ext ){ return str_replace('.', '', $ext ); }, $exts );
	}
	return $exts;
}