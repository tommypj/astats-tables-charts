<?php
/**
 * Charts module deactivation script
 *
 * @package AStats\TablesCharts\Modules\Charts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Charts are preserved on deactivation
// They will only be deleted on uninstall if the "Delete Data on Uninstall" option is enabled
