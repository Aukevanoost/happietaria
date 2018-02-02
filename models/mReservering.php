<?php
/* SELECT *, COUNT(user_id) as users FROM `company` c JOIN crew m ON m.company_id = c.company_id GROUP BY c.company_id */


namespace Models;


use core\Database;
use PDO;
use DateTime;
use tools\tSecurity;
use tools\tMailing;

class mReservering{

    public $Conn;

    public function __construct()
    {
        $conn = new Database();
        $this->Conn = $conn->connectToDb();
    }



    /*
    *  ======| GET FROM ID |==================================
    *  Get full ticket information based on a ticket id
    */
    public function getFromId($reservering){
        $data = array();
        $qry = "
			SELECT r.*, s.*, g.*, y.begin_tijd, y.eind_tijd, y.opmerking as shift, v.naam as vereniging, s.status_id as section              
			FROM reservering r                
			LEFT JOIN gebruiker g ON g.gebruiker_id = r.gebruiker_id                
			LEFT JOIN status s ON s.status_id = r.status_id
			LEFT JOIN vereniging v ON v.vereniging_id = g.vereniging_id
			LEFT JOIN shift y ON r.shift_id = y.shift_id
			WHERE reservering_id = ".$reservering;

        $stmt = $this->Conn->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }
        return $data[0];
    }



    /*
    *  ======| GET ALL CATEGORIES |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function getCategories(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT s.status_id, s.naam, s.locked, COUNT(r.reservering_id) as items FROM status s LEFT JOIN reservering r ON s.status_id = r.status_id GROUP BY s.status_id");


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }


        return $data;
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
    *  ======| GET ALL CATEGORIES |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function getShifts(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT *  FROM shift");


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }
        return $data;
    }



    /*
    *  ======| GET CATEGORY FROM ID |==================================
    *  Haalt een categorie op gebaseerd op het ID
    */
    public function getCatFromId($cat_id){
        $data = array();
        $qry = "            
            SELECT s.*, COUNT(r.reservering_id) as items 
            FROM status s 
            LEFT JOIN reservering r ON s.status_id = r.status_id         
			WHERE s.status_id = ".$cat_id;

        $stmt = $this->Conn->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }
        return $data[0];
    }



    /*
    *  ======| GET STATUSSES |==================================
    *  Haalt alle statussen op
    */
    public function getStates(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT s.*, COUNT(r.reservering_id) as items FROM status s LEFT JOIN reservering r ON s.status_id = r.status_id GROUP BY s.status_id");


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }


        return $data;
    }


    /*
    *  ======| GET ALL RESERVATIONS |==================================
    *  Haalt alle reserveringen op
    */
    public function getAllReservations(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, r.*, s.naam as status FROM `reservering` r LEFT JOIN gebruiker g ON r.gebruiker_id = g.gebruiker_id LEFT JOIN status s ON s.status_id = r.status_id ");


        if($stmt->execute()){
            while($company = $stmt->fetch()){
                array_push($data, $company);
            }
        }


        return $data;
    }



    /*
    *  ======| GET RESERVATIONS FROM STATE |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function getReservationsFromState($state){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, r.*, s.naam as status, y.opmerking as shift FROM `reservering` r LEFT JOIN gebruiker g ON r.gebruiker_id = g.gebruiker_id LEFT JOIN status s ON s.status_id = r.status_id LEFT JOIN shift y ON r.shift_id = y.shift_id WHERE r.status_id = ".$state);

        if($stmt->execute()){
            while($company = $stmt->fetch()){
                array_push($data, $company);
            }
        }

        return $data;
    }



    /*
    *  ======| GET RESERVATIONS FROM DATE |==================================
    *  Haalt alle reserveringen op van een bepaalde dag
    */
    public function getReservationsFromDate($date){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, r.*, s.naam as status, y.begin_tijd, y.eind_tijd, y.opmerking as shift FROM `reservering` r LEFT JOIN gebruiker g ON r.gebruiker_id = g.gebruiker_id LEFT JOIN status s ON s.status_id = r.status_id LEFT JOIN shift y ON r.shift_id = y.shift_id WHERE geaccepteerd = 1 AND datum BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59' ORDER BY y.begin_tijd, achternaam ");


        if($stmt->execute()){
            while($company = $stmt->fetch()){
                array_push($data, $company);
            }
        }


        return $data;
    }



    /*
    *  ======| ADD A STATUS |==================================
    *  Voegt een nieuwe status toe
    */
    public function newStatus(){
        // basic garbage removal
        $status = filter_var($_POST["status"],FILTER_SANITIZE_STRING);

        $stmt = $this->Conn->prepare("INSERT INTO status VALUES (NULL, :status, 1)");
        $stmt->bindParam(':status',$status);

        if($stmt->execute()){
            $data = '<div class="chip">New status has succesfully been added<i class="close material-icons">close</i></div>';
        }else{
            $data = '<div class="chip">Creating status failed<i class="close material-icons">close</i></div>';
        }

        return $data;
    }



    /*
    *  ======| DELETE STATUS |==================================
    *  Haalt alle categoriën met het aantal reserveringen dat erin zit
    */
    public function delStatus(){
        $data = "";
        //if( ($_SESSION['user']['role'] == 3)){
            $status = $this->getCatFromId($_GET["id"]);
            if($status["items"] == 0){
                try{
                    $qry = "DELETE FROM status WHERE status_id = ".$_GET["id"];
                    $stmt = $this->Conn->prepare($qry);
                    $stmt->execute();
                    header("location: /reservering/statussen");
                }catch(Exception $ex){
                    echo "Query failed: " + $ex;
                    die();
                }
            }else{
                echo '<b>Error 403: </b>You cannot delete a status that contains reservations.';
                die();
            }
        //}else{
        //    header("location: /pages/nopermission");
        //}
        return $data;
    }




    public function getJudgeMailData($j,$r){
        $data = array();


        // Mail voorbereiden
        $accept = ($j == 'ACCEPT') ? " geaccepteerd en verwerkt hebben! we zien u graag op het betreffende tijdstip." : "helaas op dat moment geen plek meer over hebben. We raden u aan om een ander tijdstip te proberen." ;

        $r["datum"] = new DateTime($r["datum"]);
        $datetime = $r["datum"]->format('d F');

        // subject
        $data["subject"] = "Betreft: uw reservering #".$r["reservering_id"] . " - Happietaria Zwolle";


        // uiteindelijke bericht
        $data["message"] = "Geachte heer/mevr ".$r["achternaam"]."\n\n
We hebben uw aanvraag van ".$datetime." bekeken en delen u bij deze mede dat 
Wij de reservering ".$accept."

Voor meer vragen/opmerkingen kunt u altijd contact met ons opnemen via het contactformulier.\n\n
Met vriendelijke groet, \n
Het Zwolse Happietaria Team.
        ";

        return $data;
    }



    public function getMailData($r){
        $data = array();


        // Mail voorbereiden

        $r["datum"] = new DateTime($r["datum"]);
        $datetime = $r["datum"]->format('d F');

        // subject
        $data["subject"] = "Betreft: uw reservering #".$r["reservering_id"] . " - Happietaria Zwolle";


        // uiteindelijke bericht
        $data["message"] = "Geachte heer/mevr ".$r["achternaam"]."\n\n
We hebben wat kleine wijziging doorgevoerd betreft uw reservering op ".$datetime.".

Voor meer vragen/opmerkingen kunt u altijd contact met ons opnemen via het contactformulier.\n\n
Met vriendelijke groet, \n
Het Zwolse Happietaria Team.
        ";

        return $data;
    }



    public function sendReservationMail($reservation){
        $onderwerp = filter_var($_POST["onderwerp"],FILTER_SANITIZE_STRING);
        $inhoud = filter_var($_POST["inhoud"],FILTER_SANITIZE_STRING);


        tMailing::sendAcceptRefuseMail($reservation["email"], $onderwerp, $inhoud);


        return '<div class="chip">Mail is verstuurd<i class="close material-icons">close</i></div>';
        //<div class="chip">New status has succesfully been added<i class="close material-icons">close</i></div>
        header("locattion: /reservering/index");
    }




    public function acceptOrRefuseReservation($r, $isAccepted){
            // Standaard input
            $stmt = $this->Conn->prepare("UPDATE reservering SET geaccepteerd = :accept, status_id = 2 WHERE reservering_id = ".$r["reservering_id"]);

            $stmt->bindParam(':accept',$isAccepted);

            $stmt->execute();
            return '<div class="chip">Reservering is succesvol beoordeeld<i class="close material-icons">close</i></div>';
    }




    public function editReservation(){
        $data = "";


        $phone = preg_replace('/[^-\+\s_0-9]/',"",$_POST["phone"]);
        $voornaam = filter_var($_POST["voornaam"],FILTER_SANITIZE_STRING);
        $achternaam = filter_var($_POST["achternaam"],FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);

        $status = preg_replace('/[^0-9]/',"",$_POST["status"]);
        $shift = preg_replace('/[^0-9]/',"",$_POST["shift"]);
        $user_id = preg_replace('/[^0-9]/',"",$_POST["user_id"]);
        $tafel = filter_var($_POST["tafel_nummer"],FILTER_SANITIZE_STRING);
        $pers = preg_replace('/[^0-9]/',"",$_POST["personen"]);
        $datum = DateTime::createFromFormat('Y-m-d', $_POST["date"]);
        $datum = $datum->format('Y-m-d');


        try{
            // Inschrijving wijzigen
            $stmt = $this->Conn->prepare("UPDATE reservering SET datum = :datum, shift_id = :shift, personen = :personen, tafel_nummer = :tafel, status_id = :status_id WHERE reservering_id = ".$_GET["id"]);

            $stmt->bindParam(':status_id',$status);
            $stmt->bindParam(':personen',$pers);
            $stmt->bindParam(':tafel',$tafel);
            $stmt->bindParam(':datum',$datum);
            $stmt->bindParam(':shift',$shift);

            $stmt->execute();


            // Gebruiker wijzigen
            $stmt = $this->Conn->prepare("UPDATE gebruiker SET voornaam = :voornaam, achternaam = :achternaam, email = :email, telefoon = :telefoon WHERE gebruiker_id = :gebruiker_id");

            $stmt->bindParam(':voornaam',$voornaam);
            $stmt->bindParam(':achternaam',$achternaam);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':telefoon',$phone);
            $stmt->bindParam(':gebruiker_id',$user_id);

            $stmt->execute();


            // Confirmation bericht
            $data = '<div class="chip">Reservering is succesvol gewijzigd!<i class="close material-icons">close</i></div>';
            header("location: /reservering/bekijken/".$_GET["id"]);
        }catch(Exception $ex){
            $data = '<div class="chip">Tijdens het wijzigen van de reservering ging er iets mis<i class="close material-icons">close</i></div>';
        }


        return $data;
    }




    public function newReservation(){

        // kijken of velden leeg zijn
        $empty = 0;
        $ex_arr = array('opmerking','telefoon');
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

        $shift          = preg_replace('/[^0-9]/',"",$_POST["shift"]);
        $pers           = preg_replace('/[^0-9]/',"",$_POST["personen"]);
        $opmerking      = filter_var($_POST["opmerking"],FILTER_SANITIZE_STRING);


        // voorwaarden controleren
        if(!is_numeric($pers) || $pers > 8){
            return 'Maximaal 8 personen per reservering. Als u toch met meer wilt komen, neem dan telefonisch contact met ons op';
        }

        try{
            //die($_POST["datum"]);
            $datetime       = DateTime::createFromFormat('d F, Y', $_POST["datum"]);
            $current_date   = new DateTime();

            // niet in het verleden
            if ($datetime < $current_date){
                return 'Deze datum is al geweest, Wij adviseren u een datum te kiezen die nog moet komen.';
            }

            // binnen de datums
            $begin_date = DateTime::createFromFormat('Y-m-d', "2018-02-19");
            $end_date = DateTime::createFromFormat('Y-m-d', "2018-03-17");
            if($datetime < $begin_date || $datetime > $end_date){
                return 'Wij zijn open tussen 19 februari en 17 maart 2018';
            }

            // Als dag ok is
            $dagokarray = array('Mon','Tue','Thu','Fri','Sat');
            if(!in_array($datetime->format ("D"),$dagokarray)){
                return 'Wij zijn open op maandag, dinsdag, donderdag, vrijdag en zaterdag';
            }

        }catch(Exception $ex){
            return 'Deze datum is niet geldig.';
        }


        // Gebruiker toevoegen
        try{
            $stmt = $this->Conn->prepare("INSERT INTO gebruiker VALUES(NULL, :vereniging, :vnaam, :anaam, :email, '','', :tel, 1)");

            $stmt->bindParam(':tel',$phone);
            $stmt->bindParam(':vnaam',$voornaam);
            $stmt->bindParam(':anaam',$achternaam);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':vereniging',$vereniging);

            $stmt->execute();
            $user_id = $this->Conn->lastInsertId();

            // reservering toevoegen
            $stmt = $this->Conn->prepare("INSERT INTO reservering VALUES(NULL, :user_id, :datum, :shift, :pers, '', 0, 1, :opm)");

            $stmt->bindParam(':user_id',$user_id);
            $stmt->bindParam(':datum',$datetime);
            $stmt->bindParam(':shift',$shift);
            $stmt->bindParam(':pers',$pers);
            $stmt->bindParam(':opm',$opmerking);

            $stmt->execute();

            return "Reservering was succesvol toegevoegd";
        }catch (Exception $ex){
            return "Reservering aanvragen mislukt, probeer het later opnieuw.";
        }



    }



    /*
    *  ======| SWITCH ACCEPT |==================================
    *  Wijzigt een reservering naar geaccepteerd en/of geweigerd.
    */
    public function switchAccept($reservering){
        $res = $this->getFromId($reservering);
        if($res["geaccepteerd"] != 0){
            $geaccepteerd = ($res["geaccepteerd"] == 1) ? 2 : 1 ;

            $stmt = $this->Conn->prepare("UPDATE reservering SET geaccepteerd = :geaccepteerd WHERE reservering_id = ".$_GET["id"]);
            $stmt->bindParam(':geaccepteerd',$geaccepteerd);
            $stmt->execute();
        }
        header("location: /reservering/index/".$res["status_id"]);
        //r/eturn $data[0];
    }



    /*
    *  ======| Set archived |==================================
    *  Archiveert een reservering
    */
    public function setArchived($reservering){
        $stmt = $this->Conn->prepare("UPDATE reservering SET status_id = 4 WHERE reservering_id = ".$_GET["id"]);
        $stmt->bindParam(':geaccepteerd',$geaccepteerd);
        $stmt->execute();

        header("location: /reservering/index/4");
    }
}