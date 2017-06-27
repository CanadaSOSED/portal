<?php
include_once( dirname(__FILE__) . '/list.php' );
// class HC_Html_Widget_Button_Group extends HC_Html_Element
class HC_Html_Widget_Button_Group extends HC_Html_Widget_List
{
	protected $active = NULL;

	function set_active( $active )
	{
		$this->active = $active;
	}
	function active()
	{
		return $this->active;
	}

	function render()
	{
	/* for every child add btn */
		$children = $this->children();
		foreach( array_keys($children) as $k ){
			$children[$k]
				->add_style('btn')
				;
		}
		$this->set_children( $children );

		$this
			->add_style('border')
			->add_style('rounded')
			->add_style('display', 'inline-block')
			->add_children_style('display', 'inline-block')
			// ->add_children_style('padding', 1)
			// ->add_children_style('border', 'left')
			;

		$this->add_child_style($this->active(), 'bg-color', 'silver');

		$return = parent::render();
		return $return;
	}
}
?>