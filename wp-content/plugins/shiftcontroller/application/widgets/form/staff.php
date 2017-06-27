<?php
class SFT_Form_Input_Staff extends HC_Form_Input_Select
{
	function render()
	{
		$model = HC_App::model('user');
		$model->get();

		$options = array();
		foreach( $model as $obj )
		{
			$options[ $obj->id ] = $obj->present_title();
		}
		$this->set_options( $options );

		return parent::render();
	}
}
?>