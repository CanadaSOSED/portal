<?php
if ( !class_exists( 'Learndash_Admin_Data_Upgrades_User_Meta_Courses' ) ) {
	class Learndash_Admin_Data_Upgrades_User_Meta_Courses extends Learndash_Admin_Settings_Data_Upgrades {
		
		public static $instance = null;

		private $transient_key = '';
		private $transient_data = array();

		function __construct() {
			self::$instance =& $this;
			
			$this->data_slug = 'user-meta-courses';
			$this->meta_key = 'ld-upgraded-'. $this->data_slug;

			add_filter( 'learndash_admin_settings_upgrades_register_actions', array( $this, 'register_upgrade_action' ) );
		}
		
		public static function getInstance() {
		    if ( ! isset( self::$_instance ) ) {
		        self::$_instance = new self();
		    }
		    return self::$_instance;
		}
		
		function register_upgrade_action( $upgrade_actions = array() ) {
			// Add ourselved to the upgrade actions
			$upgrade_actions[$this->data_slug] = array(
				'class'		=>	get_class( $this ),
				'instance'	=>	$this,
				'slug'		=>	$this->data_slug
			);
			
			return $upgrade_actions;
		}
		
		function show_upgrade_action() {
			?>
			<tr id="learndash-data-upgrades-container-<?php echo $this->data_slug ?>" class="learndash-data-upgrades-container">
				<td class="learndash-data-upgrades-button-container" style="width:20%">
					<button class="learndash-data-upgrades-button button button-primary" data-nonce="<?php echo wp_create_nonce( 'learndash-data-upgrades-'. $this->data_slug .'-'. get_current_user_id() ); ?>" data-slug="<?php echo $this->data_slug ?>"><?php printf( _x( 'Upgrade User %s Data', 'Export User Course Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></button></td>
				<td class="learndash-data-upgrades-status-container" style="width: 80%">
					<p><?php printf( _x('This upgrade will sync your existing user data for %s into a new database table for better reporting. (Required)', 'This upgrade will sync your existing user data for course into a new database table for better reporting. (Required)', 'learndash'), LearnDash_Custom_Label::label_to_lower( 'course' ) ) ?></p>
					<p class="description"><?php echo $this->get_last_run_info(); ?></p>	
						
					<div style="display:none;" class="meter learndash-data-upgrades-status">
						<div class="progress-meter">
							<span class="progress-meter-image"></span>
						</div>
						<div class="progress-label"></div>
					</div>
				</td>
			</tr>
			<?php
		}
				
		/**
		 * Class method for the AJAX update logic
		 * This function will determine what users need to be converted. Then the course and quiz functions
		 * will be called to convert each individual user data set.
		 *
		 * @since 2.3
		 * 
		 * @param  array 	$data 		Post data from AJAX call
		 * @return array 	$data 		Post data from AJAX call
		 */
		function process_upgrade_action( $data = array() ) {
			global $wpdb;
			
			$this->init_process_times();

			if ( ( isset( $data['nonce'] ) ) && ( !empty( $data['nonce'] ) ) ) {
				if ( wp_verify_nonce( $data['nonce'], 'learndash-data-upgrades-'. $this->data_slug .'-'. get_current_user_id() ) ) {
					$this->transient_key = $this->data_slug .'_'. $data['nonce'];
			
					if ( ( isset( $data['init'] ) ) && ( $data['init'] == true ) ) {
						unset( $data['init'] );
						
						$this->clear_previous_run_meta();
						learndash_activity_clear_mismatched_users( );
						learndash_activity_clear_mismatched_posts( );
			
						$sql_str = "SELECT ID FROM {$wpdb->users} as users
							LEFT JOIN {$wpdb->usermeta} as um1 ON users.ID = um1.user_id
							LEFT JOIN {$wpdb->usermeta} as um2 ON users.ID=um2.user_id 
								AND um2.meta_key='_sfwd-course_progress'
							WHERE 1=1
								AND um1.meta_key = '{$wpdb->prefix}capabilities'
								AND um2.meta_key IS NOT null";
														
						$data['process_users'] = $wpdb->get_col( $sql_str );
                        //$data['process_users'] = array(189);
						$users_count 				= 	count_users();
						$data['total_count'] 		= 	intval( $users_count['total_users'] );
						
						$data['result_count'] 		= 	$data['total_count'] - count( $data['process_users'] );
						$data['progress_percent'] 	= 	($data['result_count'] / $data['total_count']) * 100;
						$data['progress_label']		= 	sprintf( esc_html_x('%1$d of %2$s Users', 'placeholders: result count, total count', 'learndash'), $data['result_count'], $data['total_count']);

						$this->set_transient( $this->transient_key, $data );

					} else {
						$data = $this->get_transient( $this->transient_key );
						if ( ( isset( $data['process_users'] ) ) && ( !empty( $data['process_users'] ) ) ) {

							foreach( $data['process_users'] as $user_idx => $user_id ) {
																
								$user_complete = $this->convert_user_meta_courses_progress_to_activity( intval( $user_id ) );
							
								if ( $user_complete === true ) {
									unset( $data['process_users'][$user_idx] );
									$data['result_count'] 		= 	$data['total_count'] - count( $data['process_users'] );
									$data['progress_percent'] 	= 	($data['result_count'] / $data['total_count']) * 100;
									$data['progress_label']		= 	sprintf( esc_html_x('%1$d of %2$s Users', 'placeholders: result count, total count', 'learndash'), $data['result_count'], $data['total_count']);
						
									$this->set_transient( $this->transient_key, $data );
									//break;
								}

								if ( $this->out_of_timer() ) {
									break;
								}
							}
						}
					} 
				}
			} 
			
			// Remove process users from being returned to AJAX. 
			if ( isset( $data['process_users'] ) )
				unset( $data['process_users'] );

			// If we are at 100% then we update the internal data settings so other parts of LD know the upgrade has been run
			if ( ( isset( $data['progress_percent'] ) ) && ( $data['progress_percent'] == 100 ) ) {
				$this->set_last_run_info( $data );
				$data['last_run_info'] = $this->get_last_run_info();

				$this->remove_transient( $this->transient_key );
			}
			
			return $data;
		}
		
		function convert_user_meta_courses_progress_to_activity( $user_id = 0 ) {
			global $wpdb;

			if ( empty( $user_id ) ) return;
			
			$user_course_upgraded = get_user_meta($user_id, $this->meta_key, true);
			if ( $user_course_upgraded == 'COMPLETE' ) {
				return true;
			} else if ( is_array( $user_course_upgraded ) ) {
				$activity_ids = $user_course_upgraded;
			} else {
				$activity_ids = array();
				$activity_ids['last_course_id'] = 0;
				$activity_ids['existing'] = array();
				$activity_ids['current'] = array();
				$activity_ids['course_ids_used'] = array();
			}
			
			if ( !isset( $activity_ids['last_course_id'] ) ) 
				$activity_ids['last_course_id'] = 0;
			else 
				$activity_ids['last_course_id'] = intval( $activity_ids['last_course_id'] );

			if ( !isset( $activity_ids['existing'] ) ) 
				$activity_ids['existing'] = array();
			if ( !isset( $activity_ids['current'] ) ) 
				$activity_ids['current'] = array();
			if ( !isset( $activity_ids['course_ids_used'] ) ) 
				$activity_ids['course_ids_used'] = array();
			
			$user_meta_courses_progress = get_user_meta( $user_id, '_sfwd-course_progress', true );
			if ( ( !empty( $user_meta_courses_progress ) ) && ( is_array( $user_meta_courses_progress ) ) ) {
				// We sort the course progress array because we may need to save our place and need to know
				// where we left off
				ksort( $user_meta_courses_progress );
				
				foreach( $user_meta_courses_progress as $course_id => $course_data ) {
					
					// Need a way to seek to a specific key starting point in an array
					if ( $activity_ids['last_course_id'] >= $course_id )  continue;
					
					$course_post = get_post( $course_id );
					if ( ( $course_post ) && is_a( $course_post, 'WP_Post' ) ) {
					
						$total_activity_items = 0;
						$user_course_access_from = 0;
						$user_course_completed = 0;
						
						//$user_course_upgraded = $course_id;
						//update_user_meta( $user_id, $this->meta_key, $activity_ids );
				
						//$user_course_ids_used[$course_id] = $course_id;

						// Then loop over Lessons
						if ( ( isset( $course_data['lessons'] ) ) && ( !empty( $course_data['lessons'] ) ) ) {
							foreach( $course_data['lessons'] as $lesson_id => $lesson_complete ) {
								$lesson_post = get_post( $lesson_id );
								if ( ( $lesson_post ) && is_a( $lesson_post, 'WP_Post' ) ) {
									
									$lesson_args = array(
										'course_id'			=>	$course_id, 
										'post_id'			=>	$lesson_id,
										'user_id'			=>	$user_id,
										'activity_type'		=>	'lesson',
										'data_upgrade'		=>	true,
										'activity_meta'		=>	array( 
																	//'steps_total'		=>	intval( $course_data['total'] ),
																	//'steps_completed'	=>	intval( $course_data['completed'] ),
																)
									);

									if (!empty( $user_course_access_from ) ) {
										$lesson_args['activity_started']	= $user_course_access_from;
									}
							
									if ( $lesson_complete == true)  {
										$lesson_args['activity_status'] = true;
										if ( !empty( $user_course_completed ) ) {
											$lesson_args['activity_completed'] 	= $user_course_completed;
										}
									}
									$activity_id = learndash_update_user_activity( $lesson_args );
									if ( !empty( $activity_id ) ) 
										$activity_ids['current'][] = $activity_id;
							
									$total_activity_items += 1;
								} 
							}
						}

						// Then loop over Topics
						if ( ( isset( $course_data['topics'] ) ) && ( !empty( $course_data['topics'] ) ) ) {
							foreach( $course_data['topics'] as $lesson_id => $lessons_topics ) {
								if ( !empty( $lessons_topics ) ) {
									foreach( $lessons_topics as $topic_id => $topic_complete ) {
										$topic_post = get_post( $topic_id );
										if ( ( $lesson_post ) && is_a( $topic_post, 'WP_Post' ) ) {

											$topic_args = array(
												'course_id'			=>	$course_id,
												'post_id'			=>	$topic_id,
												'user_id'			=>	$user_id,
												'activity_type'		=>	'topic',
												'data_upgrade'		=>	true,
												'activity_meta'		=>	array( 
																			//'steps_total'		=>	intval( $course_data['total'] ),
																			//'steps_completed'	=>	intval( $course_data['completed'] ),
																		)
											);

											if (!empty( $user_course_access_from ) ) {
												$topic_args['activity_started'] = $user_course_access_from;
											}
									
											if ( $topic_complete == true)  {
												$topic_args['activity_status'] 		= true;
												if ( !empty( $user_course_completed ) ) {
													$topic_args['activity_completed'] 	= $user_course_completed;
												}
											}
									
											$activity_id = learndash_update_user_activity( $topic_args );
											if ( !empty( $activity_id ) ) 
												$activity_ids['current'][] = $activity_id;
											$total_activity_items += 1;
										} 
									}
								}
							}
						}

						// We only add the course activity record IF we have added Lessons and/or Topics
						//if ( !empty( $total_activity_items ) ) 

						$user_course_completed = get_user_meta($user_id, 'course_completed_'. $course_id, true);
						$user_course_access_from = get_user_meta($user_id, 'course_'. $course_id .'_access_from', true);
										
						if ( !empty( $user_course_access_from ) ) {
							$activity_id = learndash_update_user_activity(
								array(
									'course_id'			=>	$course_id,
									'post_id'			=>	$course_id,
									'user_id'			=>	$user_id,
									'activity_type'		=>	'access',
									'activity_started'	=>	$user_course_access_from,
									'data_upgrade'		=>	true
								)
							);
							if ( !empty( $activity_id ) ) 
								$activity_ids['current'][] = $activity_id;
						}
					
						$user_course_access_from = 0;

						// First add the main Course entry. 
						$course_args = array(
							'course_id'			=>	$course_id,
							'post_id'			=>	$course_id,
							'activity_type'		=>	'course',
							'user_id'			=>	$user_id,
							'data_upgrade'		=>	true,
							'activity_meta'		=>	array( 
														'steps_total'		=>	intval( $course_data['total'] ),
														'steps_completed'	=>	intval( $course_data['completed'] ),
													)
						);
					
						$steps_completed = intval( $course_data['completed'] );
						if ( ( !empty( $steps_completed ) ) && ( $steps_completed >= intval($course_data['total'] ) ) ) {
							$course_args['activity_status']	= true;
							
							// Finally if there is a Course Complete date we add it. 
							if ( !empty( $user_course_completed ) ) {
								$course_args['activity_completed'] = $user_course_completed;							
							}
						
						} else if ( !empty( $steps_completed ) ) {
							$course_args['activity_status']	= false;
						}
					
						if ( isset( $course_data['last_id'] ) ) {
							$course_args['activity_meta']['steps_last_id'] = intval( $course_data['last_id'] );
						}
					
						$activity_id = learndash_update_user_activity( $course_args ); 
						if ( !empty( $activity_id ) ) {
							$activity_ids['current'][] = $activity_id;
						}
					} 
					
					$activity_ids['last_course_id'] = $course_id;
					$activity_ids['course_ids_used'][$course_id] = $course_id;
					update_user_meta( $user_id, $this->meta_key, $activity_ids );
					
					if ( $this->out_of_timer() ) {
						return;
					}
				}
			}
					
			// Finally we go through the user's meta again to grab the random course access items. These would be there 
			// If the user was granted access but didn't actually start a lesson/quiz etc. 
			//$user_courses_access_sql = $wpdb->prepare( "SELECT user_id, meta_key, meta_value as course_access_from FROM ". $wpdb->prefix ."usermeta WHERE user_id=%d AND meta_key LIKE %s", $user_id, 'course_%_access_from');
			$user_courses_access_sql = $wpdb->prepare( "SELECT user_id, meta_key, meta_value as course_access_from FROM ". $wpdb->usermeta ." WHERE user_id=%d", $user_id );
			$user_courses_access_sql .= " AND meta_key LIKE 'course_%_access_from'";
			
			$user_courses_access = $wpdb->get_results( $user_courses_access_sql );
			
			if ( !empty( $user_courses_access ) ) {
				foreach( $user_courses_access as $user_course_access ) {
			
					if ( ( property_exists ( $user_course_access, 'meta_key' ) ) && ( !empty( $user_course_access->meta_key ) ) ) {
						$user_course_access->course_id = str_replace('course_', '', $user_course_access->meta_key);
						$user_course_access->course_id = str_replace('_access_from', '', $user_course_access->course_id);

						if (!isset($activity_ids['course_ids_used'][$user_course_access->course_id])) {

							$activity_id = learndash_update_user_activity(
								array(
									'course_id'			=>	$course_id,
									'post_id'			=>	$user_course_access->course_id,
									'user_id'			=>	$user_id,
									'activity_type'		=>	'access',
									//'activity_started'	=>	$user_course_access->course_access_from, 
									'data_upgrade'		=>	true
								)
							); 
							if ( !empty( $activity_id ) ) 
								$activity_ids['current'][] = $activity_id;
						}
					}
				}
			}
			
			// Here we purge items from the Activity DB where we don't have a match to processed 'current' course items. 
			$activity_ids['existing'] = learndash_report_get_activity_by_user_id( $user_id, array( 'access', 'course', 'lesson', 'topic' ) );
			if ( empty( $activity_ids['existing'] ) ) $activity_ids['existing'] = array();
			
			if ( ( !empty( $activity_ids['existing'] ) ) && ( !empty( $activity_ids['current'] ) ) ) {

				$activity_ids['existing'] = array_map( 'intval', $activity_ids['existing'] );
				sort( $activity_ids['existing'] );

				$activity_ids['current'] = array_map( 'intval', $activity_ids['current'] );
				sort( $activity_ids['current'] );
			
				$activity_ids_delete = array_diff( $activity_ids['existing'], $activity_ids['current'] );

				if ( !empty( $activity_ids_delete ) ) {
					learndash_report_clear_by_activity_ids( $activity_ids_delete );
				}
			}
			
			update_user_meta($user_id, $this->meta_key, 'COMPLETE');
			
			return true;
		}
		
	}
}
