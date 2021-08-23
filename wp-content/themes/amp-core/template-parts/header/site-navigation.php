<?php wp_nav_menu( array(
	'theme_location'  => 'primary',
	'menu_class'      => 'primary-navigation',
	'container'       => 'nav',
	'container_class' => 'primary-navigation-wrapper',
	'items_wrap'      => '<ul class="%2$s">%3$s</ul>',
	'depth'           => 1,
) );
