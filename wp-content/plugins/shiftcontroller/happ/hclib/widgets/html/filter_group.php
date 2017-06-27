<?php
include_once( dirname(__FILE__) . '/collapse.php' );

class HC_Html_Widget_Filter_Group extends HC_Html_Widget_Collapse
{
	private $filters = array();
	private $subtitle = NULL;

	function set_subtitle( $subtitle )
	{
		$this->subtitle = $subtitle;
		return $this;
	}
	function subtitle()
	{
		return $this->subtitle;
	}

	function add_filter( $handle, $filter )
	{
		$filter->set_inside( TRUE );
		$this->filters[ $handle ] = $filter;
		return $this;
	}
	function filters()
	{
		return $this->filters;
	}

	function selected()
	{
		$return = array();
		$filters = $this->filters();
		foreach( $filters as $f ){
			$this_selected = $f->selected();
			if( $this_selected ){
				$return[] = $this_selected;
			}
		}
		return $return;
	}

	function render_trigger( $self = FALSE )
	{
		$filters = $this->filters();
		$panel = $this->panel();

		/* check if we need trigger */
		$show_trigger = FALSE;
		foreach( $filters as $f ){
			if( $f->remain_options() ){
				$show_trigger = TRUE;
				break;
			}
		}

		if( ! $show_trigger ){
			return;
		}

		if( $self ){
			$title = $this->title();
			$trigger = HC_Html_Factory::element('span')
				->add_child( $title )
				->add_style('padding', 2)
				->add_style('text-align', 'center')
				->add_style('btn')
				->add_style('rounded')
				->add_style('margin', 'r1')
				->add_style('bg-color', 'silver')
				->add_style('color', 'black')
				;
			$this->set_title( $trigger );
		}

		return parent::render_trigger( $self );
	}

	public function content()
	{
		$filters = $this->filters();
		$content = HC_Html_Factory::widget('container');
		$content = HC_Html_Factory::widget('list')
			->add_children_style('margin', 'b1')
			;

		$subtitle = $this->subtitle();
		if( $subtitle ){
			$content
				->add_child( $subtitle )
				;
		}

		reset( $filters );
		foreach( $filters as $fl ){
			if( $fl->remain_options() ){
				$fl->add_style('margin', 'b1');
				if( count($filters) > 1 ){
					$content->add_child( $fl );
				}
				else {
					$content->add_child( $fl->render_options() );
				}
			}
		}
		return $content->render();
	}

	function render_selected()
	{
		$out = HC_Html_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			;

		$filters = $this->filters();
		$panel = $this->panel();

		reset( $filters );
		foreach( $filters as $fl ){
			$child_selected = $fl->render_selected();
			$out->add_child( $child_selected );
		}
		
		return $out;
	}

	function render()
	{
		$filters = $this->filters();
		$panel = $this->panel();

		reset( $filters );
		foreach( $filters as $fl ){
			$child_selected = $fl->render_selected();
			if( strlen($child_selected) ){
				$this->add_more_title( $child_selected );
			}
		}

		return parent::render();
	}

}