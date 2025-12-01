<?php
/**
 * Tables module activation script
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();

// Tables table
$tables_table = $wpdb->prefix . 'astats_tables_tables';
$sql_tables = "CREATE TABLE {$tables_table} (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    description text,
    settings longtext,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) {$charset_collate};";

// Columns table
$columns_table = $wpdb->prefix . 'astats_tables_columns';
$sql_columns = "CREATE TABLE {$columns_table} (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    table_id bigint(20) UNSIGNED NOT NULL,
    column_name varchar(255) NOT NULL,
    column_order int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY table_id (table_id)
) {$charset_collate};";

// Rows table
$rows_table = $wpdb->prefix . 'astats_tables_rows';
$sql_rows = "CREATE TABLE {$rows_table} (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    table_id bigint(20) UNSIGNED NOT NULL,
    row_data longtext NOT NULL,
    row_order int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY table_id (table_id)
) {$charset_collate};";

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

dbDelta( $sql_tables );
dbDelta( $sql_columns );
dbDelta( $sql_rows );
