<?php
/**
 * Helper and Utility Functions
 */

/**
 * Get ProPanel Template
 *
 * Templates can be overridden by creating a 'learndash-propanel' directory in the theme directory and placing
 * templates from the learndash-propanel/templates directory in it.
 *
 * @param $template_name
 *
 * @return mixed|null|void
 */
function ld_propanel_get_template( $template_name ) {
	$template_paths_array = array(
		'ld-propanel/' . $template_name,
		'ld-propanel/' . basename( $template_name )
	);
	$template_path = locate_template( $template_paths_array );

	if ( ! $template_path ) {
		$template_path = LD_PP_PLUGIN_DIR . 'templates/' . $template_name;
	}

    return apply_filters( 'learndash_propanel_template', $template_path, $template_name );
}

/**
 * Set ProPanel Report Filename
 *
 * @param string $filename_part The base filename to be used
 *
 * @return array containg two keys 
 * 			'report_filename' as the server filename and path
 * 			'report_url'  as the URL to download the file
 */
function ls_propanel_set_report_filenames( $file_part = '' ) {
	
	$files_info = array();

	$path_part = 'ld_propanel';

	if ( !empty( $file_part ) ) {

		$wp_upload_dir = wp_upload_dir();
		$wp_upload_dir['basedir'] = str_replace('\\', '/', $wp_upload_dir['basedir']);
		$wp_upload_dir['basedir'] = trailingslashit( $wp_upload_dir['basedir'] );
		$wp_upload_dir['baseurl'] = trailingslashit( $wp_upload_dir['baseurl'] );
		
		if ( wp_mkdir_p( $wp_upload_dir['basedir'] . $path_part ) !== false ) {
			// Just to ensure the directory is not readable
			file_put_contents( $wp_upload_dir['basedir'] . $path_part .'/index.php', '// nothing to see here');

			$files_info['report_file'] = $wp_upload_dir['basedir'] . $path_part .'/'. $file_part;
			$files_info['report_url'] = $wp_upload_dir['baseurl'] . $path_part .'/'. $file_part;
		}
	}
	
	return $files_info;
}

function ld_propanel_get_pager_values() {
	return (array)apply_filters('ld_propanel_per_page_array', array(5, 10, 15, 25, 35, 50, 75, 100 ) );
}

function ld_propanel_get_users_count() {
	$all_user_ids = array();
	
	$return_total_users = 0;

	$exclude_admin_users = ld_propanel_exclude_admin_users();
	$auto_enroll_admin_users = ld_propanel_auto_enroll_admin_users();
	
	$ld_open_courses = learndash_get_open_courses();

	$admin_user_ids = ld_propanel_get_admin_user_ids();
	
	if ( ld_propanel_count_post_type( 'sfwd-courses' ) ) {
	 	// If we have any OPEN courses then we just use the WP_User_Query to get all users. 
	 	if ( !empty( $ld_open_courses ) ) {
			$user_query_args = array(
				'count_total'	=>	true,
				'fields'		=>	'ID'
			);

			$user_query_args = apply_filters( 'ld_propanel_overview_students_count_args', $user_query_args );
			if ( !empty( $user_query_args ) ) {
				$user_query = new WP_User_Query( $user_query_args );
				if ( $user_query instanceof WP_User_Query ) {
					$all_user_ids = $user_query->get_results();
				}
			}		

	 	} else {
			// Else if there are no open courses we the query users with 'learndash_group_users_%' OR 'course_%_access_from' meta_keys
	 		global $wpdb;
			
			//$users_courses_sql = "SELECT DISTINCT um.user_id FROM ". $wpdb->usermeta ." um WHERE um.meta_key IN ( SELECT CONCAT('course_', p.ID, '_access_from') FROM ". $wpdb->posts ." p WHERE p.post_type='sfwd-courses' AND p.post_status='publish' )";
			
			$users_courses_sql = "SELECT DISTINCT users.ID FROM {$wpdb->users} as users
			LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )  
			LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )  
			WHERE 1=1
			AND ( 
				um1.meta_key = '{$wpdb->prefix}capabilities'
				AND ( um2.meta_key IN 	
					( 
						SELECT DISTINCT CONCAT('course_', p.ID, '_access_from') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
						UNION ALL
						SELECT DISTINCT CONCAT('course_completed_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
						UNION ALL
						SELECT DISTINCT CONCAT('learndash_course_expired_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
					) 
				) 
			)";
	 		$users_courses_results = $wpdb->get_col( $users_courses_sql );
		
			//$users_groups_sql = "SELECT DISTINCT um.user_id FROM ". $wpdb->usermeta ." um WHERE um.meta_key IN ( SELECT CONCAT('learndash_group_users_', p.ID, '') FROM ". $wpdb->posts ." p WHERE p.post_type='groups' AND p.post_status='publish' )";
			$users_groups_sql = "SELECT DISTINCT users.ID FROM {$wpdb->users} users 
			LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )  
			LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )  
			WHERE 1=1
			AND ( 
				um1.meta_key = '{$wpdb->prefix}capabilities'
				AND ( um2.meta_key IN 
						( 
							SELECT CONCAT('learndash_group_users_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='groups' AND p.post_status='publish'
						)
					)
				)";
			$users_groups_results = $wpdb->get_col( $users_groups_sql );

			$all_user_ids = array_merge( $users_courses_results, $users_groups_results );
		}

		if ( ( $exclude_admin_users !== true ) && ( $auto_enroll_admin_users === true ) && ( !empty( $admin_user_ids ) ) ) {
			$all_user_ids = array_merge( $all_user_ids, $admin_user_ids );
		} else if ( ( $exclude_admin_users === true ) && ( !empty( $admin_user_ids ) ) ) {
			$all_user_ids = array_diff( $all_user_ids, $admin_user_ids );
		}

		if ( ( !empty( $all_user_ids ) ) && ( is_array( $all_user_ids ) ) ) {
			$all_user_ids = array_map( 'intval', $all_user_ids );
			$all_user_ids = array_unique( $all_user_ids );
			$return_total_users = count( $all_user_ids );
		}
	}
	
	return $return_total_users;
}

