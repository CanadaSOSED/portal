<?php
if ( !class_exists( 'Learndash_Admin_Quiz_Edit' ) ) {
	class Learndash_Admin_Quiz_Edit {
		
		private $quiz_post_type = 'sfwd-quiz';
	    
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-post.php', 			array( $this, 'on_load') );
			add_action( 'load-post-new.php', 		array( $this, 'on_load') );
		}
		
		function on_load() {
			global $typenow;	// Contains the same as $_GET['post_type]
			
			if ( (empty( $typenow ) ) || ( $typenow != $this->quiz_post_type ) )  return;

			// Add Metabox and hook for saving post metabox
			add_action( 'add_meta_boxes', 			array( $this, 'add_metaboxes' ) );
			//add_action( 'save_post', 				array( $this, 'save_metaboxes' ) );
		}

		/**
		 * Register Groups meta box for admin
		 *
		 * Managed enrolled groups, users and group leaders
		 * 
		 * @since 2.1.2
		 */
		function add_metaboxes() {

			if ( ! empty( $_GET['post'] ) ) {
				if ( apply_filters( 'learndash_disable_advance_quiz', false, get_post( $_GET['post'] ) ) ) {
					return;
				}
			}
			add_meta_box(
				'learndash_quiz_advanced',
				sprintf( esc_html_x( '%s Advanced', 'placeholder: Quiz', 'learndash' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
				array( $this, 'quiz_advanced_page_box' ),
				$this->quiz_post_type
			);
		}
		
		function quiz_advanced_page_box( $post ) {
			//$quiz_post_id = $post->ID;
			
			echo LD_QuizPro::edithtml();

		}
		
		// End of functions
	}
}