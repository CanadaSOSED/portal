<?php
class Conflict_Overlap_HC_Presenter extends HC_Presenter
{
	function type( $model )
	{
		$return = HCM::__('Overlapping Conflict');
		return $return;
	}

	function details( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$conflicting_one = HC_App::model('shift');
		$conflicting_one
			->where('id', $model->details )
			->get()
			;

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = HC_Html_Factory::widget('shift_view')
					->set_new_window( TRUE )
					;
				$return->set_shift( $conflicting_one );
				$return->set_iknow( array('conflicts') );
				break;
			case HC_PRESENTER::VIEW_TEXT:
			case HC_PRESENTER::VIEW_RAW:
				$return = $conflicting_one->present_details($vlevel);
				break;
		}
		return $return;
	}
}