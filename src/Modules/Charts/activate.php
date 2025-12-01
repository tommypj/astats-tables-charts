<?php
/**
 * Charts module activation script
 *
 * @package AStats\TablesCharts\Modules\Charts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();

// Charts table
$charts_table = $wpdb->prefix . 'astats_charts_charts';
$sql_charts = "CREATE TABLE {$charts_table} (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    chart_type varchar(50) NOT NULL DEFAULT 'bar',
    data_source varchar(50) NOT NULL DEFAULT 'manual',
    settings longtext,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) {$charset_collate};";

// Chart data table
$data_table = $wpdb->prefix . 'astats_charts_data';
$sql_data = "CREATE TABLE {$data_table} (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    chart_id bigint(20) UNSIGNED NOT NULL,
    data_points longtext NOT NULL,
    PRIMARY KEY (id),
    KEY chart_id (chart_id)
) {$charset_collate};";

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

dbDelta( $sql_charts );
dbDelta( $sql_data );
