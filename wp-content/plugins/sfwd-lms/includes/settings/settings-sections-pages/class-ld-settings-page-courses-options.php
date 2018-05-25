<?php
if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( !class_exists( 'LearnDash_Settings_Page_Courses_Options' ) ) ) {
	class LearnDash_Settings_Page_Courses_Options extends LearnDash_Settings_Page {

		function __construct() {

			$this->parent_menu_page_url		=	'edit.php?post_type=sfwd-courses';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'courses-options';
			$this->settings_page_title 		= 	sprintf( esc_html_x( '%s Options', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course') );
			$this->settings_tab_title		=	$this->settings_page_title;
			
			parent::__construct(); 
		}
	}
}
add_action( 'learndash_settings_pages_init', function() {
	LearnDash_Settings_Page_Courses_Options::add_page_instance();
} );
