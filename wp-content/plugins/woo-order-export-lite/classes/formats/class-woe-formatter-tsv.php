<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once 'abstract-class-woe-formatter-sv.php';

class WOE_Formatter_Tsv extends WOE_Formatter_sv {
	protected $type = 'tsv';

	public function __construct( $mode, $filename, $settings, $format, $labels ) {
		parent::__construct( $mode, $filename, $settings, $format, $labels );

		$this->enclosure = '';
		$this->delimiter = "\t";
	}

	protected function delete_tabulation_from_array( &$data ) {
		$data = array_map( array( $this, 'delete_tabulation_callback' ), $data );
	}

	protected function delete_tabulation_callback( $value ) {
		return str_replace( $this->delimiter, '', $value );
	}

	protected function prepare_array( &$arr ) {
		$this->delete_tabulation_from_array( $arr );
		parent::prepare_array( $arr );
	}
}
