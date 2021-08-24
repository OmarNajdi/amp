<?php
/**
 * Plugin Name:       Airport Marketplace Platform - Custom Meta
 * Plugin URI:        https://aviamedia.com
 * Description:       Add custom meta, custom post types, and custom taxonomies to Airport Marketplace Platform theme
 * Version:           0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Omar Najdi
 * Author URI:        https://omarnajdi.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       amp-custom
 */

include 'helpers/_utilities.php';
include 'custom-post-types/retailer.php';
include 'custom-meta/product.php';
include 'custom-meta/retailer.php';

function amp_custom_activate() {
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'amp_custom_activate' );

function amp_custom_deactivate() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'amp_custom_deactivate' );
