<?php
/*
    Plugin Name: Asarinos
    Plugin URI: http://baldbold.eu
    Description: Plugin do aktualizowania listy mieszkań
    Author: Marcin Szymański
    Version: 1.3
    Author URI: http://baldbold.eu
    License: All Rights Reserved 
*/

if (!defined('ABSPATH')) {
	exit;
}

define('ASARINOS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASARINOS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ASARINOS_VERSION', '1.3');

require_once ASARINOS_PLUGIN_DIR . 'vendor/autoload.php';

require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-core.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-media.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-property-manager.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-api-client.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-admin.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-frontend.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-cron.php';
require_once ASARINOS_PLUGIN_DIR . 'includes/class-asarinos-shortcodes.php';

// Include the refactored shortcode
require_once ASARINOS_PLUGIN_DIR . 'main-page-shortcode.php';

// Initialize the plugin
function asarinos_init()
{
	new Asarinos_Core();
}
add_action('plugins_loaded', 'asarinos_init');

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array('Asarinos_Core', 'activate'));
register_deactivation_hook(__FILE__, array('Asarinos_Core', 'deactivate'));
