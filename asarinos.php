<?php
/*
    Plugin Name: Asarinos
    Plugin URI: http://baldbold.eu
    Description: Plugin do aktualizowania listy mieszkań
    Author: Marcin Szymański
    Version: 1.3
    Author URI: http://baldbold.eu
	License: All Rights Reservet 
    */
require 'vendor/autoload.php';
include 'main_page_shortcode.php';

use \Mpdf\Mpdf;

define('ALLOW_UNFILTERED_UPLOADS', true);


function set_post_term($post, $value, $taxonomy)
{
	$term = term_exists($value, $taxonomy);
	$term = wp_insert_term($value,				$taxonomy,				array('slug' => strtolower(str_ireplace(' ', '-', $value))));
}


function asari_media_sideload_image($file, $post_id, $desc = null, $return = 'html')
{
	if (! empty($file)) {

		// Set variables for storage, fix file filename for query strings.
		preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);

		$matches[1] = 'jpg';
		$file_array = array();
		$file_array['name'] = $matches[0] . '.jpg';

		// Download file to temp location.
		//$file_array['tmp_name'] = download_url( $file );
		$saveTo = ABSPATH . '/wp-content/uploads/asarinos' . rand(1, 99999) . '.jpg';
		file_put_contents(
			$saveTo,
			file_get_contents($file)
		);

		$file_array['tmp_name'] = $saveTo;

		//rename($file_array['tmp_name'], $file_array['tmp_name'] . '.jpg');
		//$file_array['tmp_name'] = $file_array['tmp_name'] . '.jpg';

		// If error storing temporarily, return the error.
		if (is_wp_error($file_array['tmp_name'])) {
			return $file_array['tmp_name'];
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload($file_array, $post_id, $desc);

		// If error storing permanently, unlink.
		if (is_wp_error($id)) {
			@unlink($file_array['tmp_name']);
			return $id;
			// If attachment id was requested, return it early.
		} elseif ($return === 'id') {
			return $id;
		}

		$src = wp_get_attachment_url($id);
	}

	// Finally, check to make sure the file has been saved, then return the HTML.
	if (! empty($src)) {
		if ($return === 'src') {
			return $src;
		}
	} else {
		return new WP_Error('image_sideload_failed');
	}
}

// discable image compression
//add_filter( 'jpeg_quality', create_function( '', 'return 100;' ) );

// display only fatal errors not warnings and notices
//error_reporting(E_ERROR);

// not display notices, warning and deprecated
//error_reporting(E_ERROR | E_PARSE);


function set_thumbnail_from_url($post_id, $url)
{
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');
	require_once(ABSPATH . 'wp-admin/includes/image.php');

	// load the image
	$result = asari_media_sideload_image($url, $post_id);

	$attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC'));
	// set image as the post thumbnail
	if (sizeof($attachments) > 0) {
		// set image as the post thumbnail
		set_post_thumbnail($post_id, $attachments[0]->ID);
	}
}

function asarino_insert_attachment($file_handler, $post_id, $setthumb = false)
{
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload($file_handler, $post_id);
	if ($setthumb) set_post_thumbnail($post_id, $attach_id);
	return $attach_id;
}

function asarino_edit_attachments($post_id = 0, $field_name = null, $gallery_files = array(), $exists_files = array(), $post_files = array(), $files_array_prefix = null, $valid_formats = array("jpg", "png", "gif"), $max_file_size = "4MB")
{
	global $post;
	$max_file_size = wp_convert_hr_to_bytes('15MB');
	$attachmnets = $attach_id = $message = array();
	if (empty($post_id)) $post_id = $post->ID;

	if (!empty($exists_files)) {
		$results = array_diff($gallery_files, $exists_files);

		if (!empty($results)) {
			foreach ($results as $res => $val) {
				wp_delete_attachment($res, true);
				unset($gallery_files[$res]);
			}
		}
	} else {
		if (!empty($gallery_files)) {
			foreach ($gallery_files as $res => $val) {
				wp_delete_attachment($res, true);
				unset($gallery_files[$res]);
			}
		}
	}


	if (!empty($exists_files)) {
		foreach ($exists_files as $key => $val) {
			$attachmnets[$key] = esc_url($val);
		}
	}

	if (!empty($post_files['name'])) {
		$tmp_files = $_FILES;
		foreach ($post_files['name'] as $f => $name) {
			if ($post_files['error'][$f] == 4) continue;
			if ($post_files['error'][$f] == 0) {
				if ($post_files['size'][$f] > $max_file_size) {
					$message[] = sprintf(__(' is too large!', 'zoner'),  $name);
					continue;
				} elseif (! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats)) {
					$message[] = sprintf(__(' is not a valid format!', 'zoner'),  $name);
					continue;
				} else {


					if ($post_files['name'][$f]) {
						$file = array();
						$file = array(
							'name' 		=> $post_files['name'][$f],
							'type' 		=> $post_files['type'][$f],
							'tmp_name' 	=> $post_files['tmp_name'][$f],
							'error' 	=> $post_files['error'][$f],
							'size' 		=> $post_files['size'][$f]

						);
					}

					$_FILES = array($files_array_prefix => $file);
					foreach ($_FILES as $file => $array) {
						$attach_id[] = asarino_insert_attachment($file, $post_id);
					}
					$_FILES = $tmp_files;
				}
			}
		}
	}


	if (!empty($attach_id)) {
		foreach ($attach_id as $val) {
			$file_url = wp_get_attachment_url($val);
			$attachmnets[$val] = $file_url;
		}
	}

	update_post_meta($post_id, $field_name, $attachmnets);

	return $message;
}

function alter_post_modification_time($data, $postarr)
{
	if (!empty($postarr['post_modified']) && !empty($postarr['post_modified_gmt'])) {
		$data['post_modified'] = $postarr['post_modified'];
		$data['post_modified_gmt'] = $postarr['post_modified_gmt'];
	}
	return $data;
}

