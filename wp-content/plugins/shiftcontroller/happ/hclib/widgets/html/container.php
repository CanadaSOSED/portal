<?php
class HC_Html_Widget_Container extends HC_Html_Element
{
	function render()
	{
		$out = '';

		$children_attr = $this->children_attr();
		$items = $this->children();
		foreach( $items as $item ){
			if( is_object($item) ){
				reset( $children_attr );
				foreach( $children_attr as $k => $v ){
					$item->add_attr( $k, $v );
				}
				$out .= $item->render();
			}
			else {
				$out .= $item;
			}
		}
		return $out;
	}
}
?>