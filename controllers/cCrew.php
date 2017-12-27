<?php

namespace controllers;

use models\mCrew;

class cCrew
{
    public $data;
    private $model;


    public function __construct()
    {
        $this->model = new mCrew();
        $_GET["header"] = true;
    }


    public function nieuw()
    {
        $_GET["page_title"] = "Nieuwe reservering";
        $_GET["template"] = "public";

        $this->data["message"] = "";
        $this->data["verenigingen"] = $this->model->getCommunities();
        $this->data["shifts"] = $this->model->getShifts();

        if (!empty($_POST)) {
            $this->data["message"] = $this->model->newCrewMember();
        } else {
            $this->data["message"] = "";
        }

    }
}