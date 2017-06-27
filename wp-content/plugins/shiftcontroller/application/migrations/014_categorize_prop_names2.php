<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_categorize_prop_names2 extends CI_Migration {
	var $changes = array(
		'working_levels'	=> 'staff:working_levels',
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