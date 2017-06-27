<?php
class Shift_HC_Model extends MY_model
{
	var $table = 'shifts';
	var $default_order_by = array(
		'date' => 'ASC',
		'start' => 'ASC',
		'date_end' => 'ASC',
		'end' => 'ASC',
		'id' => 'ASC'
		);
	var $has_one = array(
		'user' => array(
			'class'			=> 'user',
			'other_field'	=> 'shift',
			),
		'location' => array(
			'class'			=> 'location',
			'other_field'	=> 'shift',
			),
		);

	const STATUS_ACTIVE = 1;
	const STATUS_DRAFT = 2;

	const TYPE_SHIFT = 1;
	const TYPE_TIMEOFF = 2;

	var $validation = array(
		'date'		=> array('required', 'trim', 'check_date_end'),
		'location'	=> array('check_location'),
		'end'		=> array('required', 'check_time', 'check_date_end', 'check_break'),
		'end'		=> array('required', 'check_date_end', 'check_break'),
		'lunch_break'	=> array('check_break'),
		'status'	=> array(
			'enum' => array(
				self::STATUS_ACTIVE,
				self::STATUS_DRAFT,
				)
			),
		'type'	=> array(
			'enum' => array(
				self::TYPE_SHIFT,
				self::TYPE_TIMEOFF,
				)
			),
		);

	protected function _prepare_get()
	{
		parent::_prepare_get();
		$this
			->include_related( 'location', array('id', 'name', 'show_order', 'description', 'color'), TRUE, TRUE )
			->include_related( 'user', array('id', 'first_name', 'last_name', 'active'), TRUE, TRUE )
			->order_by( 'date', 'ASC' )
			->order_by( 'start', 'ASC' )
			->order_by( 'user_last_name IS NULL', 'ASC' )
			->order_by( 'user_last_name', 'ASC' )
			->order_by( 'user_first_name', 'ASC' )
			->order_by( 'date_end', 'ASC' )
			->order_by( 'end', 'ASC' )
			->order_by( 'status', 'ASC' )
			->order_by( 'location_show_order', 'ASC' )
			;
	}

/*
	public function count($exclude_ids = NULL, $column = NULL, $related_id = NULL)
	{
		$this->_prepare_get();
		return parent::count($exclude_ids, $column, $related_id);
	}
*/

	public function get_changes( $relations = NULL )
	{
		$return = parent::get_changes( $relations );
		$conf = HC_App::app_conf();
		$show_end_time = $conf->get('show_end_time');
		if( ! $show_end_time ){
			unset($return['end']);
		}
		return $return;
	}

	public function get( $limit = NULL, $offset = NULL )
	{
		$this->_prepare_get();
		return parent::get( $limit, $offset );
	}

	public function get_iterated( $limit = NULL, $offset = NULL )
	{
		$this->_prepare_get();
		return parent::get_iterated( $limit, $offset );
	}

	public function from_array( $data )
	{
		$return = parent::from_array( $data );
		$this->skip_validation( FALSE );
		$this->validate();
		$this->skip_validation( FALSE );
		return $return;
	}

	public function get_duration( $use_break = TRUE )
	{
		if( 
			( $this->date_end && ($this->date_end > $this->date) )
			OR
			( $this->end <= $this->start )
			){
			$return = $this->end + (24*60*60 - $this->start);
		}
		else {
			$return = $this->end - $this->start;
		}

		if( $use_break ){
			if( isset($this->lunch_break) && $this->lunch_break ){
				$return = $return - $this->lunch_break;
			}
		}
		return $return;
	}

/* checking conflicts */
	public function overlaps( $two )
	{
		$return = TRUE;

		$one_start	= $this->date . sprintf('%05d', $this->start);
		$one_end	= $this->date_end . sprintf('%05d', $this->end);
		$two_start	= $two->date . sprintf('%05d', $two->start);
		$two_end	= $two->date_end . sprintf('%05d', $two->end);

		if(
			( $one_end <= $two_start )
			OR
			( $one_start >= $two_end )
		){
			$return = FALSE;
		}

		if( $return ){
			/* if me */
			if(
				( $this->id ) &&
				( $this->id == $two->id )
			){
				$return = FALSE;
			}
		}

		return $return;
	}
	
	public function covers( $two )
	{
		$return = FALSE;

		$one_start	= $this->date . sprintf('%05d', $this->start);
		$one_end	= $this->date_end . sprintf('%05d', $this->end);
		$two_start	= $two->date . sprintf('%05d', $two->start);
		$two_end	= $two->date_end . sprintf('%05d', $two->end);

		if(
			( $one_end >= $two_end )
			&&
			( $one_start <= $two_start )
		){
			$return = TRUE;
		}

		if( $return ){
			/* if me */
			if(
				( $this->id ) &&
				( $this->id == $two->id )
			){
				$return = FALSE;
			}
		}

		return $return;
	}

/* validation */
	public function _related_check_location( $field )
	{
		if( $this->type == self::TYPE_TIMEOFF ){
			return TRUE;
		}

		if( $this->location && $this->location->exists() ){
			$return = TRUE;
		}
		else {
			$return = HCM::__('Required field');
		}
		return $return;
	}

