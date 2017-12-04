<?php

namespace controllers;

//use models\mPages;

class cReservering
{
    public function __construct(){
        //$this->model = new mReservering();
        $_GET["header"] = true;
    }

    public function index(){
        $_GET["page_title"] = "Reserveringen";
        $_GET["template"] = "private";

        $this->data = array();
    }


}