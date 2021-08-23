<?php

if ( ! function_exists( 'amp_customize_primary_nav' ) ) {
	function amp_customize_primary_nav( $title, $item, $args, $depth ) {
		if ( $args->theme_location == 'primary' ) {
			$svg_icon_name = preg_replace( '/\s|\+/', '-', strtolower( wp_strip_all_tags( $title ) ) );

			$svg_icon = '<img src="' . amp_image_uri( 'navigation/' . $svg_icon_name . '.svg' ) . '" alt="' . $title . '">';

			return sprintf(
				'%1$s<span class="menu-item__title">%2$s</span>',
				$svg_icon,
				$title
			);
		}

		return $title;
	}
}
add_filter( 'nav_menu_item_title', 'amp_customize_primary_nav', 10, 4 );
