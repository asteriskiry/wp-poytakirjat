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
	if(attachment.icon){
		img.setAttribute( 'src', attachment.icon );
		hidden.setAttribute( 'value', JSON.stringify( [{ id: attachment.id, src: attachment.url, tnBig: attachment.icon, tnMed: attachment.icon, tnSmall: attachment.icon }]) );
	} else {
		img.setAttribute( 'src', attachment.url );
		hidden.setAttribute( 'value', JSON.stringify( [{ id: attachment.id, src: attachment.url, tnBig: attachment.url, tnMed: attachment.url, tnSmall: attachment.url }]) );
	}
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
        img.setAttribute( 'style', 'width: 150px; height: auto;display:block;margin-bottom:10px;');
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