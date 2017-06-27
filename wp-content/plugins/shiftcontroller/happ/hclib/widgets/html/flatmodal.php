<?php
class HC_Html_Widget_Flatmodal extends HC_Html_Element
{
	protected $closer = '';
	protected $content = '';

	function set_closer( $closer )
	{
		$this->closer = $closer;
		return $this;
	}
	function closer()
	{
		return $this->closer;
	}

	function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	function content()
	{
		return $this->content;
	}

	function render()
	{
		$closer = $this->closer();

		if( ! $closer ){
			$closer = HC_Html_Factory::element('a')
				->add_child(
					HC_Html::icon('times')
					)
				->add_attr('title', HCM::__('Back'))
				->add_style('closer')
				->add_style('padding', 2)

				// ->add_attr('style', 'border: red 1px solid;')
				->add_attr('style', 'position: absolute; right: 0; top: 0;')
				;
		}

		$closer
			->add_attr('class', array('hcj-flatmodal-closer'))
			->add_attr('href', '#')
			->add_attr('style', 'display: none;')
			// ->add_attr('style', 'margin-bottom: 1em;')
			;

		$container = HC_Html_Factory::element('div')
			->add_attr('class', array('hcj-flatmodal-container', 'hcj-ajax-container'))
			->add_attr('style', 'display: none;')

			->add_style('border')
			->add_style('rounded')
			->add_style('padding', 3)
			;

		$out = HC_Html_Factory::element('div')
			->add_attr('style', 'position: relative;')
			->add_attr('class', 'hcj-flatmodal-parent')
			->add_child( $closer )
			->add_child( $container )
			->add_child( $this->content() )
			;
		$return = $out->render();

		return $return;
	}
}
?>