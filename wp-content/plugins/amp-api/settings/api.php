<?php

if ( ! function_exists( 'amp_api_get_domain' ) ) {
	function amp_api_get_domain( $env = 'local' ): string {
		return ( $env == "production" ) ? "https://grabmobilewebtop.com/Cursus/" : "https://grabmobilestagev2.com/Cursus/";
	}
}


if ( ! function_exists( 'amp_api_get_endpoint' ) ) {
	function amp_api_get_endpoint( $entity = 'retailers' ): string {
		switch ( $entity ) {
			case "retailers":
				return "CursusPortalV2_PartnerDirect_GrabActiveAirportsWithStores";
			case "products":
				return "CursusWeb_GetStoreInventoryV2";
			case "order":
				return "Cursus_PartnerDirect_GetOrderStatus";
			default:
				_doing_it_wrong( 'amp_api_get_endpoint', 'wrong entity call', '0.1' );

				return "";
		}
	}
}


if ( ! function_exists( 'amp_api_get_email' ) ) {
	function amp_api_get_email(): string {
		return "westfield@getgrab.com";
	}
}


if ( ! function_exists( 'amp_api_get_password' ) ) {
	function amp_api_get_password( $env = 'local' ): string {
		return ( $env == "production" ) ? "6cdef919cd1ebe352102632bb4d8fb1b" : "0ad7ba21deee0e9df4b201cdff4e5bb3";
	}
}

if ( ! function_exists( 'amp_api_get_route_url' ) ) {
	function amp_api_get_route_url( $entity = 'retailers', $env = 'local', $arg = "" ): string {

		$domain   = amp_api_get_domain( $env );
		$endpoint = amp_api_get_endpoint( $entity );
		$email    = amp_api_get_email();
		$password = amp_api_get_password( $env );

		switch ( $entity ) {
			case 'retailers':
				$param = 'airportIdent';
				break;
			case 'products':
				$param = 'storeWaypointID';
				break;
			case 'order':
				$param = 'orderID';
				break;
			default:
				_doing_it_wrong( 'amp_api_get_route_url', 'wrong entity call', '0.1' );
				exit();
		}


		return esc_url_raw( $domain . $endpoint . '?email=' . $email . '&sessionID=' . $password . '&kobp=' . $password . '&' . $param . '=' . $arg );

	}
}

