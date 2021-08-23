<?php

if ( ! function_exists( 'amp_file_uri' ) ) {
	function amp_file_uri( $path ) {
		return esc_url( get_theme_file_uri( $path ) );
	}
}

if ( ! function_exists( 'amp_image_uri' ) ) {
	function amp_image_uri( $path ) {
		return amp_file_uri( '/images/' . $path );
	}
}