function asarino_set_taxonomy($post_id)
{

	switch ($_POST['ptype']) {
		case 'ApartmentSale':
			$property_status = 'Sprzedaż Apartamentu';
			break;
		case 'HouseSale':
			$property_status = 'Sprzedaż Domu';
			break;
		case 'ApartmentRental':
			$property_status = 'Wynajem Apartamentu';
			break;
		case 'HouseRental':
			$property_status = 'Wynajem Domu';
			break;
		case 'CommercialSpaceRental':
			$property_status = 'Wynajem Biura';
			break;
		case 'CommercialSpaceSale':
			$property_status = 'Sprzedaż Biura';
			break;
		case 'LotSale':
			$property_status = 'Sprzedaż Działki';
			break;
		case 'LotRental':
			$property_status = 'Wynajem Działki';
			break;
	}


	switch ($_POST['market']) {
		case 'Primary':
			$property_type = 'Rynek Pierwotny';
			break;
		case 'Secondary':
			$property_type = 'Rynek Wtórny';
			break;
		default:
			$property_type = 'Rynek Pierwotny';
			break;
	}


	wp_set_object_terms($post_id, $_POST['district'], 'properties_neighborhood');
	wp_set_object_terms($post_id, $_POST['city'], 'properties_city');
	wp_set_object_terms($post_id, $_POST['country_name'], 'properties_country');
	wp_set_object_terms($post_id, $property_status, 'properties_status');
	wp_set_object_terms($post_id, $_POST['market'], 'properties_type');
	wp_set_object_terms($post_id, $_POST['author_name'], 'properties_labels');
}

function asarino_insert_property($post_id = 0, $post_modified = 0, $update = 0)
{
	global $_POST;

	$prefix = '_zoner_';
	$update_post = array();

	$status_property = 'properties';
	if (is_admin() || true) {
		$status_property = 'publish';
	}


	$_POST['author'] = $agent;

	$insert_property = array();
	$insert_property = array(
		'post_excerpt' => $_POST['id'],
		'post_title'   => $_POST['title'],
		'post_name'	   => sanitize_title_with_dashes($_POST['title'], '', 'save'),
		'post_content' => wp_filter_post_kses($_POST['description']),
		'post_status'  => $status_property,
		'post_author'  => $_POST['author'],
		'post_type'	   => 'property',
		'post_modified' => $post_modified,
		'post_date' => $_POST['date'],
		'post_date_gmt'  => $post_modified,
		'post_modified_gmt' => $post_modified
	);

	// add post			
	$post_id = 0;
	add_filter('wp_insert_post_data', 'alter_post_modification_time', 99, 2);

	if ($update) $insert_property['ID'] = $update;

	$post_id = wp_insert_post($insert_property, true);

	remove_filter('wp_insert_post_data', 'alter_post_modification_time', 99, 2);

	//update_post_meta($post_id, $prefix . 'currency', $_POST['currency']);
	//update_post_meta($post_id, $prefix . 'price_format', $_POST['price_format']);
	update_post_meta($post_id, 'properties_price', 	 $_POST['price']);

	$country = $state = '';
	if (!empty($_POST['country'])) $country = esc_attr($_POST['country']);
	if (!empty($_POST['state']))     $state = esc_attr($_POST['state']);

	//update_post_meta($post_id, $prefix . 'country', 	$country);
	//update_post_meta($post_id, $prefix . 'state', 		$state);
	update_post_meta($post_id, 'properties_area_size', 	intval($_POST['area']));
	update_post_meta($post_id, 'properties_bathrooms', 	intval($_POST['baths']));
	update_post_meta($post_id, 'properties_bedrooms', 	intval($_POST['beds']));
	update_post_meta($post_id, 'properties_garages', 	intval($_POST['garages']));
	update_post_meta($post_id, 'properties_zip', 	$_POST['zip']);
	update_post_meta($post_id, 'properties_agent', 	$agent);

	update_post_meta($post_id, 'properties_kitchen_type', $_POST['kitchenType']);
	update_post_meta($post_id, 'properties_year_built', $_POST['yearBuilt']);
	update_post_meta($post_id, 'properties_status', $_POST['status']);
	update_post_meta($post_id, 'properties_listing_id', $_POST['listingId']);
	update_post_meta($post_id, 'properties_phone', $_POST['phone']);
	update_post_meta($post_id, 'properties_agent', $_POST['agent']);
	update_post_meta($post_id, 'properties_email', $_POST['email']);
	update_post_meta($post_id, 'properties_rooms', $_POST['rooms']);
	update_post_meta($post_id, 'properties_type', $_POST['btype']);
	update_post_meta($post_id, 'vacantFromDate', $_POST['vacantFromDate']);
	update_post_meta($post_id, 'properties_perM2', $_POST['zametr']);
	update_post_meta($post_id, 'properties_city', $_POST['city']);
	update_post_meta($post_id, 'properties_street', $_POST['street']);
	update_post_meta($post_id, 'properties_latitude', $_POST['latitude']);
	update_post_meta($post_id, 'properties_longitude', $_POST['longitude']);
	update_post_meta($post_id, 'properties_location', $_POST['location']);

	if (!empty($_POST['city'])) {
		$city = (int) esc_attr($_POST['city']);
		wp_delete_term($post_id, 'property_city');
		wp_set_post_terms($post_id, $city, 'property_city');
	}

	// add thunmnail
	if (isset($_POST['thumbnail_url'])) set_thumbnail_from_url($post_id, $_POST['thumbnail_url']);

	// add gallery
	$result = null;

	foreach ($_POST['gallery'] as $gallery) {
		$result[] = asari_media_sideload_image($gallery, $post_id, null, 'src');
	}
	$attachments = get_posts(array('numberposts' => '-1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC'));

	$field_name    = $prefix . 'gallery';
	$val = 0;
	foreach ($attachments as $attach) {
		if (!empty($result[$val])) $images[$attach->ID + 1] = $result[$val];
		//$images[$attach->ID + 2] = $result[0];
		$val++;
	}
	update_post_meta($post_id, $field_name, $images);

	if (!empty($_POST['plan'])) {
		$_POST['plan'] = asari_media_sideload_image($_POST['plan'], $post_id, null, 'src');
		update_post_meta($post_id, $prefix . 'plan', $_POST['plan']);
	}

	// add taxonomy
	asarino_set_taxonomy($post_id);

	foreach ($_POST['data'] as $key => $val) {
		// update post meta
		update_post_meta($post_id, $key, $val);
	}
	return 1;
}

