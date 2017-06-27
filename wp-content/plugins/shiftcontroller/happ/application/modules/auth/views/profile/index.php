<?php
$out = HC_Html_Factory::widget('list')
	;

$out->add_child( $content );
echo $out->render();
?>