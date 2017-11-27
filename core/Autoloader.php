<?php
spl_autoload_extensions('.php');
spl_autoload_register('Autoload_class');

function Autoload_class($class)
{
    // echo $class;
    // var_dump(file_exists(str_replace('\\','/',$class.'.php')));
    $controller = str_replace("controllers\\","",$class).".php";
    $file = str_replace('\\','/',$class.'.php');
    if(file_exists($file)){
        require_once(str_replace('\\','/',$class.'.php'));
    }else{
        throw new Exception('<div class="alert alert-danger" role="alert"><b>Error 404:</b> Controller: <u>'.$controller.'</u> not found.');
        exit;
    }
}