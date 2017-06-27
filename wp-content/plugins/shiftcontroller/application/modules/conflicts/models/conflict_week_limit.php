<?php
include_once( dirname(__FILE__) . '/conflict.php' );

class Conflict_Week_Limit_HC_Model extends Conflict_HC_Model
{
	protected $type = 'week_limit';

	function get( $model )
	{
		$limit_qty = 2;
		$limit_duration = 4*60*60;

		$this->shift_id = $model->id;

		$return = array();

	/* find if the employee has more shifts that allowed per week */
		if( ! (strlen($model->start) && strlen($model->end)) ){
			return $return;
		}
		if( ! ($model->date && $model->date_end) ){
			return $return;
		}
		if( ! $model->user_id ){
			return $return;
		}

		if( $model->type != $model->_const('TYPE_SHIFT') ){
			return $return;
		}

		$t = HC_Lib::time();
		$t->setDateDb( $model->date );
		list( $start_week, $end_week ) = $t->getDates('week', TRUE);

		$my_qty = 0;
		$my_duration = 0;

		$sm = HC_App::model('shift');
		$sm
			->select('id, date, date_end, start, end')
			->where_related('user', 'id', $model->user_id)
			->where('date_end >=', $start_week)
			->where('date <=', $end_week)
			;
		$sm->get_iterated_slim();

		foreach( $sm as $test ){
			$my_qty += 1;
			$my_duration += $test->get_duration();
		}

		if( $my_qty > $limit_qty ){
			$conflict = clone $this;
			$conflict->details = 'qty:' . $my_qty;
			$return[] = $conflict;
		}
		if( $my_duration > $limit_duration ){
			$conflict = clone $this;
			$conflict->details = 'duration:' . $my_duration;
			$return[] = $conflict;
		}

		return $return;
	}
}