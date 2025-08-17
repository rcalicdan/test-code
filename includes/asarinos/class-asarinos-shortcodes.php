<?php
/**
 * Shortcodes class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Shortcodes {
    
    public function __construct() {
        add_shortcode('show_asari_offers', array($this, 'show_offers'));
        add_shortcode('asarinos_search', array($this, 'search_form'));
        add_shortcode('asarinos_search_results', array($this, 'search_results'));
    }
    
    /**
     * Show offers shortcode
     */
    public function show_offers($atts) {
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
            
            $output .= '<div class="property-box">';
            $output .= '<a href="' . esc_url($property->guid) . '">';
            $output .= '<div class="title"><h4>' . esc_html($property->post_title) . '</h4></div>';
            $output .= '<img class="img" src="' . esc_url(get_the_post_thumbnail_url($property->ID)) . '" alt="' . esc_attr($property->post_title) . '">';
            $output .= '<div class="offer_head">';
            $output .= '<div class="locality">Kraków, ul. Wrocławska</div>';
            $output .= '<div class="line-down">';
            $output .= '<div class="three" title="Powierzchnia"><img src="/wp-content/uploads/2021/vector-square-plus.svg" alt="Powierzchnia"><br>' . esc_html($area_size) . ' m2</div>';
            $output .= '<div class="three" title="Pokoje"><img src="/wp-content/uploads/2021/bed-outline.svg" alt="Pokoje"><br>' . esc_html($bedrooms) . '</div>';
            $output .= '<div class="three money" title="Koszt miesięczny"><img src="/wp-content/uploads/2021/calendar-month.svg" alt="Koszt miesięczny"><br>' . esc_html($price) . ' zł</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '<div class="hidden_info">SPRAWDŹ OFERTĘ</div>';
            $output .= '</a>';
            $output .= '</div>';
        }
        
        return $output;
    }
    
    /**
     * Search form shortcode
     */
    public function search_form($atts) {
        $atts = shortcode_atts(array(
            'main' => 'false'
        ), $atts);

        ob_start();
        ?>
        <div class="search-box asarinos">
            <?php if ($atts['main'] === 'true'): ?>
                <form action="/oferty" method="GET">
                <?php wp_nonce_field('asarinos_search_nonce', '_wpnonce'); ?>
            <?php endif; ?>
            
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
            
            <?php if ($atts['main'] === 'true'): ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Search results shortcode
     */
    public function search_results($atts) {
        return '<div id="map" style="width: 900px; height: 580px"></div>
                <div id="search_results"></div>';
    }
}