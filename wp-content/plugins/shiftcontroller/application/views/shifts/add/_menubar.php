<?php
$extensions = HC_App::extensions();
$link = HC_Lib::link( 'shifts/add' );
$temp_shift = HC_App::model('shift');

$menubar = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;

$tabs = array('type', 'location', 'start', 'time', 'user', 'status');
// $tabs = HC_Lib::sort_array_by_array( $tabs, $params->get_keys() );

$remain_users = ( count($params->get_options('user')) - count($params->get('user')) );

$model->from_array(
	array(
		'start'	=> $params->get('start'),
		'end'	=> $params->get('end'),
		'lunch_break'	=> $params->get('lunch_break'),
		// 'date'	=> $params->get('date'),
		)
	);

foreach( $tabs as $this_tab ){
	/* current tab encountered ? */
	if( $this_tab == $tab ){
		break;
	}

	if( ($params->get($this_tab) !== NULL) ){
		$all_options = $params->get_options($this_tab);
		if( (count($all_options) == 1) && ! in_array($this_tab, array('user')) ){
			continue;
		}

		$item = '';

		switch( $this_tab ){
			case 'status':
				$model
					->set('status',	$params->get('status'))
					->set('type',	$params->get('type'))
					;
				$item = HC_Html_Factory::element('div')
					->add_style('box')
					->add_child( $model->present_status() )
					;

				break;

			case 'type':
/* 				
				$model
					->set('type', $params->get('type'))
					->set('status', $model->_const('STATUS_ACTIVE'))
					;
				$title_title = $model->present_type(HC_PRESENTER::VIEW_RAW);
				$title_label = $model->present_label(HC_PRESENTER::VIEW_HTML) . $title_title;
				$title_class = $model->present_status_class();
				$item = HC_Html_Factory::element('div')
					->add_attr( 'class', 'alert' )
					->add_child( $title_label )
					;
				foreach( $title_class as $tc ){
					$item->add_attr( 'class', 'alert-' . $tc );
				}

 */				break;

			case 'location':
				if( $params->get('type') == SHIFT_HC_MODEL::TYPE_TIMEOFF ){
					break;
				}

				$model->from_array(
					array(
						'location'	=> $params->get('location'),
						)
					);

				$item = HC_Html_Factory::widget('label_row')
					->set_label( HCM::__('Location') )
					->set_content( 
						$model->present_location()
						)
					->set_content_static()
					;

				break;

			case 'start':
				$item = HC_Html_Factory::widget('container')
					;

			/* date */
				$date_input = HC_Html_Factory::input('recurring_date');
				$date_input->set_value( $params->get('date') );
				$dates = $date_input->dates();
				$dates_datails = $date_input->dates_details();

				$dates_details_view = HC_Html_Factory::widget('list')
					;

				for( $di = 0; $di < count($dates_datails); $di++ ){
					$dates_details_view->add_child( 'dd_' . $di, $dates_datails[$di] );
					if( $di > 0 ){
						$dates_details_view->add_child_style( 'dd_' . $di, 'mute' );
						$dates_details_view->add_child_style( 'dd_' . $di, 'font-size', -1 );
					}
				}

			/* now draw the calendar */
				if( count($dates) > 1 ){
					$dates_calendar = HC_Html_Factory::widget('calendar2')
						->set_date( $dates[0] )
						->set_end_date( $dates[count($dates) - 1] )
						;

					$default_date_view = HC_Html_Factory::element('div')
						->add_style('text-align', 'center')
						->add_style('margin', 1)
						->add_style('padding', 'x1')
						->add_style('font-size', -1)
						;

					$dates_calendar->set_default_date_content($default_date_view);
					foreach( $dates as $this_date ){
						$date_view = HC_Html_Factory::element('div')
							->add_style('text-align', 'center')
							->add_style('margin', 1)
							->add_style('padding', 'x1')
							->add_style('font-size', -1)
							->add_style('color', 'white')
							->add_style('bg-color', 'olive')
							;
						$dates_calendar->set_date_content( $this_date, $date_view );
					}
					$dates_details_view->add_child( 'calendar', $dates_calendar );
				}

				$date_label = HCM::__('Date');

				$item
					->add_child(
						HC_Html_Factory::widget('label_row')
							->set_label( $date_label )
							->set_content(
								$dates_details_view
								)
							->set_content_static()
							)
					;

			/* time */
				$item
					->add_child(
						HC_Html_Factory::widget('label_row')
							->set_label( HCM::__('Time') )
							->set_content( 
								$model->present_time()
								)
							->set_content_static()
							)
					;

				break;

			case 'user':
				if( $tab != 'more_user' ){
					$item = HC_Html_Factory::widget('tiles')
						->set_per_row(2)
						;

					$uids = $params->get('user');

				/* count open shifts */
					$open_shifts_count = 0;
					$open_shift_shown = FALSE;
					reset( $uids );
					foreach( $uids as $uid ){
						if( ! $uid ){
							$open_shifts_count++;
						}
					}

					reset( $uids );
					foreach( $uids as $uid ){
						$model->from_array(
							array(
								'user'	=> $uid,
								)
							);

						if( (! $uid) && ($open_shifts_count > 1) ){
							if( $open_shift_shown ){
								continue;
							}
							$main = HC_Html_Factory::widget('list')
								->add_children_style('inline')
								->add_children_style('margin', 'r1', 'b1')
								;
							$main->add_child( $model->present_user() );
							$main->add_child(
								HC_Html_Factory::element('span')
									->add_style('badge')
									->add_child($open_shifts_count) 
								);
							$open_shift_shown = TRUE;
						}
						else {
							$main = HC_Html_Factory::element('span')
								->add_child( $model->present_user() )
								;
						}

						$user_item = HC_Html_Factory::element('div')
							->add_style('box')
							->add_style('margin', 'r1', 'b1')
							;

					/* remove */
						if( count($uids) > 1 ){
							$remove_params = $params->to_array();
							$remove_params['user-'] = $uid;
							$user_item->add_child(
								HC_Html_Factory::element('a')
									->add_child(
										HC_Html::icon('times')
											// ->add_style('color', 'red')
										)
									->add_attr('href', HC_Lib::link('shifts/add/index')->url($remove_params) )
									->add_attr('title', HCM::__('Delete'))
									->add_style('closer')
								);
						}

						$user_item->add_child( $main );

					/* EXTENSIONS SUCH AS CONFLICTS */
						if( $uid ){
							for( $mi = 0; $mi < count($models); $mi++ ){
								$models[$mi]->user_id = $uid;
							}

							$more_content = $extensions->run('shifts/assign/quickview', $models);
							if( $more_content ){
								$more_wrap = HC_Html_Factory::widget('list')
									->add_children_style('margin', 'b1')
									->add_children_style('font-size', -1)
									;
								$added = 0;
								foreach($more_content as $mck => $mc ){
									$more_wrap->add_child($mc);
									$added++;
								}
								if( $added ){
									$user_item->add_child($more_wrap);
								}
							}
						}

						$item->add_child( $user_item );
					}
				}
				break;
		}

		switch( $this_tab )
		{
			case 'user':
				if( $tab != 'more_user' ){
					if( ($params->get('type') != $temp_shift->_const('TYPE_TIMEOFF')) OR ($remain_users > 0) ){
						if( count($free_staff) > 1 ){
							$item->add_child(
								HC_Html_Factory::widget('titled', 'a')
									->add_attr('href', HC_Lib::link('shifts/add/add_user')->url($params->to_array()))
									->add_child(
										HC_Html::icon('plus') . ' ' . HCM::__('Add Staff')
										)
									->add_style('btn')
									->add_style('border')
									->add_style('rounded')
									->add_style('padding', 2)
								);
						}
					}
				}

				if( count($free_staff) > 1 ){
					$item = HC_Html_Factory::widget('label_row')
						->set_label( HCM::__('Staff') )
						->set_content( $item )
						;
				}
				else {
					$item = HC_Html_Factory::widget('label_row')
						->set_label( HCM::__('Staff') )
						->set_content( $main )
						->set_content_static()
						;
				}

				break;

			default:
				break;
		}
	
		if( $item ){
			$menubar->add_child(
				$this_tab,
				$item
				);
		}
	}
}
?>
<?php echo $menubar->render(); ?>