function get_asari_houses() {}

function get_asari_house($id)
{
	return;
	return $house;
}



function get_esti_houses()
{
	$houses = json_decode(file_get_contents('https://app.esticrm.pl/apiClient/offer/list?company=7239&token=baa805dc38&take=1000'), true);
	return $houses;
}

/**
 * Pobiera listę numerów ofert oznaczonych do eksportu na stronę WWW
 * 
 * @return array Tablica numerów ofert
 */
function get_esti_exported_offers_numbers()
{
	$url = 'https://app.esticrm.pl/apiClient/offer/exported-list?company=7239&token=baa805dc38&take=1000';
	$response = file_get_contents($url);

	if ($response) {
		$data = json_decode($response, true);

		if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
			$numbers = array();

			foreach ($data['data'] as $offer) {
				if (isset($offer['id'])) {
					$numbers[] = $offer['id'];
				}
			}

			return $numbers;
		}
	}

	return array(); // Zwracamy pustą tablicę w przypadku błędu
}

function get_db_houses()
{
	$args = array(
		'post_type'  => 'property',
		'numberposts'       => -1
	);
	$posts_list = get_posts($args);
	foreach ($posts_list as $post) {
		if ($post->ID == 401713 || $post->ID == 401714) continue;
		$posts['ID'][$post->post_excerpt] = $post->ID;
		$posts['property_id'][] = $post->post_excerpt;
		$posts['date'][$post->post_excerpt] = $post->post_date;
	}
	return $posts;
}

function add_property($asari_house, $id = 0)
{


	$_POST['id'] = $asari_house['id'];
	$_POST['date'] = $asari_house['updateDate'];
	$_POST['title'] = $asari_house['portalTitle'];
	$_POST['originId'] = $asari_house['originId'];

	$_POST['data'] = $asari_house;

	$_POST['description'] = $asari_house['description'];
	$_POST['currency'] = $asari_house['priceCurrency'];
	$_POST['price_format'] = $asari_house['description'];
	$_POST['price'] = $asari_house['price'];
	//$_POST['country'] = $asari_house['country']['code'];
	$_POST['country_name'] = $asari_house['locationCountryName'];
	$_POST['address'] = $asari_house['locationStreetName'];
	$_POST['city'] = $asari_house['locationCityName'];
	$_POST['street'] = $asari_house['locationStreetName'];
	$_POST['district'] = $asari_house['locationPlaceName'];
	$_POST['location'] = $asari_house['locationPlaceName'] . ' ' . $asari_house['locationProvinceName'] . ' ' . $asari_house['locationDistrictName'] . ' ' . $asari_house['locationCommuneName'] . ' ' . $asari_house['locationCityName'];
	//$_POST['zip'] = NULL; // asari not giving :(
	$_POST['latitude'] = $asari_house['locationLatitude']; // asari not giving :(
	$_POST['longitude'] = $asari_house['locationLongitude']; // asari not giving :(
	//$_POST['location'] = NULL; // asari not giving :(
	//$_POST['condition'] = $asari_house['buildingCondition']; // tochange to polish
	//$_POST['payment-rent'] = $asari_house['administrativeRent']['amount'];
	//$_POST['rooms'] = $asari_house['apartmentRoomNumber'];
	//$_POST['beds'] = NULL; // asari not giving :(
	//$_POST['baths'] = $asari_house['noOfBathrooms'];
	$_POST['area'] = $asari_house['areaTotal'];
	$_POST['area_unit'] = 'm'; // asari not giving :(
	//if ($asari_house['garage']) $_POST['garages'] = $asari_house['garage'];
	//if ($asari_house['noOfGarageSpaces']) $_POST['garages'] = $asari_house['noOfGarageSpaces'];
	//$_POST['type'] = $asari_house['ownershipType'];
	//$_POST['status'] = $asari_house['status'];
	//$_POST['price_format'] = 0;
	//$_POST['videos'] = $asari_house['videoUrl']; // to check

	// set user
	//$_POST['author'] = $asari_house['contactFirstname'] . ' ' . $asari_house['contactLastname'];
	// to taxonomy
	//$_POST['ptype'] = $asari_house['sectionName'];
	//$_POST['btype'] = $asari_house['buildingType'];
	//$_POST['htype'] = $asari_house['houseType'];
	// featured
	/*$_POST['jaccuzi'] = $asari_house['jacuzzi'];
			$_POST['kablówka'] = $asari_house['cableTelevision'];
			$_POST['internet'] = $asari_house['internet'];
			$_POST['klimatyzacja'] = $asari_house['airConditioning'];
			$_POST['kominek'] = $asari_house['fireplace'];
			$_POST['parkiet'] = $asari_house['spaceFloorList'];
			$_POST['salon'] = $asari_house['livingRoomArea'];
			$_POST['sauna'] = $asari_house['sauna'];
			$_POST['taras'] = $asari_house['noOfTerrace'];
			$_POST['winda'] = $asari_house['elevator'];
			$_POST['alarm'] = $asari_house['alarm'];
			$_POST['domofon'] = $asari_house['intercom'];
			$_POST['monitoring'] = $asari_house['monitoring'];
			$_POST['ochrona'] = $asari_house['security'];
			$_POST['recepcja'] = $asari_house['reception'];
			$_POST['ogród'] = $asari_house['garden'];
			$_POST['balkon'] = $asari_house['noOfBalconies'];
			$_POST['pietro'] = $asari_house['floorNo'];
            $_POST['czynsz'] = $asari_house['anteroomArea'];*/

	// for lands special fields
	/*$_POST['cenem2'] = $asari_house['priceM2']['amount'];
			$_POST['powierzchnia'] = $asari_house['lotArea'];
			$_POST['przeznaczenie'] = $asari_house['lotType'];
			$_POST['ksztalt'] = $asari_house['lotShape'];
			$_POST['woda'] = $asari_house['waterTypeList'][0];
			$_POST['prad'] = $asari_house['electricityStatus'];
            $_POST['gaz'] = $asari_house['gasStatus'];
			$_POST['kanalizacja'] = $asari_house['sewerageTypeList'][0];
			$_POST['ogrzewanie_miejskie'] = $asari_house['urbanCo'];
            $_POST['plan_zag'] = $asari_house['localPlan'];
   /*
    informacje - typ nieruchomości (działka, dom), lokalizacja, cena, powierzchnia, typ działki, cena za m.kw (działki)
       
            
            $_POST['market'] = $asari_house['mortgageMarket'];*/


	// $_POST['kitchenType'] = $asari_house['kitchenType'];
	//$_POST['yearBuilt'] = $asari_house['yearBuilt'];
	//$_POST['status'] = $asari_house['status'];
	//$_POST['listingId'] = $asari_house['listingId'];
	$_POST['phone'] = $asari_house['contactPhone'];
	$_POST['email'] = $asari_house['contactEmail'];

	$_POST['images'] = $asari_house['pictures'];
	$_POST['gallery'] = $asari_house['pictures'];

	// set post thumbnail from first
	$_POST['thumbnail_url'] = $_POST['gallery'][0];




	/*
			switch ($asari_house['mortgageMarket']) {
				case 'Primary' : $_POST['market'] = 'Rynek Pierwotny'; break;
				case 'Secondary' : $_POST['market'] = 'Rynek Wtórny'; break;	
				case null	: 	$_POST['market'] = 'brak informacji'; break;
				default : $_POST['market'] = $asari_house['mortgageMarket'];
			}
		*/
	$_POST['zametr'] = $asari_house['pricePermeter'];
	$_POST['btype'] = $asari_house['typeName'];
	/*$_POST['rokbudowy'] = $asari_house['yearBuilt'];
			
			switch ($asari_house['heatingTypeList'][0]) {
				case 'City' : $_POST['ogrzewanie'] = 'z miasta'; break;
				case null : $_POST['ogrzewanie'] = 'brak informacji';break;
				default	:$_POST['ogrzewanie'] = $asari_house['heatingTypeList'][0]; 		
			}*/

	// get images
	/*foreach ($asari_house['images'] as $image) {
					if ($image['isScheme']) {
						$_POST['plan'] ='https://img.asariweb.pl/normal/' . $image['id'];
						continue;
					}
					$_POST['gallery'][] = 'https://img.asariweb.pl/normal/' . $image['id'];
			}*/
	//if (isset($_POST['gallery'][0])) {
	//array_splice($_POST['gallery'], 0, 1);
	//	}

	asarino_insert_property($asari_house['id'], $asari_house['addDate'], $id);

	// free value
	$_POST['gallery'] = null;
	$_POST['plan'] = null;
}

