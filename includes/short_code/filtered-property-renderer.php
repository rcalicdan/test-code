<?php

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

        self::render_styles();
        self::render_properties_container($properties, $pagination_info);
    }

    private static function render_no_results()
    {
?>
        <div class="asarinos-no-results">
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
                <p>Znaleziono <?= esc_html($pagination_info['total']) ?> nieruchomości</p>
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
        <a href="<?= esc_url($permalink) ?>" class="asarinos-property-link">
            <div class="asarinos-property-card">
                <?php if ($thumbnail_url): ?>
                    <?php self::render_property_image($thumbnail_url, $meta); ?>
                <?php endif; ?>
                <?php self::render_property_content($property, $meta); ?>
            </div>
        </a>
    <?php
    }

    private static function render_property_image($thumbnail_url, $meta)
    {
    ?>
        <div class="asarinos-property-image" style="background-image: url(<?= esc_url($thumbnail_url) ?>);">
            <div class="asarinos-property-price">
                <?= esc_html(AsarinosPropertyHelpers::format_price($meta['price'])) ?>
            </div>
            <div class="asarinos-property-transaction">
                <?= esc_html(AsarinosPropertyHelpers::get_transaction_label($meta['transaction'])) ?>
            </div>
        </div>
    <?php
    }

    private static function render_property_content($property, $meta)
    {
    ?>
        <div class="asarinos-property-content">
            <h3 class="asarinos-property-title"><?= esc_html($property->post_title) ?></h3>
            <p class="asarinos-property-excerpt">
                <?= esc_html(AsarinosPropertyHelpers::get_property_excerpt($property, 80)) ?>
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
                <span class="asarinos-feature">
                    🛏️ <?= esc_html($meta['apartmentBedroomNumber']) ?>
                </span>
            <?php endif; ?>

            <?php if (!empty($meta['properties_bathrooms'])): ?>
                <span class="asarinos-feature">
                    🚿 <?= esc_html($meta['properties_bathrooms']) ?>
                </span>
            <?php endif; ?>

            <?php if (!empty($meta['areaTotal'])): ?>
                <span class="asarinos-feature">
                    📏 <?= esc_html($meta['areaTotal']) ?> m²
                </span>
            <?php endif; ?>
        </div>
        <?php
    }

    private static function render_property_agent($meta)
    {
        $contact_name = AsarinosPropertyHelpers::get_contact_name($meta);
        if (!empty($contact_name)): ?>
            <div class="asarinos-property-agent">
                👤 <?= esc_html($contact_name) ?>
            </div>
        <?php endif;
    }

    private static function render_pagination($pagination_info)
    {
        if (empty($pagination_info) || $pagination_info['max_pages'] <= 1) return;

        $current_page = $pagination_info['current_page'] ?? 1;
        $max_pages = $pagination_info['max_pages'];
        ?>
        <div class="asarinos-pagination">
            <?php if ($current_page > 1): ?>
                <a href="<?= esc_url(add_query_arg('paged', $current_page - 1)) ?>" class="asarinos-pagination-prev">
                    &laquo; Poprzednia
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $max_pages; $i++): ?>
                <?php if ($i == $current_page): ?>
                    <span class="asarinos-pagination-current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= esc_url(add_query_arg('paged', $i)) ?>" class="asarinos-pagination-link">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($current_page < $max_pages): ?>
                <a href="<?= esc_url(add_query_arg('paged', $current_page + 1)) ?>" class="asarinos-pagination-next">
                    Następna &raquo;
                </a>
            <?php endif; ?>
        </div>
    <?php
    }

    private static function render_styles()
    {
    ?>
        <style>
            .asarinos-filtered-container {
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            .asarinos-results-count {
                margin-bottom: 20px;
                font-weight: 600;
                color: #333;
            }

            .asarinos-properties-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 30px;
                margin-bottom: 40px;
            }

            .asarinos-property-item {
                background: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .asarinos-property-item:hover {
                transform: translateY(-8px);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            }

            .asarinos-property-link {
                text-decoration: none;
                color: inherit;
                display: block;
                height: 100%;
            }

            .asarinos-property-card {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .asarinos-property-image {
                height: 250px;
                background-size: cover;
                background-position: center;
                position: relative;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 20px;
            }

            .asarinos-property-price {
                background: rgba(0, 0, 0, 0.85);
                color: white;
                padding: 10px 16px;
                border-radius: 8px;
                font-weight: 700;
                font-size: 18px;
                backdrop-filter: blur(10px);
            }

            .asarinos-property-transaction {
                background: linear-gradient(135deg, #007cba, #005a87);
                color: white;
                padding: 8px 14px;
                border-radius: 6px;
                font-size: 12px;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: 0.5px;
            }

            .asarinos-property-content {
                padding: 24px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .asarinos-property-title {
                margin: 0 0 12px 0;
                font-size: 20px;
                font-weight: 700;
                line-height: 1.3;
                color: #1a1a1a;
            }

            .asarinos-property-excerpt {
                color: #666;
                line-height: 1.6;
                margin: 0 0 20px 0;
                flex-grow: 1;
                font-size: 14px;
            }

            .asarinos-property-features {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 16px;
            }

            .asarinos-feature {
                display: flex;
                align-items: center;
                gap: 6px;
                color: #333;
                font-size: 14px;
                font-weight: 500;
            }

            .asarinos-property-agent {
                color: #007cba;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 500;
                padding-top: 12px;
                border-top: 1px solid #eee;
            }

            .asarinos-pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                margin-top: 40px;
                flex-wrap: wrap;
            }

            .asarinos-pagination a,
            .asarinos-pagination span {
                padding: 12px 16px;
                border: 2px solid #e1e5e9;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 500;
                transition: all 0.2s ease;
                min-width: 44px;
                text-align: center;
            }

            .asarinos-pagination a:hover {
                border-color: #007cba;
                background: #f8f9fa;
                color: #007cba;
            }

            .asarinos-pagination-current {
                background: #007cba;
                color: white;
                border-color: #007cba;
            }

            .asarinos-no-results {
                text-align: center;
                padding: 60px 20px;
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 12px;
                color: #666;
                font-size: 18px;
            }

            @media (max-width: 768px) {
                .asarinos-properties-grid {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }

                .asarinos-property-image {
                    height: 200px;
                    padding: 15px;
                }

                .asarinos-property-content {
                    padding: 20px;
                }

                .asarinos-pagination {
                    gap: 6px;
                }

                .asarinos-pagination a,
                .asarinos-pagination span {
                    padding: 10px 12px;
                    font-size: 14px;
                }
            }
        </style>
<?php
    }
}
