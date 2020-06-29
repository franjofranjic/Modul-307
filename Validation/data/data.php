<?php
$var = new daten();

class daten{

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
            case "insertData":
                $this->insertData();
                break;
            default:
                $this->getData();
                break;     
        }
    }

    function insertData(){
        if(isNameValid($_POST["name"])) {
            if($this->id == 0) {
                $sql = "INSERT INTO autos(name, kraftstoff, bauart, betankungen, farbe) 
                VALUES('" .$_POST["name"] ."','" .$_POST["kraftstoff"] ."','" .$_POST["bauart"] ."','" .$_POST["betankungen"] ."','" .$_POST["farbe"] ."')";
            } else {
                $sql = "UPDATE autos SET name='" .$_POST["name"] ."', kraftstoff='" .$_POST["kraftstoff"] ."', bauart='" .$_POST["bauart"] ."', betankungen='" .$_POST["betankungen"] ."', farbe='" .$_POST["farbe"] ."' WHERE id=" .$this->id;
            }
    
            $auto = array();
            $auto['data']=array();
            $auto['error']= array();
            $auto['error'][0]['meldung'] = "Auto erfolgreich erstellt/bearbeitet";
            echo json_encode($auto);
        }
    }


    function isNameValid()
        {
            $name = $_POST['name'];
            $auto = array();
            $auto['data']=array();
            $auto['error']= array();
            //Nachname nicht länger als 255 Zeichen und nicht kürzer als 3 Zeichen
            if(strlen($name) > 255 || strlen($name) < 3)
            {
                //Nachname ist zulange/zukurz
                $auto['error'][0] = 'Der Name muss zwischen 3 und 255 Zeichen haben';
                return false;
            } else {
                //Nachname darf folgende Zeichen nicht enthalten
                if(preg_match('/[<>\'%&1234567890]/', $nachname)){
                    //Nachname enthält ein verbotenes Zeichen
                    $auto['error'][0] = 'Der Nachname darf die Zeichen [, <, >, \', %, &, ] und Zahlen nicht enthalten';
                    return false;
                }
            }
            //Nachname ist gültig
            return true;
        }
}

?>