function asari_admin()
{
	//return;
	echo '<h2>Integracja z Esti</h2><form action="" method="POST">';
	if (isset($_POST['check'])) { // action on click
		$asari_houses = get_esti_houses();

		$db_houses = get_db_houses();
		if (count($asari_houses['data']) < 1) return 0;

		$exported_offers = get_esti_exported_offers_numbers();

		// add properties
		foreach ($asari_houses['data'] as $asari_house) {

			if (! $asari_house['portalTitle']) continue;

			if (
				! $asari_house['pictures'][0] ||
				! preg_match('/^https:\/\//', $asari_house['pictures'][0])
			) continue;

			if (!in_array($asari_house['id'], $exported_offers)) {
				echo $asari_house['portalTitle'] . ' nie jest oznaczona do eksportu.<br />';
				continue; // Pomijamy tę ofertę, jeśli nie jest oznaczona do eksportu
			} else {
				echo $asari_house['id'] . ' jest oznaczona do eksportu.<br />';
			}

			//print_r($asari_house);continue;
			if (is_array($db_houses['property_id']) && @in_array($asari_house['id'], $db_houses['property_id'])) {
				$actual_ids[] = $asari_house['id'];
				echo $asari_house['portalTitle'] . ' jest w bazie pod numerem ID ' . $db_houses['ID'][$asari_house['id']] . '<br />'; // already is in database
				if ($db_houses['date'][$asari_house['id']] != $asari_house['updateDate']) {
					echo $asari_house['portalTitle'] . ' jest aktualizowany.<br />';
					//add_property($asari_house, $db_houses['ID'][$asari_house['id']]);
					wp_delete_post($db_houses['ID'][$asari_house['id']]);
					add_property($asari_house);
				}
			} else {
				echo $asari_house['portalTitle'] . ' zostaje eksportowany.<br />'; // adding to database
				add_property($asari_house);
				//var_dump($asari_house);
			}
		}

		// delete properties
		foreach ($db_houses['property_id'] as $house_id) {
			if (!in_array($house_id, $actual_ids) && $house_id) {
				echo 'Usunięto nieruchomość o numerze: ' . $db_houses['ID'][$house_id] . ' <br />';
				wp_delete_post($db_houses['ID'][$house_id]);
			}

			$dubles[$house_id]++;
			if ($dubles[$house_id] >= 2) {
				echo '<b>Nieruchomość o ID: ' . $db_houses['ID'][$house_id] . ' się dubluje więc została usunięta</b><br />';
				wp_delete_post($db_houses['ID'][$house_id]);
			}
		}
	}

	// panel with options
	if (isset($_POST['hours_update'])) {
		update_option('asarino_hours', $_POST['hours_update']);
		echo 'Zaktualizowano godziny.<br />';
	}
	echo '<br />Podaj pełne godziny w których mają być aktualizacje np: "12,16,19"<br /><form action="" method="POST"><input type="text" id="hours_update" name="hours_update" style="width: 340px" value="' . get_option('asarino_hours') . '">';
	submit_button('Zaktualizuj godziny', 'primary', 'hours', 'yes');
	echo '</form><form action="" method="POST">';
	submit_button('Zaktualizuj ogłoszenia', 'primary', 'check', 'yes');
	echo '</form>';

	//var_dump(download_url('https://static.esticrm.pl/public/images/offers/3934/5836608/48848709_max.jpg'));


}


