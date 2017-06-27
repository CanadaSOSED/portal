<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Form extends HC_Html_Widget_Container
{
	private $method = 'post';
	// private $id = '';

	function __construct()
	{
		parent::__construct();
		$this->id = 'nts_' . hc_random();
	}

	function id()
	{
		return $this->id;
	}
	function set_method( $method )
	{
		$this->method = $method;
		return $this;
	}
	function method()
	{
		return $this->method;
	}

	function render()
	{
		list( $csrf_name, $csrf_value ) = HC_App::csrf();

		$out = HC_Html_Factory::element('form')
			->add_attr('method', $this->method())
			->add_attr('accept-charset', 'utf-8')
			->add_attr('id', $this->id())
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		/* add csrf */
		if( $this->method() != 'get' ){
			if( strlen($csrf_name) && strlen($csrf_value) ){
				$hidden = HC_Html_Factory::input( 'hidden', $csrf_name )
					->set_value($csrf_value)
					;
				$hidden_contaner = HC_Html_Factory::element('div')
					->add_attr('style', 'display:none')
					->add_child($hidden)
					;

				$ri = HC_Lib::ri();
				if( $ri == 'wordpress' ){
					$hidden2 = HC_Html_Factory::input( 'hidden', 'hc_home_url' )
						->set_value( get_permalink() )
						;
					$hidden_contaner
						->add_child($hidden2)
						;
				}

				$out->add_child( $hidden_contaner );
			}
		}

		$out->add_child( parent::render() );
		return $out->render();
	}
}
?>