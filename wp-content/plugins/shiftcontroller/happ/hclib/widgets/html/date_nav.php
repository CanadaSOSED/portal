<?php
// include_once( dirname(__FILE__) . '/list.php' );
// class HC_Html_Widget_Date_Nav extends HC_Html_Widget_List
include_once( dirname(__FILE__) . '/grid.php' );
class HC_Html_Widget_Date_Nav extends HC_Html_Widget_Grid
{
	private $range = 'week'; // may be week or day
	private $link = NULL;
	private $date_param = 'date';
	private $range_param = 'range';
	private $date = '';
	private $submit_to = '';
	private $enabled = array('day', 'week', 'month', 'custom', 'upcoming', 'all');

	function __construct( $start = '' )
	{
		parent::__construct();
		$t = HC_Lib::time();
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
	}

	function set_date( $date )
	{
		$this->date = $date;
	}
	function date()
	{
		return $this->date;
	}

	function set_enabled( $enabled )
	{
		$this->enabled = $enabled;
	}
	function enabled()
	{
		return $this->enabled;
	}

	function set_submit_to( $submit_to )
	{
		$this->submit_to = $submit_to;
	}
	function submit_to()
	{
		return $this->submit_to;
	}

	function set_range( $range )
	{
		$this->range = $range;
	}
	function range()
	{
		return $this->range;
	}

	function set_link( $link )
	{
		$this->link = $link;
	}
	function link()
	{
		return $this->link;
	}

	function set_date_param( $param )
	{
		$this->date_param = $param;
	}
	function date_param()
	{
		return $this->date_param;
	}

	function set_range_param( $param )
	{
		$this->range_param = $param;
	}
	function range_param()
	{
		return $this->range_param;
	}

	private function _nav_title( $readonly = FALSE )
	{
		$t = HC_Lib::time();
		$nav_title = '';

		switch( $this->range() ){
			case 'all':
				$nav_title = HCM::__('All Time');
				break;

			case 'upcoming':
				/* translators: it refers to the upcoming time range */
				$nav_title = HCM::__('Upcoming');
				break;

			case 'day':
				$start_date = $this->date();
				$t->setDateDb( $start_date );
				$nav_title = $t->formatDate();
				break;

			case 'custom':
				list( $start_date, $end_date ) = explode('_', $this->date());
				$nav_title = $t->formatDateRange( $start_date, $end_date );
				break;

			case 'day':
				$t->setDateDb( $this->date() );
				$start_date = $end_date = $t->formatDate_Db();
				$nav_title = $t->formatDateRange( $start_date, $end_date );
				$nav_title = HCM::__('Day');
				break;

			case 'week':
				$t->setDateDb( $this->date() );
				list( $start_date, $end_date ) = $t->getDatesRange( $this->date(), 'week' );
				$nav_title = $t->formatDateRange( $start_date, $end_date );
				break;

			case 'month':
				$t->setDateDb( $this->date() );
				$nav_title = $t->getMonthName() . ' ' . $t->getYear();
				break;
		}

		return $nav_title;
	}

