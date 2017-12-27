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
}
