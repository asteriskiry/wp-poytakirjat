<?php

/**
 * Plugin Name: WP Pöytäkirjat
 * Description: Pöytäkirja-arkisto
 * Plugin URI: https://asteriski.fi
 * Author: Maks Turtiainen, Asteriski ry
 * Version: 1.5
 * Author URI: https://github.com/asteriskiry
 * License: MIT
 **/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat.php' );
require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php' );
require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat-uploader.php' );

/* Poistetaan listauksesta quick edit */

function wppoyt_remove_quick_edit( $actions  ) {
    global $typenow;
    if ($typenow == 'poytakirjat') {
        unset($actions['inline hide-if-no-js']);
    }
    
    return $actions;
}

add_filter('post_row_actions','wppoyt_remove_quick_edit');

function wppoyt_enqueue_shortcode_assets(  ) {
   
    switch (get_option('wp_poytakirjat_settings_theme', 'default')) {
        
        case 'technica':
            wp_enqueue_style('wpark-technica', plugins_url('css/technica.css', __FILE__));
            break;
        default:
        wp_enqueue_style('wpark-asteriski', plugins_url('css/asteriski.css', __FILE__));
        break;
    }
    wp_deregister_style('jquery-ui-base-dialog');
    wp_enqueue_style('jquery-ui-base-dialog', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.1/themes/base/jquery-ui.min.css');

    wp_enqueue_style('wpark-datatables-css', plugins_url('assets/datatables.min.css', __FILE__));
    wp_enqueue_script('wpark-datatables-js', plugins_url('assets/datatables.min.js', __FILE__), array( 'jquery' ), true);
    
    wp_enqueue_script('wpark-front-js', plugins_url('js/front-poytakirjat.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-button', 'jquery-ui-position', 'wpark-datatables-js'), true);
    wp_enqueue_style('wpark-front-css', plugins_url('css/front-poytakirjat.css', __FILE__));
    
}
add_action('wp_enqueue_scripts', 'wppoyt_enqueue_shortcode_assets');

// The shortcode function
function asteriski_poytakirja_shortcode() {
    
    
    ob_start();
    include (plugin_dir_path(__FILE__) . 'templates/poytakirjat-shortcode.php');
    return ob_get_clean();
}
// Register shortcode
add_shortcode('poytakirja_taulukko', 'asteriski_poytakirja_shortcode');