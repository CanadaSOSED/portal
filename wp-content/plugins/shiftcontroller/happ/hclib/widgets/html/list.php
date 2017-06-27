<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_List extends HC_Html_Widget_Container
{
	function render()
	{
		$items = $this->children();

		$out = HC_Html_Factory::element( 'ul' );

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out
				->set_skip_css_pref(1)
				->add_attr( $k, $v )
				->set_skip_css_pref(0)
				;
		}

		$already_shown = 0;

		foreach( $items as $key => $item ){
			$li = HC_Html_Factory::element('li');

			$children_attr = $this->children_attr();
			foreach( $children_attr as $k => $v ){
				$li->add_attr( $k, $v );
			}
			$child_attr = $this->child_attr($key);
			foreach( $child_attr as $k => $v ){
				$li->add_attr( $k, $v );
			}

			$children_styles = $this->children_styles();
			foreach( $children_styles as $k => $v ){
				$pass_arg = array_merge(array($k), $v);
				call_user_func_array( array($li, 'add_style'), $pass_arg );
			}

			$child_styles = $this->child_styles($key);
			foreach( $child_styles as $k => $v ){
				$li->add_style( $k, $v );
			}

			$li->add_child( $item );
			$out->add_child( $li );

			$already_shown++;
		}

		return $out->render();
	}
}
?>