<?php wp_nav_menu(
				array(
					'menu' 						=> 'Login',
					'theme_location'  => 'primary',
					'container_class' => '',
					'container_id'    => '',
					'menu_class'      => 'jb-menu',
					'fallback_cb'     => '',
					'menu_id'         => 'topLogin',
					'walker'          => new WP_Bootstrap_Navwalker(),
				)
); ?>

<!--navbar-nav-->
