<?php

function amp_customize_register( $wp_customize ) {

	// Settings
	$wp_customize->add_setting( 'amp_primary_color', array(
		'default' => '#256eff', //Secondary Color
	) );

	$wp_customize->add_setting( 'amp_secondary_color', array(
		'default' => '#e7e6f7', //Secondary Color
	) );

	$wp_customize->add_setting( 'amp_btn_bg_color', array(
		'default' => '#150578', //Secondary Color
	) );

	$wp_customize->add_setting( 'amp_hero_image_src', array(
		'default-image' => '',
	) );


	// Section
	$wp_customize->add_section( 'amp_theme_options', array(
		'title'    => __( 'Theme Options', 'amp-core' ),
		'priority' => 30,
	) );


	// Add Settings to Sections
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'amp_link_primary_color', array(
		'label'    => __( 'Primary Color', 'amp-core' ),
		'section'  => 'amp_theme_options',
		'settings' => 'amp_primary_color',
	) ) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'amp_link_btn_bg_color', array(
		'label'    => __( 'Primary Color', 'amp-core' ),
		'section'  => 'amp_theme_options',
		'settings' => 'amp_btn_bg_color',
	) ) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'amp_link_secondary_color', array(
		'label'    => __( 'Secondary Color', 'amp-core' ),
		'section'  => 'amp_theme_options',
		'settings' => 'amp_secondary_color',
	) ) );

	$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'amp_link_hero_image_src', array(
		'label'    => __( 'Hero Image', 'amp-core' ),
		'section'  => 'amp_theme_options',
		'settings' => 'amp_hero_image_src',
	) ) );


}

add_action( 'customize_register', 'amp_customize_register' );
