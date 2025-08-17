<?php
/**
 * Core plugin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Core {
    
    public function __construct() {
        $this->init_hooks();
        $this->init_modules();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init_post_types'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Enable unfiltered uploads for property images
        if (!defined('ALLOW_UNFILTERED_UPLOADS')) {
            define('ALLOW_UNFILTERED_UPLOADS', true);
        }
    }
    
    private function init_modules() {
        new Asarinos_Admin();
        new Asarinos_Frontend();
        new Asarinos_Cron();
        new Asarinos_Shortcodes();
    }
    
    /**
     * Register custom post types
     */
    public function init_post_types() {
        $this->register_property_post_type();
    }
    
    /**
     * Register property post type
     */
    private function register_property_post_type() {
        $labels = array(
            'name' => 'Nieruchomość',
            'singular_name' => 'Nieruchomość',
            'menu_name' => 'Nieruchomości',
            'parent_item_colon' => 'Nadrzędna oferta',
            'all_items' => 'Wszystkie oferty',
            'view_item' => 'Zobacz ofertę',
            'add_new_item' => 'Dodaj ofertę',
            'add_new' => 'Dodaj nową',
            'edit_item' => 'Edytuj ofertę',
            'update_item' => 'Aktualizuj',
            'search_items' => 'Szukaj nieruchomości',
            'not_found' => 'Nie znaleziono',
            'not_found_in_trash' => 'Nie znaleziono'
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
            'description' => 'Oferty',
            'labels' => $labels,
            'supports' => array('title', 'thumbnail', 'custom-fields', 'editor'),
            'taxonomies' => array(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 8,
            'menu_icon' => 'dashicons-id-alt',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        
        register_post_type('property', $args);
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // JavaScript files
        wp_enqueue_script('asarinos-script', ASARINOS_PLUGIN_URL . 'script.js', array('jquery'), ASARINOS_VERSION);
        wp_localize_script('asarinos-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        
        wp_enqueue_script('leaflet-script', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.3/leaflet.js', array('jquery'), null);
        wp_enqueue_script('splide-script', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.1/dist/js/splide.min.js', array('jquery'), null);
        wp_enqueue_script('range-slider-script', 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js', array('jquery'), null);
        
        // CSS files
        wp_enqueue_style('asarinos-style', ASARINOS_PLUGIN_URL . 'style.css', array(), ASARINOS_VERSION);
        wp_enqueue_style('range-slider', 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css', array(), null);
        wp_enqueue_style('splide-slider-style', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.1/dist/css/splide.min.css', array(), null);
    }
    
    /**
     * Plugin activation
     */
    public static function activate() {
        // Create post type
        $core = new self();
        $core->register_property_post_type();
        flush_rewrite_rules();
        
        // Schedule cron job
        Asarinos_Cron::schedule_events();
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Clear scheduled events
        Asarinos_Cron::clear_scheduled_events();
        flush_rewrite_rules();
    }
}