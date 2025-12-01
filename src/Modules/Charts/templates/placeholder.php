<?php
/**
 * Charts placeholder template
 *
 * @package AStats\TablesCharts\Modules\Charts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap astats-wrap">
    <div class="astats-header">
        <h1><?php esc_html_e( 'Charts', 'astats-tables-charts' ); ?></h1>
    </div>

    <div class="astats-charts-placeholder">
        <div class="astats-coming-soon">
            <span class="dashicons dashicons-chart-bar"></span>
            <h2><?php esc_html_e( 'Charts Module Coming Soon', 'astats-tables-charts' ); ?></h2>
            <p><?php esc_html_e( 'The Charts module is currently under development. Soon you\'ll be able to create stunning charts including:', 'astats-tables-charts' ); ?></p>
            <ul>
                <li><?php esc_html_e( 'Bar Charts', 'astats-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'Line Charts', 'astats-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'Pie & Doughnut Charts', 'astats-tables-charts' ); ?></li>
                <li><?php esc_html_e( 'Area Charts', 'astats-tables-charts' ); ?></li>
            </ul>
            <p><?php esc_html_e( 'You\'ll also be able to import data directly from your Tables!', 'astats-tables-charts' ); ?></p>
        </div>
    </div>
</div>

<style>
.astats-charts-placeholder {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 60px 40px;
    text-align: center;
    max-width: 600px;
}

.astats-coming-soon .dashicons {
    font-size: 80px;
    width: 80px;
    height: 80px;
    color: #9b59b6;
    margin-bottom: 20px;
}

.astats-coming-soon h2 {
    font-size: 24px;
    margin: 0 0 15px;
    color: #2c3e50;
}

.astats-coming-soon p {
    color: #666;
    font-size: 15px;
    margin-bottom: 20px;
}

.astats-coming-soon ul {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
}

.astats-coming-soon li {
    padding: 8px 0;
    color: #555;
}

.astats-coming-soon li:before {
    content: "✓";
    color: #9b59b6;
    margin-right: 10px;
}
</style>
