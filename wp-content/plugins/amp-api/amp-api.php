<?php
/**
 * Plugin Name:       Airport Marketplace Platform - API
 * Plugin URI:        https://aviamedia.com
 * Description:       Add sync functionality and API requests to Airport MarketPlace Platform theme
 * Version:           0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Omar Najdi
 * Author URI:        https://omarnajdi.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       amp-api
 */


include 'settings/env.php';
include 'settings/api.php';

include 'sync/sync_retailers.php';
include 'sync/sync_products.php';


register_activation_hook( __FILE__, 'amp_api_activate' );

function amp_api_activate() {
	if ( ! wp_get_scheduled_event( 'sync_retailers_hook' ) ) {
		wp_schedule_event( time(), 'daily', 'sync_retailers_hook' ); // ToDo: change recurrence
	}
	if ( ! wp_get_scheduled_event( 'sync_products_hook' ) ) {
		wp_schedule_event( time(), 'daily', 'sync_products_hook' ); // ToDo: change recurrence
	}
}

function amp_api_deactivate() {
	if ( wp_get_scheduled_event( 'sync_retailers_hook' ) ) {
		wp_clear_scheduled_hook( 'sync_retailers_hook' );
	}
	if ( wp_get_scheduled_event( 'sync_products_hook' ) ) {
		wp_clear_scheduled_hook( 'sync_products_hook' );
	}
}

register_deactivation_hook( __FILE__, 'amp_api_deactivate' );


// Hook Syncs
add_action( 'sync_retailers_hook', 'sync_retailers' );
add_action( 'sync_products_hook', 'sync_products' );
