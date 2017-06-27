<?php
class Shift_Template_HC_Model extends MY_model
{
/*
properties: 
	name
	start
	end
*/

	var $table = 'shift_templates';
	var $default_order_by = array('start' => 'ASC');
	var $validation = array(
		'name'	=> array('required', 'trim', 'max_length' => 50, 'unique'),
		'start'	=> array('required', 'trim'),
		'end'	=> array('required', 'trim', 'differs' => 'start', 'check_break'),
		'lunch_break'	=> array('check_break'),
		);

	public function get_duration( $use_break = TRUE )
	{
		if( $this->end > $this->start )
			$return = $this->end - $this->start;
		else
			$return = $this->end + (24*60*60 - $this->start);

		if( $use_break ){
			if( isset($this->lunch_break) && $this->lunch_break ){
				$return = $return - $this->lunch_break;
			}
		}
		return $return;
	}

	public function _check_break( $field )
	{
		$return = ( $this->get_duration(FALSE) > $this->lunch_break ) ? TRUE : FALSE;
		if( ! $return ){
			$return = HCM::__('The break should not be longer than the shift itself');
		}
		return $return;
	}
}