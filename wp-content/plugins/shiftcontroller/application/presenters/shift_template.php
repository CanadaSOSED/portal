<?php
class Shift_Template_HC_Presenter extends HC_Presenter
{
	function time( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE, $with_break = TRUE )
	{
		$test_shift = HC_App::model('shift');
		$test_shift->start = $model->start;
		$test_shift->end = $model->end;
		$test_shift->lunch_break = $model->lunch_break;

		$return = $test_shift->present_time($vlevel, FALSE, $with_break);
		return $return;
	}
}