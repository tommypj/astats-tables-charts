<?php
/**
 * Admin class
 *
 * @package AStats\TablesCharts\Admin
 */

namespace AStats\TablesCharts\Admin;

use AStats\TablesCharts\Core\ModuleLoader;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main admin class
 */
class Admin {

    /**
     * Module loader
     *
     * @var ModuleLoader
     */
    private $module_loader;

    /**
     * Constructor
     *
     * @param ModuleLoader $module_loader Module loader instance.
     */
    public function __construct( ModuleLoader $module_loader ) {
        $this->module_loader = $module_loader;
        $this->init();
    }

    /**
     * Initialize admin
     */
    private function init() {
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_astats_toggle_module', array( $this, 'ajax_toggle_module' ) );
        add_action( 'wp_ajax_astats_save_settings', array( $this, 'ajax_save_settings' ) );
    }

    /**
     * Register admin menu
     */
    public function register_menu() {
        // Main menu
        add_menu_page(
            __( 'AStats Tables & Charts', 'astats-tables-charts' ),
            __( 'AStats', 'astats-tables-charts' ),
            'manage_options',
            'astats-dashboard',
            array( $this, 'render_dashboard' ),
            'dashicons-chart-area',
            30
        );

        // Dashboard submenu
        add_submenu_page(
            'astats-dashboard',
            __( 'Dashboard', 'astats-tables-charts' ),
            __( 'Dashboard', 'astats-tables-charts' ),
            'manage_options',
            'astats-dashboard',
            array( $this, 'render_dashboard' )
        );

        // Modules submenu
        add_submenu_page(
            'astats-dashboard',
            __( 'Modules', 'astats-tables-charts' ),
            __( 'Modules', 'astats-tables-charts' ),
            'manage_options',
            'astats-modules',
            array( $this, 'render_modules' )
        );

        // Settings submenu
        add_submenu_page(
            'astats-dashboard',
            __( 'Settings', 'astats-tables-charts' ),
            __( 'Settings', 'astats-tables-charts' ),
            'manage_options',
            'astats-settings',
            array( $this, 'render_settings' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current page hook.
     */
    public function enqueue_assets( $hook ) {
        // Only load on our admin pages
        if ( strpos( $hook, 'astats' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'astats-admin',
            ASTATS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ASTATS_VERSION
        );

        wp_enqueue_script(
            'astats-admin',
            ASTATS_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            ASTATS_VERSION,
            true
        );

        wp_localize_script( 'astats-admin', 'astatsAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'astats_admin_nonce' ),
            'strings' => array(
                'activating'   => __( 'Activating...', 'astats-tables-charts' ),
                'deactivating' => __( 'Deactivating...', 'astats-tables-charts' ),
                'saving'       => __( 'Saving...', 'astats-tables-charts' ),
                'saved'        => __( 'Settings saved!', 'astats-tables-charts' ),
                'error'        => __( 'An error occurred', 'astats-tables-charts' ),
            ),
        ) );
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard() {
        $modules = $this->module_loader->get_modules();
        include ASTATS_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }

    /**
     * Render modules page
     */
    public function render_modules() {
        $modules = $this->module_loader->get_modules();
        include ASTATS_PLUGIN_DIR . 'templates/admin/modules.php';
    }

    /**
     * Render settings page
     */
    public function render_settings() {
        $settings = get_option( 'astats_settings', array() );
        include ASTATS_PLUGIN_DIR . 'templates/admin/settings.php';
    }

    /**
     * AJAX: Toggle module activation
     */
    public function ajax_toggle_module() {
        check_ajax_referer( 'astats_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'astats-tables-charts' ) ) );
        }

        $module = isset( $_POST['module'] ) ? sanitize_text_field( wp_unslash( $_POST['module'] ) ) : '';
        $action = isset( $_POST['module_action'] ) ? sanitize_text_field( wp_unslash( $_POST['module_action'] ) ) : '';

        if ( empty( $module ) || ! in_array( $action, array( 'activate', 'deactivate' ), true ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request', 'astats-tables-charts' ) ) );
        }

        if ( 'activate' === $action ) {
            $result = $this->module_loader->activate_module( $module );
        } else {
            $result = $this->module_loader->deactivate_module( $module );
        }

        if ( $result ) {
            wp_send_json_success( array(
                'message' => 'activate' === $action
                    ? __( 'Module activated', 'astats-tables-charts' )
                    : __( 'Module deactivated', 'astats-tables-charts' ),
                'active'  => 'activate' === $action,
            ) );
        }

        wp_send_json_error( array( 'message' => __( 'Failed to toggle module', 'astats-tables-charts' ) ) );
    }

    /**
     * AJAX: Save settings
     */
    public function ajax_save_settings() {
        check_ajax_referer( 'astats_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'astats-tables-charts' ) ) );
        }

        $settings = isset( $_POST['settings'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['settings'] ) ) : array();

        update_option( 'astats_settings', $settings );

        wp_send_json_success( array( 'message' => __( 'Settings saved', 'astats-tables-charts' ) ) );
    }

    /**
     * Get module loader
     *
     * @return ModuleLoader
     */
    public function get_module_loader() {
        return $this->module_loader;
    }
}
