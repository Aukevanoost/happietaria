<?php


namespace Models;


use core\Database;
use PDO;
use DateTime;
use tools\tSecurity;
use tools\tMailing;

class mCrew
{

    public $Conn;

    public function __construct()
    {
        $conn = new Database();
        $this->Conn = $conn->connectToDb();
    }



    /*
    *  ======| GET ALL COMMUNITIES |==================================
    *  Haalt alle verenigingen op
    */
    public function getCommunities(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT v.vereniging_id, v.naam, COUNT(g.vereniging_id) as gebruikers FROM vereniging v LEFT JOIN gebruiker g ON v.vereniging_id = g.vereniging_id GROUP BY v.vereniging_id");

        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }
        return $data;
    }



    /*
    *  ======| GET ALL SKILLS |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function getSkills(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT *  FROM vaardigheden");


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }
        return $data;
    }



    /*
    *  ======| GET ALL SKILLS |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function newCrewMember(){

        // kijken of velden leeg zijn
        $empty = 0;
        $ex_arr = array('opmerking');  // velden die leeg mogen zijn
        foreach($_POST as $name => $value){
            $empty = ($value == "" && !in_array($name, $ex_arr)) ? $empty+1 : $empty;
        }
        if($empty > 0){
            return "Een of meerdere velden zijn leeg";
        }


        // Kleine schoonmaak
        $phone          = preg_replace('/[^-\+\s_0-9]/',"",$_POST["telefoon"]);
        $voornaam       = filter_var($_POST["voornaam"],FILTER_SANITIZE_STRING);
        $achternaam     = filter_var($_POST["achternaam"],FILTER_SANITIZE_STRING);
        $email          = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        $vereniging     = preg_replace('/[^0-9]/',"",$_POST["vereniging"]);

        //$vaardigheden   = preg_replace('/[^0-9]/',"",$_POST["shift"]);
        $beschikbaar    = implode(",", $_POST["dagen"]);

        $opmerking      = filter_var($_POST["opmerking"],FILTER_SANITIZE_STRING);



        try{
            // Gebruiker toevoegen
            $stmt = $this->Conn->prepare("INSERT INTO gebruiker VALUES(NULL, :vereniging, :vnaam, :anaam, :email, '','', :tel, 2)");

            $stmt->bindParam(':tel',$phone);
            $stmt->bindParam(':vnaam',$voornaam);
            $stmt->bindParam(':anaam',$achternaam);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':vereniging',$vereniging);

            $stmt->execute();
            $user_id = $this->Conn->lastInsertId();

            // inschrijving toevoegen
            $stmt = $this->Conn->prepare("INSERT INTO inschrijving VALUES(NULL, :user_id, :beschikbaar, :opm, CURRENT_TIMESTAMP,1,0)");

            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':beschikbaar',$beschikbaar);
            $stmt->bindParam(':opm',$opmerking);

            $stmt->execute();
            $inschrijving_id = $this->Conn->lastInsertId();

            // kwaliteiten toevoegen
            //die(implode(",",$_POST["vaardigheden"]));
            foreach($_POST["vaardigheden"] as $skill){
                $stmt = $this->Conn->prepare("INSERT INTO inschrijving_vaardigheden VALUES(:inschrijving_id, :skill_id)");

                $stmt->bindParam(':inschrijving_id',$inschrijving_id);
                $stmt->bindParam(':skill_id',$skill);

                $stmt->execute();
            }


            return "Reservering was succesvol toegevoegd";
        }catch (Exception $ex){
            return "Reservering aanvragen mislukt, probeer het later opnieuw.";
        }


    }

    public function getRegistrations($action){
        $where  = "";
        if(is_numeric($action)){
            switch($action){
                case 1   : $where = "WHERE active = 1 AND contact_gehad = 0"; break;
                case 2   : $where = "WHERE active = 1 AND contact_gehad = 1"; break;
                case 3   : $where = "WHERE active = 0"; break;
            }
        }

        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, i.* FROM `inschrijving` i LEFT JOIN gebruiker g ON i.gebruiker_id = g.gebruiker_id " . $where);


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }

        return $data;
    }


    public function chgRegistration(){
        //echo '<pre>';
        //var_dump($_POST);
        //echo '</pre>';
        //die();
        if(isset($_POST["action"])){
            //foreach($_POST["registrations"] as $r){
            $ids = join("','",$_POST["registrations"]);

            switch($_POST["action"]){
                case "setSeen" : $hdr = 2; $qry = "contact_gehad = 1"; break;
                case "setNew"  : $hdr = 1; $qry = "contact_gehad = 0"; break;
                case "delete"  : $hdr = 3; $qry = "active = 0"; break;
                case "undo"  :   $hdr = 3; $qry = "active = 1"; break;
                //case "setSeen" : $qry = ""; break;
            }

            $stmt = $this->Conn->prepare("UPDATE inschrijving SET ".$qry." WHERE inschrijving_id IN ('".$ids."')");
            $stmt->execute();
        }
        header('location: /crew/index/'.$hdr);
    }
}