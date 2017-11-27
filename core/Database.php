<?php
/**
 * Created by PhpStorm.
 * User: Auke van Oostenbrugge
 * Date: 4-7-2017
 * Time: 11:14
 */


namespace core;

use PDO;
use PDOException;

class Database
{
    /* Externe database
     *
     *     private $DB_HOST = "localhost";
     *     private $DB_NAME = "siloprobebv_com_1";
     *     private $DB_USER = "siloprobebv_01";
     *     private $DB_PASS = "hzcR8ksCHP";
     *
     */


    private $DB_HOST = "localhost";
    private $DB_NAME = "happietaria";
    private $DB_USER = "root";
    private $DB_PASS = "";



    public function connectToDb()
    {

        try {
            $conn = new PDO("mysql:host=".$this->DB_HOST.";dbname=".$this->DB_NAME, $this->DB_USER, $this->DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        }catch (PDOException $e) {
            echo "The Connection failed... ". $e->getMessage();
            return false;
        }


    }
}