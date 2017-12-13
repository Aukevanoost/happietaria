<?php
/* SELECT *, COUNT(user_id) as users FROM `company` c JOIN members m ON m.company_id = c.company_id GROUP BY c.company_id */


namespace Models;


use core\Database;
use PDO;
use tools\tSecurity;

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
			SELECT r.*, s.*, g.*               
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
    public function getTable($table){
        $data = array();
        $stmt = $this->Conn->prepare("SELECT * FROM ".$table);


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
}