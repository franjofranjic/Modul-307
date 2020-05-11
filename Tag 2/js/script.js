$(document).ready(function() {

    console.log('ready!');
    $('.modal').modal();
    $('select').formSelect();

    $("#addBtn").click(function() {
        console.log('add Vehicle');
        var mymodal = M.Modal.getInstance($('.modal'));
        mymodal.open();
        var id = $(this).parent().attr('data-id');
        $('#modaltitle').html('Auto hinzufügen');
        $('#modalinhalt').load('sites/formular.html')
    });

    $.ajax({
        method: "GET",
        url: "data/autos.json",
        dataType: "json",
        success: function (response) {
            // bestehende Anzeige leeren
            $('tbody').html('');
            var template = $('template').html();
            if (response.data.length > 0) {
                console.info('Daten erhalten und anzeigen');
                for (var i = 0; i < response.data.length; i++) {
                    console.log(response.data[i]);
                    var htmlanzeige = Mustache.to_html(template, response.data[i]);
                    $('tbody').append(htmlanzeige)
                }
            }
            if (response.error.length > 0) {
                for (var i = 0; i < response.error.length; i++) {
                    console.log(response.error[i].meldung);
                    M.toast({ html: response.error[i].meldung, classes: 'rounded red' });
                }
            }
            $('.editBtn').click(function() {
                console.log('edit');
                var mymodal = M.Modal.getInstance($('.modal'));
                mymodal.open();
                var id = $(this).parent().attr('data-id');
                $('#modaltitle').html('Auto bearbeiten: ' + id);
                $('#modalinhalt').load('sites/formular.html')
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
});



