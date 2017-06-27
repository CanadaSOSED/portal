<?php
class HC_Form_Input_Time extends HC_Form_Input_Select
{
	function __construct( $name = '' )
	{
		parent::__construct( $name );

		$start_with = 0;
		$end_with = 24 * 60 * 60;

		$conf = HC_App::app_conf();
		$time_min = $conf->get('time_min');
		if( $time_min !== NULL ){
			$this->set_conf('min', $time_min);
		}
		else {
			$this->set_conf('min', 0);
		}

		$time_max = $conf->get('time_max');
		if( $time_max !== NULL ){
			$this->set_conf('max', $time_max);
		}
		else {
			$this->set_conf('max', 24*60*60);
		}
	}

	function build_options()
	{
		$start_with = $this->conf('min');
		$end_with = $this->conf('max');

		if( $end_with < $start_with ){
			$end_with = $start_with;
		}

		$step = 15 * 60;
		$options = array();

		$t = HC_Lib::time();
		$t->setDateDb( 20130118 );

/*
		if( $value && ($value > $end_with) )
		{
			$value = $value - 24 * 60 * 60;
		}
*/
		if( $start_with )
			$t->modify( '+' . $start_with . ' seconds' );

		$no_of_steps = ( $end_with - $start_with) / $step;
		for( $ii = 0; $ii <= $no_of_steps; $ii++ ){
			$sec = $start_with + $ii * $step;
			$options[ $sec ] = $t->formatTime();
			$t->modify( '+' . $step . ' seconds' );
		}

		$this->set_options( $options );
	}

	function render()
	{
		$this->build_options();
		return parent::render();
	}
	
}