<?php
class HC_Html_Widget_Module extends HC_Html_Element
{
	private $self_target = TRUE;

	private $url = '';
	private $params = array();
	private $pass_params = array();
	private $args = array();
	private $content = NULL;
	private $skip_src = FALSE;
	private $show_empty = FALSE;

	function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	function content()
	{
		return $this->content;
	}

	function set_show_empty( $show_empty )
	{
		$this->show_empty = $show_empty;
		return $this;
	}
	function show_empty()
	{
		return $this->show_empty;
	}

	public function set_param( $param, $value )
	{
		$this->params[$param] = $value;
		return $this;
	}
	public function set_params( $params )
	{
		foreach( $params as $k => $v ){
			$this->set_param( $k, $v );
		}
		return $this;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		$return = isset($this->params[$key]) ? $this->params[$key] : '';
		return $return;
	}

	function set_skip_src( $skip_src )
	{
		$this->skip_src = $skip_src;
		return $this;
	}
	function skip_src()
	{
		return $this->skip_src;
	}

	function pass_arg( $arg )
	{
		$this->args[] = $arg;
		return $this;
	}
	function args()
	{
		return $this->args;
	}

	function pass_param( $param, $value )
	{
		$this->pass_params[$param] = $value;
		return $this;
	}
	function more_params()
	{
		return $this->pass_params;
	}

	function set_url( $url )
	{
		$this->url = $url;
		return $this;
	}
	function url()
	{
		return $this->url;
	}

	function set_self_target( $self_target = TRUE )
	{
		$this->self_target = $self_target;
		return $this;
	}
	function self_target()
	{
		return $this->self_target;
	}

	function render()
	{
		$module_params = array();
		$link_params = array();

		$module_params[] = $this->url();

		foreach( $this->args() as $k ){
			$module_params[] = $k;
			// $link_params[$k] = $v;
		}

		foreach( $this->params() as $k => $v ){
			$module_params[] = $k;
			$module_params[] = $v;
			$link_params[$k] = $v;
		}

		foreach( $this->more_params() as $k => $v ){
			$module_params[] = $k;
			$module_params[] = $v;
			$link_params[$k] = $v;
		}

		$link = HC_Lib::link( $this->url(), $link_params );

		$return = $this->content();
		if( $return === NULL ){
			$return = call_user_func_array( 'Modules::run', $module_params );
		}

		$show_empty = $this->show_empty();

		if( (strlen($return) && $this->self_target()) OR $show_empty OR (! $this->skip_src()) ){
			$out = HC_Html_Factory::element('div')
				->add_child(
					$return
					)
				;

			if( $this->self_target() ){
				$out
					->add_attr('class', 'hcj-target')
					;
			}

			if( ! $this->skip_src() ){
				$out
					->add_attr('data-src', $link->url())
					;

				if( 0 ){
					$out
						->add_child($link->url())
						;
				}
			}

			$attr = $this->attr();
			foreach( $attr as $k => $v ){
				$out->add_attr( $k, $v );
			}

			$return = $out->render();
		}
		return $return;
	}
}
?>