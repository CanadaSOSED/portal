<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_categorize_prop_names extends CI_Migration {
	var $changes = array(
		'wall_schedule_display'	=> 'wall:schedule_display',
		'trade_approval'		=> 'trades:approval',
		);

	public function up()
	{
		if( ! $this->db->table_exists('conf') ){
			return;
		}

		reset( $this->changes );
		foreach( $this->changes as $from => $to ){
			$this->db->where('name', $from);
			$this->db->set('name', $to);
			$this->db->update('conf');
		}
	}

	public function down()
	{
		if( ! $this->db->table_exists('conf') ){
			return;
		}

		reset( $this->changes );
		foreach( $this->changes as $from => $to ){
			$this->db->where('name', $to);
			$this->db->set('name', $from);
			$this->db->update('conf');
		}
	}
}