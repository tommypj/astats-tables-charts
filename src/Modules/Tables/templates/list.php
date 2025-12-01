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
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=new' ) ); ?>" class="page-title-action">
            <?php esc_html_e( 'Add New Table', 'astats-tables-charts' ); ?>
        </a>
    </div>

    <div class="astats-tables-list">
        <?php if ( empty( $tables ) ) : ?>
            <div class="astats-empty-state">
                <span class="dashicons dashicons-grid-view"></span>
                <h3><?php esc_html_e( 'No tables yet', 'astats-tables-charts' ); ?></h3>
                <p><?php esc_html_e( 'Create your first table to get started.', 'astats-tables-charts' ); ?></p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=astats-tables&action=new' ) ); ?>" class="button button-primary button-hero">
                    <?php esc_html_e( 'Create Table', 'astats-tables-charts' ); ?>
                </a>
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
