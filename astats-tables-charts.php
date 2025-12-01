<?php
/**
 * Plugin Name: AStats Tables & Charts
 * Plugin URI: https://github.com/tommypj/astats-tables-charts
 * Description: Create beautiful, responsive tables and charts for WordPress with inline editing and multiple themes.
 * Version: 1.0.0
 * Author: AStats
 * Author URI: https://astats.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: astats-tables-charts
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package AStats\TablesCharts
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'ASTATS_VERSION', '1.0.0' );
define( 'ASTATS_PLUGIN_FILE', __FILE__ );
define( 'ASTATS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASTATS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ASTATS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Autoloader
spl_autoload_register( function ( $class ) {
    $prefix = 'AStats\\TablesCharts\\';
    $base_dir = ASTATS_PLUGIN_DIR . 'src/';

    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }

    $relative_class = substr( $class, $len );
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
} );

/**
 * Initialize the plugin
 */
function astats_init() {
    return \AStats\TablesCharts\Core\Plugin::get_instance();
}

// Hook into WordPress
add_action( 'plugins_loaded', 'astats_init' );

// Activation hook
register_activation_hook( __FILE__, function () {
    \AStats\TablesCharts\Core\Plugin::activate();
} );

// Deactivation hook
register_deactivation_hook( __FILE__, function () {
    \AStats\TablesCharts\Core\Plugin::deactivate();
} );
