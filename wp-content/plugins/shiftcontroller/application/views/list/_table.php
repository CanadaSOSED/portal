<?php
$current_user_id = $this->auth->user()->id;

$list = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b2')
	;

$total_count = 0;

$this_state = $state;
$this_state['wide'] = 1;
$this_state['form'] = TRUE;

$content = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/listing' )
	->pass_arg( array($shifts, $this_state) )
	// ->set_param( 'date', $this_date )
	->add_attr('class', 'hcj-rfr')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$content->set_param( $k, $v );
	}
}

$total_count = count($shifts);

$list->add_child(
	HC_Html_Factory::element('div')
		->add_attr('class', 'hcj-ajax-container')
	);
$list->add_child( $content );

if( ! $total_count ){
	$list->add_child( HCM::__('Nothing') );
}

/* header view */
$this_state['wideheader'] = 1;
$quickheader_view = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/quickheader' )
	->pass_arg( array($shifts, $this_state) )
	->set_show_empty( TRUE )
	->add_attr('class', 'hcj-rfr')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$quickheader_view->set_param( $k, $v );
	}
}

$full_out = HC_Html_Factory::widget('list')
	->add_style('margin', 'b1')
	->add_children_style('margin', 'b2')
	->add_attr('class', 'hcj-collector-wrap')
	->add_attr('class', 'hcj-ajax-parent')
	;

$full_out->add_child( $quickheader_view );
$full_out->add_child( $list );

echo $full_out->render();
?>