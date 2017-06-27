<?php
$out = HC_Html_Factory::widget('container')
	->add_child( HC_Html::icon('list') )
	->add_child( HCM::__('History') )
	;
echo $out;