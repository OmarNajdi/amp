<?php

get_header();

// Display Hero Image if enabled
if ( get_theme_mod( 'amp_hero_enable', true ) ) {
	get_template_part( 'template-parts/banner/hero' );
}


get_footer();
