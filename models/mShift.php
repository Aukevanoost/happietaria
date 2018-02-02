<?php

namespace Models;

use core\Database;
use PDO;
use tools\tSecurity;

class mShift
{
    public $Conn;

    public function __construct()
    {
        $conn = new Database();
        $this->Conn = $conn->connectToDb();
    }

    public function getShifts()
    {
        $data = array();
        $stmt = $this->Conn->prepare("SELECT *  FROM shift");

        if($stmt->execute()){
            while($row = $stmt->fetch()){
                array_push($data, $row);
            }
        }
        return $data;
    }

    public function getFromId($shift_id)
    {
        $data = array();
        $qry = "SELECT * FROM shift WHERE shift_id = ". $shift_id;

        $stmt = $this->Conn->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }
        return $data[0];
    }

    public function editShift() {
        $opmerking = filter_var($_POST["opmerking"],FILTER_SANITIZE_STRING);
        $begin_tijd = preg_replace('/[^:0-9]/',"",$_POST["begin_tijd"]);
        $eind_tijd = preg_replace('/[^:0-9]/',"",$_POST["eind_tijd"]);

        try{
            // shift wijzigen
            $stmt = $this->Conn->prepare("UPDATE shift SET opmerking = :opmerking, begin_tijd = :begin_tijd, eind_tijd = :eind_tijd WHERE shift_id = ".$_GET["id"]);

            $stmt->bindParam(':opmerking',$opmerking);
            $stmt->bindParam(':begin_tijd',$begin_tijd);
            $stmt->bindParam(':eind_tijd',$eind_tijd);

            $stmt->execute();

            // Confirmation bericht
            $data = '<div class="chip">Shift is succesvol gewijzigd!<i class="close material-icons">close</i></div>';
            header("location: /shift/index");
        }catch(Exception $ex){
            $data = '<div class="chip">Tijdens het wijzigen van de shift ging er iets mis<i class="close material-icons">close</i></div>';
        }

        return $data;
    }

}