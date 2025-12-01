<?php
/**
 * Abstract Module class
 *
 * @package AStats\TablesCharts\Core
 */

namespace AStats\TablesCharts\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Base class for all modules
 */
abstract class AbstractModule {

    /**
     * Module configuration
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param array $config Module configuration.
     */
    public function __construct( array $config ) {
        $this->config = $config;
        $this->init();
    }

    /**
     * Initialize the module
     */
    abstract protected function init();

    /**
     * Get module slug
     *
     * @return string
     */
    public function get_slug() {
        return $this->config['slug'];
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function get_name() {
        return $this->config['name'];
    }

    /**
     * Get module description
     *
     * @return string
     */
    public function get_description() {
        return isset( $this->config['description'] ) ? $this->config['description'] : '';
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function get_version() {
        return isset( $this->config['version'] ) ? $this->config['version'] : '1.0.0';
    }

    /**
     * Get module icon
     *
     * @return string
     */
    public function get_icon() {
        return isset( $this->config['icon'] ) ? $this->config['icon'] : 'dashicons-admin-generic';
    }

    /**
     * Get module color
     *
     * @return string
     */
    public function get_color() {
        return isset( $this->config['color'] ) ? $this->config['color'] : '#3498db';
    }

    /**
     * Get module path
     *
     * @return string
     */
    public function get_path() {
        return $this->config['path'];
    }

    /**
     * Get module URL
     *
     * @return string
     */
    public function get_url() {
        return ASTATS_PLUGIN_URL . 'src/Modules/' . ucfirst( $this->get_slug() ) . '/';
    }

    /**
     * Enqueue module assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        // Override in child classes
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Override in child classes
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        // Override in child classes
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        // Override in child classes
    }
}
