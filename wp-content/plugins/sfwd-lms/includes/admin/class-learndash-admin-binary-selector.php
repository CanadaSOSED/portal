<?php

/*			
$ld_binary_selector_courses = new Learndash_Binary_Selector_Courses(
	array(
		'html_title' 		=>	'This is the Title',
		'html_id'			=>	'group-courses',
		'html_class'		=>	'group-courses',
		'html_name'			=>	'group-courses[]',
		'selection_right' 	=> 	$group_enrolled_courses
	)
);
$ld_binary_selector_courses->show();
*/


if (!class_exists('Learndash_Binary_Selector')) {
	class Learndash_Binary_Selector {
	    
		protected $args = array();
		private $defaults = array();
		
		// stores the class as a var. This is so when we send the class back over AJAX we know how to recreate it. 
		protected $selector_class;
		
		// for the AJAX calls we create a nonce 
		protected $selector_nonce;

		// data structure to contain information to pass to DOM via data="" attribute in the outer div wrapper
		protected $element_data = array();
		
		// container for the query result items
		protected $element_items = array();
		
		// container for the query result items
		protected $element_queries = array();
		
		function __construct( $args = array() ) {
			
			$this->defaults = array(
				'html_title' 				=> 	'',
				'html_id' 					=> 	'',
				'html_name'					=>	'',
				'html_class' 				=> 	'',			
				'selected_ids'				=>	array(),
				'included_ids'				=>	array(),
				'max_height'				=>	'250px',
				'min_height'				=>	'250px',
				'lazy_load'					=>	false,
				'search_label_left'			=>  esc_html__( 'Search:', 'learndash' ),
				'search_label_right'		=>	esc_html__( 'Search:', 'learndash' ),
				'is_search'					=>	false,	
				'is_pager'					=>	false
			);
			
			$this->args = wp_parse_args( $args, $this->defaults );
			
			$this->args['html_slug'] = sanitize_title_with_dashes( $this->args['html_id'] );
			
			// We want to conver this to an array
			if ((!empty($this->args['selected_ids'])) && (is_string($this->args['selected_ids']))) {
				$this->args['selected_ids'] = explode(',', $this->args['selected_ids']);
			} else if ((empty($this->args['selected_ids'])) && (is_string($this->args['selected_ids']))) {
				$this->args['selected_ids'] = array();
			}

			// If for some reason the 'include' element is passed in we convert it to our 'included_ids'
			if ((isset($this->args['include'])) && (!empty($this->args['include'])) && (empty($this->args['included_ids']))) {
				$this->args['included_ids'] = $this->args['include'];
				unset($this->args['include']);
			}
			if ((!empty($this->args['included_ids'])) && (is_string($this->args['included_ids']))) {
				$this->args['included_ids'] = explode(',', $this->args['included_ids']);
			}
			// Let the outside world override some settings.
			$this->args = apply_filters('learndash_binary_selector_args', $this->args, $this->selector_class);
			
			
			$this->element_items['left'] = array();
			$this->element_items['right'] = array();			

			$this->element_queries['left'] = array();
			$this->element_queries['right'] = array();			
		}

		function show() {

			$this->query_selection_section_items('left');
			$this->query_selection_section_items('right');

			// If we don't have items for the left (All items) then something is wrong. Abort. 
			if ( ( empty( $this->element_items['left'] ) ) && ( empty( $this->element_items['right'] ) ) ) return;

			// Before we add our data element we remove all the unneeded keys. Just to keep it small
			$element_data = $this->element_data;
			foreach($this->defaults as $key => $val) {
				if (isset($element_data['query_vars'][$key])) {
					unset($element_data['query_vars'][$key]);
				}
			}
			
			// Aware of the PHP post number vars limit we convert the inlcude and exclude arrays to json so they are sent back as strings. 
			if ((isset($element_data['query_vars']['include'])) && (!empty($element_data['query_vars']['include'])))
				$element_data['query_vars']['include'] = json_encode( $element_data['query_vars']['include'], JSON_FORCE_OBJECT );

			if ((isset($element_data['query_vars']['exclude'])) && (!empty($element_data['query_vars']['exclude'])))
				$element_data['query_vars']['exclude'] = json_encode( $element_data['query_vars']['exclude'], JSON_FORCE_OBJECT );
			
			?>
			<div id="<?php echo $this->args['html_id'] ?>" class="<?php echo $this->args['html_class'] ?> learndash-binary-selector" data="<?php echo htmlspecialchars( json_encode( $element_data ) ); ?>">
				<input type="hidden" class="learndash-binary-selector-form-element" name="<?php echo $this->args['html_name'] ?>" value="<?php echo htmlspecialchars( json_encode( $this->args['selected_ids'], JSON_FORCE_OBJECT ) ) ?>"/>
				<input type="hidden" name="<?php echo $this->args['html_id'] ?>-nonce" value="<?php echo wp_create_nonce( $this->args['html_id'] ) ?>" />
				
				<?php $this->show_selections_title(); ?>
				<table class="learndash-binary-selector-table">
				<tr>
					<?php
						$this->show_selections_section('left');
						$this->show_selections_section_controls();
						$this->show_selections_section('right');
					?>
				</tr>
			</table>
				<?php
				if ((isset($this->args['max_height'])) && (!empty($this->args['max_height']))) {
					?>
					<style>
					.learndash-binary-selector .learndash-binary-selector-section .learndash-binary-selector-items {
						max-height: <?php echo $this->args['max_height'] ?>;
						overflow-y:scroll;
					}
					</style>
					
					<?php
				}
				?>
				<?php
				if ((isset($this->args['min_height'])) && (!empty($this->args['min_height']))) {
					?>
					<style>
					.learndash-binary-selector .learndash-binary-selector-section .learndash-binary-selector-items {
						min-height: <?php echo $this->args['min_height'] ?>;
					}
					</style>
					
					<?php
				}
				?>
				
			</div>
			<?php
		}
		
		function show_selections_title() {
			if (!empty($this->args['html_title'])) {
				echo $this->args['html_title'];
			}
		}
		
		function show_selections_section_controls() {
			?>
			<td class="learndash-binary-selector-section learndash-binary-selector-section-middle">
				<a href="#" class="learndash-binary-selector-button-add"><img src="<?php echo LEARNDASH_LMS_PLUGIN_URL ."assets/images/arrow_right.png"; ?>" /></a><br>
				<a href="#" class="learndash-binary-selector-button-remove"><img src="<?php echo LEARNDASH_LMS_PLUGIN_URL ."assets/images/arrow_left.png"; ?>" /></a>
			</td>
			<?php
		}
		
		function show_selections_section( $position = '') {
			
			?>
			<td class="learndash-binary-selector-section learndash-binary-selector-section-<?php echo $position ?>">
				<input placeholder="<?php echo $this->get_search_label($position) ?>" type="text" id="learndash-binary-selector-search-<?php echo $this->args['html_slug'] ?>-<?php echo $position ?>" class="learndash-binary-selector-search learndash-binary-selector-search-<?php echo $position ?>" />
				
				<select multiple="multiple" class="learndash-binary-selector-items learndash-binary-selector-items-<?php echo $position ?>">
					<?php $this->show_selections_section_items( $position ); ?>
				</select>

				<?php /* ?>
				<div class="learndash-binary-selector-legend learndash-binary-selector-legend-<?php echo $position ?>">
					<?php $this->show_selections_section_legend( $position ); ?>
				</div>
				<?php */ ?>
				
				<ul class="learndash-binary-selector-pager learndash-binary-selector-pager-<?php echo $position ?>">
					<?php $this->show_selections_section_pager( $position ); ?>
				</ul>
			</td>
			<?php
		}
		
		function show_selections_section_items( $position = '' ) {
			echo $this->build_options_html( $position );
		}
		
		function show_selections_section_legend( $position = '' ) {
			if ( $position == 'left' ) {
				?><span class="items-loaded-count" style="display:none"> /</span> <span class="items-total-count"></span><?php
			} else if ( $position == 'right' ) { 
				?><span class="items-loaded-count" style="display:none"> /</span> <span class="items-total-count"></span><?php
			}
		}
		
		function show_selections_section_pager( $position = '' ) {
			?>
			<li class="learndash-binary-selector-pager-prev"><a class="learndash-binary-selector-pager-prev" style="display:none;" href="#"><?php esc_html_e('&lsaquo; prev', 'learndash') ?></a></li>
			<li class="learndash-binary-selector-pager-info" style="display:none;"><?php esc_html_e('Page', 'learndash'); ?> <span class="current_page"></span> of <span class="total_pages"></span> <?php /* ?><span class="total_items">(<span class="total_items_count"></span> <?php esc_html_e('items', 'learndash');?>)</span><?php */ ?></li>
			<li class="learndash-binary-selector-pager-next"><a class="learndash-binary-selector-pager-next" style="display:none;" href="#"><?php esc_html_e('next  &rsaquo;', 'learndash') ?></a></li>
			<?php
		}
		
		function get_search_label( $position = '' ) {
			if ( isset( $this->args['search_label_'. $position] ) ) {
				return $this->args['search_label_'. $position];
			} else if ( isset($this->args['search_label'] ) ) {
				return $this->args['search_label'];
			} else {
				return esc_html__( 'Search', 'learndash' );
			}
		}
		
		function get_pager_data( $position ) {
		}
		
		function query_selection_section_items($position = '') {
		}
		
		function process_query( $query_args, $position) {
		}
		
		function load_pager_ajax( $position ) {
			
			$this->query_selection_section_items( $position );
			$reply_data = $this->element_data[$position];
			$reply_data['html_options'] = $this->build_options_html( $position );
		
			return $reply_data;
		}

		function load_search_ajax( $position ) {
			$this->query_selection_section_items( $position );
			$reply_data = $this->element_data[$query_data['position']];
			$reply_data['html_options'] = $this->build_options_html( $position );
			return $reply_data;
		}
		
		function get_nonce_data() {
			return wp_create_nonce( $this->selector_class .'-'. $this->args['html_id'] );
		}
		
		function validate_nonce_data( $nonce ) {
			return wp_verify_nonce( $nonce, $this->selector_class .'-'. $this->args['html_id'] );
		}
		
		function isValidJson( $string ) {
			$json = json_decode($string);
			return (is_object($json) && json_last_error() == JSON_ERROR_NONE) ? true : false;
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Users')) {
	class Learndash_Binary_Selector_Users extends Learndash_Binary_Selector {
		
		function __construct( $args = array() ) {
		
			// Set up the defaut query args for the Users. 
			$defaults = array(
				'paged'				=>	1,
				'number'			=>	get_option('posts_per_page'),
				'search_number'		=>	get_option('posts_per_page'),
				'fields'			=>	array('ID', 'display_name', 'user_login'),
				'orderby'			=>	'display_name',
				'order'				=>	'ASC',
				'search'         	=> 	'',
			);
			
			if ( ( !isset( $args['number'] ) ) && ( isset( $args['per_page'] ) ) && ( !empty( $args['per_page'] ) ) ) 
				$args['number'] = $args['per_page'];
			
			$args = wp_parse_args( $args, $defaults );
			
			parent::__construct( $args );	
		
			if ( ( isset( $this->args['included_ids'] ) ) && ( !empty( $this->args['included_ids'] ) ) ) {
				$this->query_args['include'] = $this->args['included_ids'];
			}

			if ( ( isset( $this->args['excluded_ids'] ) ) && ( !empty( $this->args['excluded_ids'] ) ) ) {
				$this->query_args['exclude'] = $this->args['excluded_ids'];
			}
		}
		
		function get_pager_data( $position ) {
			$pager = array();
			
			if ( isset( $this->element_queries[$position] ) ) {
				if ( isset( $this->element_queries[$position]->query_vars['paged'] ) ) 
					$pager['current_page']	= intval( $this->element_queries[$position]->query_vars['paged'] );
				else 
					$pager['current_page'] = 0;
				
				if ( isset( $this->element_queries[$position]->query_vars['number'] ) ) 
					$pager['per_page'] = intval( $this->element_queries[$position]->query_vars['number'] );
				else
					$pager['per_page'] = 0;
				
				if ( isset( $this->element_queries[$position]->total_users ) )
					$pager['total_items'] = intval( $this->element_queries[$position]->total_users );
				else 
					$pager['total_items'] = 0;
				
				if ( ( !empty( $pager['per_page'] ) ) && ( !empty($pager['total_items'] ) ) )
					$pager['total_pages'] = ceil( intval( $this->element_queries[$position]->total_users ) / intval( $this->element_queries[$position]->query_vars['number'] ) );
				else
					$pager['total_pages'] = 0;
			}
			
			return $pager;
		}
		
		function build_options_html( $position ) {
			$options_html = '';
			
			if ( !empty( $this->element_items[$position] ) ) {
				
				foreach ( $this->element_items[$position] as $user ) { 
					$user_name = $user->display_name .' ('.$user->user_login.')';

					$disabled_class = '';
					$disabled_state = '';

					if ( ( is_array( $this->args['selected_ids'] ) ) && ( !empty( $this->args['selected_ids'] ) ) ) {
						if ( in_array($user->ID, $this->args['selected_ids'] ) ) { 
							$disabled_class = 'learndash-binary-selector-item-disabled';
							if ($position == 'left') {
								$disabled_state = ' disabled="disabled" ';
							}
						}
					}
					$options_html .= '<option class="learndash-binary-selector-item '. $disabled_class .'" '. $disabled_state .' value="'. $user->ID .'" data-value="'. $user->ID .'">'. $user_name .'</option>';
				} 
			}
			
			return $options_html;
		}
		
		function query_selection_section_items($position = '') {
			if ($position == 'left') {
				if ( !empty( $this->args['included_ids'] ) ) {
					$this->args['include'] = $this->args['included_ids'];
				}
				
				if ( ( isset( $this->args['excluded_ids'] ) ) && ( !empty( $this->args['excluded_ids'] ) ) ) {
					$this->args['exclude'] = $this->args['excluded_ids'];
				}
				
				
			} else if ( $position == 'right' ) {

				if ( !empty( $this->args['selected_ids'] ) ) {
					$this->args['include'] = $this->args['selected_ids'];
				} else {
					$this->args['include'] = array(0);
				}
				
				if ( ( isset( $this->args['excluded_ids'] ) ) && ( !empty( $this->args['excluded_ids'] ) ) ) {
					$this->args['exclude'] = $this->args['excluded_ids'];
				}
				
			}
			
			$this->process_query( $this->args, $position);
			
			if (isset($this->args['include']))
				unset($this->args['include']);
		}
		
		function process_query( $query_args, $position) {
			
			$query = new WP_User_Query( $query_args );
			$items = $query->get_results();
			if ( !empty( $items ) ) {
				
				$this->element_queries[$position] = $query;
				$this->element_items[$position] = $items;

				// We only need to store one reference to the query as the left and right will share this. Plus
				// the query on the right side may/will have the 'include' elements and we store this as 'selected_ids' key.
				if ($position == 'left')
					$this->element_data['query_vars']			= 	$query_args;
				
				$this->element_data['selector_class'] 			= 	$this->selector_class;
				$this->element_data['selector_nonce']			=	$this->get_nonce_data();
				
				$this->element_data[$position]['position']		= 	$position;
				$this->element_data[$position]['pager'] 		= 	$this->get_pager_data( $position );
			}
		}
		
		function load_search_ajax( $position ) {

			$reply_data = array();
			
			if ( ( isset( $this->args['search'] ) ) && ( !empty( $this->args['search'] ) ) ) {

				// For user searching Users we must include the beginning and ending '*' for wildcard matches. 
				$this->args['search'] = '*'. $this->args['search'] .'*';
			
				// Now call the parent function to perform the actual search.
				$reply_data = parent::load_search_ajax( $position );	
			}
			
			return $reply_data;
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Course_Users')) {
	class Learndash_Binary_Selector_Course_Users extends Learndash_Binary_Selector_Users {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'course_id'				=>	0,
				'html_title' 			=>	'<h3>'. esc_html_x('%s Users', 'Course Users Label', 'learndash') .'</h3>',
				'html_title' 			=>	'<h3>'. sprintf( esc_html_x('%s Users', 'Course Users label', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ) .'</h3>',
				'html_id'				=>	'learndash_course_users',
				'html_class'			=>	'learndash_course_users',
				'html_name'				=>	'learndash_course_users',
				'search_label_left'		=>	sprintf( esc_html_x( 'Search All %s Users', 'Search All Course Users', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				'search_label_right'	=>	sprintf( esc_html_x( 'Search Assigned %s Users', 'Search Assigned Course Users', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['course_id'];
			$args['html_name'] = $args['html_name'].'['. $args['course_id'] .']';

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Group_Users')) {
	class Learndash_Binary_Selector_Group_Users extends Learndash_Binary_Selector_Users {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'group_id'				=>	0,
				'html_title' 			=>	'<h3>'. esc_html__('Group Users', 'learndash') .'</h3>',
				'html_id'				=>	'learndash_group_users',
				'html_class'			=>	'learndash_group_users',
				'html_name'				=>	'learndash_group_users',
				'search_label_left'		=> esc_html__( 'Search All Group Users', 'learndash' ),
				'search_label_right'	=> esc_html__( 'Search Assigned Group Users', 'learndash' ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['group_id'];
			$args['html_name'] = $args['html_name'].'['. $args['group_id'] .']';

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Group_Leaders')) {
	class Learndash_Binary_Selector_Group_Leaders extends Learndash_Binary_Selector_Users {

		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);
			
			$defaults = array(
				'group_id'				=>	0,
				'html_title' 			=>	'<h3>'. esc_html__('Group Leaders', 'learndash') .'</h3>',
				'html_id'				=>	'learndash_group_leaders',
				'html_class'			=>	'learndash_group_leaders',
				'html_name'				=>	'learndash_group_leaders',
				'search_label_left'		=> esc_html__( 'Search All Group Leaders', 'learndash' ),
				'search_label_right'	=> esc_html__( 'Search Assigned Group Leaders', 'learndash' ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] 		= 	$args['html_id'].'-'. $args['group_id'];
			$args['html_name'] 		= 	$args['html_name'].'['. $args['group_id'] .']';

			if ( ( !isset( $args['included_ids'] ) ) || ( empty( $args['included_ids'] ) ) ) {
				$args['role__in'] 	=	array('group_leader', 'administrator');
			}

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Posts')) {
	class Learndash_Binary_Selector_Posts extends Learndash_Binary_Selector {
		function __construct( $args = array() ) {
		
			// Set up the defaut query args for the Users. 
			$defaults = array(
				'paged'					=>	1,
				'post_status'			=>	array('publish'),
				'posts_per_page'		=>	get_option('posts_per_page'),
				'search_posts_per_page'	=>	get_option('posts_per_page'),
				'orderby'				=>	'title',
				'order'					=>	'ASC',
				'ignore_sticky_posts'	=>	true,
				'search'         		=> 	'',
			);
			
			if ( ( !isset( $args['posts_per_page'] ) ) && ( isset( $args['number'] ) ) && ( !empty( $args['number'] ) ) ) 
				$args['posts_per_page'] = $args['number'];
			
			$args = wp_parse_args( $args, $defaults );
			
			parent::__construct( $args );	
		
			if ( ( isset( $this->args['included_ids'] ) ) && ( !empty( $this->args['included_ids'] ) ) ) {
				$this->query_args['include'] = $this->args['included_ids'];
			}
		}
		
		function query_selection_section_items($position = '') {
			if ($position == 'left') {
				if ( !empty( $this->args['included_ids'] ) ) {
					$this->args['post__in'] = $this->args['included_ids'];
				}
				
			} else if ( $position == 'right' ) {

				if ( !empty( $this->args['selected_ids'] ) ) {
					$this->args['post__in'] = $this->args['selected_ids'];
				} else {
					$this->args['post__in'] = array(0);
				}
			}
			
			$this->process_query( $this->args, $position);
			
			if (isset($this->args['post__in']))
				unset($this->args['post__in']);
		}
		
		function process_query( $query_args, $position) {
			
			$query = new WP_Query( $query_args );
			if ((isset($query->posts)) && (!empty($query->posts))) {
				
				$this->element_queries[$position] = $query;

				if ($position == 'left')
					$this->element_data['query_vars'] = $query_args;
				
				$this->element_items[$position] 				= 	$query->posts;

				$this->element_data['selector_class'] 			= 	$this->selector_class;
				$this->element_data['selector_nonce']			=	$this->get_nonce_data();

				$this->element_data[$position]['position']		= 	$position;
				$this->element_data[$position]['pager'] 		= 	$this->get_pager_data( $position );
			}
		}
		
		function get_pager_data( $position ) {

			$pager = array();
			
			if ( isset( $this->element_queries[$position] ) ) {
				
				if ( isset( $this->element_queries[$position]->query_vars['paged'] ) ) 
					$pager['current_page']	= intval( $this->element_queries[$position]->query_vars['paged'] );
				else 
					$pager['current_page'] = 0;
				
				if ( isset( $this->element_queries[$position]->query_vars['posts_per_page'] ) ) 
					$pager['per_page'] = intval( $this->element_queries[$position]->query_vars['posts_per_page'] );
				else
					$pager['per_page'] = 0;
				
				if ( isset( $this->element_queries[$position]->found_posts ) )
					$pager['total_items'] = intval( $this->element_queries[$position]->found_posts );
				else 
					$pager['total_items'] = 0;
				
				if ( ( !empty( $pager['per_page'] ) ) && ( !empty( $pager['total_items'] ) ) )
					$pager['total_pages'] = ceil( intval( $pager['total_items'] ) / intval( $pager['per_page'] ) );
				else
					$pager['total_pages'] = 0;
			}
			return $pager;
		}
		
		function build_options_html( $position ) {
			$options_html = '';
			
			if ( !empty( $this->element_items[$position] ) ) {
				foreach ( $this->element_items[$position] as $post ) { 
					$disabled_class = '';
					$disabled_state = '';

					if ( ( is_array( $this->args['selected_ids'] ) ) && ( !empty( $this->args['selected_ids'] ) ) ) {
						if ( in_array($post->ID, $this->args['selected_ids'] ) ) { 
							$disabled_class = 'learndash-binary-selector-item-disabled';
							if ($position == 'left') {
								$disabled_state = ' disabled="disabled" ';
							}
						} 
					}
					$options_html .= '<option class="learndash-binary-selector-item '. $disabled_class .'" '. $disabled_state .' value="'. $post->ID .'" data-value="'. $post->ID .'">'. $post->post_title .'</option>';
					
				} 
			}
			
			return $options_html;
		}
		
		function load_search_ajax( $position ) {

			$reply_data = array();
			
			if ( ( !isset( $this->args['s'] ) ) && ( isset( $this->args['search'] ) )) {
				$this->args['s'] = $this->args['search'];
				unset( $this->args['search'] );
			}
			
			if ( ( isset( $this->args['s'] ) ) && ( !empty( $this->args['s'] ) ) ) {
				$this->args['s'] = '"'. $this->args['s'] .'"';
			
				add_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
				$reply_data = parent::load_search_ajax( $positio );	
				remove_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
			}
			
			return $reply_data;
		}
		
		function search_filter_by_title( $search, $wp_query ) {
		    if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
		        global $wpdb;

		        $q = $wp_query->query_vars;
		        $n = ! empty( $q['exact'] ) ? '' : '%';

		        $search = array();

		        foreach ( ( array ) $q['search_terms'] as $term )
		            $search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

		        if ( ! is_user_logged_in() )
		            $search[] = "$wpdb->posts.post_password = ''";

		        $search = ' AND ' . implode( ' AND ', $search );
		    }
		    return $search;
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Group_Courses')) {
	class Learndash_Binary_Selector_Group_Courses extends Learndash_Binary_Selector_Posts {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'group_id'				=>	0,
				'post_type'				=>	'sfwd-courses',
				'html_title' 			=>	'<h3>'. sprintf( esc_html_x('Group %s', 'Group Courses label', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) ) .'</h3>',
				'html_id'				=>	'learndash_group_courses',
				'html_class'			=>	'learndash_group_courses',
				'html_name'				=>	'learndash_group_courses',
				'search_label_left'		=>	sprintf( esc_html_x( 'Search All Group %s', 'Search All Group Courses Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				'search_label_right'	=>	sprintf( esc_html_x( 'Search Assigned Group %s', 'Search Assigned Group Courses Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['group_id'];
			$args['html_name'] = $args['html_name'].'['. $args['group_id'] .']';

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Course_Groups')) {
	class Learndash_Binary_Selector_Course_Groups extends Learndash_Binary_Selector_Posts {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'course_id'				=>	0,
				'post_type'				=>	'groups',
				'html_title' 			=>	'<h3>'. sprintf( esc_html_x('Groups Using %s', 'Groups Using Course Label', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ) .'</h3>',
				'html_id'				=>	'learndash_course_groups',
				'html_class'			=>	'learndash_course_groups',
				'html_name'				=>	'learndash_course_groups',
				'search_label_left'		=> esc_html__( 'Search All Groups', 'learndash' ),
				'search_label_right'	=>	sprintf( esc_html_x( 'Search %s Groups', 'Search Course Groups Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['course_id'];
			$args['html_name'] = $args['html_name'].'['. $args['course_id'] .']';

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_User_Courses')) {
	class Learndash_Binary_Selector_User_Courses extends Learndash_Binary_Selector_Posts {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'user_id'				=>	0,
				'post_type'				=>	'sfwd-courses',
				'html_title' 			=>	'<h3>'. sprintf( esc_html_x('User Enrolled in %s', 'User Enrolled in Courses Label', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) ) .'</h3>',
				'html_id'				=>	'learndash_user_courses',
				'html_class'			=>	'learndash_user_courses',
				'html_name'				=>	'learndash_user_courses',
				'search_label_left'		=>	sprintf( esc_html_x( 'Search All %s', 'Search All Courses Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				'search_label_right'	=>	sprintf( esc_html_x( 'Search Enrolled %s', 'Search Enrolled Courses Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
			);
			
			$args = wp_parse_args( $args, $defaults );
						
			$args['html_id'] = $args['html_id'].'-'. $args['user_id'];
			$args['html_name'] = $args['html_name'].'['. $args['user_id'] .']';
						
			parent::__construct( $args );	
			
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_User_Groups')) {
	class Learndash_Binary_Selector_User_Groups extends Learndash_Binary_Selector_Posts {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'user_id'				=>	0,
				'post_type'				=>	'groups',
				'html_title' 			=>	'<h3>'. esc_html__('User Enrolled in Groups', 'learndash') .'</h3>',
				'html_id'				=>	'learndash_user_groups',
				'html_class'			=>	'learndash_user_groups',
				'html_name'				=>	'learndash_user_groups',
				'search_label_left'		=> esc_html__( 'Search All Groups', 'learndash' ),
				'search_label_right'	=> esc_html__( 'Search Enrolled Groups', 'learndash' ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['user_id'];
			$args['html_name'] = $args['html_name'].'['. $args['user_id'] .']';

			parent::__construct( $args );	
		}
	}
}

if (!class_exists('Learndash_Binary_Selector_Leader_Groups')) {
	class Learndash_Binary_Selector_Leader_Groups extends Learndash_Binary_Selector_Posts {
		
		function __construct( $args = array() ) {
			
			$this->selector_class = get_class($this);

			$defaults = array(
				'user_id'				=>	0,
				'post_type'				=>	'groups',
				'html_title' 			=>	'<h3>'. esc_html__('Leader of Groups', 'learndash') .'</h3>',
				'html_id'				=>	'learndash_leader_groups',
				'html_class'			=>	'learndash_leader_groups',
				'html_name'				=>	'learndash_leader_groups',
				'search_label_left'		=> esc_html__( 'Search All Groups', 'learndash' ),
				'search_label_right'	=> esc_html__( 'Search Leader Groups', 'learndash' ),
			);
			
			$args = wp_parse_args( $args, $defaults );
			
			$args['html_id'] = $args['html_id'].'-'. $args['user_id'];
			$args['html_name'] = $args['html_name'].'['. $args['user_id'] .']';

			//$args = apply_filters('learndash_binary_selector_args', $args, $this->selector_class);
						
			parent::__construct( $args );	
		}
	}
}


function learndash_binary_selector_pager_ajax() {
	
	$reply_data = array( 'status' => false);

	if ( ( isset( $_POST['query_data'] ) ) && ( !empty( $_POST['query_data'] ) ) ) {
		if ( ( isset( $_POST['query_data']['query_vars'] ) ) && ( !empty( $_POST['query_data']['query_vars'] ) ) ) {
			
			$args = $_POST['query_data']['query_vars'];

			if ( ( isset( $args['include'] ) ) && ( !empty( $args['include'] ) ) ) {
				if ( learndash_isValidJson( stripslashes( $args['include'] ) ) ) {
					$args['include'] = (array)json_decode( stripslashes( $args['include'] ) );
				} 
			} 
			if ( ( isset( $args['exclude'] ) ) && ( !empty( $args['exclude'] ) ) ) {
				if ( learndash_isValidJson( stripslashes( $args['exclude'] ) ) ) {
					$args['exclude'] = (array)json_decode( stripslashes( $args['exclude'] ) );
				}
			} 

			if ( ( isset( $_POST['query_data']['selected_ids'] ) ) && ( !empty( $_POST['query_data']['selected_ids'] ) ) ) {
				$args['selected_ids'] = (array)json_decode( stripslashes( $_POST['query_data']['selected_ids'] ) );
			} 
			
			// Set our reference flag so other functions know we are running pager 
			$args['is_pager'] = true;
			
			if ( ( isset( $_POST['query_data']['selector_class'] ) ) && ( class_exists( $_POST['query_data']['selector_class'] ) ) ) {
				$selector = new $_POST['query_data']['selector_class']( $args );
				
				if ( ( isset( $_POST['query_data']['selector_nonce'] ) ) && ( !empty( $_POST['query_data']['selector_nonce'] ) ) ) {
					if ($selector->validate_nonce_data( $_POST['query_data']['selector_nonce'] ) ) {
						if ( ( isset( $_POST['query_data']['position'] ) ) && ( !empty( $_POST['query_data']['position'] ) ) ) {
							
							$reply_data = $selector->load_pager_ajax( esc_attr( $_POST['query_data']['position'] ) );
						}
					}
				} 
			} 
		} 
	} 
	
	if ( !empty( $reply_data ) )
		echo json_encode($reply_data);

	wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_learndash_binary_selector_pager', 'learndash_binary_selector_pager_ajax' );

function learndash_binary_selector_search_ajax() {

	$reply_data = array( 'status' => false);
	
	if ( ( isset( $_POST['query_data'] ) ) && ( !empty( $_POST['query_data'] ) ) ) {
		if ( ( isset( $_POST['query_data']['query_vars'] ) ) && ( !empty( $_POST['query_data']['query_vars'] ) ) ) {
			
			$args = $_POST['query_data']['query_vars'];

			if ( ( isset( $args['include'] ) ) && ( !empty( $args['include'] ) ) ) {
				$args['include'] = (array)json_decode( stripslashes( $args['include'] ) );
			} 
			if ( ( isset( $args['exclude'] ) ) && ( !empty( $args['exclude'] ) ) ) {
				$args['exclude'] = (array)json_decode( stripslashes( $args['exclude'] ) );
			} 
			
			if ( ( isset( $_POST['query_data']['selected_ids'] ) ) && ( !empty( $_POST['query_data']['selected_ids'] ) ) ) {
				$args['selected_ids'] = (array)json_decode( stripslashes( $_POST['query_data']['selected_ids'] ) );
			}
			
			// Set our reference flag so other functions know we are running search 
			$args['is_search'] = true;
			
			if ( ( isset( $_POST['query_data']['selector_class'] ) ) && ( class_exists( $_POST['query_data']['selector_class'] ) ) ) {
				$selector = new $_POST['query_data']['selector_class']( $args );
				if ( ( isset( $_POST['query_data']['selector_nonce'] ) ) && ( !empty( $_POST['query_data']['selector_nonce'] ) ) ) {
					if ($selector->validate_nonce_data( $_POST['query_data']['selector_nonce'] ) ) {
						if ( ( isset( $_POST['query_data']['position'] ) ) && ( !empty( $_POST['query_data']['position'] ) ) ) {
							
							$reply_data = $selector->load_search_ajax( esc_attr( $_POST['query_data']['position'] ) );
						}
					}
				}
			} 
		} 
	} 

	if ( !empty( $reply_data ) )
		echo json_encode($reply_data);
	
	wp_die(); // this is required to terminate immediately and return a proper response

}

add_action( 'wp_ajax_learndash_binary_selector_search', 'learndash_binary_selector_search_ajax' );

function learndash_isValidJson( $string ) {
	$json = json_decode($string);
	return (is_object($json) && json_last_error() == JSON_ERROR_NONE) ? true : false;
}