	private function _render_range_selector( $start_date, $end_date )
	{
		$link = $this->link();

		$out = HC_Html_Factory::widget('dropdown')
			// ->set_wrap(FALSE)
			->add_style('padding', 2)
			;

		$range_options = array();

	/* week */
		$this_params = array(
			$this->range_param()	=> 'week',
			$this->date_param()		=> $start_date ? $start_date : NULL,
			);
		$range_options['week'] = HC_Html_Factory::element('a')
			->add_child( HCM::__('Week') )
			->add_attr('href', $link->url($this_params))
			;

	/* month */
		$this_params = array(
			$this->range_param()	=> 'month',
			$this->date_param()		=> $start_date ? $start_date : NULL,
			);
		$range_options['month'] = HC_Html_Factory::element('a')
			->add_child( HCM::__('Month') )
			->add_attr('href', $link->url($this_params))
			;

	/* day */
		$this_params = array(
			$this->range_param()	=> 'day',
			$this->date_param()		=> $start_date ? $start_date : NULL,
			);
		$range_options['day'] = HC_Html_Factory::element('a')
			->add_child( HCM::__('Day') )
			->add_attr('href', $link->url($this_params))
			;

	/* custom */
		$date_param = '';
		if( $start_date && $end_date ){
			$date_param = $start_date . '_' . $end_date;
		}
		elseif( $start_date ){
			$date_param = $start_date;
		}
		$this_params = array(
			$this->range_param()	=> 'custom',
			$this->date_param()		=> $date_param ? $date_param : NULL,
			);
		$range_options['custom'] = HC_Html_Factory::element('a')
			->add_child( HCM::__('Custom Range') )
			->add_attr('title', HCM::__('Custom Range') )
			->add_attr('href', $link->url($this_params))
			;

	/* upcoming */
		$this_params = array(
			$this->range_param()	=> 'upcoming',
			$this->date_param()		=> NULL,
			);
		$range_options['upcoming'] = HC_Html_Factory::element('a')
/* translators: it refers to the upcoming time range */
			->add_child( HCM::__('Upcoming') )
			->add_attr('href', $link->url($this_params))
			;

	/* all */
		$this_params = array(
			$this->range_param()	=> 'all',
			$this->date_param()		=> NULL,
			);
		$range_options['all'] = HC_Html_Factory::element('a')
			->add_child( HCM::__('All Time') )
			->add_attr('href', $link->url($this_params))
			;

		$enabled = $this->enabled();
		$this_range = $this->range();
		
		foreach( $range_options as $k => $v ){
			$subitem = $range_options[$k];

			if( $k == $this_range ){
				$subitem
					->add_style('btn')
					->add_style('padding', 'x2', 'y2')
					->add_style('display', 'block')
					// ->add_style('color', 'white')
					->add_style('bg-color', 'silver')
					->add_style('rounded')
					;
				$out->set_title( $subitem );
			}
			else {
				if( ! in_array($k, $enabled) ){
					continue;
				}
				$subitem
					->add_style('btn')
					->add_style('padding', 1)
					->add_style('display', 'block')
					;
				$out->add_child( $subitem );
			}
		}

		return $out;
	}

	private function _render_arrow_buttons( $before_date, $after_date )
	{
		$link = $this->link();
		$out = HC_HTML_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			;

		switch( $this->range() ){
			case 'month':
			case 'week':
			case 'day':
				$out->add_child( 
					'before',
					HC_Html_Factory::element('a')
						->add_attr('href', $link->url(array($this->date_param() => $before_date)))
						->add_child(HC_Html::icon('arrow-left'))
						->add_style('btn')
					);

				$out->add_child( 
					'after',
					HC_Html_Factory::element('a')
						->add_attr('href', $link->url(array($this->date_param() => $after_date)))
						->add_child(HC_Html::icon('arrow-right'))
						->add_style('btn')
					);

				$out
					->add_children_style('btn')
					->add_children_style('padding', 2)
					// ->add_children_style('border')
					->add_children_style('rounded')
					->add_children_style('margin', 'r1')
					->add_children_style('display', 'block')
					->add_children_style('text-align', 'center')
					->add_children_style('bg-color', 'silver')
					// ->add_children_style('color', 'white')
					;
		}

		if( $out->children() ){
			return $out;
		}
	}

	private function _render_current_range( $start_date, $end_date )
	{
		$out = NULL;
		$link = $this->link();
		$enabled = $this->enabled();
		$t = HC_Lib::time();

		switch( $this->range() ){
			case 'all':
			case 'upcoming':
				break;

			case 'week':
			case 'month':
				$nav_title = $this->_nav_title();
				$out = HC_Html_Factory::element('span')
					->add_child( $nav_title )
					;
				break;

			case 'day':
				if( in_array($this->range(), $enabled) ){
					$form = HC_Lib::form()
						->set_input( 'date', 'date' )
						;

					$form->set_values( 
						array(
							'date'	=> $start_date,
							)
						);

					$out = HC_Html_Factory::widget('form')
						->add_attr('action', $this->submit_to() )
						;

					$out
						->add_child(
							HC_Html_Factory::widget('grid')
								->set_scale('xs')
								->add_child(
									$form->input('date')
									, 9
									)
								->add_child(
									HC_Html_Factory::element('input')
										->add_attr('type', 'submit')

										->add_attr('title', HCM::__('OK') )
										->add_attr('value', HCM::__('OK') )
										
										->add_style('btn')
										->add_style('nowrap')
										// ->add_attr('style', 'overflow: hidden;')
										->add_attr('style', 'width: 100%;')
										// ->add_style('btn-submit')
										->add_style('margin', 'l1')
										->add_style('display', 'block')

										->add_style('padding', 2)
										->add_style('border')
										->add_style('rounded')
										->add_style('text-align', 'center')
									, 3
									)
							)
						;
				}
			/* otherwise display it readonly */
				else {
					$out = HC_Html_Factory::element('div')
						->add_child( $t->formatDate() )
						;
				}

				break;

			case 'custom':
			/* now add form */
				if( in_array($this->range(), $enabled) ){
					$form = HC_Lib::form()
						->set_input( 'start_date', 'date' )
						->set_input( 'end_date', 'date' )
						;

					$form->set_values( 
						array(
							'start_date'	=> $start_date,
							'end_date'		=> $end_date,
							)
						);

					$out = HC_Html_Factory::widget('form')
						->add_attr('action', $this->submit_to() )
						;

					$out
						->add_child(
							HC_Html_Factory::widget('grid')
								->set_scale('xs')
								->add_child(
									HC_Html_Factory::widget('grid')
										->set_scale('xs')
										->add_child(
											$form->input('start_date')
											, 5
											)
										->add_child(
											HC_Html_Factory::element('div')
												->add_attr('class', 'hc-form-control-static')
												->add_style('text-align', 'center')
												->add_style('display', 'block')
												->add_child('-')
											, 2
											)
										->add_child(
											$form->input('end_date')
											, 5
											)
									, 9
									)
								->add_child(
									HC_Html_Factory::element('input')
										->add_attr('type', 'submit')

										->add_attr('title', HCM::__('OK') )
										->add_attr('value', HCM::__('OK') )
										
										->add_style('btn')

										->add_style('margin', 'l1')
										->add_style('display', 'block')

										->add_style('padding', 2)
										->add_style('border')
										->add_style('rounded')
										->add_style('text-align', 'center')
									, 3
									)
							)
						;
				}
			/* otherwise display it readonly */
				else {
					$out = HC_Html_Factory::widget('list')
						->add_children_style('inline')
						->add_children_style('margin', 'b1', 'r1')
						->add_child(
							$t->formatDateRange( $start_date, $end_date )
							)
						;
				}

				$out
					->add_style('margin', 'b1', 'r1')
					;

				break;
		}
		
		if( $out ){
			return $out;
		}

	}

