<?php
class HC_Html_Element_Styler_Theme_Rewrite
{
	function btn_success( $el )
	{
		$el
			->set_skip_css_pref(1)
			->add_attr('class', 'page-title-action')
			->set_skip_css_pref(0)
			->add_style('padding', 'x2', 'y1')
			;
		return $el;
	}

	function btn_primary( $el )
	{
		$el
			->set_skip_css_pref(1)
			->add_attr('class', 'button')
			->add_attr('class', 'button-primary')
			->set_skip_css_pref(0)

			// ->add_style('padding', 1)
			// ->add_style('padding', 'x2', 'y1')
			;
		return $el;
	}

	function btn_secondary( $el )
	{
		$el
			->set_skip_css_pref(1)
			->add_attr('class', 'button')
			->add_attr('class', 'button-secondary')
			->set_skip_css_pref(0)
			;
		return $el;
	}

	function btn_danger( $el )
	{
		$el
			->add_style('btn')
			->add_style('btn-submit', 1)
			->add_style('border-color', 'red')
			->add_style('color', 'red')
			;
		return $el;
	}
}
?>