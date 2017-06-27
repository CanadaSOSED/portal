<?php
class Location_HC_Presenter extends HC_Presenter
{
	public function color( $model )
	{
		if( $model->id ){
			if( $model->color ){
				$return = $model->color;
			}
			else {
				$min_brightness = 180;
				$return = Hc_lib::random_html_color( $model->id, $min_brightness );
			}
		}
		else {
			$return = '#ddd';
		}
		return $return;
	}

	function label( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = '';
		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
			case HC_PRESENTER::VIEW_HTML_ICON:
				$return = HC_Html::icon(HC_App::icon_for('location'));
				$color = $model->present_color();
				$color = HC_Lib::adjust_color_brightness( $color, -64 );
				$return->add_attr('style', 'color: ' . $color . ';');
				$return->add_attr('title', $model->present_title(HC_PRESENTER::VIEW_RAW));
				$return = NULL;

				break;
			case HC_PRESENTER::VIEW_TEXT:
				$return = HCM::__('Location');
				break;
		}

		return $return;
	}

	function title( $model, $vlevel = HC_PRESENTER::VIEW_HTML )
	{
		$return = array();
		$label = $this->label( $model, $vlevel );
		if( strlen($label) ){
			$return[] = $label;
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_TEXT:
				$return[] = ': ';
				break;
		}

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML_ICON:
				break;
			default:
				if( $model->exists() ){
					$return[] = $model->name;
				}
				else {
					$return[] = HCM::__('Unknown');
				}
				break;
		}

		$return = join( '', $return );

		switch( $vlevel ){
			case HC_PRESENTER::VIEW_HTML:
				$color = $model->present_color();
				$color = HC_Lib::adjust_color_brightness( $color, -64 );

				$return = HC_Html_Factory::element('span')
					->add_style('nowrap')
					->add_child(
						HC_Html::icon(HC_App::icon_for('location'))
							->add_attr('style', 'color: ' . $color . ';')
						)
					->add_child( $return )
					;
				break;
		}
		return $return;
	}
}