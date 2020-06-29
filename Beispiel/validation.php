<?php
$text = 'text.text@text.text';
// --------------------------------------------------------------------
// String teilen 
$array = explode('.', $text);
if(is_array($array)){
    echo '<br>Konnte bei . geteilt werden.';
    if (count($array)==3){
        echo '<br>Könnte ein Datum sein?';
    }
}
// --------------------------------------------------------------------
// könnte der TEXT HTML enthalten?
echo '<hr>';
$text = '<div>sdfasf</div>';
if(!checkHTML($text)){
    echo "<br>Text : ";
    echo htmlspecialchars($text);
    echo " : Hat vermutlich HTML drin";
}
function checkHTML($str)
{
	if(preg_match("/<(.*)>/",$str)){
		return false;
	}
	return true;
}
// --------------------------------------------------------------------
// Text enthällt unerlaubte Zeichen?
echo '<hr>';
$text = 'fgasfg<';
if(preg_match('/[\'%&<>]/', $text)){
    $nostr = htmlspecialchars('\'%&<>* öäü');
    echo '<br>Es sind unerlaubte Zeichen vorhanden wie z.B. :' . $nostr;
}
// --------------------------------------------------------------------
// leere Zeichen
$text = '          k';
$text = trim($text);


// --------------------------------------------------------------------
// Datum validieren 
echo '<hr>';
$date = '2.15.2011';
$regex = '/^(([0-3][0-9]|[0-9]).([0-1][0-9]|[0-9]).([0-9]){4})$/';
if(preg_match($regex,$date)){
    echo '<br>korrekt';
    $array = explode('.', $date);
    if($array[1]<=0 OR $array[1]>12){
        echo '<br>Monat nicht korrekt';  
    }
}
// --------------------------------------------------------------------
// email validieren
echo '<hr>';
$email = 'jaja@mailinator.com';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailErr = "<br>Falsches eMail Format";
}else{
    echo '<br>Könnte eine eMail sein';  
    $array = explode('@', $email);
    if ( checkdnsrr($array[1], 'ANY') ) {
        echo "<br>OK: DNS Record found";
        $wegwerfAdressen = array();
        $wegwerfAdressen[] = '10minutemail';
        $wegwerfAdressen[] = 'instantlyemail';
        $wegwerfAdressen[] = 'Tempmailer';
        $wegwerfAdressen[] = 'emaildeutschland';
        $wegwerfAdressen[] = 'dontmail';
        $wegwerfAdressen[] = 'migmail';
        $wegwerfAdressen[] = 'mailinator';
        //echo '<br> Mail: ' . $array[1];
        foreach($wegwerfAdressen as $adresse){
            if(strstr($array[1], $adresse)){
                echo '<br>Check: Wegwerf - Email nicht gestattet.<br>';
            }
        }
    }else {
        echo "<br>Fehler: NO DNS Record found";
    }
}
// --------------------------------------------------------------------
// Zahl validieren
echo '<hr>';
$zahl = -4;

if(is_numeric($zahl)){
    echo "<br>is_numeric";
}


// keine Negativen Zahlen
// nicht grösser als das DB Feld
// Float oder nicht?