<?php
/**
 * Charts Module
 *
 * @package AStats\TablesCharts\Modules\Charts
 */

namespace AStats\TablesCharts\Modules\Charts;

use AStats\TablesCharts\Core\AbstractModule;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Charts module main class
 */
class Module extends AbstractModule {

    /**
     * Initialize the module
     */
    protected function init() {
        // Register admin menu
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );

        // Register shortcode
        add_shortcode( 'astats-chart', array( $this, 'render_shortcode' ) );

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Admin assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        add_submenu_page(
            'astats-dashboard',
            __( 'Charts', 'astats-tables-charts' ),
            __( 'Charts', 'astats-tables-charts' ),
            'manage_options',
            'astats-charts',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        include $this->get_path() . '/templates/placeholder.php';
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'astats-charts' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'astats-charts-admin',
            $this->get_url() . 'assets/css/admin.css',
            array(),
            $this->get_version()
        );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Chart.js will be enqueued here when implemented
    }

    /**
     * Render shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'astats-chart' );

        // Placeholder - will be implemented later
        return '<div class="astats-chart-wrapper"><p>' . esc_html__( 'Charts module coming soon!', 'astats-tables-charts' ) . '</p></div>';
    }
}
