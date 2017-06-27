<?php
class HC_Html_Widget_Grid extends HC_Html_Element
{
	protected $scale = 'sm'; // can be xs, sm, md, lg
	protected $gutter = 0; // from 0 to 4
	protected $right = array();

	function add_child( $child, $child_value = NULL )
	{
		$args = func_get_args();
		if( count($args) == 3 ){
			list( $item, $item_value, $width ) = $args;
			if( is_array($width) ){
				list( $width, $offset ) = $width;
			}
			else {
				$offset = 0;
			}
			$this->children[$item] = array( $item_value, $width, $offset );
		}
		elseif( count($args) == 2 ){
			list( $item_value, $width ) = $args;
			if( is_array($width) ){
				list( $width, $offset ) = $width;
			}
			else {
				$offset = 0;
			}
			$this->children[] = array( $item_value, $width, $offset );
		}
		return $this;
	}

	function scale()
	{
		return $this->scale;
	}
	function set_scale( $scale )
	{
		$this->scale = $scale;
		return $this;
	}

/* from 0 to 3 */
	function set_gutter( $gutter )
	{
		$this->gutter = $gutter;
		return $this;
	}
	function gutter()
	{
		return $this->gutter;
	}

	function set_child_width( $child, $width )
	{
		if( isset($this->children[$child]) ){
			$this->children[$child][1] = $width;
		}
		return $this;
	}

	function set_child_right( $child, $right = 1 )
	{
		$this->right[$child] = $right;
	}

	function render()
	{
		$out = HC_Html_Factory::element('div');
		$gutter = $this->gutter();

		$out
			->add_style('grid', $gutter)
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$scale = $this->scale();
		$items = $this->children();

		foreach( $items as $key => $item ){
			list( $item, $width, $offset ) = $item;
			$right = isset($this->right[$key]) ? $this->right[$key] : 0;

			$slot = HC_Html_Factory::element('div')
				->add_style('col', $scale, $width, $offset, $gutter, $right)
				;

			$child_attr = $this->child_attr($key);
			foreach( $child_attr as $k => $v ){
				$slot->add_attr( $k, $v );
			}
			$child_styles = $this->child_styles($key);
			foreach( $child_styles as $k => $v ){
				$slot->add_style( $k, $v );
			}

			$slot->add_child( $item );

			$out->add_child( $slot );
		}
		return $out->render();
	}
}
?>