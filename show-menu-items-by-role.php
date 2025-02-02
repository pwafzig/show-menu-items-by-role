<?php

/**
 * Plugin Name: Show Menu Items by Role
 * Plugin URI: https://github.com/pwafzig/show-menu-items-by-role
 * Description: Show specific navigation menu items and content based on user role / used for the internal website of LSC Erftland e.V. flying club
 * Version: 1.0.1
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Peter Wafzig
 * Author URI: https://www.lsc-erftland.de
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: smibyrole
 */

// Define constants
define('SMI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SMI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SMI_PLUGIN_DIR . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/custom-fields.php';

// Initialize the plugin
add_action('plugins_loaded', 'smi_init');
function smi_init()
{
    // Initialize the plugin code here
}
