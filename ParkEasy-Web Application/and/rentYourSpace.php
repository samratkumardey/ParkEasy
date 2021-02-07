<?php

 require_once '../includes/DbOperations.php';


$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(isset($_POST['park_name']) and  isset($_POST['owner_name']) and  isset($_POST['phone']) and isset($_POST['capacity']) and isset($_POST['address']) and 
            isset($_POST['password']) and isset($_POST['latitude']) and isset($_POST['longitude']) and isset($_POST['map_location']) ){
		$db = new DbOperations(); 
		$result = $db->rentYourSpaceReg($_POST['park_name'], $_POST['owner_name'], $_POST['phone'], $_POST['capacity'], $_POST['address'],
                        $_POST['password'], $_POST['latitude'], $_POST['longitude'], $_POST['map_location'] );
		if($result == 0){
			$response['error'] = true; 
			$response['message'] = "Park Name is already exist. Try different name.";
		}elseif($result == 1){
			$response['error'] = true; 
			$response['message'] = "Phone Number is already reserved, try different number.";			
		}elseif($result == 2 or $result == 5){
			$response['error'] = false; 
			$response['message'] = "Your Parking Spot is listed :) ";						
		}elseif($result == 3){
			$response['error'] = true; 
			$response['message'] = "Execution Error)";						
		}elseif($result == 4){
			$response['error'] = true; 
			$response['message'] = "Already, You have a Parking Garage)";						
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
  
