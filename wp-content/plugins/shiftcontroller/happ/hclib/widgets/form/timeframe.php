<?php
class HC_Form_Input_Timeframe extends HC_Form_Input_Composite
{
	function __construct( $name )
	{
		parent::__construct();

		$this->fields['start'] = HC_Html_Factory::input( 'time', $name . '_start' );
		$this->fields['end'] = HC_Html_Factory::input( 'time', $name . '_end' );
	}

	function render()
	{
		$wrap = HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r1')
			->add_style('nowrap')
			;
		$wrap->add_child( 
			$this->fields['start']
			);
		$wrap->add_child( '-' );
		$wrap->add_child( 
			$this->fields['end']
			);

		return $this->decorate( $wrap->render() );
	}
}