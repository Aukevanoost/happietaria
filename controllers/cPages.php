<?php

namespace controllers;

use models\mPages;

class cPages
{

    public function __construct(){
        $this->model = new mPages();
        $_GET["header"] = true;
    }



    public function home(){
        $_GET["page_title"] = "Homepage";
        $_GET["template"] = "public";
		
        $this->data = array();
    }

    public function onsdoel(){
        $_GET["page_title"] = "Ons doel";
        $_GET["template"] = "public";
        $this->data = array();
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
}