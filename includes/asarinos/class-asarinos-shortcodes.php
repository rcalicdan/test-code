<?php

/**
 * Shortcodes class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Shortcodes
{

    public function __construct()
    {
        add_shortcode('show_asari_offers', array($this, 'show_offers'));
        add_shortcode('asarinos_search', array($this, 'search_form'));
        add_shortcode('asarinos_search_results', array($this, 'search_results'));
    }

    /**
     * Show offers shortcode
     */
    public function show_offers($atts)
    {
        $properties = get_posts(array(
            'post_type' => 'property',
            'numberposts' => 1000,
            'post_status' => 'publish'
        ));

        $output = '';

        foreach ($properties as $property) {
            $area_size = get_post_meta($property->ID, 'properties_area_size', true);
            $bedrooms = get_post_meta($property->ID, 'properties_bedrooms', true);
            $price = get_post_meta($property->ID, 'properties_price', true);
            $property_url = esc_url($property->guid);
            $property_title = esc_html($property->post_title);
            $thumbnail_url = esc_url(get_the_post_thumbnail_url($property->ID));
            $property_title_attr = esc_attr($property->post_title);
            $area_size_html = esc_html($area_size);
            $bedrooms_html = esc_html($bedrooms);
            $price_html = esc_html($price);

            $output .= <<<HTML
<div class="property-box">
    <a href="{$property_url}">
        <div class="title"><h4>{$property_title}</h4></div>
        <img class="img" src="{$thumbnail_url}" alt="{$property_title_attr}">
        <div class="offer_head">
            <div class="locality">Kraków, ul. Wrocławska</div>
            <div class="line-down">
                <div class="three" title="Powierzchnia">
                    <img src="/wp-content/uploads/2021/vector-square-plus.svg" alt="Powierzchnia"><br>
                    {$area_size_html} m2
                </div>
                <div class="three" title="Pokoje">
                    <img src="/wp-content/uploads/2021/bed-outline.svg" alt="Pokoje"><br>
                    {$bedrooms_html}
                </div>
                <div class="three money" title="Koszt miesięczny">
                    <img src="/wp-content/uploads/2021/calendar-month.svg" alt="Koszt miesięczny"><br>
                    {$price_html} zł
                </div>
            </div>
        </div>
        <div class="hidden_info">SPRAWDŹ OFERTĘ</div>
    </a>
</div>
HTML;
        }

        return $output;
    }

    /**
     * Search form shortcode
     */
    public function search_form($atts)
    {
        $atts = shortcode_atts(array(
            'main' => 'false'
        ), $atts);

        $form_start = '';
        $form_end = '';
        $nonce_field = '';

        if ($atts['main'] === 'true') {
            $nonce_field = wp_nonce_field('asarinos_search_nonce', '_wpnonce', true, false);
            $form_start = '<form action="/oferty" method="GET">';
            $form_end = '</form>';
        }

        return <<<HTML
<div class="search-box asarinos">
    {$form_start}
    {$nonce_field}
    
    <div class="elementor-row">
        <div class="elementor-row">
            <div class="elementor-col elementor-col-40">
                <div class="elementor-column elementor-col-100">
                    <label for="type">Typ Nieruchomości:</label>
                    <select name="type" id="type">
                        <option value="">-- Wszystkie --</option>
                        <option value="mieszkanie">Mieszkania</option>
                        <option value="dom">Domy</option>
                        <option value="działka">Działki</option>
                        <option value="lokal">Lokale</option>
                        <option value="hala">Hale</option>
                    </select>
                </div>
                
                <div class="elementor-row">
                    <div class="elementor-column elementor-col-100">
                        <label for="city">Lokalizacja:</label><br />
                        <input type="text" id="city" value="" name="city" />
                    </div>
                </div>
            </div>

            <div class="elementor-col elementor-col-30">
                <div class="elementor-column elementor-col-100">
                    <label for="transaction">Transakcja:</label><br />
                    <select name="transaction" id="transaction">
                        <option value="">-- Wszystkie --</option>
                        <option value="sprzedaż">Sprzedaż</option>
                        <option value="wynajem">Wynajem</option>
                    </select>
                </div>

                <div class="elementor-row">
                    <div class="elementor-column elementor-col-50">
                        <label for="area_from">Powierzchnia od:</label><br />
                        <input type="text" id="area_from" value="" name="area_from" />
                    </div>
                    <div class="elementor-column elementor-col-50">
                        <label for="area_to">Powierzchnia do:</label><br />
                        <input type="text" id="area_to" value="" name="area_to" />
                    </div>
                </div>
            </div>

            <div class="elementor-col elementor-col-30">
                <div class="elementor-row">
                    <div class="elementor-column elementor-col-50">
                        <label for="price_from">Cena od:</label><br />
                        <input type="text" id="price_from" value="" name="price_from" />
                    </div>
                    <div class="elementor-column elementor-col-50">
                        <label for="price_to">Cena do:</label><br />
                        <input type="text" id="price_to" value="" name="price_to" />
                    </div>
                </div>

                <div class="elementor-row">
                    <div class="elementor-column elementor-col-50">
                        <label for="rooms_since">Pokoje od:</label><br />
                        <input type="text" id="rooms_since" value="" name="rooms_since" />
                    </div>
                    <div class="elementor-column elementor-col-50">
                        <label for="rooms_to">Pokoje do:</label><br />
                        <input type="text" id="rooms_to" value="" name="rooms_to" />
                    </div>
                </div>

                <div class="" style="text-align: right; height: 100%; width:100%">
                    <input type="submit" id="search_asarino" value="Szukaj" style="width: 100%;" >
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0-beta.3/leaflet.min.css"/>
    
    {$form_end}
</div>
HTML;
    }

    /**
     * Search results shortcode
     */
    public function search_results($atts)
    {
        return <<<HTML
<div id="map" style="width: 900px; height: 580px"></div>
<div id="search_results"></div>
HTML;
    }
}
