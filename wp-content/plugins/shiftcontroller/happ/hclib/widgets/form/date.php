<?php
class HC_Form_Input_Date extends HC_Form_Input_Text
{
	protected $options = array();

	function add_option( $k, $v )
	{
		$this->options[$k] = $v;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$name = $this->name();
		$value = $this->value();
		$id = 'nts-' . $name;

		$t = HC_Lib::time();
		$value ? $t->setDateDb( $value ) : $t->setNow();
		$value = $t->formatDate_Db();

		$out = HC_Html_Factory::widget('container');

	/* hidden field to store our value */
		$hidden = HC_Html_Factory::input('hidden')
			->set_name( $name )
			->set_value( $value )
			->set_id($id)
			;
		$out->add_child( $hidden );

	/* text field to display */
		$display_name = $name . '_display';
		$display_id = 'nts-' . $display_name;
		$datepicker_format = $t->formatToDatepicker();
		$display_value = $t->formatDate();

		$text = HC_Html_Factory::input('text')
			->set_name( $display_name )
			->set_value( $display_value )
			->set_id($display_id)
			->add_attr('data-date-format', $datepicker_format)
			->add_attr('data-date-week-start', $t->weekStartsOn)
			// ->add_attr( 'style', 'width: 8em' )
			->add_attr( 'style', 'width: 100%;' )
			->add_attr( 'class', 'hc-datepicker' )
			->add_attr( 'readonly', 'readonly' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$text->add_attr( $k, $v );
		}

		$out->add_child( $text );

		$return = $this->decorate( $out->render() );
		return $return;
	}
}
