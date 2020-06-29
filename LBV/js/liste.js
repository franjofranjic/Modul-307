$(function() {
    //Alle Daten anzeigen
    getData();

    //Wenn auf Button save mit id = save geklickt
    $('#save').click(function (e) { 
        e.preventDefault();
        //alle Daten aus dem auslesen
        var id = $('#id').val();
        var vorname = $('#vorname').val();
        var nachname = $('#nachname').val();
        var email = $('#email').val();
        var themen = $('#themen').val();
        var firma = $('#firma').val();
        
        var werbung = '';

        
        $('input:radio').each(function(){
            if($(this).prop('checked')){
                werbung = $(this).val();
            }
        })

              
        if (vorname.length < 3) {
            //console.log('zu klein, mindestens 3 Zeichen');
            $('#vorname').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'rounded red black-text' });
        } else if(nachname.length < 3) {
            //console.log('zu klein, mindestens 3 Zeichen');
            $('#nachname').addClass('orange lighten-4');
            M.toast({ html: 'Name bitte min. 3 Zeichen lang', classes: 'rounded red black-text' });
        } else if(EMail(!email)){
            $('#email').addClass('orange lighten-4');
            M.toast({ html: 'Email nicht gültig', classes: 'rounded red black-text' });
        }else{

        
    
        //Ajax mit update, wenn eine ID gesetzt ist oder insert wenn keine gesetzt ist abschicken
        $.ajax({
            type: "POST",
            url: "./api.php?action=updateInsert&id=" + id,
            data: {
                vorname : vorname,
                nachname : nachname,
                email : email,
                themen : themen,
                firma : firma,
                werbung : werbung
            },
            dataType: "json",
            success: function (response) {
                showMeldungen(response);
                //console.log(response);
                if(response['error'].length == 0){
                    getData();
                    Felderleeren();
                }
            }
        });
    }
  });
     

  
  //Wenn auf Button cancel mit id = cancel geklickt
  $('#cancel').click(function (e) { 
        var id = $('#id').val();
        //console.log(id);
      Felderleeren();
      //Toast mit dem Hinweis "abgebrochen wird gezeigt."
      M.toast({html: 'abgebrochen', classes: 'rounded red accent-4'})
      e.preventDefault();
  });
});

function Felderleeren(){
    $('#id').val('');
    
    $('#vorname').val('');
    $('#nachname').val('');
    $('#email').val('');
    $('#themen').val('');
    $('#firma').val('');
    //console.warn($('#werbung').val());

    $('input:radio').each(function(){
        if($(this).val() == 'nein'){
            $(this).attr('checked','checked');
        }
    })

    
   
    
    //Textfelder werden upgedatet
    M.updateTextFields();
    $('select').formSelect();
}

//Funktion um alle Daten upzudaten.
function getData() {
    //body wird geleert  
    $('tbody').html('');
    var template = $('template').html();
    //console.log(template);

    //Ajax mit action = getData
    $.ajax({
        type: "POST",
        url: "./api.php?action=getData",
        dataType: "json",
        success: function (response) {
            showMeldungen(response);

            //Wenn im Array die data länger als 0 ist
            if (response['data'].length > 0) {
                
                //Für jeden Datensatz wiederholen
                for (i = 0; i < response['data'].length; i++) {

                    // 4. in der Schleife Mustache in der anzeige darstellen
                    var html = Mustache.to_html(template, response['data'][i]);
                    //html in den Tabellenbody einfügen
                    $('tbody').append(html);
                }
            }

            //Wenn auf Button mit der Klasse = edit geklickt
            $('.edit').click(function (e) { 
                e.preventDefault();
                //id wird aus dem Elternelement des aktuellen Elements ausgelesen
                var id = $(this).parent().attr('data-id');
                $('#id').val(id);
                
                
                $.ajax({
                    type: "post",
                    url: "./api.php?action=getData&id=" + id,
                    dataType: "json",
                    success: function (response) {
                       $('#vorname').val(response['data'][0]['vorname']);
                       $('#nachname').val(response['data'][0]['nachname']);
                       $('#email').val(response['data'][0]['email']);
                       $('#themen').val(response['data'][0]['themen']);
                       $('#firma').val(response['data'][0]['firma']);
                       $('#werbung').val(response['data'][0]['radio']);

                       //console.log(response['data'][0]['werbung']);
                       $('input:radio').each(function(){
                           if($(this).val() == response['data'][0]['werbung']){
                               $(this).attr('checked', 'checked');
                           }
                       })

                       //Textfelder werden upgedatet
                       M.updateTextFields();
                       $('select').formSelect();
                    }
                });
            });

            //Wenn auf Button mit der Klasse = delete geklickt
            $('.delete').click(function (e) { 
                e.preventDefault();
                //id wird aus dem Elternelement des aktuellen Elements ausgelesen
                var id = $(this).parent().attr('data-id');

                //Abfrage, ob der Datensatz wirklich gelöscht werden soll.
                var answer = confirm("Willst Du den Datensatz " + id + " wirklich löschen?");

                //Wenn die Antwort ja ist...
                if(answer)
                {
                    //Ajax mit action = delete und id = id wird ausgeführt.
                    $.ajax({
                        type: "post",
                        url: "./api.php?action=delete&id=" + id,
                        dataType: "json",
                        success: function (response) {
                            showMeldungen(response);

                            //Daten werden aktualisiert
                            getData();
                        }
                    });
                }
            });
        }
    });
}

//succes/error Meldungen
    function showMeldungen(response){
    // Wenn error - Meldungen existieren, anzeigen
    if (response['error'].length > 0) {
        console.info('error');
        for (var i = 0; i < response['error'].length; i++) {
            M.toast({ html: response['error'][i], classes: 'rounded red' });
        }
    }
    // Wenn success - Meldungen existieren, anzeigen
    if (response['success'].length > 0) {
        console.info('success');
        for (var i = 0; i < response['success'].length; i++) {
            M.toast({ html: response['success'][i], classes: 'rounded green' });
        }
    }
}
function EMail(s)
{
 var a = false;
 var res = false;
 if(typeof(RegExp) == 'function')
 {
  var b = new RegExp('abc');
  if(b.test('abc') == true){a = true;}
  }

       
 if(a == true)
 {
  reg = new RegExp('^([a-zA-Z0-9-._]+)'+
                   '(@)([a-zA-Z0-9-.]+)'+
                   '(.)([a-zA-Z]{2,4})$');
  res = (reg.test(s));
 }
 else
 {
  res = (s.search('@') >= 1 &&
         s.lastIndexOf('.') > s.search('@') &&
         s.lastIndexOf('.') >= s.length-5)
 }
 return(res);
} 

    


///////////////////
