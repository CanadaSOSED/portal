<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once 'abstract-class-woe-formatter-sv.php';

class WOE_Formatter_Csv extends WOE_Formatter_sv {
	protected $type = 'csv';
}