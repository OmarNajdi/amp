<?php

// ToDo: Cleanup
function sync_products() {
	global $wpdb;

	// Setting Variables
	$env     = amp_get_environment_type();
	$airport = get_field( 'iata', 'option' );


	// Changes List
	$logger = [];


	$retailers_args = array(
		'post_type'      => 'retailer',
		'fields'         => 'ids',
		'post_status'    => array( 'publish', 'draft' ),
		'posts_per_page' => - 1,
	);


	$retailers_query = new WP_Query( $retailers_args );
	$amp_retailers   = $retailers_query->get_posts();

	if ( ! empty( $amp_retailers ) ) {
		foreach ( $amp_retailers as $amp_retailer ) {

			$grab_waypoint_id = get_field( 'grab_retailer_id', $amp_retailer );

			if ( $grab_waypoint_id ) {

				$url = amp_api_get_route_url( 'products', $env, $grab_waypoint_id );

				$response = wp_remote_get( $url );


				if ( is_array( $response ) && ! is_wp_error( $response ) ) {
					$body = $response['body'];

					$values = json_decode( $body );

					$products = $values->inventoryItemMains;

					$amp_retailer_products = $wpdb->get_col( "select post_id from $wpdb->postmeta where meta_key = 'grab_retailer_id' and meta_value = '$grab_waypoint_id'" );

					// Remove URW Retailer from List
					$do_not_delete_list = [ $amp_retailer ];


					foreach ( $products as $product ) {
						$grab_product_id   = $product->inventoryItemID;
						$grab_product_name = $product->inventoryItemName;
						$amp_product_id    = $wpdb->get_var( "select post_id from $wpdb->postmeta where meta_key = 'grab_product_id' and meta_value = '$grab_product_id' LIMIT 1" );

						// ToDo: Delete Products

						if ( $amp_product_id ) {
							// UPDATE EXISTING PRODUCT


							$do_not_delete_list[] = $amp_product_id;


							// Update Price
							$change_price = update_post_meta( $amp_product_id, '_regular_price',
								$product->inventoryItemSubs[0]->cost );
							update_post_meta( $amp_product_id, '_price', $product->inventoryItemSubs[0]->cost );
							if ( $change_price ) {
								error_log( $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Price changed to ' . $product->inventoryItemSubs[0]->cost . ' - ' . get_the_title( $amp_product_id ) );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Price changed to ' . $product->inventoryItemSubs[0]->cost . ' - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}


							// Update IsActive
							$change_is_active = update_post_meta( $amp_product_id, '_is_active',
								$product->inventoryItemAvailable );
							if ( $change_is_active ) {
								$is_active = $product->inventoryItemAvailable ? "Active" : "Not Active";
								error_log( $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - ' . get_the_title( $amp_product_id ) . ' - Active tag changed to ' . $is_active . ' - ' . get_the_title( $amp_product_id ) );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Active tag changed to ' . $is_active . ' - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}

							// Update Purchasable
							$change_is_purchasable = update_post_meta( $amp_product_id, '_is_purchasable',
								$product->inventoryItemAvailableAndInsideTimeWindow );
							if ( $change_is_purchasable ) {
								$is_purchasable = $product->inventoryItemAvailableAndInsideTimeWindow ? "Purchasable" : "Not Purchasable";
								error_log( $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Purchasable tag changed to ' . $is_purchasable . ' - ' . get_the_title( $amp_product_id ) );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Purchasable tag changed to ' . $is_purchasable . ' - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}


							$change_alcohol_tag = update_post_meta( $amp_product_id, '_is_alcohol',
								$product->bAlcohol ?? 0 );
							if ( $change_alcohol_tag ) {
								$alcoholic = $product->bAlcohol ? "Alcoholic" : "Non-alcoholic";
								error_log( $grab_waypoint_id . ' - ' . $amp_product_id . ' - Alcohol tag changed to ' . $alcoholic );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Alcohol tag changed to ' . $alcoholic . ' - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}


							// Update Addons
							$change_addons = add_or_update_product_addons( $product->inventoryMainOptionChoice,
								$amp_product_id );
							if ( $change_addons ) {
								error_log( $grab_waypoint_id . ' - ' . $amp_product_id . ' - Addons were updated' );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Addons were updated - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}


							// Update External Vendor Data
							$external_vendor_data        = json_encode( $product,
								JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
							$change_external_vendor_data = update_post_meta( $amp_product_id, '_external_vendor_data',
								$external_vendor_data );
							if ( $change_addons ) {
								error_log( $grab_waypoint_id . ' - ' . $amp_product_id . ' - External Vendor Data was updated' );
								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - External Vendor Data was updated - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
							}


							// EXPERIMENTAL: Update Tag Order
							$tag_order = is_array( $product->inventoryTitles ) ? $product->inventoryTitles[0]->inventoryTitleOrder : null;
							if ( ! is_null( $tag_order ) ) {
								$change_tag_order = update_post_meta( $amp_product_id, '_tag_order', $tag_order );
								if ( $change_addons ) {
									error_log( $grab_waypoint_id . ' - ' . $amp_product_id . ' - Tag Order was updated' );
									$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Tag Order was updated - ' . get_the_title( $amp_product_id ) . ' - ' . get_permalink( $amp_product_id );
								}
							}


							// ToDo: Remove
							// EXPERIMENTAL: Update Menu Order
//							$change_menu_order = wp_update_post( array(
//									'ID'         => $amp_product_id,
//									'menu_order' => $product->inventoryOrder,
//								)
//							);


							// ToDo: Only if External Vendor Data is updated, then do the rest of the checks

							// ToDo: Update Name
							// ToDo: Update Description
							// ToDo: Update Type
							// ToDo: Update Virtual
							// ToDo: Update Start Time
							// ToDo: Update End Time
							// ToDo: Update Tags
							// ToDo: Update Variations
							// ToDo: Update Image


						} else {
							// CREATE A NEW PRODUCT

							$amp_product_id = create_product( $product, $amp_retailer, $grab_waypoint_id );

							if ( $amp_product_id ) {

								$do_not_delete_list[] = $amp_product_id;

								// Add Variations
								if ( count( $product->inventoryItemSubs ) > 1 ) {
									add_product_variations( $product->inventoryItemSubs, $amp_product_id,
										$grab_product_name );
								}

								// Add AddOns
								add_or_update_product_addons( $product->inventoryMainOptionChoice, $amp_product_id );


								// Add Image
								if ( ! empty( $product->imageV2->product_1_1 ) ) {
									add_product_image( $product->imageV2->product_1_1, $amp_product_id,
										$grab_product_name );
								}

								$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - New Product - ' . $grab_product_name . ' - ' . get_permalink( $amp_product_id );
								error_log( $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - New Product - ' . $grab_product_name );
							} else {
								error_log( 'Failed to create a new product ' . $grab_waypoint_id . '-' . $grab_product_id );
							}

						}

						// REMOVE
//						wp_set_object_terms( $amp_product_id, 'Dine', 'product_cat' );
					}

					// DELETE UNDETECTED PRODUCTS

					// Remove Retailer ID and Existing Products From List
					$delete_list = array_diff( $amp_retailer_products, $do_not_delete_list );

					foreach ( $delete_list as $product_id ) {
						$grab_product_id = get_field( 'grab_product_id', $product_id );
						if ( $grab_product_id ) {
							$logger[] = $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' -  Product Deleted - ' . get_the_title( $product_id ) . ' - ' . $product_id;
							error_log( $grab_waypoint_id . ' - ' . $airport . ' - ' . $grab_product_id . ' - Product Deleted - ' . get_the_title( $product_id ) . ' - ' . $product_id );
							wp_delete_post( $product_id, true );
						}
					}

				}
			}


		}
	}

	error_log( 'Products Sync Complete' );
}


function create_product( $product, $amp_retailer, $grab_waypoint_id ) {

	$product_employee_only = ! empty( $product->bEmployeeItem );

	$product_name = $product_employee_only ? 'EMPLOYEE ONLY - ' . $product->inventoryItemName : $product->inventoryItemName;

	$product_status = $product_employee_only ? 'draft' : 'publish';

	$product_type        = count( $product->inventoryItemSubs ) > 1 ? 'variable' : 'simple';
	$product_slug        = sanitize_title( $product->inventoryItemName . '-' . get_post_field( 'post_name',
			$amp_retailer ) );
	$product_virtual     = $product_type == 'simple' ? 'yes' : 'no';
	$product_description = $product->inventoryItemDescription;
	$product_price       = $product->inventoryItemSubs[0]->cost;
	$product_SKU         = $grab_waypoint_id . '-' . $product->inventoryItemID;

	// Meta Data
	$product_grab_retailer_id     = $grab_waypoint_id;
	$product_grab_product_id      = $product->inventoryItemID;
	$product_start_time           = $product->startTimeLocalString;
	$product_end_time             = $product->endTimeLocalString;
	$product_external_vendor_data = json_encode( $product, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$product_is_active            = $product->inventoryItemAvailable;
	$product_is_purchasable       = $product->inventoryItemAvailableAndInsideTimeWindow;
//	$product_retailers            = $amp_retailer;
	$product_is_alcohol = $product->bAlcohol;
	$menu_order         = $product->inventoryOrder;
//	$product_category             = 'Dine';

	$product_tag = is_array( $product->inventoryTitles ) ? $product->inventoryTitles[0]->inventoryTitleDescription : null;
	$tag_order   = $product_tag ? $product->inventoryTitles[0]->inventoryTitleOrder : null;


	$post_id = wp_insert_post( array(
			'post_title'   => $product_name,
			'post_name'    => $product_slug,
			'post_type'    => 'product',
			'post_status'  => $product_status,
			'post_content' => $product_description,
			'post_excerpt' => $product_description,
			'menu_order'   => $menu_order,
		)
	);

//	wp_set_object_terms( $post_id, $product_category, 'product_cat' ); // Set up its categories
	wp_set_object_terms( $post_id, $product_type, 'product_type' ); // set product is simple/variable/grouped

	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_virtual', $product_virtual );
	update_post_meta( $post_id, '_price', $product_price );
	update_post_meta( $post_id, '_regular_price', $product_price );
	update_post_meta( $post_id, '_sku', $product_SKU );
	update_post_meta( $post_id, '_product_attributes', array() );

	// Special Meta
	update_post_meta( $post_id, 'grab_retailer_id', $product_grab_retailer_id );
	update_post_meta( $post_id, 'grab_product_id', $product_grab_product_id );
	update_post_meta( $post_id, '_product_start_time', $product_start_time );
	update_post_meta( $post_id, '_product_end_time', $product_end_time );
	update_post_meta( $post_id, '_external_vendor_data', $product_external_vendor_data );
	update_post_meta( $post_id, '_is_active', $product_is_active );
	update_post_meta( $post_id, '_is_purchasable', $product_is_purchasable );
//	update_post_meta( $post_id, 'product_retailers', $product_retailers );
	update_post_meta( $post_id, '_is_alcohol', $product_is_alcohol );
	update_post_meta( $post_id, '_tag_order', $tag_order );

	// Set Tags
	if ( $product_tag ) {
		wp_set_object_terms( $post_id, $product_tag, 'product_tag' );
	}

	return $post_id;
}


function add_product_variations( $variations, $product_id, $product_name ) {

	// Create the Variations Attribute
	$product_attributes_data['select-a-size'] = array(
		'name'         => 'Select a size',
		'value'        => implode( ' | ', wp_list_pluck( $variations, 'inventoryItemSubName' ) ),
		'position'     => '0',
		'is_visible'   => '1',
		'is_variation' => '1',
		'is_taxonomy'  => '0'
	);

	update_post_meta( $product_id, '_product_attributes', $product_attributes_data );


	foreach ( $variations as $variation ) {
		$variation_post = array(
			'post_title'  => $variation->inventoryItemSubName,
			'post_name'   => sanitize_title( $product_name . '-' . $variation->inventoryItemSubName ),
			'post_status' => 'publish',
			'post_parent' => $product_id,
			'post_type'   => 'product_variation',
			'guid'        => ''
		);

		$variation_post_id = wp_insert_post( $variation_post );

		update_post_meta( $variation_post_id, 'attribute_select-a-size', $variation->inventoryItemSubName );
		update_post_meta( $variation_post_id, '_price', $variation->cost );
		update_post_meta( $variation_post_id, '_regular_price', $variation->cost );
		update_post_meta( $variation_post_id, '_virtual', 'yes' );
		update_post_meta( $variation_post_id, 'total_sales', 0 );
		update_post_meta( $variation_post_id, '_stock_status', 'instock' );
	}
}


function add_or_update_product_addons( $addons, $amp_product_id ): bool {


	$choices = get_product_addons( $addons->choices, 'choices' );
	$options = get_product_addons( $addons->options, 'options' );

	return update_post_meta( $amp_product_id, '_product_addons', array_merge( $choices, $options ) );
}

function get_product_addons( $choices, $choices_or_options = 'choices' ): array {

	$choices_array = [];
	if ( count( $choices ) > 0 ) {

		// Get the right word for attributes (Either Choice or Option)
		$addonName  = ( $choices_or_options == 'choices' ) ? 'choice' : 'option';
		$addonNames = ( $choices_or_options == 'choices' ) ? 'Choices' : 'Options'; //Plural

		foreach ( $choices as $position => $choice ) {
			if ( $choice->{$addonName . 'GroupAvailable'} ) {

				$choice_options = [];

				foreach ( $choice->{'inventory' . $addonNames} as $choice_option ) {
					$choice_options[] = [
						'label'      => $choice_option->{$addonName . 'Description'},
						'price'      => $choice_option->{$addonName . 'Cost'} > 0 ? $choice_option->{$addonName . 'Cost'} : '',
						'image'      => '',
						'price_type' => 'flat_fee',
					];
				}

				$type    = 'multiple_choice';
				$display = 'radiobutton';

				if ( $choices_or_options == 'options' && $choice->optionSelection != 1 ) {
					$type    = 'checkbox';
					$display = 'select';
				}

				$choices_array[] = [
					'name'               => $choice->{$addonName . 'GroupName'},
					'title_format'       => 'label',
					'description_enable' => 0,
					'description'        => '',
					'type'               => $type,
					'display'            => $display,
					'position'           => $position,
					'required'           => $choices_or_options == 'choices' ? 1 : 0,
					'adjust_price'       => 0,
					'price_type'         => 'flat_fee',
					'price'              => '',
					'min'                => 0,
					'max'                => 0,
					'options'            => $choice_options
				];
			}
		}
	}

	return $choices_array;
}

function add_product_image( $image_url, $amp_product_id, $product_name ) {
	// Ensure url is valid.

	$image_url = esc_url_raw( 'https://images.poweredbyservy.com/CursusMenuImages' . $image_url );


	if ( ! function_exists( 'download_url' ) ) {
		include_once ABSPATH . 'wp-admin/includes/file.php';
	}

	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		include_once ABSPATH . 'wp-admin/includes/image.php';
	}


	$file_array         = array();
	$file_array['name'] = basename( current( explode( '?', $image_url ) ) );

	// Download file to temp location.
	$file_array['tmp_name'] = download_url( $image_url );

	// Do the validation and storage stuff.
	$file = wp_handle_sideload( $file_array, [ 'test_form' => false ] );

	$info = wp_check_filetype( $file['file'] );


	$attachment = array(
		'post_mime_type' => $info['type'],
		'guid'           => $file['url'],
		'post_parent'    => $amp_product_id,
		'post_title'     => $product_name,
		'post_content'   => '',
	);

	$attachment_id = wp_insert_attachment( $attachment, $file['file'], $amp_product_id );
	if ( ! is_wp_error( $attachment_id ) ) {
		wp_update_attachment_metadata( $attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $file['file'] ) );
	}

	set_post_thumbnail( $amp_product_id, $attachment_id );
}
