$(function() {

    getData();
    $('.modal').modal();

    $("#addBtn").click(function() {
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = 0;
        $('#modaltitle').html('Auto hinzufügen');
        $('#modalinhalt').load('sites/formular.html')
        loadform(id);
    });

    $('#abbrechen').click(function() {
        console.log('abbrechen');
    });

    $('#speichern').click(function() {
        var name = $('#name').val();
        var kraftstoff = $('#kraftstoff').val();
        var farbe = $('#farbe').val();
        var bauart = $('#bauart').val();
        var tank = $('#tank').val();

        console.log(farbe);
        if (name.length < 3) {
            console.log('zu klein, mindestens 3 Zeichen');
            $('#name').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'red black-text'});
        } else {
            var mymodal = M.Modal.getInstance($('.modal'));
            mymodal.close();
        }
    });

    function loadform(id) {
        $('#modalinhalt').load('sites/formular.html', function (id) {
            $('select').formSelect();
            M.updateTextFields();
        });

        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
    }

    function getData() {
        $('tbody').html('');
        var template = $('template').html();

        $.ajax({
            method: "GET",
            url: "data/autos.json",
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
                    $('#modalinhalt').load('sites/formular.html');
                    loadform(id);
                });
                
                $('.tankenBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('tanken von: ' + id);
                });
                
                $('.removeBtn').click(function() {
                    var id = $(this).parent().attr('data-id');
                    console.log('löschen von: ' + id);
                });
            }
        })
    }
});



