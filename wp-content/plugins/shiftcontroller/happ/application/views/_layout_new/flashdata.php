<?php
if( $message ){
	$out = HC_Html_Factory::element('div')
		->add_attr('class', 'hcj-auto-dismiss')
		->add_attr('class', 'hcj-alert')
		->add_style('margin', 'b2')
		->add_style('padding', 0)
		->add_style('border')
		;

	if( ! is_array($message) ){
		$message = array( $message );
	}

	$msg_view = HC_Html_Factory::widget('list')
		->add_style('margin', 0)
		->add_attr('style', 'border-width: 4px;')
		->add_style('border', 'left')
		->add_style('border', 'olive')
		;
	foreach( $message as $m ){
		$msg_view2 = HC_Html_Factory::element('div')
			->add_style('padding', 2)
			->add_style('bg-lighten', 4)

			->add_child( $m )
			->add_child(
				HC_Html_Factory::element('titled', 'a')
					->add_child( HC_Html::icon('times') )
					->add_style('color', 'red')
					->add_style('closer')
					->add_attr('class', 'hcj-alert-dismisser')
				)
			;
		$msg_view->add_child( $msg_view2 );
	}
	$out->add_child( $msg_view );
	echo $out;
}

if( $error ){
	$out = HC_Html_Factory::element('div')
		->add_attr('class', 'hcj-auto-dismiss')
		->add_attr('class', 'hcj-alert')
		->add_style('margin', 'b2')
		->add_style('padding', 0)
		->add_style('border')
		;

	if( ! is_array($error) ){
		$error = array( $error );
	}

	$msg_view = HC_Html_Factory::widget('list')
		->add_style('margin', 0)
		->add_attr('style', 'border-width: 4px;')
		->add_style('border', 'left')
		->add_style('border', 'red')
		;
	foreach( $error as $m ){
		$msg_view2 = HC_Html_Factory::element('div')
			->add_style('padding', 2)
			->add_style('bg-lighten', 4)

			->add_child( $m )
			->add_child(
				HC_Html_Factory::element('titled', 'a')
					->add_child( HC_Html::icon('times') )
					->add_style('color', 'red')
					->add_style('closer')
					->add_attr('class', 'hcj-alert-dismisser')
				)
			;
		$msg_view->add_child( $msg_view2 );
	}

	$out->add_child( $msg_view );
	echo $out;
}

if( isset($debug_message) && $debug_message ){
	if( ! is_array($debug_message) ){
		$debug_message = array( $debug_message );
	}

	$debug_message = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		->set_children( $debug_message )
		;

	$debug = HC_Html_Factory::element('div')
		->add_style('box')
		->add_style('border-color', 'orange')
		->add_child( $debug_message )
		;
	echo $debug->render();
}
?>