<?php
/**
 * Main Page Shortcode
 * Displays the latest 3 properties in a featured layout
 */


if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-query.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-renderer.php';
require_once plugin_dir_path(__FILE__) . 'includes/short_code/property-helpers.php';

/**
 * Create shortcode asarinos_main_page
 */
function asarinos_main_page_shortcode($atts) {
    // Get properties data
    $properties = AsarinosPropertyQuery::get_latest_properties(3);
    
    if (empty($properties)) {
        return '<p>No properties found.</p>';
    }
    
    ob_start();
    
    AsarinosPropertyRenderer::render_properties_section($properties);
    
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('asarionos_mainpage', 'asarinos_main_page_shortcode');