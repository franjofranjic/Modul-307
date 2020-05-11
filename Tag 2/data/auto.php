<?php
/**
 * Diese Klasse repräsentiert ein Auto
 * @author      Franjo Franjic
 * @version     1.0
 * @category    Logik
 */

 class auto{
    public $action = 'getData';
    public $id = 0;
    private $DBName = "meineDB.sqlite3";

    /**
    * Kontstruktor wird automatisch bein instanzieren ausgeführt
    */
    function __construct() {
        // action oder id im GET vorhanden
        if(isset($_GET['action'])) {
            $this->action = $_GET['action'];
        }
        if(isset($_GET['id'])) { 
            $this->action = $_GET['id'];
        }
        // existiert die Datenbank?
        $this->chkDB();

        // echo 'action=' . $this->action . " / id= " . $this->id;
        switch($this->action){
            case "getData":
                $this->getData();
                break;
            case "tankeID":
                break;
            case "deleteID":
                break;
            case "insert":
                break;
            default:
                $this->getData();
                break;
                
        }
    }

    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json
     */
    function getData(){
        if(0==$this->id){
            // echo "alle Daten anzeigen";
            $sql = "SELECT * FROM autos";
        }else{
            // echo "nur Datensatz mit id = " . $this->id . " anzeigen.";
            $sql = "SELECT * FROM autos WHERE id=" .$this->id;
        }
        $con = new SQLite3($this->DBName);
        $results = $con->query($sql);
        $arr = array();
        $i=0;
        while($res = $results->fetchArray(SQLITE3_ASSOC)){
            foreach($res as $key => $value){
                // $key = name der Spalte
                // $value = wert
                $arr[$i][$key] = $value;
            }
            $i++;
        }
        $auto = array();
        $auto['data']=$arr;
        $auto['error']= array();

        echo json_encode($auto);
        // echo '<pre>';
        // print_r($arr);
        // echo '<pre>';
    }

    /**
     * Datenbank Check und erstellen falls nicht vorhanden
     */
    function chkDB(){
        // existiert die Datenbank, bzw. das file?
        if(!file_exists($this->DBName)){
            // Nein, dann File erstellen
            $con = new SQLite3($this->DBName);
            // Tabellen erstellen
            $sql = "CREATE TABLE IF NOT EXISTS autos (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                kraftstoff TEXT NOT NULL,
                farbe TEXT NOT NULL,
                bauart TEXT NOT NULL,
                betankungen INTEGER NOT NULL DEFAULT 0    
            )";
            $con->query($sql);
            // Beispieldaten einfügen
            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                VALUES ('Passat', 'Diesel', '#000000', 'Limousine')";   
            $con->query($sql);

            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                VALUES ('Honda', 'Bezin', '#008888', 'PKW')";   
            $con->query($sql);

            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                VALUES ('Opel', 'Elektro', '#005555', 'SUV')";   
            $con->query($sql);


        }else{
            // File existerit und wird davon ausgegangen, dass auch Tabellen usq. existieren
            // ACHTUNG: kann möglich sien, dass inder php.ini sqlite3 aktiviert werden muss
            $con = new SQLite3($this->DBName);
        }
    }
 }

 $neuesAuto = new auto();
