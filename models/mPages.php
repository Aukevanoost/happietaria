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

				if($pass == $user["wachtwoord"] && $user["rol"] == 3){
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

    public function editProfile(){

        $data = "";
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
        $user_id = $_SESSION["gebruiker"]["gebruiker_id"];

        try{
            // Inschrijving wijzigen
            $stmt = $this->connection->prepare("UPDATE gebruiker SET email = :email WHERE gebruiker_id = ".$user_id);
            $stmt->bindParam(':email',$email);
            $stmt->execute();

            if(!empty($_POST["wachtwoord1"])){
                if($_POST["wachtwoord1"] == $_POST["wachtwoord2"]){
                    $uppercase = preg_match('@[A-Z]@', $_POST["wachtwoord1"]);
                    $lowercase = preg_match('@[a-z]@', $_POST["wachtwoord1"]);
                    $number    = preg_match('@[0-9]@', $_POST["wachtwoord1"]);

                    if(!$uppercase || !$lowercase || !$number || strlen($_POST["wachtwoord1"]) < 8) {
                        $data = '<div class="chip">Wachtwoord voldoet niet aan de eisen (min 8 karakters, 1 kleine letter, 1 hoofdletter en 1 nummer)<i class="close material-icons">close</i></div>';
                    }else{
                        $salt = tSecurity::randomString();
                        $password = tSecurity::hashPassword($_POST["wachtwoord1"],$salt);

                        // Gebruiker wijzigen
                        $stmt = $this->connection->prepare("UPDATE gebruiker SET wachtwoord = :password, salt = :salt WHERE gebruiker_id = ".$user_id);

                        $stmt->bindParam(':password',$password);
                        $stmt->bindParam(':salt',$salt);

                        $stmt->execute();
                    }
                }else{
                    $data = '<div class="chip">Wachtwoorden zijn niet gelijk<i class="close material-icons">close</i></div>';
                }
            }

            // Confirmation bericht
            if(empty($data)) {
                $data = '<div class="chip">Account is succesvol gewijzigd!<i class="close material-icons">close</i></div>';
                $_SESSION["gebruiker"] = $this->getCurrentAccount();
            }
        }catch(Exception $ex){
            $data = '<div class="chip">Account wijzigen is mislukt<i class="close material-icons">close</i></div>';
        }


        return $data;



    }

    public function getCurrentAccount(){
        $data = array();
        $qry = "SELECT * FROM gebruiker WHERE gebruiker_id = ".$_SESSION["gebruiker"]["gebruiker_id"];

        $stmt = $this->connection->prepare($qry);

        if($stmt->execute()){
            $data = $stmt->fetchAll();
        }
        return $data[0];
    }

    public function getPassword(){
        $password = tSecurity::hashPassword("test","tjH1BSozZb");
        die($password);
    }

}