function asarino_check_updates()
{
	$asari_houses = get_esti_houses();

	$db_houses = get_db_houses();
	if (count($asari_houses['data']) < 1) return 0;


	$exported_offers = get_esti_exported_offers_numbers();

	// add properties
	foreach ($asari_houses['data'] as $asari_house) {

		// if $asari_house['pictures"] first record not contain "https://" at begin then continue
		if (
			! $asari_house['pictures'][0] ||
			! preg_match('/^https:\/\//', $asari_house['pictures'][0])
		) continue;

		if (!in_array($asari_house['id'], $exported_offers)) {
			continue; // Pomijamy tę ofertę, jeśli nie jest oznaczona do eksportu
		}

		// display title and picteres
		echo $asari_house['portalTitle'] . ' ' . $asari_house['pictures'][0] . '<br />';



		//if ( ! $asari_house['availableDescription'] ) continue;
		if (! $asari_house['portalTitle']) continue;
		//print_r($asari_house);continue;
		if (@in_array($asari_house['id'], $db_houses['property_id'])) {
			$actual_ids[] = $asari_house['id'];
			echo $asari_house['portalTitle'] . ' jest w bazie pod numerem ID ' . $db_houses['ID'][$asari_house['id']] . '<br />'; // already is in database
			if ($db_houses['date'][$asari_house['id']] != $asari_house['updateDate']) {
				//echo $asari_house['portalTitle'] . ' jest aktualizowany.<br />'; 
				//add_property($asari_house, $db_houses['ID'][$asari_house['id']]);
				wp_delete_post($db_houses['ID'][$asari_house['id']]);
				add_property($asari_house);
			}
		} else {
			echo $asari_house['portalTitle'] . ' zostaje eksportowany.<br />'; // adding to database
			add_property($asari_house);
			//var_dump($asari_house);
		}
	}

	// delete properties
	foreach ($db_houses['property_id'] as $house_id) {
		if (!in_array($house_id, $actual_ids) && $house_id) {
			////echo 'Usunięto nieruchomość o numerze: ' . $db_houses['ID'][$house_id] . ' <br />';
			wp_delete_post($db_houses['ID'][$house_id]);
		}

		$dubles[$house_id]++;
		if ($dubles[$house_id] >= 2) {
			//echo '<b>Nieruchomość o ID: ' . $db_houses['ID'][$house_id] . ' się dubluje więc została usunięta</b><br />';
			wp_delete_post($db_houses['ID'][$house_id]);
		}
	}
	return; // TODO: later copy and paste from admin
	$asari_houses = get_asari_houses();
	$db_houses = get_db_houses();

	if (count($asari_houses['data']) < 1) return 0;


	// add properties

	$int = 0;
	foreach ($asari_houses['data'] as $asari_house) {

		// for test only
		$int++;
		if ($int > 10) continue;

		// do not add empty
		if (! $asari_house['portalTitle']) continue;

		if (@in_array($asari_house['id'], $db_houses['property_id'])) {
			$actual_ids[] = $asari_house['id'];
			//echo $asari_house['portalTitle'] . ' jest w bazie.<br />'; // already is in database
			if ($db_houses['date'][$asari_house['id']] != $asari_house['updateDate']) {
				//echo $asari_house['portalTitle'] . ' jest aktualizowany.<br />'; 
				//var_dump($db_houses['ID'][$asari_house['id']]);
				//add_property($asari_house, $db_houses['ID'][$asari_house['id']]);
				wp_delete_post($db_houses['ID'][$asari_house['id']]);
				add_property($asari_house);
			}
		} else {
			//echo $asari_house['portalTitle'] . ' zostaje eksportowany.<br />'; // adding to database
			add_property($asari_house);
			//var_dump($asari_house);
		}
	}

	// delete properties
	foreach ($db_houses['property_id'] as $house_id) {
		if (!in_array($house_id, $actual_ids) && $house_id) {
			//echo 'Usunięto nieruchomość o numerze: ' . $db_houses['ID'][$house_id] . ' <br />';
			wp_delete_post($db_houses['ID'][$house_id]);
		}

		$dubles[$house_id]++;
		if ($dubles[$house_id] >= 2) {
			//echo '<b>Nieruchomość o ID: ' . $db_houses['ID'][$house_id] . ' się dubluje więc została usunięta</b><br />';
			wp_delete_post($db_houses['ID'][$house_id]);
		}
	}
}

//cron
register_activation_hook(__FILE__, 'my_activation');
add_action('hourly_check', 'asarino_check_updates');

function my_activation()
{
	if (! wp_next_scheduled('hourly_check')) {
		wp_schedule_event(time(), 'hourly', 'hourly_check');
	}
}
add_action('wp', 'my_activation');
register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation()
{
	wp_clear_scheduled_hook('asarino_check_updates');
}
// end cron	

function asari_admin_actions()
{
	add_options_page("Synchronizacja z Esti", "Synchronizacja z Esti", 1, "Synchronizacja z Esti", "asari_admin");
}

add_action('admin_menu', 'asari_admin_actions');

