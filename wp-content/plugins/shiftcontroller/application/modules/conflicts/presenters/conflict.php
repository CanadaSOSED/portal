<?php
class Conflict_HC_Presenter extends HC_Presenter
{
	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$icon = HC_Html::icon(HC_App::icon_for('conflict'));
				$icon->add_style('color', 'red');

				$span = HC_Html_Factory::element('span')
					->add_attr('title', $model->present_title(HC_PRESENTER::VIEW_TEXT))
					->add_child($icon)
					->add_child($model->present_title(HC_PRESENTER::VIEW_TEXT))
					;
				$return[] = $span;
				break;

			case HC_PRESENTER::VIEW_TEXT:
				$return[] = HCM::__('Conflict');
				$return[] = ': ';
				$return[] = $model->type;
				$return[] = ': ';
				$return[] = $model->details;
				break;

			case HC_PRESENTER::VIEW_RAW:
				$return[] = $model->type;
				$return[] = ': ';
				$return[] = $model->details;
				break;
		}

		$return = join( '', $return );
		return $return;
	}

	private function _status_details( $model )
	{
		$return = NULL;
		$status = $model->status;
		$details = array(
		/* translators: status */
			$model->_const('STATUS_PENDING') 	=> array( HCM::__('Pending'),	'warning' ),
		/* translators: status */
			$model->_const('STATUS_ACTIVE')		=> array( HCM::__('Active'),	'success' ),
		/* translators: status */
			$model->_const('STATUS_CANCELLED')	=> array( HCM::__('Cancelled'),	'archive' ),
			);
		if( isset($details[$status]) ){
			$return = $details[$status];
		}
		return $return;
	}
}