<?php


if (!defined('ABSPATH')) {
    exit;
}

class AsarinosFilteredPropertyStyles
{
    public static function render_styles()
    {
    ?>
        <style>
            /* K&D Theme Colors */
            :root {
                --kd-gold: #B5985A;
                --kd-gold-light: #C8A872;
                --kd-gold-dark: #A08749;
                --kd-gold-pale: #F5F1E8;
                --kd-dark: #2C2C2C;
                --kd-gray: #6B6B6B;
                --kd-light-gray: #F8F8F8;
                --kd-white: #FFFFFF;
                --kd-shadow: rgba(181, 152, 90, 0.15);
            }

            .asarinos-filtered-container {
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                padding: 0 20px;
            }

            .asarinos-results-count {
                margin-bottom: 40px;
                text-align: left;
                border-bottom: 1px solid rgba(181, 152, 90, 0.2);
                padding-bottom: 24px;
            }

            .results-title {
                color: var(--kd-dark);
                font-size: 32px;
                font-weight: 700;
                margin: 0 0 8px 0;
                line-height: 1.2;
                position: relative;
                display: inline-block;
            }

            .results-title::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 0;
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, var(--kd-gold), var(--kd-gold-light));
                border-radius: 2px;
            }

            .results-subtitle {
                color: var(--kd-gray);
                font-size: 16px;
                margin: 0;
                font-weight: 500;
            }

            .asarinos-properties-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 32px;
                margin-bottom: 48px;
            }

            .asarinos-property-item {
                background: var(--kd-white);
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(181, 152, 90, 0.1);
                position: relative;
            }

            .asarinos-property-item::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, var(--kd-gold), var(--kd-gold-light));
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .asarinos-property-item:hover {
                transform: translateY(-12px);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                border-color: var(--kd-gold);
            }

            .asarinos-property-item:hover::before {
                opacity: 1;
            }

            .asarinos-property-link {
                text-decoration: none;
                color: inherit;
                display: block;
            }

            .asarinos-property-card {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .asarinos-property-image {
                height: 280px;
                background-size: cover;
                background-position: center;
                position: relative;
                overflow: hidden;
            }

            .image-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .asarinos-property-item:hover .image-overlay {
                opacity: 1;
            }

            .asarinos-property-badges {
                position: absolute;
                top: 20px;
                left: 20px;
                right: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                z-index: 2;
            }

            .asarinos-property-price {
                background: rgba(45, 45, 45, 0.95);
                color: var(--kd-white);
                padding: 12px 18px;
                border-radius: 12px;
                font-weight: 700;
                font-size: 18px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            }

            .asarinos-property-transaction {
                background: linear-gradient(135deg, var(--kd-gold), var(--kd-gold-dark));
                color: var(--kd-white);
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 12px;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: 1px;
                box-shadow: 0 4px 12px var(--kd-shadow);
            }

            .asarinos-property-content {
                padding: 28px;
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .asarinos-property-title {
                margin: 0 0 16px 0;
                font-size: 22px;
                font-weight: 700;
                line-height: 1.3;
                color: var(--kd-dark);
                transition: color 0.3s ease;
            }

            .asarinos-property-link:hover .asarinos-property-title {
                color: var(--kd-gold-dark);
            }

            .asarinos-property-excerpt {
                color: var(--kd-gray);
                line-height: 1.6;
                margin: 0 0 24px 0;
                flex-grow: 1;
                font-size: 15px;
            }

            .asarinos-property-features {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 20px;
                padding: 20px 0;
                border-top: 1px solid #f0f0f0;
                border-bottom: 1px solid #f0f0f0;
            }

            .asarinos-feature {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--kd-dark);
                font-size: 14px;
                font-weight: 500;
                padding: 8px 12px;
                background: var(--kd-gold-pale);
                border-radius: 8px;
                border: 1px solid rgba(181, 152, 90, 0.2);
                transition: all 0.3s ease;
            }

            .asarinos-feature:hover {
                background: var(--kd-gold);
                color: var(--kd-white);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px var(--kd-shadow);
            }

            .feature-icon {
                width: 16px;
                height: 16px;
                fill: currentColor;
            }

            .asarinos-property-agent {
                color: var(--kd-gold-dark);
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 500;
                margin-top: 12px;
            }

            .agent-icon {
                width: 18px;
                height: 18px;
                fill: currentColor;
            }

            .asarinos-property-footer {
                padding: 0 28px 28px 28px;
            }

            .asarinos-property-cta {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: linear-gradient(135deg, var(--kd-gold), var(--kd-gold-dark));
                color: var(--kd-white);
                padding: 16px 24px;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 600;
                font-size: 15px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .asarinos-property-cta::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s ease;
            }

            .asarinos-property-cta:hover::before {
                left: 100%;
            }

            .asarinos-property-cta:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px var(--kd-shadow);
            }

            .cta-arrow {
                width: 18px;
                height: 18px;
                fill: currentColor;
                transition: transform 0.3s ease;
            }

            .asarinos-property-cta:hover .cta-arrow {
                transform: translateX(4px);
            }

            .asarinos-pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 16px;
                margin: 48px 0;
                flex-wrap: wrap;
            }

            .asarinos-pagination-numbers {
                display: flex;
                gap: 8px;
            }

            .asarinos-pagination-nav {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 20px;
                background: var(--kd-white);
                border: 2px solid var(--kd-gold);
                color: var(--kd-gold-dark);
                text-decoration: none;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .asarinos-pagination-nav svg {
                width: 18px;
                height: 18px;
                fill: currentColor;
            }

            .asarinos-pagination-nav:hover {
                background: var(--kd-gold);
                color: var(--kd-white);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px var(--kd-shadow);
            }

            .asarinos-pagination-link,
            .asarinos-pagination-current {
                padding: 12px 16px;
                border: 2px solid transparent;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
                min-width: 48px;
                text-align: center;
                background: var(--kd-white);
                color: var(--kd-dark);
            }

            .asarinos-pagination-link:hover {
                border-color: var(--kd-gold);
                background: var(--kd-gold-pale);
                color: var(--kd-gold-dark);
                transform: translateY(-2px);
            }

            .asarinos-pagination-current {
                background: linear-gradient(135deg, var(--kd-gold), var(--kd-gold-dark));
                color: var(--kd-white);
                border-color: var(--kd-gold-dark);
                box-shadow: 0 4px 12px var(--kd-shadow);
            }

            .asarinos-no-results {
                text-align: center;
                padding: 80px 40px;
                background: linear-gradient(135deg, var(--kd-gold-pale), var(--kd-white));
                border-radius: 20px;
                border: 2px dashed var(--kd-gold);
                margin: 40px 0;
            }

            .no-results-icon {
                font-size: 64px;
                margin-bottom: 20px;
                opacity: 0.7;
            }

            .asarinos-no-results h3 {
                color: var(--kd-dark);
                font-size: 24px;
                font-weight: 700;
                margin: 0 0 12px 0;
            }

            .asarinos-no-results p {
                color: var(--kd-gray);
                font-size: 16px;
                margin: 0;
                line-height: 1.6;
            }

            @media (max-width: 1024px) {
                .asarinos-properties-grid {
                    grid-template-columns: 1fr;
                    gap: 28px;
                }
            }

            @media (max-width: 768px) {
                .asarinos-properties-grid {
                    gap: 24px;
                }

                .asarinos-property-image {
                    height: 240px;
                }

                .asarinos-property-badges {
                    padding: 16px;
                }

                .asarinos-property-content {
                    padding: 24px;
                }

                .asarinos-property-features {
                    gap: 12px;
                }

                .asarinos-feature {
                    padding: 6px 10px;
                    font-size: 13px;
                }

                .asarinos-pagination {
                    gap: 12px;
                    margin: 32px 0;
                }

                .asarinos-pagination-nav {
                    padding: 10px 16px;
                }

                .asarinos-pagination-link,
                .asarinos-pagination-current {
                    padding: 10px 14px;
                    font-size: 14px;
                }

                .results-title {
                    font-size: 28px;
                }

                .results-subtitle {
                    font-size: 15px;
                }
            }

            @media (max-width: 480px) {
                .asarinos-filtered-container {
                    padding: 0 16px;
                }

                .asarinos-properties-grid {
                    gap: 20px;
                }

                .asarinos-property-content {
                    padding: 20px;
                }

                .asarinos-property-footer {
                    padding: 0 20px 20px 20px;
                }

                .results-title {
                    font-size: 24px;
                }
            }
        </style>
    <?php
    }
}
