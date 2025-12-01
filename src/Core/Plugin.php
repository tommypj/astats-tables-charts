<?php
/**
 * Main Plugin class
 *
 * @package AStats\TablesCharts\Core
 */

namespace AStats\TablesCharts\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin singleton class
 */
class Plugin {

    /**
     * Singleton instance
     *
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * Module loader instance
     *
     * @var ModuleLoader
     */
    private $module_loader;

    /**
     * Admin instance
     *
     * @var \AStats\TablesCharts\Admin\Admin
     */
    private $admin;

    /**
     * Get singleton instance
     *
     * @return Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Initialize plugin
     */
    private function init() {
        // Load text domain
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Initialize module loader
        $this->module_loader = new ModuleLoader();

        // Initialize admin if in admin context
        if ( is_admin() ) {
            $this->admin = new \AStats\TablesCharts\Admin\Admin( $this->module_loader );
        }

        // Initialize frontend shortcodes
        add_action( 'init', array( $this, 'init_shortcodes' ) );
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'astats-tables-charts',
            false,
            dirname( ASTATS_PLUGIN_BASENAME ) . '/languages'
        );
    }

    /**
     * Initialize shortcodes
     */
    public function init_shortcodes() {
        // Shortcodes will be registered by individual modules
    }

    /**
     * Get module loader
     *
     * @return ModuleLoader
     */
    public function get_module_loader() {
        return $this->module_loader;
    }

    /**
     * Plugin activation
     */
    public static function activate() {
        // Create options table for module states
        add_option( 'astats_module_states', array() );
        add_option( 'astats_settings', array() );

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
