<?php

namespace controllers;

use Core\Controller;
use models\mCrew;
use Models\mShift;

class cShift extends Controller
{
    public $data;
    private $model;
    protected $protected_pages = array('index', 'action', 'bekijken', 'sendmail');


    public function __construct()
    {
        parent::__construct();
        $this->model = new mShift();
    }


    public function index()
    {
        $_GET["page_title"] = "Shifts beheren";
        $_GET["page_footer"] = true;
        $_GET["template"] = "private";

        $this->data = array();

        // get shifts
        $this->data["shifts"] = $this->model->getShifts();

    }

    public function wijzigen()
    {
        $_GET["page_title"] = "Shifts beheren";
        $_GET["page_footer"] = true;
        $_GET["template"] = "private";


        if(!empty($_POST)){
            $this->data["message"] = $this->model->editShift();
        }else{
            $this->data["message"] = "";
        }

        $this->data["shift"] = $this->model->getFromId($_GET["id"]);
    }
}
