/**
 * Javascripit pöytäkirjojen fronttiin
 **/

/* Pöytäkirjojen sorttausta varten */

jQuery(function ($)  {

    $.fn.dataTable.moment( 'DD.MM.YYYY' );

    $('#pk-taulukko').DataTable({
        "order": [[ 3, "desc"  ], [1, "desc"]],
        "pageLength": 25,
        "language": {
            "sProcessing":    "Käsitellään...",
            "sLengthMenu":    "Näytä _MENU_ pöytäkirjaa",
            "sZeroRecords":   "Yhtäkään pöytäkirjaa ei löytynyt",
            "sEmptyTable":    "Ei löytynyt",
            "sInfo":          "Näytetään pöytäkirjat _START_-_END_ yhteensä _TOTAL_:stä pöytäkirjasta",
            "sInfoEmpty":     "Näytetään pöytäkirjat 0-0 yhteensä 0:sta pöytäkirjasta",
            "infoFiltered":   "(Haettu _MAX_:stä pöytäkirjasta)",
            "decimal":        ",",
            "thousands":      "",
            "sInfoPostFix":   "",
            "sSearch":        "_INPUT_",
            "searchPlaceholder": "Etsi..",
            "sUrl":           "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Ladataan...",
            "oPaginate": {
                "sFirst":    "Ensimmäinen",
                "sLast":    "Viimeinen",
                "sNext":    "Seuraava",
                "sPrevious": "Edellinen"
            },
        }
    });
});
