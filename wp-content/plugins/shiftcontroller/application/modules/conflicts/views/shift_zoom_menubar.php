<?php
$label = NULL;

if( $entries ){
	$label = array();
	$icon = HC_Html::icon( HC_App::icon_for('conflict') )
		->add_style('color', 'red')
		;

	$label[] = $icon;
	$label[] = sprintf( HCM::_n('%d Conflict', '%d Conflicts', count($entries)), count($entries) );
	$label = join('', $label);
}

if( $label ){
	echo $label;
}