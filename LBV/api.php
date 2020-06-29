<?php
// Instanz erstellen und Konstruktor aufrufen
$var = new daten();
// Instanz löschen und Destriktor aufrufen
$var = NULL;
/**
* Meine Klasse
* @author   	Josip Skara
* @copyright	Josip Skara
* @license		OpenSource
* @version  	1.0
* @category 	Controller 
*/ 
class daten{
    private $database = 'data/meinedatei.sqlite3';
    private $id;
    private $con;
    private $myArr = array();
    /**
    * Constructor
    * @param nothing
    * @return  nothing
    */
    function __construct(){
        //Method chkDatabase aufrufen
        $this->chkDatabase();
        $this->con = new SQLite3($this->database);
        //Action/id auslesen (@ unterdrücken der Fehlermeldung)
        $action = @$_GET['action'];
        $this->id = @$_GET['id'];
        $this->myArr['GET']= $_GET;
        $this->myArr['POST']= $_POST;
        $this->myArr['sql']= array();
        $this->myArr['data']= array();
        $this->myArr['error']= array();
        $this->myArr['success']= array();

        switch($action){
            case "getData":
                //api.php?action=getData
                //api.php?action=getData&id=2
                $this->getData();
            break;

            case "delete":
                //api.php?action=delete&id=2
                $this->delete();
            break;

            case "updateInsert":
                //auto.php?action=update_insert          //insert
                //auto.php?action=update_insert&id=3     //update
                $this->updateInsert();
            break;

            default:
                //getData wenn eine falsche Aktion aufgeführt wird
                $this->getData();
        }
    }

    /**
      * Daten als JSON ausgaben
      * @param id Falls nur ein Datensatz ausgegeben werden soll
      * @return JSON echo vom JSON
      */
      private function getData(){
        //Wenn id nicht gesetzt alle Datensätze auslesen
        if(0 == $this->id){ 
            $sql ="SELECT * FROM kunden";
        } else {
            //Wenn id gesetzt nur einen Datensatz auslesen
            $sql ="SELECT * FROM kunden WHERE id =" .$this->id;
        }
        //Die Resultate werden abgespeichert
        $results = $this->con->query($sql);

        //Variable i wird auf 0 gesetzt
        $i = 0;
        //Für alle Datensätze wiederholen
        while($res = $results->fetchArray(SQLITE3_ASSOC)){
            $this->myArr['data'][$i] = $res;
            $i++;
        }
        //Überprüfen ob i noch null ist
        if($i==0){
            //wenn i noch null ist kommt ein Fehler, denn keine Daten sind vorhanden
            $this->myArr['error'][] = 'fehlerhaft -> keine Daten vorhanden';
        }
        echo json_encode($this->myArr);
     }

     /**
      * Daten löschen
      * @param id vom Auto
      * @return JSON echo vom JSON
      */

      private function delete(){
        //Datensatz mit einer bestimmten id wird gelöscht
        $sql ="DELETE FROM kunden WHERE id =" .$this->id;
        //Absetzten des sql-Kommandos
        $this->myArr['sql']= $sql;

        //Einfügen von Meldungen in den Array
        if($this->con->query($sql)){
            //Datensatz konnte gelöscht werden
            $this->myArr['success'][] = 'Datensatz gelöscht';
        } else {
            //Datensatz konnte nicht gelöscht werden
            $this->myArr['error'][] = 'Fehler beim Löschen';
        }

        echo json_encode($this->myArr);
      }

      /**
      * ein Auto anlegen oder updaten
      * @param id vom Kunden
      * @return JSON echo vom JSON
      */
        private function updateInsert(){
            //Aus dem POST werden die Werte name, kraftstoff, ... genommen
            $vorname = $_POST['vorname'];
            $nachname = $_POST['nachname'];
            $email = $_POST['email'];         
            $themen = $_POST['themen'];
            $firma = $_POST['firma'];
            $werbung = $_POST['werbung'];
            

                if($this->isEmailValid() && $this->isNummerValid() && $this->isVornameValid() && $this->isNachnameValid())
                {
                    //Überprüfung, ob eine id gesetzt wurde
                    if($this->id==0)
                    {
                        //Wenn keine id gesetzt
                        //neuer Datensatz wird eingefügt mit den ausgelesenen Werten
                        $sql = "INSERT INTO kunden ('vorname', 'nachname', 'email', 'themen', 'firma', 'werbung')
                        VALUES ('$vorname', '$nachname', '$email', '$themen', '$firma', '$werbung')";  
                    } else { 
                        //Wenn eine id gesetzt wurde
                        $sql = "UPDATE kunden SET vorname = '$vorname', nachname = '$nachname', email = '$email', themen = '$themen', firma = '$firma', werbung= '$werbung' WHERE id = " .$this->id;
                    }
                    //Absetzen des sql-Kommandos
                    $this->myArr['sql']= $sql;

                    //Einfügen von Meldungen in den Array
                    if($this->con->query($sql)){
                        
                        $this->myArr['success'][] = 'Kunden eingefügt/geupdatet';
                    } else {
                        //Fehler beim Updaten oder Einfügen
                        $this->myArr['error'][] = 'Fehler beim Einfügen/Updaten';
                    }

                echo json_encode($this->myArr);
            }
        }


