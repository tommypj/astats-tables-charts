<?php
/**
 * Tables API for external module access
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

namespace AStats\TablesCharts\Modules\Tables;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * API class for Tables module
 * Used by Charts module to import table data
 */
class API {

    /**
     * Get table data by ID
     *
     * @param int $table_id Table ID.
     * @return array|null
     */
    public static function get_table_data( $table_id ) {
        global $wpdb;

        $table = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}astats_tables_tables WHERE id = %d",
                $table_id
            )
        );

        if ( ! $table ) {
            return null;
        }

        $columns = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}astats_tables_columns WHERE table_id = %d ORDER BY column_order ASC",
                $table_id
            )
        );

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}astats_tables_rows WHERE table_id = %d ORDER BY row_order ASC",
                $table_id
            )
        );

        // Parse row data
        $parsed_rows = array();
        foreach ( $rows as $row ) {
            $parsed_rows[] = json_decode( $row->row_data, true );
        }

        return array(
            'id'          => $table->id,
            'title'       => $table->title,
            'description' => $table->description,
            'columns'     => wp_list_pluck( $columns, 'column_name' ),
            'rows'        => $parsed_rows,
        );
    }

    /**
     * Get all tables (for dropdown selection)
     *
     * @return array
     */
    public static function get_all_tables() {
        global $wpdb;

        $tables = $wpdb->get_results(
            "SELECT id, title FROM {$wpdb->prefix}astats_tables_tables ORDER BY title ASC"
        );

        $result = array();
        foreach ( $tables as $table ) {
            $result[ $table->id ] = $table->title;
        }

        return $result;
    }

    /**
     * Check if Tables module is active
     *
     * @return bool
     */
    public static function is_active() {
        $module_states = get_option( 'astats_module_states', array() );
        return isset( $module_states['tables'] ) && $module_states['tables'];
    }
}
