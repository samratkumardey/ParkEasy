<?php

 require_once '../includes/DbOperations.php';


$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['fullName']) and isset($_POST['phoneNum']) and isset($_POST['address'])
                    and isset($_POST['vehicleType']) and isset($_POST['vehicleRegNo']) and isset($_POST['password'])){
		$db = new DbOperations(); 
		$result = $db->createUser($_POST['fullName'], $_POST['phoneNum'], $_POST['address'], $_POST['vehicleType'], $_POST['vehicleRegNo'], $_POST['password']);
		if($result == 1){
			$response['error'] = false; 
			$response['message'] = "User registered successfully";
		}elseif($result == 2){
			$response['error'] = true; 
			$response['message'] = "Some error occurred please try again";			
		}elseif($result == 0){
			$response['error'] = true; 
			$response['message'] = "Phone number is already registered";						
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
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
