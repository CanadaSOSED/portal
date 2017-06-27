<?php
$out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b2')
	;

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$more_content = $extensions->run('admin/todo');

foreach( $more_content as $subtab => $subtitle ){
	if( $subtitle ){
		$out->add_child(
			$subtab,
			$subtitle
			);
	}
}

if( ! $out->children() ){
	$out->add_child( HCM::__('No action needed') );
}

echo $out;
?>