<?php
/**
 * Table editor template
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_new = ! $table;
$title = $table ? $table->title : '';
$description = $table ? $table->description : '';
$settings = $table && $table->settings ? json_decode( $table->settings, true ) : array();
$theme = isset( $settings['theme'] ) ? $settings['theme'] : 'default';
$enable_search = isset( $settings['enable_search'] ) ? $settings['enable_search'] : '0';
$enable_sorting = isset( $settings['enable_sorting'] ) ? $settings['enable_sorting'] : '0';
$enable_pagination = isset( $settings['enable_pagination'] ) ? $settings['enable_pagination'] : '0';
$rows_per_page = isset( $settings['rows_per_page'] ) ? $settings['rows_per_page'] : '10';

// Default 3x3 for new tables
if ( $is_new ) {
    $columns = array(
        (object) array( 'column_name' => __( 'Column 1', 'astats-tables-charts' ) ),
        (object) array( 'column_name' => __( 'Column 2', 'astats-tables-charts' ) ),
        (object) array( 'column_name' => __( 'Column 3', 'astats-tables-charts' ) ),
    );
    $rows = array(
        (object) array( 'row_data' => '{"col_0":"","col_1":"","col_2":""}' ),
        (object) array( 'row_data' => '{"col_0":"","col_1":"","col_2":""}' ),
        (object) array( 'row_data' => '{"col_0":"","col_1":"","col_2":""}' ),
    );
}
?>
<div class="wrap astats-wrap astats-tables-editor-wrap">
    <div class="astats-header">
        <h1>
            <?php echo $is_new ? esc_html__( 'Create New Table', 'astats-tables-charts' ) : esc_html__( 'Edit Table', 'astats-tables-charts' ); ?>
        </h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables' ) ); ?>" class="page-title-action">
            <?php esc_html_e( '← Back to Tables', 'astats-tables-charts' ); ?>
        </a>
    </div>

    <form id="astats-table-editor-form" method="post">
        <input type="hidden" name="table_id" value="<?php echo esc_attr( $table ? $table->id : 0 ); ?>">

        <div class="astats-editor-container">
            <div class="astats-editor-main">
                <!-- Title & Description -->
                <div class="astats-editor-section">
                    <div class="astats-field">
                        <label for="table-title"><?php esc_html_e( 'Table Title', 'astats-tables-charts' ); ?> <span class="required">*</span></label>
                        <input type="text" id="table-title" name="title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_attr_e( 'Enter table title', 'astats-tables-charts' ); ?>" required>
                    </div>

                    <div class="astats-field">
                        <label for="table-description"><?php esc_html_e( 'Description', 'astats-tables-charts' ); ?></label>
                        <textarea id="table-description" name="description" rows="3" placeholder="<?php esc_attr_e( 'Optional table description', 'astats-tables-charts' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
                    </div>
                </div>

                <!-- Table Grid -->
                <div class="astats-editor-section">
                    <div class="astats-table-toolbar">
                        <button type="button" class="button astats-add-column">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            <?php esc_html_e( 'Add Column', 'astats-tables-charts' ); ?>
                        </button>
                        <button type="button" class="button astats-add-row">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            <?php esc_html_e( 'Add Row', 'astats-tables-charts' ); ?>
                        </button>
                    </div>

                    <div class="astats-table-grid-wrapper">
                        <table class="astats-table-grid" id="table-grid">
                            <thead>
                                <tr>
                                    <?php foreach ( $columns as $index => $column ) : ?>
                                        <th>
                                            <div class="astats-column-header">
                                                <input type="text" class="astats-column-input" value="<?php echo esc_attr( $column->column_name ); ?>" placeholder="<?php esc_attr_e( 'Column name', 'astats-tables-charts' ); ?>">
                                                <button type="button" class="astats-delete-column" title="<?php esc_attr_e( 'Delete column', 'astats-tables-charts' ); ?>">
                                                    <span class="dashicons dashicons-no-alt"></span>
                                                </button>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="astats-row-actions-header"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $rows as $row_index => $row ) :
                                    $row_data = json_decode( $row->row_data, true ) ?: array();
                                ?>
                                    <tr>
                                        <?php foreach ( $columns as $col_index => $column ) :
                                            $cell_value = isset( $row_data[ 'col_' . $col_index ] ) ? $row_data[ 'col_' . $col_index ] : '';
                                        ?>
                                            <td>
                                                <div class="astats-cell" contenteditable="true" data-col="<?php echo esc_attr( $col_index ); ?>"><?php echo esc_html( $cell_value ); ?></div>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="astats-row-actions">
                                            <button type="button" class="astats-delete-row" title="<?php esc_attr_e( 'Delete row', 'astats-tables-charts' ); ?>">
                                                <span class="dashicons dashicons-no-alt"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="astats-editor-sidebar">
                <!-- Save Button -->
                <div class="astats-editor-section astats-save-section">
                    <button type="submit" class="button button-primary button-large" id="save-table">
                        <?php esc_html_e( 'Save Table', 'astats-tables-charts' ); ?>
                    </button>
                    <span class="astats-save-status"></span>

                    <?php if ( ! $is_new ) : ?>
                        <div class="astats-shortcode-display">
                            <label><?php esc_html_e( 'Shortcode', 'astats-tables-charts' ); ?></label>
                            <code class="astats-shortcode">[astats-table id="<?php echo esc_attr( $table->id ); ?>"]</code>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Theme Settings -->
                <div class="astats-editor-section">
                    <h3><?php esc_html_e( 'Theme', 'astats-tables-charts' ); ?></h3>
                    <div class="astats-theme-selector">
                        <label class="astats-theme-option <?php echo 'default' === $theme ? 'selected' : ''; ?>">
                            <input type="radio" name="settings[theme]" value="default" <?php checked( $theme, 'default' ); ?>>
                            <span class="astats-theme-preview astats-theme-default"></span>
                            <span class="astats-theme-name"><?php esc_html_e( 'Default', 'astats-tables-charts' ); ?></span>
                        </label>
                        <label class="astats-theme-option <?php echo 'minimal' === $theme ? 'selected' : ''; ?>">
                            <input type="radio" name="settings[theme]" value="minimal" <?php checked( $theme, 'minimal' ); ?>>
                            <span class="astats-theme-preview astats-theme-minimal"></span>
                            <span class="astats-theme-name"><?php esc_html_e( 'Minimal', 'astats-tables-charts' ); ?></span>
                        </label>
                        <label class="astats-theme-option <?php echo 'dark' === $theme ? 'selected' : ''; ?>">
                            <input type="radio" name="settings[theme]" value="dark" <?php checked( $theme, 'dark' ); ?>>
                            <span class="astats-theme-preview astats-theme-dark"></span>
                            <span class="astats-theme-name"><?php esc_html_e( 'Dark', 'astats-tables-charts' ); ?></span>
                        </label>
                        <label class="astats-theme-option <?php echo 'striped' === $theme ? 'selected' : ''; ?>">
                            <input type="radio" name="settings[theme]" value="striped" <?php checked( $theme, 'striped' ); ?>>
                            <span class="astats-theme-preview astats-theme-striped"></span>
                            <span class="astats-theme-name"><?php esc_html_e( 'Striped', 'astats-tables-charts' ); ?></span>
                        </label>
                    </div>
                </div>

                <!-- Features -->
                <div class="astats-editor-section">
                    <h3><?php esc_html_e( 'Features', 'astats-tables-charts' ); ?></h3>

                    <label class="astats-toggle-option">
                        <input type="checkbox" name="settings[enable_search]" value="1" <?php checked( $enable_search, '1' ); ?>>
                        <span><?php esc_html_e( 'Enable Search', 'astats-tables-charts' ); ?></span>
                    </label>

                    <label class="astats-toggle-option">
                        <input type="checkbox" name="settings[enable_sorting]" value="1" <?php checked( $enable_sorting, '1' ); ?>>
                        <span><?php esc_html_e( 'Enable Sorting', 'astats-tables-charts' ); ?></span>
                    </label>

                    <label class="astats-toggle-option">
                        <input type="checkbox" name="settings[enable_pagination]" value="1" <?php checked( $enable_pagination, '1' ); ?>>
                        <span><?php esc_html_e( 'Enable Pagination', 'astats-tables-charts' ); ?></span>
                    </label>

                    <div class="astats-field astats-pagination-rows" style="<?php echo '1' !== $enable_pagination ? 'display:none;' : ''; ?>">
                        <label for="rows-per-page"><?php esc_html_e( 'Rows per page', 'astats-tables-charts' ); ?></label>
                        <select id="rows-per-page" name="settings[rows_per_page]">
                            <option value="5" <?php selected( $rows_per_page, '5' ); ?>>5</option>
                            <option value="10" <?php selected( $rows_per_page, '10' ); ?>>10</option>
                            <option value="25" <?php selected( $rows_per_page, '25' ); ?>>25</option>
                            <option value="50" <?php selected( $rows_per_page, '50' ); ?>>50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
