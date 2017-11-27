<?php
/**
 * Created by PhpStorm.
 * User: Auke van Oostenbrugge
 * Date: 4-7-2017
 * Time: 10:59
 */

    session_start();

    include_once('./core/App.php');
    include_once('./core/Autoloader.php');
    include_once('./core/Database.php');

    $app = new Core\App();
    try{
        $content = $app->run();
    }catch(Exception $ex){
        $content = '<p>'.$ex->getMessage().'</p>';
    }

    require_once('template/layout.phtml');

?>