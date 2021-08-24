<?php


if ( ! function_exists( 'amp_get_environment_type' ) ) {
	function amp_get_environment_type(): string {
		$environment = $_SERVER['HTTP_HOST'];
		switch ( $environment ) {
			case preg_match( '/dev/i', $environment ) == 1:
				return 'development';
				break;

			case preg_match( '/stage/i', $environment ) == 1:
				return 'staging';
				break;

			case preg_match( '/test/i', $environment ) == 1:
				return 'local';
				break;

			default:
				return 'production';
				break;
		}
	}
}
