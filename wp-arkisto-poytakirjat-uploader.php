<?php

/** 
 * Pöytäkirjojen PDF-uploaderi 
 **/

namespace pdf_uploader;

/* Lisätään metaboxi pöytäkirjojen lisäyssivuille */

function register_metaboxes() {
    add_meta_box(
        'pdf_uploader_metabox', 
        'Pöytäkirjan tiedosto', 
        __NAMESPACE__ . '\pdf_uploader_callback',
        'poytakirjat',
        'normal'
    );
}
add_action( 'add_meta_boxes', __NAMESPACE__ . '\register_metaboxes' );

/* HTML:n generointi lisäyssivulle */

function pdf_uploader_callback( $post_id ) {
    wp_nonce_field( basename( __FILE__ ), 'custom_pdf_nonce' ); 
    ?>

    <div id="metabox_wrapper">
        <img id="pdf-tag"></img>
		<input type="hidden" id="pdf-hidden" name="custom_pdf_data">
		<input type="button" id="pdf-upload-button" class="button" value="Lisää pöytäkirja">
		<input type="button" id="pdf-delete-button" class="button" value="Poista pöytäkirja">
	</div>

	<?php
}

/* Tallennus tietokantaan */

function save_custom_pdf( $post_id ) {
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'custom_pdf_nonce' ] ) && wp_verify_nonce( $_POST[ 'custom_pdf_nonce' ], basename( __FILE__ ) ) );
	if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		return;
	}
	if ( isset( $_POST[ 'custom_pdf_data' ] ) ) {
		$pdf_data = json_decode( stripslashes( $_POST[ 'custom_pdf_data' ] ) );
		if ( is_object( $pdf_data[0] ) ) {
			$pdf_data = array( 'id' => intval( $pdf_data[0]->id ), 'src' => esc_url_raw( $pdf_data[0]->src ), 'tnBig' => esc_url_raw( $pdf_data[0]->tnBig ), 'tnMed' => esc_url_raw( $pdf_data[0]->tnMed ), 'tnSmall' => esc_url_raw( $pdf_data[0]->tnSmall ) );
		} else {
			$pdf_data = [];
		}
		update_post_meta( $post_id, 'custom_pdf_data', $pdf_data );
	}
}
add_action( 'save_post', __NAMESPACE__ . '\save_custom_pdf' );
