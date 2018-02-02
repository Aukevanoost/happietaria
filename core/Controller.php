<?php
/**
 * Created by PhpStorm.
 * User: aukev
 * Date: 3-1-2018
 * Time: 11:51
 */

namespace Core;

use Tools\tSecurity;
use LogicException;

abstract class Controller
{
    public $Data;               // de data die naar de view gestuurd wordt
    private $model;              // de model die bij de controller hoort
    protected $protected_pages;    // de pagina's die beschermd moeten worden

    public function __construct(){

        // Default properties
        $_GET["page_footer"] = false;
        $_GET["page_footer"] = true;
        $_GET["template"] = "public";
        $this->Data = array();

        // check protected pages
        if(!isset($this->protected_pages))
            throw new LogicException(get_class($this) . ' needs a "protected pages" array');

        // otherwise the errors are not visible
        $_GET["page_header"] = true;

        // veiligheidscheck
        if(!empty($_GET["method"])){
            tSecurity::checkSignedIn($_GET["method"],$this->protected_pages, $_SESSION);
        }
    }
}