<?php
class User_HC_Presenter extends HC_Presenter
{
	function level( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$text = array(
			USER_HC_MODEL::LEVEL_STAFF 		=> HCM::__('Staff'),
			USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
			);
		$return = isset($text[$model->level]) ? $text[$model->level] : 'N/A';
		return $return;
	}

	function status( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		list( $label_text, $label_color ) = $this->_status_details($model);

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$return = HC_Html_Factory::element('span')
					->add_child( $label_text )
					->add_style('badge')
					->add_style('bg-color', $label_color )
					;
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::__('Status') . ': ' . $label_text;
				break;
			case HC_PRESENTER::VIEW_RAW:
				$return = $label_text;
				break;
		}
		return $return;
	}

	function status_class( $model, $skip_type = FALSE )
	{
		$return = array();
		list( $label_text, $label_color ) = $this->_status_details($model, $skip_type);
		if( ! is_array($label_color) )
			$label_color = array( $label_color );
		$return = array_merge( $return, $label_color );
		return $return;
	}

	private function _status_details( $model )
	{
		$return = NULL;
		$status = $model->active;

		$details = array(
/* translators: status */
			USER_HC_MODEL::STATUS_ACTIVE 	=> array( HCM::__('Active'),	'olive' ),
/* translators: status */
			USER_HC_MODEL::STATUS_ARCHIVE	=> array( HCM::__('Archived'),	'gray' ),
			);

		if( isset($details[$status]) ){
			$return = $details[$status];
		}

		return $return;
	}

	function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = '';
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$ri = HC_Lib::ri();
				if( $ri == 'wordpress' ){
					// $avatar = get_avatar( $model->email, 16 );
					if( $model->id ){
						$avatar = get_avatar( $model->id, 16 );
						$return = HC_Html::icon( '', $avatar );
					}
					else {
						$return = HC_Html::icon(HC_App::icon_for('user'));
					}
				}
				else {
					$return = HC_Html::icon(HC_App::icon_for('user'));
					if( ! $model->exists() ){
						$return->add_style('color', 'orange');
					}
					else {
						if( ($model->id) && ($model->active != $model->_const('STATUS_ACTIVE')) ){
							$return = HC_Html::icon(HC_App::icon_for('user'))
								->add_style('mute')
								;
						}
					}
				}
				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::__('Staff');
				break;
		}
		return $return;
	}

	function sort_property( $model )
	{
		$return = array();
		if( strlen($model->last_name) ){
			$return[] = $model->last_name;
		}
		if( strlen($model->first_name) ){
			$return[] = $model->first_name;
		}
		$return = join( ' ', $return );
		return $return;
	}

	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();

		$label = $this->label( $model, $vlevel );
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_TEXT:
				$label .= ': ';
				break;
		}

		$title = '';
		if( $model->exists() ){
			$title = $model->first_name . ' ' . $model->last_name;
		}
		else{
			$title = '___';
		}

		$return[] = $label;
		$return[] = $title;

		$return = join( '', $return );

		if( ! $model->exists() ){
			switch( $vlevel ){
				case HC_PRESENTER::VIEW_HTML:
					$return = HC_Html_Factory::element('span')
						->add_attr('title', HCM::__('Open Shift'))
						->add_child($return)
						->add_style('color', 'orange')
						;
					break;
			}
		}

		return $return;
	}

	function title_misc( $model )
	{
		$title_misc = array();
		$title_misc[] = $model->present_status();
		$title_misc[] = 'id: ' . $model->id;
		$title_misc[] = $model->present_level();

		$out = HC_Html_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r1', 'b1')
			->add_children_attr('style', 'vertical-align: middle;')
			;
		foreach( $title_misc as $tm ){
			$out->add_child( $tm );
		}
		return $out;
	}
}