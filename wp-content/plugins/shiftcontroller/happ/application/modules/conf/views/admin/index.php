<?php
$link = HC_Lib::link('conf/admin/update/' . $tab);

$this->layout->set_partial(
	'header',
	HC_Html::page_header(
		HC_Html_Factory::element('h1')
			->add_child(HCM::__('Settings'))
		)
	);

/* check if we need tabs */
$these_fields = array_keys($fields);
if( count($tabs) > 1 ){
	$menubar = HC_Html_Factory::element('div')
		->add_style('margin', 'b3')
		->add_style('padding', 'y1')
		->add_style('border', 'bottom')
//		->add_children_style('padding')
		->add_children_style('display', 'inline-block')
		// ->add_children_style('btn')
		->add_children_style('margin', 'r1', 'b1')
		->add_children_style('padding', 2)
		// ->add_children_style('display', 'block')
		;

	$app_conf = HC_App::app_conf();
	foreach( $tabs as $tk => $ms ){
		$conf_key = 'menu_conf_settings:' . $tk;
		$tab_label = $app_conf->conf( $conf_key );
		if( $tab_label === FALSE ){
			$tab_label = $tk;
		}

		$menubar->add_child(
			$tk,
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link('conf/admin/index/' . $tk))
				->add_child( $tab_label )
			);
	}

	$menubar->add_child_style($tab, 'btn-primary');

	echo $menubar->render();
	$these_fields = $tabs[$tab];
}
?>
<?php
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	;

foreach( $these_fields as $fn ){
	$f = $fields[$fn];

	$field_input = $form->input($fn);
	switch( $f['type'] ){
		case 'dropdown':
			$field_input->set_options( $f['options'] );
			break;
		case 'checkbox_set':
			$field_input->set_options( $f['options'] );

			if( isset($f['dependencies']) ){
				$field_input->set_dependencies( $f['dependencies'] );
			}
			if( isset($f['inline']) ){
				$field_input->set_inline($f['inline']);
			}
			break;
	}

	$display_form->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( $f['label'] )
			->set_content( $field_input )
			->set_error( $form->input($fn)->error() )
		);
}

$buttons = HC_Html_Factory::widget('list')
	->add_style('submit-bar')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', HCM::__('Save') )
		->add_attr('value', HCM::__('Save') )
		->add_style('btn-primary')
	);
$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

echo $display_form->render();
?>