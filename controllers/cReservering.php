<?php

namespace controllers;

use models\mReservering;

class cReservering
{
    public $data;
    private $model;


    public function __construct(){
        $this->model = new mReservering();
        $_GET["header"] = true;
    }



    public function index(){
        $_GET["page_title"] = "Reserveringen";
        $_GET["template"] = "private";

        $this->data = array();


        // get statuses
        $this->data["states"] = $this->model->getTable("status");

        // get reservations
        $_GET["id"] = ($_GET["id"] == "") ? 1 : $_GET["id"];
        $this->data["reservations"] = $this->model->getReservationsFromState($_GET["id"]);
    }



    public function statussen(){
        $_GET["page_title"] = "Statussen beheren";
        $_GET["template"] = "private";

        $this->data["message"] = (!empty($_POST)) ? $this->model->newStatus() : "";

        //if(!empty($_POST)){
        //	$this->data["message"] = $this->model->newStatus();
        //}else{
        //	$this->data["message"] = "";
        //}

        $this->data["list"] = $this->model->getStates();
    }



    public function delstatus(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "Status verwijderen";

        //if($_SESSION["user"]["role"] == 3){
            $this->model->delStatus();
        //}else{
        //    header("location: /pages/nopermission");
        //}
    }


    public function beoordelen(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "Reservering bekijken";
        $this->data["status"] = $this->model->getStates();

        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);
    }

    public function sendmail(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "Een mail";
        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);

        if(isset($_POST["JUDGE"])) {
            $this->data["mail"] = $this->model->getMailData($_POST["JUDGE"], $this->data["reservation"]);
        }else{
            header('location: /reservering/beoordelen/'.$_GET["id"]);
        }

        if(isset($_POST["confirmation"]) && $_POST["confirmation"] == "mailOk"){
            $this->data["message"] = $this->model->sendReservationMail($this->data["reservation"]);
            $this->data["mail"] = array("subject" => $_POST["onderwerp"], "message" => $_POST["inhoud"]);
        }else{
            $this->data["message"] = "";
        }
    }
}