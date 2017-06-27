<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Table extends HC_Html_Widget_Container
{
	protected $header = array();
	protected $rows = array();
	protected $row_attr = array();
	protected $cell_attr = array();
	protected $engine = 'table'; // or div, or div2, or grid

	public function set_cell( $rid, $cid, $value )
	{
		$this->rows[ $rid ][ $cid ] = $value;
		return $this;
	}

	public function set_engine( $engine )
	{
		$this->engine = $engine;
		return $this;
	}
	public function engine()
	{
		return $this->engine;
	}

	function add_row($row)
	{
		$this->rows[] = $row;
	}
	function rows()
	{
		return $this->rows;
	}

	public function add_row_attr( $rid, $attr ){
		if( ! isset($this->row_attr[$rid]) ){
			$this->row_attr[$rid] = array();
		}
		$this->row_attr[$rid] = array_merge( $this->row_attr[$rid], $attr );
		return $this;
	}

	public function add_cell_attr( $rid, $cid, $attr ){
		if( ! isset($this->cell_attr[$rid]) ){
			$this->cell_attr[$rid] = array();
		}
		if( ! isset($this->cell_attr[$rid][$cid]) ){
			$this->cell_attr[$rid][$cid] = HC_Html_Factory::element('a');;
		}

		foreach( $attr as $k => $v ){
			$this->cell_attr[$rid][$cid]->add_attr( $k, $v );
		}
		return $this;
	}

	public function row_attr( $rid )
	{
		$return = isset($this->row_attr[$rid]) ? $this->row_attr[$rid] : array();
		return $return;
	}

	public function cell_attr( $rid, $cid )
	{
		if( ! isset($this->cell_attr[$rid][$cid]) ){
			$this->cell_attr[$rid][$cid] = HC_Html_Factory::element('a');
		}
		return $this->cell_attr[$rid][$cid]->attr();
	}

	function set_header( $header )
	{
		$this->header = $header;
		return $this;
	}
	function header()
	{
		return $this->header;
	}

	protected function _get_table()
	{
		$engine = $this->engine();
		switch( $engine ){
			case 'table':
				$return = HC_Html_Factory::element( 'table' );
				break;
		}
		return $return;
	}
	protected function _get_tr()
	{
		$engine = $this->engine();
		switch( $engine ){
			case 'table':
				$return = HC_Html_Factory::element('tr');
				break;
		}
		return $return;
	}
	protected function _get_td()
	{
		$engine = $this->engine();
		switch( $engine ){
			case 'table':
				$return = HC_Html_Factory::element('td');
				break;
		}

		$children_attr = $this->children_attr();
		foreach( $children_attr as $k => $v ){
			$return->add_attr( $k, $v );
		}
		$children_styles = $this->children_styles();
		foreach( $children_styles as $k => $v ){
			$pass_arg = array_merge(array($k), $v);
			call_user_func_array( array($return, 'add_style'), $pass_arg );
		}

		return $return;
	}

	function render()
	{
		$out = $this->_get_table();

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$header = $this->header();
		if( $header ){
			$tr = HC_Html_Factory::element('tr');
			foreach( $header as $r ){
				$td = HC_Html_Factory::element('th');
				$td->add_child( $r );
				$tr->add_child( $td );
				}
			$out->add_child( $tr );
		}

		$rows = $this->rows();
		foreach( array_keys($rows) as $rid ){
			$row = $rows[$rid];
			$tr = $this->_get_tr();

			$attr = $this->row_attr( $rid );
			foreach( $attr as $k => $v ){
				$tr->add_attr( $k, $v );
			}

			foreach( array_keys($row) as $cid ){
				$cell = $row[$cid];

				$td = $this->_get_td();
				$td->add_child( $cell );

				$attr = $this->cell_attr( $rid, $cid );
				foreach( $attr as $k => $v ){
					$td->add_attr( $k, $v );
				}
				$tr->add_child( $td );
			}
			$out->add_child( $tr );
		}
		return $out->render();
	}
}
?>