function ld_propanel_exclude_admin_users() {
	$reports_exclude_admin_users = false;
	
	if ( version_compare( LEARNDASH_VERSION, '2.4.0') >= 0 ) {
		$reports_exclude_admin_users = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'reports_include_admin_users' );
		if ( $reports_exclude_admin_users == 'yes' )
			$reports_exclude_admin_users = false;
		else
			$reports_exclude_admin_users = true;
	} 
	
	return apply_filters( 'ld_propanel_exclude_admin_users', $reports_exclude_admin_users );
}

function ld_propanel_auto_enroll_admin_users() {
	$auto_enroll_admin_users = false;

	if ( version_compare( LEARNDASH_VERSION, '2.4.0') >= 0 ) {
		$auto_enroll_admin_users = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
		if ( $auto_enroll_admin_users == 'yes' ) $auto_enroll_admin_users = true;
		else $auto_enroll_admin_users = false;
	}
	
	return apply_filters( 'ld_propanel_auto_enroll_admin_users', $auto_enroll_admin_users );
}

function ld_propanel_get_admin_user_ids( $return_count = false ) {
	$admin_user_query_args = array(
		'fields' 	=>	'ID',
		'role'		=>	'administrator'
	);
	
	if ( $return_count === true ) {
		$admin_user_query_args['count_total'] = true;
	}

	$admin_user_query = new WP_User_Query( $admin_user_query_args );
	if ( $return_count === true ) {
		return $admin_user_query->get_total();
	} else {
		$admin_user_ids = $admin_user_query->get_results();
		if ( !empty( $admin_user_ids ) ) {
			$admin_user_ids = array_map( 'intval', $admin_user_ids );
		}
		return $admin_user_ids;
	}
}

