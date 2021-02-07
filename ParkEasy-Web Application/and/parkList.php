<?php

require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='GET'){
		$db = new DbOperations(); 
                $parkLists = $db->getParkList();
                $response['error'] = false; 
                $response['message'] = "Getting all Parking Garage's List";
		$response['parkList']=$parkLists;
              		
}else{
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}

echo json_encode($response);