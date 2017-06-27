<?php
class HC_Html_Widget_Schedule_Calendar extends HC_Html_Element
{
	protected $dates = array();
	protected $titles = array();
	protected $cells = array();
	protected $disable_weekdays = array();

	function __construct()
	{
		parent::__construct();
		
		$conf = HC_App::app_conf();
		$disable_weekdays_conf = $conf->get('disable_weekdays');
		$this->set_disable_weekdays( $disable_weekdays_conf );
	}

	public function set_disable_weekdays( $disable_weekdays )
	{
		if( $disable_weekdays !== NULL ){
			if( ! is_array($disable_weekdays) ){
				$disable_weekdays = array($disable_weekdays);
			}
		}
		$this->disable_weekdays = $disable_weekdays;
		return $this;
	}
	public function disable_weekdays()
	{
		return $this->disable_weekdays;
	}

	public function set_dates( $dates ){
		$this->dates = $dates;
	}
	public function dates(){
		$disable_weekdays = $this->disable_weekdays();

		$all_dates = $this->dates;
		if( ! $disable_weekdays ){
			$return = $all_dates;
		}
		else {
			$return = array();
			/* remove not used weekdays */
			$t = HC_Lib::time();
			foreach( $all_dates as $d ){
				$t->setDateDb($d);
				$this_weekday = $t->getWeekDay();
				if( ! in_array($this_weekday, $disable_weekdays) ){
					$return[] = $d;
				}
			}
		}
		return $return;
	}

	public function set_title( $rid, $title ){
		$this->titles[$rid] = $title;
	}
	public function title( $rid ){
		$return = isset($this->titles[$rid]) ? $this->titles[$rid] : NULL;
		return $return;
	}

	public function set_cell( $rid, $date, $cell ){
		$this->cells[$rid][$date] = $cell;
	}
	public function cell( $rid, $date ){
		$return = isset($this->cells[$rid][$date]) ? $this->cells[$rid][$date] : NULL;
		return $return;
	}

