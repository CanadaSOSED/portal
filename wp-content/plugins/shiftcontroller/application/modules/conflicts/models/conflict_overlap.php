<?php
include_once( dirname(__FILE__) . '/conflict.php' );

class Conflict_Overlap_HC_Model extends Conflict_HC_Model
{
	protected $type = 'overlap';

	function get( $model )
	{
		$myclass = get_class();

		$return = array();

	/* find shifts that overlap */
		if( ! (strlen($model->start) && strlen($model->end)) ){
			return $return;
		}
		if( ! ($model->date && $model->date_end) ){
			return $return;
		}
		if( ! $model->user_id ){
			return $return;
		}

		$sm = HC_App::model('shift');
		$sm
			->select('id, date, date_end, start, end')
			->where_related('user', 'id', $model->user_id)
			->where('date_end >=', $model->date)
			->where('date <=', $model->date_end)
			;
		if( $model->id ){
			$sm->where('id <>', $model->id);
		}
		if( $model->type == $model->_const('TYPE_TIMEOFF') ){
			$sm->where('type <>', $model->_const('TYPE_TIMEOFF'));
		}
		$sm->get_iterated_slim();

		foreach( $sm as $test ){
			if( $model->overlaps($test) ){
				$conflict = new $myclass;

				$conflict_id = array($model->id, $test->id);
				sort($conflict_id);
				$conflict_id = $this->type . '_' . join('_', $conflict_id);

				$conflict->id = $conflict_id;
				$conflict->shift_id = $model->id;
				$conflict->details = $test->id;
				$return[] = $conflict;
			}
		}
		return $return;
	}
}