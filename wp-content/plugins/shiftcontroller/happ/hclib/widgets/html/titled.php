<?php
class HC_Html_Widget_Titled extends HC_Html_Element
{
	function __construct( $element = 'span' )
	{
		parent::__construct($element);
	}

	function init( $tag = NULL )
	{
		if( $tag ){
			$this->set_tag( $tag );
		}
	}

	function render()
	{
		$already_title = $this->attr('title');
		if( ! $already_title ){
			$children_return = $this->_prepare_children();
			$this->add_attr('title', $children_return);
		}
		return parent::render();
	}
}