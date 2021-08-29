<?php
/**
 * Plugin Name:       Airport Marketplace Platform - Order Now
 * Plugin URI:        https://aviamedia.com
 * Description:       Add Order Now Functionality to Airport Marketplace Platform theme
 * Version:           0.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Omar Najdi
 * Author URI:        https://omarnajdi.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       amp-ordernow
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'OrderNow' ) ) :

	class OrderNow {

		protected $terminal;

		public function __construct() {

			if ( ! defined( 'OrderNow' ) ) {
				define( 'OrderNow', true );
			}

			// Set the Current terminal if provided
			add_action( 'init', [ $this, 'setTerminalCookie' ] );
		}

		public function getFilterPretext() {
			return _x( get_theme_mod( 'filter_pretext', 'Order Now to' ), 'Filter Pretext', 'amp-ordernow' );
		}

		public function getTerminalPickerLink(): string {
			return sprintf( '<a id="TerminalFilterModalLink" href="#">%s</a>', $this->getTerminalName() );
		}

		public function getTerminalName(): string {
			return $this->getTerminal() == "all" ? _x( 'All Terminals', 'Filter All Terminals', 'amp-ordernow' )
				: $this->getTerminal()->name;
		}


		public function getTerminal() {
			if ( $this->getTerminalSlug() != 'all' ) {
				$terminal = get_term_by( 'slug', $this->getTerminalSlug(), 'terminal' );
			}

			return ! empty( $terminal ) ? $terminal : "all";
		}


		public function getTerminalSlug() {
			$iata = get_field( 'iata', 'option' );

			return $_GET['t'] ?? $_COOKIE["{$iata}_terminal-filter"] ?? 'all';
		}

		public function setTerminalCookie() {
			if ( isset( $_GET['t'] ) ) {
				$iata = get_field( 'iata', 'option' );
				setcookie( "{$iata}_terminal-filter", $_GET['t'], time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
			}
		}

	}


	function ordernow() {
		global $ordernow;

		// Instantiate only once.
		if ( ! isset( $ordernow ) ) {
			$ordernow = new OrderNow();
		}

		return $ordernow;
	}

	// Instantiate.
	ordernow();

endif; // class_exists check
