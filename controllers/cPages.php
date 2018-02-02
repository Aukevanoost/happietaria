<?php

namespace controllers;

use Core\Controller;
use models\mPages;

class cPages extends Controller
{
    protected $protected_pages = array('dashboard','uitloggen');
    private $model;
    public $data;



    public function __construct(){
        parent::__construct();
        $this->model = new mPages();
    }
    


    public function home(){
        $_GET["page_title"] = "Homepage";
        $_GET["template"] = "homepage";
		
        $this->data = array();
        $_GET["page_header"] = false;
    }



    public function onsdoel(){
        $_GET["page_title"] = "onsdoel";
        $_GET["template"] = "onsdoel";

        $this->data = array();
        //$this->Data["message"] = "Er is nog geen bericht";
        $_GET["page_header"] = false;
    }



    public function doneren(){
        $_GET["page_title"] = "doneren";
        $_GET["template"] = "doneren";
        $this->data = array();
        $_GET["page_header"] = false;
    }



    public function inloggen(){
        $_GET["page_title"] = "Inloggen";
        $_GET["template"] = "public";
        $_GET["header"] = false;

        if(!empty($_POST)){
            $this->data = $this->model->signIn();
        }else{
            $this->data["message"] = "";
        }

        if(isset($_SESSION['ingelogd']) && !empty($_SESSION['ingelogd'])) {
            header("location: /pages/dashboard");
        }
    }



    public function dashboard(){
        if(!isset($_SESSION['ingelogd']) || empty($_SESSION['ingelogd'])) {
            header("location: /pages/inloggen");
        }

        $_GET["template"] = "private";
        $_GET["page_title"] = "Dashboard";
    }



    public function uitloggen(){
        $_GET["page_title"] = "Sign out";
        $this->data = $this->model->signOut();
    }




    public function contact(){
        $_GET["page_title"] = "Contact";

    }
}