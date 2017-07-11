<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WOE_Formatter_Xml extends WOE_Formatter {

	public function start( $data = '' ) {
		parent::start( $data );
		fwrite( $this->handle, apply_filters( "woe_xml_output_header", '<?xml version="1.0" encoding="UTF-8"?>') . "\n" );
		if(@$this->settings['prepend_raw_xml'])
			fwrite( $this->handle, $this->settings['prepend_raw_xml'] . "\n" );
        
        fwrite( $this->handle, apply_filters( "woe_xml_output_before_root_tag", ''));
        
		if($this->settings['root_tag'])	
			fwrite( $this->handle, "<" . $this->settings['root_tag'] . ">\n" );
        
        fwrite( $this->handle, apply_filters( "woe_xml_output_after_root_tag", ''));
	}

	public function output( $rec ) {
		parent::output( $rec );
		$xml = new SimpleXMLElement( "<" . $this->settings['order_tag'] . "></" . $this->settings['order_tag'] . ">" );

		$labels = $this->labels['order'];
		$rec = apply_filters('woe_xml_prepare_record', $rec);
		foreach ( $rec as $field => $value ) {
			$value = apply_filters('woe_xml_prepare_field_'.$field, $value, $rec);
			if ( is_array( $value ) ) {
				$childs = $xml->addChild( $labels[ $field ] ); // add Products
				if ( $field == "products" ) {
					$child_tag    = $this->settings['product_tag'];
					$child_labels = $this->labels['products'];
				} elseif ( $field == "coupons" ) {
					$child_tag    = $this->settings['coupon_tag'];
					$child_labels = $this->labels['coupons'];
				} else {
					// array was created by hook!
					$child_tag = '';
					$child_labels =  array() ;
				}
				// modify children using filters
				$child_tag = apply_filters('woe_xml_child_tagname_'.$field, $child_tag, $value, $rec );
				$child_labels = apply_filters('woe_xml_child_labels_'.$field, $child_labels, $value, $rec );
				
				foreach ( $value as $child_key=>$child_elements ) {
					$tag_name = $child_tag ? $child_tag : $child_key;
					// add nested Product if array!
					$child = $childs->addChild( $tag_name, is_array($child_elements) ? NULL : $this->prepare_string($child_elements) ); 
					// products/coupons	
					if( is_array($child_elements) )	 {
						foreach ( $child_elements as $field_child => $value_child ) {
							if( isset( $child_labels[ $field_child ] ) ) 
								$child->addChild( $child_labels[ $field_child ], $this->prepare_string($value_child) );
						}
					}	
				}
			} else {
				$xml->addChild( $labels[ $field ] , $this->prepare_string($value) );
			}
		}

		//format it!
		$dom                              = dom_import_simplexml( $xml );
		$dom->ownerDocument->formatOutput = ( $this->mode == 'preview' );
		$xml                              = $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement );

		if ( $this->has_output_filter ) {
			$xml = apply_filters( "woe_xml_output_filter", $xml, $rec );
		}

		fwrite( $this->handle, $xml . "\n" );
	}

	public function finish( $data = '' ) {
		if($this->settings['root_tag'])	
			fwrite( $this->handle, "</" . $this->settings['root_tag'] . ">\n" );
		if(@$this->settings['append_raw_xml'])
			fwrite( $this->handle, $this->settings['append_raw_xml'] . "\n" );
		parent::finish();
	}
    
    private function prepare_string($value) {
        return htmlspecialchars($value);
    }
}