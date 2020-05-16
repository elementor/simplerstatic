<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin Name:       Simpler Static
 * Plugin URI:        http://simplerstatic.com
 * Description:       Simple WordPress static site generator
 * Version:           0.2
 * Author:            Leon Stafford
 * Author URI:        https://ljs.dev
 * License:           The Unlicense
 * License URI:       https://unlicense.org
 * Text Domain:       simplerstatic
 */

/**
 * Check that we're using at least version 7.3 of PHP
 */
if ( version_compare( PHP_VERSION, '7.3', '<' ) ) {
    if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        if ( ! is_plugin_active( plugin_basename( __FILE__ ) ) ) {
            echo( "<p>'<b>Simpler Static</b> requires PHP 7.3 or higher</p>" );
            exit();
        }

        deactivate_plugins( __FILE__ );
    }
} else {
    define( 'SIMPLERSTATIC_PATH', plugin_dir_path( __FILE__ ) );

    if ( file_exists( SIMPLERSTATIC_PATH . 'vendor/autoload.php' ) ) {
        require_once SIMPLERSTATIC_PATH . 'vendor/autoload.php';
    }
    // Loading up Simpler Static in a separate file so that there's nothing to
    // trigger a PHP error in this file (e.g. by using namespacing)
    SimplerStatic\Plugin::instance();
}
