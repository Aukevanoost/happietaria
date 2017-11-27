<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
  
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
if(empty($data)){
	echo 'Please send data';
	exit();
}	
 
// set product property values
$lrrid           = $data->DevEUI_uplink->Lrrid;
$lrrlon      	 = $data->DevEUI_uplink->LrrLAT;
$lrrlat      	 = $data->DevEUI_uplink->LrrLON;
$customerId      = $data->DevEUI_uplink->CustomerID;

var_dump( $customerId);

 
// create the LoRa stuff
$result = addRowToDatabase($lrrid, $lrrlon, $lrrlat, $customerId);

if($result){
    echo '{';
        echo '"message": "row was created."';
    echo '}';
}
 
// if unable to create the product, tell the server nobody reads lel
else{
    echo '{';
        echo '"message": "Unable to create LoRa row."';
    echo '}';
}


	// DB connection

	
	function addRowToDatabase( $lrrid, $lrrlon, $lrrlat, $customerId){
		$DB_HOST = "localhost";
		$DB_NAME = "siloprobebv_com_1";
		$DB_USER = "siloprobebv_01";
		$DB_PASS = "hzcR8ksCHP";
        $conn = null;
 
        try{
            $conn = new PDO("mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME, $DB_USER, $DB_PASS);
            $conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
		
		
				// sanitize
		$lrrid  	= htmlspecialchars(strip_tags($lrrid));
		$lrrlon 	= htmlspecialchars(strip_tags($lrrlon));
		$lrrlat 	= htmlspecialchars(strip_tags($lrrlat));
		$customerId = htmlspecialchars(strip_tags($customerId));
		

		// query to insert record
		$query = "INSERT INTO lora VALUES (NULL, :Lrrid, :LrrLAT, :LrrLON, :CustomerID, CURRENT_TIMESTAMP)";

		// prepare query
		$stmt = $conn->prepare($query);

		// bind values
		$stmt->bindParam(":Lrrid", $lrrid);
		$stmt->bindParam(":LrrLAT", $lrrlon);
		$stmt->bindParam(":LrrLON", $lrrlat);
		$stmt->bindParam(":CustomerID", $customerId);
	 
		// execute query
		if($stmt->execute()){
			//echo 'Value = ' . $customerId ;

			return true;
		}else{
			//echo 'Shit failed' ;

			return false;
		}
	}

?>