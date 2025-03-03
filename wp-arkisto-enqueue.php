<?php

/**
 * CSS-tyylien ja javascriptin rekisteröinti ja lataus
 **/

function wppoyt_admin_enqueue_scripts(): void
{
    global $pagenow, $typenow;

    /* Rekisteröidään admin-puolen scriptit ja tyylit */

	wp_register_style('jquery-style', plugins_url('assets/jquery-ui-theme-asteriski/jquery-ui.css', __FILE__), [], POKE_VERSION);
	wp_register_script('w3js', plugins_url('assets/w3.js', __FILE__), [], POKE_VERSION,true);
	wp_register_style('wpark-admin-css', plugins_url('css/admin-poytakirjat.css', __FILE__), [], POKE_VERSION);
	wp_register_script('wpark-admin-js', plugins_url('js/admin-poytakirjat.js', __FILE__), array('jquery', 'jquery-ui-datepicker', 'media-upload'), POKE_VERSION,true);

    /* Ladataan pöytäkirjojen admin-puolelle */

    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && $typenow == 'poytakirjat') {
        wp_enqueue_media();
        wp_enqueue_style('wpark-admin-css');
        wp_enqueue_script('wpark-admin-js');
        wp_enqueue_style('jquery-style');
	}

    if (get_current_screen()->taxonomy === "vuosi" || get_current_screen()->taxonomy === "tyyppi") {
        wp_enqueue_style('wpark-admin-css');
    }
}

add_action('admin_enqueue_scripts', 'wppoyt_admin_enqueue_scripts');

function wppoyt_enqueue_shortcode_assets()
{
    global $post;
    if (get_query_var('post_type') == 'poytakirjat' || (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'poytakirja_taulukko'))) {

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
        wp_enqueue_script('wpark-datatables-js', plugins_url('assets/datatables.min.js', __FILE__), array('jquery'), true);

        wp_enqueue_script('wpark-front-js', plugins_url('js/front-poytakirjat.js', __FILE__), array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-dialog',
            'jquery-ui-button',
            'jquery-ui-position',
            'wpark-datatables-js',
        ), true);
        wp_enqueue_style('wpark-front-css', plugins_url('css/front-poytakirjat.css', __FILE__));

    }
}

add_action('wp_enqueue_scripts', 'wppoyt_enqueue_shortcode_assets');