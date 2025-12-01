<?php
/**
 * Frontend table template
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enable_search = isset( $settings['enable_search'] ) && '1' === $settings['enable_search'];
$enable_sorting = isset( $settings['enable_sorting'] ) && '1' === $settings['enable_sorting'];
$enable_pagination = isset( $settings['enable_pagination'] ) && '1' === $settings['enable_pagination'];
$rows_per_page = isset( $settings['rows_per_page'] ) ? intval( $settings['rows_per_page'] ) : 10;
?>
<div class="astats-table-wrapper astats-theme-<?php echo esc_attr( $theme ); ?>"
     data-table-id="<?php echo esc_attr( $table->id ); ?>"
     data-search="<?php echo $enable_search ? 'true' : 'false'; ?>"
     data-sorting="<?php echo $enable_sorting ? 'true' : 'false'; ?>"
     data-pagination="<?php echo $enable_pagination ? 'true' : 'false'; ?>"
     data-per-page="<?php echo esc_attr( $rows_per_page ); ?>">

    <?php if ( ! empty( $table->title ) ) : ?>
        <h3 class="astats-table-title"><?php echo esc_html( $table->title ); ?></h3>
    <?php endif; ?>

    <?php if ( ! empty( $table->description ) ) : ?>
        <p class="astats-table-description"><?php echo esc_html( $table->description ); ?></p>
    <?php endif; ?>

    <?php if ( $enable_search ) : ?>
        <div class="astats-table-search">
            <input type="text" class="astats-search-input" placeholder="<?php esc_attr_e( 'Search...', 'astats-tables-charts' ); ?>">
        </div>
    <?php endif; ?>

    <div class="astats-table-container">
        <table class="astats-table">
            <thead>
                <tr>
                    <?php foreach ( $columns as $index => $column ) : ?>
                        <th data-col="<?php echo esc_attr( $index ); ?>" <?php echo $enable_sorting ? 'class="astats-sortable"' : ''; ?>>
                            <?php echo esc_html( $column->column_name ); ?>
                            <?php if ( $enable_sorting ) : ?>
                                <span class="astats-sort-icon"></span>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
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
                            <td><?php echo esc_html( $cell_value ); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ( $enable_pagination ) : ?>
        <div class="astats-table-pagination">
            <button type="button" class="astats-page-prev" disabled>&laquo; <?php esc_html_e( 'Prev', 'astats-tables-charts' ); ?></button>
            <span class="astats-page-info"></span>
            <button type="button" class="astats-page-next"><?php esc_html_e( 'Next', 'astats-tables-charts' ); ?> &raquo;</button>
        </div>
    <?php endif; ?>
</div>
