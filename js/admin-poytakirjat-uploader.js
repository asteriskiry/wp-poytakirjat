/**
 * PDF-uploaderin js:t
 **/

/* Tallennetaan HTML-elementit muuttujiin */

var addButton = document.getElementById( 'pdf-upload-button' );
var deleteButton = document.getElementById( 'pdf-delete-button' );
var img = document.getElementById( 'pdf-tag' );
var hidden = document.getElementById( 'pdf-hidden' );
var pdfUploader = wp.media({
    title: 'Valitse pöytäkirja',
    button: { text: 'Valitse' },
    multiple: false,
    library: { order: 'ASC', orderby: 'date', type: 'application/pdf' }
});

addButton.addEventListener( 'click', function() {
    if ( pdfUploader ) {
        pdfUploader.open();
    }
} );

/* Datan tallennus */

pdfUploader.on( 'select', function() {
    var attachment = pdfUploader.state().get('selection').first().toJSON();
    img.setAttribute( 'src', attachment.sizes.full.url );
    hidden.setAttribute( 'value', JSON.stringify( [{ id: attachment.id, src: attachment.url, tnBig: attachment.sizes.full.url, tnMed: attachment.sizes.medium.url, tnSmall: attachment.sizes.thumbnail.url }]) );
    toggleVisibility( 'ADD' );
} );

/* Poistonappi */

deleteButton.addEventListener( 'click', function() {
    img.removeAttribute( 'src' );
    hidden.removeAttribute( 'value' );
    toggleVisibility( 'DELETE' );
} );

/* Lisäys- ja poistonappien piilotus */

var toggleVisibility = function( action ) {
    if ( 'ADD' === action ) {
        addButton.style.display = 'none';
        deleteButton.style.display = '';
        img.setAttribute( 'style', 'width: 100%; max-width: 500px; border: 1px solid gray; box-shadow: 2px 2px 2px #556b55;' );
    }

    if ( 'DELETE' === action ) {
        addButton.style.display = '';
        deleteButton.style.display = 'none';
        img.removeAttribute('style');
    }
};

/* Pakotetaan valitsemaan tiedosto */

jQuery(function($) {
    $('form').submit(function(event) {

        if (!($('#pdf-hidden').val())) {
            alert('Valitse tiedosto!');
            event.preventDefault();
        }
    })
});

/* Jos tiedosto on valittu, lisätään poistonappio ja piilotetaan lisäysnappi */

window.addEventListener( 'DOMContentLoaded', function() {
    if ( "" === pdfUploads.pdfdata || 0 === pdfUploads.pdfdata.length ) {
        toggleVisibility( 'DELETE' );
    } else {
        img.setAttribute( 'src', pdfUploads.pdfdata.tnBig );
        hidden.setAttribute( 'value', JSON.stringify([ pdfUploads.pdfdata ]) );
        toggleVisibility( 'ADD' );
    }
} );