function ld_propanel_adjust_admin_users( $activity_query_args = array() ) {	

	if ( !empty( $activity_query_args ) ) {
		$exclude_admin_users = ld_propanel_exclude_admin_users();

		if ( ( isset( $activity_query_args['user_ids'] ) ) && ( !empty( $activity_query_args['user_ids'] ) ) ) {
			if ( !isset( $activity_query_args['user_ids_action'] ) ) {
				$activity_query_args['user_ids_action'] = 'IN';
			}
		
			if ( $exclude_admin_users ) {
				$admin_user_query_args = array(
					'fields' 	=>	'ID',
					'role'		=>	'administrator'
				);
				$admin_user_query = new WP_User_Query( $admin_user_query_args );
				$admin_user_ids = $admin_user_query->get_results();
				if ( !empty( $admin_user_ids ) ) {
					if ( $activity_query_args['user_ids_action'] == 'IN' )
						$activity_query_args['user_ids'] = array_diff( $activity_query_args['user_ids'], $admin_user_ids );
					else
						$activity_query_args['user_ids'] = array_merge( $activity_query_args['user_ids'], $admin_user_ids );
				}
			}
		} else {
			if ( $exclude_admin_users ) {
				$admin_user_query_args = array(
					'fields' 	=>	'ID',
					'role'		=>	'administrator'
				);
				$admin_user_query = new WP_User_Query( $admin_user_query_args );
				$admin_user_ids = $admin_user_query->get_results();
				if ( !empty( $admin_user_ids ) ) {
					$activity_query_args['user_ids_action'] = 'NOT IN';
					$activity_query_args['user_ids'] = $admin_user_ids;
				}
			}
		}
	}
	
	return $activity_query_args;
	
}

function ld_propanel_convert_fewer_users( $activity_query_args = array() ) {
	if ( ( !is_multisite() ) && ( !empty( $activity_query_args ) ) ) {
		if ( !isset( $activity_query_args['user_ids_action'] ) ) 
			$activity_query_args['user_ids_action'] = 'IN';
		
		if ( $activity_query_args['user_ids_action'] !== 'NOT IN' ) {
		
			//$total_users = ld_propanel_get_users_count();
			//if ( count( $activity_query_args['user_ids'] ) > ( $total_users / 2 ) ) {

			$result = count_users();
			if ( !isset( $activity_query_args['user_ids'] ) ) 
				$activity_query_args['user_ids'] = array();
			
			if ( count( $activity_query_args['user_ids'] ) > ( $result['total_users'] / 2 ) ) {
				$user_query_args = array( 
					'fields'	=> 'ID',
					'exclude'	=> $activity_query_args['user_ids']
				);
				$user_query = new WP_User_Query( $user_query_args );
				if ( $user_query instanceof WP_User_Query ) {	
					$exclude_user_ids = $user_query->get_results();
					if ( !empty( $exclude_user_ids ) ) {
						$activity_query_args['user_ids'] = $exclude_user_ids;
						$activity_query_args['user_ids_action'] = 'NOT IN';
					}
				}
			}
		}
	}
	
	return $activity_query_args;
}

