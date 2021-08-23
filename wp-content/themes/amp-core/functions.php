<?php

require 'inc/navigation.php';
require 'inc/helpers.php';

if ( ! function_exists( 'theme_setup' ) ) {
	function theme_setup() {
		add_theme_support( 'html5', array(
			'search-form',
			'gallery',
			'caption'
		) );
		add_theme_support( 'title-tag' );
	}
}
add_action( 'after_setup_theme', 'theme_setup' );


if ( ! function_exists( 'amp_load_styles_and_scripts' ) ) {
	function amp_load_styles_and_scripts() {
		$timestamp = filemtime( get_template_directory() . '/style.css' );
		wp_enqueue_style( 'amp-style', get_template_directory_uri() . '/style.css', false, $timestamp );
		wp_enqueue_style( 'typekit', 'https://use.typekit.net/kac7brd.css', false );


		// Remove Block Editor Styles
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS

	}
}
add_action( 'wp_enqueue_scripts', 'amp_load_styles_and_scripts' );


if ( ! function_exists( 'amp_dequeue_script' ) ) {
	function amp_dequeue_script() {
		wp_dequeue_script( 'wp-embed' );
	}
}
add_action( 'wp_footer', 'amp_dequeue_script' );


// Remove WP EMOJI
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
