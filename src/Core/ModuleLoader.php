<?php
/**
 * Module Loader class
 *
 * @package AStats\TablesCharts\Core
 */

namespace AStats\TablesCharts\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles loading and managing modules
 */
class ModuleLoader {

    /**
     * Available modules
     *
     * @var array
     */
    private $modules = array();

    /**
     * Active modules
     *
     * @var array
     */
    private $active_modules = array();

    /**
     * Module states from database
     *
     * @var array
     */
    private $module_states = array();

    /**
     * Constructor
     */
    public function __construct() {
        $this->module_states = get_option( 'astats_module_states', array() );
        $this->discover_modules();
        $this->load_active_modules();
    }

    /**
     * Discover available modules
     */
    private function discover_modules() {
        $modules_dir = ASTATS_PLUGIN_DIR . 'src/Modules/';

        if ( ! is_dir( $modules_dir ) ) {
            return;
        }

        $directories = glob( $modules_dir . '*', GLOB_ONLYDIR );

        foreach ( $directories as $dir ) {
            $module_name = basename( $dir );
            $config_file = $dir . '/config.php';
            $module_file = $dir . '/Module.php';

            if ( file_exists( $config_file ) && file_exists( $module_file ) ) {
                $config = include $config_file;
                $config['slug'] = strtolower( $module_name );
                $config['path'] = $dir;
                $config['active'] = isset( $this->module_states[ $config['slug'] ] )
                    ? $this->module_states[ $config['slug'] ]
                    : false;

                $this->modules[ $config['slug'] ] = $config;
            }
        }
    }

    /**
     * Load active modules
     */
    private function load_active_modules() {
        foreach ( $this->modules as $slug => $config ) {
            if ( $config['active'] ) {
                $this->load_module( $slug );
            }
        }
    }

    /**
     * Load a specific module
     *
     * @param string $slug Module slug.
     * @return bool
     */
    public function load_module( $slug ) {
        if ( ! isset( $this->modules[ $slug ] ) ) {
            return false;
        }

        $config = $this->modules[ $slug ];
        $class_name = 'AStats\\TablesCharts\\Modules\\' . ucfirst( $slug ) . '\\Module';

        if ( class_exists( $class_name ) ) {
            $this->active_modules[ $slug ] = new $class_name( $config );
            return true;
        }

        return false;
    }

    /**
     * Get all available modules
     *
     * @return array
     */
    public function get_modules() {
        return $this->modules;
    }

    /**
     * Get active modules
     *
     * @return array
     */
    public function get_active_modules() {
        return $this->active_modules;
    }

    /**
     * Check if module is active
     *
     * @param string $slug Module slug.
     * @return bool
     */
    public function is_module_active( $slug ) {
        return isset( $this->modules[ $slug ] ) && $this->modules[ $slug ]['active'];
    }

    /**
     * Activate a module
     *
     * @param string $slug Module slug.
     * @return bool
     */
    public function activate_module( $slug ) {
        if ( ! isset( $this->modules[ $slug ] ) ) {
            return false;
        }

        // Run activation script if exists
        $activate_file = $this->modules[ $slug ]['path'] . '/activate.php';
        if ( file_exists( $activate_file ) ) {
            include $activate_file;
        }

        // Update state
        $this->module_states[ $slug ] = true;
        $this->modules[ $slug ]['active'] = true;
        update_option( 'astats_module_states', $this->module_states );

        // Load the module
        $this->load_module( $slug );

        return true;
    }

    /**
     * Deactivate a module
     *
     * @param string $slug Module slug.
     * @return bool
     */
    public function deactivate_module( $slug ) {
        if ( ! isset( $this->modules[ $slug ] ) ) {
            return false;
        }

        // Run deactivation script if exists
        $deactivate_file = $this->modules[ $slug ]['path'] . '/deactivate.php';
        if ( file_exists( $deactivate_file ) ) {
            include $deactivate_file;
        }

        // Update state
        $this->module_states[ $slug ] = false;
        $this->modules[ $slug ]['active'] = false;
        update_option( 'astats_module_states', $this->module_states );

        // Remove from active modules
        unset( $this->active_modules[ $slug ] );

        return true;
    }

    /**
     * Get module instance
     *
     * @param string $slug Module slug.
     * @return AbstractModule|null
     */
    public function get_module( $slug ) {
        return isset( $this->active_modules[ $slug ] ) ? $this->active_modules[ $slug ] : null;
    }
}
