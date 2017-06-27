<?php
class HC_Html_Widget_Calendar2 extends HC_Html_Element
{
	private $date = '';
	private $end_date = '';
	private $content = array();
	private $show_other = FALSE;
	private $show_weekdays = TRUE;
	private $default_date_content = NULL;

	function __construct( $date = '' )
	{
		parent::__construct();
		$t = HC_Lib::time();
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
	}

	public function set_default_date_content( $c )
	{
		$this->default_date_content = $c;
	}
	public function default_date_content()
	{
		return $this->default_date_content;
	}

	public function set_show_other( $show_other = TRUE )
	{
		$this->show_other = $show_other;
		return $this;
	}
	public function show_other()
	{
		return $this->show_other;
	}

	public function set_show_weekdays( $show_weekdays = TRUE )
	{
		$this->show_weekdays = $show_weekdays;
		return $this;
	}
	public function show_weekdays()
	{
		return $this->show_weekdays;
	}

	function dates()
	{
		$t = HC_Lib::time();

		$start_date = $this->date();
		$end_date = $this->end_date();

		if( $end_date ){
			$return = array();
			$t->setDateDb( $start_date );
			$rex_date = $t->formatDate_Db();
			while( $rex_date <= $end_date ){
				$return[] = $rex_date;
				$t->modify('+1 day');
				$rex_date = $t->formatDate_Db();
			}
		}
		else {
			$t->setDateDb( $start_date );
			$return = $t->getDates( 'month' );
		}

		return $return;
	}

	function set_date_content( $date, $content )
	{
		$this->content[$date] = $content;
		return $this;
	}
	function date_content( $date )
	{
		return isset($this->content[$date]) ? $this->content[$date] : NULL;
	}

	function set_date( $date )
	{
		$this->date = $date;
		return $this;
	}
	function date()
	{
		return $this->date;
	}

	function set_end_date( $end_date )
	{
		$this->end_date = $end_date;
		return $this;
	}
	function end_date()
	{
		return $this->end_date;
	}

	function render()
	{
		$t = HC_Lib::time();

		$start_date = $this->date();
		$end_date = $this->end_date();

		$months = array();

		$t->setDateDb( $start_date );
		$t->setStartMonth();
		$months[] = $t->formatDate_Db();
		$t->setEndMonth();
		$rex_date = $t->formatDate_Db();

		while( $rex_date < $end_date ){
			$t->modify('+1 day');
			$t->setStartMonth();
			$months[] = $t->formatDate_Db();
			$t->setEndMonth();
			$rex_date = $t->formatDate_Db();
		}

		$full_out = HC_Html_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r1', 'b1')
			;

		$show_other = $this->show_other();
		$show_weekdays = $this->show_weekdays();

		foreach( $months as $start_month_day ){
			$t->setDateDb( $start_month_day );
			$month_matrix = $t->getMonthMatrix();

			$t->setDateDb( $start_month_day );
			$start_month = $t->formatDate_Db();
			$t->setEndMonth();
			$end_month = $t->formatDate_Db();

			$out = HC_Html_Factory::widget('table')
				->add_attr('style', 'table-layout: fixed; width: 14em;')
				;

			$rid = 0;
			if( $show_weekdays ){
				$cid = 0;
				foreach( $month_matrix[0] as $date ){
					$t->setDateDb( $date );

					$cell_content = $t->formatWeekDayShort();
					$cell_content = HC_Html_Factory::element('div')
						->add_style('mute')
						->add_style('font-size', -1)
						->add_style('text-align', 'center')
						// ->add_style('nowrap')
						->add_style('border', 'bottom')
						->add_child( $cell_content )
						;

					$out->set_cell( $rid, $cid,
						$cell_content
						);

					$out->add_cell_attr( $rid, $cid,
						array(
							'style'	=> 'width: 14.2857%;',
							)
						);

					$cid++;
				}
				$rid++;
			}

			foreach( $month_matrix as $week => $week_dates ){
				$cid = 0;
				foreach( $week_dates as $date ){
					if( ! $show_other && (($date > $end_month) OR ($date < $start_month)) ){
						$cell_content = '';
						/* empty cell */
						if( ! $show_weekdays ){
							$out->add_cell_attr( $rid, $cid,
								array(
									'class'	=> array('noborder'),
									)
								);
						}
					}
					else {
						$t->setDateDb( $date );

						$cell_content = $this->date_content($date);
						if( $cell_content === NULL ){
							$cell_content = clone $this->default_date_content();
						}

						if( is_object($cell_content) ){
							$cell_content
								->add_child( $t->getDayShort() )
								;
						}
						elseif( $cell_content !== NULL ){
						}
						else {
							$cell_content = $t->getDayShort();
						}
					}

					$out->set_cell( $rid, $cid,
						$cell_content
						);

					$out->add_cell_attr( $rid, $cid,
						array(
							'style'	=> 'width: 14.2857%; white-space: nowrap;',
							)
						);

					$cid++;
				}
				$rid++;
			}

			$month_out = HC_Html_Factory::widget('list')
				->add_children_style('margin', 'b1')
				->add_style('border')
				->add_style('rounded')
				->add_style('padding', 1)
				;

			$t->setDateDb( $start_month_day );
			$month_label = $t->getMonthName() . ' ' . $t->getYear();

			$month_out->add_child('label', $month_label );
			$month_out->add_child_style('label', 'text-align', 'center');
			$month_out->add_child('calendar', $out );

			$full_out->add_child(
				'month_' . $start_month_day,
				$month_out
				);
			$full_out->add_child_attr(
				'month_' . $start_month_day,
				'style',
				'vertical-align: top;'
				);
		}

		return $full_out->render();
	}
}
