<?php
$_template = array();

if( $current_user_id ){
	$_template['title'] = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		->add_child('{TITLE}')
		->add_child('{QUICKHEADER}')
		->render()
		;
}
else {
	$_template['title'] = '{TITLE}';
}

$_template['links'] = HC_Html_Factory::element('div')
	->add_attr('class', 'hc-hover-visible')
	->add_child('{LINK}')
	->render()
	;

$_template['entity_title'] = HC_Html_Factory::element('h4')
	->add_child( '{ENTITY_TITLE}' )
	->add_style('margin', 0)
	->add_style('padding', 0)
	->render()
	;

$_template['date_label'] = HC_Html_Factory::element('h4')
	->add_style('text-align', 'center')
	->add_child(
		HC_Html_Factory::widget('list')
			->add_style('nowrap')
			->add_child('{FORMAT_WEEKDAY_SHORT}')
			->add_child(
				HC_Html_Factory::element('small')
					->add_child('FORMAT_DATE')
			)
		)
	->render()
	;

$_template['btns'] = HC_Html_Factory::widget('titled', 'a')
	->add_attr('href', '{HREF}')
	->add_attr('class', 'hcj-flatmodal-loader')

	->add_style('btn')
	->add_style('padding', 1)
	->add_style('display', 'block')
	->add_style('border')
	->add_style('rounded')
	->add_style('text-align', 'center')

	->add_child( HC_Html::icon('plus') )
	->add_child( HCM::__('Add') )
	->add_attr('title', HCM::__('Add'))

	->render()
	;

$_template['btns_slim'] = HC_Html_Factory::widget('titled', 'a')
	->add_attr('href', '{HREF}')
	->add_attr('class', 'hcj-flatmodal-loader')

	->add_style('btn')
	->add_style('padding', 'y1')
	->add_style('display', 'block')
	->add_style('border')
	->add_style('rounded')
	->add_style('text-align', 'center')

	->add_child( HC_Html::icon('plus') )
	->add_attr('title', HCM::__('Add'))

	->render()
	;
?>