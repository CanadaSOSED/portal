<?php
$out = HC_Html_Factory::widget('list')
	;

if( $subheader ){
	$out->add_child(
		HC_Html_Factory::element('h4')
			->add_child($subheader)
		);
}
$out->add_child( $content );
echo $out->render();
?>