<?php

/**
 * Pöytäkirjat
 **/

/* Custom post type "Pöytäkirjat" rekisteröinti */

function wpark_pk_register_post_type() {

    $singular = 'Pöytäkirja';
    $plural = 'Pöytäkirjat';
    $slug = 'poytakirjat';

    $labels = array(
        'name'                  => $plural,
        'singular_name'         => $singular,
        'add_name'              => 'Lisää uusi',
        'add_new_item'          => 'Lisää uusi ' . $singular,
        'edit'                  => 'Muokkaa',
        'edit_item'             => 'Muokkaa pöytäkirjaa',
        'new_item'              => 'Uusi ' . $singular,
        'view'                  => 'Näytä ' . $singular,
        'view_item'             => 'Näytä ' . $singular,
        'search_term'           => 'Etsi pöytäkirjaa',
        'parent'                => 'Vanhempi ' . $singular,
        'not_found'             => 'Pöytäkirjoja ei löytynyt',
        'not_found_in_trash'    => 'Pöytäkirjoja ei löytynyt roskakorista'
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'exclude_from_search'   => false,
        'show_in_nav_menus'     => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 10,
        'menu_icon'             => 'dashicons-media-text',
        'can_export'            => true,
        'delete_with_user'      => false,
        'hierarchical'          => false,
        'has_archive'           => true,
        'query_var'             => true,
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
        // 'capabilities'       => array(),
        'taxonomies'            => array( 'vuosi', 'tyyppi', ),
        'rewrite'               => array( 
            'slug'                  => $slug,
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
        ),
        'supports'              => array(
            'title',
            //'comments',
            // 'editor',
            // 'custom-fields',
        )
    );

    register_post_type( $slug, $args );
}
add_action( 'init', 'wpark_pk_register_post_type' );


/* Custom taxonomyn "Vuodet" rekisteröinti pöyräkirjoille */

function wpark_pk_register_taxonomy_vuosi() {

    $plural = 'Vuodet';
    $singular = 'Vuosi';
    $slug = 'vuosi';

    $labels = array(
        'name'                       => $singular,
        'singular_name'              => $singular,
        'search_items'               => 'Etsi vuotta',
        'popular_items'              => 'Suositut vuodet',
        'all_items'                  => 'Kaikki vuodet',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Muokkaa vuotta',
        'update_item'                => 'Päivitä ' . $singular,
        'add_new_item'               => 'Lisää uusi ' . $singular,
        'new_item_name'              => 'Nimeä ' . $singular,
        'separate_items_with_commas' => 'Erottele ' . $plural . ' pilkuilla',
        'add_or_remove_items'        => 'Lisää tai poista vuosia',
        'choose_from_most_used'      => 'Valitse suosituimmista vuosista',
        'not_found'                  => 'Vuosia ei löytynyt',
        'menu_name'                  => $plural,
    );

    $args = array(
        'public'                => false,
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'vuosi' ),
        'meta_box_cb'           => 'wpark_pk_taxonomy_meta_box',
    ); 
    register_taxonomy( 'vuosi', 'poytakirjat', $args );
}
add_action('init', 'wpark_pk_register_taxonomy_vuosi');

/* Custom taxonomyn "Tyyppi" rekisteröinti pöyräkirjoille */

function wpark_pk_register_taxonomy_tyyppi() {

    $plural = 'Tyypit';
    $singular = 'Tyyppi';
    $slug = 'tyyppi';

    $labels = array(
        'name'                       => $singular,
        'singular_name'              => $singular,
        'search_items'               => 'Etsi tyyppiä',
        'popular_items'              => 'Suositut tyypit',
        'all_items'                  => 'Kaikki tyypit',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Muokkaa tyyppiä',
        'update_item'                => 'Päivitä ' . $singular,
        'add_new_item'               => 'Lisää uusi ' . $singular,
        'new_item_name'              => 'Nimeä ' . $singular,
        'separate_items_with_commas' => 'Erottele ' . $plural . ' pilkuilla',
        'add_or_remove_items'        => 'Lisää tai poista tyyppejä',
        'choose_from_most_used'      => 'Valitse suosituimmista tyypeistä',
        'not_found'                  => 'Tyyppejä ei löytynyt',
        'menu_name'                  => $plural,
    );

    $args = array(
        'public'                => false,
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => $slug ),
        'meta_box_cb'           => 'wpark_pk_taxonomy_meta_box',
    ); 
    register_taxonomy( 'tyyppi', 'poytakirjat', $args );
}
add_action('init', 'wpark_pk_register_taxonomy_tyyppi');

/* Lisäyssivun taxonomioiden meta boxit */

