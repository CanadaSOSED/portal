<div class="mainNav">
	<div class="row">
		<div class="social-icons col-md-3">
			<ul>
				<li><a href="https://www.facebook.com/StudentsOfferingSupport" target="_blank"><img alt="facebook" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154317/facebook.png" width="15" height="15"></a></li>
				<li><a href="https://www.instagram.com/studentsofferingsupport/" target="_blank"><img alt="instagram" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154317/instagram.png" width="15" height="15"></a></li>
				<li><a href="https://www.linkedin.com/groups/1845827" target="_blank"><img alt="linkedin" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154318/linkedin.png" width="15" height="15"></a></li>
				<li><a href="https://twitter.com/SOSheadoffice" target="_blank"><img alt="twitter" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154318/twitter.png" width="15" height="15"></a></li>
			</ul>
		</div>
		<div class="col-md-9">
			<?php wp_nav_menu(
				array(
					'menu' => 'SOSVolu', //ismaramenu 2018-08-23 it was not working without it
					'theme_location'  => '',
					'container_class' => 'collapse navbar-collapse',
					'container_id'    => 'primaryNav',
					'menu_class'      => 'ml-auto sf-menu',
					'fallback_cb'     => '',
					'menu_id'         => '',
					'walker'          => new WP_Bootstrap_Navwalker(),
				)
				); ?>
		</div>
	</div>
</div>

<!--navbar-nav-->
