<?php
class HC_Form_Input_Colorpicker extends HC_Form_Input
{
	protected $colors = array();
	
	function __construct( $name = '' )
	{
		parent::__construct( $name );
		$this->colors = array(
			'#ffb3a7',	// 1
			'#cbe86b',	// 2
			'#89c4f4',	// 3
			'#f5d76e',	// 4
			'#be90d4',	// 5
			'#fcf13a',	// 6
			'#ffffbb',	// 7
			'#ffbbff',	// 8
			'#87d37c',	// 9
			'#ff8000',	// 12
			'#73faa9',	// 13
			'#c8e9fc',	// 14
			'#cb9987',	// 15
			'#cfd8dc',	// 16
			'#99bb99',	// 17
			'#99bbbb',	// 18
			'#bbbbff',	// 19
			'#dcedc8',	// 20
			);
	}

	function render()
	{
		$value = $this->value();
		$name = $this->name();

		$hidden = HC_Html_Factory::input('hidden')
			->set_name( $name )
			->set_value( $value )
			->add_attr('class', 'hcj-color-picker-value')
			;

		$title = HC_Html_Factory::element('a')
			->add_child('&nbsp;')
			->add_style('btn')
			->add_style('border')
			->add_style('padding', 1)
			->add_attr('style', 'background-color: ' . $value . ';')
			->add_attr('style', 'width: 2em;')
			->add_attr('class', 'hcj-color-picker-display')
			;

		$options = HC_Html_Factory::widget('list')
			->add_style('margin', 't2')
			->add_style('padding', 'y2')
			->add_style('border', 'top')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r1', 'b1')
			;

		foreach( $this->colors as $color ){
			$option = HC_Html_Factory::element('a')
				->add_child('&nbsp;')
				->add_style('btn')
				->add_style('border')
				->add_style('padding', 1)
				->add_attr('style', 'background-color: ' . $color . ';')
				->add_attr('style', 'width: 2em;')
				->add_attr('data-color', $color)
				->add_attr('class', 'hcj-color-picker-selector')
				->add_attr('class', 'hcj-collapse-closer')
				;
			$options->add_child( $option );
		}

		$display = HC_Html_Factory::widget('collapse')
			->set_title( $title )
			->set_content( $options )
			;

		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'hcj-color-picker')
			->add_child( $hidden )
			->add_child( $display )
			;

		return $out->render();
	}
	
}