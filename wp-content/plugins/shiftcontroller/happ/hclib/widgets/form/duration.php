<?php
class HC_Form_Input_Duration extends HC_Form_Input_Select
{
	function __construct( $name = '' )
	{
		parent::__construct( $name );

		$start_with = 0;
		$end_with = 4 * 60 * 60;

		if( $end_with < $start_with ){
			$end_with = $start_with;
		}

		$step = 5 * 60;
		$options = array();

		$t = HC_Lib::time();
		$t->setDateDb( 20130118 );

		if( $start_with )
			$t->modify( '+' . $start_with . ' seconds' );

		$no_of_steps = ( $end_with - $start_with) / $step;
		for( $ii = 0; $ii <= $no_of_steps; $ii++ ){
			$sec = $start_with + $ii * $step;
			// $options[ $sec ] = $t->formatPeriod( $sec - $start_with );
			$options[ $sec ] = $t->formatPeriodExtraShort( $sec - $start_with, 'min' );
			$t->modify( '+' . $step . ' seconds' );
		}

		$this->set_options( $options );
	}
}