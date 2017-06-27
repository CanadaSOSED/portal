<?php
class Html_Icon_HC_Presenter
{
	private $convert = array();

	public function __construct()
	{
		$app_conf = HC_App::app_conf();
		$this->convert = $app_conf->conf('icons');
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new Html_Icon_HC_Presenter;
		}
		return $instance;
	}

	public function icon( $icon, $inside = '' ){
		// <span class="oi oi-icon-name" title="icon name" aria-hidden="true"></span>
		$icon = isset($this->convert[$icon]) ? $this->convert[$icon] : $icon;

		$return = HC_Html_Factory::element('i');
		$return
			->add_attr('class', array('icomoon'))
			;

		if( strlen($icon) ){
			$return
				->add_attr('class', array('icomoon-' . $icon))
				;
		}
		elseif( strlen($inside) ){
			$return
				->add_child($inside)
				;
		}

		return $return;
	}
}