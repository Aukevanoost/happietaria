<?php

namespace controllers;

use Core\Controller;
use models\mCrew;

class cCrew extends Controller
{
    public $data;
    private $model;
    protected $protected_pages = array('index','action','bekijken','sendmail');


    public function __construct()
    {
        parent::__construct();
        $this->model = new mCrew();
    }


    public function nieuw()
    {
        $_GET["page_title"] = "Nieuwe reservering";
        $_GET["template"] = "public";

        $this->data["message"] = "";
        $this->data["verenigingen"] = $this->model->getCommunities();
        $this->data["skills"] = $this->model->getSkills();

        if (!empty($_POST)) {
            $this->data["message"] = $this->model->newCrewMember();
        } else {
            $this->data["message"] = "";
        }

    }



    public function index(){
        $_GET["page_title"] = "Inschrijvingen";
        $_GET["template"] = "private";

        $this->data = array();

        // kleine check
        if($_GET["id"] == "" || !(is_numeric($_GET["id"])) ){
            header("location: /crew/index/1");
        }

        // get reservations
        $_GET["id"] = ($_GET["id"] == "") ? 1 : $_GET["id"];
        $this->data["registrations"] = $this->model->getRegistrations($_GET["id"]);
    }

    public function bekijken(){
        $_GET["page_title"] = "Vrijwilliger bekijken";
        $_GET["template"] = "private";

        $this->data["inschrijving"] = $this->model->getFromId($_GET["id"]);
        $this->data["skills"] = $this->model->getChosenSkills($_GET["id"]);
    }


    public function sendmail(){
        $_GET["page_title"] = "Vrijwilliger mailen";
        $_GET["template"] = "private";

        $this->data["inschrijving"] = $this->model->getFromId($_GET["id"]);
        $this->data["skills"] = $this->model->getChosenSkills($_GET["id"]);
        $this->data["message"] = "";


        if(isset($_POST["confirmation"]) && $_POST["confirmation"] == "mailOk"){
            $this->data["message"] = $this->model->sendCreatedMail($this->data["inschrijving"]);
            $this->data["mail"] = array("subject" => $_POST["onderwerp"], "message" => $_POST["inhoud"]);
        }else {
            //$this->data["message"] = "<div class=\"chip\">Whats this? a bug?.<i class=\"close material-icons\">close</i></div>";
            $this->data["mail"] = $this->model->getMailData($this->data["inschrijving"]);
        }
    }


    public function action(){
        $this->model->chgRegistration($_GET["id"]);
    }
}