function ld_propanel_load_post_data( $post_data = array(), $_get = array() ) {

	if ( empty( $_get ) ) 
		$_get = $_GET;

	$per_page_array = ld_propanel_get_pager_values();
	if ( empty( $per_page_array ) ) {
		$per_page_array = array(5);
	}

	if ( ( isset( $_get['container_type'] ) ) && ( !empty( $_get['container_type'] ) ) )
		$post_data['container_type'] = esc_attr( $_get['container_type'] );
	else
		$post_data['container_type'] = '';
	
	if ( ( isset( $_get['template'] ) ) && ( !empty( $_get['template'] ) ) )
		$post_data['template'] = esc_attr( $_get['template'] );
	else
		$post_data['template'] = '';

	$post_data['filters'] = array(
		'id' => 0,
		'type' => '',
		'courseStatus' => array(),
		'search' => '',
		'reporting_pager' => array(
			'current_page' => 1,
			'per_page' => $per_page_array[0]
		)
	);
				
	if ( isset( $_get['filters'] ) ) {
		if ( ( isset( $_get['filters']['id'] ) ) && ( !empty( $_get['filters']['id'] ) ) )
			$post_data['filters']['id'] = intval( $_get['filters']['id'] );

		if ( ( isset( $_get['filters']['type'] ) ) && ( !empty( $_get['filters']['type'] ) ) )
			$post_data['filters']['type'] = esc_attr( $_get['filters']['type'] );

		if ( ( isset( $_get['filters']['search'] ) ) && ( !empty( $_get['filters']['search'] ) ) )
			$post_data['filters']['search'] = esc_attr( $_get['filters']['search'] );

		if ( ( isset( $_get['filters']['courseStatus'] ) ) && ( !empty( $_get['filters']['courseStatus'] ) ) ) {
			$post_data['filters']['courseStatus'] = array();

			$courseStatus = $_get['filters']['courseStatus'];
			if ( is_string( $courseStatus ) ) $courseStatus = array( $courseStatus );
			
			if ( in_array( 'not-started', $courseStatus ) !== false)  {
				$post_data['filters']['courseStatus'][] = 'NOT_STARTED';
			} 

			if ( in_array( 'in-progress', $courseStatus ) !== false)  {
				$post_data['filters']['courseStatus'][] = 'IN_PROGRESS';
			}

			if ( in_array( 'completed', $courseStatus ) !== false)  {
				$post_data['filters']['courseStatus'][] = 'COMPLETED';
			}
		}
		
		if ( ( isset( $_get['filters']['users'] ) ) && ( !empty( $_get['filters']['users'] ) ) )
			$post_data['filters']['users'] = intval( $_get['filters']['users'] );

		if ( ( isset( $_get['filters']['courses'] ) ) && ( !empty( $_get['filters']['courses'] ) ) )
			$post_data['filters']['courses'] = intval( $_get['filters']['courses'] );

		if ( ( isset( $_get['filters']['groups'] ) ) && ( !empty( $_get['filters']['groups'] ) ) )
			$post_data['filters']['groups'] = intval( $_get['filters']['groups'] );


		if ( ( isset( $_get['filters']['reporting_pager'] ) ) && ( !empty( $_get['filters']['reporting_pager'] ) ) ) {
			if ( ( isset( $_get['filters']['reporting_pager']['current_page'] ) ) && ( !empty( $_get['filters']['reporting_pager']['current_page'] ) ) )
				$post_data['filters']['reporting_pager']['current_page'] = intval( $_get['filters']['reporting_pager']['current_page'] );

			if ( ( isset( $_get['filters']['reporting_pager']['per_page'] ) ) && ( !empty( $_get['filters']['reporting_pager']['per_page'] ) ) )
				$post_data['filters']['reporting_pager']['per_page'] = intval( $_get['filters']['reporting_pager']['per_page'] );
		}
	}

	$post_data = apply_filters( 'ld_propanel_reporting_post_args', $post_data, $_get );
	return $post_data;
}

function ld_propanel_load_activity_query_args( $activity_query_args = array(), $post_data = array() ) {

	$activity_query_args['per_page'] = $post_data['filters']['reporting_pager']['per_page'];
	$activity_query_args['paged'] =  $post_data['filters']['reporting_pager']['current_page'];
	
	if ( !isset( $activity_query_args['activity_status'] ) )
		$activity_query_args['activity_status'] = array();

	if ( !empty( $post_data['filters']['courseStatus'] ) ) {
		$activity_query_args['activity_status'] = $post_data['filters']['courseStatus'];
//		if ( in_array( 'not-started', $post_data['filters']['courseStatus'] ) !== false)  {
//			$activity_query_args['activity_status'][] = 'NOT_STARTED';
//		} 
//
//		if ( in_array( 'in-progress', $post_data['filters']['courseStatus'] ) !== false)  {
//			$activity_query_args['activity_status'][] = 'IN_PROGRESS';
//		}
//
//		if ( in_array( 'completed', $post_data['filters']['courseStatus'] ) !== false)  {
//			$activity_query_args['activity_status'][] = 'COMPLETED';
//		}
	}

	if ( empty( $activity_query_args['activity_status'] ) )
		$activity_query_args['activity_status'] = array( 'NOT_STARTED', 'IN_PROGRESS', 'COMPLETED' );
		
	if ( !empty( $post_data['filters']['search'] ) )
		$activity_query_args['s'] = sprintf( '%%%s%%', esc_html( $post_data['filters']['search'] ) );
	else
		$activity_query_args['s'] = '';

	$activity_query_args = apply_filters( 'ld_propanel_reporting_activity_args', $activity_query_args, $post_data );
	
	return $activity_query_args;
}

