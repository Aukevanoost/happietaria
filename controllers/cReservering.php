<?php

namespace controllers;

use Core\Controller;
use models\mReservering;

class cReservering extends Controller
{
    public $data;
    private $model;
    protected $protected_pages = array('index','calendar','statussen', 'delstatus', 'beoordelen','sendmail', 'bekijken', 'accept', 'archive','wijzigen');


    public function __construct(){
        parent::__construct();
        $this->model = new mReservering();
    }


    public function nieuw(){
        $_GET["page_title"] = "Nieuwe reservering";
        $_GET["template"] = "public";

        $this->data["message"] = "";
        $this->data["verenigingen"] = $this->model->getCommunities();
        $this->data["shifts"] = $this->model->getShifts();

        if(!empty($_POST)){
            $this->data["message"] = $this->model->newReservation();
        }else{
            $this->data["message"] = "";
        }

    }



    public function index(){
        $_GET["page_title"] = "Reserveringen";
        $_GET["template"] = "private";

        $this->data = array();

        // kleine check
        if($_GET["id"] != "" && !(is_numeric($_GET["id"])) ){
            header("location: /reservering/index/1");
        }

        // get statuses
        $this->data["states"] = $this->model->getCategories("status");

        // get reservations
        $_GET["id"] = ($_GET["id"] == "") ? 1 : $_GET["id"];
        $this->data["reservations"] = $this->model->getReservationsFromState($_GET["id"]);


    }



    public function calendar(){
        $_GET["page_title"] = "Kalender";
        $_GET["template"] = "private";

        $this->data = array();

        // kleine check
        //if($_GET["id"] != "" && !(is_numeric($_GET["id"])) ){
        //    header("location: /reservering/index/1");
        //}

        // Navigation
        if(isset($_POST["navigate"])){
            if($_POST["navigate"] == "addDay"){
                $newdate = date('Y-m-d', strtotime("+1 day",strtotime($_GET["id"]) ));
                header('location: /reservering/calendar/'.$newdate);
            }elseif ($_POST["navigate"] == "remDay"){
                $newdate = date('Y-m-d', strtotime("-1 day",strtotime($_GET["id"]) ));
                header('location: /reservering/calendar/'.$newdate);
            }else{
                header('location: /reservering/calendar/'.$_POST["newDate"]);
            }
        }

        // get statuses

        // get reservations
        $_GET["id"] = ($_GET["id"] == "") ? 1 : $_GET["id"];
        $this->data["reservations"] = $this->model->getReservationsFromDate($_GET["id"]);


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
        $_GET["page_title"] = "Reservering beoordelen";
        //$this->data["status"] = $this->model->getStates();

        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);
    }

    public function sendmail(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "De klant mailen";
        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);
        $this->data["message"] = "";

        if(isset($_POST["JUDGE"])) {
            $this->data["mail"] = $this->model->getJudgeMailData($_POST["JUDGE"], $this->data["reservation"]);
        }elseif(isset($_POST["confirmation"]) && $_POST["confirmation"] == "mailOk"){
            if(isset($_POST["isAccepted"]) && ($_POST["isAccepted"] == 2 || $_POST["isAccepted"] == 1) ){
                $this->data["message"] = $this->model->acceptOrRefuseReservation($this->data["reservation"], $_POST["isAccepted"]);
                $this->data["message"] .= $this->model->sendReservationMail($this->data["reservation"]);
                $this->data["mail"] = array("subject" => $_POST["onderwerp"], "message" => $_POST["inhoud"]);
            }else{
                $this->data["message"] = '<div class="chip">Something went terribly wrong, please try again.<i class="close material-icons">close</i></div>';
            }
        }else {
            //$this->data["message"] = "<div class=\"chip\">Whats this? a bug?.<i class=\"close material-icons\">close</i></div>";
            $this->data["mail"] = $this->model->getMailData($this->data["reservation"]);
        }

    }

    public function bekijken(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "Reservering bekijken";


        //$this->data["status"] = $this->model->getStates();
        //$this->data["shifts"] = $this->model->getShifts();

        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);
    }

    public function wijzigen(){
        $_GET["template"] = "private";
        $_GET["page_title"] = "Reservering wijzigen";


        if(!empty($_POST)){
            $this->data["message"] = $this->model->editReservation();
        }else{
            $this->data["message"] = "";
        }


        $this->data["status"] = $this->model->getStates();
        $this->data["shifts"] = $this->model->getShifts();

        $this->data["reservation"] = $this->model->getFromId($_GET["id"]);
    }

    public function accept(){
        $cat_id = $this->model->switchAccept($_GET["id"]);
       // header("location: /reservering/index/".$cat_id);
    }

    public function archive(){
        $cat_id = $this->model->setArchived($_GET["id"]);
    }
}