// add custom post type
function property_post_type()
{
	$labels = array(
		'name'                => 'Nieruchomość',
		'singular_name'       => 'Nieruchomość',
		'menu_name'           => 'Nieruchomości',
		'parent_item_colon'   => 'Nadrzędna oferta',
		'all_items'           => 'Wszystkie oferty',
		'view_item'           => 'Zobacz ofertę',
		'add_new_item'        => 'Dodaj ofertę',
		'add_new'             => 'Dodaj nową',
		'edit_item'           => 'Edytuj ofertę',
		'update_item'         => 'Aktualizuj',
		'search_items'        => 'Szukaj nieruchomości',
		'not_found'           => 'Nie znaleziono',
		'not_found_in_trash'  => 'Nie znaleziono'
	);
	$args = array(
		'labels' => array(
			'name' => __('Properties'),
			'singular_name' => __('Property')
		),
		'rewrite' => array(
			'slug' => 'property',
			'with_front' => true
		),
		'description'         => 'Oferty',
		'labels'              => $labels,
		'supports'            => array('title', 'thumbnail', 'custom-fields', 'editor'),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 8,
		'menu_icon'           => 'dashicons-id-alt',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type('property', $args);
}
add_action('init', 'property_post_type', 0);

// Flush rewrite rules
function rewrite_flush()
{
	property_post_type();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'rewrite_flush');


add_shortcode('show_asari_offers', function () {
	$propertys = get_posts(array('post_type' => 'property', 'numberposts' => 1000));

	foreach ($propertys as $property) {
		@$out .= '<div class="property-box">
			
			<a href="' . $property->guid . '">
                
			<!--<div class="sectionName"></div>    -->            
			<div class="title"><h4>' . $property->post_title . '</h4></div>
			<img class="img" src="' . get_the_post_thumbnail_url($property->ID) . '" alt="">

				<div class="offer_head">
				
				
				 <div class="locality">Kraków, ul. Wrocławska</div>
				
					<div class="line-down">
						<div class="three" title="Powierzchnia"><img src="/wp-content/uploads/2021/vector-square-plus.svg" alt="Powierzchnia"><br>' . get_post_meta($property->ID, 'properties_area_size', 1) . ' m2</div>
						<div class="three" title="Pokoje"><img src="/wp-content/uploads/2021/bed-outline.svg" alt="Pokoje"><br>' . get_post_meta($property->ID, 'properties_bedrooms', 1) . '</div>
						<div class="three money" title="Koszt miesięczny"><img src="/wp-content/uploads/2021/calendar-month.svg" alt="Koszt miesięczny"><br>' . get_post_meta($property->ID, 'properties_price', 1) . ' zł</div>
					</div>
					
				</div><div class="hidden_info">SPRAWDŹ OFERTĘ</div></a>
			</div>';
	}
	return $out;
});

function add_scripts($hook)
{


	wp_enqueue_script('asarinos-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));
	wp_localize_script('asarinos-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

	wp_enqueue_script('leaflet-script', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.3/leaflet.js', array('jquery'));
	wp_enqueue_script('splide-script', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.1/dist/js/splide.min.js', array('jquery'));



	wp_enqueue_script('range-slider-script', 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js', array('jquery'));


	//wp_register_style( 'namespace', 'http://locationofcss.com/mycss.css' );
	wp_enqueue_style('asarinos-style', plugin_dir_url(__FILE__) . 'style.css');
	wp_enqueue_style('range-slider', 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css	');
	wp_enqueue_style('splide-slider-style', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.1/dist/css/splide.min.css');
}

add_action('wp_enqueue_scripts', 'add_scripts');


add_action('wp_ajax_asarinos_search', 'asarinos_search_ajax');
add_action('wp_ajax_nopriv_asarinos_search', 'asarinos_search_ajax');

function asarinos_search_ajax()
{
	global $wpdb;

	//var_dump($_POST);

	$filters = [];
	/*
	$args = array(
		'post_type'  => 'product',
		'meta_query' => array(
			'relation' => 'AND' 
			array(
				'key'     => 'post_code',
				'value'   => '432C',
			),
			array(
				'key'     => 'location',
				'value'   => 'XYZ',
			),
		),
	);

*/
	// rooms
	if (isset($_POST['rooms_since']) && $_POST['rooms_since']) {
		$filters[] = 			array(
			'key'     => 'properties_rooms',
			'value'   => intval($_POST['rooms_since']),
			'compare' => '>=',
			'type'    => 'numeric'
		);
	}
	if (isset($_POST['rooms_to']) && $_POST['rooms_to']) {
		$filters[] = 			array(
			'key'     => 'properties_rooms',
			'value'   => intval($_POST['rooms_to']),
			'compare' => '<=',
			'type'    => 'numeric'
		);
	}
	// transaction
	if (isset($_POST['transaction']) && $_POST['transaction']) {
		switch ($_POST['transaction']) {
			case 'sprzedaż':
				$transaction = 131;
				break;
			case 'wynajem':
				$transaction = 132;
				break;

			default:
				$transaction = '';
		}
		$filters[] = 			array(
			'key'     => 'transaction',
			'value'   => $transaction,
			'compare' => 'LIKE'
		);
	}

	if (isset($_POST['agent']) && $_POST['agent']) {
		$filters[] = 			array(
			'key'     => 'contactId',
			'value'   => $_POST['agent'],
			'compare' => '='
		);
	}

	if (isset($_POST['city']) && $_POST['city']) {
		$filters[] = 			array(
			'key'     => 'properties_location',
			'value'   => $_POST['city'],
			'compare' => 'LIKE'
		);
	}

	if (isset($_POST['area_from']) && $_POST['area_from']) {
		$filters[] = 			array(
			'key'     => 'properties_area_size',
			'value'   => intval($_POST['area_from']),
			'compare' => '>=',
			'type'    => 'numeric'
		);
	}


	if (isset($_POST['area_to']) && $_POST['area_to']) {
		$filters[] = 			array(
			'key'     => 'properties_area_size',
			'value'   => intval($_POST['area_to']),
			'compare' => '<=',
			'type'    => 'numeric'
		);
	}


	if (isset($_POST['price_from']) && $_POST['price_from']) {
		$filters[] = 			array(
			'key'     => 'properties_price',
			'value'   => intval($_POST['price_from']),
			'compare' => '>=',
			'type'    => 'numeric'
		);
	}


	if (isset($_POST['price_to']) && $_POST['price_to']) {
		$filters[] = 			array(
			'key'     => 'properties_price',
			'value'   => intval($_POST['price_to']),
			'compare' => '<=',
			'type'    => 'numeric'
		);
	}

	if (isset($_POST['perM2']) && $_POST['perM2']) {
		$filters[] = 			array(
			'key'     => 'properties_perM2',
			'value'   => intval($_POST['perM2']),
			'compare' => '<=',
			'type'    => 'numeric'
		);
	}

	if (isset($_POST['market']) && $_POST['market']) {
		$filters[] = 			array(
			'key'     => 'properties_type',
			'value'   => $_POST['market'],
			'compare' => 'LIKE',
		);
	}


	if (isset($_POST['type']) && $_POST['type']) {

		// when type is "komercyjne" look for hala or obiekt
		if ($_POST['type'] == 'lokal') {
			$type_filters = array('relation' => 'OR');
			foreach (array('obiekt', 'biuro', 'lokal') as $term) {
				$type_filters[] = array(
					'key'     => 'properties_type',
					'value'   => $term,
					'compare' => 'LIKE',
				);
			}
			$filters[] = $type_filters;
		} else {
			$filters[] = 			array(
				'key'     => 'properties_type',
				'value'   => $_POST['type'],
				'compare' => 'LIKE',
			);
		}
	}

	if (isset($_POST['type2']) && $_POST['type2']) {
		$filters[] = 			array(
			'key'     => 'properties_type',
			'value'   => $_POST['type2'],
			'compare' => 'LIKE',
		);
	}

	if (isset($_POST['pokoje']) && $_POST['pokoje']) {
		$filters[] = 			array(
			'key'     => 'properties_rooms',
			'value'   => intval($_POST['pokoje']),
			'compare' => '>',
			'type'    => 'numeric'
		);
	}

	if (isset($_POST['discrict']) && $_POST['discrict']) {
		$filters[] = 			array(
			'key'     => 'properties_discrict',
			'value'   => $_POST['discrict'],
			'compare' => 'LIKE'
		);
	}

	$args = array(
		'post_type'  => 'property',
		'limit'	=> -1,
		'nopaging' => 'true',
	);

	if (!empty($filters)) {
		$args['meta_query'] = array(
			'relation' => 'AND',
			$filters
		);
	}

	if (!empty($_POST['sort'])) {
		switch ($_POST['sort']) {
			case 'price_down':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = 'properties_price';
				$args['order'] = 'DESC';
				break;
			case 'price_up':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = 'properties_price';
				$args['order'] = 'ASC';
				break;
			case 'area_down':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = 'properties_area_size';
				$args['order'] = 'DESC';
				break;
			case 'area_up':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = 'properties_area_size';
				$args['order'] = 'ASC';
				break;
			case 'date_down':
				$args['orderby'] = 'ID';
				$args['order'] = 'DESC';
				break;
			case 'date_up':
				$args['orderby'] = 'ID';
				$args['order'] = 'ASC';
				break;
		}
	}

	$query = new WP_Query($args);

	$propertys = [];

	foreach ($query->posts as $post) {
		$propertys[] = array_merge(array('title' => $post->post_title, 'content' => $post->post_content, 'url' => get_permalink($post->ID), 'thumb' => get_the_post_thumbnail_url($post->ID)), get_post_meta($post->ID));
	}

	wp_send_json_success($propertys);
	//update_post_meta( $_POST['post_id'], $_POST['fields'], 'extra_fields');

	wp_die();
}


function show_asarino_description()
{

	global $post;

	$meta = get_post_meta($post->ID);

	echo '
	<style>.woocommerce-product-details__short-description {display: none}</style>
	';

	echo '<div class="asarino_property_details">' .
		((isset($meta['properties_status'])) ? '<b>' . __('Typ oferty:', 'asarinos') . '</b> ' . $meta['properties_status'][0] . '<br />' : '') .
		((isset($meta['properties_type'])) ? '<b>' . __('Rynek:', 'asarinos') . '</b> ' . $meta['properties_type'][0] . '<br />' : '') .
		((isset($meta['properties_price'])) ? '<b>' . __('Cena:', 'asarinos') . '</b> ' . $meta['properties_price'][0] . '<br />' : '') .
		((isset($meta['properties_area_size'])) ? '<b>' . __('Powierzchnia:', 'asarinos') . '</b> ' . $meta['properties_area_size'][0] . '<br />' : '') .
		((isset($meta['properties_bedrooms']) && $meta['properties_bedrooms']) ? '<b>' . __('Sypialnie:', 'asarinos') . '</b> ' . $meta['properties_bedrooms'][0] . '<br />' : '') .
		((isset($meta['properties_bathrooms']) && $meta['properties_bathrooms']) ? '<b>' . __('Łazienki:', 'asarinos') . '</b> ' . $meta['properties_bathrooms'][0] . '<br />' : '') .
		((isset($meta['properties_garages'])) ? '<b>' . __('Garaze:', 'asarinos') . '</b> ' . $meta['properties_garages'][0] . '<br />' : '') .
		((isset($meta['properties_rooms'])) ? '<b>' . __('Pokoje:', 'asarinos') . '</b> ' . $meta['properties_rooms'][0] . '<br />' : '') .
		((isset($meta['661342957'])) ? '<b>' . __('Telefon agenta:', 'asarinos') . '</b> ' . $meta['661342957'][0] . '<br />' : '') .
		((isset($meta['properties_email'])) ? '<b>' . __('Email agenta:', 'asarinos') . '</b> ' . $meta['properties_email'][0] . '<br />' : '')
		. '</div>';
}
add_action('woocommerce_single_product_summary', 'show_asarino_description', 20);

add_shortcode('asarinos_search', function ($atts) {
	$propertys = get_posts(array('post_type' => 'product', 'numberposts' => 1000));

	echo '<div class="search-box asarinos">';

	// if $atts main is true then add form action to /oferty and display form tags
	if (isset($atts['main']) && $atts['main'] == 'true') {
		echo '<form action="/oferty" method="GET">';
	}

	echo	'<div class="elementor-row">

			<div class="elementor-row">
					<div class="elementor-col elementor-col-40">
							<div class="elementor-column elementor-col-100">
								<label for="city">Typ Nieruchomości:</label>
								<select name="type" id="type">
									<option value="">-- Wszystkie --</option>
									<option value="mieszkanie">Mieszkania</option>
									<option value="dom">Domy</option>
									<option value="działka">Działki</option>
									<option value="lokal">Lokale</option>
									<option value="hala">Hale</option>
								</select>
							</div>

						<!--<div class="elementor-column elementor-col-100">
							<label for="city">Typ Działki:</label>
							<select name="type2" id="type2">
							<option value="">-- Wybierz Typ Działki --</option>
							<option value="działka">Budowlana</option>
							<option value="Usługowo-mieszkaniowa">Usługowo-mieszkaniowa</option>
							<option value="Pod zabudowę jednorodzinną">Pod zabudowę jednorodzinną</option>
							<option value="Pod zabudowę bliźniaczą">Pod zabudowę bliźniaczą</option>
							<option value="Rekreacyjna">Rekreacyjna</option>
							<option value="Pod zabudowę bliźniaczą">Pod zabudowę bliźniaczą</option>
							<option value="Mieszkaniowa">Mieszkaniowa</option>
							<option value="Inwestycyjna">Inwestycyjna</option>
							<option value="Usługowa">Usługowa</option>
							<option value="Rolna">Rolna</option>
							<option value="Usługowo-mieszkaniowa">Usługowo-mieszkaniowa</option>
							</select>
						</div>-->

						<div class="elementor-row">
							<div class="elementor-column elementor-col-100">
							<label for="city">Lokalizacja:</label><br />
							<input type="text" id="city" value="" name="city" />
							</div>
						</div>
					</div>


				<div class="elementor-col elementor-col-30">

					<div class="elementor-column elementor-col-100">
					<label for="transaction">Transakcja:</label><br />
					<select name="transaction" id="transaction">
					<option value="">-- Wszystkie --</option>
					<option value="sprzedaż">Sprzedaż</option>
					<option value="wynajem">Wynajem</option>
					</select>
					</div>

					<div class="elementor-row">
						<div class="elementor-column elementor-col-50">
							<label for="area_from">Powierzchnia od:</label><br />
							<input type="text" id="area_from" value="" name="area_from" />
						</div>

						<div class="elementor-column elementor-col-50">
							<label for="area_to">Powierzchnia do:</label><br />
							<input type="text" id="area_to" value="" name="area_to" />
						</div>
					</div>
		
					<!--<div class="elementor-column elementor-col-100">
						<label for="perM2">Cena za metr do:</label><br />
						<input type="text" name="perM2" id="perM2" value="" />
					</div>-->
		

				</div>


				<div class="elementor-col elementor-col-30">

					<div class="elementor-row">
						<div class="elementor-column elementor-col-50">
							<label for="price_from">Cena od:</label><br />
							<input type="text" id="price_from" value="" name="price_from" />
						</div>

						<div class="elementor-column elementor-col-50">
							<label for="price_to">Cena do:</label><br />
							<input type="text" id="price_to" value="" name="price_to" />
						</div>
					</div>

					<div class="elementor-row">
						<div class="elementor-column elementor-col-50">
							<label for="rooms_since">Pokoje od:</label><br />
							<input type="text" id="rooms_since" value="" name="rooms_since" />
						</div>

						<div class="elementor-column elementor-col-50">
							
							<label for="rooms_to">Pokoje do:</label><br />
							<input type="text" id="rooms_to" value="" name="rooms_to" />
						</div>
					</div>

					<!--
					<div class="elementor-column elementor-col-100">
					<label for="sort">Sortuj po:</label><br />
					<select name="sort" id="sort">
					<option value="">-- Sortowanie --</option>
					<option value="price_down">Cena malejąco</option>
					<option value="price_up">Cena rosnąco</option>
					<option value="area_down">Powierzchnia malejąco</option>
					<option value="area_up">Powierzchnia rosnąco</option>
					<option value="date_down">Data malejąco</option>
					<option value="date_up">Data rosnąco</option>
					</select>
					</div>
					-->

										<div class="" style="text-align: right;align-bottom;height: 100%; width:100%">
					<input type="submit" id="search_asarino" value="Szukaj" style="width: 100%;" >

				</div>


				</div>

			</div>


		</div>


	<link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.3/leaflet.min.css"/>';

	if (isset($atts['main']) && $atts['main'] == 'true') {
		echo '</form>';
	}



	return $out;

	foreach ($propertys as $property) {
		@$out .= '<div class="property-box">
		
		<a href="' . $property->guid . '">
			
		<!--<div class="sectionName"></div>    -->            
		<div class="title"><h4>' . $property->post_title . '</h4></div>
		<img class="img" src="' . get_the_post_thumbnail_url($property->ID, array(280, 210)) . '" alt="">

			<div class="offer_head">
			
			
			 <div class="locality">Kraków, ul. Wrocławska</div>
			
				<div class="line-down">
					<div class="three" title="Powierzchnia"><img src="/wp-content/uploads/2021/vector-square-plus.svg" alt="Powierzchnia"><br>' . get_post_meta($property->ID, 'properties_area_size', 1) . ' m2</div>
					<div class="three" title="Pokoje"><img src="/wp-content/uploads/2021/bed-outline.svg" alt="Pokoje"><br>' . get_post_meta($property->ID, 'properties_bedrooms', 1) . '</div>
					<div class="three money" title="Koszt miesięczny"><img src="/wp-content/uploads/2021/calendar-month.svg" alt="Koszt miesięczny"><br>' . get_post_meta($property->ID, 'properties_price', 1) . ' zł</div>
				</div>
				
			</div><div class="hidden_info">SPRAWDŹ OFERTĘ</div></a>
		</div>';
	}
	return $out;
});


add_shortcode('asarinos_search_results', function () {
	return '
	<div id="map" style="width: 900px; height: 580px"></div>
	<div id="search_results"> 

	</div>';
});
