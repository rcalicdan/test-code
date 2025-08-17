<?php
/**
 * Filtered Property Query Handler
 * Handles database queries for filtered properties
 */

if (!defined('ABSPATH')) {
    exit;
}

class AsarinosFilteredPropertyQuery extends AsarinosPropertyQuery {
    
    /**
     * Get filtered properties based on criteria
     */
    public static function get_filtered_properties($filters = [], $count = 10, $page = 1) {
        $meta_query = ['relation' => 'AND'];
        $tax_query = ['relation' => 'AND'];
        
        // Property type mapping
        $property_type_map = [
            'apartments' => 'ApartmentSale',
            'houses' => 'HouseSale', 
            'commercial' => 'CommercialSpaceSale',
            'lots' => 'LotSale',
            'rental' => ['ApartmentRental', 'HouseRental', 'CommercialSpaceRental', 'LotRental']
        ];
        
        // Transaction type mapping
        $transaction_map = [
            'sale' => '131',
            'rent' => '132'
        ];
        
        // Build meta query
        self::add_type_filter($meta_query, $filters, $property_type_map);
        self::add_transaction_filter($meta_query, $filters, $transaction_map);
        self::add_numeric_filters($meta_query, $filters);
        self::add_taxonomy_filters($tax_query, $filters);
        
        $args = [
            'post_type' => 'property',
            'posts_per_page' => $count,
            'paged' => $page,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
            'meta_query' => $meta_query
        ];
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        $query = new WP_Query($args);
        
        return [
            'properties' => $query->posts,
            'total' => $query->found_posts,
            'max_pages' => $query->max_num_pages
        ];
    }
    
    private static function add_type_filter(&$meta_query, $filters, $property_type_map) {
        if (empty($filters['type'])) return;
        
        if (isset($property_type_map[$filters['type']])) {
            $type_values = is_array($property_type_map[$filters['type']]) 
                ? $property_type_map[$filters['type']] 
                : [$property_type_map[$filters['type']]];
            
            $meta_query[] = [
                'key' => 'ptype',
                'value' => $type_values,
                'compare' => 'IN'
            ];
        }
    }
    
    private static function add_transaction_filter(&$meta_query, $filters, $transaction_map) {
        if (empty($filters['transaction'])) return;
        
        if (isset($transaction_map[$filters['transaction']])) {
            $meta_query[] = [
                'key' => 'transaction',
                'value' => $transaction_map[$filters['transaction']],
                'compare' => '='
            ];
        }
    }
    
    private static function add_numeric_filters(&$meta_query, $filters) {
        $numeric_filters = [
            'price_min' => ['key' => 'price', 'compare' => '>='],
            'price_max' => ['key' => 'price', 'compare' => '<='],
            'area_min' => ['key' => 'areaTotal', 'compare' => '>='],
            'area_max' => ['key' => 'areaTotal', 'compare' => '<='],
            'bedrooms' => ['key' => 'apartmentBedroomNumber', 'compare' => '>=']
        ];
        
        foreach ($numeric_filters as $filter_key => $config) {
            if (!empty($filters[$filter_key])) {
                $meta_query[] = [
                    'key' => $config['key'],
                    'value' => intval($filters[$filter_key]),
                    'type' => 'NUMERIC',
                    'compare' => $config['compare']
                ];
            }
        }
    }
    
    private static function add_taxonomy_filters(&$tax_query, $filters) {
        if (!empty($filters['city'])) {
            $tax_query[] = [
                'taxonomy' => 'properties_city',
                'field' => 'name',
                'terms' => sanitize_text_field($filters['city'])
            ];
        }
    }
}