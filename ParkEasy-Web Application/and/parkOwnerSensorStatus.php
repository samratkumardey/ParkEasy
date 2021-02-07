<?php

 require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
 if(isset($_POST['park_name']) ){
		$db = new DbOperations(); 
                        $result = $db->getParkOwnerSensorData($_POST['park_name']);
                        $response['error'] = false; 
                        $response['message'] = "Geeting Sensor Data...";
                        $response['sensorData']=$result;
                }else{
		$response['error'] = true; 
		$response['message'] = "Required field is missing";
	}
    
}else{
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}

echo json_encode($response);