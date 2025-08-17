<?php
/**
 * Cron functionality class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Cron {
    
    public function __construct() {
        add_action('hourly_check', array($this, 'check_updates'));
        add_action('wp', array($this, 'schedule_events'));
    }
    
    /**
     * Schedule cron events
     */
    public static function schedule_events() {
        if (!wp_next_scheduled('hourly_check')) {
            wp_schedule_event(time(), 'hourly', 'hourly_check');
        }
    }
    
    /**
     * Clear scheduled events
     */
    public static function clear_scheduled_events() {
        wp_clear_scheduled_hook('hourly_check');
    }
    
    /**
     * Check for updates (cron job)
     */
    public function check_updates() {
        $houses_data = Asarinos_API_Client::get_houses();
        $db_houses = Asarinos_API_Client::get_db_houses();
        
        if (count($houses_data['data']) < 1) {
            return;
        }

        $exported_offers = Asarinos_API_Client::get_exported_offers_numbers();
        $actual_ids = array();

        // Process properties
        foreach ($houses_data['data'] as $property_data) {
            
            // Skip if no title
            if (!$property_data['portalTitle']) {
                continue;
            }

            // Skip if invalid images
            if (!$property_data['pictures'][0] || 
                !preg_match('/^https:\/\//', $property_data['pictures'][0])) {
                continue;
            }

            // Skip if not marked for export
            if (!in_array($property_data['id'], $exported_offers)) {
                continue;
            }

            // Check if property exists
            if (is_array($db_houses['property_id']) && 
                in_array($property_data['id'], $db_houses['property_id'])) {
                
                $actual_ids[] = $property_data['id'];
                
                // Update if needed
                if ($db_houses['date'][$property_data['id']] != $property_data['updateDate']) {
                    wp_delete_post($db_houses['ID'][$property_data['id']]);
                    Asarinos_Property_Manager::add_property($property_data);
                }
            } else {
                // Add new property
                Asarinos_Property_Manager::add_property($property_data);
            }
        }

        // Remove properties no longer in API
        $duplicates = array();
        foreach ($db_houses['property_id'] as $house_id) {
            if (!in_array($house_id, $actual_ids) && $house_id) {
                wp_delete_post($db_houses['ID'][$house_id]);
            }

            $duplicates[$house_id] = isset($duplicates[$house_id]) ? $duplicates[$house_id] + 1 : 1;
            
            if ($duplicates[$house_id] >= 2) {
                wp_delete_post($db_houses['ID'][$house_id]);
            }
        }
    }
}