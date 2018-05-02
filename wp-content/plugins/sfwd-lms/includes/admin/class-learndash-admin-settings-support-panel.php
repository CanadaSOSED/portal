<?php
if (!class_exists('Learndash_Admin_Settings_Support_Panel')) {
	class Learndash_Admin_Settings_Support_Panel {

		private $mo_files = array();
		
		private $template_array = array();
		
		private $db_tables = array( 'learndash_user_activity', 'learndash_user_activity_meta', 'wp_pro_quiz_category', 'wp_pro_quiz_form', 'wp_pro_quiz_lock', 'wp_pro_quiz_master', 'wp_pro_quiz_prerequisite', 'wp_pro_quiz_question', 'wp_pro_quiz_statistic', 'wp_pro_quiz_statistic_ref', 'wp_pro_quiz_template', 'wp_pro_quiz_toplist' );
		private $php_ini_settings = array( 'max_execution_time', 'max_input_time', 'max_input_vars', 'post_max_size', 'max_file_uploads', 'upload_max_filesize' );
		private $php_extensions = array( 'mbstring' );

		private $system_info = array();
		
		function __construct() {
			$this->parent_menu_page_url		=	'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'learndash_support'; 
			$this->settings_page_title 		= 	_x( 'Support', 'Support Tab Label', 'learndash' );
			$this->settings_tab_title		=	$this->settings_page_title;
			$this->settings_tab_priority	=	40;
			
			add_action( 'admin_menu', 			array( $this, 'admin_menu' ) );
			add_action( 'load_textdomain', array( $this, 'load_textdomain' ), 10, 2 );
			add_action( 'learndash_admin_tabs_set', array( $this, 'admin_tabs' ), 10 );
		}
		
		/**
		 * Register settings page
		 */
		public function admin_menu() {
			$this->settings_screen_id = add_submenu_page(
				$this->parent_menu_page_url,
				$this->settings_page_title,
				$this->settings_page_title,
				$this->menu_page_capability,
				$this->settings_page_id,
				array( $this, 'admin_page' )
			);
			add_action( 'load-'. $this->settings_screen_id, array( $this, 'on_load_panel' ) );
		}
		
		function admin_tabs( $admin_menu_section = '' ) {

			if ( $admin_menu_section == $this->parent_menu_page_url ) {
				learndash_add_admin_tab_item(
					$this->parent_menu_page_url,
					array(
						'id'	=> 	$this->settings_screen_id,
						'link'	=> 	add_query_arg( array( 'page' => $this->settings_page_id ), 'admin.php' ),
						'name'	=> 	!empty( $this->settings_tab_title )  ? $this->settings_tab_title : $this->settings_page_title,
					),
					$this->settings_tab_priority
				);
			}
		}

		// Track the loaded MO files for our text domain. This is used on Support tab
		function load_textdomain( $domain = '', $mofile = '' ) {			
			if ( ( $domain == LEARNDASH_LMS_TEXT_DOMAIN ) || ( $domain == LEARNDASH_WPPROQUIZ_TEXT_DOMAIN ) && ( !empty( $mofile ) ) ) {
				if ( file_exists( $mofile ) ) {
					if  ( !isset( $this->mo_files[$domain] ) ) {
						$this->mo_files[$domain] = array();
					}
				
					if ( !isset( $this->mo_files[$mofile] ) ) {
						$this->mo_files[$domain][$mofile] = $mofile;
					}
				}
			}
		}
						
		function on_load_panel() {			
			global $sfwd_lms;
			
			$this->gather_system_details();
			
			//download-system-info
			if ( ( isset( $_GET['ld_download_system_info_nonce'] ) ) && ( !empty( $_GET['ld_download_system_info_nonce'] ) ) && ( wp_verify_nonce( $_GET['ld_download_system_info_nonce'], 'ld_download_system_info_'. get_current_user_id() ) ) ) { 
				header("Content-type: text/plain");
				header("Content-Disposition: attachment; filename=ld_system_info-". date('Ymd').".txt");
				$this->show_system_info( 'text' );
				die();
			}
			
			
			if ( ( isset( $_POST['ld_data_remove_nonce'] ) ) && ( !empty( $_POST['ld_data_remove_nonce'] ) ) && ( wp_verify_nonce( $_POST['ld_data_remove_nonce'], 'ld_data_remove_'. get_current_user_id() ) ) ) { 

				if ( ( isset( $_POST['ld_data_remove_verify'] ) ) && ( !empty( $_POST['ld_data_remove_verify'] ) ) && ( wp_verify_nonce( $_POST['ld_data_remove_verify'], 'ld_data_remove_'. get_current_user_id() ) ) ) { 
					learndash_delete_all_data();

					$active_plugins = (array) get_option( 'active_plugins', array() );
					if ( !empty( $active_plugins ) ) {
						$active_plugins = array_diff( $active_plugins, array( LEARNDASH_LMS_PLUGIN_KEY ) );
						update_option( 'active_plugins', $active_plugins );
						
						// Hook into our own deactivate function
						$sfwd_lms->deactivate();
							
						// finally redirect the admin to the plugins listing. 	
						wp_redirect( admin_url( 'plugins.php') );
						
						die();
					}
				}
			}
			
			// Load JS/CSS as needed for page
			wp_enqueue_style( 
				'sfwd-module-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array(), 
				LEARNDASH_SCRIPT_VERSION_TOKEN 
			);
			$learndash_assets_loaded['styles']['sfwd-module-style'] = __FUNCTION__;
		}

		/**
		 * Output settings page
		 */
		public function admin_page() {
			global $wpdb, $wp_version;

			?>
			<div id="learndash-settings-support" class="learndash-settings" class="wrap">
				<h1><?php _e( 'Support', 'learndash' ); ?></h1>
				<p><a class="button button-primary" target="_blank" href="http://support.learndash.com/"><?php _e('Go to LearnDash Support', 'learndash') ?></a><?php
					if ( learndash_is_admin_user() ) {
						?> <a class="button button-primary" href="<?php echo add_query_arg('ld_download_system_info_nonce', wp_create_nonce( 'ld_download_system_info_'. get_current_user_id() )) ?>"><?php _e('Download System Info', 'learndash') ?></a><?php
					}
					?>
				</p>
				<hr />
				
				<?php
					$this->show_system_info( 'html' );
					$this->show_copy_textarea();
					$this->show_reset();
				?>
			</div>
			<?php
		}
		
		function show_system_info( $output_type = 'html' ) {
			if ( !empty( $this->system_info ) ) {
				switch( $output_type ) {
					case 'text':
						foreach( $this->system_info as $_key => $_set ) {
							echo strtoupper( $_set['header']['text'] ) ."\r\n";
				
							if ( ( isset( $_set['columns'] ) ) && ( !empty( $_set['columns'] ) ) && ( isset( $_set['settings'] ) ) && ( !empty( $_set['settings'] ) ) ) {
								foreach( $_set['settings'] as $setting_key => $setting_set ) { 
									$_SHOW_FIRST = false;
									foreach( $_set['columns'] as $column_key => $column_set ) { 
										$value = strip_tags( str_replace( array('<br />', '<br>', '<br >'), "\r\n", $setting_set[$column_key] ) );
										// Add some format spacing to make the raw txt version easier to read. 
										$spaces_needed = 50 - strlen( $value );
										if ( $spaces_needed > 0 ) {
											$value .= str_repeat( ' ', $spaces_needed );
										}
										echo $value;
									}
									echo "\r\n";
								}
							}
							echo "\r\n";
						}
						break;
				
					case 'html':
					default:
						foreach( $this->system_info as $_key => $_set ) {
							if ( isset( $_set['header']['html'] ) ) {
								?><h2><?php echo $_set['header']['html'] ?></h2><?php
							} else if ( isset( $_set['header']['text'] ) ) {
								?><h2><?php echo $_set['header']['text'] ?></h2><?php
							}
					
							if ( isset( $_set['desc'] ) ) {
								?><div class="learndash-support-settings-desc"><?php echo wptexturize( $_set['desc'] ); ?></div><?php
							}
					
							if ( ( isset( $_set['columns'] ) ) && ( !empty( $_set['columns'] ) ) 
							  && ( isset( $_set['settings'] ) ) && ( !empty( $_set['settings'] ) ) ) {
								?>
								<table cellspacing="0" class="learndash-support-settings">
									<thead>
										<tr>
										<?php 
											foreach( $_set['columns'] as $column_key => $column_set ) { 
												$column_class = '';
												if ( isset( $column_set['class'] ) )
													$column_class = $column_set['class'];
												$column_class = apply_filters('learndash_support_column_class', $column_class, $column_key, $_key );
												?>
												<th scope="col" class="<?php echo $column_class ?>">
												<?php
													if ( isset( $column_set['html'] ) ) {
														echo $column_set['html'];
													} else if ( isset($column_set['text'] ) ) {
														echo $column_set['text'];
													}
												?>
												</th>
												<?php
											}
										?>
										</tr>
									</thead>
									<body>
										<?php 
											foreach( $_set['settings'] as $setting_key => $setting_set ) { 
												?><tr><?php
												foreach( $_set['columns'] as $column_key => $column_set ) { 
													?>
													<td scope="col" class="<?php apply_filters('learndash_support_column_class', '', $column_key, $_key ) ?>">
													<?php
														if ( isset( $setting_set[$column_key .'_html'] ) ) {
															echo $setting_set[$column_key .'_html'];
														} else if ( isset( $setting_set[$column_key] ) ) {
															echo $setting_set[$column_key];
														}
													?>
													</td>
													<?php
												}
												?></tr><?php
											}
										?>
									</body>
								</table>
								<?php
							}
						}
				}
			}
		}
		
		function show_copy_textarea() {
			?>
			<h2><?php _e('Copy System Info', 'learndash' ) ?></h2>
			<textarea id="ld-system-info-text" style="width: 80%; min-height: 80px; font-family: monospace"><?php echo $this->show_system_info( 'text' ) ?></textarea><br />
			<p><button id="ld-system-info-copy-button"><?php _e('Copy to Clipboard', 'learndash') ?></button> 
				<span style="display:none" id="ld-copy-status-success"><?php _e( 'Copy Success', 'learndash' ); ?></span><span style="display:none" id="ld-copy-status-failed"><?php _e('Copy Failed', 'learndash' ); ?></span></p>
			<script>
			var copyBtn = document.querySelector('#ld-system-info-copy-button');  
			copyBtn.addEventListener('click', function(event) {  
			  // Select the email link anchor text  
			  var copy_text = document.querySelector('#ld-system-info-text');  
			  var range = document.createRange();  
			  range.selectNode(copy_text);  
			  window.getSelection().addRange(range);  

			  try {  
			    // Now that we've selected the anchor text, execute the copy command  
			    var successful = document.execCommand('copy');  
				if ( successful ) {
					jQuery( '#ld-copy-status-success').show();
				}
			  } catch(err) {  
					console.log('Oops, unable to copy');  
			  }  

			  // Remove the selections - NOTE: Should use
			  // removeRange(range) when it is supported  
			  window.getSelection().removeAllRanges();  
			});
			</script>
			<?php
		}
		
		function show_reset() {
			if ( learndash_is_admin_user() ) {
				$remove_nonce = wp_create_nonce( 'ld_data_remove_'. get_current_user_id() );
				?>
				<hr style="margin-top: 30px; border-top: 5px solid red;"/>
				<h2><?php _e('Reset LearnDash', 'learndash' ); ?></h2>
				<div class="learndash-support-settings-desc"><p><?php _e('<span style="color:red;">Warning: This will remove ALL LearnDash data including any custom database tables.</style></span>', 'learndash' ) ?></p></div>
				<form id="ld_data_remove_form" method="POST">
					<input type="hidden" name="ld_data_remove_nonce" value="<?php echo $remove_nonce ?>"
					<p><label for="ld_data_remove_verify"><?php _e('<strong>Confirm the data deletion</strong>', 'learndash' ); ?></label><br /> 
					<input id="ld_data_remove_verify" name="ld_data_remove_verify" type="password" size="50" value="" /><br />
					<span class="description"><?php echo sprintf( _x('Enter <code>%s</code> in the above field and click the submit button', 'placeholder: secret generated code', 'learndash'), $remove_nonce ) ?></span></p>

					<p><input type="submit" value="<?php _e('Submit', 'learndash') ?>" /></p>
				</form>
				<script>
					jQuery('form#ld_data_remove_form').submit( function( event ) {
						var ld_data_remove_verify = jQuery('input#ld_data_remove_verify').val();
						if ( ld_data_remove_verify !== '' ) {
							if ( !confirm( '<?php _e( 'Are you sure that you want to remove ALL LearnDash data?', 'learndash' ) ?>' ) ) {
								event.preventDefault();
								return;
							}
						}
					});
				</script>
				<?php 
			}
		}
		
		function load_templates() {
			$LEARNDASH_LMS_PLUGIN_DIR_tmp = str_replace( '\\', '/', LEARNDASH_LMS_PLUGIN_DIR );
			$this->template_array = glob( $LEARNDASH_LMS_PLUGIN_DIR_tmp . 'templates/*.{php,js,css}', GLOB_BRACE);
			if ( !empty( $this->template_array ) ) {
				foreach( $this->template_array as $idx => $template_file ) {
					$filename = basename( $template_file );
				
					if ( strpos( $filename, '.min.') !== false) {
						unset( $this->template_array[$idx] );
					} else if ( substr( $filename, -4 ) === '.php') {
						// for PHP files we remove the 
						$this->template_array[$idx] = substr($filename, 0, -4);
					} else {
						$this->template_array[$idx] = $filename;
					}
				}
			}
		}
		
		/**
		 * Used to collect all needed display elements. Many filters by section as well as a final filter
		 * 
		 * @since v2.5.4
		 */
		function gather_system_details() {
			global $wpdb, $wp_version, $wp_rewrite;
			global $sfwd_lms;
			
			$ABSPATH_tmp = str_replace('\\', '/', ABSPATH );
			$LEARNDASH_LMS_PLUGIN_DIR_tmp = str_replace( '\\', '/', LEARNDASH_LMS_PLUGIN_DIR );
			$LEARNDASH_TEMPLATES_DIR_tmp = str_replace( '\\', '/', LEARNDASH_TEMPLATES_DIR );
			$CHILD_THEME_TEMPLATE_DIR_tmp = str_replace( '\\', '/', get_stylesheet_directory() );
			$PARENT_THEME_TEMPLATE_DIR_tmp = str_replace( '\\', '/', get_template_directory() );
			
			/************************************************************************************************
			 * LearnDash Settings 
			 ************************************************************************************************/
			
			$settings_set = array();
			//$this->system_info['Learndash Settings'] = array();
			
			$settings_set['header'] = array(
				'html' => esc_html__( 'Learndash Settings', 'learndash' ),
				'text' => 'Learndash Settings'
			);

 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Setting', 'learndash' ),
					'text' => 'Setting',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Value', 'learndash' ),
					'text' => 'Value',
					'class' => 'learndash-support-settings-right',
				)
			);

			$settings_set['settings'] = array();

			$element = Learndash_Admin_Settings_Data_Upgrades::get_instance();
			
			$ld_license_info = get_option( 'nss_plugin_info_sfwd_lms' );
			
			if ( ( $ld_license_info ) && ( property_exists( $ld_license_info, 'new_version' ) ) && ( !empty( $ld_license_info->new_version ) ) ) {
				if ( version_compare( LEARNDASH_VERSION, $ld_license_info->new_version, '<' ) ) {
					$LEARNDASH_VERSION_value_html = '<span style="color: red">'. LEARNDASH_VERSION .'</span>' . ' '. sprintf( esc_html_x('A newer version of LearnDash (%s) is available.', 'placeholder: version number', 'learndash' ), $ld_license_info->new_version ) . ' <a href="'. admin_url('plugins.php?plugin_status=upgrade') .'">'. esc_html__('Please upgrade.', 'learndash' ) .'</a>';;
					$LEARNDASH_VERSION_value = LEARNDASH_VERSION .' - (X)';
					
				} else {
					$LEARNDASH_VERSION_value_html = '<span style="color: green">'. LEARNDASH_VERSION .'</span>';
					$LEARNDASH_VERSION_value = LEARNDASH_VERSION;
				}
				
			} else {
				$LEARNDASH_VERSION_value = LEARNDASH_VERSION;
				$LEARNDASH_VERSION_value_html = LEARNDASH_VERSION;
			}

			$ld_prior_version = $element->get_data_settings( 'prior_version' );
			if ( ( !empty( $ld_prior_version ) ) && ( $ld_prior_version != LEARNDASH_VERSION ) ) {
				$LEARNDASH_VERSION_value .= sprintf( ' (upgraded from %s)', $ld_prior_version );
				$LEARNDASH_VERSION_value_html .= sprintf( esc_html_x(' (upgraded from %s)', 'placeholder: prior LearnDash version', 'learndash' ), $ld_prior_version );
			}
			
			$settings_set['settings']['LEARNDASH_VERSION'] = array(
				'label' => 'Learndash Version',
				'label_html' => esc_html__( 'Learndash Version', 'learndash' ),
				'value' => $LEARNDASH_VERSION_value,
				'value_html' => $LEARNDASH_VERSION_value_html,
			);


			$ld_license_valid = get_option( 'nss_plugin_remote_license_sfwd_lms' );
			$ld_license_check = get_option( 'nss_plugin_check_sfwd_lms' );
			
			if ( ( isset( $ld_license_valid['value'] ) ) && ( $ld_license_valid['value'] == 1 ) ) {
				$license_value_html = '<span style="color: green">'. esc_html__('Yes', 'learndash' ) .'</span>';
				$license_value = 'Yes';
				if ( !empty( $ld_license_check ) ) {
					$license_value_html .= ' ('. sprintf( esc_html_x('last check: %s', 'placeholder: date', 'learndash' ), learndash_adjust_date_time_display( $ld_license_check ) ) .')';
					$license_value .= ' (last check: '. learndash_adjust_date_time_display( $ld_license_check ) .')';
				} 
			} else {
				$license_value_html = '<span style="color: red">'. esc_html__('No', 'learndash' ) .'</span>';
				$license_value = 'No (X)';
			}
			$settings_set['settings']['LEARNDASH_license'] = array(
				'label' => 'LearnDash License Valid',
				'label_html' => esc_html__( 'LearnDash License Valid', 'learndash' ),
				'value' => $license_value,
				'value_html' => $license_value_html
			);
			
			$settings_set['settings']['LEARNDASH_SETTINGS_DB_VERSION'] = array(
				'label' => 'DB Version',
				'label_html' => esc_html__( 'DB Version', 'learndash' ),
				'value' => LEARNDASH_SETTINGS_DB_VERSION
			);

			$data_settings_courses = $element->get_data_settings('user-meta-courses');
			if ( ( !empty( $data_settings_courses ) ) && ( !empty( $data_settings_courses ) ) ) {
				if ( version_compare( $data_settings_courses['version'], LEARNDASH_SETTINGS_DB_VERSION, '<' ) ) {
					$color = 'red';
					$color_text = ' (X)';
				} else {
					$color = 'green';
					$color_text = '';
				}
				$data_upgrade_courses_value = $data_settings_courses['version'] . $color_text;
				$data_upgrade_courses_value_html = '<span style="color: '. $color.'">'. $data_settings_courses['version'] .'</span>';
				
				if ( $color == 'red' ) {
					$data_upgrade_courses_value_html .= ' <a href="'. admin_url('admin.php?page=learndash_data_upgrades') .'">'. esc_html__('Please run the Data Upgrade.', 'learndash' ) .'</a>';
				} else if ( ( isset( $data_settings_courses['last_run'] ) ) && ( !empty( $data_settings_courses['last_run'] ) ) ) {
					$data_upgrade_courses_value .= ' ('. learndash_adjust_date_time_display( $data_settings_courses['last_run'] ) .')';
	 				$data_upgrade_courses_value_html .= ' ('. sprintf( esc_html_x('last run %s', 'placeholder: datetime', 'learndash'), learndash_adjust_date_time_display( $data_settings_courses['last_run'] ) ) .')';
				}
			} else {
				$data_upgrade_courses_value = '';
				$data_upgrade_courses_value_html = '';
			}
			$settings_set['settings']['Date Upgrade Courses'] = array(
				'label' => 'Date Upgrade Courses',
				'label_html' => esc_html__( 'Date Upgrade Courses', 'learndash' ),
				'value' => $data_upgrade_courses_value,
				'value_html' => $data_upgrade_courses_value_html
			);

			$data_settings_quizzes = $element->get_data_settings('user-meta-quizzes');
			if ( ( !empty( $data_settings_quizzes ) ) && ( !empty( $data_settings_quizzes ) ) ) {
				if ( version_compare( $data_settings_quizzes['version'], LEARNDASH_SETTINGS_DB_VERSION, '<' ) ) {
					$color = 'red';
					$color_text = ' (X)';
				} else {
					$color = 'green';
					$color_text = '';
				}
				$data_upgrade_quizzes_value = $data_settings_quizzes['version'] . $color_text;
				$data_upgrade_quizzes_value_html = '<span style="color: '. $color .'">'. $data_settings_quizzes['version'] .'</span>';
				if ( $color == 'red' ) {
					$data_upgrade_quizzes_value_html .= ' <a href="'. admin_url('admin.php?page=learndash_data_upgrades') .'">'. esc_html__('Please run the Data Upgrade.', 'learndash' );
				} else if ( ( isset( $data_settings_quizzes['last_run'] ) ) && ( !empty( $data_settings_quizzes['last_run'] ) ) ) {
					$data_upgrade_quizzes_value .= ' ('. learndash_adjust_date_time_display( $data_settings_quizzes['last_run'] ) .')';
	 				$data_upgrade_quizzes_value_html .= ' ('. sprintf( esc_html__('last run %s', 'placeholder: datetime', 'learndash'), learndash_adjust_date_time_display( $data_settings_quizzes['last_run'] ) ) .')';
				 }
			} else {
				$data_upgrade_quizzes_value = '';
				$data_upgrade_quizzes_value_html = '';
			}

			$settings_set['settings']['Date Upgrade Quizzes'] = array(
				'label' => 'Date Upgrade Quizzes',
				'label_html' => esc_html__( 'Date Upgrade Quizzes', 'learndash' ),
				'value' => $data_upgrade_quizzes_value,
				'value_html' => $data_upgrade_quizzes_value_html
			);

			$courses_count = wp_count_posts( 'sfwd-courses' );
			$settings_set['settings']['courses_count'] = array(
				'label' => 'Courses Count',
				'label_html' => esc_html__( 'Courses Count', 'learndash' ),
				'value' => $courses_count->publish
			);

			$lessons_count = wp_count_posts( 'sfwd-lessons' );
			$settings_set['settings']['lessons_count'] = array(
				'label' => 'Lessons Count',
				'label_html' => esc_html__( 'Lessons Count', 'learndash' ),
				'value' => $lessons_count->publish
			);

			$topics_count = wp_count_posts( 'sfwd-topic' );
			$settings_set['settings']['topics_count'] = array(
				'label' => 'Topics Count',
				'label_html' => esc_html__( 'Topics Count', 'learndash' ),
				'value' => $topics_count->publish
			);

			$quizzes_count = wp_count_posts( 'sfwd-quiz' );
			$settings_set['settings']['quizzes_count'] = array(
				'label' => 'Quizzes Count',
				'label_html' => esc_html__( 'Quizzes Count', 'learndash' ),
				'value' => $quizzes_count->publish
			);


			$settings_set['settings']['courses_autoenroll_admin_users'] = array(
				'label' => 'Courses Auto-enroll',
				'label_html' => sprintf( _x( '%s Auto-enroll', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' ) === 'yes' ) ? 'Yes' : 'No',
				'value_html' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' ) === 'yes' ) ? esc_html__('Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);
			$settings_set['settings']['bypass_course_limits_admin_users'] = array(
				'label' => 'Bypass Course limits',
				'label_html' => sprintf( _x( 'Bypass %s limits', 'placeholder: Course','learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' ) === 'yes' ) ? 'Yes' : 'No',
				'value_html' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' ) === 'yes' ) ? esc_html__('Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);
			
			$settings_set['settings']['reports_include_admin_users'] = array(
				'label' => 'Include in Reports',
				'label_html' => esc_html__( 'Include in Reports', 'learndash' ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'reports_include_admin_users' ) === 'yes' ) ? 'Yes' : 'No',
				'value_html' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_General_Admin_User', 'reports_include_admin_users' ) === 'yes' ) ? esc_html__('Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);
			
			
			$settings_set['settings']['course_builder'] = array(
				'label' => 'Course Builder Interface',
				'label_html' => sprintf( _x( '%s Builder Interface', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) === 'yes' ) ? 'Yes' : 'No',
				'value_html' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'enabled' ) === 'yes' ) ? esc_html__('Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);

			$settings_set['settings']['course_shared_steps'] = array(
				'label' => 'Shared Course Steps',
				'label_html' => sprintf( _x( 'Shared %s Steps', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) === 'yes' ) ? 'Yes' : 'No',
				'value_html' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) === 'yes' ) ? esc_html__('Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);
			
			$settings_set['settings']['nested_urls'] = array(
				'label' => 'Nested URLs',
				'label_html' => esc_html__( 'Nested URLs', 'learndash' ),
				'value' => ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'nested_urls' ) === 'yes' ) ? esc_html__( 'Yes', 'learndash' ) : esc_html__( 'No', 'learndash' ),
			);

			$settings_set['settings']['courses_permalink_slug'] = array(
				'label' => 'Courses Permalink slug',
				'label_html' => sprintf( _x( '%s Permalink slug', 'placeholder: Courses', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				'value' => '/'. LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'courses' ),
			);
			$settings_set['settings']['lessons_permalink_slug'] = array(
				'label' => 'Lessons Permalink slug',
				'label_html' => sprintf( _x( '%s Permalink slug', 'placeholder: Lessons', 'learndash' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
				'value' => '/'. LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'lessons' ),
			);
			$settings_set['settings']['topics_permalink_slug'] = array(
				'label' => 'Topics Permalink slug',
				'label_html' => sprintf( _x( '%s Permalink slug', 'placeholder: Topics', 'learndash' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
				'value' => '/'. LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'topics' ),
			);
			$settings_set['settings']['quizzes_permalink_slug'] = array(
				'label' => 'Quizzes Permalink slug',
				'label_html' => sprintf( _x( '%s Permalink slug', 'placeholder: Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
				'value' => '/'. LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Section_Permalinks', 'quizzes' ),
			);


			$learndash_settings_permalinks_taxonomies = get_option( 'learndash_settings_permalinks_taxonomies' );
			if ( !is_array( $learndash_settings_permalinks_taxonomies ) ) $learndash_settings_permalinks_taxonomies = array();
			$learndash_settings_permalinks_taxonomies = wp_parse_args(
				$learndash_settings_permalinks_taxonomies, 
				array(
					'ld_course_category' 	=>	'course-category',
					'ld_course_tag' 		=>	'course-tag',
					'ld_lesson_category' 	=> 	'lesson-category',
					'ld_lesson_tag' 		=> 	'lesson-tag',
					'ld_topic_category'		=> 	'topic-category',
					'ld_topic_tag' 			=> 	'topic-tag'
				)
			);

			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes') {				
				$courses_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-courses', 'taxonomies' );
				if ( ( isset( $courses_taxonomies['ld_course_category'] ) ) && ( $courses_taxonomies['ld_course_category']['public'] == true ) ) {
					$settings_set['settings']['ld_course_category'] = array(
						'label' => 'Courses Category base',
						'label_html' => sprintf( _x( '%s Category base', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_course_category'],
					);
				}
				
				if ( ( isset( $courses_taxonomies['ld_course_tag'] ) ) && ( $courses_taxonomies['ld_course_tag']['public'] == true ) ) {
					$settings_set['settings']['ld_course_tag'] = array(
						'label' => 'Courses Tag',
						'label_html' => sprintf( _x( '%s Tag base', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_course_tag'],
					);
				}
			}

			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) == 'yes') {				
				$lessons_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-lessons', 'taxonomies' );
				if ( ( isset( $lessons_taxonomies['ld_lesson_category'] ) ) && ( $lessons_taxonomies['ld_lesson_category']['public'] == true ) ) {
					$settings_set['settings']['ld_lesson_category'] = array(
						'label' => 'Lesson Category base',
						'label_html' => sprintf( _x( '%s Category base', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_lesson_category'],
					);
				}
				
				if ( ( isset( $lessons_taxonomies['ld_lesson_tag'] ) ) && ( $lessons_taxonomies['ld_lesson_tag']['public'] == true ) ) {
					$settings_set['settings']['ld_lesson_tag'] = array(
						'label' => 'Lessons Tag',
						'label_html' => sprintf( _x( '%s Tag base', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_lesson_tag'],
					);
				}
			}

			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes' ) {
				$topics_taxonomies = $sfwd_lms->get_post_args_section( 'sfwd-topic', 'taxonomies' );
				if ( ( isset( $topics_taxonomies['ld_topic_category'] ) ) && ( $topics_taxonomies['ld_topic_category']['public'] == true ) ) {
					$settings_set['settings']['ld_topic_category'] = array(
						'label' => 'Topics Category base',
						'label_html' => sprintf( _x( '%s Category base', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_topic_category'],
					);
				}
				
				if ( ( isset( $topics_taxonomies['ld_topic_tag'] ) ) && ( $topics_taxonomies['ld_topic_tag']['public'] == true ) ) {
					$settings_set['settings']['ld_topic_tag'] = array(
						'label' => 'Topics Tag',
						'label_html' => sprintf( _x( '%s Tag base', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
						'value' => '/'. $learndash_settings_permalinks_taxonomies['ld_topic_tag'],
					);
				}
			}

			// LD Assignment upload path
			$upload_dir = wp_upload_dir();
			$upload_dir_base = str_replace( '\\', '/', $upload_dir['basedir'] );
			$upload_url_base = $upload_dir['baseurl'];
			
			$assignment_upload_dir_path = $upload_dir_base . '/assignments';
			$assignment_upload_dir_path_r = str_replace( $ABSPATH_tmp, '', $assignment_upload_dir_path );
			$settings_set['settings']['Assignment Upload Dir'] = array(
				'label' => 'Assignment Upload Dir',
				'label_html' => esc_html__( 'Assignment Upload Dir', 'learndash' ),
				'value' => $assignment_upload_dir_path_r
			);
			
			$color = 'green';
			
			if ( ! file_exists( $assignment_upload_dir_path ) ) {
				$color = 'red';
				$settings_set['settings']['Assignment Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $assignment_upload_dir_path_r .'</span>'; 
				$settings_set['settings']['Assignment Upload Dir']['value_html'] .= ' - '. esc_html__( 'Directory does not exists', 'learndash' );

				$settings_set['settings']['Assignment Upload Dir']['value'] .= " - (X) ". 'Directory does not exists';

			} else if ( !is_writable( $assignment_upload_dir_path ) ) {
				$color = 'red';
				$settings_set['settings']['Assignment Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $assignment_upload_dir_path_r .'</span>'; 
				$settings_set['settings']['Assignment Upload Dir']['value_html'] .= ' - '. esc_html__( 'Directory not writable', 'learndash' );

				$settings_set['settings']['Assignment Upload Dir']['value'] .= ' - (X) '. 'Directory not writable';

			} else {
				$settings_set['settings']['Assignment Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $assignment_upload_dir_path_r .'</span>'; 
			}


			$essay_upload_dir_path = $upload_dir_base . '/essays';
			$essay_upload_dir_path_r = str_replace( $ABSPATH_tmp, '', $essay_upload_dir_path );
			$settings_set['settings']['Essay Upload Dir'] = array(
				'label' => 'Essay Upload Dir',
				'label_html' => esc_html__( 'Essay Upload Dir', 'learndash' ),
				'value' => $essay_upload_dir_path_r
			);
			
			$color = 'green';
			
			if ( ! file_exists( $essay_upload_dir_path ) ) {
				$color = 'red';
				$settings_set['settings']['Essay Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $essay_upload_dir_path_r .'</span>'; 
				$settings_set['settings']['Essay Upload Dir']['value_html'] .= ' - '. esc_html__( 'Directory does not exists', 'learndash' );

				$settings_set['settings']['Essay Upload Dir']['value'] .= " - (X) ". 'Directory does not exists';

			} else if ( !is_writable( $essay_upload_dir_path ) ) {
				$color = 'red';
				$settings_set['settings']['Essay Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $essay_upload_dir_path_r .'</span>'; 
				$settings_set['settings']['Essay Upload Dir']['value_html'] .= ' - '. esc_html__( 'Directory not writable', 'learndash' );

				$settings_set['settings']['Essay Upload Dir']['value'] .= ' - (X) '. 'Directory not writable';

			} else {
				$settings_set['settings']['Essay Upload Dir']['value_html'] = '<span style="color: '. $color .'">'. $essay_upload_dir_path_r .'</span>'; 
			}


			foreach( apply_filters( 'learndash_support_ld_defines', array( 'LEARNDASH_LMS_PLUGIN_DIR', 'LEARNDASH_LMS_PLUGIN_URL', 'LEARNDASH_SCRIPT_DEBUG', 'LEARNDASH_SCRIPT_VERSION_TOKEN', 'LEARNDASH_ADMIN_CAPABILITY_CHECK', 'LEARNDASH_GROUP_LEADER_CAPABILITY_CHECK', 'LEARNDASH_COURSE_BUILDER', 'LEARNDASH_LESSON_VIDEO', 'LEARNDASH_ADDONS_UPDATER', 'LEARNDASH_LMS_DEFAULT_QUESTION_POINTS', 'LEARNDASH_LMS_DEFAULT_ANSWER_POINTS', 'LEARNDASH_LMS_DEFAULT_WIDGET_PER_PAGE', 'LEARNDASH_WPPROQUIZ_TEXT_DOMAIN' ) ) as $defined_item ) {
				$defined_value = ( defined( $defined_item ) ) ? constant( $defined_item ) : '';
				if ( $defined_item == 'LEARNDASH_LMS_PLUGIN_DIR' ) {
					$defined_value = str_replace( $ABSPATH_tmp, '', $defined_value );
				}
				
				$settings_set['settings'][$defined_item] = array(
					'label' => $defined_item,
					'label_html' => $defined_item,
					'value' => $defined_value
				);
			}

			$ld_translation_files = '';
			if ( !empty( $this->mo_files ) ) {
				
				foreach( $this->mo_files as $domain => $mo_files ) {
					$mo_files_output = '';
					foreach( $mo_files as $mo_file ) {
						if ( file_exists( $mo_file ) ) {
							if ( !empty( $mo_files_output ) ) $mo_files_output .= ', ';
							$mo_files_output .= str_replace( ABSPATH, '', $mo_file );
							$mo_files_output .= ' <em>'. learndash_adjust_date_time_display( filectime( $mo_file ) ) .'</em>';
						}
					}
					if ( !empty( $mo_files_output ) ) {
						$ld_translation_files .= '<strong>'. $domain .'</strong> - ' . $mo_files_output .'<br />';
					}
				}
			}

			$settings_set['settings']['Translation Files'] = array(
				'label' => 'Translation Files',
				'label_html' => esc_html__( 'Translation Files', 'learndash' ),
				'value' => $ld_translation_files
			);


			$this->system_info['ld_settings'] = apply_filters('learndash_support_section', $settings_set, 'ld_settings' );
			
			
			/************************************************************************************************
			 * Server Settings 
			 ************************************************************************************************/
			$settings_set = array();
			
			$settings_set['header'] = array(
				'html' => esc_html__( 'Server', 'learndash' ),
				'text' => 'Server'
			);
						
			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Setting', 'learndash' ),
					'text' => 'Setting',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Value', 'learndash' ),
					'text' => 'Value',
					'class' => 'learndash-support-settings-right',
				)
			);
			
			$settings_set['settings'] = array();
			
			$php_version = phpversion();			
			$settings_set['settings']['phpversion'] = array(
				'label' => 'PHP Version',
				'label_html' => esc_html__( 'PHP Version', 'learndash' ),
				'value' => $php_version
			);
			
			$version_compare = version_compare( '7.0', $php_version, '>' );
			$color = 'green';
			if ( $version_compare == -1) {
				$color = 'red';
			} 
			$settings_set['settings']['phpversion']['value_html'] = '<span style="color: '. $color .'">'. $php_version .'</span>'; 
			if ( $version_compare == -1 ) {
				$settings_set['settings']['phpversion']['value_html'] .= ' - <a href="https://wordpress.org/about/requirements/" target="_blank">'. esc_html__('WordPress Minimum Requirements', 'learndash') .'</a>';
			}

			if ( defined( 'PHP_OS' ) ) {
				$settings_set['settings']['PHP_OS'] = array(
					'label' => 'PHP OS',
					'label_html' => esc_html__( 'PHP OS', 'learndash' ),
					'value' => PHP_OS
				);
			}
			
			if ( defined( 'PHP_OS_FAMILY' ) ) {
				$settings_set['settings']['PHP_OS_FAMILY'] = array(
					'label' => 'PHP OS Family',
					'label_html' => esc_html__( 'PHP OS Family', 'learndash' ),
					'value' => PHP_OS_FAMILY
				);
			}
							
			if ( $wpdb->is_mysql == true ) {
				$mysql_version = $wpdb->db_version();

				$settings_set['settings']['mysql_version'] = array(
					'label' => 'MySQL version',
					'label_html' => esc_html__( 'MySQL version', 'learndash' ),
					'value' => $mysql_version
				);
				
				$version_compare = version_compare( '5.6', $mysql_version, '>' );
				$color = 'green';
				if ( $version_compare == -1) {
					$color = 'red';
				} 
				$settings_set['settings']['mysql_version']['value_html'] = '<span style="color: '. $color .'">'. $mysql_version .'</span>'; 
				if ( $version_compare == -1) {
					$settings_set['settings']['mysql_version']['value_html'] .= ' - <a href="https://wordpress.org/about/requirements/" target="_blank">'. esc_html__('WordPress Minimum Requirements', 'learndash') .'</a>';
				}
			}

			$this->php_ini_settings = apply_filters('learndash_support_php_ini_settings', $this->php_ini_settings );
			if ( !empty( $this->php_ini_settings ) ) {
				sort( $this->php_ini_settings );
				$this->php_ini_settings = array_unique( $this->php_ini_settings );
				
				foreach( $this->php_ini_settings as $ini_key ) {
					$settings_set['settings'][$ini_key] = array(
						'label' => $ini_key,
						'value' => ini_get( $ini_key )
					);
				}
				
				$settings_set['settings']['curl'] = array(
					'label' => 'curl',
				);
				
				if ( !extension_loaded( 'curl' ) ) {
					$settings_set['settings']['curl']['value'] = 'No';
					$settings_set['settings']['curl']['value_html'] = '<span style="color: red">'. esc_html__('No', 'learndash' ) .'</span>';
					
				} else {
					$settings_set['settings']['curl']['value'] = 'Yes<br />';
					$settings_set['settings']['curl']['value_html'] = '<span style="color: green">'. esc_html__('Yes', 'learndash' ) .'</span><br />';
					
					$version = curl_version();
					$settings_set['settings']['curl']['value'] .= "Version: ". $version['version'] ."<br />";
					$settings_set['settings']['curl']['value_html'] .= esc_html__("Version", 'learndash' ) .": ". $version['version'] ."<br />";

					$settings_set['settings']['curl']['value'] .= "SSL Version: ". $version['ssl_version'] ."<br />";
					$settings_set['settings']['curl']['value_html'] .= esc_html__("SSL Version", 'learndash' ) .": ". $version['ssl_version'] ."<br />";

					$settings_set['settings']['curl']['value'] .= "Libz Version: ". $version['libz_version'] ."<br />";
					$settings_set['settings']['curl']['value_html'] .= esc_html__("Libz Version", 'learndash' ) .": ". $version['libz_version'] ."<br />";

					$settings_set['settings']['curl']['value'] .= "Protocols: ". join(', ', $version['protocols']) ."<br />";
					$settings_set['settings']['curl']['value_html'] .= esc_html__("Protocols", 'learndash' ) .": ". join(', ', $version['protocols']) ."<br />";
					
				}
			}
			
			$this->php_extensions = apply_filters('learndash_support_php_extensions', $this->php_extensions );
			if ( !empty( $this->php_extensions ) ) {
				sort( $this->php_extensions );
				$this->php_extensions = array_unique( $this->php_extensions );
				
				foreach( $this->php_extensions as $ini_key ) {
					$settings_set['settings'][$ini_key] = array(
						'label' => $ini_key,
						'value' => extension_loaded('mbstring') ? esc_html__('Yes', 'learndash' ) : esc_html__('No', 'learndash' )
					);
				}
			}
			
			$this->system_info['server_settings'] = apply_filters('learndash_support_section', $settings_set, 'server_settings' );


			/************************************************************************************************
			 * WordPress Settings 
			 ************************************************************************************************/
			$settings_set = array();
			$settings_set['header'] = array(
				'html' => esc_html__( 'WordPress Settings', 'learndash' ),
				'text' => 'WordPress Settings'
			);
			
 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Setting', 'learndash' ),
					'text' => 'Setting',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Value', 'learndash' ),
					'text' => 'Value',
					'class' => 'learndash-support-settings-right',
				)
			);
			
			$settings_set['settings'] = array();
			
			$settings_set['settings']['wp_version'] = array(
				'label' => 'WordPress Version',
				'label_html' => esc_html__( 'WordPress Version', 'learndash' ),
				'value' => $wp_version
			);

			$settings_set['settings']['home'] = array(
				'label' => 'WordPress Home URL',
				'label_html' => esc_html__( 'WordPress Home URL', 'learndash' ),
				'value' => get_option('home')
			);

			$settings_set['settings']['siteurl'] = array(
				'label' => 'WordPress Site URL',
				'label_html' => esc_html__( 'WordPress Site URL', 'learndash' ),
				'value' => get_option('siteurl')
			);

			$settings_set['settings']['is_multisite'] = array(
				'label' => 'Is Multisite',
				'label_html' => esc_html__( 'Is Multisite', 'learndash' ),
				'value' => is_multisite() ? 'Yes' : 'No',
				'value_html' => is_multisite() ? esc_html__( 'Yes', 'learndash' ) : esc_html__( 'No', 'learndash' ),
			);

			$settings_set['settings']['Site Language'] = array(
				'label' => 'Site Language',
				'label_html' => esc_html__( 'Site Language', 'learndash' ),
				'value' => get_locale()
			);

			if ( $wp_rewrite->using_permalinks() ) {
				$value_html = '<span style="color: green">'. esc_html__( 'Yes', 'learndash' ) .'</span>'; 
				$value = 'Yes';
			} else {
				$value_html = '<span style="color: red">'. esc_html__( 'No', 'learndash' ) .'</span>'; 
				$value = 'No (X)';
			}
			$settings_set['settings']['using_permalinks'] = array(
				'label' => 'Using Permalinks',
				'label_html' => esc_html__( 'Using Permalinks', 'learndash' ),
				'value_html' => $value_html,
				'value' => $value
			);

			$settings_set['settings']['Object Cache'] = array(
				'label' => 'Object Cache',
				'label_html' => esc_html__( 'Object Cache', 'learndash' ),
				'value' => wp_using_ext_object_cache() ? esc_html__( 'Yes', 'learndash' ) : esc_html__( 'No', 'learndash' )
			);

			foreach( apply_filters( 'learndash_support_wp_defines', array( 'DISABLE_WP_CRON', 'WP_DEBUG', 'WP_DEBUG_DISPLAY', 'SCRIPT_DEBUG', 'WP_DEBUG_DISPLAY', 'WP_DEBUG_LOG', 'WP_PLUGIN_DIR', 'WP_AUTO_UPDATE_CORE', 'WP_MAX_MEMORY_LIMIT', 'WP_MEMORY_LIMIT', 'DB_CHARSET', 'DB_COLLATE' ) ) as $defined_item ) {

				$defined_value = ( defined( $defined_item ) ) ? constant( $defined_item ) : '';
				$defined_value_html = $defined_value;
				if ( $defined_item == 'WP_PLUGIN_DIR' ) {
					$defined_value = str_replace( $ABSPATH_tmp, '', $defined_value );
				} else if ( $defined_item == 'WP_MEMORY_LIMIT' ) {
					if ( learndash_return_bytes_from_shorthand( $defined_value ) < learndash_return_bytes_from_shorthand( '100M' ) ) {
						$defined_value .= ' - (X) Recommended at least 100M memory.';
						$defined_value_html = '<span style="color: red;">' . $defined_value_html .'</span>' . ' - <a target="_blank" href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP">'. esc_html('Recommended at least 100M memory.', 'learndash' ) .'</a>';
					} else {
						$defined_value_html = '<span style="color: green;">' . $defined_value_html .'</span>';
					}
				} else if ( $defined_item == 'WP_MAX_MEMORY_LIMIT' ) {
					if ( learndash_return_bytes_from_shorthand( $defined_value ) < learndash_return_bytes_from_shorthand( '256M' ) ) {
						$defined_value .= ' - (X) Recommended at least 256M memory.';
						$defined_value_html = $defined_value_html = '<span style="color: red;">' . $defined_value_html .'</span>' . ' - <a target="_blank" href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP">'. esc_html('Recommended at least 256M memory.', 'learndash' ) .'</a>';
					} else {
						$defined_value_html = '<span style="color: green;">' . $defined_value_html .'</span>';
					}
				}
				
				$settings_set['settings'][$defined_item] = array(
					'label' => $defined_item,
					'label_html' => $defined_item,
					'value' => $defined_value,
					'value_html' => $defined_value_html
				);
			}
				

			$this->system_info['wp_settings'] = apply_filters('learndash_support_section', $settings_set, 'wp_settings' );


			/************************************************************************************************
			 * Learndash Templates 
			 ************************************************************************************************/
			
			//$this->system_info['Learndash Templates'] = array();
			$this->load_templates();
			
			$settings_set = array();
			$settings_set['header'] = array(
				'html' => esc_html__( 'Learndash Templates', 'learndash' ),
				'text' => 'Learndash Templates'
			);

 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Template Name', 'learndash' ),
					'text' => 'Template Name',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Template Path', 'learndash' ),
					'text' => 'Template Path',
					'class' => 'learndash-support-settings-right',
				)
			);

			$settings_set['desc'] = esc_html__('The following is the search order paths for override templates, relative to site root:', 'learndash' ) .'
				<ol>
					<li>'. str_replace( $ABSPATH_tmp, '/', $CHILD_THEME_TEMPLATE_DIR_tmp ).'/learndash/' .'</li>
					<li>'. str_replace( $ABSPATH_tmp, '/', $CHILD_THEME_TEMPLATE_DIR_tmp ).'/' .'</li>';
					
					if ( $CHILD_THEME_TEMPLATE_DIR_tmp != $PARENT_THEME_TEMPLATE_DIR_tmp ) {
						$settings_set['desc'] .= '<li>'. str_replace( $ABSPATH_tmp, '/', $PARENT_THEME_TEMPLATE_DIR_tmp ).'/learndash/' .'</li>
						<li>'. str_replace( $ABSPATH_tmp, '/', $PARENT_THEME_TEMPLATE_DIR_tmp ).'/' .'</li>';
					}

					$settings_set['desc'] .= '<li>'. str_replace( $ABSPATH_tmp, '/', $LEARNDASH_TEMPLATES_DIR_tmp ) .'</li>
					<li>'. str_replace( $ABSPATH_tmp, '/', $LEARNDASH_LMS_PLUGIN_DIR_tmp ).'templates/' .'</li>
				</ol>';
			
			
			$settings_set['settings'] = array();
			
			$ABSPATH_tmp = str_replace('\\', '/', ABSPATH );
			$LEARNDASH_LMS_PLUGIN_DIR_tmp = str_replace( '\\', '/', LEARNDASH_LMS_PLUGIN_DIR );
			
			if ( !empty( $this->template_array ) ) {
				foreach( $this->template_array as $template ) {
					$template_path = SFWD_LMS::get_template( $template, null, null, true );
					$template_path = str_replace('\\', '/', $template_path );
					
					$settings_set['settings'][basename($template)] = array(
						'label' => basename($template),
						'value' => str_replace( $ABSPATH_tmp, '', $template_path )
					);
					
					$template_path = SFWD_LMS::get_template( $template, null, null, true );
					$template_path = str_replace('\\', '/', $template_path );
					if ( strncmp ( $template_path, $LEARNDASH_LMS_PLUGIN_DIR_tmp , strlen( $LEARNDASH_LMS_PLUGIN_DIR_tmp ) ) != 0) {
						$settings_set['settings'][basename($template)]['value_html'] = '<span style="color: red;">'. str_replace( $ABSPATH_tmp, '', $template_path ) .'</span>'; 
						$settings_set['settings'][basename($template)]['value'] = str_replace( $ABSPATH_tmp, '', $template_path ) .' (X)'; 
					} 
				}
			}
			$this->system_info['ld_templates'] = apply_filters('learndash_support_section', $settings_set, 'ld_templates' );


			/************************************************************************************************
			 * Learndash Database Tables
			 ************************************************************************************************/
			
			$settings_set = array();
			$settings_set['header'] = array(
				'html' => esc_html__( 'Database Tables', 'learndash' ),
				'text' => 'Database Tables'
			);			
			
 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Table Name', 'learndash' ),
					'text' => 'Table Name',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Present', 'learndash' ),
					'text' => 'Present',
					'class' => 'learndash-support-settings-right',		
				)
			);
			
			$settings_set['desc'] = '<p>'. esc_html__('When the LearnDash plugin or related add-ons are activated they will create the following tables. If the tables are not present try reactivating the plugin. If the table still do not show check the DB_USER defined in your wp-config.php and ensure it has the proper permissions to create tables. Check with your host for help.', 'learndash') .'</p>';
			$grants = learndash_get_db_user_grants();
			if ( !empty( $grants ) ) {
				if ( ( array_search( 'ALL PRIVILEGES', $grants ) === false ) && ( array_search( 'CREATE', $grants ) === false ) ) {
					$settings_set['desc'] .= '<p style="color: red">'. esc_html__('The DB_USER defined in your wp-config.php does not have CREATE permission.', 'learndash') .'</p>';
				}
			}
			
			$settings_set['settings'] = array();

			$this->db_tables = apply_filters('learndash_support_db_tables', $this->db_tables );
			if ( !empty( $this->db_tables ) ) {
				sort( $this->db_tables );
				$this->db_tables = array_unique( $this->db_tables );
				
				foreach( $this->db_tables as $db_table ) {
					$settings_set['settings'][$wpdb->prefix.$db_table] = array(
						'label' => $wpdb->prefix.$db_table,
					);
										
					if ( $wpdb->get_var("SHOW TABLES LIKE '". $wpdb->prefix.$db_table ."'") == $wpdb->prefix.$db_table ) {
						$settings_set['settings'][$wpdb->prefix.$db_table]['value'] = 'Yes';
						$settings_set['settings'][$wpdb->prefix.$db_table]['value_html'] = esc_html__( 'Yes', 'learndash' );
					} else {
						$settings_set['settings'][$wpdb->prefix.$db_table]['value'] = 'No';
						$settings_set['settings'][$wpdb->prefix.$db_table]['value_html'] = esc_html__( 'No', 'learndash' );
					}
				}
			}
			$this->system_info['ld_database_tables'] = apply_filters('learndash_support_section', $settings_set, 'ld_database_tables' );
			
			
			/************************************************************************************************
			 * WordPress Active Theme 
			 ************************************************************************************************/
			$settings_set = array();
			$settings_set['header'] = array(
				'html' => esc_html__( 'Active Theme', 'learndash' ),
				'text' => 'Active Theme'
			);
			
 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Theme', 'learndash' ),
					'text' => 'Theme',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Details', 'learndash' ),
					'text' => 'Details',
					'class' => 'learndash-support-settings-right',
				),
			);
			
			$settings_set['settings'] = array();
			
			$current_theme =  wp_get_theme(); 
			
			if ( $current_theme->exists() ) {
				$theme_stylesheet = $current_theme->get_stylesheet();
				
				$themes_update = get_site_transient('update_themes');							

				$theme_value = 'Version: '. $current_theme->get( 'Version' );
				$theme_value_html = esc_html__('Version', 'learndash' ) .': '. $current_theme->get( 'Version' );
				
				if ( isset( $themes_update->response[$theme_stylesheet] ) ) {
					if ( version_compare( 
					$current_theme->get( 'Version' ), 
					$themes_update->response[$theme_stylesheet]['new_version'], 
					'<' ) ) {
						$theme_value .= ' Update available: '. $themes_update->response[$theme_stylesheet]['new_version'] .' (X)';
						$theme_value_html .= ' <span style="color:red;">' . esc_html__( 'Update available', 'learndash' ) .': '. $themes_update->response[$theme_stylesheet]['new_version'] .'</span>';
					}
				}
				
				$theme_value .= ' '. 'Path: '. $current_theme->get( 'ThemeURI' );
				$theme_value_html .= '<br />'. esc_html__('Path', 'learndash' ) .': '. $current_theme->get( 'ThemeURI' );
				
				$settings_set['settings']['active_theme'] = array(
					'label' => $current_theme->get( 'Name' ),
					'value' => $theme_value,
					'value_html' => $theme_value_html,
				);
			}
			$this->system_info['wp_active_theme'] = apply_filters('learndash_support_section', $settings_set, 'wp_active_theme' );



			/************************************************************************************************
			 * WordPress Active Plugins 
			 ************************************************************************************************/
			$settings_set = array();
			$settings_set['header'] = array(
				'html' => esc_html__( 'Active Plugins', 'learndash' ),
				'text' => 'Active Plugins'
			);

 			$settings_set['columns'] = array(
				'label' => array(
					'html' => esc_html__( 'Plugin', 'learndash' ),
					'text' => 'Plugin',
					'class' => 'learndash-support-settings-left',
				),
				'value' => array(
					'html' => esc_html__( 'Details', 'learndash' ),
					'text' => 'Details',
					'class' => 'learndash-support-settings-right',
				),
			);
			
			$settings_set['settings'] = array();

			$current_plugins = get_site_transient( 'update_plugins' );

			$all_plugins = get_plugins(); 
			
			if (!empty( $all_plugins ) ) {
				foreach( $all_plugins as $plugin_key => $plugin_data ) { 
					if ( is_plugin_active( $plugin_key ) ) {

						$plugin_value = 'Version: '. $plugin_data['Version'];
						$plugin_value_html = esc_html__('Version', 'learndash' ) .': '. $plugin_data['Version'];
						
						if ( isset( $current_plugins->response[$plugin_key] ) ) {
							if ( version_compare( $plugin_data['Version'], $current_plugins->response[$plugin_key]->new_version, '<' ) ) {
								$plugin_value .= ' Update available: '. $current_plugins->response[$plugin_key]->new_version .' (X)';
								$plugin_value_html .= ' <span style="color:red;">' . esc_html__( 'Update available', 'learndash' ) .': '. $current_plugins->response[$plugin_key]->new_version .'</span>';
							}
						}
						
						$plugin_value .= ' '. 'Path: '. $plugin_data['PluginURI'];
						$plugin_value_html .= '<br />'. esc_html__('Path', 'learndash' ) .': '. $plugin_data['PluginURI'];
						
						$settings_set['settings'][$plugin_key] = array(
							'label' => $plugin_data['Name'],
							'value' => $plugin_value,
							'value_html' => $plugin_value_html,
						);
					}
				}
			}
			$this->system_info['wp_active_plugins'] = apply_filters('learndash_support_section', $settings_set, 'wp_active_plugins' );
			
			// Finally a filter for all sections. This is where some external process will add new sections etc.
			$this->system_info = apply_filters('learndash_support_sections', $this->system_info );
			
		}

		// End of functions
	}
}
