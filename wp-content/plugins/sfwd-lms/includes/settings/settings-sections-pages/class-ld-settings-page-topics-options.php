<?php
if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( !class_exists( 'LearnDash_Settings_Page_Topics_Options' ) ) ) {
	class LearnDash_Settings_Page_Topics_Options extends LearnDash_Settings_Page {

		function __construct() {
			
			$this->parent_menu_page_url		=	'edit.php?post_type=sfwd-topic';
			$this->menu_page_capability		=	LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id 		= 	'topics-options';
			$this->settings_page_title 		= 	sprintf( esc_html_x( '%s Options', 'placeholder: Topic', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic') );

			parent::__construct(); 
		}

		function admin_menu() {
			//$hookname = add_submenu_page( $parent_slug, $name, $name, LEARNDASH_ADMIN_CAPABILITY_CHECK, $this->get_prefix( $k ) . $k, array( $this, "display_settings_page_$k") );
			
			$this->settings_screen_id = add_submenu_page(
				'edit.php?post_type=sfwd-topic',
				$this->settings_page_title,
				$this->settings_page_title,
				LEARNDASH_ADMIN_CAPABILITY_CHECK,
				$this->settings_page_id,
				array( $this, 'show_settings_page' )
			);
			parent::admin_menu();
		}
	}
}
add_action( 'learndash_settings_pages_init', function() {
	LearnDash_Settings_Page_Topics_Options::add_page_instance();
} );
