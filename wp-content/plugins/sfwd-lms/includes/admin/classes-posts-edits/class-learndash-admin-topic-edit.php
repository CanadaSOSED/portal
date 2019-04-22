<?php
/**
 * LearnDash Admin Topic Edit Class.
 *
 * @package LearnDash
 * @subpackage Admin
 */

if ( ( class_exists( 'Learndash_Admin_Post_Edit' ) ) && ( ! class_exists( 'Learndash_Admin_Topic_Edit' ) ) ) {
	/**
	 * Class for LearnDash Admin Topic Edit.
	 */
	class Learndash_Admin_Topic_Edit extends Learndash_Admin_Post_Edit {
		/**
		 * Public constructor for class.
		 */
		public function __construct() {
			$this->post_type = learndash_get_post_type_slug( 'topic' );

			parent::__construct();
		}

		// End of functions.
	}
}
new Learndash_Admin_Topic_Edit();
