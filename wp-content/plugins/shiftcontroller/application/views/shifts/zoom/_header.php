<?php
echo HC_Html::page_header(
	HC_Html_Factory::element('h2')
		->add_child( $object->present_title() )
);
?>