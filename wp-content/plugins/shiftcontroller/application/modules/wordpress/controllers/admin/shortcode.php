<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shortcode_Admin_Wordpress_HC_controller extends _Backend_HC_controller
{
	function index()
	{
		$app = $this->config->item('nts_app');

	/* render view */
		$this->layout->set_partial(
			'content', 
			$this->render( 
				'wordpress/admin/shortcode',
				array(
					'shortcode'	=> $app,
					)
				)
			);
		$this->layout();
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */