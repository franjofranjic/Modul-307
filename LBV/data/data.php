<?php
/**
 * Diese Klasse repräsentiert ein Bestellliste
 * @author      Franjo Franjic
 * @version     1.0
 * @category    Logik
 */

 class auto{
    public $action = 'getData';
    public $id = 0;

    /**
    * Kontstruktor wird automatisch bein instanzieren ausgeführt
    */
    function __construct() {
        // action oder id im GET vorhanden
        if(isset($_GET['action'])) {
            $this->action = $_GET['action'];
        }
        if(isset($_GET['id'])) { 
            $this->id = $_GET['id'];
        }
        // existiert die Datenbank?
        $this->chkDB();

        switch($this->action){
            case "getData":
                $this->getData();
                break;
            case "deleteID":
                $this->deleteID();
                break;
            case "insertData":
                $this->insertData();
                break;
            default:
                $this->getData();
                break;     
        }
    }

    function isValidArtikel() {
        $bestellung = array();
        $bestellung['data']=array();
        $bestellung['error']= array();

        $artikel = $_POST['bestellung_artikel'];
            //Nachname nicht länger als 255 Zeichen und nicht kürzer als 3 Zeichen
            if(strlen($nachname) > 255 || strlen($nachname) < 3)
            {
                //Nachname ist zulange/zukurz
                $bestellung['error'][0] = 'Der Artikel muss zwischen 3 und 255 Zeichen haben';
                return false;
            } else {
                //Nachname darf folgende Zeichen nicht enthalten
                if(preg_match('/[<>\'%&1234567890]/', $bestellung)){
                    //Nachname enthält ein verbotenes Zeichen
                    $this->myArr['error'][] = 'Der Artikel darf die Zeichen [, <, >, \', %, &, ] und Zahlen nicht enthalten';
                    return false;
                }
            }
            //Nachname ist gültig
            return true;
    }

    function isValidMenge()
    {
        $bestellung = array();
        $bestellung['data']=array();
        $bestellung['error']= array();
        $menge = $_POST['bestellung_menge'];
        if($menge < 1)
        {

            $bestellung['error'][0] = 'Die Menge darf nicht kleiner als 1 sein';
            return false;
        } else {
            //Nummer darf folgende Zeichen nicht enthalten
            if(preg_match('/[<>\'%&]/', $menge)){
  
                $bestellung['error'][0] = 'Die Menge darf die Zeichen [, <, >, \', %, &, ] nicht enthalten';
                return false;
            }else if(is_float($menge)){
                $bestellung['error'][0] = 'Die Menge darf nur ganzzahlig sein';
                return false;
            }
        }

        return true;
    }

    function isValidPreis()
    {
        $bestellung = array();
        $bestellung['data']=array();
        $bestellung['error']= array();
        $preis = $_POST['bestellung_preis'];
        if($preis < 1)
        {

            $bestellung['error'][0] = 'Der Preis darf nicht kleiner als 0 sein';
            return false;
        } else {
            //Nummer darf folgende Zeichen nicht enthalten
            if(preg_match('/[<>\'%&]/', $menge)){
  
                $bestellung['error'][0] = 'Der Preis darf die Zeichen [, <, >, \', %, &, ] nicht enthalten';
                return false;
            }
        }

        return true;
    }
    
    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json
     */
    function insertData(){
        $new_status = 0;

        $res = explode("-", $_POST["bestellung_kaufdatum"]);
        $new_date = $res[2]."-".$res[0]."-".$res[1];

        if($this->isValidArtikel() && $this->isValidMenge() && $this->isValidPreis()) {
            if($this->id == 0) {
                $sql = "INSERT INTO franjo_bestellung (bestellung_artikel, bestellung_menge, bestellung_preis, bestellung_kaufdatum, bestellung_bemerkung, bestellung_status)
                VALUES('" .$_POST["bestellung_artikel"] ."','" .$_POST["bestellung_menge"] ."','" .$_POST["bestellung_preis"] ."','" .$new_date ."','" .$_POST["bestellung_bemerkung"]."'," .$new_status .")";
            } else {
                $sql = "UPDATE franjo_bestellung SET bestellung_artikel='" .$_POST["bestellung_artikel"] ."', bestellung_menge='" .$_POST["bestellung_menge"] ."', bestellung_preis='" .$_POST["bestellung_preis"] ."', bestellung_kaufdatum= '".$new_date ."', bestellung_bemerkung='" .$_POST["bestellung_bemerkung"]."', bestellung_status=" .$new_status ." WHERE bestellung_id=" .$this->id;
            }
            $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 

            $bestellung = array();
            $bestellung['data']=array();
            $bestellung['error']= array();
            if($con->query($sql)){
                $bestellung['error'][0]['meldung'] = "Bestellung erfolgreich erstellt/bearbeitet";
            }
            echo json_encode($bestellung);
        }   
    }

    /**
     * delete
     * @param int $this->id
     * @todo
     * @return json 
     */
    function deleteID(){
        $sql="DELETE from franjo_bestellung WHERE bestellung_id=".$this->id;
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 

        $bestellung = array();
        $bestellung['data']=array();
        $bestellung['error']= array();
        if($con->query($sql)){
            $bestellung['error'][0]['meldung'] = "Bestellung mit id= " . $this->id . " wurde gelöscht";
        }
        echo json_encode($bestellung);
    }

    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @return json
     */
    function getData(){
        // if else abändern
        if(0==$this->id){
            $sql = "SELECT * FROM franjo_bestellung";
        }else{
            $sql = "SELECT * FROM franjo_bestellung WHERE bestellung_id=" .$this->id;
        }
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 
        $results = $con->query($sql);
        $arr = array();
        $i=0;
        while($res = mysqli_fetch_array($results)){
            foreach($res as $key => $value){
                // $key = name der Spalte
                // $value = wert
                if(!is_int($key)){
                    if($key === 'bestellung_kaufdatum') {
                        $timestamp = strtotime($value);
                        $value = date("d-m-Y", $timestamp);
                    }
                    $arr[$i][$key] = $value;
                }
            }
            $i++;
        }
        $bestellung = array();
        $bestellung['data']=$arr;
        $bestellung['error']= array();

        echo json_encode($bestellung);
        $con->close();
    }

    /**
     * Datenbank Check und erstellen falls nicht vorhanden
     */
    function chkDB(){
        define('MYSQL_HOST',"localhost");  
        define('MYSQL_USER',"root");  
        define('MYSQL_PW',"");  
        define('MYSQL_DB',"m307_franjo"); 
        define('MYSQL_INFO_DB',"information_schema"); 

        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 
        if(mysqli_connect_errno()){ 
            // DB existiert nicht, also neu erstellen 
            $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW); 
            $createdb = "CREATE DATABASE IF NOT EXISTS " . MYSQL_DB . " DEFAULT CHARACTER SET utf8"; 
            $con->query($createdb);     
        } 
        $con->select_db(MYSQL_INFO_DB) or die('Datenbankverbindung nicht möglich'); 

        $result = $con->query("SELECT count(table_name) as tables FROM TABLES 
        WHERE table_schema = '".MYSQL_DB."'"); 
        if($result){ 
            while($res = mysqli_fetch_array($result)){ 
                foreach($res as $key => $value){ 
                    $arr[$key] = $value; 
                } 
            } 
         
            if($arr['tables'] == '0'){ 
            $con->select_db(MYSQL_DB) or die('Datenbankverbindung nicht möglich'); 
            $sql = "CREATE TABLE IF NOT EXISTS franjo_bestellung (
                bestellung_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
                bestellung_artikel TEXT NOT NULL,
                bestellung_menge INTEGER NOT NULL DEFAULT 1,
                bestellung_preis DECIMAL(6,2) NOT NULL,
                bestellung_kaufdatum DATE,
                bestellung_bemerkung TEXT,
                bestellung_status TINYINT(1)   
                )";
            $con->query($sql);

                // Beispieldaten einfügen
                // WICHTIG: DATUM
                $sql = "INSERT INTO franjo_bestellung (bestellung_artikel, bestellung_menge, bestellung_preis, bestellung_kaufdatum)
                VALUES ('Apple MacBook Air 13.3', 1, 1199.00, STR_TO_DATE('01-01-2016','%d-%m-%Y'))";   
                $con->query($sql);

                $sql = "INSERT INTO franjo_bestellung (bestellung_artikel, bestellung_menge, bestellung_preis, bestellung_kaufdatum)
                VALUES ('Apple Magic Mouse 2', 2, 79.00, STR_TO_DATE('01-01-2017','%d-%m-%Y'))";   
                $con->query($sql);

                $sql = "INSERT INTO franjo_bestellung (bestellung_artikel, bestellung_menge, bestellung_preis, bestellung_kaufdatum, bestellung_bemerkung, bestellung_status)
                VALUES ('Apple Thunderbolt/Ethernet', 3, 39.00, STR_TO_DATE('01-01-2018','%d-%m-%Y'), 'ist leicht beschädigt', 1)";   
                $con->query($sql);
            }
        } 
        $con->close();
    }
 }

 //für was das?
 $neuesAuto = new auto();
