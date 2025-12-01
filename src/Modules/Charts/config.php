<?php
/**
 * Charts module configuration
 *
 * @package AStats\TablesCharts\Modules\Charts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return array(
    'name'        => __( 'Charts', 'astats-tables-charts' ),
    'description' => __( 'Create stunning, interactive charts with multiple chart types and data import options.', 'astats-tables-charts' ),
    'version'     => '1.0.0',
    'icon'        => 'dashicons-chart-bar',
    'color'       => '#9b59b6',
);