	public function _check_date_end( $field )
	{
		if( ! $this->date ){
			return TRUE;
		}

		if( $this->end <= $this->start ){
			$t = HC_Lib::time();
			$t->setDateDb( $this->date );
			$t->modify( '+1 day' );
			$this->date_end = $t->formatDate_Db();
		}
		else {
			$this->date_end = $this->date;
		}
		return TRUE;
	}

	public function _check_time( $field )
	{
		$return = ( $this->end != $this->start ) ? TRUE : FALSE;
		if( ! $return ){
			$return = HCM::__('The end time should differ from the start time');
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

	public function load( $state = array() )
	{
		$t = HC_Lib::time();

		$state_date = isset($state['date']) ? $state['date'] : '';
		list( $start_date, $end_date ) = $t->getDatesRange( $state_date, $state['range'] );

		if( $start_date && $end_date ){
			// $shifts->where('date_end >=', $start_date);
			// $shifts->where('date <=', $end_date);
			$this->where('date_end >=', $start_date);
			$this->where('date <=', $end_date);
			$this->where('date >=', $start_date);
		}
		elseif( $start_date && $end_date === 0 ){
			if( isset($state['include_yesterday']) && $state['include_yesterday'] ){
				$this->group_start();
					$this->where('date =', $start_date);
					$this->or_where('date_end =', $start_date);
				$this->group_end();
			}
			else {
				$this->where('date =', $start_date);
			}
		}
		elseif( $start_date && ($end_date === NULL) ){
			$this->where('date_end >=', $start_date);
		}

	/* location */
		$where_location = array();
		if( isset($state['location']) ){
			if( ! is_array($state['location']) ){
				$state['location'] = array($state['location']);
			}
			$where_location = $state['location'];
		}

	/* type */
		if( isset($state['by']) && ($state['by'] == 'location') ){
			$this->where('type', $this->_const('TYPE_SHIFT'));
		}
/* 
		if( $where_location ){
			$this->where('type', $this->_const('TYPE_SHIFT'));
		}
*/
		if( $where_location ){
			$this->group_start();
			$this->or_where('type', $this->_const('TYPE_TIMEOFF'));
			$this->or_where_in_related('location', 'id', $where_location);
			$this->group_end();
		}

	/* staff */
		$where_staff = array();
		if( isset($state['staff']) ){
			if( ! is_array($state['staff']) ){
				$state['staff'] = array($state['staff']);
			}
			$where_staff = $state['staff'];
		}

		if( count($where_staff) ){
			if( in_array(0, $where_staff) ){
				$this->group_start();
				$this->or_where_related('user', 'id', NULL, TRUE);
				$this->or_where_related('user', 'id', 0);
				$this->or_where_in_related('user', 'id', $where_staff);
				$this->group_end();
			}
			else {
				$this->where_related('user', 'id', $where_staff);
			}
		}

		if( isset($state['type']) && ($state['type'] !== NULL) ){
			$this_types = array();
			$this_statuses = array();
			foreach( $state['type'] as $stype ){
				if( strpos($stype, '_') === FALSE ){
					$this_type = $stype;
					$this_types[$this_type] = 1;
				}
				else {
					list( $this_type, $this_status ) = explode('_', $stype);
					$this_types[$this_type] = 1;
					$this_statuses[$this_status] = 1;
				}
			}
			if( $this_types ){
				$this->where_in('type', array_keys($this_types));
			}
			if( $this_statuses ){
				$this->where_in('status', array_keys($this_statuses));
			}
		}

	/* status */
		if( isset($state['status']) ){
			$this->where('status', $state['status']);
		}
	
	/* extensions */
		$extensions = HC_App::extensions();
		$current_filter = '';
		if( isset($state['filter']) ){
			$current_filter = $state['filter'];
		}

		$return = $this;

	/* preprocess */
		if( $current_filter ){
			if( $extensions->has(array('list/filter', $current_filter)) ){
				$return = $extensions->run(
					array('list/filter', $current_filter),
					'pre',
					$return
					);
			}
		}

	/* NOW GET */
		$return->get();
		// $shifts->get_iterated();
		// $return->check_last_query();

	/* extensions with postprocess */
		if( $current_filter ){
			if( $extensions->has(array('list/filter', $current_filter)) ){
				$return = $extensions->run(
					array('list/filter', $current_filter),
					'post',
					$return
					);
			}
		}

		return $return;
	}
	
	protected function _before_delete()
	{
		$CI =& ci_get_instance();

	/* delete notes */
		if( $CI->hc_modules->exists('notes') ){
			$this->note->get()->delete_all();
		}

	/* delete logaudit */
		if( $CI->hc_modules->exists('logaudit') ){
			$logaudit = HC_App::model("logaudit");
			$logaudit
				->where('object_class',	$this->my_class())
				->where('object_id',	$this->id)
				->delete()
				;
		}

	/* delete trade */
		if( $CI->hc_modules->exists('trades') ){
			$this->trade_request->get()->delete_all();
			$this->offer_request->get()->delete_all();
			$this->offer_offer->get()->delete_all();
		}
	}

	public function __get($name)
	{
		switch( $name ){
			case 'time_id':
				return $this->start . '-' . $this->end;
				break;
		}
		return parent::__get($name);
	}
}