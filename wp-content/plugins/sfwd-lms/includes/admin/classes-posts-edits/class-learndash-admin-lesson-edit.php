<?php
/**
 * LearnDash Admin Lesson Edit Class.
 *
 * @package LearnDash
 * @subpackage Admin
 */

if ( ( class_exists( 'Learndash_Admin_Post_Edit' ) ) && ( ! class_exists( 'Learndash_Admin_Lesson_Edit' ) ) ) {
	/**
	 * Class for LearnDash Admin Lesson Edit.
	 */
	class Learndash_Admin_Lesson_Edit extends Learndash_Admin_Post_Edit {

		/**
		 * Public constructor for class.
		 */
		public function __construct() {
			$this->post_type = learndash_get_post_type_slug( 'lesson' );

			parent::__construct();
		}

		// End of functions.
	}
}
new Learndash_Admin_Lesson_Edit();
