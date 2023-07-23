/**
 * Javascriptit admin-sivuille
 **/

/* Päivämäärän valitsin */

jQuery(document).ready(function() {
    let datepick = jQuery( '.datepicker'  );
    datepick.datepicker();
    datepick.datepicker( "option", "showAnim", "slideDown" );
});