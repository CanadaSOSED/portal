<?php
if ( !class_exists( 'LD_REST_Terms_Controller' ) ) {
	abstract class LD_REST_Terms_Controller extends WP_REST_Terms_Controller {

		protected $version = 'v1';

		public function __construct( $taxonomy = '' ) {
			parent::__construct( $taxonomy );
		}		
	}
}