<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Tabs extends HC_Html_Element
{
	protected $tabs = array();
	protected $active = NULL;
	protected $id = NULL;

	function __construct()
	{
		parent::__construct();
		$id = 'nts' . hc_random();
		$this->set_id( $id );
	}

	function set_id($id)
	{
		$this->id = $id;
	}
	function id()
	{
		return $this->id;
	}

	function set_active( $active )
	{
		$this->active = $active;
	}
	function active()
	{
		$return = NULL;
		if( $this->active ){
			$return = $this->active;
		}
		elseif( count($this->tabs) ){
			$tabs = array_keys($this->tabs);
			$return = $tabs[0];
		}
		return $return;
	}

	function add_tab( $key, $label, $content )
	{
		$this->tabs[ $key ] = array( $label, $content );
	}
	function tabs()
	{
		return $this->tabs;
	}

	function render_content()
	{
		$active = $this->active();
		$my_tabs = $this->tabs();

		$content = HC_Html_Factory::element('div')
			->add_attr('class', 'hcj-tab-content')
			->add_attr('style', 'overflow: visible;')
			;
		reset( $my_tabs );
		foreach( $my_tabs as $key => $tab_array ){
			list( $tab_label, $tab_content ) = $tab_array;
			$tab = HC_Html_Factory::element('div')
				->add_attr('class', 'hcj-tab-pane')
				->add_attr('id', $key)
				->add_attr('data-tab-id', $key)
				;
			if( $active == $key ){
				$tab->add_attr('class', 'hc-active');
			}
			$tab->add_child( $tab_content );
			$content->add_child( $tab );
		}
		return $content;
	}

	function render()
	{
		$return = '';
		$my_tabs = $this->tabs();

		if( count($my_tabs) == 1 ){
			foreach( $my_tabs as $key => $tab_array ){
				list( $tab_label, $tab_content ) = $tab_array;
				$return = $tab_content;
				break;
			}
			return $return;
		}

	/* tabs */
		$id = $this->id();

		$tabs = HC_Html_Factory::widget('list')
			->add_attr('id', $id) 
			->add_attr('class', array('hcj-tab-links'))

			->add_style('margin', 'b1')
			->add_style('padding', 'y2', 'x1')
			// ->add_style('border', 'bottom')

			->add_children_style('inline')
			// ->add_children_style('margin', 'r1')
			;

		$active = $this->active();
		reset( $my_tabs );
		foreach( $my_tabs as $key => $tab_array ){
			list( $tab_label, $tab_content ) = $tab_array;
			if( ! is_object($tab_label) ){
				$tab_label = HC_Html_Factory::widget('titled', 'a')
					->add_child( $tab_label )
					;
			}

			$tab_label
				->add_attr('href', '#' . $key)
				->add_attr('class', 'hcj-tab-toggler')
				->add_attr('data-toggle-tab', $key)

				->add_style('btn')
				// ->add_style('margin', 'r1')
				->add_style('margin', 0)
				->add_style('padding', 'x3', 'y2')
				;

			$tab_label->add_style('border', 'bottom');
			if( $active == $key ){
				$tab_label->add_attr('class', 'hc-active');
/*
				$tab_label->add_style('border', 'left');
				$tab_label->add_style('border', 'top');
				$tab_label->add_style('border', 'right');
				$tab_label->add_style('rounded');
*/
			}
			else {
				// $tab_label->add_style('border', 'bottom');
			}

			$tabs->add_child( $key, $tab_label );
		}

	/* content */
		$content = $this->render_content();

	/* out */
		$out = HC_Html_Factory::widget('list')
			->add_attr('class', array('hcj-tabs'))
			;
		$out->add_child( $tabs );
		$out->add_child( $content );

		return $out->render();
	}
}
?>