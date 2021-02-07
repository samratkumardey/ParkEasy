<?php 

require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
	if(  isset($_POST['user_phoneNum']) and isset($_POST['vehicleType']) and isset($_POST['vehicleRegNo'])){
		$db = new DbOperations(); 
           
			if($result = $db->parkStatus_parkedNwhere($_POST['user_phoneNum'], $_POST['vehicleType'], $_POST['vehicleRegNo'])){
                             $response['error'] = false; 
                             $response['message'] = "Your car is Parked";
                             $response['parkStatus'] = $result;
                        } else {
                            $response['error'] = true; 
                             $response['message'] = "Execution Error";
                        }
                         
			
	}else{
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}
}else{
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}

  echo json_encode($response);
  
























