<?php
$user_level = $user ? $user->level : 0;

$menu = HC_Html_Factory::widget('main_menu')
	->set_menu( $menu_conf )
	->set_disabled( $disabled_panels )
	->set_current( $this_uri )
	->set_root( $user_level . '/' )
	;

$menu
	->add_style('hidden', 'print')
	;
echo $menu->render();
?>