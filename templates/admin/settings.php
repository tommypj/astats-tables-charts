<?php
/**
 * Settings template
 *
 * @package AStats\TablesCharts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$license_key = isset( $settings['license_key'] ) ? $settings['license_key'] : '';
$delete_data = isset( $settings['delete_data'] ) ? $settings['delete_data'] : '';
?>
<div class="wrap astats-wrap">
    <div class="astats-header">
        <h1><?php esc_html_e( 'Settings', 'astats-tables-charts' ); ?></h1>
        <p><?php esc_html_e( 'Configure global settings for AStats Tables & Charts.', 'astats-tables-charts' ); ?></p>
    </div>

    <div class="astats-settings-page">
        <form id="astats-settings-form" method="post">
            <div class="astats-settings-section">
                <h2><?php esc_html_e( 'License', 'astats-tables-charts' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="astats_license_key"><?php esc_html_e( 'License Key', 'astats-tables-charts' ); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="astats_license_key"
                                   name="settings[license_key]"
                                   value="<?php echo esc_attr( $license_key ); ?>"
                                   class="regular-text"
                                   placeholder="<?php esc_attr_e( 'Enter your license key', 'astats-tables-charts' ); ?>">
                            <p class="description">
                                <?php esc_html_e( 'Enter your license key to unlock Pro features.', 'astats-tables-charts' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="astats-settings-section">
                <h2><?php esc_html_e( 'Data Management', 'astats-tables-charts' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Delete Data on Uninstall', 'astats-tables-charts' ); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox"
                                       name="settings[delete_data]"
                                       value="1"
                                       <?php checked( $delete_data, '1' ); ?>>
                                <?php esc_html_e( 'Remove all plugin data when uninstalling', 'astats-tables-charts' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( 'Warning: This will permanently delete all tables, charts, and settings.', 'astats-tables-charts' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            <p class="submit">
                <button type="submit" class="button button-primary" id="astats-save-settings">
                    <?php esc_html_e( 'Save Settings', 'astats-tables-charts' ); ?>
                </button>
                <span class="astats-save-status"></span>
            </p>
        </form>
    </div>
</div>