function wpark_pk_taxonomy_meta_box($post, $meta_box_properties) {
    $taxonomy = $meta_box_properties['args']['taxonomy'];
    $tax = get_taxonomy($taxonomy);
    $terms = get_terms($taxonomy, array('hide_empty' => 0));
    $name = 'tax_input[' . $taxonomy . ']';
    $postterms = get_the_terms( $post->ID, $taxonomy );
    $current = ($postterms ? array_pop($postterms) : false);
    $current = ($current ? $current->term_id : 0);
?>

<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
    <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
    <li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
    </ul>

    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
        <input name="tax_input[<?php echo $taxonomy; ?>][]" value="0" type="hidden">            
        <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:symbol" class="categorychecklist form-no-clear">

<?php
    foreach($terms as $term){
    $id = $taxonomy.'-'.$term->term_id; ?>

        <li id="<?php echo $id?>">
        <label class="selectit"><input required value="<?php echo $term->term_id; ?>" name="tax_input[<?php echo $taxonomy; ?>][]" id="in-<?php echo $id; ?>"<?php if( $current === (int)$term->term_id ){?>checked="checked"<?php } ?> type="radio"> <?php echo $term->name; ?></label>
        </li>
<?php   } ?>
        </ul>
    </div>
</div>
<?php
}

/* Pöytäkirjojen lisäyssivun meta boxit (Järjestysnum, pvm, helppi) */

function wpark_pk_add_metabox() {

    add_meta_box(
        'wpark_pk_meta',
        'Pöytäkirjan tiedot',
        'wpark_pk_callback',
        'poytakirjat',
        'normal',
        'high'
    );

    add_meta_box(
        'wpark_pk_help',
        'Tiedote',
        'wpark_pk_help_callback',
        'poytakirjat',
        'normal',
        'high'
    );
} 

add_action('add_meta_boxes', 'wpark_pk_add_metabox');


/* Lisäyssivun html:n generointi */

function wpark_pk_callback( $post ) {
    wp_nonce_field( basename( __FILE__  ), 'wpark_pk_nonce' );
    $wpark_pk_stored_meta = get_post_meta( $post->ID );   
?>

<div class="meta-row">
    <div class="meta-th">
        <label for="pk-numero" class="pk-row-title">Järjestysnumero</label>
    </div>
    <div class="meta-td">
        <input type="number" class="pk-row-content" required max=99 min=1  name="pk_numero" id="pk-numero" value="<?php if ( ! empty ( $wpark_pk_stored_meta['pk_numero'] ) ) echo esc_attr( $wpark_pk_stored_meta['pk_numero'][0]  ); ?>"/>
    </div>
</div>

<div class="meta-row">
    <div class="meta-th">
        <label for="pk-paivamaara" class="pk-row-title">Kokouksen päivämäärä</label>
    </div>
    <div class="meta-td">
        <input type="text" pattern="[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}" class="pk-row-content datepicker" required size=8  name="pk_paivamaara" id="pk-paivamaara" value="<?php if ( ! empty ( $wpark_pk_stored_meta['pk_paivamaara'] ) ) echo esc_attr( $wpark_pk_stored_meta['pk_paivamaara'][0]  ); ?>"/>
    </div>
</div>

<?php 
    /***********************************************************
     * Editori HTML-pöytäkirjoja varten, kommentoitu pois      *
     ***********************************************************
?>

<div class="meta">
<div class="meta-th">
<span>Lisää pöytäkirja "Lisää media" -näppäimestä.</span>
</div>
</div>

<div class="meta-editor"></div>
<?php
     $content = get_post_meta( $post->ID, 'poytakirja', true  );
    $editor = 'poytakirja';
    $settings = array(
        'textarea_rows' => 8,
        'media_buttons' => true,
    );
    wp_editor( $content, $editor, $settings );
?>
    </div>

<?php

    **********************************************************/

}

function wpark_pk_help_callback( $post ) {
    echo '<div class="meta-help">Jos et ole ihan varma mitä teet, katso <a href="' . admin_url( 'edit.php?post_type=poytakirjat&page=pk-ohjeet' ) . '">ohjeet</a></div>';
}

/* Metatietojen tallennus */

