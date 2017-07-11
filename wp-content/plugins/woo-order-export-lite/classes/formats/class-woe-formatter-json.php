<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WOE_Formatter_Json extends WOE_Formatter {
	var $prev_added = false;

	public function start( $data = '' ) {
		parent::start( $data );
		$start_text  = "[" ;
		fwrite( $this->handle, apply_filters( "woe_json_start_text", $start_text) );
	}

	public function output( $rec ) {
		parent::output( $rec );
		if ( $this->prev_added ) {
			fwrite( $this->handle, "," );
		}
		fwrite( $this->handle, "\n" );

		//rename fields in array
		$rec_out = array();
		$labels  = $this->labels['order'];
		foreach ( $rec as $field => $value ) {
			if ( is_array( $value ) ) {
				if ( $field == "products" ) {
					$child_labels = $this->labels['products'];
				} elseif ( $field == "coupons" ) {
					$child_labels = $this->labels['coupons'];
				} else {
					$rec_out[ $labels[ $field ] ] = $value;
					continue;
				}

				$rec_out[ $labels[ $field ] ] = array();
				foreach ( $value as $child_elements ) {
					$child = array();
					foreach ( $child_elements as $field_child => $value_child ) {
						if( isset( $child_labels[ $field_child ] ) )
							$child[ $child_labels[ $field_child ] ] = $value_child;
					}
					$rec_out[ $labels[ $field ] ][] = $child;
				}
			} else {
				$rec_out[ $labels[ $field ] ] = $value;
			}
		}

		if ( $this->mode == 'preview' ) {
			$json = json_encode( $rec_out, JSON_PRETTY_PRINT );
		} else {
			$json = json_encode( $rec_out );
		}

		if ( $this->has_output_filter ) {
			$json = apply_filters( "woe_json_output_filter", $json, $rec_out);
		}
		fwrite( $this->handle, $json );

		// first record added!
		if ( ! $this->prev_added ) {
			$this->prev_added = true;
		}
	}

	public function finish( $data = '' ) {
		$end_text  = "\n]" ;
		fwrite( $this->handle, apply_filters( "woe_json_end_text", $end_text) );
		parent::finish();
	}
}