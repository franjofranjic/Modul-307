$(function() {

    getData();
    $('.modal').modal();

    $("#addBtn").click(function() {
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = 0;
        $('#modaltitle').html('Auto hinzufügen');
        loadform(id);
    });

    $('#abbrechen').click(function() {
        console.log('abbrechen');
    });

    $('#speichern').click(function() {
        var data = {};
        data.name = $('#name').val();
        data.kraftstoff = $('#kraftstoff').val();
        data.farbe = $('#farbe').val();
        data.bauart = $('#bauart').val();
        data.betankungen = $('#tank').val();
        id = $('#FieldID').val();


        console.log(data);
        console.log(id);
        if (data.name.length < 3) {
            console.log('zu klein, mindestens 3 Zeichen');
            $('#name').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'red black-text'});
        } else {
            var mymodal = M.Modal.getInstance($('.modal'));
            mymodal.close();
            $.ajax({
                method: "POST",
                url: "data/auto.php?action=insertData&id=" + id,
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
            $('.datepicker').datepicker();
        });

        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
    }

    function getData() {
        $('tbody').html('');
        var template = $('template').html();

        $.ajax({
            method: "GET",
            url: "data/auto.php",
            dataType: "json",
            success: function (response) {
                // bestehende Anzeige leeren
                if (response['data'].length > 0) {
                    for (var i = 0; i < response.data.length; i++) {
                        var htmlanzeige = Mustache.to_html(template, response['data'][i]);
                        $('tbody').append(htmlanzeige)
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
                $('.editBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    $('#modaltitle').html('Auto bearbeiten: ' + id);
                    var mymodal = M.Modal.getInstance($('.modal'));
                    mymodal.open();
                    loadform(id);
                    $.ajax({
                        method:"GET",
                        url: "data/auto.php?action=getData&id="+id,
                        dataType: "json",
                        success: function (response) {
                            console.log(response.data[0]);
                            $('#name').val(response.data[0].name);
                            $('#kraftstoff').val(response.data[0].kraftstoff);
                            $('#farbe').val(response.data[0].farbe);
                            $('#bauart').val(response.data[0].bauart);
                            $('#tank').val(response.data[0].betankungen);
                        }
                    });
                });
                
                $('.tankenBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('tanken von: ' + id);
                    $.ajax({
                        url: 'data/auto.php?action=tankeID&id=' + id,
                        dataType: "json",
                        success: function (response) {
                            if (response['error'].length > 0) {
                                for (var i = 0; i < response.error.length; i++) {
                                    console.log(response['error'][i].meldung);
                                    M.toast({ html: response['error'][i].meldung, classes: 'rounded blue' });
                                }
                            }
                            getData();
                        }
                    });
                });
                
                $('.removeBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('delete von : ' + id);
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
                });
            }
        })
    }
});