	function render( $readonly = FALSE )
	{
		if( (! $readonly) && (! $link = $this->link()) ){
			return 'HC_Html_Widget_Date_Nav: link is not set!';
		}

		$t = HC_Lib::time();

		if( $readonly ){
			$nav_title = $this->_nav_title( $readonly );
			$return = HC_Html_Factory::element('span')
				->add_child( $nav_title )
				->add_style('btn')
				->add_style('padding', 2)
				->add_style('border')
				;
			return $return;
		}

		$before_date = $after_date = 0;
		switch( $this->range() ){
			case 'all':
				$t->setNow();
				$start_date = $end_date = 0;
				// $start_date = $end_date = $t->formatDate_Db();
				break;

			case 'upcoming':
				$t->setNow();
				$start_date = $end_date = 0;
				break;

			case 'custom':
				list( $start_date, $end_date ) = explode('_', $this->date());

				$t->setDateDb($start_date)->modify('-1 day');
				$before_date =  $t->formatDate_Db();

				$t->setDateDb($end_date)->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'day':
				$t->setDateDb( $this->date() );
				$start_date = $end_date = $t->formatDate_Db();

				$t->modify( '-1 day' );
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'week':
				$t->setDateDb( $this->date() );

				$start_date = $t->setStartWeek()->formatDate_Db();
				$end_date = $t->setEndWeek()->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->modify( '-1 week' );
				$t->setStartWeek();
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->setEndWeek();
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'month':
				$t->setDateDb( $this->date() );

				$start_date = $t->setStartMonth()->formatDate_Db();
				$end_date = $t->setEndMonth()->formatDate_Db();

				$month_view = $t->getMonthName() . ' ' . $t->getYear();

				$t->setDateDb( $this->date() );
				$t->modify( '-1 month' );
				$t->setStartMonth();
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->setEndMonth();
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;
		}

	/* range selector */
		$out = HC_Html_Factory::widget('list')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r3', 'b1')
			->add_children_attr('style', 'vertical-align: middle;')
			;

		$_out = array();
		$_out['range_selector'] = $this->_render_range_selector( $start_date, $end_date );
		$_out['current_range'] = $this->_render_current_range( $start_date, $end_date );
		$_out['arrow_buttons'] = $this->_render_arrow_buttons( $before_date, $after_date );

		foreach( $_out as $k => $v ){
			if( $v ){
				$out->add_child( $k, $v );
			}
		}

	/* print view */
		$nav_title = $this->_nav_title();
		$out->add_child( 'print_view', $nav_title );

		$out->add_child_style('range_selector', 'hidden', 'print');
		$out->add_child_style('current_range', 'hidden', 'print');
		$out->add_child_style('arrow_buttons', 'hidden', 'print');
		$out->add_child_style('print_view', 'visible', 'print');
		// $out->add_child_style('range_selector', 'color', 'gray');

		return $out->render();
	}
}