<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ( !class_exists( 'LDLMS_Model_Post' ) ) && ( class_exists( 'LDLMS_Model' ) ) ) {
	abstract class LDLMS_Model_Post extends LDLMS_Model {

		protected $id = null;
		protected $post = null;
		protected static $settings = array();

		public function __construct( ) {
		}
	}
}