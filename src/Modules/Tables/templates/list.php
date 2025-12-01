<?php
/**
 * Tables list template
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap astats-wrap astats-tables-wrap">
    <div class="astats-header">
        <h1><?php esc_html_e( 'Tables', 'astats-tables-charts' ); ?></h1>
        <div class="astats-header-actions">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=new' ) ); ?>" class="page-title-action">
                <?php esc_html_e( 'Add New Table', 'astats-tables-charts' ); ?>
            </a>
            <button type="button" class="page-title-action astats-import-btn">
                <?php esc_html_e( 'Import', 'astats-tables-charts' ); ?>
            </button>
        </div>
    </div>

    <div class="astats-tables-list">
        <?php if ( empty( $tables ) ) : ?>
            <div class="astats-empty-state">
                <span class="dashicons dashicons-grid-view"></span>
                <h3><?php esc_html_e( 'No tables yet', 'astats-tables-charts' ); ?></h3>
                <p><?php esc_html_e( 'Create your first table to get started, or import from a CSV file.', 'astats-tables-charts' ); ?></p>
                <div class="astats-empty-actions">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=new' ) ); ?>" class="button button-primary button-hero">
                        <?php esc_html_e( 'Create Table', 'astats-tables-charts' ); ?>
                    </a>
                    <button type="button" class="button button-secondary button-hero astats-import-btn">
                        <?php esc_html_e( 'Import File', 'astats-tables-charts' ); ?>
                    </button>
                </div>
            </div>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped astats-tables-table">
                <thead>
                    <tr>
                        <th class="column-title"><?php esc_html_e( 'Title', 'astats-tables-charts' ); ?></th>
                        <th class="column-shortcode"><?php esc_html_e( 'Shortcode', 'astats-tables-charts' ); ?></th>
                        <th class="column-columns"><?php esc_html_e( 'Columns', 'astats-tables-charts' ); ?></th>
                        <th class="column-rows"><?php esc_html_e( 'Rows', 'astats-tables-charts' ); ?></th>
                        <th class="column-date"><?php esc_html_e( 'Created', 'astats-tables-charts' ); ?></th>
                        <th class="column-actions"><?php esc_html_e( 'Actions', 'astats-tables-charts' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $tables as $table ) : ?>
                        <tr data-table-id="<?php echo esc_attr( $table->id ); ?>">
                            <td class="column-title">
                                <strong>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=edit&id=' . $table->id ) ); ?>">
                                        <?php echo esc_html( $table->title ); ?>
                                    </a>
                                </strong>
                                <?php if ( ! empty( $table->description ) ) : ?>
                                    <p class="description"><?php echo esc_html( wp_trim_words( $table->description, 10 ) ); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="column-shortcode">
                                <code class="astats-shortcode" title="<?php esc_attr_e( 'Click to copy', 'astats-tables-charts' ); ?>">[astats-table id="<?php echo esc_attr( $table->id ); ?>"]</code>
                            </td>
                            <td class="column-columns">
                                <?php echo esc_html( $table->column_count ); ?>
                            </td>
                            <td class="column-rows">
                                <?php echo esc_html( $table->row_count ); ?>
                            </td>
                            <td class="column-date">
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $table->created_at ) ) ); ?>
                            </td>
                            <td class="column-actions">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=edit&id=' . $table->id ) ); ?>" class="button button-small">
                                    <?php esc_html_e( 'Edit', 'astats-tables-charts' ); ?>
                                </a>
                                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=astats-tables&action=export&id=' . $table->id ), 'astats_export_' . $table->id ) ); ?>" class="button button-small">
                                    <?php esc_html_e( 'Export', 'astats-tables-charts' ); ?>
                                </a>
                                <button type="button" class="button button-small button-link-delete astats-delete-table" data-table-id="<?php echo esc_attr( $table->id ); ?>">
                                    <?php esc_html_e( 'Delete', 'astats-tables-charts' ); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Import Modal -->
<div id="astats-import-modal" class="astats-modal" style="display:none;">
    <div class="astats-modal-overlay"></div>
    <div class="astats-modal-content">
        <div class="astats-modal-header">
            <h2><?php esc_html_e( 'Import Table', 'astats-tables-charts' ); ?></h2>
            <button type="button" class="astats-modal-close">&times;</button>
        </div>
        <div class="astats-modal-body">
            <form id="astats-import-form" enctype="multipart/form-data">
                <div class="astats-field">
                    <label for="import-title"><?php esc_html_e( 'Table Title', 'astats-tables-charts' ); ?> <span class="required">*</span></label>
                    <input type="text" id="import-title" name="title" required placeholder="<?php esc_attr_e( 'Enter table title', 'astats-tables-charts' ); ?>">
                </div>

                <div class="astats-field">
                    <label for="import-file"><?php esc_html_e( 'File', 'astats-tables-charts' ); ?> <span class="required">*</span></label>
                    <div class="astats-file-upload">
                        <input type="file" id="import-file" name="import_file" accept=".csv,.xlsx,.xls" required>
                        <div class="astats-file-upload-info">
                            <span class="dashicons dashicons-upload"></span>
                            <span class="astats-file-name"><?php esc_html_e( 'Choose a CSV or Excel file, or drag it here', 'astats-tables-charts' ); ?></span>
                        </div>
                    </div>
                    <p class="description"><?php esc_html_e( 'Supported formats: CSV, Excel (.xlsx, .xls). The first row will be used as column headers.', 'astats-tables-charts' ); ?></p>
                </div>

                <div class="astats-field">
                    <label>
                        <input type="checkbox" name="has_header" value="1" checked>
                        <?php esc_html_e( 'First row contains column headers', 'astats-tables-charts' ); ?>
                    </label>
                </div>

                <div class="astats-import-preview" style="display:none;">
                    <h4><?php esc_html_e( 'Preview', 'astats-tables-charts' ); ?></h4>
                    <div class="astats-preview-table-wrapper">
                        <table class="astats-preview-table"></table>
                    </div>
                    <p class="astats-preview-info"></p>
                </div>
            </form>
        </div>
        <div class="astats-modal-footer">
            <button type="button" class="button astats-modal-cancel"><?php esc_html_e( 'Cancel', 'astats-tables-charts' ); ?></button>
            <button type="button" class="button button-primary astats-import-submit" disabled><?php esc_html_e( 'Import Table', 'astats-tables-charts' ); ?></button>
        </div>
    </div>
</div>
