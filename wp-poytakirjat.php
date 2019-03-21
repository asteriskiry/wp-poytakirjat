<?php

/**
 * Plugin Name: WP Pöytäkirjat
 * Description: Pöytäkirja-arkisto
 * Plugin URI: https://asteriski.fi
 * Author: Maks Turtiainen, Asteriski ry
 * Version: 1.3
 * Author URI: https://github.com/asteriskiry
 * License: MIT
 **/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat.php' );
require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php' );
require_once (plugin_dir_path(__FILE__) . 'wp-arkisto-poytakirjat-uploader.php' );

/* Dashboard-widgetti */

/*
function wpark_dashboard () {
    add_meta_box( 'wpark_dashboard_welcome', 'Hei', 'wpark_add_dashboard_widget', 'dashboard', 'normal', 'high' );
}
function wpark_add_dashboard_widget () {
?>
    <div class="wpark-dashboard">
        <h1>Tervetuloa</h1>
        <h3>Haluatko:</h3>
        <ul>
<?php   
    echo '<li><a href="' . admin_url( 'edit.php?post_type=poytakirjat' ) . '">Lisätä pöytäkirjan</a></li>';
    echo '<li><a href="' . admin_url( 'edit.php?post_type=tentit' ) . '">Lisätä tentin tenttiarkistoon</a></li>'; 
    echo '<h3>Vahvistusta odottavat tentit:</h3>';
    echo '</ul>';
    echo '</div>';
}

add_action( 'wp_dashboard_setup', 'wpark_dashboard' );
 */

/* Luodaan sivut fronttiin */

function wppoyt_add_pages () {
    $pk_query = new WP_Query('pagename=poytakirjat');	
    if(empty($pk_query->posts) && empty($pk_query->queried_object) && get_option('poytakirjat-created') == false) {
        $poytakirjat_page = array(
            'post_title' => 'Pöytäkirjat',
            'post_name' => 'poytakirjat',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'page',
            'comment_status' => 'closed'
        );
        $poytakirjat_post_id = wp_insert_post( $poytakirjat_page );
        update_option('poytakirjat-created', true);
    }
}

add_action( 'admin_init', 'wppoyt_add_pages'  );

/* Poistetaan listauksesta quick edit */

function wppoyt_remove_quick_edit( $actions  ) {
    global $typenow;
    if ($typenow == 'poytakirjat') {
        unset($actions['inline hide-if-no-js']);
        return $actions;
    } else {
        return $actions;
    }
}

add_filter('post_row_actions','wppoyt_remove_quick_edit',10,1);
