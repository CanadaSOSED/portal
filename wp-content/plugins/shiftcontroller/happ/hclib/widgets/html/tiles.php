<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Tiles extends HC_Html_Widget_Container
{
	protected $per_row = 4;

	function set_per_row( $per_row )
	{
		$this->per_row = $per_row;
		return $this;
	}
	function per_row()
	{
		return $this->per_row;
	}

	function render()
	{
		$out = array();
		$items = $this->children();
		$per_row = $this->per_row();
		$number_of_rows = ceil( count($items) / $per_row );

		$row_class = 'row';
		switch( $per_row ){
			case 1:
				$tile_width = 12;
				break;
			case 2:
				$tile_width = 6;
				break;
			case 3:
				$tile_width = 4;
				break;
			case 4:
				$tile_width = 3;
				break;
			case 6:
				$tile_width = 2;
				break;
		}

		for( $ri = 0; $ri < $number_of_rows; $ri++ ){
			$row = HC_Html_Factory::widget('grid')
				;

			for( $ii = ($ri*$per_row); $ii < (($ri+1)*$per_row); $ii++ ){
				if( isset($items[$ii]) ){
					$row->add_child(
						$items[$ii],
						$tile_width
						);
				}
			}
			$out[] = $row;
		}

		$return = '';
		foreach( $out as $o ){
			$return .= $o->render();
		}
		return $return;
	}
}
?>