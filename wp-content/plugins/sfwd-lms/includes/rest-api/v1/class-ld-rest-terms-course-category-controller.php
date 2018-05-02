<?php
if ( ( !class_exists( 'LD_REST_Terms_Course_Category_Controller' ) ) && ( class_exists( 'LD_REST_Terms_Controller' ) ) ) {
	class LD_REST_Terms_Course_Category_Controller extends LD_REST_Terms_Controller {
		public function __construct( $taxonomy = '' ) {
			$this->taxonomy = 'ld_course_category';
			
			parent::__construct( $this->taxonomy );
			$this->namespace = LEARNDASH_REST_API_NAMESPACE .'/'. $this->version;
		}
	}
}