	public function render()
	{
		$has_title = $this->titles ? TRUE : FALSE;
		$this_dates = $this->dates();

		if( count($this_dates) == 1 ){
			$range = 'day';
		}
		else {
			if( count($this_dates) > 7 ){
				if( $has_title ){
					$range = 'month';
				}
				else {
					$range = 'month-week';
				}
			}
			else {
				$range = 'week';
			}
		}

		$dates_matrix = array();
		if( $range == 'month-week' ){
			$t = HC_Lib::time();
			$t->setDateDb( $this_dates[0] );
			$t->setStartMonth();
			$dates_matrix = $t->getMonthMatrix();
		}

	/* TEMPLATES */
		$_template = array();
		/* ONE CELL */
		$_template['view_cell'] = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal-cell')
			->add_attr('class', 'hc-hover-parent')
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'schecal-cell-date')
					->add_child('{DATE_LABEL}')
				)
			->add_child('{CELL_CONTENT}')
			->render()
			;

		$_template['view_cell_border'] = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal-cell')
			->add_attr('class', 'hc-hover-parent')
			->add_attr('class', array('schecal-cell-start-week'))
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'schecal-cell-date')
					->add_child('{DATE_LABEL}')
				)
			->add_child('{CELL_CONTENT}')
			->render()
			;

		/* CELLS ROW */
		$_template['view_cells'] = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal-cells')
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'hcj-ajax-container')
					->add_style('padding', 'y2')
				)
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'schecal-cells-row')
					->add_child('{VIEW_CELLS}')
				)
			->render()
			;

		/* ROW TITLE */
		$_template['view_title'] = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal-title')
			->add_child('{TITLE}')
			->render()
			;

		/* ROW */
		$_template['view_row'] = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal-row')
			->add_attr('class', 'hcj-ajax-parent')
			->add_child('{TITLE}')
			->add_child('{VIEW_CELLS}')
			->render()
			;

		/* DATE LABEL */
		$date_label_template = HC_Html_Factory::widget('list')
			->add_style('nowrap')
			// ->add_children_style('border')
			// ->add_children_style('padding', 0)
			// ->add_children_style('margin', 0)
			// ->add_children_attr('style', 'line-height: 1em;')
			;
		$final_date_label_template = NULL;

		switch( $range ){
			case 'month':
				$date_label_template
					->add_child( 
						HC_Html_Factory::element('small')
							->add_child( '{TIME_FORMAT_WEEKDAY}' )
						)
					->add_child(
						'{TIME_FORMAT_DAY}'
						)
					;

				$final_date_label_template = HC_Html_Factory::element('div')
					;
				break;

			case 'month-week':
			case 'week':
				$date_label_template
					->add_child( '{TIME_FORMAT_WEEKDAY}' )
					->add_child(
						HC_Html_Factory::element('small')
							->add_child( '{TIME_FORMAT_DATE}' )
					)
					;

				$final_date_label_template = HC_Html_Factory::element('h4')
					;
				break;
		}

		$_template['date_label'] = '';
		if( $final_date_label_template ){
			$final_date_label_template
				->add_style('text-align', 'center')
				->add_child( $date_label_template )
				->add_style('margin', 0)
				->add_style('padding', 0)
				;
			$_template['date_label'] = $final_date_label_template->render();
		}

	/* build out */
		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'schecal')
			;

		switch( $range ){
			case 'month-week':
				$out->add_attr('class', 'schecal-week');
				$out->add_attr('class', 'schecal-month-week');
				break;
			case 'month':
				$out->add_attr('class', 'schecal-month');
				break;
			case 'week':
				$out->add_attr('class', 'schecal-week');
				break;
		}

		$t = HC_Lib::time();
		$week_starts_on = $t->weekStartsOn();

	/* move week starts on to enabled dates if needed */
		$disable_weekdays = $this->disable_weekdays();
		if( $disable_weekdays && (count($disable_weekdays) < 7)){
			while( in_array($week_starts_on, $disable_weekdays) ){
				$week_starts_on++;
				if( $week_starts_on > 6 ){
					$week_starts_on = 0;
				}
			}
		}

		$week_border_days = array();
		reset( $this_dates );
		foreach( $this_dates as $date ){
			$t->setDateDb( $date );
			if( $t->getWeekDay() == $week_starts_on ){
				$week_border_days[] = $date;
			}
		}

	/* prepare dates labels */
		$DATE_LABELS = array();
		reset( $this_dates );
		foreach( $this_dates as $date ){
			$t->setDateDb( $date );

			$date_label = str_replace(
				array(
					'{TIME_FORMAT_WEEKDAY}',
					'{TIME_FORMAT_DATE}',
					'{TIME_FORMAT_DAY}'
					),
				array(
					$t->formatWeekdayShort(),
					$t->formatDate(),
					$t->getDayShort()
					),
				$_template['date_label']
				);
			$DATE_LABELS[$date] = $date_label;
		}

	/* dates labels */
		if( ! in_array($range, array('day', 'month-week')) ){
			$view_dates_row = HC_Html_Factory::element('div')
				->add_attr('class', 'schecal-row')
				->add_attr('class', 'schecal-dates')
				;

		/* blank title */
			if( $has_title ){
				$view_title = HC_Html_Factory::element('div')
					->add_attr('class', 'schecal-title')
					// ->add_child( $title )
					;
				$view_dates_row->add_child( $view_title );
			}

			$view_dates_cells = HC_Html_Factory::element('div')
				->add_attr('class', 'schecal-cells')
				;
			$view_dates_cells_row = HC_Html_Factory::element('div')
				->add_attr('class', 'schecal-cells-row')
				;

			reset( $this_dates );
			foreach( $this_dates as $date ){
				$view_cell = HC_Html_Factory::element('div')
					->add_attr('class', 'schecal-cell')
					;
				if( in_array($date, $week_border_days) ){
					$view_cell->add_attr('class', array('schecal-cell-start-week'));
				}

				$view_cell->add_child( $DATE_LABELS[$date] );
				$view_dates_cells_row->add_child( $view_cell );
			}
			$view_dates_cells->add_child( $view_dates_cells_row );
			$view_dates_row->add_child( $view_dates_cells );

			$out->add_child( $view_dates_row );
		}

	/* items */
		if( $this->titles ){
			$rids = count($this->titles);
		}
		elseif( $dates_matrix ){
			$rids = count($dates_matrix);
		}
		else {
			$rids = 1;
		}

		for( $rid = 0; $rid < $rids; $rid++ ){
		/* title */
			$view_title = '';
			if( $has_title ){
				$title = $this->title( $rid );
				$view_title = str_replace(
					array(
						'{TITLE}'
						),
					array(
						$title
						),
					$_template['view_title']
					);
			}

		/* cells */
			$view_cells = array();

			$data_rid = $rid;
			if( $dates_matrix ){
				$iterate_dates = $dates_matrix[$rid];
				$data_rid = 0;
			}
			else {
				$iterate_dates = $this_dates;
			}

			reset( $iterate_dates );
			foreach( $iterate_dates as $date ){
				if( in_array($date, $week_border_days) ){
					$_this_template = $_template['view_cell_border'];
				}
				else {
					$_this_template = $_template['view_cell'];
				}

				if( ! isset($DATE_LABELS[$date]) ){
					$cell_content = '';
					$date_label = '';
				}
				else {
					$cell_content = $this->cell( $data_rid, $date );
					if( is_array($cell_content) ){
						$cell_content = join('', $cell_content);
					}
					$date_label = $DATE_LABELS[$date];
				}

				$view_cell = str_replace(
					array(
						'{CELL_CONTENT}',
						'{DATE_LABEL}'
						),
					array(
						$cell_content,
						$date_label
						),
					$_this_template
					);

				$view_cells[] = $view_cell;
			}

			$view_cells = join('', $view_cells);
			$view_cells = str_replace(
				array(
					'{VIEW_CELLS}',
					),
				array(
					$view_cells,
					),
				$_template['view_cells']
				);

			$view_row = str_replace(
				array(
					'{VIEW_CELLS}',
					'{TITLE}',
					),
				array(
					$view_cells,
					$view_title
					),
				$_template['view_row']
				);

			$out->add_child( $view_row );
		}

		return $out->render();
	}
}
?>