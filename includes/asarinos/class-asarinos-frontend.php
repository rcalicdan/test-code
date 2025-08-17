<?php
/**
 * Frontend functionality class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Frontend {
    
    public function __construct() {
        add_action('woocommerce_single_product_summary', array($this, 'show_property_description'), 20);
        add_action('wp_ajax_asarinos_search', array($this, 'ajax_search'));
        add_action('wp_ajax_nopriv_asarinos_search', array($this, 'ajax_search'));
    }
    
    /**
     * Show property description on single product page
     */
    public function show_property_description() {
        global $post;

        if ($post->post_type !== 'property') {
            return;
        }

        $meta = get_post_meta($post->ID);

        echo '<style>.woocommerce-product-details__short-description {display: none}</style>';

        $fields = array(
            'properties_status' => __('Typ oferty:', 'asarinos'),
            'properties_type' => __('Rynek:', 'asarinos'),
            'properties_price' => __('Cena:', 'asarinos'),
            'properties_area_size' => __('Powierzchnia:', 'asarinos'),
            'properties_bedrooms' => __('Sypialnie:', 'asarinos'),
            'properties_bathrooms' => __('Łazienki:', 'asarinos'),
            'properties_garages' => __('Garaze:', 'asarinos'),
            'properties_rooms' => __('Pokoje:', 'asarinos'),
            '661342957' => __('Telefon agenta:', 'asarinos'),
            'properties_email' => __('Email agenta:', 'asarinos')
        );

        echo '<div class="asarino_property_details">';
        
        foreach ($fields as $field_key => $field_label) {
            if (isset($meta[$field_key]) && !empty($meta[$field_key][0])) {
                echo '<b>' . $field_label . '</b> ' . esc_html($meta[$field_key][0]) . '<br />';
            }
        }
        
        echo '</div>';
    }
    
    /**
     * Handle AJAX search
     */
    public function ajax_search() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'asarinos_search_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }

        $filters = $this->build_search_filters();
        $args = $this->build_query_args($filters);
        
        $query = new WP_Query($args);
        $properties = array();

        foreach ($query->posts as $post) {
            $properties[] = array_merge(
                array(
                    'title' => $post->post_title,
                    'content' => $post->post_content,
                    'url' => get_permalink($post->ID),
                    'thumb' => get_the_post_thumbnail_url($post->ID)
                ),
                get_post_meta($post->ID)
            );
        }

        wp_send_json_success($properties);
    }
    
    /**
     * Build search filters from POST data
     */
    private function build_search_filters() {
        $filters = array();

        // Rooms filter
        if (!empty($_POST['rooms_since'])) {
            $filters[] = array(
                'key' => 'properties_rooms',
                'value' => intval($_POST['rooms_since']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }

        if (!empty($_POST['rooms_to'])) {
            $filters[] = array(
                'key' => 'properties_rooms',
                'value' => intval($_POST['rooms_to']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        // Transaction type
        if (!empty($_POST['transaction'])) {
            $transaction_map = array(
                'sprzedaż' => 131,
                'wynajem' => 132
            );
            
            if (isset($transaction_map[$_POST['transaction']])) {
                $filters[] = array(
                    'key' => 'transaction',
                    'value' => $transaction_map[$_POST['transaction']],
                    'compare' => 'LIKE'
                );
            }
        }

        // Agent filter
        if (!empty($_POST['agent'])) {
            $filters[] = array(
                'key' => 'contactId',
                'value' => sanitize_text_field($_POST['agent']),
                'compare' => '='
            );
        }

        // City filter
        if (!empty($_POST['city'])) {
            $filters[] = array(
                'key' => 'properties_location',
                'value' => sanitize_text_field($_POST['city']),
                'compare' => 'LIKE'
            );
        }

        // Area filters
        if (!empty($_POST['area_from'])) {
            $filters[] = array(
                'key' => 'properties_area_size',
                'value' => intval($_POST['area_from']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }

        if (!empty($_POST['area_to'])) {
            $filters[] = array(
                'key' => 'properties_area_size',
                'value' => intval($_POST['area_to']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        // Price filters
        if (!empty($_POST['price_from'])) {
            $filters[] = array(
                'key' => 'properties_price',
                'value' => intval($_POST['price_from']),
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }

        if (!empty($_POST['price_to'])) {
            $filters[] = array(
                'key' => 'properties_price',
                'value' => intval($_POST['price_to']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        // Price per m2
        if (!empty($_POST['perM2'])) {
            $filters[] = array(
                'key' => 'properties_perM2',
                'value' => intval($_POST['perM2']),
                'compare' => '<=',
                'type' => 'NUMERIC'
            );
        }

        // Market type
        if (!empty($_POST['market'])) {
            $filters[] = array(
                'key' => 'properties_type',
                'value' => sanitize_text_field($_POST['market']),
                'compare' => 'LIKE'
            );
        }

        // Property type
        if (!empty($_POST['type'])) {
            if ($_POST['type'] == 'lokal') {
                $type_filters = array('relation' => 'OR');
                foreach (array('obiekt', 'biuro', 'lokal') as $term) {
                    $type_filters[] = array(
                        'key' => 'properties_type',
                        'value' => $term,
                        'compare' => 'LIKE'
                    );
                }
                $filters[] = $type_filters;
            } else {
                $filters[] = array(
                    'key' => 'properties_type',
                    'value' => sanitize_text_field($_POST['type']),
                    'compare' => 'LIKE'
                );
            }
        }

        // Additional filters
        $additional_filters = array(
            'type2' => 'properties_type',
            'pokoje' => 'properties_rooms',
            'discrict' => 'properties_discrict'
        );

        foreach ($additional_filters as $post_key => $meta_key) {
            if (!empty($_POST[$post_key])) {
                $compare = ($post_key === 'pokoje') ? '>' : 'LIKE';
                $type = ($post_key === 'pokoje') ? 'NUMERIC' : 'CHAR';
                $value = ($post_key === 'pokoje') ? intval($_POST[$post_key]) : sanitize_text_field($_POST[$post_key]);
                
                $filters[] = array(
                    'key' => $meta_key,
                    'value' => $value,
                    'compare' => $compare,
                    'type' => $type
                );
            }
        }

        return $filters;
    }
    
    /**
     * Build WP_Query args from filters
     */
    private function build_query_args($filters) {
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        if (!empty($filters)) {
            $args['meta_query'] = array(
                'relation' => 'AND'
            );
            $args['meta_query'] = array_merge($args['meta_query'], $filters);
        }

        // Handle sorting
        if (!empty($_POST['sort'])) {
            $sort_options = array(
                'price_down' => array('meta_value_num', 'properties_price', 'DESC'),
                'price_up' => array('meta_value_num', 'properties_price', 'ASC'),
                'area_down' => array('meta_value_num', 'properties_area_size', 'DESC'),
                'area_up' => array('meta_value_num', 'properties_area_size', 'ASC'),
                'date_down' => array('ID', '', 'DESC'),
                'date_up' => array('ID', '', 'ASC')
            );

            if (isset($sort_options[$_POST['sort']])) {
                $sort_config = $sort_options[$_POST['sort']];
                $args['orderby'] = $sort_config[0];
                if ($sort_config[1]) {
                    $args['meta_key'] = $sort_config[1];
                }
                $args['order'] = $sort_config[2];
            }
        }

        return $args;
    }
}