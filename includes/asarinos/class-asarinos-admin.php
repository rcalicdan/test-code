<?php
/**
 * Admin interface class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Asarinos_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            "Synchronizacja z Esti", 
            "Synchronizacja z Esti", 
            'manage_options', 
            "synchronizacja-z-esti", 
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
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
        .success { color: #46b450; }
        .error { color: #dc3232; }
        .info { color: #0073aa; }
        </style>
        <?php
    }
    
    /**
     * Handle form submissions
     */
    private function handle_form_submissions() {
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
     * Sync properties with Esti API
     */
    private function sync_properties() {
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
            if (!$property_data['pictures'][0] || 
                !preg_match('/^https:\/\//', $property_data['pictures'][0])) {
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
            if (is_array($db_houses['property_id']) && 
                in_array($property_data['id'], $db_houses['property_id'])) {
                
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