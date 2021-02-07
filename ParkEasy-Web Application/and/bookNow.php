<?php

 require_once '../includes/DbOperations.php';


$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['park_name']) and isset($_POST['user_fullName']) and isset($_POST['user_phoneNum'])
                    and isset($_POST['vehicleType']) and isset($_POST['vehicleRegNo']) ){
		$db = new DbOperations(); 
		$result = $db->bookNow($_POST['park_name'], $_POST['user_fullName'], $_POST['user_phoneNum'], $_POST['vehicleType'], $_POST['vehicleRegNo'] );
		if($result == 0){
			$response['error'] = true; 
			$response['message'] = "Already a request is pending";
		}elseif($result == 1){
			$response['error'] = true; 
			$response['message'] = "Already your car is Parked";			
		}elseif($result == 2){
			$response['error'] = false; 
			$response['message'] = "Requesting for a park :)";						
		}elseif($result == 3){
			$response['error'] = true; 
			$response['message'] = "Execution Error)";						
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
  
