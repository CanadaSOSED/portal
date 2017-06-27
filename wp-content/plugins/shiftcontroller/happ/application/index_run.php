<?php
require_once BASEPATH.'core/CodeIgniter_run.php';

/* if ajax display it and exit */
$CI =& ci_get_instance();

if( $CI->input->wants_json() ){
	$CI->output->set_content_type('application/json');
	$already_out = $CI->output->get_output();
	echo $already_out;
	hc_ci_before_exit();
	exit;
}

if( $CI->input->is_ajax_request() ){
	if( $CI->input->post() ){
		$return = array();
		$already_out = $CI->output->get_output();
		if( strlen($already_out) ){
			$return = array('html' => $already_out);
			$CI->output->set_content_type('application/json');
			$CI->output->set_output( json_encode($return) );
		}
	}
	$CI->output->_display();
	hc_ci_before_exit();
	exit;
}

/* End of file index.php */
/* Location: ./index.php */