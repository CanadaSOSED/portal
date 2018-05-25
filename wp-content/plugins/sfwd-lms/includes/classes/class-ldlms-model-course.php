<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ( !class_exists( 'LDLMS_Model_Course' ) ) && ( class_exists( 'LDLMS_Model_Post' ) ) ) {	
	class LDLMS_Model_Course extends LDLMS_Model_Post {

		private static $post_type = 'sfwd-courses';
		private $steps_object = null;
		
		//private $steps_loaded = false;
		//protected $steps = array();

		function __construct( $course_id = 0 ) {
			if ( !empty( $course_id ) ) {
				$course_id = absint( $course_id );
				if ( !$this->initialize( $course_id ) )
					throw new LDLMS_Exception_NotFound();
				//$this->initialize( $course_id );
			} else {
				throw new LDLMS_Exception_NotFound();
			}
		}
		
		function initialize( $course_id ) {
			if ( !empty( $course_id ) ) {
				$course = get_post( $course_id );
				if ( ( $course instanceof WP_Post ) && ( $course->post_type == LDLMS_Model_Course::$post_type ) ) {
					$this->id = $course_id;
					$this->post = $course;

					$this->load_settings();
					return true;
				} else {
					return false;
				}
			}
		}
		
		function load_settings() {
			if ( !empty( $this->post ) ) {
				$settings = learndash_get_setting( $this->post );
				if ( !is_array( $settings ) ) {
					if ( !empty( $settings ) ) 
						LDLMS_Model_Course::$settings = array( $settings );
					else
						LDLMS_Model_Course::$settings = array();
				}
				
				$lesson_settings = LDLMS_Model_Lesson::get_settings();
				
				// We can't do a sinple merge because the keys are different. So hopefuly we can remember to update this with the logic for each mis-matching key
				if ( ( isset( $lesson_settings['order'] ) ) && ( !empty( $lesson_settings['order'] ) ) ) {
					LDLMS_Model_Course::$settings['course_lesson_order'] = $lesson_settings['order'];
				}

				if ( ( isset( $lesson_settings['orderby'] ) ) && ( !empty( $lesson_settings['orderby'] ) ) ) {
					LDLMS_Model_Course::$settings['course_lesson_orderby'] = $lesson_settings['orderby'];
				}

				if ( ( isset( $lesson_settings['posts_per_page'] ) ) && ( !empty( $lesson_settings['posts_per_page'] ) ) ) {
					LDLMS_Model_Course::$settings['course_lesson_per_page'] = $lesson_settings['posts_per_page'];
				}
			}
		}		
		
		static function get_setting( $setting_key = '' ) {
			if ( ( !empty( $setting_key ) ) && ( isset( self::$settings[$setting_key] ) ) ) {
				return self::$settings[$setting_key];
			} else {
				return self::$settings;
			}
		}
		
		static function get_post_type() {
			return self::$post_type;
		}

		/*
		function load_steps() {
			
			if ( is_null( $this->steps_object ) ) {
				$this->steps_object = LDLMS_Factory_Post::course_steps( $this->id );
				$this->steps_object->load_steps();
			}
		}
		*/
		/*
		function get_steps( $steps_type = 'h' ) {
			$this->load_steps();
			
			return $this->steps_object->get_steps( $steps_type );
		}
		*/
		/*		
		function set_steps( $course_steps = array() ) {
			$this->load_steps();
			
			return $this->steps_object->set_steps( $course_steps );
		}		
		*/
		/*		
		function get_item_parent_steps( $post_id = 0, $post_type = '' ) {
			$item_ancestor_steps = array();
			
			if ( !empty( $post_id ) ) {
				if ( empty( $post_type ) ) {
					$post_type = get_post_type( $post_id );
				}

				if ( !empty( $post_type ) ) {
					//$this->load_steps();
					$steps_r = $this->get_steps('r');
					
					$steps_key = $post_type .':'. $post_id;
					if ( isset( $steps_r[$steps_key] ) ) {
						$item_ancestor_steps = $steps_r[$steps_key];
					}
				} 
			} 
			
			return $item_ancestor_steps;
		}
		*/
		/*
		function get_parent_step_id( $step_post_id = 0, $ancestor_step_type = '' ) {
			if ( !empty( $step_post_id ) ) {
				$step_ancestor_item = $this->get_item_parent_steps( $step_post_id );
				if ( !empty( $step_ancestor_item ) ) {
					foreach( $step_ancestor_item as $parent_steps_value ) {
						//error_log('parent_steps_value<pre>'. print_r($parent_steps_value, true) .'</pre>');
						if ( ( is_string( $parent_steps_value ) ) && ( !empty( $parent_steps_value ) ) ) {
							list( $s_post_type, $s_post_id ) = explode(':', $parent_steps_value );
							if ( !empty( $ancestor_step_type ) ) {
								if ( $ancestor_step_type == $s_post_type ) {
									return intval( $s_post_id );
								}
							} else {
								return intval( $s_post_id );
							}
						}
					}
				}
			}
		}
		*/
		/*
		function get_children_steps( $parent_post_id = 0, $post_type = '' ) {
			$item_children_steps = array();
			
			$steps_h = $this->get_steps('h');

			if ( !empty( $parent_post_id ) ) {

				$ancestor_steps = $this->get_item_parent_steps( $parent_post_id );
				if ( !empty( $ancestor_steps ) ) {
					$ancestor_steps = array_reverse( $ancestor_steps );
				}
				$ancestor_steps[] = get_post_type( $parent_post_id ) .':'. $parent_post_id;
				foreach( $ancestor_steps as $ancestor_step ) {
					if ( ( is_string( $ancestor_step ) ) && ( !empty( $ancestor_step ) ) ) {
						list( $ancestor_step_post_type, $ancestor_step_post_id ) = explode(':', $ancestor_step );
						if ( isset( $steps_h[$ancestor_step_post_type][$ancestor_step_post_id] ) ) {
							$steps_h = $steps_h[$ancestor_step_post_type][$ancestor_step_post_id];
						}
					} 
				}
			} 

			if ( !empty( $steps_h ) ) {
				foreach( $steps_h as $steps_post_type => $steps_post_set ) {
					if (( empty( $post_type) ) || ( $post_type == $steps_post_type ) ) {
						$item_children_steps = array_merge( $item_children_steps, array_keys( $steps_post_set ) );
					}
				}
			}
			
			return $item_children_steps;
		}
		*/
		/*
		function load_lessons_list( ) {
			$lessons_ids = array();
			
			if ( !empty( $this->id ) ) {
				$this->lessons_query_args = array(
					'post_type'		=>	LearnDash_Lesson::get_post_type(),
					'orderby' 		=> 	$this->settings['course_lesson_orderby'], 
					'order' 		=> 	$this->settings['course_lesson_order'],
					'fields'		=>	'ids',
					'meta_key' 		=> 	'course_id', 
					'meta_value' 	=> 	$this->id,
					'nopaging'		=>	true
				);

				error_log('lessons_query<pre>'. print_r($this->lessons_query_args, true) .'</pre>');
				$this->lessons_query = new WP_Query( $this->lessons_query_args );
				//error_log('lessons_query<pre>'. print_r($lessons_query, true) .'</pre>');
				if ( ( $this->lessons_query instanceof WP_Query ) && ( property_exists( $this->lessons_query, 'posts' ) ) ) {
					$lessons_ids = $this->lessons_query->posts;
				}
			}
			
			return $lessons_ids;
		}
		*/
	}
}
