<?php
class HC_Html_Widget_Day_Grid extends HC_Html_Element
{
	protected $time_min = 0;
	protected $time_max = 86400;
	protected $slots = array();

	function __construct()
	{
		parent::__construct();

		$conf = HC_App::app_conf();
		$this->time_min = $conf->get('time_min');
		$this->time_max = $conf->get('time_max');
		$this->time_unit = 5*60;
	}

	public function slots()
	{
		return $this->slots;
	}

	public function add_slot( $start, $duration, $content )
	{
		$slot = new stdClass();
		$slot->start	= $start;
		$slot->duration	= $duration;
		$slot->content	= $content;
		$slot->offset	= 0;
		$this->slots[] = $slot;
	}

	public function render()
	{
		$out = HC_Html_Factory::element('div')
			// ->add_style('padding', 2)
			// ->add_style('border')
			->add_children_style('margin', 'b1')
			;

	/* slots */
		$this_length = $this->time_max - $this->time_min;
		$top = 0;

		$slots = $this->slots();

	/* split by rows */
		$rows = array();
		foreach( $slots as $slot ){
			/* find suitable row */
			$my_row = count($rows);
			for( $ri = 0; $ri < count($rows); $ri++ ){
				$failed_row = FALSE;
				foreach( $rows[$ri] as $check_slot ){
					if( 
						( $check_slot->start < ($slot->start + $slot->duration) ) &&
						( ($check_slot->start + $check_slot->duration) > $slot->start)
						){
						$failed_row = TRUE;
					}
					if( $failed_row ){
						break;
					}
				}
				if( ! $failed_row ){
					$my_row = $ri;
					break;
				}
			}
			if( ! isset($rows[$my_row]) ){
				$rows[$my_row] = array();
			}
			$rows[$my_row][] = $slot;
		}

	/* add offset */
		for( $ri = 0; $ri < count($rows); $ri++ ){
			for( $si = 0; $si < count($rows[$ri]); $si++ ){
				$check_with = $si ? ($rows[$ri][$si-1]->start + $rows[$ri][$si-1]->duration) : $this->time_min;
				$offset = $rows[$ri][$si]->start - $check_with;
				$rows[$ri][$si]->offset = $offset;
			}
		}

		foreach( $rows as $row ){
			$row_view = HC_Html_Factory::widget('grid')
				->set_scale('xs')
				->set_gutter(1)
				// ->add_style('border')
				;

			foreach( $row as $slot ){
				$left = floor( 100 * 100 * (($slot->start - $this->time_min) / $this_length ) ) / 100;

				$slot_duration = $slot->duration;
				if( $slot_duration + $slot->start > $this->time_max ){
					$slot_duration = $this->time_max - $slot->start;
				}

				$width = floor( 100 * 100 * ($slot_duration / $this_length ) ) / 100;
				$offset = floor( 100 * 100 * ($slot->offset / $this_length ) ) / 100;

				// echo "left = $left, width = $width, offset = $offset<br>";

				$row_view->add_child(
					$slot->content,
					array( $width . '%', $offset . '%')
					);
			}
			$out->add_child( $row_view );
		}

		return $out->render();
	}
}
?>