function wpark_pk_meta_save( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id  );
    $is_revision = wp_is_post_revision( $post_id  );
    $is_valid_nonce = ( isset ( $_POST[ 'wpark_pk_nonce' ] ) && wp_verify_nonce( $_POST[ 'wpark_pk_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    if ( isset ( $_POST[ 'pk_numero' ] ) ) {
        update_post_meta( $post_id, 'pk_numero', sanitize_text_field( $_POST[ 'pk_numero' ] ) );
    }
    if ( isset ( $_POST[ 'pk_paivamaara' ] ) ) {
        update_post_meta( $post_id, 'pk_paivamaara', sanitize_text_field( $_POST[ 'pk_paivamaara' ] ) );
    }

    /* HTML-pöytäkirjoja varten, kommentoitu pois

    if ( isset ( $_POST[ 'poytakirja' ] ) ) {
        update_post_meta( $post_id, 'poytakirja', $_POST[ 'poytakirja' ]  );
    }
     */
    $pktitle = array();
    $pktitle['ID'] = $post_id;
    $vuosi = get_the_terms( $post_id, 'vuosi' );

    if ( get_post_type() == 'poytakirjat' ) {
        $pktitle['post_title'] = 'Pöytäkirja ' . get_post_meta( $post_id, 'pk_numero', true ) . '/' . $vuosi[0]->name;
    }

    remove_action( 'save_post', 'wpark_pk_meta_save' );
    wp_update_post($pktitle);
    add_action( 'save_post', 'wpark_pk_meta_save' );
}

add_action( 'save_post', 'wpark_pk_meta_save' );

/* Templojen lataus */

function wpark_load_templates( $original_template ) {
    if ( get_query_var( 'post_type' ) !== 'poytakirjat' ) {
        return $original_template;
    }
    if ( is_archive() || is_search() ) {
        return plugin_dir_path( __FILE__ ) . 'templates/poytakirjat-archive.php';
    } elseif(is_singular('poytakirjat')) {
        return plugin_dir_path( __FILE__ ) . 'templates/poytakirjat-single.php';
    } else {
        return get_page_template();
    }
    return $original_template;
}
add_action( 'template_include', 'wpark_load_templates' );

/* Ohjeet-sivu */

function wpark_pk_add_help_page() {

    add_submenu_page( 
        'edit.php?post_type=poytakirjat',
        'Pöytäkirjojen ohjeet',
        'Ohjeet',
        'manage_options',
        'pk-ohjeet',
        'wpark_pk_help_cb'
    );
}

add_action( 'admin_menu', 'wpark_pk_add_help_page' ); 

function wpark_pk_help_cb() {
?>
    <div class="help-page">
        <h1>Ohjeet pöytäkirjojen hallintaan</h1>
        <h3>Pöytäkirjan lisääminen</h3>
        <p>Sinulla tulisi olla pöytäkirjan PDF-tiedosto tietokoneellasi ennen aloitusta</p>
        <ol type="1">    
            <li>Valitse järjestysnumero</li>
            <li>Valitse "Kokouksen päivämäärä" kentästä avautuvasta kalenterista kokouksen päivämäärä</li>
            <li>Valitse vuosi (jona kokous pidettiin) kategorisointia varten</li>
            <li>Valitse pöytäkirjan tyyppi (kts. kohta "Tyypeistä")</li>
            <li>Paina "Julkaise"</li>
            <li>Valmista! Käy vielä tarkistamassa että lisäämäsi pöytäkirja näkyy oikein</li>
        </ol>

        <h3>Tyyppien ja vuosien hallinta</h3>
        <p>Tyyppejä voi lisätä tarpeen mukaan Pöytäkirjat->Tyypit -valikosta. Vuosia vastaavasta. Ainoa täytettävä kenttä on "Nimi". Nimen täyttämisen jälkeen paina "Lisää uusi Tyyppi/Vuosi"-näppäintä ja uusi vuosi/tyyppi on käytettävissä pöytäkirjan lisäyssivulla</p>

        <h3>Tyypeistä</h3>
        <p>Hallituksen kokousten pöytäkirjojen tyyppi on "Hallitus"</p>
        <p>Syyskokouksella ja kevätkokouksella on omat tyyppinsä, muut yhdistyksen kokoukset tyyppiin "Yhdistys"</p>
        <p>Toimikuntien kokousten pöytäkirjojen tyyppi on toimikunnan nimi, esim. "WWW-toimikunta"</p>
    </div>

<?php 
}

/* Adminin listauksen dataa */

function wpark_pk_columns( $columns ) {
    $columns['kokouksen_pvm'] = 'Kokouksen päivämäärä';
    return $columns;
}

add_filter( 'manage_edit-poytakirjat_columns', 'wpark_pk_columns' );

function wpark_pk_populate_columns( $column ) {
    if ( 'kokouksen_pvm' == $column ) {
        $kok_pvm = esc_html( get_post_meta( get_the_ID(), 'pk_paivamaara', true ) );
        echo $kok_pvm;
    }
}

add_action( 'manage_posts_custom_column', 'wpark_pk_populate_columns' );


