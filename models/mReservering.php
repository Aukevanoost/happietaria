<?php
/* SELECT *, COUNT(user_id) as users FROM `company` c JOIN members m ON m.company_id = c.company_id GROUP BY c.company_id */


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
			SELECT r.*, s.*, g.*, s.status_id as section              
			FROM reservering r                
			LEFT JOIN gebruiker g ON g.gebruiker_id = r.gebruiker_id                
			LEFT JOIN status s ON s.status_id = r.status_id                 
			WHERE reservering_id = ".$reservering;

        $stmt = $this->Conn->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }
        return $data[0];
    }






    /*
     * Haalt alle statussen op
     *
     */
    public function getCategories(){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT s.status_id, s.naam, COUNT(r.reservering_id) as items FROM status s LEFT JOIN reservering r ON s.status_id = r.status_id GROUP BY s.status_id");


        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }


        return $data;
    }



    /*
     * Haalt 1 status op
     *
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
     * Haalt alle statussen op
     *
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
     * Haalt alle reserveringen op
     *
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
     * Haalt alle reserveringen op van een bepaalde status
     *
     */

    public function getReservationsFromState($state){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, r.*, s.naam as status FROM `reservering` r LEFT JOIN gebruiker g ON r.gebruiker_id = g.gebruiker_id LEFT JOIN status s ON s.status_id = r.status_id WHERE r.status_id = ".$state);


        if($stmt->execute()){
            while($company = $stmt->fetch()){
                array_push($data, $company);
            }
        }


        return $data;
    }



    /*
     * Haalt alle reserveringen op van een bepaalde status
     *
     */

    public function getReservationsFromDate($date){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT g.*, r.*, s.naam as status FROM `reservering` r LEFT JOIN gebruiker g ON r.gebruiker_id = g.gebruiker_id LEFT JOIN status s ON s.status_id = r.status_id WHERE geaccepteerd = 1 AND reservering BETWEEN '".$date." 00:00:00' AND '".$date." 23:59:59' ORDER BY reservering ");


        if($stmt->execute()){
            while($company = $stmt->fetch()){
                array_push($data, $company);
            }
        }


        return $data;
    }



    /*
     * Voegt een nieuwe status toe aan de database
     *
     */
    public function newStatus(){
        // basic garbage removal
        $status = filter_var($_POST["status"],FILTER_SANITIZE_STRING);

        //if( ($_SESSION['user']['role'] == 3)){
            $stmt = $this->Conn->prepare("INSERT INTO status VALUES (NULL, :status)");

            $stmt->bindParam(':status',$status);

            if($stmt->execute()){
                $data = '<div class="chip">New status has succesfully been added<i class="close material-icons">close</i></div>';
            }else{
                $data = '<div class="chip">Creating status failed<i class="close material-icons">close</i></div>';
            }
        //}else{
        //    $data = '<div class="alert alert-danger" role="alert"><b>Error 403</b>, You are not authorised for this action.</div>';
        //}
        return $data;
    }


    /*
     * Verwijdert een status
     *
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




    public function getMailData($j,$r){
        $data = array();


        // Mail voorbereiden
        $accept = ($j == 'ACCEPT') ? " geaccepteerd en verwerkt hebben! we zien u graag op het betreffende tijdstip." : "helaas op dat moment geen plek meer over hebben. We raden u aan om een ander tijdstip te proberen." ;

        $r["reservering"] = new DateTime($r["reservering"]);
        $datetime = $r["reservering"]->format('d M \o\m H:i');

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



    public function sendReservationMail($reservation){
        $onderwerp = filter_var($_POST["onderwerp"],FILTER_SANITIZE_STRING);
        $inhoud = filter_var($_POST["inhoud"],FILTER_SANITIZE_STRING);


        tMailing::sendAcceptRefuseMail($reservation["email"], $onderwerp, $inhoud);


        return '<div class="chip">Mail is verstuurd<i class="close material-icons">close</i></div>';
        //<div class="chip">New status has succesfully been added<i class="close material-icons">close</i></div>
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
        $user_id = preg_replace('/[^0-9]/',"",$_POST["user_id"]);
        $pers = preg_replace('/[^0-9]/',"",$_POST["personen"]);
        $datetime = DateTime::createFromFormat('Y-m-d H:i', $_POST["date"].' '.$_POST["time"]);
        $datetime = $datetime->format('Y-m-d H:i:s');


        try{
            // Inschrijving wijzigen
            $stmt = $this->Conn->prepare("UPDATE reservering SET reservering = :reservering, personen = :personen, status_id = :status_id WHERE reservering_id = ".$_GET["id"]);

            $stmt->bindParam(':status_id',$status);
            $stmt->bindParam(':personen',$pers);
            $stmt->bindParam(':reservering',$datetime);

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
        }catch(Exception $ex){
            $data = '<div class="chip">Tijdens het wijzigen van de reservering ging er iets mis<i class="close material-icons">close</i></div>';
        }


        return $data;
    }
}