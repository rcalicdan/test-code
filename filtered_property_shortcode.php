<?php
/**
 * Filtered Properties Shortcode
 * Main shortcode file that coordinates the filtered property display
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-query.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-renderer.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/filtered-property-query.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/filtered-property-renderer.php';

/**
 * Filtered properties shortcode handler
 * 
 * @param array $atts Shortcode attributes
 * @return string Rendered HTML output
 */
function asarinos_filtered_properties_shortcode($atts) {
    $atts = shortcode_atts([
        'type' => '',           
        'transaction' => '',   
        'count' => 10,        
        'price_min' => '',
        'price_max' => '',
        'area_min' => '',
        'area_max' => '',
        'bedrooms' => '',
        'city' => ''
    ], $atts, 'asarinos_filtered_properties');
    
    $current_page = get_query_var('paged') ?: 1;

    $filters = array_filter([
        'type' => $atts['type'],
        'transaction' => $atts['transaction'],
        'price_min' => $atts['price_min'],
        'price_max' => $atts['price_max'],
        'area_min' => $atts['area_min'],
        'area_max' => $atts['area_max'],
        'bedrooms' => $atts['bedrooms'],
        'city' => $atts['city']
    ], function($value) {
        return $value !== '' && $value !== null;
    });
    
    $result = AsarinosFilteredPropertyQuery::get_filtered_properties(
        $filters, 
        intval($atts['count']), 
        $current_page
    );
    
    $pagination_info = [
        'total' => $result['total'],
        'max_pages' => $result['max_pages'],
        'current_page' => $current_page
    ];
    
    // Render output
    ob_start();
    AsarinosFilteredPropertyRenderer::render_filtered_properties(
        $result['properties'], 
        $pagination_info
    );
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('asarinos_filtered_properties', 'asarinos_filtered_properties_shortcode');