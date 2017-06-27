<?php
class HC_Html_Widget_Label_Row extends HC_Html_Element
{
	protected $label = NULL;
	protected $content = array();
	protected $content_static = FALSE;
	protected $error = FALSE;

	function set_error( $error )
	{
		$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}
	function set_label( $label )
	{
		$this->label = $label;
		return $this;
	}
	function label()
	{
		return $this->label;
	}
	function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	function content()
	{
		$return = $this->content;
		if( ! is_array($return) )
		{
			$return = array( $return );
		}
		return $return;
	}

	function set_content_static( $content_static = TRUE )
	{
		$this->content_static = $content_static;
		return $this;
	}
	function content_static()
	{
		return $this->content_static;
	}

	function render()
	{
		$error = $this->error();
		$label = $this->label();
		$content = $this->content();

		$div = HC_Html_Factory::widget('grid')
			->add_style('margin', 'b2')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$div->add_attr( $k, $v );
		}

		$label_c = '';
		if( $label ){
			$label_c = HC_Html_Factory::element( 'label' )
				->add_child( $label )
				->add_attr('class', 'hc-form-label')
				;
		}

		$div->add_child(
			'label',
			$label_c,
			2
			);

		$content_holder = HC_Html_Factory::element('div')
			->add_style('display', 'block')
			;
		if( $this->content_static() ){
			$content_holder->add_attr('class', 'hc-form-control-static');
		}
		if( $error ){
			$content_holder->add_style('form-error');
		}

		foreach( $content as $cont ){
			$content_holder->add_child( $cont );
		}

		$div->add_child(
			'content',
			$content_holder,
			8
			);

		$div->add_child_style('label', 'text-align', 'sm-right'); 
		// $div->add_child_style('label', 'font-style', 'bold'); 
		$div->add_child_style('label', 'margin', 'r3'); 

//		$div->add_child_attr('label', 'style', 'border: blue 1px solid;'); 
//		$div->add_child_style('content', 'style', 'border: blue 1px solid;'); 

		return $div->render();
	}
}
?>