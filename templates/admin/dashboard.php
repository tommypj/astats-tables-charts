<?php
/**
 * Dashboard template
 *
 * @package AStats\TablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap astats-wrap">
    <div class="astats-header">
        <h1><?php esc_html_e( 'AStats Tables & Charts', 'astats-tables-charts' ); ?></h1>
        <p class="astats-version"><?php echo esc_html( sprintf( __( 'Version %s', 'astats-tables-charts' ), ASTATS_VERSION ) ); ?></p>
    </div>

    <div class="astats-dashboard">
        <div class="astats-welcome-card">
            <h2><?php esc_html_e( 'Welcome to AStats', 'astats-tables-charts' ); ?></h2>
            <p><?php esc_html_e( 'Create beautiful, responsive tables and charts for your WordPress site. Get started by activating the modules you need.', 'astats-tables-charts' ); ?></p>
        </div>

        <div class="astats-module-cards">
            <?php if ( empty( $modules ) ) : ?>
                <div class="astats-no-modules">
                    <p><?php esc_html_e( 'No modules found. Please check your installation.', 'astats-tables-charts' ); ?></p>
                </div>
            <?php else : ?>
                <?php foreach ( $modules as $slug => $module ) : ?>
                    <div class="astats-module-card <?php echo $module['active'] ? 'is-active' : ''; ?>" data-module="<?php echo esc_attr( $slug ); ?>">
                        <div class="astats-module-icon" style="background-color: <?php echo esc_attr( $module['color'] ?? '#3498db' ); ?>">
                            <span class="dashicons <?php echo esc_attr( $module['icon'] ?? 'dashicons-admin-generic' ); ?>"></span>
                        </div>
                        <div class="astats-module-info">
                            <h3><?php echo esc_html( $module['name'] ); ?></h3>
                            <p><?php echo esc_html( $module['description'] ?? '' ); ?></p>
                            <div class="astats-module-meta">
                                <span class="astats-module-version"><?php echo esc_html( sprintf( __( 'v%s', 'astats-tables-charts' ), $module['version'] ?? '1.0.0' ) ); ?></span>
                                <span class="astats-module-status <?php echo $module['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $module['active'] ? esc_html__( 'Active', 'astats-tables-charts' ) : esc_html__( 'Inactive', 'astats-tables-charts' ); ?>
                                </span>
                            </div>
                        </div>
                        <div class="astats-module-actions">
                            <?php if ( $module['active'] ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-' . $slug ) ); ?>" class="button button-primary">
                                    <?php esc_html_e( 'Manage', 'astats-tables-charts' ); ?>
                                </a>
                            <?php else : ?>
                                <button type="button" class="button astats-activate-module" data-module="<?php echo esc_attr( $slug ); ?>">
                                    <?php esc_html_e( 'Activate', 'astats-tables-charts' ); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="astats-quick-links">
            <h3><?php esc_html_e( 'Quick Links', 'astats-tables-charts' ); ?></h3>
            <ul>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-modules' ) ); ?>"><?php esc_html_e( 'Manage Modules', 'astats-tables-charts' ); ?></a></li>
                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-settings' ) ); ?>"><?php esc_html_e( 'Plugin Settings', 'astats-tables-charts' ); ?></a></li>
                <li><a href="https://github.com/tommypj/astats-tables-charts" target="_blank"><?php esc_html_e( 'Documentation', 'astats-tables-charts' ); ?></a></li>
            </ul>
        </div>
    </div>
</div>
