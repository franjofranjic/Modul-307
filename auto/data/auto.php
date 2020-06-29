<?php
/**
 * Diese Klasse repräsentiert ein Auto
 * @author      Hans Muster
 * @version     1.0
 * @category    Logik
 */

 class auto{
    public $action = "getData";
    public $id = 0;
    private $DBName = "meineDB.sqlite3";

    /**
     * Konstruktor wird automatisch bein instanzieren ausgeführt
     */
    function __construct(){
        // action oder id im GET vorhanden?
        if(isset($_GET['action'])){
            $this->action = $_GET['action'];
        }
        if(isset($_GET['id'])){
            $this->id = $_GET['id'];
        }
        // existiert die Datenbank?
        $this->chkDB();

        // echo 'action=' . $this->action . " / id= " . $this->id;
        // alle Daten anzeigen
        // auto.php?action=getData -> im Browser eingeben
        // einen Datensatz ausgeben
        // auto.php?action=getData&id=3
        // Auto betanken
        // auto.php?action=tankeID&id=3
        // einen Datensatz löschen
        // auto.php?action=deleteID&id=3
        // einen Datensatz hinzufügen
        // auto.php?action=insert
        switch($this->action){
            case "getData":
                $this->getData();
                break;
            case "tankeID":
                $this->tankeID();
                break;
            case "deleteID":
                $this->deleteID();
                break;
            case "insert":
                $this->insert();
                break;
            default:
                $this->getData();
                break;
        }
    }

    /**
     * insert
     * @param int $this->id
     * @todo
     * @return str html temporär 
     */
    function insert(){
        // Daten vom JS auslesen die per POST gesendet wurden
        /*name: name,
        bauart: bauart,
        kraftstoff: kraftstoff,
        farbe: farbe,
        betankungen: betankungen*/
        $name = $_POST['name'];
        $bauart = $_POST['bauart'];
        $kraftstoff = $_POST['kraftstoff'];
        $farbe = $_POST['farbe'];
        $betankungen = $_POST['betankungen'];
        $mydate = $_POST['mydate'];

        $meldung = "";
        // id 0, dann insert
        if(0==$this->id){
            //INSERT
            $sql= "INSERT INTO autos (name, kraftstoff, farbe, bauart, betankungen,mydate) VALUES ('".$name."','".$kraftstoff."','".$farbe."','".$bauart."','".$betankungen."','".$mydate."')";
            // zur Überprüfung
            //echo $sql;

            $meldung = "INSERT erfolgreich, Auto erfasst.";
        }else{
            //UPDATE
            $sql = "UPDATE autos SET
            name = '".$name."',
            kraftstoff = '".$kraftstoff."',
            farbe = '".$farbe."',
            bauart = '".$bauart."',
            betankungen = '".$betankungen."',
            mydate = '".$mydate."'
            WHERE id=" . $this->id;
            $meldung = "Update erfolgreich, Auto mit id = ".$this->id." aktualisiert.";
        }

        
        $con = new SQLite3($this->DBName);
        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = $meldung;
        }
        echo json_encode($auto);

    }

    /**
     * delete
     * @param int $this->id
     * @todo
     * @return json 
     */
    function deleteID(){
        $sql="DELETE from autos WHERE id=".$this->id;
        $con = new SQLite3($this->DBName);

        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Auto mit id= " . $this->id . " wurde gelöscht";
        }
        echo json_encode($auto);
    }

    /**
     * tanken
     * @param int $this->id
     * @todo
     * @return json 
     */
    function tankeID(){
        $sql="UPDATE autos SET betankungen=betankungen+1 WHERE id=".$this->id;
        $con = new SQLite3($this->DBName);

        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Update erfolgreich, Auto wurde betankt";
        }
        echo json_encode($auto);
    }
 
    /**
     * Daten als JSON anzeigen
     * @param int $this->id - wenn > 0 dann nur einen Datensatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json 
     */
    function getData(){
        if(0==$this->id){
            //echo "alle Daten anzeigen.";
            $sql = "SELECT * FROM autos";
        }else{
            //echo "nur Datensatz mit id = " . $this->id . " anzeigen.";
            $sql = "SELECT * FROM autos WHERE id=".$this->id;
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
        //echo '<pre>'; 
        //print_r($auto); 
        //echo '<//pre>';

        
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
                betankungen INTEGER NOT NULL DEFAULT 0,
                mydate DATE NOT NULL DEFAULT CURRENT_DATE
            )";
            $con->query($sql);
            // Beispieldaten einfügen
            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart) 
                VALUES ('Passat', 'Diesel', '#000000', 'Limousine')";
            $con->query($sql);

            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart) 
                VALUES ('Honda', 'Benzin', '#008888', 'Bus')";
            $con->query($sql);

            $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart) 
                VALUES ('Opel', 'Elektro', '#005555', 'SUV')";
            $con->query($sql);

        }else{
            // File existiert und wird davon ausgegangen, dass auch Tabellen usw. existieren 
            // ACHTUNG: kann möglich sein, dass in der php.ini sqlite3 aktiviert werden muss
        }
    }
 }

 $neuesAuto = new auto();