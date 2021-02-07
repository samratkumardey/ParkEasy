<?php 

require_once './includes/DbOperations.php';

$response = array(); 
$request = $_SERVER['REQUEST_METHOD'];
	
	switch($request){
            
		case 'GET':
                    break;
		
		case 'PUT':
                    break;
		
                case 'POST':
                    registerUser();
                    break;
		
		case 'DELETE':
                    break;
		
		default :
			$response['error'] = true; 
                        $response['message'] = "Invalid Request";
		break;
		
	}
        
        
        
       
        function registerUser(){
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
			$response['message'] = "It seems you are already registered, please choose a different email and username";						
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}
        echo json_encode($response);
        }










     