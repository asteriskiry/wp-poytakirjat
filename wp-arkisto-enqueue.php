<?php

/**
 * CSS-tyylien ja javascriptin rekisteröinti ja lataus
 **/

function wppoyt_admin_enqueue_scripts() {
    global $pagenow, $typenow;

    /* Rekisteröidään admin-puolen scriptit ja tyylit */

        wp_register_style( 'jquery-style', plugins_url( 'assets/jquery-ui-theme-asteriski/jquery-ui.css', __FILE__ ) );
        wp_register_script( 'w3js', plugins_url( 'assets/w3.js', __FILE__ ),  true );
        wp_register_style( 'wpark-admin-css', plugins_url( 'css/admin-poytakirjat.css', __FILE__ ) );
        wp_register_script( 'wpark-admin-js', plugins_url( 'js/admin-poytakirjat.js', __FILE__ ), array( 'jquery', 'jquery-ui-datepicker', 'media-upload' ), true );
        wp_register_script( 'wpark_pdf_uploader', plugin_dir_url( __FILE__  ) . 'js/admin-poytakirjat-uploader.js', array('jquery', 'media-upload'), '0.0.2', true  );

    /* Ladataan pöytäkirjojen admin-puolelle */

    if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && $typenow == 'poytakirjat' ) {
        wp_enqueue_media(); 
        wp_enqueue_style( 'wpark-admin-css' );
        wp_enqueue_script( 'wpark-admin-js' );
        wp_enqueue_style( 'jquery-style' );
        wp_enqueue_script( 'wpark_pdf_uploader' );
        wp_localize_script( 'wpark_pdf_uploader', 'pdfUploads', array( 'pdfdata' => get_post_meta( get_the_ID(), 'custom_pdf_data', true ) ) );
    }

    if ( get_current_screen() ->taxonomy === "vuosi" || get_current_screen() ->taxonomy === "tyyppi" ) {
        wp_enqueue_style( 'wpark-admin-css' );
    }
}

add_action( 'admin_enqueue_scripts', 'wppoyt_admin_enqueue_scripts' );

function wppoyt_front_enqueue_scripts() {

    /* Rekisteröidään frontin scriptit ja tyylit */

    wp_register_style( 'hover-master-css', plugins_url( 'assets/hover.css', __FILE__ ) );
    wp_register_style( 'animatism-css', plugins_url( 'assets/animatism.css', __FILE__ ) );
    wp_register_style( 'buttons-css', plugins_url( 'assets/buttons.css', __FILE__ ) );
    wp_register_style( 'datatables-css', plugins_url( 'assets/datatables.min.css', __FILE__ ) );
    wp_register_script( 'datatables-js', plugins_url( 'assets/datatables.min.js', __FILE__ ), array( 'jquery' ), true );
    wp_register_script( 'datatables-moment-js', plugins_url( 'assets/moment.min.js', __FILE__ ), true );
    wp_register_script( 'datatables-date-plugin-js', plugins_url( 'assets/datetime-moment.js', __FILE__ ), true );
    wp_register_script( 'font-awesome', plugins_url( 'assets/fontawesome-all.js', __FILE__ ),  true );
    wp_register_style( 'font-awesome-legacy', plugins_url( 'assets/Font-Awesome-legacy/css/font-awesome.min.css', __FILE__ ) );

    wp_register_script( 'wpark-front-js', plugins_url( 'js/front-poytakirjat.js', __FILE__ ),  true );
    wp_register_style( 'wpark-front-css', plugins_url( 'css/front-poytakirjat.css', __FILE__ ) );

    /* Ladataan koko fronttiin */

    wp_enqueue_style( 'hover-master-css' );
    wp_enqueue_style( 'animatism-css' );
    wp_enqueue_style( 'buttons-css' );
    wp_enqueue_style( 'datatables-css' );
    wp_enqueue_script( 'datatables-js' );
    wp_enqueue_script( 'datatables-moment-js' );
    wp_enqueue_script( 'datatables-date-plugin-js' );

    /* Ladataan vain pöytäkirjoille */

    if ( get_query_var( 'post_type' ) == 'poytakirjat' ) {
        wp_enqueue_script( 'wpark-front-js' );
        wp_enqueue_style( 'wpark-front-css' );
    }
    
    if ( get_query_var( 'post_type' ) == 'poytakirjat' || is_singular( 'tentit' ) ) {
        wp_enqueue_style( 'font-awesome-legacy' );

    } else {
        // Tämä myös kaikkialle fronttiin
        wp_enqueue_script( 'font-awesome' );    
    }
}

add_action( 'wp_enqueue_scripts', 'wppoyt_front_enqueue_scripts' );
