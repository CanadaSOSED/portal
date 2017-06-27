<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if( ! class_exists('Migration_loginlog') )
{
	class Migration_loginlog extends CI_Migration
	{
		public function up()
		{
			/* nothing here as it is now handled by modules migrations */
		}

		public function down()
		{
		}
	}
}
