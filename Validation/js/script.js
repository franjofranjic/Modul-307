$(function() {
    $('#name').keyup(function () { 
        if($('#name').val().length < 3 || $('#name').val().length > 10){
          
          //Wenn kleiner als 3 Zeichen, wird der Text rot gefärbt.
          $('.colorName').removeClass('green-text')
          $('.colorName').addClass('red-text');
        } else {
          //Wenn grösser oder genau 3 Zeichen, wird der Text grün gefärbt.
          $('.colorName').removeClass('red-text')
          $('.colorName').addClass('green-text');
        }
    });
    

});