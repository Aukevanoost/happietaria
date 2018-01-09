<?php

namespace Core;

use controllers;
use Exception;

class App
{
    private $controller = 'controllers\cPages';
    private $method = 'home';
    private $id = 0;

    public function run()
    {

        if (isset($_GET["controller"])) {
            $this->controller = "controllers\\c" . ucfirst($_GET["controller"]);
        }

        if (isset($_GET["method"])) {
            $this->method = $_GET["method"];
        }

        if(isset($_GET["id"])){
            $this->id = $_GET["id"];
        }

        if(!empty($this->method) && $this->method != ""){
            if(method_exists($this->controller, $this->method)) {
                $this->obj = new $this->controller();
                $method = $this->method;
                $this->obj->$method();
                $this->Data = $this->obj->Data;

                return $this->getView();
            } else {
                //throw new Exception('<div role="alert"><b>Error 404:</b> Method: <u>"'.$this->method.'"</u> not found.</div>');
                header("location: /pages/notfound");

            }
        }else{
            //throw new Exception('<div role="alert"><b>Error 404:</b> Method cannot be empty.</div>');
            header("location: /error/e404");

        }



    }

    public function getView(){
        ob_start();
        $controller = str_replace("controllers\\c", "", $this->controller);
        $ViewFile = './views/'.strtolower(ucfirst($controller)).'/'.$this->method .'.phtml';
        if(file_exists($ViewFile)){
            $file = file_get_contents('./views/'.strtolower($controller).'/'.$this->method .'.phtml');
            try{
                eval('?>'.$file);
            }catch(Exception $ex){
                throw new Exception('<div role="alert"><b>Something went wrong</b>. msg: '.$ex.'</div>');
            }

        }else{
            throw new Exception('<div role="alert"><b>Error 404:</b> File <u>views/'.strtolower($controller).'/'.$this->method .'.phtml</u> not found.</div>');
        }
        return ob_get_clean();
    }
}