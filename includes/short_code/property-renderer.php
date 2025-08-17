<?php
/**
 * Property Renderer
 * Handles HTML output for properties
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AsarinosPropertyRenderer {
    
    /**
     * Render the main properties section
     * 
     * @param array $properties Array of property objects
     */
    public static function render_properties_section($properties) {
        ?>
        <section class="elementor-section elementor-top-section elementor-element elementor-element-638511d elementor-element-638511dd elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="638511d" data-element_type="section" data-settings='{"background_background":"classic"}'>
            <div class="elementor-container elementor-column-gap-default">
                <?php
                for ($i = 0; $i < count($properties) && $i < 3; $i++) {
                    self::render_single_property($properties[$i], $i);
                }
                ?>
            </div>
            <section class="elementor-section elementor-top-section elementor-element elementor-element-638511d elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="638511d" data-element_type="section" data-settings='{"background_background":"classic"}'>
                <style>
                    .elementor-244 .elementor-element.elementor-element-638511dd {
                        display: block !important;
                    }
                </style>
            </section>
        </section>
        <?php
    }
    
    /**
     * Render a single property column
     * 
     * @param object $property Property post object
     * @param int $index Property index (0, 1, or 2)
     */
    private static function render_single_property($property, $index) {
        $meta = AsarinosPropertyQuery::get_property_meta($property->ID);
        $column_classes = self::get_column_classes($index);
        $animation_delay = ($index + 1) * 200;
        
        ?>
        <a href="<?php echo esc_url(get_permalink($property->ID)); ?>" class="front-photos">
            <div class="elementor-column elementor-col-33 elementor-top-column elementor-element <?php echo esc_attr($column_classes); ?> animated fadeInUp" 
                 data-element_type="column" 
                 data-settings='{"animation":"fadeInUp","animation_delay":<?php echo $animation_delay; ?>}'>
                <div class="elementor-widget-wrap elementor-element-populated">
                    <?php 
                    self::render_property_header($property, $meta);
                    self::render_property_details($property, $meta);
                    self::render_property_contact($meta);
                    ?>
                </div>
            </div>
        </a>
        <?php
    }
    
    /**
     * Render property header section
     */
    private static function render_property_header($property, $meta) {
        ?>
        <section class="elementor-section elementor-inner-section elementor-element elementor-section-height-min-height elementor-section-boxed elementor-section-height-default animated fadeInUp" 
                 data-element_type="section" 
                 data-settings='{"background_background":"classic","animation":"fadeInUp","animation_delay":100}'
                 <?php echo AsarinosPropertyHelpers::get_background_image_style($property->ID); ?>>
            <div class="elementor-background-overlay"></div>
            <div class="elementor-container elementor-column-gap-default">
                <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element animated slideInUp" 
                     data-element_type="column" 
                     data-settings='{"animation":"slideInUp","animation_delay":200}'>
                    <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-widget elementor-widget-heading" 
                             data-element_type="widget" 
                             data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                                <h2 class="elementor-heading-title elementor-size-default">
                                    <?php echo esc_html(AsarinosPropertyHelpers::format_price($meta['price'])); ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element animated fadeIn" 
                     data-element_type="column" 
                     data-settings='{"animation":"fadeIn","animation_delay":200}'>
                    <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-widget__width-auto elementor-widget elementor-widget-heading" 
                             data-element_type="widget" 
                             data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                                <h2 class="elementor-heading-title elementor-size-default">
                                    <?php echo esc_html(AsarinosPropertyHelpers::get_transaction_label($meta['transaction'])); ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
    
    /**
     * Render property details section
     */
    private static function render_property_details($property, $meta) {
        ?>
        <section class="elementor-section elementor-inner-section elementor-element elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" 
                 data-element_type="section" 
                 data-settings='{"background_background":"classic","animation":"fadeInDown","animation_delay":200}'>
            <div class="elementor-container elementor-column-gap-default">
                <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element" data-element_type="column">
                    <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-widget elementor-widget-heading" 
                             data-element_type="widget" 
                             data-widget_type="heading.default">
                            <div class="elementor-widget-container">
                                <h2 class="elementor-heading-title elementor-size-default">
                                    <?php echo esc_html($property->post_title); ?>
                                </h2>
                            </div>
                        </div>
                        <div class="elementor-element elementor-widget elementor-widget-text-editor" 
                             data-element_type="widget" 
                             data-widget_type="text-editor.default">
                            <div class="elementor-widget-container">
                                <p><?php echo esc_html(AsarinosPropertyHelpers::get_property_excerpt($property)); ?></p>
                            </div>
                        </div>
                        <?php self::render_property_features($meta); ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
    
    /**
     * Render property features icons
     */
    private static function render_property_features($meta) {
        $garage_count = !empty($meta['properties_garages']) ? $meta['properties_garages'] : 
                       (!empty($meta['parkingSpaces']) ? $meta['parkingSpaces'] : '1');
        ?>
        <div class="elementor-element elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" 
             data-element_type="widget" 
             data-widget_type="icon-list.default">
            <div class="elementor-widget-container">
                <ul class="elementor-icon-list-items elementor-inline-items">
                    <li class="elementor-icon-list-item elementor-inline-item">
                        <span class="elementor-icon-list-icon">
                            <?php echo self::get_bed_icon(); ?>
                        </span>
                        <span class="elementor-icon-list-text">
                            <?php echo esc_html($meta['apartmentBedroomNumber']); ?>
                        </span>
                    </li>
                    <li class="elementor-icon-list-item elementor-inline-item">
                        <span class="elementor-icon-list-icon">
                            <?php echo self::get_shower_icon(); ?>
                        </span>
                        <span class="elementor-icon-list-text">
                            <?php echo esc_html($meta['properties_bathrooms']); ?>
                        </span>
                    </li>
                    <li class="elementor-icon-list-item elementor-inline-item">
                        <span class="elementor-icon-list-icon">
                            <?php echo self::get_car_icon(); ?>
                        </span>
                        <span class="elementor-icon-list-text">
                            <?php echo esc_html($garage_count); ?>
                        </span>
                    </li>
                    <li class="elementor-icon-list-item elementor-inline-item">
                        <span class="elementor-icon-list-icon">
                            <?php echo self::get_ruler_icon(); ?>
                        </span>
                        <span class="elementor-icon-list-text">
                            <?php echo esc_html($meta['areaTotal']); ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render property contact section
     */
    private static function render_property_contact($meta) {
        ?>
        <section class="elementor-section elementor-inner-section elementor-element elementor-section-boxed elementor-section-height-default elementor-section-height-default animated fadeInDown" 
                 data-element_type="section" 
                 data-settings='{"background_background":"classic","animation":"fadeInDown","animation_delay":300}'>
            <div class="elementor-container elementor-column-gap-default">
                <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element" data-element_type="column">
                    <div class="elementor-widget-wrap elementor-element-populated">
                        <div class="elementor-element elementor-icon-list--layout-inline elementor-mobile-align-center elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list" 
                             data-element_type="widget" 
                             data-widget_type="icon-list.default">
                            <div class="elementor-widget-container">
                                <ul class="elementor-icon-list-items elementor-inline-items">
                                    <li class="elementor-icon-list-item elementor-inline-item">
                                        <span class="elementor-icon-list-icon">
                                            <?php echo self::get_user_icon(); ?>
                                        </span>
                                        <span class="elementor-icon-list-text">
                                            <?php echo esc_html(AsarinosPropertyHelpers::get_contact_name($meta)); ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
    
    /**
     * Get column classes based on index
     */
    private static function get_column_classes($index) {
        $classes = array(
            0 => 'elementor-element-2b0ca37',
            1 => 'elementor-element-6193c1a',
            2 => 'elementor-element-e73d527'
        );
        
        return isset($classes[$index]) ? $classes[$index] : '';
    }
    
    private static function get_bed_icon() {
        return '<svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path></svg>';
    }
    
    private static function get_shower_icon() {
        return '<svg aria-hidden="true" class="e-font-icon-svg e-fas-shower" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M304,320a16,16,0,1,0,16,16A16,16,0,0,0,304,320Zm32-96a16,16,0,1,0,16,16A16,16,0,0,0,336,224Zm32,64a16,16,0,1,0-16-16A16,16,0,0,0,368,288Zm-32,32a16,16,0,1,0-16-16A16,16,0,0,0,336,320Zm-32-64a16,16,0,1,0,16,16A16,16,0,0,0,304,256Zm128-32a16,16,0,1,0-16-16A16,16,0,0,0,432,224Zm-48,16a16,16,0,1,0,16-16A16,16,0,0,0,384,240Zm-16-48a16,16,0,1,0,16,16A16,16,0,0,0,368,192Zm96,32a16,16,0,1,0,16,16A16,16,0,0,0,464,224Zm32-32a16,16,0,1,0,16,16A16,16,0,0,0,496,192Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,432,256Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,400,288Zm-64,64a16,16,0,1,0,16,16A16,16,0,0,0,336,352Zm-32,32a16,16,0,1,0,16,16A16,16,0,0,0,304,384Zm64-64a16,16,0,1,0,16,16A16,16,0,0,0,368,320Zm21.65-218.35-11.3-11.31a16,16,0,0,0-22.63,0L350.05,96A111.19,111.19,0,0,0,272,64c-19.24,0-37.08,5.3-52.9,13.85l-10-10A121.72,121.72,0,0,0,123.44,32C55.49,31.5,0,92.91,0,160.85V464a16,16,0,0,0,16,16H48a16,16,0,0,0,16-16V158.4c0-30.15,21-58.2,51-61.93a58.38,58.38,0,0,1,48.93,16.67l10,10C165.3,138.92,160,156.76,160,176a111.23,111.23,0,0,0,32,78.05l-5.66,5.67a16,16,0,0,0,0,22.62l11.3,11.31a16,16,0,0,0,22.63,0L389.65,124.28A16,16,0,0,0,389.65,101.65Z"></path></svg>';
    }
    
    private static function get_car_icon() {
        return '<svg aria-hidden="true" class="e-font-icon-svg e-fas-car" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M499.99 176h-59.87l-16.64-41.6C406.38 91.63 365.57 64 319.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4L71.87 176H12.01C4.2 176-1.53 183.34.37 190.91l6 24C7.7 220.25 12.5 224 18.01 224h20.07C24.65 235.73 16 252.78 16 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-19.22-8.65-36.27-22.07-48H494c5.51 0 10.31-3.75 11.64-9.09l6-24c1.89-7.57-3.84-14.91-11.65-14.91zm-352.06-17.83c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L384 208H128l19.93-49.83zM96 319.8c-19.2 0-32-12.76-32-31.9S76.8 256 96 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S396.8 256 416 256s32 12.76 32 31.9-12.8 31.9-32 31.9z"></path></svg>';
    }
    
    private static function get_ruler_icon() {
        return '<svg aria-hidden="true" class="e-font-icon-svg e-fas-ruler-combined" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M160 288h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56v-64h-56c-4.42 0-8-3.58-8-8v-16c0-4.42 3.58-8 8-8h56V96h-56c-4.42 0-8-3.58-8-8V72c0-4.42 3.58-8 8-8h56V32c0-17.67-14.33-32-32-32H32C14.33 0 0 14.33 0 32v448c0 2.77.91 5.24 1.57 7.8L160 329.38V288zm320 64h-32v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-64v56c0 4.42-3.58 8-8 8h-16c-4.42 0-8-3.58-8-8v-56h-41.37L24.2 510.43c2.56.66 5.04 1.57 7.8 1.57h448c17.67 0 32-14.33 32-32v-96c0-17.67-14.33-32-32-32z"></path></svg>';
    }
    
    private static function get_user_icon() {
        return '<svg aria-hidden="true" class="e-font-icon-svg e-fas-user" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>';
    }
}