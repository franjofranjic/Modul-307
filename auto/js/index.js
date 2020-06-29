// JQuery - Document ready
$(function() {
    // ----------------------------------------------
    // Beim Starten der Seite alle Daten anzeigen
    // ----------------------------------------------
    getData();
    // ----------------------------------------------
    // Initialisierung vom Modal
    // ----------------------------------------------
    $('.modal').modal();
    // ----------------------------------------------
    // Button hinzufügen eines Autos
    // ----------------------------------------------
    $('#addauto').click(() => {
        console.log('addauto');
        $('#modat_titel').html('Neues Auto erfassen');
        //$('#modat_inhalt').load('sites/formular.html');
        var id = 0;
        loadform(id);
    });
    // ----------------------------------------------
    // Formular Button abbrechen
    // ----------------------------------------------
    $('#abbrechen').click(function() {
        console.log('abbrechen');
    });
    // ----------------------------------------------
    // Formular Button senden
    // ----------------------------------------------
    $('#speichern').click(function() {
        console.log('speichern');
        // Formulardaten auslesen
        var name = $('#name').val();
        var kraftstoff = $('#kraftstoff').val();
        var farbe = $('#farbe').val();
        var bauart = $('#bauart').val();
        var betankungen = $('#betankungen').val();
        var mydate = $('#mydate').val();
        //id - Feld wird benutz für Update bei insert ist die ID 0
        var id = $('#id').val();

        // Kontrolle der Daten
        /*
        console.log(name);
        console.log(bauart);
        console.log(kraftstoff);
        console.log(farbe);
        console.log(betankungen);
        console.log(id);
        */
        var send = true;
        // Eingaben überprüfen
        if (name.length < 3) {
            console.log('zu klein, mindestens 3 Zeichen');
            $('#name').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'red black-text' });
            send = false;
        }

        if (name.length > 255) {
            $('#name').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte max. 255 Zeichen', classes: 'red black-text' });
            send = false;
        }

        // Formular senden wenn alle Überprüfungen korrekt sind
        if (send) {
            var mymodal = M.Modal.getInstance($('.modal'));
            mymodal.close();

            $.ajax({
                url: "data/auto.php?action=insert&id=" + id,
                type: "POST",
                dataType: "json",
                data: {
                    name: name,
                    bauart: bauart,
                    kraftstoff: kraftstoff,
                    farbe: farbe,
                    betankungen: betankungen,
                    mydate: mydate
                },
                success: function(response) {
                    console.log('Daten an PHP gesendet und Rückmeldung:');
                    console.log(response);
                    // Wenn error - Meldungen existieren, anzeigen
                    if (response['error'].length > 0) {
                        console.info('Daten erhalten');
                        for (var i = 0; i < response['error'].length; i++) {
                            M.toast({ html: response['error'][i].meldung, classes: 'rounded green' });
                        }
                    }
                    getData();
                }
            });
        }
    });
});

// ----------------------------------------------
// Formular laden in das Modal
// ----------------------------------------------
function loadform(id) {
    $('#modat_inhalt').load('sites/formular.html', function() {
        $('select').formSelect();
        $('#id').val(id);
        // wenn id > 0 dann Felder füllen
        if (id > 0) {
            $.ajax({
                url: "data/auto.php?action=getData&id=" + id,
                dataType: 'json',
                success: function(response) {
                    console.info(response);
                    $('#name').val(response['data'][0]['name']);
                    $('#bauart').val(response['data'][0]['bauart']);
                    $('#kraftstoff').val(response['data'][0]['kraftstoff']);
                    $('#farbe').val(response['data'][0]['farbe']);
                    $('#betankungen').val(response['data'][0]['betankungen']);
                    $('#mydate').val(response['data'][0]['mydate']);
                    // Datum Initialization
                    $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        setDefaultDate: true,
                        firstDay: 1
                    });
                    // DROP - DOWN wieder initialisieren nach befüllen
                    $('select').formSelect();
                    // label für input - Felder klasse active hinzufügen
                    $('.labelset label').addClass('active');
                    M.updateTextFields();
                }
            });
        } else {
            // Datum Initialization
            var date = new Date();
            var str = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
            $('#mydate').val(str);
            // Datum Initialization
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                setDefaultDate: true,
                firstDay: 1
            });

        }
    });
    // Modal öffnen
    var mymodal = M.Modal.getInstance($('.modal'));
    mymodal.open();
}
// ----------------------------------------------
// Holen und Anzeigen der Daten
// ----------------------------------------------
function getData() {
    // leeren der Autoliste
    $('tbody').html('');
    // template in Variable laden für späteres anzeigen mit Mustache
    var mtpl = $('template').html();
    // JSON laden mittels AJAX
    $.ajax({
        // welche Datei wird aufgerufen
        url: './data/auto.php',
        // Rückgabetyp von der url (JSON, HTML, XML usw.)
        dataType: 'json',
        success: function(response) {
            // was passiert wenn die Datei erfolgreich aufgerufen wurde
            console.log(response);
            if (response['meldung']) {
                console.info(response['meldung']);
            }
            // Wenn error - Meldungen existieren, anzeigen
            if (response['error'].length > 0) {
                console.info('Daten erhalten');
                for (var i = 0; i < response['error'].length; i++) {
                    M.toast({ html: response['error'][i].meldung, classes: 'rounded red' });
                }
            }
            // Datensätze anzeigen
            if (response['data'].length > 0) {
                for (i = 0; i < response['data'].length; i++) {
                    //console.log(response[i]);
                    // 4. in der Schleife Mustache in der anzeige darstellen
                    var html = Mustache.to_html(mtpl, response['data'][i]);
                    //console.log(html);
                    $('tbody').append(html);
                }
            }
            // EVENTS SIND ERST JETZT ZU DEFINIEREN
            // ----------------------------------------------
            // Liste Button tanken eines Autos
            // ----------------------------------------------
            $('.tanken').click(function() {
                var id = $(this).parent().attr('data-id');
                console.log('tanken von : ' + id);
                $.ajax({
                    url: 'data/auto.php?action=tankeID&id=' + id,
                    dataType: 'json',
                    success: function(response) {
                        // Wenn error - Meldungen existieren, anzeigen
                        if (response['error'].length > 0) {
                            console.info('Daten erhalten');
                            for (var i = 0; i < response['error'].length; i++) {
                                M.toast({ html: response['error'][i].meldung, classes: 'rounded blue' });
                            }
                        }
                        getData();
                    }
                });
            });
            // ----------------------------------------------
            // Liste Button löschen eines Autos
            // ----------------------------------------------
            $('.delete').click(function() {
                var id = $(this).parent().attr('data-id');
                console.log('delete von : ' + id);
                var check = confirm('Wollen Sie den Datensatz wirklich löschen?');
                if (check) {
                    $.ajax({
                        url: 'data/auto.php?action=deleteID&id=' + id,
                        dataType: 'json',
                        success: function(response) {
                            // Wenn error - Meldungen existieren, anzeigen
                            if (response['error'].length > 0) {
                                console.info('Daten erhalten');
                                for (var i = 0; i < response['error'].length; i++) {
                                    M.toast({ html: response['error'][i].meldung, classes: 'rounded red' });
                                }
                            }
                            getData();
                        }
                    });
                }
            });
            // ----------------------------------------------
            // Liste Button editieren eines Autos
            // ----------------------------------------------
            $('.edit').click(function() {
                var id = $(this).parent().attr('data-id');
                console.log('edit von : ' + id);
                $('#modat_titel').html('Auto editieren von : ' + id);
                //$('#modat_inhalt').load('sites/formular.html');
                loadform(id);
            });

        }
    });
}