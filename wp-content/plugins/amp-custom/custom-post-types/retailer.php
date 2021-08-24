<?php

/**
 * Register retailer custom post type
 */

function amp_retailers_custom_post_type() {
	$labels = array(
		'name'               => _x( 'Retailers', 'retailer post type general name', 'amp' ),
		'singular_name'      => _x( 'Retailer', 'retailer post type singular name', 'amp' ),
		'add_new'            => _x( 'Add New', 'retailer', 'amp' ),
		'add_new_item'       => __( 'Add New Retailer', 'amp' ),
		'edit_item'          => __( 'Edit Retailer', 'amp' ),
		'new_item'           => __( 'New Retailer', 'amp' ),
		'all_items'          => __( 'All Retailers', 'amp' ),
		'view_item'          => __( 'View Retailer', 'amp' ),
		'search_items'       => __( 'Search Retailers', 'amp' ),
		'not_found'          => __( 'No Retailers found', 'amp' ),
		'not_found_in_trash' => __( 'No Retailers found in the Trash', 'amp' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Retailers'
	);

	$args = array(
		'labels'                => $labels,
		'description'           => 'Retailers',
		'public'                => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-store',
		'supports'              => array( 'title', 'editor', 'excerpt', 'custom-fields' ),
		'has_archive'           => true,
		'capability_type'       => array( 'retailer', 'retailers' ),
		'map_meta_cap'          => false,
		'taxonomies'            => array( 'post_tag' ),
		'show_in_rest'          => true,
		'rest_base'             => 'retailers',
		'show_in_nav_menus'     => true,
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	);
	register_post_type( 'retailer', $args );

	$tax_args = array(
		'hierarchical'          => true,
		'labels'                => array(
			'name'          => 'Retailer categories',
			'singular_name' => 'Category',
			'menu_name'     => 'Categories',
		),
		'capabilities'          => compile_term_capabilities( 'retailer-categories' ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'retailer-categories',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rewrite'               => array(
			'slug'         => 'retailer-category',
			'hierarchical' => true
		)
	);

	register_taxonomy( 'retailer_category', 'retailer', $tax_args );


	register_taxonomy( 'terminal', 'retailer', array(
		'hierarchical'          => true,
		'singular_label'        => 'Terminal',
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'terminals',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'rewrite'               => array(
			'slug'         => 'terminal',
			'hierarchical' => true
		),
		'capabilities'          => compile_term_capabilities( 'terminals' ),
		'labels'                => array(
			'name'              => __( 'Terminals' ),
			'singular_name'     => __( 'Terminal' ),
			'menu_name'         => __( 'Terminals' ),
			'edit_item'         => __( 'Edit Terminal' ),
			'update_item'       => __( 'Update Terminal' ),
			'add_new_item'      => __( 'Add New Terminal' ),
			'new_item_name'     => __( 'New Terminal Name' ),
			'parent_item'       => __( 'Parent Terminal' ),
			'parent_item_colon' => __( 'Parent Terminal:' ),
		),
	) );

}

add_action( 'init', 'amp_retailers_custom_post_type' );


function amp_assign_retailer_capabilities() {
	$roles = [ 'administrator' ];
	$posts = [
		'retailer' => [ 'retailer', 'retailers' ],
	];
	$terms = [ 'retailer-categories', 'terminals' ];
	foreach ( $roles as $the_role ) {
		$role = get_role( $the_role );
		foreach ( $posts as $post ) {
			$capabilities = compile_post_type_capabilities( $post[0], $post[1] );
			foreach ( $capabilities as $capability ) {
				$role->add_cap( $capability );
			}
		}

		foreach ( $terms as $term ) {
			$capabilities = compile_term_capabilities( $term );
			foreach ( $capabilities as $capability ) {
				$role->add_cap( $capability );
			}
		}
	}
}


add_action( 'admin_init', 'amp_assign_retailer_capabilities' );
