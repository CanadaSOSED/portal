<?php
include_once( dirname(__FILE__) . '/list.php' );
class HC_Html_Widget_Dropdown extends HC_Html_Widget_List
{
	protected $title = NULL;
	protected $no_caret = FALSE;
	protected $wrap = TRUE;
	private $active = NULL;

	function set_active( $active )
	{
		$this->active = $active;
		return $this;
	}
	function active()
	{
		return $this->active;
	}

	function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	function title()
	{
		return $this->title;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_wrap( $wrap = TRUE )
	{
		$this->wrap = $wrap;
		return $this;
	}
	function wrap()
	{
		return $this->wrap;
	}

	function render()
	{
		$out = array();

	/* build trigger */
		$title = $this->title();
		if( 
			( $active = $this->active() ) && $this->child($active)
			){
			$title = $this->child($active);
			$this->remove_child( $active );
		}
	
		if( 
			is_object($title) &&
			( $title->tag() == 'a' )
			){
			$trigger = $title;
		}
		else {
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = HC_Html_Factory::element('a')
				->add_attr('title', $title)
					->add_child( 
						$full_title
						)
				;
		}

		$trigger
			->add_attr('href', '#')
			->add_attr('class', 'hcj-dropdown-toggle')
			;

		if( ! $this->no_caret() ){
			$trigger
				->add_child(
					HC_Html::icon('caret-down')
					)
				;
		}

		$out[] = $trigger->render();

		$this->add_attr('class', 'hcj-dropdown-menu');
		$out[] = parent::render();

		$return = '';
		foreach( $out as $o ){
			$return .= $o;
		}

		if( $this->wrap() ){
			$wrap = HC_Html_Factory::element('div')
				->add_attr('class', 'hcj-dropdown')
				->add_child( $return )
				;
			$return = $wrap->render();
		}
		return $return;
	}
}
?>