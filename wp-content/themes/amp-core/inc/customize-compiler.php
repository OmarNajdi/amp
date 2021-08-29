<?php

if ( is_customize_preview() ) {
	add_action( 'wp_head', function () {
		require_once( get_stylesheet_directory() . '/inc/scssphp/scss.inc.php' );
		$compiler = new ScssPhp\ScssPhp\Compiler();

		$source_scss  = get_stylesheet_directory() . '/sass/style.scss';
		$scssContents = file_get_contents( $source_scss );
		$import_path  = get_stylesheet_directory() . '/sass';
		$compiler->addImportPath( $import_path );

		$variables = [
			'$primary-color'   => get_theme_mod( 'amp_primary_color', '#256eff' ),
			'$secondary-color' => get_theme_mod( 'amp_secondary_color', '#e7e6f7' ),
			'$button-bg-color' => get_theme_mod( 'amp_btn_bg_color', '#150578' ),
			'$hero-image-url'  => "'" . get_theme_mod( 'amp_hero_image_src', '' ) . "'",
		];
		$compiler->addVariables( $variables );

		$css = $compiler->compileString( $scssContents )->getCss();

		if ( ! empty( $css ) && is_string( $css ) ) {
			echo '<style>' . $css . '</style>';
		}
	} );
}

add_action( 'customize_save_after', function () {
	require_once( get_stylesheet_directory() . '/inc/scssphp/scss.inc.php' );
	$compiler = new ScssPhp\ScssPhp\Compiler();

	$source_scss  = get_stylesheet_directory() . '/sass/style.scss';
	$scssContents = file_get_contents( $source_scss );
	$import_path  = get_stylesheet_directory() . '/sass';
	$compiler->addImportPath( $import_path );
	$target_css = get_stylesheet_directory() . '/style.css';

	$variables = [
		'$primary-color'   => get_theme_mod( 'amp_primary_color', '#256eff' ),
		'$secondary-color' => get_theme_mod( 'amp_secondary_color', '#e7e6f7' ),
		'$button-bg-color' => get_theme_mod( 'amp_btn_bg_color', '#150578' ),
		'$hero-image-url'  => "'" . get_theme_mod( 'amp_hero_image_src', '' ) . "'",
	];
	$compiler->addVariables( $variables );

	$css = $compiler->compileString( $scssContents )->getCss();
	if ( ! empty( $css ) && is_string( $css ) ) {
		file_put_contents( $target_css, $css );
	}
} );
