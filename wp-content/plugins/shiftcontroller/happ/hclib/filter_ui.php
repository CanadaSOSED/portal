<?php
class HC_Filter_UI
{
	private $disabled = array();

	protected function __construct()
	{
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new HC_Filter_UI();
		}
		return $instance;
	}

	public function disable( $what )
	{
		$this->disabled[$what] = 1;
	}

	public function is_disabled( $what )
	{
		$return = FALSE;
		if( isset($this->disabled[$what]) && $this->disabled[$what] ){
			$return = TRUE;
		}
		return $return;
	}
}
