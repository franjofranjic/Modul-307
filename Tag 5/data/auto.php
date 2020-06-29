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
            $this->id = $_GET['id'];
        }
        // existiert die Datenbank?
        $this->chkDB();

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
            case "insertData":
                $this->insertData();
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
    function updateID(){
        $sql = "UPDATE autos SET name='" .$_POST["name"] ."', kraftstoff='" .$_POST["kraftstoff"] ."', bauart='" .$_POST["bauart"] ."', betankungen='" .$_POST["betankungen"] ."' WHERE id=" .$this->id;
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 
        echo $sql;
        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Auto erfolgreich bearbeitet";
        }
        echo json_encode($auto);
    }

    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json
     */
    function insertData(){
        if($this->id == 0) {
            $sql = "INSERT INTO autos(name, kraftstoff, bauart, betankungen, farbe) 
            VALUES('" .$_POST["name"] ."','" .$_POST["kraftstoff"] ."','" .$_POST["bauart"] ."','" .$_POST["betankungen"] ."','" .$_POST["farbe"] ."')";
        } else {
            $sql = "UPDATE autos SET name='" .$_POST["name"] ."', kraftstoff='" .$_POST["kraftstoff"] ."', bauart='" .$_POST["bauart"] ."', betankungen='" .$_POST["betankungen"] ."', farbe='" .$_POST["farbe"] ."' WHERE id=" .$this->id;
        }
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 

        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Auto erfolgreich erstellt/bearbeitet";
        }
        echo json_encode($auto);
    }

    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json
     */
    function tankeID(){
        $sql = "UPDATE autos SET betankungen=betankungen+1 WHERE id=" .$this->id;
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 
        
        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Update erfolgreich, Auto wurde betankt";
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
        $con = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PW, MYSQL_DB); 

        $auto = array();
        $auto['data']=array();
        $auto['error']= array();
        if($con->query($sql)){
            $auto['error'][0]['meldung'] = "Auto mit id= " . $this->id . " wurde gelöscht";
        }
        echo json_encode($auto);
    }

    /**
     * Daten als JSON anzeigen
     * @param int $this-> id - wenn > 0 dann nur einen Datnesatz anzeigen
     * @todo Datenbankverbindung kommt später im Moment nur die Logik realisieren
     * @return json
     */
    function getData(){
        // if else abändern
        if(0==$this->id){
            $sql = "SELECT * FROM autos";
        }else{
            $sql = "SELECT * FROM autos WHERE id=" .$this->id;
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
                    $arr[$i][$key] = $value;
                }
            }
            $i++;
        }
        $auto = array();
        $auto['data']=$arr;
        $auto['error']= array();

        echo json_encode($auto);
        $con->close();
    }

    /**
     * Datenbank Check und erstellen falls nicht vorhanden
     */
    function chkDB(){
        define('MYSQL_HOST',"localhost");  
        define('MYSQL_USER',"root");  
        define('MYSQL_PW',"");  
        define('MYSQL_DB',"meineDatenbank"); 
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
         
            // Abändern nach Aufgabenstellung

            if($arr['tables'] == '0'){ 
            $con->select_db(MYSQL_DB) or die('Datenbankverbindung nicht möglich'); 
            $sql = "CREATE TABLE IF NOT EXISTS autos (
                id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
                name TEXT NOT NULL,
                kraftstoff TEXT NOT NULL,
                farbe TEXT NOT NULL,
                bauart TEXT NOT NULL,
                betankungen INTEGER NOT NULL DEFAULT 0,
                inverkehrssetzung DATE NOT NULL,    
                )";
                $con->query($sql);

                // Beispieldaten einfügen
                $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                VALUES ('Passat', 'Diesel', '#000000', 'Limousine', '2020-05-18')";   
                $con->query($sql);

                $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                    VALUES ('Honda', 'Bezin', '#008888', 'PKW', , '2020-05-18')";   
                $con->query($sql);

                $sql = "INSERT INTO autos (name, kraftstoff, farbe, bauart)
                    VALUES ('Opel', 'Elektro', '#005555', 'SUV', , '2020-05-18')";   
                $con->query($sql);
            }
        } 
        $con->close();
    }
 }

 //für was das?
 $neuesAuto = new auto();
