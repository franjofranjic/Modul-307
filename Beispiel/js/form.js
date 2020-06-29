//Initialisieren von select
$('select').formSelect();


//Überprüfen wie lange der eingegebene Vorname ist.
$('#vorname').keyup(function () { 
    if($('#vorname').val().length < 3){
      
      //Wenn kleiner als 3 Zeichen, wird der Text rot gefärbt.
      $('.colorvorname').removeClass('green-text')
      $('.colorvorname').addClass('red-text');
    } else {
  
      //Wenn grösser oder genau 3 Zeichen, wird der Text grün gefärbt.
      $('.colorvorname').removeClass('red-text')
      $('.colorvorname').addClass('green-text');
    }
});

//Überprüfen wie lange der eingegebene Nachname ist.
$('#nachname').keyup(function () { 
    if($('#nachname').val().length < 3){
      
      //Wenn kleiner als 3 Zeichen, wird der Text rot gefärbt.
      $('.colornachname').removeClass('green-text')
      $('.colornachname').addClass('red-text');
    } else {
  
      //Wenn grösser oder genau 3 Zeichen, wird der Text grün gefärbt.
      $('.colornachname').removeClass('red-text')
      $('.colornachname').addClass('green-text');
    }
});

$('#firma').keyup(function(){
  var wert = $('#firma').val();

  if(0 == wert){
      $('#firma').addClass('red-text');
  } else{
      $('#firma').removeClass('red-text');
  }

  if(wert < 0){ 
      $('#firma').val(0);
      M.toast({
          html: '!nicht kleiner als 0', classes:"rounded red accent-4"
      });
  }


  if(isFloat(wert)){ 
      $('#firma').val(0);
      M.toast({
          html: '!Nur ganze Zahlen!!', classes:"rounded red accent-4"
      });
  }
  
  function isFloat(n){
      var int = parseInt(n);
      if(n - int){
          return 1;
      }else {
          return 0;
      }
  }


  
});
