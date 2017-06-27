<?php
class Shift_HC_Presenter extends HC_Presenter
{
	function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = '';
		switch( $model->type ){
			case SHIFT_HC_MODEL::TYPE_SHIFT:
				switch( $vlevel ){
					case HC_PRESENTER::VIEW_HTML:
					case HC_PRESENTER::VIEW_HTML_ICON:
						$return = HC_Html::icon(HC_App::icon_for('shift'));
						break;
					case HC_PRESENTER::VIEW_TEXT:
						$return = HCM::__('Shift');
						break;
				}
				break;

			case SHIFT_HC_MODEL::TYPE_TIMEOFF:
				switch( $vlevel ){
					case HC_PRESENTER::VIEW_HTML:
					case HC_PRESENTER::VIEW_HTML_ICON:
						$return = HC_Html::icon(HC_App::icon_for('timeoff'));
						break;
					case HC_PRESENTER::VIEW_TEXT:
						$return = HCM::__('Timeoff');
						break;
				}
				break;
		}
		return $return;
	}

	public function property_name( $model, $pname, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = parent::property_name( $model, $pname, $vlevel );

		switch( $pname ){
			case 'user':
				$return = $model->user->present_label($vlevel);
				break;
			case 'location':
				// $return = $model->location->present_label($vlevel);
				$return = HCM::__('Location');
				break;
			case 'start':
				$return = HCM::__('Start Time');
				break;
			case 'lunch_break':
				$return = HCM::__('Break');
				break;
			case 'end':
				$return = HCM::__('End Time');
				break;
			case 'release_request':
				$return = HCM::__('Shift Release');
				break;
		}
		return $return;
	}

	function id( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = 'id:' . $model->id;
				$return = HC_Html_Factory::element('span')
					->add_style('mute')
					->add_style('nowrap')
					->add_style('font-size', -1)
					->add_child( $return )
					;
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = 'id:' . $model->id;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $model->id;
				break;
		}

		return $return;
	}

	function text( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		$return = array();
		$return['date'] = $model->present_date($vlevel, $with_change);
		$return['time'] = $model->present_time($vlevel, $with_change);
		if( $model->type == $model->_const('TYPE_SHIFT') ){
			$return['location'] = $model->present_location($vlevel, $with_change);
		}
		$return['user'] = $model->present_user($vlevel, $with_change);
		return $return;
	}

	function status_class( $model )
	{
		$return = array();
		list( $label_text, $label_class ) = $this->_status_details($model);
		if( ! is_array($label_class) )
			$label_class = array( $label_class );
		$return = array_merge( $return, $label_class );
		return $return;
	}

	function type( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $model->type ){
			case $model->_const('TYPE_TIMEOFF'):
				$type_text = HCM::__('Timeoff');
				$type_icon = 'timeoff';
				break;
			default:
				$type_text = HCM::__('Shift');
				$type_icon = 'shift';
				break;
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = HC_Html_Factory::widget('titled', 'span')
					->add_style('badge')
					->add_child( HC_Html::icon(HC_App::icon_for($type_icon)) )
					->add_child( $type_text )
					;
				break;
			case HC_PRESENTER::VIEW_HTML_ICON:
				$return = HC_Html::icon( HC_App::icon_for($type_icon) );

				list( $label_text, $label_class ) = $this->_status_details($model);
				$title = array();
				$title[] = $model->present_type(HC_PRESENTER::VIEW_RAW);
				$title[] = $label_text;
				$title = join( ': ', $title );
				$return->add_attr('title', $title);
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::_x('Type', 'noun') . ': ' . $type_text;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $type_text;
				break;
		}

		return $return;
	}

	function status( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		list( $label_text, $label_class ) = $this->_status_details($model);
		$label_class = $this->status_class($model, TRUE);
		$type_text = $model->present_type( HC_PRESENTER::VIEW_RAW );

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$label_text_text = $model->present_status( HC_PRESENTER::VIEW_RAW );

				$return = HC_Html_Factory::element('span')
					->add_attr('title', $label_text_text)
					->add_child( $label_text )
					->add_style('padding', 1)
					->add_style('rounded')
					;

				$type = $model->type; 
				switch( $type ){
					case $model->_const('TYPE_TIMEOFF'):
						$color = '#ddd';
						$color = '#eee';
						break;
					default:
						$color = '#dff0d8';
						// $color = Hc_lib::random_html_color( 2 );
						break;
				}

				if( $model->status == $model->_const('STATUS_DRAFT') ){
					$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
					$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

					$return->add_attr('style',
						"background: repeating-linear-gradient(
							-45deg,
							$color1,
							$color1 6px,
							$color2 6px,
							$color2 12px
							);
						"
						);
				}
				else { 
					$return->add_attr('style', 'background-color: ' . $color . ';');
				}

				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = join(': ', array(HCM::__('Status'), $type_text, $label_text));
				break;
			case HC_PRESENTER::VIEW_RAW:
				$type_label = $model->present_label( $vlevel );
				$return = join(': ', array($type_text, $label_text));
				break;
		}

		return $return;
	}

	private function _status_details( $model, $skip_type = TRUE )
	{
		$return = NULL;
		$status = $model->status;
		$type = $model->type; 

		switch( $type ){
			case $model->_const('TYPE_TIMEOFF'):
				$details = array(
					SHIFT_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'warning' ),
					SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Pending'),	array('warning-o', 'border-dotted') ),
					);
				break;
			default:
				$details = array(
					SHIFT_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'success' ),
					// SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Draft'),		array('success-o', 'border-dotted') ),
					SHIFT_HC_MODEL::STATUS_DRAFT	=> array( HCM::__('Draft'),		array('success-o') ),
					);
				break;
		}

		if( isset($details[$status]) ){
			$return = $details[$status];
		}

		return $return;
	}

	public function conflict_class( $model )
	{
		if( $model->status == $model->_const('STATUS_ACTIVE') ){
			$return = array('danger-o');
			$return = array('danger-m');
			// $return = array('danger', 'danger-o');
		}
		else {
			// $return = array('danger-m');
			$return = array();
			$return = array('danger-m');
		}
		return $return;
	}

	function status_text( $model )
	{
		$return = '';
		$details = $this->_status_details( $model );
		if( $details ){
			$return = $details[0];
		}
		return $return;
	}

	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array(
			$model->present_date($vlevel),
			$model->present_time($vlevel),
			);
		$return = join( ' ', $return );

		return $return;
	}

	function title_misc( $model )
	{
		$title_misc = array();

		switch( $model->type ){
			case $model->_const('TYPE_SHIFT'):
				$title_misc[] = $model->present_location();
				break;
		}

		$title_misc[] = $model->present_user();

		$out = HC_Html_Factory::widget('list')
			->add_style('inline')
			->add_style('margin', 'r1', 'b1')
			;
		foreach( $title_misc as $tm ){
			$out->add_child( $tm );
		}
		return $out;
	}

	function title_misc_misc( $model )
	{
		$title_misc = array();
		$title_misc[] = 'id: ' . $model->id;

		$out = HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r1', 'b1')
			;
		foreach( $title_misc as $tm ){
			$out->add_child( $tm );
		}
		return $out;
	}

	function details( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return = HC_Html_Factory::widget('shift_view');
				$return->set_shift( $model );
				break;
			case HC_PRESENTER::VIEW_TEXT:
			case HC_PRESENTER::VIEW_RAW:
				$return = array();
				$return[] = $model->present_title($vlevel);
				$return[] = $model->present_title_misc($vlevel);
				$return = join( ' ', $return );
				break;
		}
		return $return;
	}

	function user( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		if( ! isset($model->user) )
			$model->user->get();

		$return = array();
		if( $model->user->id && $model->user->exists() ){
			$return[] = $model->user->present_title($vlevel);
		}
		else {
			switch( $vlevel ){
				case HC_PRESENTER::VIEW_HTML:
					$user_view = $model->user->present_label($vlevel) . '______';
					$user_view = HC_Html_Factory::element('span')
						->add_attr('title', HCM::__('Open Shift'))
						->add_child( $user_view )
						->add_style('color', 'orange')
						;
					break;
				default:
					$user_view = HCM::__('Open Shift');
					break;
			}
			$return[] = $user_view;
		}

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['user_id'])
			){
				$current_user_id = $model->user ? $model->user->id : 0;
				if( $changes['user_id'] != $current_user_id ){
					$old_obj = HC_App::model( $model->user->my_class() );
					$old_obj->get_by_id( $changes['user_id'] );
					$return[] = ' [' . HCM::__('Old Value') . ': ' . $old_obj->present_title(HC_PRESENTER::VIEW_RAW) . ']';
				}
			}
		}
		$return = join( '', $return );
		return $return;
	}

	function location( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		if( $model->type == $model->_const('TYPE_TIMEOFF') ){
			$return = array();
			$return[] = $model->location->set('name', HCM::__('Timeoff'))->present_title($vlevel);
		}
		else {
			if( ! (isset($model->location) && $model->location->id) ){
				$model->location->get();
			}

			$return = array();
			$return[] = $model->location->present_title($vlevel);

			if( $with_change ){
				$changes = $model->get_changes();
				if( 
					isset($changes['location_id'])
				){
					$old_obj = HC_App::model( $model->location->my_class() );
					$old_obj->get_by_id( $changes['location_id'] );
					$return[] = ' [' . HCM::__('Old Value') . ': ' . $old_obj->present_title(HC_PRESENTER::VIEW_RAW) . ']';
				}
			}
		}
		$return = join( '', $return );
		return $return;
	}

	function start( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		return $this->_time( $model->start, $vlevel );
	}

	function end( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		return $this->_time( $model->end, $vlevel );
	}

	function release_request( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = NULL;
		if( $model->release_request ){
			$return = HCM::__('Pending');
			}
		else {
			$return = HCM::__('No Request');
		}
		return $return;
	}

	private function _time( $value, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return[] = HC_Html::icon(HC_App::icon_for('time'));
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Time');
				$return[] = ': ';
				break;
		}

		$t = HC_Lib::time();
		$t->setTimestamp( $value );
		$return[] = $t->formatTime();

		$return = join( '', $return );
		return $return;
	}

	function lunch_break( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		if( $model->type == $model->_const('TYPE_TIMEOFF') ){
			return;
		}

		$break = $model->lunch_break;
		if( ! $break ){
			return;
		}

		$t = HC_Lib::time();
		$return = array();

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return[] = HC_Html::icon(HC_App::icon_for('break'));
				break;
			default:
				$return[] = HCM::__('Break');
				$return[] = ': ';
				break;
		}

		$break_view = $t->formatPeriodExtraShort( $break, 'min' );
		$return[] = $break_view;

		$return = join( '', $return );
		return $return;
	}

	function time( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE, $with_break = TRUE )
	{
		$t = HC_Lib::time();
		$conf = HC_App::app_conf();
		$show_end_time = $conf->get('show_end_time');

		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				if( $model->type == $model->_const('TYPE_TIMEOFF') ){
					$return[] = HC_Html::icon(HC_App::icon_for('timeoff'));
				}
				else {
					// $return[] = HC_Html::icon(HC_App::icon_for('time'));
				}
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Time');
				$return[] = ': ';
				break;
		}

		switch( $model->type ){
			case $model->_const('TYPE_TIMEOFF'):
				$period_view = $t->formatPeriodOfDay($model->start, $model->end);
				break;

			default:
				if( $show_end_time ){
					$period_view = $t->formatPeriodOfDay($model->start, $model->end);
				}
				else {
					$t->setStartDay();
					$period_view = $t->modify('+' . $model->start . ' seconds')->formatTime();
				}
				break;
		}

		$period_view = str_replace(' ', '', $period_view);
		$return[] = $period_view;

		if( $with_break ){
			$break_view = $model->present_lunch_break($vlevel);
			if( strlen($break_view) ){
				$return[] = ' ' . $break_view;
			}
		}

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['start'])
				OR
				isset($changes['end'])
				OR
				isset($changes['lunch_break'])
			){
				$old_start = isset($changes['start']) ? $changes['start'] : $model->start;
				$old_end = isset($changes['end']) ? $changes['end'] : $model->end;
				$old_lunch_break = isset($changes['lunch_break']) ? $changes['lunch_break'] : $model->lunch_break;

				$old_model = clone $model;
				$old_model->start = $old_start;
				$old_model->end = $old_end;
				$old_model->lunch_break = $old_lunch_break;

				$return[] = ' [' . HCM::__('Old Value') . ': ' . $old_model->present_time($vlevel) . ']';
			}
		}

		$return = join( '', $return );
		return $return;
	}

	function date( $model, $vlevel = HC_PRESENTER::VIEW_HTML, $with_change = FALSE )
	{
		$t = HC_Lib::time();
		$t->setDateDb( $model->date );

		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$return[] = HC_Html::icon(HC_App::icon_for('date'));
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Date');
				$return[] = ': ';
				break;
		}

		$return[] = $t->formatDateFull();

		if( $with_change ){
			$changes = $model->get_changes();
			if( 
				isset($changes['date'])
			){
				$t->setDateDb( $changes['date'] );
				$return[] = ' [' . HCM::__('Old Value') . ': ' . $t->formatDateFull() . ']';
			}
		}

		$return = join( '', $return );
		return $return;
	}

	function calendar_refresh( $model )
	{
		$refresh_keys = array();
		$refresh_keys[] = 'dat-' . $model->date;
		$refresh_keys[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
		$refresh_keys[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';

		$parent_refresh = HC_Lib::get_combinations($refresh_keys);

		if( $model->date_end != $model->date ){
			$refresh_keys = array();
			$refresh_keys[] = 'dat-' . $model->date_end;
			$refresh_keys[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
			$refresh_keys[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';

			$parent_refresh2 = HC_Lib::get_combinations($refresh_keys);
			$parent_refresh = array_merge( $parent_refresh, $parent_refresh2 );
		}

		$refresh_keys2 = array();
		$refresh_keys3 = array();
		$changes = $model->get_changes();

		if( ! array_key_exists('id', $changes) ){
			if( array_intersect(array('date', 'user_id', 'location_id'), array_keys($changes)) ){
				if( array_key_exists('user_id', $changes) ){
					$refresh_keys2[] = $changes['user_id'] ? 'use-' . $changes['user_id'] : 'use-0';
				}
				else {
					$refresh_keys2[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';
				}

				if( array_key_exists('location_id', $changes) ){
					$refresh_keys2[] = $changes['location_id'] ? 'loc-' . $changes['location_id'] : 'loc-0';
				}
				else {
					$refresh_keys2[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
				}

				if( array_key_exists('date', $changes) ){
					$refresh_keys2[] = $changes['date'] ? 'dat-' . $changes['date'] : 'dat-0';
				}
				else {
					$refresh_keys2[] = 'dat-' . $model->date;
				}
			}

			if( array_intersect(array('date_end', 'user_id', 'location_id'), array_keys($changes)) ){
				if( array_key_exists('user_id', $changes) ){
					$refresh_keys3[] = $changes['user_id'] ? 'use-' . $changes['user_id'] : 'use-0';
				}
				else {
					$refresh_keys3[] = $model->user_id ? 'use-' . $model->user_id : 'use-0';
				}

				if( array_key_exists('location_id', $changes) ){
					$refresh_keys3[] = $changes['location_id'] ? 'loc-' . $changes['location_id'] : 'loc-0';
				}
				else {
					$refresh_keys3[] = $model->location_id ? 'loc-' . $model->location_id : 'loc-0';
				}

				if( array_key_exists('date_end', $changes) ){
					$refresh_keys3[] = $changes['date_end'] ? 'dat-' . $changes['date_end'] : 'dat-0';
				}
				else {
					$refresh_keys3[] = 'dat-' . $model->date_end;
				}
			}
		}

		if( $refresh_keys2 ){
			$parent_refresh2 = HC_Lib::get_combinations($refresh_keys2);
			$parent_refresh = array_merge( $parent_refresh, $parent_refresh2 );
		}

		if( $refresh_keys3 ){
			$parent_refresh3 = HC_Lib::get_combinations($refresh_keys3);
			$parent_refresh = array_merge( $parent_refresh, $parent_refresh3 );
		}

		$final_parent_refresh = array();
		foreach( $parent_refresh as $pr ){
			$final_parent_refresh[ join('-', $pr) ] = 1;
		}

//		$return = array_keys( $final_parent_refresh );
		$return = $final_parent_refresh;
		return $return;
	}
}