function ld_propanel_get_course_post_items( $course_id = 0, $post_types = array( 'sfwd-courses', 'sfwd-quiz', 'sfwd-lessons', 'sfwd-topic' ) ) {
	if ( !empty( $course_id ) ) {
		$query_course_args = array( 
			'post_type' 		=> 	$post_types, 
			'post_status' 		=> 	'publish',
			'posts_per_page' 	=> 	-1,
			'fields'			=>	'ids',
			'meta_query'		=>	array(
				'relation' 	=> 'OR',
				array(
					'key'     => 'course_id',
					'value'   => $course_id,
					'compare' => '=',
				)
			)		
		);

		if ( version_compare( LEARNDASH_VERSION, '2.4.9.9') >= 0 ) {
			$query_course_args['meta_query'][] = array(
				'key'     => 'ld_course_' . $course_id,
				'value'   => $course_id,
				'compare' => '=',
			);
		}
			
		//error_log('query_course_args<pre>'. print_r($query_course_args, true) .'</pre>');
		$query_course = new WP_Query( $query_course_args );
		if ( ! empty( $query_course->posts ) ) {
			//error_log('course_id['. $course_id .'] count['. count( $query_course->posts ).']<pre>'. print_r($query_course, true) .'</pre>');
			return $query_course->posts;
		}
	}
}

// General utility function to count various post types
function ld_propanel_count_post_type( $post_type = '' ) {
	if ( !empty( $post_type ) ) {
		$query_args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
		);
		
		return learndash_get_courses_count( $query_args );
	}
}


function ld_propanel_get_assignments_pending_count( $query_args = array(), $return_field = 'found_posts' ) {
	$return = 0;

	$default_args = array(
		'post_type'		=>	'sfwd-assignment',
		'post_status'	=>	'publish',
		'fields'		=>	'ids',
		'meta_query' 	=> array(
			array(
				'key' 		=> 	'approval_status',
				'compare' 	=> 	'NOT EXISTS',
			),
		),
	);
	
	// added logic for non-admin user like group leaders who will only see a sub-set of assignments
	$user_id = get_current_user_id();

	if ( learndash_is_admin_user() ) {
		if ( ld_propanel_exclude_admin_users( $user_id ) ) {
			$admin_user_ids = ld_propanel_get_admin_user_ids();
			if ( !empty( $admin_user_ids ) ) {
				$default_args['author__not_in'] = $admin_user_ids;
			}
		}
	} else if ( learndash_is_group_leader_user( $user_id ) ) {
		$group_ids = learndash_get_administrators_group_ids( $user_id );
		$user_ids = array();
		$course_ids = array();
		
		if ( ! empty( $group_ids ) && is_array( $group_ids ) ) {			
			foreach( $group_ids as $group_id ) {
				$group_users = learndash_get_groups_user_ids( $group_id );

				if ( ! empty( $group_users ) && is_array( $group_users ) ) {
					foreach( $group_users as $group_user_id ) {
						$user_ids[ $group_user_id ] = $group_user_id;
					}
				}
				
				$group_course_ids = learndash_group_enrolled_courses( $group_id );
				if ( ( !empty( $group_course_ids ) ) && (is_array( $group_course_ids ) ) ) {
					$course_ids = array_merge( $course_ids, $group_course_ids );
				}
				
			}
		} else {
			return $return;
		}
		
		if ( ! empty( $course_ids ) && count( $course_ids ) ) {
			$default_args['meta_query'][] = array(
				'key'     => 'course_id',
				'value'   => $course_ids,
				'compare' => 'IN',
			);
		} else {
			return $return;
		}
	
		if ( ! empty( $user_ids ) && count( $user_ids ) ) {
			if ( ld_propanel_exclude_admin_users() ) {
				$admin_user_ids = ld_propanel_get_admin_user_ids();
				if ( !empty( $admin_user_ids ) ) {
					//$user_ids = array_intersect( $user_ids, $admin_user_ids );
					$user_ids = array_diff( $user_ids, $admin_user_ids );
				}
			}
			$default_args['author__in'] = $user_ids;
		} else {
			return $return;
		}
	}
	
	$query_args = wp_parse_args( $query_args, $default_args );
	$query_args = apply_filters( 'learndash_get_assignments_pending_count_query_args', $query_args );

	if ( $return_field == 'found_posts' ) {
		$query_args['posts_per_page'] = 1;
		$query_args['paged'] = 1;
	}

	if ( ( is_array( $query_args ) ) && ( !empty( $query_args ) ) ) {
		$query = new WP_Query( $query_args );

		if ( ( !empty( $return_field ) ) && ( property_exists( $query, $return_field ) ) ) {
			$return = $query->$return_field;
		} else {
			$return = $query;
		}
	}	
	
	return $return;
}

