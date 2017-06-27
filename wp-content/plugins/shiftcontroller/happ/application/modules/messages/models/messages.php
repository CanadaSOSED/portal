<?php
class Messages_HC_Model
{
	private $engines = array();
	private $msgs = array();

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new Messages_HC_Model;
		}
		return $instance;
	}

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
	}

	public function add_engine( $tag, $engine )
	{
		$this->engines[$tag] = $engine;
	}

	public function remove_engine( $tag )
	{
		unset($this->engines[$tag]);
	}

	public function send( $key, $user, $payload = array() )
	{
		$uid = $user->id;
		if( ! isset($this->msgs[$uid])){
			$this->msgs[$uid] = array();
		}
		if( ! isset($this->msgs[$uid][$key])){
			$this->msgs[$uid][$key] = array();
		}

		$this->msgs[$uid][$key][] = $payload;
	}

/* it actually sends out messages, normally called in the post_controller hook */
	public function run()
	{
		reset( $this->engines );
		foreach( $this->engines as $engine ){
			$engine->run( $this->msgs );
		}
	}
}