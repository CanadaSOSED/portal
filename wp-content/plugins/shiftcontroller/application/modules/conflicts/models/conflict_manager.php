<?php
class Conflict_Manager_HC_Model extends MY_Model_Virtual
{
	protected $types = array();

	public function __construct()
	{
		$this->add_type( 'overlap', HC_App::model('conflict_overlap') );
		// $this->add_type( 'week_limit', HC_App::model('conflict_week_limit') );
	}

	public function add_type( $key, $type )
	{
		$this->types[$key] = $type;
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$my_class = get_class();
			$instance = new $my_class();
		}
		return $instance;
	}

	function get( $model, $just_yes_or_no = FALSE )
	{
		$return = array();
		reset( $this->types );
		foreach( $this->types as $type ){
			$this_return = $type->get( $model );
			$return = array_merge( $return, $this_return );
			if( $just_yes_or_no && $return ){
				break;
			}
		}
		return $return;
	}
}