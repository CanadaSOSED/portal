<?php
if (!class_exists('Learndash_Admin_Metabox_Course_Builder' ) ) {
	class Learndash_Admin_Metabox_Course_Builder {
		
		private $courses_post_type = 'sfwd-courses';
		private $cb_prefix = 'learndash_course_builder';
		private $learndash_course_builder_assets = array();
		private $selector_post_types = array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' );
		private $ld_course_steps_object = null;
		private $course_id = 0;

		function __construct() {
			
			add_action( 'admin_footer', array( $this, 'admin_footer' ), 1 );

			add_action( 'wp_ajax_learndash_course_builder_selector_pager', array( $this, 'learndash_course_builder_selector_pager_ajax' ) );
			add_action( 'wp_ajax_learndash_course_builder_selector_search', array( $this, 'learndash_course_builder_selector_pager_search' ) );
			add_action( 'wp_ajax_learndash_course_builder_selector_new_step', array( $this, 'learndash_course_builder_selector_new_step' ) );
			add_action( 'wp_ajax_learndash_course_builder_selector_trash_step', array( $this, 'learndash_course_builder_selector_trash_step' ) );
			add_action( 'wp_ajax_learndash_course_builder_selector_set_step_title', array( $this, 'learndash_course_builder_selector_set_step_title' ) );

			if ( !empty( $course_id ) ) {
				$this->course_id = intval( $course_id );
			}
		}

		function on_load() {
			global $learndash_assets_loaded;
			
			if ( wp_is_mobile() )
				wp_enqueue_script( 'jquery-touch-punch' );
				
			//wp_enqueue_script( 'jquery-effects-slide' );
			wp_enqueue_script( 'jquery-ui-accordion' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			//wp_enqueue_style ( 'jquery-ui-dialog' );
			
			wp_register_script( 
				'ld-course-builder-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/ld-course-builder'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' ) .'.js', 
				array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-dialog' ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true 
			);
			$learndash_assets_loaded['scripts']['ld-course-builder-script'] = __FUNCTION__;	

			wp_enqueue_style( 
				'ld-course-builder-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/ld-course-builder'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min' ) .'.css', 
				array( ), 
				LEARNDASH_SCRIPT_VERSION_TOKEN
			);
			wp_style_add_data( 'ld-course-builder-style', 'rtl', 'replace' );
			
			$learndash_assets_loaded['styles']['ld-course-builder-style'] = __FUNCTION__;	
		}
		
		function admin_footer() {
			$this->learndash_course_builder_assets['learndash_upload_message'] = esc_html__('You have unsaved Course Builder changes. Are you sure you want to leave?');
			$this->learndash_course_builder_assets['course_id'] = $this->course_id;
			
			wp_localize_script( 'ld-course-builder-script', 'learndash_course_builder_assets', $this->learndash_course_builder_assets );
			wp_enqueue_script( 'ld-course-builder-script' );
		}
		
		/**
		 * Prints content for Course Builder meta box for admin
		 * This function is called from other add_meta_box functions
		 *
		 * @since 2.5
		 * 
		 * @param  object $post WP_Post
		 * @return string 		meta box HTML output
		 */
		function course_builder_box( $course_post ) {
			
			if ( ( is_a( $course_post, 'WP_Post' ) ) && ( $course_post->post_type == 'sfwd-courses' ) ) {
			
				$this->course_id = $course_post->ID;

				// Use nonce for verification
				wp_nonce_field( $this->cb_prefix .'_nonce_'. $this->course_id, $this->cb_prefix .'_nonce' );
			
				if ( !empty( $this->course_id ) ) {
					$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( $this->course_id );
				}
				$this->course_steps_html = $this->build_course_steps_html();
			
				$course_steps = $this->ld_course_steps_object->get_steps();
				$course_steps = '';
				?>
				<div id="learndash_course_builder_box_wrap" class="learndash_course_builder_box_wrap" data-ld-course-id="<?php echo $this->course_id ?>">
					<input type="hidden" id="<?php echo $this->cb_prefix ?>_data" name="<?php echo $this->cb_prefix ?>[<?php echo $this->courses_post_type ?>][<?php echo $this->course_id ?>]" value="<?php echo $course_steps; ?>" />
					<div class="learndash_selectors">
						<div class="learndash-header-right">
							<span class="ld-show-all"><?php esc_html_e('Expand All', 'learndash'); ?></span>
							<span class="ld-divide-all">|</span>
							<span class="ld-hide-all"><?php esc_html_e('Collapse All', 'learndash'); ?></span>
						</div>
						<?php $this->show_selectors(); ?>
					</div>
					<div class="learndash_builder_items">
						<div class="learndash-header-left">
							<span class="ld-course-steps-total"><?php echo sprintf( esc_html_x('Total Steps: %s', 'placeholder: number of steps', 'learndash' ), '<span class="ld-course-steps-value">'. $this->ld_course_steps_object->get_steps_count() .'</span>'  ); ?></span>
						</div>
						<div class="learndash-header-right">
							<span class="ld-show-all"><?php esc_html_e('Expand All', 'learndash'); ?></span>
							<span class="ld-divide-all">|</span>
							<span class="ld-hide-all"><?php esc_html_e('Collapse All', 'learndash'); ?></span>
						</div>
						<?php echo $this->course_steps_html; ?>
					</div>
					<br style="clear:both;"/>
				</div>
					
				<style>
					#learndash_course_builder_box_wrap .learndash_selectors #learndash-selector-post-listing-sfwd-lessons:empty::after {
						content: "<?php echo sprintf( _x( 'Click the \'+\' to add a new %s', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'Lesson' ) ) ?>";
					}
					#learndash_course_builder_box_wrap .learndash_selectors #learndash-selector-post-listing-sfwd-topic:empty::after {
						content: "<?php echo sprintf( _x( 'Click the \'+\' to add a new %s', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'Topic' ) ); ?>";
					}
					#learndash_course_builder_box_wrap .learndash_selectors #learndash-selector-post-listing-sfwd-quiz:empty::after {
						content: "<?php echo sprintf( _x( 'Click the \'+\' to add a new %s', 'placeholder: Quiz', 'learndash' ), LearnDash_Custom_Label::get_label( 'Quiz' ) ); ?>";
					}
					
					#learndash_course_builder_box_wrap .learndash_builder_items .ld-course-builder-lesson-items:empty:after {
						content: "<?php echo sprintf( _x( 'Drop %s Here', 'placeholder: Lessons', 'learndash' ), LearnDash_Custom_Label::get_label( 'lessons' ) ); ?>";
					}
					#learndash_course_builder_box_wrap .learndash_builder_items .ld-course-builder-topic-items:empty:after {
						content: "<?php echo sprintf( _x( 'Drop %1$s %2$s Here', 'placeholder: Lesson, Topics', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'topics' ) ); ?>";
					}
					#learndash_course_builder_box_wrap .learndash_builder_items .ld-course-builder-quiz-items:empty:after {
						content: "<?php echo sprintf( _x( 'Drop Global %s Here', 'placeholder: Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); ?>";
					}

					#learndash_course_builder_box_wrap .learndash_builder_items .ld-course-builder-lesson-items .ld-course-builder-quiz-items:empty:after {
						content: "<?php echo sprintf( _x( 'Drop %1$s %2$s Here', 'placeholder: Lesson, Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); ?>";
					}
					#learndash_course_builder_box_wrap .learndash_builder_items .ld-course-builder-lesson-items .ld-course-builder-topic-items .ld-course-builder-quiz-items:empty:after {
						content: "<?php echo sprintf( _x( 'Drop %1$s %2$s Here', 'placeholder: Topic, Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); ?>";
					}
				
				</style>
				<?php 
			}
		}
		
		function get_label_for_post_type( $post_type = '', $singular = true ) {
			
			switch( $post_type ) {
				case 'sfwd-courses':
					if ( $singular === true ) return 'course';
					else return 'courses';
					break;
				
				case 'sfwd-lessons':
					if ( $singular === true ) return 'lesson';
					else return 'lessons';
					break;
					
				case 'sfwd-topic':
					if ( $singular === true ) return 'topic';
					else return 'topics';
					break;

				case 'sfwd-quiz':
					if ( $singular === true ) return 'quiz';
					else return 'quizzes';
					break;
			}
		}

		function show_selectors() {
			$course_steps = $this->ld_course_steps_object->get_steps('t');
			
			foreach( $this->selector_post_types as $selector_post_type ) {
				$post_type_object = get_post_type_object( $selector_post_type );
				if ( is_a( $post_type_object, 'WP_Post_Type' ) ) {
					
					$this->learndash_course_builder_assets['confirm_remove_'. $selector_post_type] = sprintf( esc_html_x('Are you sure you want to remove this %1$s from the %2$s? (This will also remove all sub-items)', 'placeholders: will be post type labels like Course, Lesson, Topic', 'learndash'), LearnDash_Custom_Label::get_label( $this->get_label_for_post_type( $selector_post_type ) ), LearnDash_Custom_Label::get_label( 'Course' ) );

					$this->learndash_course_builder_assets['confirm_trash_'. $selector_post_type] = sprintf( esc_html_x('Are you sure you want to move this %s to Trash?', 'placeholder: will be post type label like Course, Lesson, Topic', 'learndash'), LearnDash_Custom_Label::get_label( $this->get_label_for_post_type( $selector_post_type ) ) );
								

					$post_type_query_args = $this->build_selector_query(
						array(
							'post_type'	=> $selector_post_type,
						)
					);
					
					if ( !empty( $post_type_query_args ) ) {
						$post_type_query = new WP_Query( $post_type_query_args );
						$selector_post_type_steps = array();
							 
						if ( ( isset( $course_steps[$selector_post_type] ) ) && ( !empty( $course_steps[$selector_post_type] ) ) ) {
							$selector_post_type_steps = $course_steps[$selector_post_type];
						}
						$selector_post_type_steps = htmlspecialchars( json_encode( $selector_post_type_steps ) );
						?>
						<div class="learndash-selector-container learndash-selector-container-<?php echo $selector_post_type ?>" data-ld-type="<?php echo $selector_post_type ?>" data-ld-selected="<?php echo $selector_post_type_steps; ?>">
							<h3 class="learndash-selector-header"><span class="learndash-selector-title"><?php echo LearnDash_Custom_Label::get_label( $this->get_label_for_post_type( $selector_post_type, false ) ); ?></span><span class="ld-course-builder-action ld-course-builder-action-show-hide ld-course-builder-action-show dashicons" title="<?php esc_html_e( 'Expand/Collape Section', 'learndash' ) ?>"></span><span class="ld-course-builder-action ld-course-builder-action-add dashicons" title="<?php esc_html_e( 'New', 'learndash' ) ?>"><img src="<?php echo admin_url('images/wpspin_light-2x.gif') ?>" alt="" /></span></h3>
							<div class="learndash-selector-post-listing">
								
								<?php
									$row_single = $this->build_selector_row_single( null, $selector_post_type );
									//error_log('row_single['. $row_single .']');
									if ( !empty( $row_single ) ) {
										?><ul class="learndash-row-placeholder" style="display:none"><?php echo $row_single ?></ul><?php
									}
								?>
								
								<div class="learndash-selector-pager">
									<p class="pager-info">
										<?php echo $this->build_selector_pages_buttons( $post_type_query ); ?>
									</p>	
								</div>
								<div class="learndash-selector-search"><input type="text" placeholder="<?php esc_html_e('Search...', 'learndash' ); ?>" /></div>
								
								<ul id="learndash-selector-post-listing-<?php echo $selector_post_type ?>" class="learndash-selector-post-listing dropfalse"><?php 
									if ( $post_type_query->have_posts() ) {
										echo $this->build_selector_rows( $post_type_query ); 
									}
								?></ul>
							</div>
						</div>
						<?php
					}
				}
			}
		}

		function build_selector_query( $args = array() ) {
			$per_page = LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'per_page' );
			if ( empty( $per_page ) ) 
				$per_page = 10;
			
			$defaults = array(
				'post_status'		=>	array( 'publish' ),
				'posts_per_page'	=>	$per_page,
				'paged'				=>	1,
				'orderby'			=>	'title',
				'order'				=>	'ASC'
			);

			$args = wp_parse_args( $args, $defaults );
			
			// If we are not sharing steps then we limit the query results to only show items associated with the course or items
			// not associated with any course. 
			if ( LearnDash_Settings_Section::get_section_setting('LearnDash_Settings_Courses_Builder', 'shared_steps' ) !== 'yes' ) {
				if ( !isset( $args['meta_query'] ) ) $args['meta_query'] = array();
				
				$args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'     => 'course_id',
						'value'   => $this->course_id,
						'compare' => '=',
						'type'	  => 'NUMERIC'
					),
					array(
						'key'     => 'course_id',
						'value'   => 0,
						'compare' => '=',
						'type'	  => 'NUMERIC'
					),
					array(
						'key'     => 'course_id',
						'compare' => 'NOT EXISTS',
					)
				);
			}
			return apply_filters('learndash_course_builder_selector_args', $args );
		}

		function build_selector_pages_buttons( $post_type_query ) {
			$pager_buttons = '';
			
			if ( $post_type_query instanceof WP_Query ) {
				$first_page = 1;
				
				$current_page = intval( $post_type_query->query['paged'] );
				$last_page = intval( $post_type_query->max_num_pages );
				if ( empty( $last_page ) ) $last_page = 1;
				
				if ( $current_page <= 1 ) {
					$prev_page = 1;
					$prev_disabled = ' disabled="disabled" ';
				} else {
					$prev_page = $current_page - 1;
					$prev_disabled = '';
				}
				
				if ( $current_page >= $last_page ) {
					$next_page = $last_page;
					$next_disabled = ' disabled="disabled" ';
				} else {
					$next_page = $current_page + 1;
					$next_disabled = '';
				}
				
				$pager_buttons .= '<button '. $prev_disabled .' class="button button-simple first" data-page="'. $first_page.'" title="'. esc_attr__( 'First Page', 'ld_propanel' ) .'">&laquo;</button>';
				$pager_buttons .= '<button '. $prev_disabled .' class="button button-simple prev" data-page="'. $prev_page .'" title="'. esc_attr__( 'Previous Page', 'ld_propanel' ) .'">&lsaquo;</button>';
				$pager_buttons .= '<span><span class="pagedisplay"><span class="current_page">'. $current_page .'</span> / <span class="total_pages">'. $last_page .'</span></span></span>';
				$pager_buttons .= '<button '. $next_disabled .' class="button button-simple next" data-page="'. $next_page .'" title="'. esc_attr__( 'Next Page', 'ld_propanel' ) .'">&rsaquo;</button>';
				$pager_buttons .= '<button '. $next_disabled .' class="button button-simple last" data-page="'. $last_page .'" title="'. esc_attr__( 'Last Page', 'ld_propanel' ) .'" >&raquo;</button>';
			}
			
			return $pager_buttons;
		}

		function build_selector_rows( $post_type_query ) {
			$selector_rows = '';
			
			if ( $post_type_query instanceof WP_Query ) {
				$selector_post_type = $post_type_query->query['post_type'];
				$selector_post_type_object = get_post_type_object( $selector_post_type );
				
				$selector_label = $selector_post_type_object->label;
				$selector_slug = $this->get_label_for_post_type( $selector_post_type );
				
				foreach( $post_type_query->posts as $p ) {
					$selector_rows .= $this->build_selector_row_single( $p, $selector_post_type );
				}
			}
			
			return $selector_rows;
		}

		function build_selector_row_single( $p = null, $selector_post_type = '' ) {
			$selector_row = '';

			if ( empty( $selector_post_type ) ) return $selector_row;
			
			
			$selector_post_type_object = get_post_type_object( $selector_post_type );
			
			$selector_label = $selector_post_type_object->label;
			$selector_slug = $this->get_label_for_post_type( $selector_post_type );

			$selector_sub_actions = '';

			$p_id = '';
			$p_title = '';
			$edit_post_link = '';
			$view_post_link = '';
			
			if ( $p ) {
				$p_id = $p->ID;
				$p_title = get_the_title( $p->ID );
				//$view_post_link = learndash_get_step_permalink( $p->ID, $this->course_id );
				
				// We add this to force the course_id to zero for the selectors as we don't want the
				// the 'view' URL to reflect the nested course. 
				add_filter( 'learndash_post_link_course_id', function( $course_id ) {
					return 0;
				} );
				
				$view_post_link = get_permalink( $p->ID );
				if ( current_user_can('edit_courses' ) ) {
					$edit_post_link = get_edit_post_link( $p->ID );
					$edit_post_link = remove_query_arg('course_id', $edit_post_link );
				}
			} else {
				// We need a unique ID
				$p_id = $selector_post_type . '-placeholder';
				$p_title = $selector_post_type_object->labels->singular_name;
			}
			
			$selector_sub_actions .= '<a target="_blank" class="ld-course-builder-action ld-course-builder-action-edit ld-course-builder-action-'. $selector_slug .'-edit dashicons" href="'. $edit_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'Edit %s Settings (new window)', 'placeholder: will contain post type label', 'learndash' ), LearnDash_Custom_Label::get_label( $selector_slug ) ) .'</span></a>';

			$selector_sub_actions .= '<a target="_blank" class="ld-course-builder-action ld-course-builder-action-view ld-course-builder-action-'. $selector_slug .'-view dashicons" href="'. $view_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'View %s (new window)', 'placeholder: will contain post type label', 'learndash' ), LearnDash_Custom_Label::get_label( $selector_slug ) ) .'</span></a>';

			if ( current_user_can('delete_courses' ) ) {
				
				$selector_sub_actions .= '<span class="ld-course-builder-action ld-course-builder-action-trash ld-course-builder-action-'. $selector_slug .'-trash dashicons" title="'. 
					sprintf( esc_html_x( 'Move %s to Trash', 'placeholder: will contain post type label', 'learndash' ), LearnDash_Custom_Label::get_label( $selector_slug ) ) .'"></span>';
			}
			$selector_sub_actions .= '<span class="ld-course-builder-action ld-course-builder-action-remove ld-course-builder-action-'. $selector_slug .'-remove dashicons" title="'. 
				sprintf( esc_html_x( 'Remove %1$s from %2$s', 'placeholders: will contain post type label, Course', 'learndash' ),
				LearnDash_Custom_Label::get_label( $selector_slug ), LearnDash_Custom_Label::get_label('Course') ) .'"></span>';

			$selector_sub_items	= '';
			$selector_action_expand = '';
			if ( $selector_post_type == 'sfwd-lessons' ) {
				$selector_sub_items .= '<div class="ld-course-builder-topic-items ld-course-builder-lesson-topic-items"></div>';
				$selector_sub_items .= '<div class="ld-course-builder-quiz-items ld-course-builder-lesson-quiz-items"></div>';

				$selector_action_expand = '<span class="ld-course-builder-action ld-course-builder-action-show-hide ld-course-builder-action-show ld-course-builder-action-'. $selector_slug .'-show dashicons" title="'. esc_html__( 'Expand/Collape Section', 'learndash' ) .'"></span>';

			} else if ( $selector_post_type == 'sfwd-topic' ) {
				$selector_sub_items .= '<div class="ld-course-builder-quiz-items ld-course-builder-topic-quiz-items"></div>';
				$selector_action_expand = '<span class="ld-course-builder-action ld-course-builder-action-show-hide ld-course-builder-action-show ld-course-builder-action-'. $selector_slug .'-show dashicons" title="'. esc_html__( 'Expand/Collape Section', 'learndash' ) .'"></span>';
				
			} else if ( $selector_post_type == 'sfwd-quiz' ) {
			}
			
			$selector_row .= '<li id="ld-post-'. $p_id .'" class="ld-course-builder-item ld-course-builder-'. $selector_slug .'-item " data-ld-type="'. $selector_post_type .'" data-ld-id="'. $p_id .'">
				<div class="ld-course-builder-'. $selector_slug .'-header ld-course-builder-header">
					<span class="ld-course-builder-actions">
						<span class="ld-course-builder-action ld-course-builder-action-move ld-course-builder-action-'. $selector_slug .'-move dashicons" title="'. sprintf( esc_html_x( 'Move %s', 'placeholder: will contain post type label', 'learndash' ), LearnDash_Custom_Label::get_label( $selector_slug ) ) .'"></span>
						<span class="ld-course-builder-sub-actions">'. $selector_sub_actions .'</span>
					</span>
					<span class="ld-course-builder-title"><span class="ld-course-builder-title-text">'. $p_title . '</span>
						<span class="ld-course-builder-action ld-course-builder-edit-title-pencil dashicons" title="'. esc_html__( 'Edit Title', 'learndash' ) .'" ></span>
						<span class="ld-course-builder-action ld-course-builder-edit-title-ok dashicons" title="'. esc_html__( 'Ok', 'learndash' ) .'" ></span>
						<span class="ld-course-builder-action ld-course-builder-edit-title-cancel dashicons" title="'. esc_html__( 'Cancel', 'learndash' ) .'" ></span>
					</span>
					'. $selector_action_expand .'
				</div>
				<div class="ld-course-builder-sub-items" style="display:none">'. $selector_sub_items .'</div>
				</li>';
			
			return $selector_row;	
		}


		function build_course_steps_html( ) {
			$steps_html = '';
			
			$course_steps = $this->ld_course_steps_object->get_steps();
			
			if ( !empty( $course_steps ) ) {
				//error_log('course_steps<pre>'. print_r($this->course_steps, true) .'</pre>');
				$steps_html .= $this->process_course_steps( $course_steps );
			}
			return $steps_html; 
		}

		function process_course_steps( $steps = array(), $steps_parent_type = 'sfwd-courses' ) {
			$steps_section_html = '';

			if ( !empty( $steps ) ) {
				foreach( $steps as $steps_type => $steps_items ) {
					$steps_section_items_html = '';
					
					if ( !empty( $steps_items ) ) {
						foreach( $steps_items as $steps_id => $steps_set ) {
							
							//error_log('steps_type['. $steps_type .']<pre>'. print_r( $steps_set, true ) .'</pre>' );
							$steps_section_item_html = $this->process_course_steps( $steps_set, $steps_type ); 
							$edit_post_link = get_edit_post_link( $steps_id );
							$edit_post_link = add_query_arg( 'course_id', $this->course_id, $edit_post_link );
							
							$view_post_link = learndash_get_step_permalink( $steps_id, $this->course_id );
							
							if ( $steps_type == 'sfwd-lessons' ) {
								$steps_section_item_html = '<div id="ld-post-'. $steps_id .'" class="ld-course-builder-item ld-course-builder-lesson-item" data-ld-type="'. $steps_type .'" data-ld-id="'. $steps_id.'">
									<div class="ld-course-builder-lesson-header ld-course-builder-header">
										<span class="ld-course-builder-actions">
											<span class="ld-course-builder-action ld-course-builder-action-move ld-course-builder-action-lesson-move dashicons" title="'. sprintf( esc_html_x( 'Move %s', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label('Lesson') ) .'"></span>
											<span class="ld-course-builder-sub-actions">
												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-edit ld-course-builder-action-lesson-edit dashicons" href="'. $edit_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'Edit %s Settings (new window)', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label('Lesson') ) .'</span></a>

												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-view ld-course-builder-action-lesson-view dashicons" href="'. $view_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'View %s (new window)', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label('Lesson') ) .'"</span></a>
										
												<span class="ld-course-builder-action ld-course-builder-action-remove ld-course-builder-action-lesson-remove dashicons" title="'. sprintf( esc_html_x( 'Remove %1$s from %2$s', 'placeholders: Lesson, Course', 'learndash' ), LearnDash_Custom_Label::get_label('Lesson'), LearnDash_Custom_Label::get_label('Course') ) .'"></span>
											</span>
										</span>
										<span class="ld-course-builder-title"><span class="ld-course-builder-title-text">'. get_the_title( $steps_id ) .'</span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-pencil dashicons" title="'. esc_html__( 'Edit Title', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-ok dashicons" title="'. esc_html__( 'Ok', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-cancel dashicons" title="'. esc_html__( 'Cancel', 'learndash' ) .'" ></span>
										</span>

										<span class="ld-course-builder-action ld-course-builder-action-show-hide ld-course-builder-action-show ld-course-builder-action-lesson-show dashicons" title="'. esc_html__( 'Expand/Collape Section', 'learndash' ) .'"></span>
										
									</div>
									<div class="ld-course-builder-sub-items" style="display:none">'. $steps_section_item_html .'</div>
								</div>';
							} else if ( $steps_type == 'sfwd-topic' ) {
								$steps_section_item_html = '<div id="ld-post-'. $steps_id .'" class="ld-course-builder-item ld-course-builder-topic-item" data-ld-type="'. $steps_type .'" data-ld-id="'. $steps_id .'">
									<div class="ld-course-builder-topic-header ld-course-builder-header">
										<span class="ld-course-builder-actions">
											<span class="ld-course-builder-action ld-course-builder-action-move ld-course-builder-action-topic-move dashicons" title="'. esc_html__( 'Move', 'learndash' ) .'"></span>
											<span class="ld-course-builder-sub-actions">
												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-edit ld-course-builder-action-topic-edit dashicons" href="'. $edit_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'Edit %s Settings (new window)', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label('Topic') ) .'" ></span></a>
												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-view ld-course-builder-action-topic-edit dashicons" href="'. $view_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'View %s (new window)', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label('Topic') ) .'</span></a>
												<span class="ld-course-builder-action ld-course-builder-action-remove ld-course-builder-action-topic-remove dashicons" title="'. sprintf( esc_html_x( 'Remove %1$s from %2$s', 'placeholders: Lesson, Course', 'learndash' ), LearnDash_Custom_Label::get_label('Topic'), LearnDash_Custom_Label::get_label('Course') ) .'"></span>
											</span>
										</span>
										<span class="ld-course-builder-title"><span class="ld-course-builder-title-text">'. get_the_title( $steps_id ) .'</span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-pencil dashicons" title="'. esc_html__( 'Edit Title', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-ok dashicons" title="'. esc_html__( 'Ok', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-cancel dashicons" title="'. esc_html__( 'Cancel', 'learndash' ) .'" ></span>
										</span>
										
										<span class="ld-course-builder-action ld-course-builder-action-show-hide ld-course-builder-action-show ld-course-builder-action-topic-show dashicons" title="'. esc_html__( 'Expand/Collape Section', 'learndash' ) .'"></span>
									</div>
									<div class="ld-course-builder-sub-items" style="display:none">'. $steps_section_item_html .'</div>			
								</div>';
								
							} else if ( $steps_type == 'sfwd-quiz' ) {
								$steps_section_item_html = '<div id="ld-post-'. $steps_id .'" class="ld-course-builder-item ld-course-builder-quiz-item" data-ld-type="'. $steps_type .'" data-ld-id="'. $steps_id .'">
									<div class="ld-course-builder-quiz-header ld-course-builder-header">
										<span class="ld-course-builder-actions">
											<span class="ld-course-builder-action ld-course-builder-action-move ld-course-builder-action-quiz-move dashicons" title="'. esc_html__( 'Move', 'learndash' ) .'"></span>
											<span class="ld-course-builder-sub-actions">
												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-edit ld-course-builder-action-quiz-edit dashicons" href="'. $edit_post_link .'"><span class="screen-reader-text">'. sprintf( esc_html_x( 'Edit %s Settings (new window)', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label('Quiz') ) .'" ></span></a>
												<a target="_blank" class="ld-course-builder-action ld-course-builder-action-view ld-course-builder-action-quiz-view dashicons" href="'. $view_post_link .'"><span class="screen-reader-text" >'. sprintf( esc_html_x( 'View %s (new window)', 'placeholder: Quiz', 'learndash' ), LearnDash_Custom_Label::get_label('Quiz') ) .'"></span></a>
												<span class="ld-course-builder-action ld-course-builder-action-remove ld-course-builder-action-quiz-remove dashicons" title="'. sprintf( esc_html_x( 'Remove %1$s from %2$s', 'placeholders: Lesson, Course', 'learndash' ), LearnDash_Custom_Label::get_label('Quiz'), LearnDash_Custom_Label::get_label('Course') ) .'"></span>
											</span>
										</span>
										<span class="ld-course-builder-title"><span class="ld-course-builder-title-text">'. get_the_title( $steps_id ) .'</span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-pencil dashicons" title="'. esc_html__( 'Edit Title', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-ok dashicons" title="'. esc_html__( 'Ok', 'learndash' ) .'" ></span>
											<span class="ld-course-builder-action ld-course-builder-edit-title-cancel dashicons" title="'. esc_html__( 'Cancel', 'learndash' ) .'" ></span>
										</span>
									</div>
									<div class="ld-course-builder-sub-items"  style="display:none">'. $steps_section_item_html .'</div>			
								</div>';
							}
							
							$steps_section_items_html .= $steps_section_item_html; 
						}
					}
										
					if ( $steps_parent_type == 'sfwd-courses' ) {
						if ( $steps_type == 'sfwd-lessons' ) {
							$steps_section_html = '<div class="ld-course-builder-lesson-items">'. $steps_section_items_html .'</div>';
						} else if ( $steps_type == 'sfwd-quiz' ) {
							$steps_section_html .= '<div class="ld-course-builder-quiz-items ld-course-builder-course-quiz-items">'. $steps_section_items_html .'</div>';
						}

					} else if ( $steps_parent_type == 'sfwd-lessons' ) {
						if ( $steps_type == 'sfwd-topic' ) {
				        	$steps_section_html = '<div class="ld-course-builder-topic-items ld-course-builder-lesson-topic-items">'. $steps_section_items_html .'</div>';
							
						} else if ( $steps_type == 'sfwd-quiz' ) {
							$steps_section_html .= '<div class="ld-course-builder-quiz-items ld-course-builder-lesson-quiz-items">'. $steps_section_items_html .'</div>';
						}
					} else if ( $steps_parent_type == 'sfwd-topic' ) {
						if ( $steps_type == 'sfwd-quiz' ) {
							$steps_section_html = '<div class="ld-course-builder-quiz-items ld-course-builder-topic-quiz-items">'. $steps_section_items_html .'</div>';
						}
					}				
				}
			} else {
				if ( $steps_parent_type == 'sfwd-courses' ) {
					$steps_section_html .= '<div class="ld-course-builder-lesson-items"></div>';
					$steps_section_html .= '<div class="ld-course-builder-quiz-items"></div>';
				}
			}
			
			return $steps_section_html;
		}

		function save_course_builder( $post_id, $post, $update ) {
			$cb_nonce = $this->cb_prefix .'_nonce';
			if ( ( isset( $_POST[$cb_nonce] ) ) && ( wp_verify_nonce( $_POST[$cb_nonce], $cb_nonce . '_'. $post_id ) ) ) {
				if ( isset( $_POST[$this->cb_prefix][$this->courses_post_type][$post_id] ) ) {
					$course_builder_data = $_POST[$this->cb_prefix][$this->courses_post_type][$post_id];
					
					if ( $course_builder_data !== '' ) {
						$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( $post_id );
					
						$course_steps = (array)json_decode( stripslashes( $course_builder_data ), true );
					
						if ( ( is_array( $course_steps ) ) && ( !empty( $course_steps ) ) ) {
							$course_steps_split = LDLMS_Course_Steps::steps_split_keys( $course_steps );
						} else {
							$course_steps_split = array();
						}
						$this->ld_course_steps_object->set_steps( $course_steps_split );
					}
				}
			}
		}

		function learndash_course_builder_selector_pager_ajax() {

			$reply_data = array( );
			$reply_data['selector_pager'] = '';
			$reply_data['selector_rows'] = '';

			if ( isset( $_POST['course_id'] ) ) {
				$this->course_id = intval( $_POST['course_id'] );
				if ( !empty( $this->course_id ) ) {
					$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( $this->course_id );
				} 
			
				if ( ( isset( $_POST['query_args'] ) ) && ( !empty( $_POST['query_args'] ) ) ) {

					$post_type_query_args = $this->build_selector_query( $_POST['query_args'] );
					if ( !empty( $post_type_query_args ) ) {
						$post_type_query = new WP_Query( $post_type_query_args );
						if ( $post_type_query->have_posts() ) {
							$reply_data['selector_pager'] = $this->build_selector_pages_buttons( $post_type_query );
							$reply_data['selector_rows'] = $this->build_selector_rows( $post_type_query );
						}
					}
				}
			}
			
			echo json_encode($reply_data);

			wp_die(); // this is required to terminate immediately and return a proper response			
		}

		function learndash_course_builder_selector_pager_search() {

			$reply_data = array( );
			$reply_data['selector_rows'] = '';

			if ( isset( $_POST['course_id'] ) ) {
				$this->course_id = intval( $_POST['course_id'] );
				if ( !empty( $this->course_id ) ) {
					$this->ld_course_steps_object = LDLMS_Factory_Post::course_steps( $this->course_id );
				} 
			
				if ( ( isset( $_POST['query_args'] ) ) && ( !empty( $_POST['query_args'] ) ) ) {
					$post_type_query_args = $this->build_selector_query( $_POST['query_args'] );
					$post_type_query_args['paged'] = 1;
					$post_type_query_args['posts_per_page'] = 50;
				
					$post_type_query = new WP_Query( $post_type_query_args );
					if ( $post_type_query->have_posts() ) {
						$reply_data['selector_rows'] = $this->build_selector_rows( $post_type_query );
					}
				}
			}
			
			echo json_encode($reply_data);

			wp_die(); // this is required to terminate immediately and return a proper response
			
		}

		function learndash_course_builder_selector_new_step() {
			$reply_data = array( );
			$reply_data['new_steps'] = array();
			
			if ( ( isset( $_POST['new_steps'] ) ) && ( !empty( $_POST['new_steps'] ) ) ) {
				foreach( $_POST['new_steps'] as $old_step_id => $step_set ) {
					if ( ( isset( $step_set['post_type'] ) ) && ( !empty( $step_set['post_type'] ) ) 
					  && ( in_array( $step_set['post_type'], array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' )  ) !== false ) ) {

						$post_args = array( 
							'post_type' 	=> esc_attr( $step_set['post_type'] ), 
							'post_status' 	=> 'publish', 
							'post_title' 	=> '', 
							'post_content' 	=> '' 
						);

						if ( ( isset( $step_set['post_title'] ) ) && ( !empty( $step_set['post_title'] ) ) ) {
							$post_args['post_title'] = $step_set['post_title'];
						} else {
							$post_type_object = get_post_type_object( $step_set['post_type'] );	
							if ( $post_type_object ) {
								$post_args['post_title'] = $post_type_object->labels->singular_name;
							}
						} 
						
						$new_step_id = wp_insert_post( apply_filters( 'course_builder_selector_new_step_post_args', $post_args ) );
						if ( $new_step_id ) {

							$reply_data['status'] = true;
							
							$reply_data['new_steps'][$old_step_id] = array();
							$reply_data['new_steps'][$old_step_id]['post_id'] = $new_step_id;
							$reply_data['new_steps'][$old_step_id]['view_url'] = get_permalink( $new_step_id );
							$reply_data['new_steps'][$old_step_id]['edit_url'] = get_edit_post_link( $new_step_id );
							
							if ( $post_args['post_type'] == 'sfwd-quiz' ) {
					
								// This form element is required when creating a new Quiz in WPProQuiz. Don't ask.
								$_POST['form'] = array();
								$_POST['name'] = $post_args['post_title'];
								$_POST['text'] = 'AAZZAAZZ'; //$post_args['post_content'];
					
								$pro_quiz = new WpProQuiz_Controller_Quiz();
								ob_start();
								$pro_quiz->route( array( 'action' => 'addEdit', 'quizId' => 0, 'post_id' => $new_step_id ) );
								ob_get_clean();
								
								$quiz_id = learndash_get_setting( $new_step_id, "quiz_pro" );

								$quiz_meta = SFWD_CPT_Instance::$instances[ 'sfwd-quiz' ]->get_settings_values( 'sfwd-quiz' );
								if ( !empty( $quiz_meta ) ) {
									//error_log('quiz_meta<pre>'. print_r($quiz_meta, true) .'</pre>');
									$quiz_meta_values = wp_list_pluck( $quiz_meta, 'value' );
									//error_log('quiz_meta_values<pre>'. print_r($quiz_meta_values, true) .'</pre>');
									
									if ( !empty( $quiz_id ) ) {
										$quiz_meta_values['sfwd-quiz_quiz_pro'] = intval( $quiz_id );
										update_post_meta( $new_step_id, 'quiz_pro_id_'. $quiz_id, $quiz_id );
										update_post_meta( $new_step_id, 'quiz_pro_id', $quiz_id );
										
										// Set the 'View Statistics on Profile' for the new quiz.  
										update_post_meta( $new_step_id, '_viewProfileStatistics', 1 );
									}
									update_post_meta( $new_step_id, '_sfwd-quiz', $quiz_meta_values );
								}
							}
						}	
					}
				}
			}
			echo json_encode( $reply_data );

			wp_die(); // this is required to terminate immediately and return a proper response
		}


		function learndash_course_builder_selector_trash_step() {

			$reply_data = array( );
			
			$post_args = array( 'post_id' => 0, 'post_type' => '' );
			
			if ( ( isset( $_POST['step_args']['post_id'] ) ) && ( !empty( $_POST['step_args']['post_id'] ) ) ) {
				$post_args['post_id'] = intval( $_POST['step_args']['post_id'] );
			}
			if ( ( isset( $_POST['step_args']['post_type'] ) ) && ( !empty( $_POST['step_args']['post_type'] ) ) ) {
				$post_args['post_type'] = esc_attr( $_POST['step_args']['post_type'] );
			}
			if ( ( empty( $post_args['post_type'] ) ) || ( empty( $post_args['post_id'] ) ) ) {
				$reply_data['status'] = false;
				$reply_data['error_message'] = esc_html__( '#1: Invalid post data', 'learndash' );
			} else if ( in_array( $post_args['post_type'], array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' )  ) === false ) {
				$reply_data['status'] = false;
				$reply_data['error_message'] = esc_html__( '#2: Invalid post data', 'learndash' );
			} else {
				$new_step_id = wp_trash_post( $post_args['post_id'] );
				$reply_data['status'] = true;
			}
			
			echo json_encode( $reply_data );

			wp_die();
		}

		function learndash_course_builder_selector_set_step_title() {

			$reply_data = array( );
			$post_title = '';
			$post_args = array( 'post_id' => 0, 'post_type' => '' );
			
			if ( ( isset( $_POST['step_args']['post_id'] ) ) && ( !empty( $_POST['step_args']['post_id'] ) ) ) {
				$post_args['post_id'] = intval( $_POST['step_args']['post_id'] );
			}
			if ( ( isset( $_POST['step_args']['post_type'] ) ) && ( !empty( $_POST['step_args']['post_type'] ) ) ) {
				$post_args['post_type'] = esc_attr( $_POST['step_args']['post_type'] );
			}

			if ( ( isset( $_POST['new_title'] ) ) && ( !empty( $_POST['new_title'] ) ) ) {
				$post_title = esc_attr( $_POST['new_title'] );
			}

			if ( ( empty( $post_title ) ) || ( empty( $post_args['post_type'] ) ) || ( empty( $post_args['post_id'] ) ) ) {
				$reply_data['status'] = false;
				$reply_data['error_message'] = esc_html__( '#1: Invalid post data', 'learndash' );
			} else if ( in_array( $post_args['post_type'], array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' )  ) === false ) {
				$reply_data['status'] = false;
				$reply_data['error_message'] = esc_html__( '#2: Invalid post data', 'learndash' );
			} else {
				$edit_post = array(
					'ID'			=> $post_args['post_id'],
					'post_title'	=> $post_title,
					'post_name'		=> ''
				);
				wp_update_post( $edit_post );
				$reply_data['status'] = true;
				
				if ( $post_args['post_type'] == 'sfwd-quiz' ) {
					$quiz_id = get_post_meta( $post_args['post_id'], 'quiz_pro_id', true );
					if ( !empty( $quiz_id ) ) {
						$quizMapper = new WpProQuiz_Model_QuizMapper();
						$quiz = $quizMapper->fetch( $quiz_id );
						if ( is_a( $quiz, 'WpProQuiz_Model_Quiz' ) ) { 
							$quiz->setName( $post_title );
							$quizMapper->save( $quiz );
						}
					}
				}
			}
			
			echo json_encode( $reply_data );

			wp_die();
			
		}
		// End of functions
	}
}
add_action( 'plugins_loaded', function() {
	new Learndash_Admin_Metabox_Course_Builder();
} );
