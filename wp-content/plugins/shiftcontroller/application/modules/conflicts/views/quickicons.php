<?php
/* failed */
if( $count_fail ){
	$title = sprintf( HCM::_n('%d Conflict', '%d Conflicts', $count_fail), $count_fail );

	if( $wrap ){
	/* red border for wrap */
		$wrap
			->add_style('border')
			->add_style('border-color', 'red')
			// ->add_attr('style', 'position: relative;')
			;
	}

	$out = HC_Html::icon(HC_App::icon_for('conflict'))
		->add_attr('title', $title)
		->add_style('bg-color', 'red')
		->add_style('color', 'white')
		->add_style('rounded')
		;

	echo $out->render();
}
?>