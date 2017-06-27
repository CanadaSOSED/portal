<?php
$list = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;

/* extensions */
$extensions = HC_App::extensions();
$more_content = $extensions->run('list/quickheader', array($shifts, $state));

foreach( $more_content as $subtab => $subcontent ){
	if( $subcontent ){
		$list->add_child( $subcontent );
	}
}
/*
if( $list->children() ){
	echo $list->render();
}
*/

/* add stats */
$quickstats_view = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/quickstats' )
	->pass_arg( array($shifts, $state) )
	->add_attr('class', 'hcj-rfr')
	;
foreach( $state as $k => $v ){
	if( $v OR ($v === 0) ){
		$quickstats_view->set_param( $k, $v );
	}
}

if( isset($state['wideheader']) && $state['wideheader'] ){
	$out = HC_Html_Factory::widget('grid')
		->add_child('stats',		$quickstats_view,	4)
		->add_child('extensions',	$list,				8)
		;
	$out->add_child_style('stats', 'text-align', 'sm-right');
	$out->set_child_right('stats', 1);
}
else {
	$out = HC_Html_Factory::widget('list')
		// ->add_children_style('margin', 'b1')
		->add_child($quickstats_view)
		;
	if( $list->children() ){
		$out
			->add_child($list)
			;
	}
}

echo $out->render();