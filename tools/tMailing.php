<?php

namespace tools;


class tMailing
{

    /*
     * ======| Accept/Refuse mail |=============================================
     * This mail will be send to the customer his/her reservation has been refused/accepted
     */

    public static function sendAcceptRefuseMail($email, $subject, $description)
    {
        $to = $email;

        $message = '
            <html>
                <head>
                    <title>Regarding your reservation</title>
                </head>
                <body>
                    <div style="width: 80%; max-width: 800px; min-width: 360px; margin: 10px auto">
                        <div style="background: #FF9819; width: 100%; height: 250px">
                            <img src="http://happietaria-zwolle.nl/img/happie_logo.png" style="width: 30%; margin: 70px 35%" />
                        </div>
                        <div style="padding: 25px;">
                            <br /><br /><br />
                            '.nl2br($description).'
                        </div> 
                        <br /><br /><Br /><br />
                        <div style="background: #FF9819; width: 100%; height: 70px">

                        </div>
                    </div>

                </body>
            </html>
		';


        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Happietaria Zwolle <info@happietariazwolle.nl>' . "\r\n";

        if(!mail($to, $subject, $message, $headers)){
            echo 'mail failed';
            die();
        }
    }
}