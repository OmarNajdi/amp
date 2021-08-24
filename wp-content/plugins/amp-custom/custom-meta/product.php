<?php

function wf_add_product_meta() {
	register_post_meta( 'product', '_external_vendor_data', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	) );

	register_post_meta( 'product', '_external_vendor_modifications', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	) );

	register_post_meta( 'product', '_product_start_time', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	) );

	register_post_meta( 'product', '_product_end_time', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'string',
	) );

	register_post_meta( 'product', '_is_active', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'boolean',
	) );

	register_post_meta( 'product', '_is_purchasable', array(
		'show_in_rest' => true,
		'single'       => true,
		'type'         => 'boolean',
	) );
}

add_action( 'init', 'wf_add_product_meta' );
