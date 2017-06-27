<?php
$filter_ui = HC_App::filter_ui();

$out = array();

/* GROUP BY */
$this_view = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	->add_child( 
		HC_Html_Factory::element('h4')
			->add_child( HCM::__('Group By') )
		)
;

$options = array(
/* translators: list output without grouping */
	'none'	=> HCM::__('No Grouping')
	);
if( count($all_staffs) > 1 ){
	$options['staff'] = HCM::__('Staff');
}
if( count($all_locations) > 1 ){
	$options['location'] = HCM::__('Location');
}

$state_by = $state['by'] ? $state['by'] : 'none';
$options_view = HC_Html_Factory::input('radio')
	->set_name('by')
	->set_inline(TRUE)
	->set_default( $state_by )
	;
foreach( $options as $k => $o ){
	$options_view->add_option( $k, $o );
}

$this_view->add_child( $options_view );
$out['group_by'] = $this_view->render();

/* VIEW TYPE */
$this_view = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	->add_child( 
		HC_Html_Factory::element('h4')
			->add_child( HCM::__('View Type') )
		)
;

$options = array(
/* translators: view type, i.e. Calendar View */
	'calendar'	=> HCM::__('Calendar'),
/* translators: view type, i.e. List View */
	'browse'	=> HCM::__('List'),
/* translators: view type, i.e. Report View */
	'report'	=> HCM::__('Report'),
	);

$options_view = HC_Html_Factory::input('radio')
	->set_name('tab')
	->set_inline(TRUE)
	->set_default( $tab )
	;
foreach( $options as $k => $o ){
	$options_view->add_option( $k, $o );
}

$this_view->add_child( $options_view );
$out['view_type'] = $this_view->render();

/* NOW FILTER IT */
if( $filter_ui->is_disabled('group-by') ){
	unset($out['group_by']);
}
if( $filter_ui->is_disabled('view-type') ){
	unset($out['view_type']);
}
if( ! ((count($all_staffs) > 1) OR (count($all_locations) > 1)) ){
	unset($out['group_by']);
}

$full_out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
;
foreach( $out as $k => $v ){
	$full_out->add_child( $k, $v );
}

if( $full_out->children() ){
	$link = HC_Lib::link( 'list/' . $tab, $state );

	$display_form = HC_Html_Factory::widget('form')
		->add_attr('action', $link->url(array('updateview' => 1)) )
		;

	$display_form->add_child( $full_out );

	$buttons = HC_Html_Factory::widget('list')
		->add_style('submit-bar')
		;
	$buttons->add_child(
		HC_Html_Factory::element('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('Proceed') )
			->add_attr('value', HCM::__('Proceed') )
			->add_style('btn-primary')
		);
	$display_form->add_child( $buttons );
	echo $display_form->render();
}
return;
?>