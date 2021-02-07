<?php 

require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
	if(isset($_POST['phoneNum']) and isset($_POST['password'])){
		$db = new DbOperations(); 

		if($db->userLogin($_POST['phoneNum'], $_POST['password'])){
			$user = $db->getUserByPhoneNum($_POST['phoneNum']);
			$response['error'] = false; 
                        $response['message'] = "Login Successful";
                        $response['user']=$user;
                        
		}else if($db->getUserByPhoneNum($_POST['phoneNum']) == NULL){
                        $response['error'] = true; 
			$response['message'] = "User is not found";
                }else{
			$response['error'] = true; 
			$response['message'] = "Password wrong!";			
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}
}

echo json_encode($response);



























