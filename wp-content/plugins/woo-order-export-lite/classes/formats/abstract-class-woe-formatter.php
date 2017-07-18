<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class WOE_Formatter {
	var $has_output_filter;
	var $mode;
	var $settings;
	var $labels;
	var $handle;
	var $format;

	public function __construct( $mode, $filename, $settings, $format, $labels ) {
		$this->has_output_filter = has_filter( "woe_{$format}_output_filter" );
		$this->mode              = $mode;
		$this->settings          = $settings;
		$this->labels            = $labels;
		$this->handle            = fopen( $filename, 'a' );
		if ( ! $this->handle ) {
			throw new Exception( $filename . __( 'can not open for output', 'woocommerce-order-export' ) );
		}
		$this->format            = $format;
	}

	public function start( $data = '' ) {
		do_action("woe_formatter_start", $data);
		do_action("woe_formatter_" .$this->format. "_start", $data);
	}

	public function output( $rec ) {
		$this->handle = apply_filters( "woe_formatter_set_handler_for_" . $this->format . "_row", $this->handle );
	}

	public function finish() {
		fclose( $this->handle );
		do_action("woe_formatter_finish");
		do_action("woe_formatter_" .$this->format. "_finished");
	}

	public function truncate() {
		ftruncate( $this->handle, 0 );
	}
}