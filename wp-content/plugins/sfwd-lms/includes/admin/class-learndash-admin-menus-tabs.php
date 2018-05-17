<?php
if ( !class_exists( 'Learndash_Admin_Menus_Tabs' ) ) {
	class Learndash_Admin_Menus_Tabs {

		private static $instance;
		
		protected $admin_tab_sets = array();
		public $admin_tab_priorities = array(
			'private'	=>	0,
			'high'		=>	10,
			'normal'	=>	20,
			'taxonomy'	=>	30,
			'misc'		=>	40
		);
		
		function __construct() {
			// We first add this hook so we are calling 'admin_menu' early.
			add_action( 'admin_menu', array( $this, 'learndash_admin_menu_early' ), 0 );

			// Then within the 'wp_loaded' handler we add another hook into 'admin_menu' to be in the last-est
			// position where we add all the misc menu items 
			add_action( 'wp_loaded', array( $this, 'wp_loaded' ), 1000 );
			
			add_action( 'all_admin_notices', array( $this, 'learndash_admin_tabs' ) );
		}

		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new static();
			}

			return self::$instance;
		}

		
		// We hook into the 'wp_loaded' action which comes just before the 'admin_menu' action. The reason for this
		// we want to add a special 'admin_menu' and ensure it is the last action taken on the menu. 
		function wp_loaded() {
			
			global $wp_filter;

			/***********************************************************************
			admin_menu
			************************************************************************/
			// Set a default priority.
			
			$top_priority = 100;
			if ( defined( 'LEARNDASH_SUBMENU_SETTINGS_PRIORITY' ) ) 
				$top_priority = intval( LEARNDASH_SUBMENU_SETTINGS_PRIORITY );
			
			$top_priority = apply_filters( 'learndash_submenu_settings_priority', $top_priority );
			
			// Check to see of there are existing 'admin_menu' actions. 
			/*
			if ( ( isset( $wp_filter['admin_menu'] ) ) && ( property_exists( $wp_filter['admin_menu'], 'callbacks' ) ) && ( !empty( $wp_filter['admin_menu']->callbacks ) ) ) {

				// Get all the priority keys from the callbacks element
				$priorities = array_keys( $wp_filter['admin_menu']->callbacks );
				if ( !empty( $priorities ) ) {
					rsort( $priorities );
					$top_priority = intval($priorities[0])+1;
				}
			}
			*/
			add_action( 'admin_menu', array( $this, 'learndash_admin_menu_last' ), $top_priority );


			/***********************************************************************
			learndash_menu_args
			************************************************************************/			
			// Check to see of there are existing 'admin_menu' actions. 
			/*
			if ( ( isset( $wp_filter['learndash_menu_args'] ) ) && ( property_exists( $wp_filter['learndash_menu_args'], 'callbacks' ) ) && ( !empty( $wp_filter['learndash_menu_args']->callbacks ) ) ) {

				// Get all the priority keys from the callbacks element
				$priorities = array_keys( $wp_filter['learndash_menu_args']->callbacks );
				if ( !empty( $priorities ) ) {
					rsort( $priorities );
					$top_priority = intval($priorities[0])+1;
				}
			}
			*/
		}

		function learndash_menu_args( $menu_args = array() ) {
			if ( ( is_array( $menu_args['admin_tabs'] ) ) && ( !empty( $menu_args['admin_tabs'] ) ) ) {
				foreach( $menu_args['admin_tabs'] as &$admin_tab_item ) {

					// Similar to the logic from admin_menu above. 
					// We need to convert the 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses'
					// menu_links to 'admin.php?page=learndash_lms_settings' so all the LearnDash > Settings tabs connect 
					// to that menu instead.
					if ( $admin_tab_item['menu_link'] == 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses' ) {
						$admin_tab_item['menu_link'] = 'admin.php?page=learndash_lms_settings';
					}
				}
			}

			$menu_args['admin_tabs_on_page']['admin_page_learndash_lms_settings'] = $menu_args['admin_tabs_on_page']['sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses']; 

			$menu_args['admin_tabs_on_page']['sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses'] = $menu_args['admin_tabs_on_page']['edit-sfwd-courses'];

			return $menu_args;
		}
		
		function learndash_admin_menu_tabs( $menu_args = array() ) {
			$menu_item_tabs = array();

			// Now we take the current page id and collect all the tab items. This is the newer 
			// form of the tab logic instead of them being global. 
			$current_page_id = $menu_args['current_page_id'];
			if ( isset( $menu_args['admin_tabs_on_page'][$current_page_id] ) ) {
				$menu_link = '';
				
				foreach( $menu_args['admin_tabs_on_page'][$current_page_id] as $admin_tabs_on_page_id ) {
					if ( isset( $menu_args['admin_tabs'][$admin_tabs_on_page_id] ) ) {
						if ( empty( $menu_link ) ) {
							$menu_link = $menu_args['admin_tabs'][$admin_tabs_on_page_id]['menu_link'];
						}
						
						$menu_item_tabs[$admin_tabs_on_page_id] = $menu_args['admin_tabs'][$admin_tabs_on_page_id];
					}
				}
						
				foreach( $menu_args['admin_tabs'] as $admin_tab_id => $admin_tab ) {
					if ( $admin_tab['menu_link'] == $menu_link ) {
						if ( !isset( $menu_item_tabs[$admin_tab_id] ) ) {
							$menu_item_tabs[$admin_tab_id] = $admin_tab;
						}
					}
				}
			}
			
			return $menu_item_tabs;
		}
		
		
		function add_admin_tab_set( $menu_slug, $menu_item ) {
			
			if ( ( $menu_slug == 'edit.php?post_type=sfwd-courses' ) 
			 || ( $menu_slug == 'edit.php?post_type=sfwd-lessons' )
 			 || ( $menu_slug == 'edit.php?post_type=sfwd-topic' )
			 || ( $menu_slug == 'edit.php?post_type=sfwd-certificates' )
			 || ( $menu_slug == 'edit.php?post_type=sfwd-quiz' )
			 || ( $menu_slug == 'edit.php?post_type=sfwd-assignment' )
			 || ( $menu_slug == 'edit.php?post_type=sfwd-transactions' )
			 || ( $menu_slug == 'edit.php?post_type=groups' ) ) {
								
			 	if ( !isset( $admin_tab_sets[$menu_slug] ) ) $admin_tab_sets[$menu_slug] = array();

				foreach( $menu_item as $menu_item_section ) {
					$url_parts = parse_url( html_entity_decode( $menu_item_section[2] ) );
					if ( ( isset( $url_parts['query'] ) ) && ( !empty( $url_parts['query'] ) ) )
						parse_str( $url_parts['query'], $link_params );
					else {
						$link_params = array(
							'post_type'	=>	'',
							'taxonomy'	=>	''
						);
					}
					
					// Add New - We add in the 0 position
					if ( substr( $menu_item_section[2], 0, strlen( 'post-new.php?' ) ) == 'post-new.php?' ) {
						
						$this->admin_tab_sets[$menu_slug][0] = array(
							'id'	=> 	$link_params['post_type'],
							'name'	=>	$menu_item_section[0],
							'cap'	=>	$menu_item_section[1],
							'link'	=>	$menu_item_section[2]
						);
					} 

					// Edit - We add in the 1 position
					else if ( substr( $menu_item_section[2], 0, strlen( 'edit.php?' ) ) == 'edit.php?' ) {
						$this->admin_tab_sets[$menu_slug][1] = array(
							'id'	=> 	'edit-'. $link_params['post_type'],
							'name'	=>	$menu_item_section[0],
							'cap'	=>	$menu_item_section[1],
							'link'	=>	$menu_item_section[2]
						);
					}

					else if ( substr( $menu_item_section[2], 0, strlen( 'edit-tags.php?' ) ) == 'edit-tags.php?' ) {
						$this->add_admin_tab_item(
							$menu_slug,
							array(
								'id'	=> 	'edit-'. $link_params['taxonomy'],
								'name'	=>	$menu_item_section[0],
								'cap'	=>	$menu_item_section[1],
								'link'	=>	$menu_item_section[2]
							),
							40
						);
					}
				}
			}
		}
		
		function add_admin_tab_item( $menu_slug, $menu_item, $menu_priority = 20 ) {
			
			if ( !isset( $this->admin_tab_sets[$menu_slug] ) ) $this->admin_tab_sets[$menu_slug] = array();
			else ksort( $this->admin_tab_sets[$menu_slug] );
			
			while( true ) {
				if ( !isset( $this->admin_tab_sets[$menu_slug][$menu_priority] ) ) {
					$this->admin_tab_sets[$menu_slug][$menu_priority] = $menu_item;
					break;
				}
				$menu_priority += 1;
			}
		}
		
		
		/* The purpose of this early function is to setup the main 'learndash-lms' menu page. Then 
		 * re-position the various custom post type submenu items to be found under it. 
		 */
		function learndash_admin_menu_early() {
			if ( ! is_admin() ) {
				return;
			}			

			global $submenu, $menu;

			$add_submenu = array();
	
			if ( current_user_can('edit_courses') ) {
				if ( isset( $submenu['edit.php?post_type=sfwd-courses'] ) ) {
					$add_submenu['sfwd-courses'] = array(
						'name' 	=> LearnDash_Custom_Label::get_label( 'courses' ),
						'cap'	=> 'edit_courses',
						'link'	=> 'edit.php?post_type=sfwd-courses',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-courses', $submenu['edit.php?post_type=sfwd-courses'] );
				}
				
				if ( isset( $submenu['edit.php?post_type=sfwd-lessons'] ) ) {
					$add_submenu['sfwd-lessons'] = array(
						'name' 	=> LearnDash_Custom_Label::get_label( 'lessons' ),
						'cap'	=> 'edit_courses',
						'link'	=> 'edit.php?post_type=sfwd-lessons',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-lessons', $submenu['edit.php?post_type=sfwd-lessons'] );
				}
				
				if ( isset( $submenu['edit.php?post_type=sfwd-topic'] ) ) {
					$add_submenu['sfwd-topic'] = array(
						'name' 	=> LearnDash_Custom_Label::get_label( 'topics' ),
						'cap'	=> 'edit_courses',
						'link'	=> 'edit.php?post_type=sfwd-topic',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-topic', $submenu['edit.php?post_type=sfwd-topic'] );
				}
				
				if ( isset( $submenu['edit.php?post_type=sfwd-quiz'] ) ) {
					$add_submenu['sfwd-quiz'] = array(
						'name' 	=> LearnDash_Custom_Label::get_label( 'quizzes' ),
						'cap'	=> 'edit_courses',
						'link'	=> 'edit.php?post_type=sfwd-quiz',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-quiz', $submenu['edit.php?post_type=sfwd-quiz'] );
				}
				
				if ( isset( $submenu['edit.php?post_type=sfwd-certificates'] ) ) {
					$add_submenu['sfwd-certificates'] = array(
						'name' 	=> esc_html_x( 'Certificates', 'Certificates Menu Label', 'learndash' ),
						'cap'	=> 'edit_courses',
						'link'	=> 'edit.php?post_type=sfwd-certificates',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-certificates', $submenu['edit.php?post_type=sfwd-certificates'] );
				}
			}
		
			if ( current_user_can('edit_assignments') ) {
				if ( isset( $submenu['edit.php?post_type=sfwd-assignment'] ) ) {
					$add_submenu['sfwd-assignment'] = array(
						'name' 	=> esc_html_x( 'Assignments', 'Assignments Menu Label', 'learndash' ),
						'cap'	=> 'edit_assignments',
						'link'	=> 'edit.php?post_type=sfwd-assignment',
					);
					$this->add_admin_tab_set('edit.php?post_type=sfwd-assignment', $submenu['edit.php?post_type=sfwd-assignment'] );
					$this->admin_tab_sets['edit.php?post_type=sfwd-assignment'] = array();
				}
			}
		
			if ( current_user_can('edit_groups') ) {
				if ( isset( $submenu['edit.php?post_type=groups'] ) ) {
					$add_submenu['groups'] = array(
						'name' 	=> esc_html_x( 'Groups', 'Groups Menu Label', 'learndash' ),
						'cap'	=> 'edit_groups',
						'link'	=> 'edit.php?post_type=groups',
					);
					$this->add_admin_tab_set('edit.php?post_type=groups', $submenu['edit.php?post_type=groups'] );
				}
			}

			if ( learndash_is_group_leader_user() ) {
				$add_submenu['sfwd-essays'] = array(
					'name' 	=> esc_html_x( 'Submitted Essays', 'Submitted Essays Menu Label', 'learndash' ),
					'cap'	=> 'group_leader',
					'link'	=> 'edit.php?post_type=sfwd-essays',
				);
			}
			
			 /**
			 * Filter submenu array before it is registered
			 *
			 * @since 2.1.0
			 *
			 * @param  array  $add_submenu
			 */
			$add_submenu = apply_filters( 'learndash_submenu', $add_submenu );
			
			if (!empty( $add_submenu ) ) {
				add_menu_page(
					esc_html__( 'LearnDash LMS', 'learndash' ),
					esc_html__( 'LearnDash LMS', 'learndash' ),
					'read',
					'learndash-lms',
					null,
					null,
					apply_filters( 'learndash-menu-position', null )
				);

				$location = 0;

				foreach ( $add_submenu as $key => $add_submenu_item ) {
					if ( current_user_can( $add_submenu_item['cap'] ) ) {
						$submenu['learndash-lms'][ $location++ ] = array( $add_submenu_item['name'], $add_submenu_item['cap'], $add_submenu_item['link'] );
					}
				}
				
	   			 /**
	   			 * Action added to trigger add-ons when LD menu and submenu items have been added to the system. 
				 * This works better than trying to fiddle with priority on WP 'admin_menu' hook. 
	   			 *
	   			 * @since 2.4.0
	   			 *
	   			 * @param  string LD menu parent slug 'learndash-lms'. 
	   			 */
				do_action( 'learnadash_admin_menu', 'learndash-lms' );
			}
			
			global $learndash_post_types;
			foreach( $learndash_post_types as $ld_post_type ) {
				$menu_slug = 'edit.php?post_type='. $ld_post_type;
				if ( isset( $submenu[ $menu_slug ] ) ) {
					remove_menu_page( $menu_slug );
				}
			}
		}
		
		function learndash_admin_menu_last() {
			global $submenu, $menu, $_wp_real_parent_file, $_wp_submenu_nopriv, $_registered_pages, $_parent_pages;

			$add_submenu = array();

			if ( ( isset( $submenu['learndash-lms-non-existant'] ) ) && ( !empty( $submenu['learndash-lms-non-existant'] ) ) ) {
				foreach( $submenu['learndash-lms-non-existant'] as $submenu_idx => $submenu_item ) {
					if ( isset( $_parent_pages[$submenu_item[2]] ) ) {
						$_parent_pages[$submenu_item[2]] = 'admin.php?page=learndash_lms_settings';
						
						$submenu['admin.php?page=learndash_lms_settings'][] = $submenu_item;
					}
				}
			}
			
			/**
			 * Allow add-ons and other LD core menus to be added to the bottom of the sub-menu. 
			 *
			 * @since 2.5.6
			 */
			$add_submenu = apply_filters( 'learndash_submenu_last', $add_submenu );
			
			$add_submenu['settings'] = array(
				'name' 	=> esc_html_x( 'Settings', 'Settings Menu Label', 'learndash' ),
				'cap'	=> LEARNDASH_ADMIN_CAPABILITY_CHECK,
				'link'	=> 'admin.php?page=learndash_lms_settings'
			);
			
			foreach ( $add_submenu as $key => $add_submenu_item ) {
				if ( current_user_can( $add_submenu_item['cap'] ) ) {
					$submenu['learndash-lms'][] = array( $add_submenu_item['name'], $add_submenu_item['cap'], $add_submenu_item['link'] );
				}
			}
		}
		
		/**
		 * Set up admin tabs for each admin menu page under LearnDash
		 *
		 * @since 2.1.0
		 */
		function learndash_admin_tabs() {
			if ( ! is_admin() ) {
				return;
			}
            global $submenu, $menu;
			global $learndash_current_page_link;
			$learndash_current_page_link = '';

			$current_screen = get_current_screen();
			
			$current_page_id = $current_screen->id; 

			$current_screen_parent_file = $current_screen->parent_file;
			if ( $current_screen_parent_file == 'learndash-lms' ) {
				if ( $current_screen->id == 'learndash-lms_page_learndash-lms-reports' )
					$current_screen_parent_file = 'admin.php?page=learndash-lms-reports';

				// See LEARNDASH-581:
				// In a normal case when viewing the LearnDash > Courses > All Courses tab the screen ID is set to 'edit-sfwd-courses' and the parent_file is set ''edit.php?post_type=sfwd-courses'.
				// However when the Admin Menu Editor plugin is installed it somehow sets the parent_file to 'learndash-lms'. So below we need to change the value back. Note this is just for the 
				// listing URL. The Add New and other tabs are not effected. 
				if ( $current_screen->id == 'edit-sfwd-courses' )
					$current_screen_parent_file = 'edit.php?post_type=sfwd-courses';

				if ( $current_screen->id == 'edit-sfwd-lessons' )
					$current_screen_parent_file = 'edit.php?post_type=sfwd-lessons';

				if ( $current_screen->id == 'edit-sfwd-topic' )
					$current_screen_parent_file = 'edit.php?post_type=sfwd-topic';

				if ( $current_screen->id == 'edit-sfwd-quiz' )
					$current_screen_parent_file = 'edit.php?post_type=sfwd-quiz';

				if ( $current_screen->id == 'edit-sfwd-certificates' )
					$current_screen_parent_file = 'edit.php?post_type=sfwd-certificates';

				if ( $current_screen->id == 'edit-groups' )
					$current_screen_parent_file = 'edit.php?post_type=groups';


			}

			if ( ( $current_screen_parent_file == 'edit.php?post_type=sfwd-quiz' ) || ( $current_screen_parent_file == 'edit.php?post_type=sfwd-essays' ) ) {
				$post_id = ! empty( $_GET['post_id'] ) ? $_GET['post_id'] : ( empty( $_GET['post'] ) ? 0 : $_GET['post'] );

				if ( ! empty( $_GET['module'] ) ) {
					$current_page_id = $current_page_id .'_'. esc_attr( $_GET['module'] );
				} else if ( !empty( $post_id ) ) {
					$current_page_id = $current_page_id .'_edit';
				}
			
				$this->add_admin_tab_item(
					'edit.php?post_type=sfwd-quiz',
					array(
						'link'			=> 	'admin.php?page=ldAdvQuiz&module=globalSettings',
						'name'			=> 	sprintf( esc_html_x( '%s Options', 'Quiz Options Tab Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
						'id'			=> 	'sfwd-quiz_page_ldAdvQuiz_globalSettings',
						'cap'			=> 	'wpProQuiz_change_settings',
					),
					$this->admin_tab_priorities['high']
				);
				$this->add_admin_tab_item(
					'edit.php?post_type=sfwd-quiz',
					array(
						'link'				=> 	'edit.php?post_type=sfwd-essays',
						'name'				=>  esc_html_x( 'Submitted Essays', 'Quiz Submitted Essays Tab Label', 'learndash' ),
						'id'				=> 	'edit-sfwd-essays',
						'parent_menu_link'	=> 	'edit.php?post_type=sfwd-quiz',
					),
					$this->admin_tab_priorities['high']
				);
				$this->add_admin_tab_item(
					'edit.php?post_type=sfwd-quiz',
					array(
						'link'			=> 	'admin.php?page=ldAdvQuiz',
						'name'			=>  esc_html_x( 'Import/Export', 'Quiz Import/Export Tab Label', 'learndash' ),
						'id'			=> 	'sfwd-quiz_page_ldAdvQuiz',
						'cap'			=> 	'wpProQuiz_export',
					),
					$this->admin_tab_priorities['high']
				);
			}
			
			// Somewhat of a kludge. The essays are shown within the quiz post type menu section. So we can't just use
			// the default logic. But we can (below) copy the quiz tab items to a new tab set for essays. 
			if ( $current_screen_parent_file == 'edit.php?post_type=sfwd-essays' ) {

				$post_id = ! empty( $_GET['post_id'] ) ? $_GET['post_id'] : ( empty( $_GET['post'] ) ? 0 : $_GET['post'] );
				if ( !empty( $post_id ) ) {
					$current_page_id = 'edit-sfwd-essays'; //. $current_page_id;
				}

				$this->admin_tab_sets['edit.php?post_type=sfwd-essays'] = array();
				
				foreach( $this->admin_tab_sets['edit.php?post_type=sfwd-quiz'] as $menu_key => $menu_item ) {
					$this->admin_tab_sets['edit.php?post_type=sfwd-essays'][$menu_key] = $menu_item;
				}
			} 
			
			if ( $current_screen_parent_file == 'edit.php?post_type=sfwd-quiz' ) {	
			
				if ( empty( $post_id ) && ! empty( $_GET['quiz_id'] ) && $current_page_id == 'admin_page_ldAdvQuiz' ) {
					$post_id = learndash_get_quiz_id_by_pro_quiz_id( $_GET['quiz_id'] );
				}
			
				if ( ! empty( $post_id ) ) {
					$quiz_id = learndash_get_setting( $post_id, 'quiz_pro', true );
					if ( ! empty( $quiz_id ) ) {
			
						$this->add_admin_tab_item(
							$current_screen->parent_file,
							array(
								'link'			=> 	'post.php?post='. $post_id .'&action=edit',
								'name'			=> 	sprintf( esc_html_x( 'Edit %s', 'Edit Quiz Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
								'id'			=> 	'sfwd-quiz_edit',
							)
						);
						
						$this->add_admin_tab_item(
							$current_screen->parent_file,
							array(
								'link'			=> 	'admin.php?page=ldAdvQuiz&module=question&quiz_id='. $quiz_id .'&post_id='. $post_id,
								'name'			=>  esc_html_x( 'Questions', 'Quiz Questions Tab Label', 'learndash' ),
								'id'			=> 	'sfwd-quiz_page_ldAdvQuiz_question',
							)
						);
						
						$this->add_admin_tab_item(
							$current_screen->parent_file,
							array(
								'link'			=> 	'admin.php?page=ldAdvQuiz&module=statistics&id='. $quiz_id .'&post_id='. $post_id,
								'name'			=>  esc_html_x( 'Statistics', 'Quiz Statistics Tab Label', 'learndash' ),
								'id'			=> 	'sfwd-quiz_page_ldAdvQuiz_statistics',
							)
						);
						
						$this->add_admin_tab_item(
							$current_screen->parent_file,
							array(
								'link'			=> 	'admin.php?page=ldAdvQuiz&module=toplist&id='. $quiz_id .'&post_id='. $post_id,
								'name'			=>  esc_html_x( 'Leaderboard', 'Quiz Leaderboard Tab Label', 'learndash' ),
								'id'			=> 	'sfwd-quiz_page_ldAdvQuiz_toplist',
							)
						);
					}
				}
			}
			
			if ( ( $current_screen_parent_file == 'admin.php?page=learndash-lms-reports' ) || ( $current_screen_parent_file == 'edit.php?post_type=sfwd-transactions' ) ) {	
						
				$this->add_admin_tab_item(
					$current_screen_parent_file,
					array(
						'id'			=> 	'learndash-lms_page_learndash-lms-reports',
						'name' 			=>  esc_html_x( 'Reports', 'Reports Menu Label', 'learndash' ),
						'cap'			=> 	LEARNDASH_ADMIN_CAPABILITY_CHECK, 
					),
					$this->admin_tab_priorities['high']
					);

				$this->add_admin_tab_item(
					$current_screen_parent_file,
					array(
						'id'				=> 	'edit-sfwd-transactions',
						'name'				=>  esc_html_x( 'Transactions', 'Transactions Tab Label', 'learndash' ),
						'link'				=> 	'edit.php?post_type=sfwd-transactions',
						'parent_menu_link'	=> 	'admin.php?page=learndash-lms-reports',
					),
					$this->admin_tab_priorities['high']
				);
				
				if ( $current_screen_parent_file == 'edit.php?post_type=sfwd-transactions' ) {	
					$post_id = ! empty( $_GET['post_id'] ) ? $_GET['post_id'] : ( empty( $_GET['post'] ) ? 0 : $_GET['post'] );
					if ( !empty( $post_id ) )
						$current_page_id = 'edit-sfwd-transactions'; 
				}
				
				
			}
									
			$admin_tabs_legacy = apply_filters( 'learndash_admin_tabs', array() );
			foreach( $admin_tabs_legacy as $tab_idx => $tab_item ) {
				if ( empty( $tab_item ) ) {
					unset( $admin_tabs_legacy[$tab_idx] );
				} else {
					if ( $admin_tabs_legacy[$tab_idx]['menu_link'] == 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses' )
						$admin_tabs_legacy[$tab_idx]['menu_link'] = 'admin.php?page=learndash_lms_settings';
				}
			}
				
			if ( $current_screen_parent_file == 'learndash-lms-non-existant' ) {
				$menu_link = '';
				foreach( $admin_tabs_legacy as $tab_idx => $tab_item ) {
					if ( $tab_item['id'] == $current_page_id ) {
						$current_screen_parent_file = $tab_item['menu_link'];
						break;
					}
				}
			}
			
			if ( $current_screen_parent_file == 'admin.php?page=learndash_lms_settings' ) {	
				
				$this->add_admin_tab_item(
					'admin.php?page=learndash_lms_settings',
					array(
						'link'			=> 	'admin.php?page=nss_plugin_license-sfwd_lms-settings',
						'name'			=>  esc_html_x( 'LMS License', 'LMS License Tab Label', 'learndash' ),
						'id'			=> 	'admin_page_nss_plugin_license-sfwd_lms-settings',
					),
					50
				);

				do_action('learndash_admin_tabs_set', $current_screen_parent_file, $this );

				// Here we add the legacy tabs to the end of the existing tabs. 
				if (!empty( $admin_tabs_legacy ) ) {
					foreach( $admin_tabs_legacy as $tab_idx => $tab_item ) {
						if ( $tab_item['menu_link'] == $current_screen_parent_file ) {
							$this->add_admin_tab_item(
								$current_screen_parent_file,
								$tab_item,
								80
							);
						}
					}
				}
			}

			if ( $current_screen_parent_file != 'admin.php?page=learndash_lms_settings' ) {	
				do_action('learndash_admin_tabs_set', $current_screen_parent_file, $this );
			}


			$admin_tabs_on_page_legacy = array();
			$admin_tabs_on_page_legacy['sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses'] = array();


			$admin_tabs_on_page_legacy = apply_filters( 'learndash_admin_tabs_on_page', $admin_tabs_on_page_legacy, array(), $current_page_id );
			foreach( $admin_tabs_on_page_legacy as $tab_idx => $tab_set ) {
				if ( empty( $tab_set ) ) unset( $admin_tabs_on_page_legacy[$tab_idx] );
			}

			if ( isset( $admin_tabs_on_page_legacy[$current_page_id] ) ) {
				$admin_tabs_on_page_legacy_set = $admin_tabs_on_page_legacy[$current_page_id];
				if ( ( !empty( $admin_tabs_on_page_legacy_set ) ) && ( is_array( $admin_tabs_on_page_legacy_set ) ) ) {
					foreach( $admin_tabs_on_page_legacy_set as $admin_tab_idx ) {
						if ( isset( $admin_tabs_legacy[$admin_tab_idx] ) ) {
							$admin_tab_item = $admin_tabs_legacy[$admin_tab_idx];
							$current_screen_parent_file = $admin_tab_item['menu_link'];
							$this->add_admin_tab_item(
								$admin_tab_item['menu_link'],
								$admin_tab_item,
								80
							);
							unset( $admin_tabs_legacy[$admin_tab_idx] );
						}
						unset( $admin_tabs_on_page_legacy_set[$admin_tab_idx] );
					}
				}
			}
			
			$this->show_admin_tabs( $current_screen_parent_file, $current_page_id );
		}


		function show_admin_tabs( $menu_tab_key = '', $current_page_id = '' ) {
			
			if ( isset( $this->admin_tab_sets[$menu_tab_key] ) ) {

				if ( !empty( $this->admin_tab_sets[$menu_tab_key] ) ) {
					
					ksort( $this->admin_tab_sets[$menu_tab_key] );

					/**
					 * Filter for current admin tab set
					 * @since 2.5
					 */
					$this->admin_tab_sets[$menu_tab_key] = apply_filters('learndash_admin_tab_sets', $this->admin_tab_sets[$menu_tab_key], $menu_tab_key, $current_page_id );
					if ( !empty( $this->admin_tab_sets[$menu_tab_key] ) ) {
						echo '<h1 class="nav-tab-wrapper">';

						$post_id = ! empty( $_GET['post_id'] ) ? $_GET['post_id'] : ( empty( $_GET['post'] ) ? 0 : $_GET['post'] );

						foreach ( $this->admin_tab_sets[$menu_tab_key] as $admin_tab_item ) {		
							if ( !isset( $admin_tab_item['id'] ) ) 
								$admin_tab_item['id'] = '';
						
							if ( ! empty( $admin_tab_item['id'] ) ) {
					
								if ( $admin_tab_item['id'] == $current_page_id ) {
									$class = 'nav-tab nav-tab-active';
						
									global $learndash_current_page_link;
									if ( ( isset( $admin_tab_item['parent_menu_link'] ) ) && ( !empty( $admin_tab_item['parent_menu_link'] ) ) ) 
										$learndash_current_page_link = trim( $admin_tab_item['parent_menu_link'] );
									else
										$learndash_current_page_link = $menu_tab_key;
								
									add_action( 'admin_footer', 'learndash_select_menu' );
						
								} else {
									$class = 'nav-tab';
								}
					
								$target = ! empty( $admin_tab_item['target'] ) ? 'target="'. $admin_tab_item['target'].'"':'';
					
								$url = '';
								if ( ( isset( $admin_tab_item['external_link'] ) ) && ( !empty( $admin_tab_item['external_link'] ) ) ) {
									$url = $admin_tab_item['external_link'];
								} else if ( ( isset( $admin_tab_item['link'] ) ) && ( !empty( $admin_tab_item['link'] ) ) ) {
									$url = $admin_tab_item['link'];
								
								} else {
									if ( false !== ( $pos = strpos( $admin_tab_item['id'], 'learndash-lms_page_' ) ) ) {
										$url_page = str_replace( 'learndash-lms_page_', '', $admin_tab_item['id'] );
										$url = add_query_arg( array( 'page' => $url_page ), 'admin.php' );
									}
								}
														
								if ( !empty( $url ) ) {
									echo '<a href="'. $url .'" class="'. $class .' nav-tab-'. $admin_tab_item['id'] .'"  '. $target .'>'. $admin_tab_item['name'] .'</a>';
								}
							}
						}
						echo '</h1>';
					}
				} else {
					global $learndash_current_page_link;
					$learndash_current_page_link = $menu_tab_key;
					add_action( 'admin_footer', 'learndash_select_menu' );
				}
			}
		}
		
		// End of methods
	}
}
$ld_admin_menus_tabs = Learndash_Admin_Menus_Tabs::get_instance();

function learndash_add_admin_tab_item( $menu_slug, $menu_item, $menu_priority ) {
	Learndash_Admin_Menus_Tabs::get_instance()->add_admin_tab_item( $menu_slug, $menu_item, $menu_priority );
}