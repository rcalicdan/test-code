<?php

require_once ASARINOS_PLUGIN_DIR . 'includes/short_code/filtered-property-styles.php';

/**
 * Filtered Property Renderer
 * Handles HTML output for filtered properties
 */

if (!defined('ABSPATH')) {
    exit;
}

class AsarinosFilteredPropertyRenderer extends AsarinosPropertyRenderer
{

    /**
     * Render filtered properties grid
     */
    public static function render_filtered_properties($properties, $pagination_info = [])
    {
        if (empty($properties)) {
            self::render_no_results();
            return;
        }

        AsarinosFilteredPropertyStyles::render_styles();
        self::render_properties_container($properties, $pagination_info);
    }

    private static function render_no_results()
    {
    ?>
        <div class="asarinos-no-results">
            <div class="no-results-icon">🏠</div>
            <h3>Brak wyników</h3>
            <p>Nie znaleziono nieruchomości spełniających kryteria wyszukiwania.</p>
        </div>
    <?php
    }

    private static function render_properties_container($properties, $pagination_info)
    {
    ?>
        <div class="asarinos-filtered-container">
            <?php self::render_results_count($pagination_info); ?>
            <?php self::render_properties_grid($properties); ?>
            <?php self::render_pagination($pagination_info); ?>
        </div>
        <?php
    }

    private static function render_results_count($pagination_info)
    {
        if (!empty($pagination_info['total'])) {
        ?>
            <div class="asarinos-results-count">
                <h2 class="results-title">Podobne oferty</h2>
                <p class="results-subtitle">Znaleziono <?= esc_html($pagination_info['total']) ?> nieruchomości</p>
            </div>
        <?php
        }
    }

    private static function render_properties_grid($properties)
    {
        ?>
        <div class="asarinos-properties-grid">
            <?php foreach ($properties as $property): ?>
                <div class="asarinos-property-item">
                    <?php self::render_property_card($property); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php
    }

    private static function render_property_card($property)
    {
        $meta = AsarinosPropertyQuery::get_property_meta($property->ID);
        $permalink = get_permalink($property->ID);
        $thumbnail_url = get_the_post_thumbnail_url($property->ID);
    ?>
        <div class="asarinos-property-card">
            <a href="<?= esc_url($permalink) ?>" class="asarinos-property-link">
                <?php if ($thumbnail_url): ?>
                    <?php self::render_property_image($thumbnail_url, $meta); ?>
                <?php endif; ?>
                <?php self::render_property_content($property, $meta); ?>
            </a>
            <div class="asarinos-property-footer">
                <?php self::render_property_cta($permalink); ?>
            </div>
        </div>
    <?php
    }

    private static function render_property_image($thumbnail_url, $meta)
    {
    ?>
        <div class="asarinos-property-image" style="background-image: url(<?= esc_url($thumbnail_url) ?>);">
            <div class="asarinos-property-badges">
                <div class="asarinos-property-price">
                    <?= esc_html(AsarinosPropertyHelpers::format_price($meta['price'])) ?>
                </div>
                <div class="asarinos-property-transaction">
                    <?= esc_html(AsarinosPropertyHelpers::get_transaction_label($meta['transaction'])) ?>
                </div>
            </div>
            <div class="image-overlay"></div>
        </div>
    <?php
    }

    private static function render_property_content($property, $meta)
    {
    ?>
        <div class="asarinos-property-content">
            <h3 class="asarinos-property-title"><?= esc_html($property->post_title) ?></h3>
            <p class="asarinos-property-excerpt">
                <?= esc_html(AsarinosPropertyHelpers::get_property_excerpt($property, 100)) ?>
            </p>
            <?php self::render_property_features($meta); ?>
            <?php self::render_property_agent($meta); ?>
        </div>
    <?php
    }

    private static function render_property_features($meta)
    {
    ?>
        <div class="asarinos-property-features">
            <?php if (!empty($meta['apartmentBedroomNumber'])): ?>
                <div class="asarinos-feature">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M7 14c1.66 0 3-1.34 3-3S8.66 8 7 8s-3 1.34-3 3 1.34 3 3 3zm0-4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm12-3h-8v8H3V7H1v10h2v-2h18v2h2v-5.5c0-2.21-1.79-4-4-4z" />
                    </svg>
                    <span><?= esc_html($meta['apartmentBedroomNumber']) ?> pokoje</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($meta['properties_bathrooms'])): ?>
                <div class="asarinos-feature">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M9 2v1h6V2h2v1h2a1 1 0 011 1v16a1 1 0 01-1 1H5a1 1 0 01-1-1V4a1 1 0 011-1h2V2h2zM8 17a4 4 0 008 0v-3H8v3z" />
                    </svg>
                    <span><?= esc_html($meta['properties_bathrooms']) ?> łazienki</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($meta['areaTotal'])): ?>
                <div class="asarinos-feature">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                        <path d="M7 17h2v-7H7v7zm4 0h2V7h-2v10zm4-5h2v-2h-2v2z" />
                    </svg>
                    <span><?= esc_html($meta['areaTotal']) ?> m²</span>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    private static function render_property_agent($meta)
    {
        $contact_name = AsarinosPropertyHelpers::get_contact_name($meta);
        if (!empty($contact_name)): ?>
            <div class="asarinos-property-agent">
                <svg class="agent-icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                </svg>
                <span><?= esc_html($contact_name) ?></span>
            </div>
        <?php endif;
    }

    private static function render_property_cta($permalink)
    {
        ?>
        <a href="<?= esc_url($permalink) ?>" class="asarinos-property-cta">
            <span>Zobacz szczegóły</span>
            <svg class="cta-arrow" viewBox="0 0 24 24">
                <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
            </svg>
        </a>
    <?php
    }

    private static function render_pagination($pagination_info)
    {
        if (empty($pagination_info) || $pagination_info['max_pages'] <= 1) return;

        $current_page = $pagination_info['current_page'] ?? 1;
        $max_pages = $pagination_info['max_pages'];
    ?>
        <div class="asarinos-pagination">
            <?php if ($current_page > 1): ?>
                <a href="<?= esc_url(add_query_arg('paged', $current_page - 1)) ?>" class="asarinos-pagination-nav asarinos-pagination-prev">
                    <svg viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                    </svg>
                    <span>Poprzednia</span>
                </a>
            <?php endif; ?>

            <div class="asarinos-pagination-numbers">
                <?php for ($i = 1; $i <= $max_pages; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="asarinos-pagination-current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= esc_url(add_query_arg('paged', $i)) ?>" class="asarinos-pagination-link">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <?php if ($current_page < $max_pages): ?>
                <a href="<?= esc_url(add_query_arg('paged', $current_page + 1)) ?>" class="asarinos-pagination-nav asarinos-pagination-next">
                    <span>Następna</span>
                    <svg viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    <?php
    }


}
