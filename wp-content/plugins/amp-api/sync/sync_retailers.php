<?php

function sync_retailers() {

	// Setting Variables
	$env     = amp_get_environment_type();
	$airport = get_field( 'iata', 'option' );

	// Check if there's no airport set
	if ( ! $airport ) {
		error_log( 'IATA NOT DETECTED' );
		exit( 'IATA NOT DETECTED' );
	}

	$url      = amp_api_get_route_url( 'retailers', $env, $airport );
	$response = wp_remote_get( $url );

	// Changes List
	$logger = [];

	if ( is_array( $response ) && ! is_wp_error( $response ) ) {

		$body = $response['body'];

		$values = json_decode( $body );

		$terminals = $values->grabAirportMap[0]->grabTerminalMap;

		if ( is_array( $terminals ) ) {
			foreach ( $terminals as $terminal ) {
				$retailers     = $terminal->grabWaypointMap;
				$terminal_name = $terminal->terminalName;
				foreach ( $retailers as $retailer ) {


					// Grab Retailer Data
					$grab_waypoint_id         = $retailer->waypointID;
					$grab_store_name          = $retailer->storeName;
					$open_time                = date( 'g:i A', strtotime( $retailer->localStartTimeString ) );
					$close_time               = date( 'g:i A', strtotime( $retailer->localEndTimeString ) );
					$mobile_order_unavailable = ! $retailer->bStoreIsCurrentlyOpen;
					$tag                      = $retailer->categoryDescription;

					$amp_retailers = get_posts( array(
						'posts_per_page' => 1,
						'post_type'      => 'retailer',
						'post_status'    => [ 'publish', 'draft' ],
						'meta_query'     => array(
							array(
								'key'     => 'grab_retailer_id',
								'value'   => $grab_waypoint_id,
								'compare' => '=',
							),
						)
					) );

					if ( count( $amp_retailers ) > 0 ) {
						foreach ( $amp_retailers as $amp_retailer ) {

							$amp_retailer_id = $amp_retailer->ID;

							// Update Availability
							$change_availability = update_post_meta( $amp_retailer_id, 'mobile_order_unavailable',
								$mobile_order_unavailable );
							if ( $change_availability ) {
								$open_text = $mobile_order_unavailable ? 'CLOSED' : 'OPEN';
								error_log( $grab_waypoint_id . ' - ' . $grab_store_name . ' is now ' . $open_text );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_store_name . ' is now ' . $open_text . ' - ' . get_permalink( $amp_retailer_id );
							}

							// Update Open Time
							$change_open_time = update_post_meta( $amp_retailer_id, 'retailer_open_time', $open_time );
							if ( $change_open_time ) {
								error_log( $grab_waypoint_id . ' - ' . $grab_store_name . ' now opens at ' . $open_time );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_store_name . ' now opens at ' . $open_time . ' - ' . get_permalink( $amp_retailer_id );
							}

							// Update Close Time
							$change_close_time = update_post_meta( $amp_retailer_id, 'retailer_close_time',
								$close_time );
							if ( $change_close_time ) {
								error_log( $grab_waypoint_id . ' - ' . $grab_store_name . ' now closes at ' . $close_time );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_store_name . ' now closes at ' . $close_time . ' - ' . get_permalink( $amp_retailer_id );
							}
						}
					} else {


						$amp_retailer_id = wp_insert_post( array(
							'post_type'  => 'retailer',
							'post_title' => $grab_store_name,
							'tags_input' => $tag,
							'meta_input' => array(
								'grab_retailer_id'         => $grab_waypoint_id,
								'mobile_order_unavailable' => $mobile_order_unavailable,
								'retailer_open_time'       => $open_time,
								'retailer_close_time'      => $close_time,
							),
						) );


						if ( $amp_retailer_id ) {
							// Set Terminal
							wp_set_object_terms( $amp_retailer_id, $terminal_name, 'terminal' );


							error_log( $grab_waypoint_id . ' - ' . $grab_store_name . ' - Store Created' );
							$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_store_name . ' - Store Created - ' . get_permalink( $amp_retailer_id );
						} else {
							error_log( $grab_waypoint_id . ' - ' . $grab_store_name . ' - Store Failed to Create' );
							$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_store_name . ' -  Store Failed to be Created';
						}

					}
				}
			}
		}
	}

	error_log( 'Retailers Sync Complete' );
}
