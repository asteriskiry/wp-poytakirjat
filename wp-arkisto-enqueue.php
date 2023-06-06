<?php

/**
 * CSS-tyylien ja javascriptin rekisteröinti ja lataus
 **/

function wppoyt_admin_enqueue_scripts(): void
{
    global $pagenow, $typenow;

    /* Rekisteröidään admin-puolen scriptit ja tyylit */

    wp_register_style('jquery-style', plugins_url('assets/jquery-ui-theme-asteriski/jquery-ui.css', __FILE__));
    wp_register_script('w3js', plugins_url('assets/w3.js', __FILE__), true);
    wp_register_style('wpark-admin-css', plugins_url('css/admin-poytakirjat.css', __FILE__));
    wp_register_script('wpark-admin-js', plugins_url('js/admin-poytakirjat.js', __FILE__), array( 'jquery', 'jquery-ui-datepicker', 'media-upload' ), true);
    wp_register_script('wpark_pdf_uploader', plugin_dir_url(__FILE__) . 'js/admin-poytakirjat-uploader.js', array('jquery', 'media-upload'), '0.0.2', true);

    /* Ladataan pöytäkirjojen admin-puolelle */

    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && $typenow == 'poytakirjat') {
        wp_enqueue_media();
        wp_enqueue_style('wpark-admin-css');
        wp_enqueue_script('wpark-admin-js');
        wp_enqueue_style('jquery-style');
        wp_enqueue_script('wpark_pdf_uploader');
        wp_localize_script('wpark_pdf_uploader', 'pdfUploads', array( 'pdfdata' => get_post_meta(get_the_ID(), 'custom_pdf_data', true) ));
    }

    if (get_current_screen() ->taxonomy === "vuosi" || get_current_screen() ->taxonomy === "tyyppi") {
        wp_enqueue_style('wpark-admin-css');
    }
}

add_action('admin_enqueue_scripts', 'wppoyt_admin_enqueue_scripts');