<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WOE_Formatter_sv' ) ) {
	return;
}

abstract class WOE_Formatter_sv extends WOE_Formatter {
	protected $type;
	protected $enclosure;
	protected $linebreak;
	protected $delimiter;
	protected $encoding;
	protected $rows;

	public function __construct( $mode, $filename, $settings, $format, $labels ) {
		parent::__construct( $mode, $filename, $settings, $format, $labels );

		$this->enclosure = $this->convert_literals( isset( $this->settings['enclosure'] ) ? $this->settings['enclosure'] : '' );
		$this->linebreak = $this->convert_literals( isset( $this->settings['linebreak'] ) ? $this->settings['linebreak'] : '' );
		$this->delimiter = $this->convert_literals( isset( $this->settings['delimiter'] ) ? $this->settings['delimiter'] : '' );
		$this->encoding  = isset( $this->settings['encoding'] ) ? $this->settings['encoding'] : '';

		// register the filter
		WOE_Formatter_sv_crlf_filter::set_linebreak( $this->linebreak );
		stream_filter_register( "WOE_Formatter_{$this->type}_crlf", 'WOE_Formatter_sv_crlf_filter' );
		// attach to stream 
		stream_filter_append( $this->handle, "WOE_Formatter_{$this->type}_crlf" );
	}

	public function start( $data = '' ) {
		$data = apply_filters( "woe_{$this->type}_header_filter", $data );
		$this->prepare_array( $data );
		parent::start( $data );

		if ( $this->settings['add_utf8_bom'] ) {
			fwrite( $this->handle, chr( 239 ) . chr( 187 ) . chr( 191 ) );
		}

		if ( $this->settings['display_column_names'] ) {
			if ( $this->mode == 'preview' ) {
				$this->rows[] = $data;
			} else {
				if( !apply_filters("woe_{$this->type}_custom_output_func",false, $this->handle, $data, $this->delimiter, $this->linebreak, $this->enclosure ) ) {
					if ( $this->enclosure !== '' ) {
						fputcsv( $this->handle, $data, $this->delimiter, $this->enclosure );
					} else {
						fwrite( $this->handle, implode( $this->delimiter, $data ) . $this->linebreak );
					}
				}
				do_action( "woe_{$this->type}_print_header", $this->handle, $data, $this);
			}
		}
	}

	public function output( $rec ) {
		$this->prepare_array( $rec );
		parent::output( $rec );

		if ( $this->has_output_filter ) {
			$rec = apply_filters( "woe_{$this->type}_output_filter", $rec, $rec );
		}

		if ( $this->mode == 'preview' ) {
			$this->rows[] = $rec;
		} else {
			if( ! apply_filters("woe_{$this->type}_custom_output_func",false, $this->handle, $rec, $this->delimiter, $this->linebreak, $this->enclosure ) ) {
				if ( $this->enclosure !== '' ) {
					fputcsv( $this->handle, $rec, $this->delimiter, $this->enclosure );
				} else {
					fwrite( $this->handle, implode( $this->delimiter, $rec ) . $this->linebreak );
				}
			}
		}
	}

	public function finish() {
		if ( $this->mode == 'preview' ) {
			$this->rows = apply_filters( "woe_{$this->type}_preview_rows", $this->rows );
			fwrite( $this->handle, '<table>' );
			if ( count( $this->rows ) < 2 ) {
				$this->rows[] = array( __( '<td colspan=10><b>No results</b></td>', 'woocommerce-order-export' ) );
			}
			foreach ( $this->rows as $rec ) {
				$rec = array_map( function ( $a ) {
					return '<td>' . $a . '';
				}, $rec );
				fwrite( $this->handle, '<tr><td>' . join( '</td><td>', $rec ) . "</td><tr>\n" );
			}
			fwrite( $this->handle, '</table>' );
		} else {
			do_action( "woe_{$this->type}_print_footer", $this->handle );
		}
		parent::finish();
	}

	protected function convert_literals( $s ) {
		$s = str_replace( '\r', "\r", $s );
		$s = str_replace( '\t', "\t", $s );
		$s = str_replace( '\n', "\n", $s );

		return $s;
	}

	protected function prepare_array( &$arr ) {
		$this->encode_array( $arr );
	}

	protected function encode_array( &$arr ) {
		if ( ! in_array( $this->encoding, array( '', 'utf-8', 'UTF-8' ) ) ) {
			$arr = array_map( array( $this, 'encode_value' ), $arr );
		}
	}

	protected function encode_value( $value ) {
		return iconv( 'UTF-8', $this->encoding, $value );
	}
}

// filter class that applies CRLF line endings
class WOE_Formatter_sv_crlf_filter extends php_user_filter {
	protected static $linebreak;

	public static function set_linebreak( $linebreak ) {
		self::$linebreak = $linebreak;
	}

	function filter( $in, $out, &$consumed, $closing ) {
		while ( $bucket = stream_bucket_make_writeable( $in ) ) {
			// make sure the line endings aren't already CRLF
			$bucket->data = preg_replace( "/(?<!\r)\n/", self::$linebreak, $bucket->data );
			$consumed += $bucket->datalen;
			stream_bucket_append( $out, $bucket );
		}

		return PSFS_PASS_ON;
	}
}