        /**
      * Existiert eine DB?
      * @param
      * @todo
      * @return bool 0 oder 1 (1 wurde erstellt)
      */
      private function chkDatabase(){
            //Wenn File nicht existiert
            if(!file_exists($this->database)){
                //Datenbank wird erstellt
                $con = new SQLite3($this->database);
                
                //----------------------------------------------------------TABLE
                //Tabelle erstellen
                $sql = "CREATE TABLE IF NOT EXISTS kunden(
                    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                    vorname TEXT NOT NULL,
                    nachname TEXT NOT NULL,
                    email TEXT,
                    themen TEXT DEFAULT News,
                    firma INTEGER DEFAULT 0,
                    werbung TEXT DEFAULT Nein
                )";
                //Kommando Absetzen
                $con->query($sql);

                //----------------------------------------------------------DATENSATZ 1 - 3
                //Insert eines Datensatzes
                $sql ="INSERT INTO kunden(vorname, nachname, email, themen, firma, werbung) VALUES ('Max', 'Hauser', 'email@adresse.com', 'News', 0, 'Ja')";
                //Kommando Absetzen
                $con->query($sql);

                //Insert eines Datensatzes
                $sql ="INSERT INTO kunden(vorname, nachname, email, themen, firma, werbung) VALUES ('Linda', 'Daxer', 'info@daxer.com', 'Bücher', 1, 'Nein')";
                //Kommando Absetzen
                $con->query($sql);

                //Insert eines Datensatzes
                $sql ="INSERT INTO kunden(vorname, nachname, email, themen, firma, werbung) VALUES ('Max', 'Hauser', 'email@adresse.com', 'News', 1, 'Ja')";
                //Kommando Absetzen
                $con->query($sql);
                //Default
                $sql ="INSERT INTO kunden(vorname, nachname, email) VALUES ('Max', 'Hauser', 'email@adresse.com')";
                //Kommando Absetzen
                $con->query($sql);
            }
        }


        ///Validierung///
        function isVornameValid()
        {
            $vorname = $_POST['vorname'];
            //Vorname nicht länger als 255 Zeichen und nicht kürzer als 3 Zeichen
            if(strlen($vorname) > 255 || strlen($vorname) < 3)
            {
                //Vorname ist zulange/zukurz
                $this->myArr['error'][] = 'Der Vorname muss zwischen 3 und 255 Zeichen haben';
                return false;
            } else {
                //Vorname darf folgende Zeichen nicht enthalten
                if(preg_match('/[<>\'%&1234567890]/', $vorname)){
                    //Vorname enthält ein verbotenes Zeichen
                    $this->myArr['error'][] = 'Der Vorname darf die Zeichen [, <, >, \', %, &, ] und Zahlen nicht enthalten';
                    return false;
                }
            }
            //Vorname ist gültig
            return true;
        }
    
        /**
         * Funktion überprüft, ob der Nachname gültig ist
         * @param nichts
         * @return bool (true gültig false nicht gültig)
         */
        
        function isNachnameValid()
        {
            $nachname = $_POST['nachname'];
            //Nachname nicht länger als 255 Zeichen und nicht kürzer als 3 Zeichen
            if(strlen($nachname) > 255 || strlen($nachname) < 3)
            {
                //Nachname ist zulange/zukurz
                $this->myArr['error'][] = 'Der Nachname muss zwischen 3 und 255 Zeichen haben';
                return false;
            } else {
                //Nachname darf folgende Zeichen nicht enthalten
                if(preg_match('/[<>\'%&1234567890]/', $nachname)){
                    //Nachname enthält ein verbotenes Zeichen
                    $this->myArr['error'][] = 'Der Nachname darf die Zeichen [, <, >, \', %, &, ] und Zahlen nicht enthalten';
                    return false;
                }
            }
            //Nachname ist gültig
            return true;
        }
        function isNummerValid()
        {
            $nummer = $_POST['firma'];
            if($nummer < 0)
            {

                $this->myArr['error'][] = 'Die Nummer darf nicht kleiner als 0 sein';
                return false;
            } else {
                //Nummer darf folgende Zeichen nicht enthalten
                if(preg_match('/[<>\'%&]/', $nummer)){
      
                    $this->myArr['error'][] = 'Die Nummer darf die Zeichen [, <, >, \', %, &, ] nicht enthalten';
                    return false;
                }else if(is_float($nummer)){
                    $this->myArr['error'][] = 'Die Nummer darf nur ganzzahlig sein';
                    return false;
                }
            }

            return true;
        }
        /**
         * Funktion überprüft, ob die eMail gültig ist
         * @param nichts
         * @return bool (true gültig false nicht gültig)
         */
        function isEmailValid()
        {
            //Email Domains, welche nicht gültig sind
            $wegwerfemail = array();
            $wegwerfemail[] = 'mailinator';
            $wegwerfemail[] = 'migmail';
            $wegwerfemail[] = 'dontmail';
            $wegwerfemail[] = 'tempmailer';
            $wegwerfemail[] = '10minutenemail';
            $wegwerfemail[] = 'emaildeutschland';
            
    
            $email = $_POST['email'];
            //Email wird validiert
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                //Email Adresse ist nicht gültig
                $this->myArr['error'][] = 'E-Mail nicht gültig';
                return false;
            } else {
                //Email wird beim @ aufgeteilt
                $strarr = explode('@', $email);
            
                ///Überprüfung ob es Domain gibt
                if(checkdnsrr($strarr[1], 'ANY')){
                    //Überprüfun ob die Email eine verbotene Adresse ist
                    foreach($wegwerfemail as $adresse){
                        if(strstr($strarr[1], $adresse)){
                            //Die Domain ist nicht erlaubt
                            $this->myArr['error'][] = 'Diese Domain ist nicht erlaubt';
                            return false;
                        }
                    }
                } else {
                    //Domain existiert nicht
                    $this->myArr['error'][] = 'Domain gibt es nicht.';
                    return false;
                }
                //Email adresse ist gültig
                return true;
            } 
        }    




    function __destruct(){
    }
}