<?php
/**
 * Tables Module
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

namespace AStats\TablesCharts\Modules\Tables;

use AStats\TablesCharts\Core\AbstractModule;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Tables module main class
 */
class Module extends AbstractModule {

    /**
     * Initialize the module
     */
    protected function init() {
        // Register admin menu (priority 20 to run after parent menu is registered)
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );

        // Register shortcode
        add_shortcode( 'astats-table', array( $this, 'render_shortcode' ) );

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        // Admin assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // AJAX handlers
        add_action( 'wp_ajax_astats_tables_save', array( $this, 'ajax_save_table' ) );
        add_action( 'wp_ajax_astats_tables_delete', array( $this, 'ajax_delete_table' ) );
        add_action( 'wp_ajax_astats_tables_get', array( $this, 'ajax_get_table' ) );
    }

    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        add_submenu_page(
            'astats-dashboard',
            __( 'Tables', 'astats-tables-charts' ),
            __( 'Tables', 'astats-tables-charts' ),
            'manage_options',
            'astats-tables',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : 'list';
        $table_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

        switch ( $action ) {
            case 'new':
            case 'edit':
                $this->render_editor( $table_id );
                break;
            default:
                $this->render_list();
                break;
        }
    }

    /**
     * Render table list
     */
    private function render_list() {
        $tables = $this->get_all_tables();
        include $this->get_path() . '/templates/list.php';
    }

    /**
     * Render table editor
     *
     * @param int $table_id Table ID (0 for new).
     */
    private function render_editor( $table_id = 0 ) {
        $table = $table_id ? $this->get_table( $table_id ) : null;
        $columns = $table_id ? $this->get_columns( $table_id ) : array();
        $rows = $table_id ? $this->get_rows( $table_id ) : array();
        include $this->get_path() . '/templates/editor.php';
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'astats-tables' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'astats-tables-admin',
            $this->get_url() . 'assets/css/admin.css',
            array(),
            $this->get_version()
        );

        wp_enqueue_script(
            'astats-tables-admin',
            $this->get_url() . 'assets/js/admin.js',
            array( 'jquery' ),
            $this->get_version(),
            true
        );

        wp_localize_script( 'astats-tables-admin', 'astatsTablesAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'astats_tables_nonce' ),
            'strings' => array(
                'saving'        => __( 'Saving...', 'astats-tables-charts' ),
                'saved'         => __( 'Table saved!', 'astats-tables-charts' ),
                'deleting'      => __( 'Deleting...', 'astats-tables-charts' ),
                'confirmDelete' => __( 'Are you sure you want to delete this table?', 'astats-tables-charts' ),
                'error'         => __( 'An error occurred', 'astats-tables-charts' ),
                'titleRequired' => __( 'Please enter a table title', 'astats-tables-charts' ),
            ),
        ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'astats-tables-frontend',
            $this->get_url() . 'assets/css/frontend.css',
            array(),
            $this->get_version()
        );

        wp_enqueue_script(
            'astats-tables-frontend',
            $this->get_url() . 'assets/js/frontend.js',
            array( 'jquery' ),
            $this->get_version(),
            true
        );
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
        ), $atts, 'astats-table' );

        $table_id = absint( $atts['id'] );

        if ( ! $table_id ) {
            return '';
        }

        $table = $this->get_table( $table_id );

        if ( ! $table ) {
            return '';
        }

        $columns = $this->get_columns( $table_id );
        $rows = $this->get_rows( $table_id );
        $settings = json_decode( $table->settings ?? '{}', true ) ?: array();
        $theme = isset( $settings['theme'] ) ? $settings['theme'] : 'default';

        ob_start();
        include $this->get_path() . '/templates/frontend.php';
        return ob_get_clean();
    }

    /**
     * Get all tables
     *
     * @return array
     */
    public function get_all_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'astats_tables_tables';

        return $wpdb->get_results(
            "SELECT t.*,
                    (SELECT COUNT(*) FROM {$wpdb->prefix}astats_tables_columns WHERE table_id = t.id) as column_count,
                    (SELECT COUNT(*) FROM {$wpdb->prefix}astats_tables_rows WHERE table_id = t.id) as row_count
             FROM {$table_name} t
             ORDER BY t.created_at DESC"
        );
    }

    /**
     * Get single table
     *
     * @param int $table_id Table ID.
     * @return object|null
     */
    public function get_table( $table_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'astats_tables_tables';

        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $table_id )
        );
    }

    /**
     * Get table columns
     *
     * @param int $table_id Table ID.
     * @return array
     */
    public function get_columns( $table_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'astats_tables_columns';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE table_id = %d ORDER BY column_order ASC",
                $table_id
            )
        );
    }

    /**
     * Get table rows
     *
     * @param int $table_id Table ID.
     * @return array
     */
    public function get_rows( $table_id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'astats_tables_rows';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE table_id = %d ORDER BY row_order ASC",
                $table_id
            )
        );
    }

    /**
     * AJAX: Save table
     */
    public function ajax_save_table() {
        check_ajax_referer( 'astats_tables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'astats-tables-charts' ) ) );
        }

        global $wpdb;

        $table_id = isset( $_POST['table_id'] ) ? absint( $_POST['table_id'] ) : 0;
        $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
        $description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
        $columns = isset( $_POST['columns'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['columns'] ) ) : array();
        $rows = isset( $_POST['rows'] ) ? wp_unslash( $_POST['rows'] ) : array();
        $settings = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();

        if ( empty( $title ) ) {
            wp_send_json_error( array( 'message' => __( 'Title is required', 'astats-tables-charts' ) ) );
        }

        $tables_table = $wpdb->prefix . 'astats_tables_tables';
        $columns_table = $wpdb->prefix . 'astats_tables_columns';
        $rows_table = $wpdb->prefix . 'astats_tables_rows';

        // Sanitize settings
        $sanitized_settings = array();
        if ( is_array( $settings ) ) {
            foreach ( $settings as $key => $value ) {
                $sanitized_settings[ sanitize_key( $key ) ] = sanitize_text_field( $value );
            }
        }

        // Insert or update table
        if ( $table_id ) {
            $wpdb->update(
                $tables_table,
                array(
                    'title'       => $title,
                    'description' => $description,
                    'settings'    => wp_json_encode( $sanitized_settings ),
                    'updated_at'  => current_time( 'mysql' ),
                ),
                array( 'id' => $table_id ),
                array( '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );
        } else {
            $wpdb->insert(
                $tables_table,
                array(
                    'title'       => $title,
                    'description' => $description,
                    'settings'    => wp_json_encode( $sanitized_settings ),
                    'created_at'  => current_time( 'mysql' ),
                    'updated_at'  => current_time( 'mysql' ),
                ),
                array( '%s', '%s', '%s', '%s', '%s' )
            );
            $table_id = $wpdb->insert_id;
        }

        // Delete existing columns and rows
        $wpdb->delete( $columns_table, array( 'table_id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $rows_table, array( 'table_id' => $table_id ), array( '%d' ) );

        // Insert columns
        foreach ( $columns as $order => $column_name ) {
            $wpdb->insert(
                $columns_table,
                array(
                    'table_id'     => $table_id,
                    'column_name'  => $column_name,
                    'column_order' => $order,
                ),
                array( '%d', '%s', '%d' )
            );
        }

        // Insert rows
        foreach ( $rows as $order => $row_data ) {
            // Sanitize row data
            $sanitized_row = array();
            if ( is_array( $row_data ) ) {
                foreach ( $row_data as $key => $value ) {
                    $sanitized_row[ sanitize_key( $key ) ] = sanitize_text_field( $value );
                }
            }

            $wpdb->insert(
                $rows_table,
                array(
                    'table_id'  => $table_id,
                    'row_data'  => wp_json_encode( $sanitized_row ),
                    'row_order' => $order,
                ),
                array( '%d', '%s', '%d' )
            );
        }

        wp_send_json_success( array(
            'message'  => __( 'Table saved successfully', 'astats-tables-charts' ),
            'table_id' => $table_id,
        ) );
    }

    /**
     * AJAX: Delete table
     */
    public function ajax_delete_table() {
        check_ajax_referer( 'astats_tables_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Unauthorized', 'astats-tables-charts' ) ) );
        }

        global $wpdb;

        $table_id = isset( $_POST['table_id'] ) ? absint( $_POST['table_id'] ) : 0;

        if ( ! $table_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid table ID', 'astats-tables-charts' ) ) );
        }

        // Delete table and related data
        $wpdb->delete( $wpdb->prefix . 'astats_tables_tables', array( 'id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->prefix . 'astats_tables_columns', array( 'table_id' => $table_id ), array( '%d' ) );
        $wpdb->delete( $wpdb->prefix . 'astats_tables_rows', array( 'table_id' => $table_id ), array( '%d' ) );

        wp_send_json_success( array( 'message' => __( 'Table deleted', 'astats-tables-charts' ) ) );
    }

    /**
     * AJAX: Get table data
     */
    public function ajax_get_table() {
        check_ajax_referer( 'astats_tables_nonce', 'nonce' );

        $table_id = isset( $_POST['table_id'] ) ? absint( $_POST['table_id'] ) : 0;

        if ( ! $table_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid table ID', 'astats-tables-charts' ) ) );
        }

        $table = $this->get_table( $table_id );

        if ( ! $table ) {
            wp_send_json_error( array( 'message' => __( 'Table not found', 'astats-tables-charts' ) ) );
        }

        wp_send_json_success( array(
            'table'   => $table,
            'columns' => $this->get_columns( $table_id ),
            'rows'    => $this->get_rows( $table_id ),
        ) );
    }
}
