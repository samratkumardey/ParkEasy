<?php 

require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){
	if(isset($_POST['park_name'])){
		$db = new DbOperations(); 
           
			if($result = $db->phnNcapacityByParkNmNAdd_park_owner($_POST['park_name'])){
                             $response['error'] = false; 
                             $response['message'] = "Fetching Result";
                             $response['result'] = $result;
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
  
























