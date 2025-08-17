<?php

/**
 * Property management class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Property_Manager
{

    /**
     * Insert or update property
     */
    public static function insert_property($post_id = 0, $post_modified = 0, $update = 0)
    {
        global $_POST;

        $prefix = '_zoner_';
        $agent = isset($_POST['author']) ? $_POST['author'] : '';

        $status_property = 'publish';
        if (is_admin() || true) {
            $status_property = 'publish';
        }

        $insert_property = array(
            'post_excerpt' => sanitize_text_field($_POST['id']),
            'post_title' => sanitize_text_field($_POST['title']),
            'post_name' => sanitize_title_with_dashes($_POST['title'], '', 'save'),
            'post_content' => wp_filter_post_kses($_POST['description']),
            'post_status' => $status_property,
            'post_author' => $agent,
            'post_type' => 'property',
            'post_modified' => $post_modified,
            'post_date' => sanitize_text_field($_POST['date']),
            'post_date_gmt' => $post_modified,
            'post_modified_gmt' => $post_modified
        );

        // Add filter for post modification time
        add_filter('wp_insert_post_data', array(__CLASS__, 'alter_post_modification_time'), 99, 2);

        if ($update) {
            $insert_property['ID'] = $update;
        }

        $post_id = wp_insert_post($insert_property, true);

        remove_filter('wp_insert_post_data', array(__CLASS__, 'alter_post_modification_time'), 99, 2);

        // Update property metadata
        self::update_property_meta($post_id, $prefix, $agent);

        // Add taxonomy
        self::set_taxonomy($post_id);

        // Add custom data
        if (!empty($_POST['data'])) {
            foreach ($_POST['data'] as $key => $val) {
                update_post_meta($post_id, sanitize_key($key), sanitize_text_field($val));
            }
        }

        return $post_id;
    }

    /**
     * Update property metadata
     */
    private static function update_property_meta($post_id, $prefix, $agent)
    {
        $meta_fields = array(
            'properties_price' => 'price',
            'properties_area_size' => 'area',
            'properties_bathrooms' => 'baths',
            'properties_bedrooms' => 'beds',
            'properties_garages' => 'garages',
            'properties_zip' => 'zip',
            'properties_agent' => 'agent',
            'properties_kitchen_type' => 'kitchenType',
            'properties_year_built' => 'yearBuilt',
            'properties_status' => 'status',
            'properties_listing_id' => 'listingId',
            'properties_phone' => 'phone',
            'properties_email' => 'email',
            'properties_rooms' => 'rooms',
            'properties_type' => 'btype',
            'vacantFromDate' => 'vacantFromDate',
            'properties_perM2' => 'zametr',
            'properties_city' => 'city',
            'properties_street' => 'street',
            'properties_latitude' => 'latitude',
            'properties_longitude' => 'longitude',
            'properties_location' => 'location'
        );

        foreach ($meta_fields as $meta_key => $post_key) {
            if (isset($_POST[$post_key])) {
                $value = $_POST[$post_key];

                // Sanitize numeric fields
                if (in_array($post_key, ['area', 'baths', 'beds', 'garages'])) {
                    $value = intval($value);
                } else {
                    $value = sanitize_text_field($value);
                }

                update_post_meta($post_id, $meta_key, $value);
            }
        }

        // Handle special fields
        $country = !empty($_POST['country']) ? esc_attr($_POST['country']) : '';
        $state = !empty($_POST['state']) ? esc_attr($_POST['state']) : '';

        if (!empty($_POST['city'])) {
            $city = (int) esc_attr($_POST['city']);
            wp_delete_term($post_id, 'property_city');
            wp_set_post_terms($post_id, $city, 'property_city');
        }

        // Handle media
        self::handle_property_media($post_id, $prefix);
    }

    /**
     * Handle property media (thumbnails and gallery)
     */
    private static function handle_property_media($post_id, $prefix)
    {
        // Add thumbnail
        if (isset($_POST['thumbnail_url'])) {
            Asarinos_Media::set_thumbnail_from_url($post_id, $_POST['thumbnail_url']);
        }

        // Add gallery
        if (!empty($_POST['gallery'])) {
            $result = array();

            foreach ($_POST['gallery'] as $gallery) {
                $result[] = Asarinos_Media::sideload_image($gallery, $post_id, null, 'src');
            }

            $attachments = get_posts(array(
                'numberposts' => '-1',
                'post_parent' => $post_id,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => 'ASC'
            ));

            $field_name = $prefix . 'gallery';
            $images = array();
            $val = 0;

            foreach ($attachments as $attach) {
                if (!empty($result[$val])) {
                    $images[$attach->ID + 1] = $result[$val];
                }
                $val++;
            }

            update_post_meta($post_id, $field_name, $images);
        }

        // Handle plan
        if (!empty($_POST['plan'])) {
            $plan_url = Asarinos_Media::sideload_image($_POST['plan'], $post_id, null, 'src');
            update_post_meta($post_id, $prefix . 'plan', $plan_url);
        }
    }

    /**
     * Set taxonomy for property
     */
    public static function set_taxonomy($post_id)
    {
        if (!isset($_POST['ptype'])) {
            return;
        }

        $property_status_map = array(
            'ApartmentSale' => 'Sprzedaż Apartamentu',
            'HouseSale' => 'Sprzedaż Domu',
            'ApartmentRental' => 'Wynajem Apartamentu',
            'HouseRental' => 'Wynajem Domu',
            'CommercialSpaceRental' => 'Wynajem Biura',
            'CommercialSpaceSale' => 'Sprzedaż Biura',
            'LotSale' => 'Sprzedaż Działki',
            'LotRental' => 'Wynajem Działki'
        );

        $property_type_map = array(
            'Primary' => 'Rynek Pierwotny',
            'Secondary' => 'Rynek Wtórny'
        );

        $property_status = isset($property_status_map[$_POST['ptype']]) ?
            $property_status_map[$_POST['ptype']] : '';

        $property_type = isset($_POST['market']) && isset($property_type_map[$_POST['market']]) ?
            $property_type_map[$_POST['market']] : 'Rynek Pierwotny';

        // Set terms
        if (!empty($_POST['district'])) {
            wp_set_object_terms($post_id, sanitize_text_field($_POST['district']), 'properties_neighborhood');
        }

        if (!empty($_POST['city'])) {
            wp_set_object_terms($post_id, sanitize_text_field($_POST['city']), 'properties_city');
        }

        if (!empty($_POST['country_name'])) {
            wp_set_object_terms($post_id, sanitize_text_field($_POST['country_name']), 'properties_country');
        }

        if ($property_status) {
            wp_set_object_terms($post_id, $property_status, 'properties_status');
        }

        if (!empty($_POST['market'])) {
            wp_set_object_terms($post_id, sanitize_text_field($_POST['market']), 'properties_type');
        }

        if (!empty($_POST['author_name'])) {
            wp_set_object_terms($post_id, sanitize_text_field($_POST['author_name']), 'properties_labels');
        }
    }

    /**
     * Alter post modification time
     */
    public static function alter_post_modification_time($data, $postarr)
    {
        if (!empty($postarr['post_modified']) && !empty($postarr['post_modified_gmt'])) {
            $data['post_modified'] = $postarr['post_modified'];
            $data['post_modified_gmt'] = $postarr['post_modified_gmt'];
        }
        return $data;
    }

    /**
     * Add property from API data
     */
    public static function add_property($property_data, $id = 0)
    {
        // Sanitize and prepare $_POST data
        $_POST = array();
        $_POST['id'] = sanitize_text_field($property_data['id']);
        $_POST['date'] = sanitize_text_field($property_data['updateDate']);
        $_POST['title'] = sanitize_text_field($property_data['portalTitle']);
        $_POST['originId'] = sanitize_text_field($property_data['originId']);
        $_POST['data'] = $property_data;
        $_POST['description'] = wp_kses_post($property_data['description']);
        $_POST['currency'] = sanitize_text_field($property_data['priceCurrency']);
        $_POST['price_format'] = sanitize_text_field($property_data['description']);
        $_POST['price'] = sanitize_text_field($property_data['price']);
        $_POST['country_name'] = sanitize_text_field($property_data['locationCountryName']);
        $_POST['address'] = sanitize_text_field($property_data['locationStreetName']);
        $_POST['city'] = sanitize_text_field($property_data['locationCityName']);
        $_POST['street'] = sanitize_text_field($property_data['locationStreetName']);
        $_POST['district'] = sanitize_text_field($property_data['locationPlaceName']);
        $_POST['location'] = sanitize_text_field($property_data['locationPlaceName'] . ' ' . $property_data['locationProvinceName'] . ' ' . $property_data['locationDistrictName'] . ' ' . $property_data['locationCommuneName'] . ' ' . $property_data['locationCityName']);
        $_POST['latitude'] = sanitize_text_field($property_data['locationLatitude']);
        $_POST['longitude'] = sanitize_text_field($property_data['locationLongitude']);
        $_POST['area'] = sanitize_text_field($property_data['areaTotal']);
        $_POST['area_unit'] = 'm';
        $_POST['phone'] = sanitize_text_field($property_data['contactPhone']);
        $_POST['email'] = sanitize_email($property_data['contactEmail']);
        $_POST['images'] = array_map('esc_url_raw', $property_data['pictures']);
        $_POST['gallery'] = array_map('esc_url_raw', $property_data['pictures']);
        $_POST['thumbnail_url'] = isset($_POST['gallery'][0]) ? $_POST['gallery'][0] : '';
        $_POST['zametr'] = sanitize_text_field($property_data['pricePermeter']);
        $_POST['btype'] = sanitize_text_field($property_data['typeName']);

        self::insert_property($property_data['id'], $property_data['addDate'], $id);

        $_POST['gallery'] = null;
        $_POST['plan'] = null;
    }
}
