<?php
/**
 * Uninstall Simpler Static
 */

// exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete Simpler Static's settings
delete_option( 'simplerstatic' );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-ss-plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/models/class-ss-model.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/models/class-ss-page.php';

// Drop the Pages table
SimplerStatic\Page::drop_table();
