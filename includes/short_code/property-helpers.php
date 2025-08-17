<?php
/**
 * Property Helper Functions
 * Contains utility functions for property data processing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AsarinosPropertyHelpers {
    
    /**
     * Format price for display
     * 
     * @param string $price Raw price value
     * @return string Formatted price
     */
    public static function format_price($price) {
        if (empty($price)) {
            return '';
        }
        
        // Remove last 3 characters and add 'zł'
        return substr($price, 0, -3) . 'zł';
    }
    
    /**
     * Get transaction type label
     * 
     * @param string $transaction_code Transaction code
     * @return string Transaction label
     */
    public static function get_transaction_label($transaction_code) {
        switch ($transaction_code) {
            case '131':
                return 'Sprzedaż';
            case '132':
                return 'Wynajem';
            default:
                return '';
        }
    }
    
    /**
     * Get property excerpt
     * 
     * @param object $property Property post object
     * @param int $length Maximum length of excerpt
     * @return string Property excerpt
     */
    public static function get_property_excerpt($property, $length = 100) {
        if (empty($property->post_content)) {
            return '';
        }
        
        $excerpt = substr(strip_tags($property->post_content), 0, $length);
        return $excerpt . '...';
    }
    
    /**
     * Get contact name
     * 
     * @param array $meta Property metadata
     * @return string Full contact name
     */
    public static function get_contact_name($meta) {
        $firstname = isset($meta['contactFirstname']) ? $meta['contactFirstname'] : '';
        $lastname = isset($meta['contactLastname']) ? $meta['contactLastname'] : '';
        
        return trim($firstname . ' ' . $lastname);
    }
    
    /**
     * Get property background image style
     * 
     * @param int $property_id Property post ID
     * @return string CSS style attribute
     */
    public static function get_background_image_style($property_id) {
        if (has_post_thumbnail($property_id)) {
            $image_url = get_the_post_thumbnail_url($property_id);
            return 'style="background-image: url(' . esc_url($image_url) . ')"';
        }
        
        return '';
    }
}