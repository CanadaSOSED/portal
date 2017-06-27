<?php
class Location_HC_Model extends MY_model
{
	static $me_all = array();

	var $table = 'locations';
	var $default_order_by = array('show_order' => 'ASC');

	var $has_many = array(
		'shift' => array(
			'class'			=> 'shift',
			'other_field'	=> 'location',
			),
		);

	var $validation = array(
		'name'	=> array('required', 'trim', 'max_length' => 50, 'unique', 'check_show_order'),
		);
}