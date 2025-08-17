<?php
/**
 * Property Query Handler
 * Handles all database queries for properties
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AsarinosPropertyQuery {
    
    /**
     * Get latest properties
     * 
     * @param int $count Number of properties to retrieve
     * @return array Array of property post objects
     */
    public static function get_latest_properties($count = 3) {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => $count,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish'
        );
        
        $query = new WP_Query($args);
        return $query->posts;
    }
    
    /**
     * Get property metadata
     * 
     * @param int $property_id Property post ID
     * @return array Property metadata
     */
    public static function get_property_meta($property_id) {
        return array(
            'price' => get_post_meta($property_id, 'price', true),
            'transaction' => get_post_meta($property_id, 'transaction', true),
            'apartmentBedroomNumber' => get_post_meta($property_id, 'apartmentBedroomNumber', true),
            'properties_bathrooms' => get_post_meta($property_id, 'properties_bathrooms', true),
            'properties_garages' => get_post_meta($property_id, 'properties_garages', true),
            'parkingSpaces' => get_post_meta($property_id, 'parkingSpaces', true),
            'areaTotal' => get_post_meta($property_id, 'areaTotal', true),
            'contactFirstname' => get_post_meta($property_id, 'contactFirstname', true),
            'contactLastname' => get_post_meta($property_id, 'contactLastname', true),
        );
    }
}