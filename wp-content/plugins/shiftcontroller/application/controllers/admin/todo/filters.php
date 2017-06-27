<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Filters_Todo_Admin_HC_controller extends _Front_HC_controller
{
	public function draft( $what = 'label', $shifts = NULL )
	{
		switch( $what ){
			case 'label':
				return $this->_draft_label();
				break;
			case 'pre':
				return $this->_draft_pre($shifts);
				break;
			case 'post':
				return $this->_draft_post($shifts);
				break;
		}
	}

	private function _draft_label()
	{
		return HCM::__('Draft Shifts');
	}
	private function _draft_pre( $shifts )
	{
		$shifts
			->where('type', $shifts->_const('TYPE_SHIFT'))
			->where('status', $shifts->_const('STATUS_DRAFT'))
			;
		return $shifts;
	}
	private function _draft_post( $shifts )
	{
		return $shifts;
	}

	public function pending_timeoffs( $what = 'label', $shifts = NULL )
	{
		switch( $what ){
			case 'label':
				return $this->_pending_timeoffs_label();
				break;
			case 'pre':
				return $this->_pending_timeoffs_pre($shifts);
				break;
			case 'post':
				return $this->_pending_timeoffs_post($shifts);
				break;
		}
	}

	private function _pending_timeoffs_label()
	{
		return HCM::__('Pending Timeoff Requests');
	}
	private function _pending_timeoffs_pre( $shifts )
	{
		$shifts
			->where('type', $shifts->_const('TYPE_TIMEOFF'))
			->where('status', $shifts->_const('STATUS_DRAFT'))
			;
		return $shifts;
	}
	private function _pending_timeoffs_post( $shifts )
	{
		return $shifts;
	}	
}
?>