/**
 * Get count of pending Essays posts ( sfwd-essays )
 *
 * @param  array 	$essays_query_args override query arguments
 * @param  string 	$return_field specific field from WP_Query to return. Default is 'found_posts'
 * @return mixed   	$assignments_return if $return_field is empty then return is WP_Query instance. Otherwise specific field from WP_Query returned
 * 
 * @since 2.3
 */
function ld_propanel_get_essays_pending_count( $query_args = array(), $return_field = 'found_posts' ) {
	$return = 0;

	$default_args = array(
		'post_type'		=>	'sfwd-essays',
		'post_status'	=>	'not_graded',
		'fields'		=>	'ids',
	);
	
	// added logic for non-admin user like group leaders who will only see a sub-set of assignments
	$user_id = get_current_user_id();
	if ( learndash_is_admin_user( $user_id ) ) {
		if ( ld_propanel_exclude_admin_users() ) {
			$admin_user_ids = ld_propanel_get_admin_user_ids();
			if ( !empty( $admin_user_ids ) ) {
				$default_args['author__not_in'] = $admin_user_ids;
			}
		}
	} else
	if ( learndash_is_group_leader_user( $user_id ) ) {
		$group_ids = learndash_get_administrators_group_ids( $user_id );
		$user_ids = array();
		$course_ids = array();

		if ( ! empty( $group_ids ) && is_array( $group_ids ) ) {
			foreach( $group_ids as $group_id ) {
				$group_users = learndash_get_groups_user_ids( $group_id );

				if ( ! empty( $group_users ) && is_array( $group_users ) ) {
					foreach( $group_users as $group_user_id ) {
						$user_ids[ $group_user_id ] = $group_user_id;
					}
				}

				$group_course_ids = learndash_group_enrolled_courses( $group_id );
				if ( ( !empty( $group_course_ids ) ) && (is_array( $group_course_ids ) ) ) {
					$course_ids = array_merge( $course_ids, $group_course_ids );
				}
			}
		} else {
			return $return;
		}
	
		if ( ! empty( $course_ids ) && count( $course_ids ) ) {
			$default_args['meta_query'][] = array(
				'key'     => 'course_id',
				'value'   => $course_ids,
				'compare' => 'IN',
			);
		} else {
			return $return;
		}
	
		if ( ! empty( $user_ids ) && count( $user_ids ) ) {
			if ( ld_propanel_exclude_admin_users() ) {
				$admin_user_ids = ld_propanel_get_admin_user_ids();
				if ( !empty( $admin_user_ids ) ) {
					//$user_ids = array_intersect( $user_ids, $admin_user_ids );
					$user_ids = array_diff( $user_ids, $admin_user_ids );
				}
			}
			$default_args['author__in'] = $user_ids;
		} else {
			return $return;
		}
	}
	
	$query_args = wp_parse_args( $query_args, $default_args );
	$query_args = apply_filters( 'learndash_get_essays_pending_count_query_args', $query_args );

	if ( $return_field == 'found_posts' ) {
		$query_args['posts_per_page'] = 1;
		$query_args['paged'] = 1;
	}

	if ( ( is_array( $query_args ) ) && ( !empty( $query_args ) ) ) {
		$query = new WP_Query( $query_args );
		
		if ( ( !empty( $return_field ) ) && ( property_exists( $query, $return_field ) ) ) {
			$return = $query->$return_field;
		} else {
			$return = $query;
		}
	}	
	
	return $return;
}


function ld_propanel_get_widget_screen_type_class( $widget_id = '', $screen_class_prefix = 'ld-propanel-screen-' ) {
	if ( !empty( $widget_id ) ) {
		$screen_type = '';
	
		if ( is_admin() ) {
			$screen = get_current_screen();

			if ( in_array( $screen->id, array( 'dashboard', 'dashboard_page_propanel-reporting' ) ) ) {
				$screen_type = 'dashboard';
			}
		
		} else {
			global $learndash_shortcode_used;
			if ( $learndash_shortcode_used === true ) {
				$screen_type = 'shortcode';
			}
		}
		
		$screen_type = apply_filters('ld_propanel_screen_type', $screen_type, $widget_id, $screen_class_prefix );
		if ( !empty( $screen_type ) ) {
			return $screen_class_prefix .= $screen_type;
		}
	}
}