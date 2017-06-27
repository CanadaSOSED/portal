<?php
/* default decorator making use of hc- utility css classes */
class HC_Html_Element_Styler_Theme
{
	function btn_success( $el )
	{
		$el
			->add_style('btn')
			->add_style('btn-submit')
			->add_style('color', 'white')
			->add_style('bg-color', 'green')
			->add_style('border-color', 'green')
		;
		return $el;
	}

	function btn_primary( $el )
	{
		$el
			->add_style('btn')
			->add_style('btn-submit')
			->add_style('color', 'white')
			->add_style('bg-color', 'blue')
			;
		return $el;
	}

	function btn_secondary( $el )
	{
		$el
			->add_style('btn')
			->add_style('btn-submit')
			->add_style('bg-color', 'silver')
			;
		return $el;
	}

	function btn_danger( $el )
	{
		$el
			->add_style('btn')
			->add_style('btn-submit')
			// ->add_style('color', 'white')
			// ->add_style('bg-color', 'red')
			->add_style('border-color', 'red')
			->add_style('color', 'red')
			;
		return $el;
	}

	function badge( $el )
	{
		$el
			->add_style('padding', 'x2', 'y1')
			->add_style('display', 'inline-block')
			->add_style('rounded')
			->add_style('nowrap')

			->add_style('color', 'white')
			->add_style('bg-color', 'gray')
			;
		return $el;
	}

	function label( $el )
	{
		$el
			->add_style('padding', 'x2', 'y1')
			->add_style('rounded')
			// ->add_style('nowrap')

			->add_style('color', 'white')
			->add_style('bg-color', 'gray')
			;
		return $el;
	}
}
?>