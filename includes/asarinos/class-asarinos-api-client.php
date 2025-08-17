<?php
/**
 * API client for Esti CRM
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_API_Client {
    
    private static $company_id = '7239';
    private static $token = 'baa805dc38';
    private static $base_url = 'https://app.esticrm.pl/apiClient';
    
    /**
     * Get houses from Esti API
     */
    public static function get_houses() {
        $url = self::$base_url . '/offer/list?company=' . self::$company_id . '&token=' . self::$token . '&take=1000';
        
        $response = wp_remote_get($url, array(
            'timeout' => 60,
            'headers' => array(
                'User-Agent' => 'Asarinos Plugin/' . ASARINOS_VERSION
            )
        ));
        
        if (is_wp_error($response)) {
            return array('data' => array());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data ? $data : array('data' => array());
    }
    
    /**
     * Get exported offers numbers
     */
    public static function get_exported_offers_numbers() {
        $url = self::$base_url . '/offer/exported-list?company=' . self::$company_id . '&token=' . self::$token . '&take=1000';
        
        $response = wp_remote_get($url, array(
            'timeout' => 60,
            'headers' => array(
                'User-Agent' => 'Asarinos Plugin/' . ASARINOS_VERSION
            )
        ));
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($data && isset($data['success']) && $data['success'] && isset($data['data'])) {
            $numbers = array();

            foreach ($data['data'] as $offer) {
                if (isset($offer['id'])) {
                    $numbers[] = $offer['id'];
                }
            }

            return $numbers;
        }

        return array();
    }
    
    /**
     * Get database houses
     */
    public static function get_db_houses() {
        $args = array(
            'post_type' => 'property',
            'numberposts' => -1,
            'post_status' => 'any'
        );
        
        $posts_list = get_posts($args);
        $posts = array('ID' => array(), 'property_id' => array(), 'date' => array());
        
        foreach ($posts_list as $post) {
            if ($post->ID == 401713 || $post->ID == 401714) continue;
            
            $posts['ID'][$post->post_excerpt] = $post->ID;
            $posts['property_id'][] = $post->post_excerpt;
            $posts['date'][$post->post_excerpt] = $post->post_date;
        }
        
        return $posts;
    }
}