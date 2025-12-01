<?php
/**
 * Tables module deactivation script
 *
 * @package AStats\TablesCharts\Modules\Tables
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tables are preserved on deactivation
// They will only be deleted on uninstall if the "Delete Data on Uninstall" option is enabled
