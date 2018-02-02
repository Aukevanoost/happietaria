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
    *  ======| GET FROM ID |==================================
    *  Get full ticket information based on a ticket id
    */
    public function getFromId($inschrijving){
        $data = array();
        $qry = "
			SELECT i.*, g.*, v.naam as vereniging           
			FROM inschrijving i                
			LEFT JOIN gebruiker g ON g.gebruiker_id = i.gebruiker_id                
			LEFT JOIN vereniging v ON v.vereniging_id = g.vereniging_id
			WHERE inschrijving_id = ".$inschrijving;

        $stmt = $this->Conn->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }

        return $data[0];
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
    *  ======| GET CHOSEN SKILLS |==================================
    *  Haalt alle categoriën op die gekozen zijn
    */
        public function getChosenSkills($inschrijving){
            $data = array();

            $qry = "
                SELECT * 
                FROM vaardigheden v
                LEFT JOIN inschrijving_vaardigheden iv ON v.skill_id = iv.skill_id
                WHERE iv.inschrijving_id = ".$inschrijving;

            $stmt = $this->Conn->prepare($qry);


            if($stmt->execute()){
                while($row = $stmt->fetch()){
                    array_push($data, $row);
                }
            }
            return $data;
        }




    /*
    *  ======| ADD CREW MEMBER |==================================
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


            return "Bedankt voor je bijdrage! we gaan er zsm naar kijken.";
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




    public function getMailData($r){
        $data = array();


        // Mail voorbereiden


        // subject
        $data["subject"] = "Betreft: uw inschrijving als vrijwilliger - Happietaria Zwolle";


        // uiteindelijke bericht
        $data["message"] = "Geachte heer/mevr ".$r["achternaam"]."\n\n
We hebben besloten dat jij een perfecte toevoeging bent aan ons happietaria team!


Voor meer vragen/opmerkingen kunt u altijd contact met ons opnemen via het contactformulier.\n\n
Met vriendelijke groet, \n
Het Zwolse Happietaria Team.
        ";

        return $data;
    }

    public function sendCreatedMail($inschrijving){
        $onderwerp = filter_var($_POST["onderwerp"],FILTER_SANITIZE_STRING);
        $inhoud = filter_var($_POST["inhoud"],FILTER_SANITIZE_STRING);


        tMailing::sendAcceptRefuseMail($inschrijving["email"], $onderwerp, $inhoud);


        return '<div class="chip">Mail is verstuurd<i class="close material-icons">close</i></div>';

        header("locattion: /inschrijving/index");
    }



    public function editCrewMember(){
        $data = "";


        $phone = preg_replace('/[^-\+\s_0-9]/',"",$_POST["phone"]);
        $voornaam = filter_var($_POST["voornaam"],FILTER_SANITIZE_STRING);
        $achternaam = filter_var($_POST["achternaam"],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        $user_id = preg_replace('/[^0-9]/',"",$_POST["user_id"]);


        $beschikbaar = filter_var($_POST["beschikbaar"],FILTER_SANITIZE_STRING);
        $opmerking = filter_var($_POST["opmerking"],FILTER_SANITIZE_STRING);


        try{
            // Inschrijving wijzigen
            $stmt = $this->Conn->prepare("UPDATE inschrijving SET beschikbaar = :beschikbaar, opmerking = :opmerking WHERE inschrijving_id = ".$_GET["id"]);

            $stmt->bindParam(':beschikbaar',$beschikbaar);
            $stmt->bindParam(':opmerking',$opmerking);

            if($stmt->execute()){
                // Gebruiker wijzigen
                $stmt = $this->Conn->prepare("UPDATE gebruiker SET voornaam = :voornaam, achternaam = :achternaam, email = :email, telefoon = :telefoon WHERE gebruiker_id = :gebruiker_id");

                $stmt->bindParam(':voornaam',$voornaam);
                $stmt->bindParam(':achternaam',$achternaam);
                $stmt->bindParam(':email',$email);
                $stmt->bindParam(':telefoon',$phone);
                $stmt->bindParam(':gebruiker_id',$user_id);

                if($stmt->execute()) {
                    // Confirmation bericht
                    $data = '<div class="chip">Inschrijving is succesvol gewijzigd!<i class="close material-icons">close</i></div>';
                    header("location: /crew/bekijken/".$_GET["id"]);
                }else{
                    $data = '<div class="chip">Tijdens het wijzigen van de gebruiker ging er iets mis<i class="close material-icons">close</i></div>';
                }


            }else{
                $data = '<div class="chip">Tijdens het wijzigen van de inschrijving ging er iets mis<i class="close material-icons">close</i></div>';

            }



        }catch(Exception $ex){
            $data = '<div class="chip">Tijdens het wijzigen van de inschrijving ging er iets mis<i class="close material-icons">close</i></div>';
        }


        return $data;
    }
}
