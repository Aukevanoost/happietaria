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
		$stmt = $this->connection->prepare("SELECT *, u.active FROM members u JOIN company c ON c.company_id = u.company_id WHERE username = '".$user."'");


        if ($stmt->execute()) {
            $count = $stmt->rowCount();
            if($count >= 1){
                $user = $stmt->fetchAll();
                $user = $user[0];
				
				$pass = $this->hashPassword($_POST['password'], $user["salt"]);

				if($pass == $user["password"]){
					if($user["active"] == 1){
						if($user["blocked"] == 0){
							$_SESSION["ingelogd"] = true;
							$_SESSION["user"] = $user;
						}else{
							$data["message"] = '<div class="msg_error">You have been blocked from the server</div>';
						}
					}else{
						$data["message"] = '<div class="msg_error">Username and/or password incorrect</div>';
					}
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
        header("location: /pages/signin");

    }

}