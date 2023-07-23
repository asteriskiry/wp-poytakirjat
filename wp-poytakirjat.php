<?php

/**
 * Plugin Name: WP Pöytäkirjat
 * Description: Pöytäkirja-arkisto
 * Plugin URI: https://asteriski.fi
 * Author: Maks Turtiainen, Asteriski ry
 * Version: 1.5
 * Author URI: https://github.com/asteriskiry
 * License: MIT
 * Collaboration: Roosa Virta, Asteriski ry
 **/

if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat.php');
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php');
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat-uploader.php');

/**
 * Removing Quick edit functionality from the list.
 *
 * @param $actions
 *
 * @return mixed
 */
function wppoyt_remove_quick_edit($actions)
{
    global $typenow;
    if ($typenow == 'poytakirjat') {
        unset($actions['inline hide-if-no-js']);
    }
    
    return $actions;
}

add_filter('post_row_actions', 'wppoyt_remove_quick_edit');

/**
 * Adding a shortcode for the documents.
 *
 * @return false|string
 */
function asteriski_poytakirja_shortcode()
{
    
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/poytakirjat-shortcode.php');
    
    return ob_get_clean();
}

/**
 * Registering the shortcode.
 */
add_shortcode('poytakirja_taulukko', 'asteriski_poytakirja_shortcode');