<?php

/**
 * Admin interface class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_options_page(
            "Synchronizacja z Esti",
            "Synchronizacja z Esti",
            'manage_options',
            "synchronizacja-z-esti",
            array($this, 'admin_page')
        );

        // Add shortcodes submenu to Properties menu
        add_submenu_page(
            'edit.php?post_type=property',
            'Property Shortcodes',
            'Krótkie kody',
            'manage_options',
            'asarinos-shortcodes',
            array($this, 'shortcodes_page')
        );
    }

    /**
     * Admin page content (existing sync functionality)
     */
    public function admin_page()
    {
?>
        <div class="wrap">
            <h2>Integracja z Esti</h2>

            <?php $this->handle_form_submissions(); ?>

            <form action="" method="POST">
                <?php wp_nonce_field('asarinos_admin_action', 'asarinos_nonce'); ?>

                <h3>Aktualizacja ofert</h3>
                <p>Kliknij przycisk poniżej, aby zsynchronizować oferty z systemem Esti.</p>
                <?php submit_button('Zaktualizuj ogłoszenia', 'primary', 'check', false); ?>
            </form>

            <hr>

            <form action="" method="POST">
                <?php wp_nonce_field('asarinos_hours_action', 'asarinos_hours_nonce'); ?>

                <h3>Harmonogram aktualizacji</h3>
                <p>Podaj pełne godziny w których mają być aktualizacje np: "12,16,19"</p>
                <input type="text"
                    id="hours_update"
                    name="hours_update"
                    style="width: 340px"
                    value="<?php echo esc_attr(get_option('asarino_hours', '')); ?>"
                    placeholder="12,16,19">

                <?php submit_button('Zaktualizuj godziny', 'secondary', 'hours', false); ?>
            </form>
        </div>

        <style>
            .asarinos-log {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 10px;
                max-height: 400px;
                overflow-y: auto;
                margin-top: 20px;
            }

            .asarinos-log p {
                margin: 5px 0;
                font-family: monospace;
            }

            .success {
                color: #46b450;
            }

            .error {
                color: #dc3232;
            }

            .info {
                color: #0073aa;
            }
        </style>
    <?php
    }

    /**
     * Shortcodes page content with Alpine.js
     */
    public function shortcodes_page()
    {
    ?>
        <div class="wrap" x-data="shortcodeGenerator()">
            <h1>Property Shortcodes</h1>
            <p>Zarządzaj shortcode'ami do wyświetlania nieruchomości na stronie.</p>

            <div class="asarinos-admin-container">
                <!-- Main Page Shortcode Section -->
                <div class="shortcode-section">
                    <h2>🏠 Główna Strona - Polecane Nieruchomości</h2>
                    <p>Wyświetla najnowsze 3 nieruchomości w układzie polecanym (do użycia na stronie głównej)</p>
                    <div class="shortcode-box">
                        <code>[asarionos_mainpage]</code>
                        <button class="copy-btn"
                            @click="copyToClipboard('[asarionos_mainpage]', $event.target)"
                            :class="{ 'copied': copiedButton === 'main' }">
                            <span x-text="copiedButton === 'main' ? 'Skopiowano!' : 'Kopiuj'"></span>
                        </button>
                    </div>
                </div>

                <!-- Filtered Properties Section -->
                <div class="shortcode-section">
                    <h2>🔍 Filtrowane Nieruchomości</h2>
                    <p>Wyświetla nieruchomości z możliwością filtrowania według różnych kryteriów</p>

                    <form class="shortcode-form" @submit.prevent="generateShortcode()">
                        <table class="form-table">
                            <tr>
                                <th><label for="type">Typ Nieruchomości</label></th>
                                <td>
                                    <select x-model="formData.type" id="type">
                                        <option value="">Wszystkie typy</option>
                                        <option value="apartments">Apartamenty</option>
                                        <option value="houses">Domy</option>
                                        <option value="commercial">Lokale komercyjne</option>
                                        <option value="lots">Działki</option>
                                        <option value="rental">Wynajem</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="transaction">Typ Transakcji</label></th>
                                <td>
                                    <select x-model="formData.transaction" id="transaction">
                                        <option value="">Wszystkie transakcje</option>
                                        <option value="sale">Sprzedaż</option>
                                        <option value="rent">Wynajem</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="count">Liczba na stronę</label></th>
                                <td>
                                    <input type="number"
                                        x-model="formData.count"
                                        id="count"
                                        min="1"
                                        max="50">
                                    <p class="description">Maksymalnie 50 nieruchomości na stronę</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Zakres Ceny</th>
                                <td>
                                    <input type="number"
                                        x-model="formData.price_min"
                                        placeholder="Cena minimalna"
                                        style="width: 150px;">
                                    <span style="margin: 0 10px;">-</span>
                                    <input type="number"
                                        x-model="formData.price_max"
                                        placeholder="Cena maksymalna"
                                        style="width: 150px;">
                                    <p class="description">Zostaw puste aby nie filtrować po cenie</p>
                                </td>
                            </tr>
                            <tr>
                                <th>Powierzchnia (m²)</th>
                                <td>
                                    <input type="number"
                                        x-model="formData.area_min"
                                        placeholder="Min powierzchnia"
                                        style="width: 150px;">
                                    <span style="margin: 0 10px;">-</span>
                                    <input type="number"
                                        x-model="formData.area_max"
                                        placeholder="Max powierzchnia"
                                        style="width: 150px;">
                                    <p class="description">Zostaw puste aby nie filtrować po powierzchni</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="bedrooms">Min. Sypialni</label></th>
                                <td>
                                    <input type="number"
                                        x-model="formData.bedrooms"
                                        id="bedrooms"
                                        min="1"
                                        max="10"
                                        placeholder="Minimalna liczba sypialni">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="city">Miasto</label></th>
                                <td>
                                    <input type="text"
                                        x-model="formData.city"
                                        id="city"
                                        placeholder="Nazwa miasta"
                                        style="width: 300px;">
                                    <p class="description">Filtruje nieruchomości w określonym mieście</p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="button"
                                @click="generateShortcode()"
                                class="button button-primary">
                                🔧 Generuj Shortcode
                            </button>
                        </p>
                    </form>

                    <div class="shortcode-output" :class="{ 'highlighted': isHighlighted }">
                        <h3>Wygenerowany Shortcode:</h3>
                        <div class="shortcode-box">
                            <code x-text="generatedShortcode"></code>
                            <button class="copy-btn"
                                @click="copyToClipboard(generatedShortcode, $event.target)"
                                :class="{ 'copied': copiedButton === 'generated' }">
                                <span x-text="copiedButton === 'generated' ? 'Skopiowano!' : 'Kopiuj'"></span>
                            </button>
                        </div>
                        <p class="description">
                            <strong>Instrukcja:</strong> Skopiuj powyższy shortcode i wklej go na stronie lub poście gdzie chcesz wyświetlić nieruchomości.
                        </p>
                    </div>
                </div>

                <!-- Examples Section -->
                <div class="shortcode-section">
                    <h2>📋 Gotowe Przykłady</h2>
                    <div class="examples-grid">
                        <template x-for="(example, index) in examples" :key="index">
                            <div class="example-item">
                                <h4 x-text="example.title"></h4>
                                <div class="shortcode-box">
                                    <code x-text="example.shortcode"></code>
                                    <button class="copy-btn"
                                        @click="copyToClipboard(example.shortcode, $event.target)"
                                        :class="{ 'copied': copiedButton === `example-${index}` }">
                                        <span x-text="copiedButton === `example-${index}` ? 'Skopiowano!' : 'Kopiuj'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="shortcode-section">
                    <h2>❓ Pomoc</h2>
                    <div style="background: #f0f8ff; padding: 20px; border-radius: 8px; border-left: 4px solid #007cba;">
                        <h4>Jak używać shortcode'ów?</h4>
                        <ol>
                            <li>Skopiuj wybrany shortcode klikając przycisk "Kopiuj"</li>
                            <li>Przejdź do strony lub posta gdzie chcesz wyświetlić nieruchomości</li>
                            <li>Wklej shortcode w edytorze treści (tryb tekstowy lub blok shortcode)</li>
                            <li>Opublikuj/zaktualizuj stronę</li>
                        </ol>

                        <h4>Dostępne parametry:</h4>
                        <ul>
                            <li><code>type</code> - apartments, houses, commercial, lots, rental</li>
                            <li><code>transaction</code> - sale, rent</li>
                            <li><code>count</code> - liczba nieruchomości na stronę (1-50)</li>
                            <li><code>price_min, price_max</code> - zakres cenowy</li>
                            <li><code>area_min, area_max</code> - zakres powierzchni</li>
                            <li><code>bedrooms</code> - minimalna liczba sypialni</li>
                            <li><code>city</code> - nazwa miasta</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'property_page_asarinos-shortcodes') {
            return;
        }
    ?>
        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            .asarinos-admin-container {
                max-width: 1200px;
            }

            .shortcode-section {
                background: #fff;
                padding: 25px;
                margin: 25px 0;
                border: 1px solid #ddd;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .shortcode-section h2 {
                margin-top: 0;
                color: #1d2327;
                border-bottom: 3px solid #007cba;
                padding-bottom: 15px;
                font-size: 22px;
            }

            .shortcode-box {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border: 2px solid #007cba;
                border-radius: 8px;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 15px 0;
            }

            .shortcode-box code {
                background: none;
                padding: 0;
                color: #d63384;
                font-weight: 600;
                font-size: 14px;
                flex: 1;
                word-break: break-all;
                font-family: 'Monaco', 'Consolas', monospace;
            }

            .copy-btn {
                background: linear-gradient(135deg, #007cba, #005a87);
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 13px;
                font-weight: 600;
                margin-left: 15px;
                transition: all 0.3s ease;
            }

            .copy-btn:hover {
                background: linear-gradient(135deg, #005a87, #004066);
                transform: translateY(-2px);
            }

            .copy-btn.copied {
                background: linear-gradient(135deg, #28a745, #1e7e34);
                transform: scale(0.95);
            }

            .shortcode-form {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 8px;
                margin: 20px 0;
                border: 1px solid #dee2e6;
            }

            .shortcode-form .form-table th {
                width: 200px;
                font-weight: 600;
                color: #495057;
            }

            .shortcode-form input[type="number"],
            .shortcode-form input[type="text"],
            .shortcode-form select {
                min-width: 200px;
                padding: 8px 12px;
                border: 1px solid #ced4da;
                border-radius: 4px;
            }

            .shortcode-output {
                background: linear-gradient(135deg, #fff3cd, #ffeaa7);
                border: 2px solid #ffc107;
                border-radius: 8px;
                padding: 25px;
                margin: 20px 0;
                transition: all 0.5s ease;
            }

            .shortcode-output.highlighted {
                background: linear-gradient(135deg, #d4edda, #c3e6cb);
                border-color: #28a745;
                transform: scale(1.02);
            }

            .shortcode-output h3 {
                margin-top: 0;
                color: #856404;
            }

            .examples-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .example-item {
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 20px;
                background: #fff;
                transition: all 0.3s ease;
            }

            .example-item:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-3px);
            }

            .example-item h4 {
                margin: 0 0 15px 0;
                color: #495057;
                font-size: 16px;
            }

            .description {
                font-style: italic;
                color: #6c757d;
                font-size: 13px;
                margin-top: 5px;
            }

            #generate-shortcode {
                font-size: 16px;
                padding: 12px 24px;
                height: auto;
            }

            /* Alpine.js transition classes */
            [x-cloak] {
                display: none !important;
            }
        </style>

        <script>
            function shortcodeGenerator() {
                return {
                    formData: {
                        type: '',
                        transaction: '',
                        count: '10',
                        price_min: '',
                        price_max: '',
                        area_min: '',
                        area_max: '',
                        bedrooms: '',
                        city: ''
                    },

                    generatedShortcode: '[asarinos_filtered_properties]',
                    copiedButton: null,
                    isHighlighted: false,

                    examples: [{
                            title: '🏘️ Domy na Sprzedaż',
                            shortcode: '[asarinos_filtered_properties type="houses" transaction="sale"]'
                        },
                        {
                            title: '🏢 Apartamenty do Wynajmu',
                            shortcode: '[asarinos_filtered_properties type="apartments" transaction="rent"]'
                        },
                        {
                            title: '💰 Nieruchomości w Budżecie',
                            shortcode: '[asarinos_filtered_properties price_min="200000" price_max="500000"]'
                        },
                        {
                            title: '📏 Duże Nieruchomości (100m²+)',
                            shortcode: '[asarinos_filtered_properties area_min="100" count="12"]'
                        },
                        {
                            title: '🏙️ Nieruchomości w Krakowie',
                            shortcode: '[asarinos_filtered_properties city="Kraków" count="15"]'
                        },
                        {
                            title: '🛏️ Min. 3 Sypialnie',
                            shortcode: '[asarinos_filtered_properties bedrooms="3"]'
                        }
                    ],

                    generateShortcode() {
                        let shortcode = '[asarinos_filtered_properties';
                        let params = [];

                        // Build parameters from form data
                        Object.keys(this.formData).forEach(key => {
                            const value = this.formData[key];
                            if (value && value.toString().trim() !== '') {
                                params.push(`${key}="${value}"`);
                            }
                        });

                        // Add parameters to shortcode
                        if (params.length > 0) {
                            shortcode += ' ' + params.join(' ');
                        }
                        shortcode += ']';

                        // Update generated shortcode
                        this.generatedShortcode = shortcode;

                        // Highlight the output section
                        this.isHighlighted = true;
                        setTimeout(() => {
                            this.isHighlighted = false;
                        }, 1500);
                    },

                    async copyToClipboard(text, button) {
                        // Determine button identifier first (before any try/catch blocks)
                        let buttonId = 'main';
                        if (text === this.generatedShortcode) {
                            buttonId = 'generated';
                        } else if (text !== '[asarionos_mainpage]') {
                            // Find example index
                            const exampleIndex = this.examples.findIndex(ex => ex.shortcode === text);
                            if (exampleIndex !== -1) {
                                buttonId = `example-${exampleIndex}`;
                            }
                        }

                        try {
                            await navigator.clipboard.writeText(text);

                            // Show visual feedback
                            this.copiedButton = buttonId;

                            // Reset after 2 seconds
                            setTimeout(() => {
                                this.copiedButton = null;
                            }, 2000);

                        } catch (err) {
                            // Fallback for older browsers
                            const textArea = document.createElement('textarea');
                            textArea.value = text;
                            textArea.style.position = 'fixed';
                            textArea.style.left = '-999999px';
                            textArea.style.top = '-999999px';
                            document.body.appendChild(textArea);
                            textArea.focus();
                            textArea.select();

                            try {
                                document.execCommand('copy');
                                // buttonId is now defined here
                                this.copiedButton = buttonId;
                                setTimeout(() => {
                                    this.copiedButton = null;
                                }, 2000);
                            } catch (err) {
                                console.error('Could not copy text: ', err);
                            }

                            textArea.remove();
                        }
                    }
                }
            }
        </script>
<?php
    }

    /**
     * Handle form submissions (existing method)
     */
    private function handle_form_submissions()
    {
        // Handle hours update
        if (isset($_POST['hours']) && check_admin_referer('asarinos_hours_action', 'asarinos_hours_nonce')) {
            if (isset($_POST['hours_update'])) {
                $hours = sanitize_text_field($_POST['hours_update']);
                update_option('asarino_hours', $hours);
                echo '<div class="notice notice-success"><p>Zaktualizowano godziny.</p></div>';
            }
        }

        // Handle property sync
        if (isset($_POST['check']) && check_admin_referer('asarinos_admin_action', 'asarinos_nonce')) {
            echo '<div class="asarinos-log">';
            echo '<h3>Log synchronizacji:</h3>';

            $this->sync_properties();

            echo '</div>';
        }
    }

    /**
     * Sync properties with Esti API (existing method)
     */
    private function sync_properties()
    {
        $houses_data = Asarinos_API_Client::get_houses();
        $db_houses = Asarinos_API_Client::get_db_houses();

        if (count($houses_data['data']) < 1) {
            echo '<p class="error">Brak danych z API Esti.</p>';
            return;
        }

        $exported_offers = Asarinos_API_Client::get_exported_offers_numbers();
        $actual_ids = array();

        echo '<p class="info">Znaleziono ' . count($houses_data['data']) . ' ofert w systemie Esti.</p>';
        echo '<p class="info">Znaleziono ' . count($exported_offers) . ' ofert oznaczonych do eksportu.</p>';

        // Process each property
        foreach ($houses_data['data'] as $property_data) {

            if (!$property_data['portalTitle']) {
                continue;
            }

            // Check if images are valid
            if (
                !$property_data['pictures'][0] ||
                !preg_match('/^https:\/\//', $property_data['pictures'][0])
            ) {
                echo '<p class="error">Oferta "' . esc_html($property_data['portalTitle']) . '" nie ma prawidłowych zdjęć.</p>';
                continue;
            }

            // Check if property is marked for export
            if (!in_array($property_data['id'], $exported_offers)) {
                echo '<p class="info">Oferta "' . esc_html($property_data['portalTitle']) . '" nie jest oznaczona do eksportu.</p>';
                continue;
            } else {
                echo '<p class="success">Oferta ID: ' . esc_html($property_data['id']) . ' jest oznaczona do eksportu.</p>';
            }

            // Check if property exists in database
            if (
                is_array($db_houses['property_id']) &&
                in_array($property_data['id'], $db_houses['property_id'])
            ) {

                $actual_ids[] = $property_data['id'];
                echo '<p class="info">Oferta "' . esc_html($property_data['portalTitle']) . '" jest w bazie pod numerem ID ' . $db_houses['ID'][$property_data['id']] . '</p>';

                // Check if update is needed
                if ($db_houses['date'][$property_data['id']] != $property_data['updateDate']) {
                    echo '<p class="success">Oferta "' . esc_html($property_data['portalTitle']) . '" jest aktualizowana.</p>';
                    wp_delete_post($db_houses['ID'][$property_data['id']]);
                    Asarinos_Property_Manager::add_property($property_data);
                }
            } else {
                echo '<p class="success">Oferta "' . esc_html($property_data['portalTitle']) . '" zostaje eksportowana.</p>';
                Asarinos_Property_Manager::add_property($property_data);
            }
        }

        // Remove properties that are no longer in the API
        $duplicates = array();
        foreach ($db_houses['property_id'] as $house_id) {
            if (!in_array($house_id, $actual_ids) && $house_id) {
                echo '<p class="error">Usunięto nieruchomość o numerze: ' . $db_houses['ID'][$house_id] . '</p>';
                wp_delete_post($db_houses['ID'][$house_id]);
            }

            $duplicates[$house_id] = isset($duplicates[$house_id]) ? $duplicates[$house_id] + 1 : 1;

            if ($duplicates[$house_id] >= 2) {
                echo '<p class="error"><b>Nieruchomość o ID: ' . $db_houses['ID'][$house_id] . ' się dubluje więc została usunięta</b></p>';
                wp_delete_post($db_houses['ID'][$house_id]);
            }
        }

        echo '<p class="success"><strong>Synchronizacja zakończona pomyślnie!</strong></p>';
    }
}
