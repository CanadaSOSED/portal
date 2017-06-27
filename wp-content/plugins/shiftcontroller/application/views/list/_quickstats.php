<?php
$list = HC_Html_Factory::widget('list')
	// ->add_style('nowrap')
	->add_children_style('display', 'inline-block')
	->add_children_style('margin', 'r1', 'b1')
	;

$count = array(
	'shift_active'		=> 0,
	'shift_draft'		=> 0,
	'timeoff_active'	=> 0,
	'timeoff_draft'		=> 0,
	);
$duration = array(
	'shift_active'		=> 0,
	'shift_draft'		=> 0,
	'timeoff_active'	=> 0,
	'timeoff_draft'		=> 0,
	);

foreach( $shifts as $sh ){
	$type = '';
	switch( $sh->type ){
		case $sh->_const('TYPE_SHIFT'):
			switch( $sh->status ){
				case $sh->_const('STATUS_ACTIVE'):
					$type = 'shift_active';
					break;
				case $sh->_const('STATUS_DRAFT'):
					$type = 'shift_draft';
					break;
			}
			break;

		case $sh->_const('TYPE_TIMEOFF'):
			switch( $sh->status ){
				case $sh->_const('STATUS_ACTIVE'):
					$type = 'timeoff_active';
					break;
				case $sh->_const('STATUS_DRAFT'):
					$type = 'timeoff_draft';
					break;
			}
			break;
	}

	if( $type ){
		$count[$type]++;
		$duration[$type] += $sh->get_duration();
	}
}

$t = HC_Lib::time();
$shift = HC_App::model('shift');

if( $count['shift_active'] ){
	$title = $t->formatPeriodExtraShort( $duration['shift_active'], 'hour');
	$title2 = HCM::__('Active');

	$item = HC_Html_Factory::element('span')
		->add_child( HC_Html::icon(HC_App::icon_for('time')) )
		->add_child( $title )
		->add_attr('title', $title . ' - ' . $title2 )
		// ->add_style('badge')
		->add_style('box', 1, 0)
		->add_style('bg-color', 'olive')
		->add_style('color', 'white')
		;
	$list->add_child( $item );
}

if( $count['shift_draft'] ){
	$title = $t->formatPeriodExtraShort( $duration['shift_draft'], 'hour');
	$title2 = HCM::__('Draft');

	$item = HC_Html_Factory::element('span')
		->add_child( HC_Html::icon(HC_App::icon_for('time')) )
		->add_child( $title )
		->add_attr('title', $title . ' - ' . $title2 )
		->add_style('box', 1, 0)
		;

	$color = '#dff0d8';
	$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
	$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

	$item->add_attr('style',
		"background: repeating-linear-gradient(
			-45deg,
			$color1,
			$color1 6px,
			$color2 6px,
			$color2 12px
			);
		"
		);
	$item->add_style('color', 'black');

	$list->add_child( $item );
}

/* open shifts */
$count_open = 0;
reset( $shifts );
foreach( $shifts as $sh ){
	if( ! $sh->user_id ){
		$count_open++;
	}
}
if( $count_open ){
	$title = sprintf( HCM::_n('%d Open Shift', '%d Open Shifts', $count_open), $count_open );

	$item = HC_Html_Factory::element('span')
		->add_child( HC_Html::icon(HC_App::icon_for('user')) )
		->add_child( $count_open )
		->add_attr('title', $title )
		// ->add_style('badge')
		->add_style('box', 1, 0)
		->add_style('bg-color', 'orange')
		->add_style('color', 'white')
		;
	$list->add_child( $item );
}

/* extensions */
$extensions = HC_App::extensions();
$more_content = $extensions->run('list/quickstats', $shifts, $list);
$sublist = HC_Html_Factory::widget('list')
	->add_children_style('display', 'inline-block')
	// ->add_children_style('margin', 'r1', 'b1')
	;

foreach( $more_content as $subtab => $subcontent ){
	if( $subcontent ){
		$sublist->add_child( $subcontent );
	}
}
if( $sublist->children() ){
	$list->add_child( $sublist );
}

if( $list->children() ){
	echo $list->render();
}