$(function() {
    getData();

    function checked(index,data) {
        var status = data.data[index].bestellung_status;
        console.log(status);
        if (status == 1)
            return true;
        else
            return false;
    }

    $('.modal').modal();

    $("#addBtn").click(function() {
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = 0;
        $('#modaltitle').html('Bestellung hinzufügen');
        loadform(id);
    });

    $('#abbrechen').click(function() {
        console.log('abbrechen');
    });

    $('#speichern').click(function() {
        var data = {};
        data.bestellung_artikel = $('#bestellung_artikel').val();
        data.bestellung_menge = $('#bestellung_menge').val();
        data.bestellung_preis = $('#bestellung_preis').val();
        data.bestellung_kaufdatum = $('#bestellung_kaufdatum').val();
        data.bestellung_bemerkung = $('#bestellung_bemerkung').val();
        if($('#bestellung_status').prop("checked")) {
            data.bestellung_status = 1;
        }else {
            data.bestellung_status = 0;
        }
        id = $('#FieldID').val();

        console.log(data);
        console.log(id);

        if (data.bestellung_artikel.length < 3) {
            console.log('zu klein, mindestens 3 Zeichen');
            $('#bestellung_artikel').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'red black-text'});
        } else if(data.bestellung_preis < 0){
            console.log('Preis muss vorhanden sein und grösser 0');
            $('#bestellung_preis').addClass('orange lighten-4');
            M.toast({ html: 'Bitte einen Stückpreis eingeben, der grösser als 0 ist', classes: 'red black-text'});
        }
        else {
            var mymodal = M.Modal.getInstance($('.modal'));
            mymodal.close();
            $.ajax({
                method: "POST",
                url: "data/data.php?action=insertData&id=" + id,
                data: data,
                dataType: "json",
                success: function (response) {
                    if (response['error'].length > 0) {
                        for (var i = 0; i < response.error.length; i++) {
                            console.log(response['error'][i].meldung);
                            M.toast({ html: response['error'][i].meldung, classes: 'rounded blue' });
                        }
                    }
                }
            });
        }
        getData();
    });

    function loadform(id) {
        $('#modalinhalt').load('sites/formular.html', function () {
            M.updateTextFields();
            $('#FieldID').val(id);
            $('select').formSelect();
            $('.datepicker').datepicker({
                format: "mm-dd-yyyy",
                defaultDate: true,
                firstDay: 1
            });
        });

        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
    }

    function getData() {
        $('tbody').html('');
        var template = $('template').html();

        $.ajax({
            method: "GET",
            url: "data/data.php",
            dataType: "json",
            success: function (response) {
                // bestehende Anzeige leeren
                if (response['data'].length > 0) {
                    for (var i = 0; i < response.data.length; i++) {
                        var htmlanzeige = Mustache.to_html(template, response['data'][i]);
                        $('tbody').append(htmlanzeige);
                        $('.onChecked').each(function(index){ //Checkboxen entsprechend setzen
                            $(this).prop('checked',checked(index,response));
                        });
                    }
                }
                if(response['meldung']) {
                    console.info(response[response['meldung']]);
                }
                if (response['error'].length > 0) {
                    for (var i = 0; i < response.error.length; i++) {
                        console.log(response['error'][i].meldung);
                        M.toast({ html: response['error'][i].meldung, classes: 'rounded red' });
                    }
                }

                // ToDo
                $('.editBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    $('#modaltitle').html('Bestellung bearbeiten: ' + id);
                    var mymodal = M.Modal.getInstance($('.modal'));
                    mymodal.open();
                    loadform(id);
                    $.ajax({
                        method:"GET",
                        url: "data/data.php?action=getData&id="+id,
                        dataType: "json",
                        success: function (response) {
                            console.log(response.data[0]);
                            $('#bestellung_artikel').val(response.data[0].bestellung_artikel);
                            $('#bestellung_menge').val(response.data[0].bestellung_menge);
                            $('#bestellung_preis').val(response.data[0].bestellung_preis);
                            $('#bestellung_kaufdatum').val(response.data[0].bestellung_kaufdatum);
                            $('#bestellung_bemerkung').val(response.data[0].bestellung_bemerkung);
                            $('#bestellung_status').prop('checked', response.data[0].bestellung_status);
                        }
                    });
                });
                
                //erledigt
                $('.removeBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('delete von : ' + id);
                    $.ajax({
                        url: 'data/data.php?action=deleteID&id=' + id,
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
                });

                //erledigt
                $('.onChecked').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('set checked/unchecked von : ' + id);
                    $.ajax({
                        url: 'data/data.php?action=checkID&id=' + id,
                        dataType: 'json',
                        success: function(response) {
                            // Wenn error - Meldungen existieren, anzeigen
                            console.log('hei');
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
            }
        })
    }

});