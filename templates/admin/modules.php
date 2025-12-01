<?php
/**
 * Modules template
 *
 * @package AStats\TablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap astats-wrap">
    <div class="astats-header">
        <h1><?php esc_html_e( 'Modules', 'astats-tables-charts' ); ?></h1>
        <p><?php esc_html_e( 'Activate or deactivate modules to customize your AStats experience.', 'astats-tables-charts' ); ?></p>
    </div>

    <div class="astats-modules-page">
        <table class="wp-list-table widefat fixed striped astats-modules-table">
            <thead>
                <tr>
                    <th class="column-status"><?php esc_html_e( 'Status', 'astats-tables-charts' ); ?></th>
                    <th class="column-module"><?php esc_html_e( 'Module', 'astats-tables-charts' ); ?></th>
                    <th class="column-description"><?php esc_html_e( 'Description', 'astats-tables-charts' ); ?></th>
                    <th class="column-version"><?php esc_html_e( 'Version', 'astats-tables-charts' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $modules ) ) : ?>
                    <tr>
                        <td colspan="4"><?php esc_html_e( 'No modules found.', 'astats-tables-charts' ); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $modules as $slug => $module ) : ?>
                        <tr class="astats-module-row <?php echo $module['active'] ? 'is-active' : ''; ?>" data-module="<?php echo esc_attr( $slug ); ?>">
                            <td class="column-status">
                                <label class="astats-toggle-switch">
                                    <input type="checkbox"
                                           class="astats-module-toggle"
                                           data-module="<?php echo esc_attr( $slug ); ?>"
                                           <?php checked( $module['active'] ); ?>>
                                    <span class="astats-toggle-slider"></span>
                                </label>
                            </td>
                            <td class="column-module">
                                <div class="astats-module-name">
                                    <span class="dashicons <?php echo esc_attr( $module['icon'] ?? 'dashicons-admin-generic' ); ?>" style="color: <?php echo esc_attr( $module['color'] ?? '#3498db' ); ?>"></span>
                                    <strong><?php echo esc_html( $module['name'] ); ?></strong>
                                </div>
                            </td>
                            <td class="column-description">
                                <?php echo esc_html( $module['description'] ?? '' ); ?>
                            </td>
                            <td class="column-version">
                                <?php echo esc_html( $module['version'] ?? '1.0.0' ); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
