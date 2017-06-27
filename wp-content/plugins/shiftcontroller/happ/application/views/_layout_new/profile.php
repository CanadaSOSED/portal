<?php
$filter_ui = HC_App::filter_ui();

$nav = HC_Html_Factory::widget('list')
	// ->add_children_style('margin', 'b1')
	;

if( $user && $user->id ){
	$show_profile = 1;

	$auth_user = $this->auth->user();
	$acl = HC_App::acl();
	$acl_user = $acl->user();
	$ri = HC_Lib::ri();

	if( $ri == 'wordpress' ){
		if( is_admin() ){
			// $show_profile = 0;
		}
	}

	// echo "show profile = $show_profile<br>";

	if( $show_profile ){
		$link_profile = 'auth/profile';

		$profile_link = HC_Html_Factory::widget('titled', 'a')
			// ->add_attr('href', '#')
			->add_child( $user->present_title() )
			->add_style('padding', 2)
			->add_style('color', 'darkgray')
			->add_style('btn')
			;

		$profile_details = HC_Html_Factory::widget('list')
			->add_children_style('margin', 'b1')
			->add_style('padding', 2)
			;
		
		$profile_details
			->add_child( 'title', $user->present_title() )
			;

		if( $login_with == 'username' ){
			$profile_details
				->add_child( 'login', $user->username )
				;
		}
		else {
			$profile_details
				->add_child( 'login', $user->email )
				;
		}

		$profile_links = HC_Html_Factory::widget('list')
			->add_style('margin', 't2')
			->add_children_style('display', 'inline-block')
			->add_children_style('margin', 'r2')
			;
		$profile_links
			->add_child(
				HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', HC_Lib::link('auth/profile'))
					// ->add_child( HC_Html::icon('sign-out') )
					->add_child( HCM::__('Edit My Profile') )
					->add_style('padding', 'y1')
					)
			->add_child(
				HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', HC_Lib::link('auth/logout'))
					->add_child( HCM::__('Log Out') )
					->add_style('border', 'left')
					->add_style('padding', 1)
					// ->add_child( HC_Html::icon('sign-out') )
					)
			;

		$profile_details
			->add_child( 'links', $profile_links )
			;

		$profile_details
			->add_child_style('title', 'font-size', 1)
			->add_child_style('login', 'color', 'darkgray')
			;
		
		
		$user_title = $user->present_title();
		$user_title .= ' [';
		if( $login_with == 'username' )
			$user_title .= $user->username;
		else
			$user_title .= $user->email;
		$user_title .= ']';

		$profile_view = HC_Html_Factory::widget('list')
			->add_child( $profile_link )
			->add_child( $profile_details )
			;

		$profile_view = HC_Html_Factory::widget('collapse')
			->set_title( $profile_link )
			->set_content( $profile_details )
			->set_no_caret(FALSE)
			->set_self_hide(TRUE)
			;

		$nav->add_child( 'title', $profile_view );
	}
	if( $auth_user->id != $acl_user->id ){
		if( $ri == 'wordpress' ){
			if( $auth_user->level >= $auth_user->_const('LEVEL_MANAGER') ){
				$app = HC_App::app();
				$admin_url = get_admin_url() . 'admin.php?page=' . $app;

				$nav->add_child(
					'admin',
					HC_Html_Factory::widget('titled', 'a')
						->add_attr('href', $admin_url)
						->add_child( HC_Html::icon('cog') )
						->add_child(
							HC_Html_Factory::element('span')
								->add_child( HCM::__('Admin Area') )
							)
						->add_style('padding', 2)
						->add_style('color', 'darkgray')
						->add_style('btn')
					);
			}
		}
	}
}
else {
	if( $this_method != 'login' ){
		if( ! $filter_ui->is_disabled('login') ){
			$nav->add_child(
				HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', HC_Lib::link('auth/login'))
					->add_child( HC_Html::icon('sign-in') )
					->add_child(
						HC_Html_Factory::element('span')
							->add_child( HCM::__('Log In') )
						)
					->add_style('padding', 2)
					->add_style('color', 'darkgray')
					->add_style('btn')
				);
		}
	}
	else {
		$nav->add_child(
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link())
				->add_child( HC_Html::icon('arrow-left') )
				->add_child( HCM::__('Back To Start Page') )
				->add_style('padding', 2)
				->add_style('color', 'darkgray')
				->add_style('btn')
			);
	}
}

if( $nav->children() ){
	$nav
		->add_style('margin', 'b2')
		->add_style('border', 'bottom')
		->add_style('hidden', 'print')
		;
	echo $nav->render();
}
?>