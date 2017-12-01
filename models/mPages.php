<?php
/**
 * Created by PhpStorm.
 * User: aukev
 * Date: 28-2-2017
 * Time: 15:10
 */

namespace Models;

use core\Database;
use PDO;
use tools\tSecurity;

class mPages
{
    public function __construct()
    {
        $conn = new Database();
        $this->connection = $conn->connectToDb();
    }



    public function signIn()
    {
        $data = array();
        $data["message"] = "";


        $user = filter_var ( $_POST['username'], FILTER_SANITIZE_STRING);
		$stmt = $this->connection->prepare("SELECT * FROM gebruiker WHERE email = '".$user."'");


        if ($stmt->execute()) {
            $count = $stmt->rowCount();
            if($count >= 1){
                $user = $stmt->fetchAll();
                //var_dump($user);
                $user = $user[0];
				
				$pass = tSecurity::hashPassword($_POST['password'], $user["salt"]);

				if($pass == $user["wachtwoord"]){
                    $_SESSION["ingelogd"] = true;
                    $_SESSION["gebruiker"] = $user;


				}else{
					$data["message"] = '<div class="msg_error">Username and/or password incorrect</div>';
				}
            }else{
                $data["message"] = '<div class="msg_error">Username and/or password incorrect</div>';
            }

        }

        return $data;
    }



    public function signOut(){
        session_destroy();
        header("location: /pages/inloggen");

    }

}