<?php
$css_files = array(
	'happ/assets/css/hc-hitcode.css',
	'happ/assets/css/hc-datepicker.css',
	);

if( defined('NTS_DEVELOPMENT') ){
	$css_files = array(
		'happ/assets/css/hc-1-reset.css',
		'happ/assets/css/hc-2-utilities.css',
		'happ/assets/css/hc-3-bass.css',
		'happ/assets/css/hc-6-grid.css',
		'happ/assets/css/hc-7-javascript.css',
		'happ/assets/css/hc-8-datepicker.css',
		'happ/assets/css/hc-9-schecal.css',
		);
	if( ! (isset($ri) && $ri) ){
		$css_files = array_merge( $css_files, array(
			'happ/assets/css/hc-4-style.css',	//
			'happ/assets/css/hc-5-form.css',	//
			)
		);
	}
	sort( $css_files );
}
else {
	$css_files = array(
		'happ/assets/css/hc.css',
		);
}

$js_files = array(
	'happ/assets/js/jquery-1.11.3.min.js',
//	'happ/assets/js/jquery-ui-sortable.min.js',
	'happ/assets/js/hc.js',
	'happ/assets/js/hc-datepicker.js',
	// 'happ/assets/js/underscore-min.js',
	// 'happ/assets/js/backbone-min.js',
	);
?>