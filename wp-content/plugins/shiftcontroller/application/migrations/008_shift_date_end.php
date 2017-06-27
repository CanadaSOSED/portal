<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_shift_date_end extends CI_Migration {
	public function up()
	{
		if( ! $this->db->field_exists('date_end', 'shifts') ){
			$this->dbforge->add_column(
				'shifts',
				array(
					'date_end' => array(
						'type'		=> 'INT',
						'null'		=> FALSE,
						),
					),
				'start'
				);

		// init date end
			$this->db->set('date_end', 'date', FALSE);
			$this->db->update('shifts');

		// now check those that go next day
			$this->db->where('end > ', 24 * 60 * 60);
			$this->db->set('end', 'end - ' . 24 * 60 * 60, FALSE);
			$this->db->update('shifts');

			$affected_count = 0;
			$t = HC_Lib::time();
			$query = $this->db
				->where('start >= ', 'end', FALSE)
				->get('shifts')
				;
			foreach( $query->result_array() as $row ){
				$t->setDateDb( $row['date'] );
				$t->modify('+1 day');
				$date_end = $t->formatDate_Db();

				$this->db
					->where( 'id', $row['id'] )
					->update('shifts',
						array(
							'date_end'	=> $date_end
							)
						)
					; 

				$affected_count++;
			}
		}
	}

	public function down()
	{
	}
}