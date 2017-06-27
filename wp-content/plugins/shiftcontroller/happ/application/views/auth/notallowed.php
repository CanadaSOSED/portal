<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Access denied') )
		)
	);
$out = HC_Html_Factory::element('p')
	->add_child( HCM::__('Access denied') )
	;
echo $out->render();
?>