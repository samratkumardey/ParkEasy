<?php 

	class DbOperations{

		private $con; 
                // int $i;

		function __construct(){

			require_once dirname(__FILE__).'/DbConnect.php';

			$db = new DbConnect();

			$this->con = $db->connect();

		}

		/*CRUD -> C -> CREATE */

		public function createUser($fullName, $phoneNum, $address, $vehicleType, $vehicleRegNo, $pass){
			if($this->isUserExist($phoneNum)){
				return 0; 
			}else{
				$password = md5($pass);
				$stmt = $this->con->prepare("INSERT INTO `users` (`id`, `fullName`, `phoneNum`, `address`, `vehicleType`, `vehicleRegNo`, `password`) VALUES (NULL, ?, ?, ?, ?, ?, ?);");
				$stmt->bind_param("ssssss", $fullName, $phoneNum, $address, $vehicleType, $vehicleRegNo, $password);

				if($stmt->execute()){
					return 1; 
				}else{
					return 2; 
				}
			}
		}

		public function userLogin($phoneNum, $pass){
			$password = md5($pass);
			$stmt = $this->con->prepare("SELECT id FROM users WHERE phoneNum = ? AND password = ?");
			$stmt->bind_param("ss", $phoneNum, $password);
			$stmt->execute();
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
		}

		public function getUserByPhoneNum($phnNum){
			$stmt = $this->con->prepare("SELECT id, fullName, phoneNum, address, vehicleType, vehicleRegNo FROM users WHERE phoneNum = ?");
			$stmt->bind_param("s",$phnNum);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
		

		private function isUserExist($phoneNum){
			$stmt = $this->con->prepare("SELECT id FROM users WHERE phoneNum = ?");
			$stmt->bind_param("s", $phoneNum);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
		}
                
                
                public function getParkList(){
                     $stmt = $this->con->prepare("SELECT park_name,phone,address,capacity,latitude,longitude,map_location FROM park_owner_list ORDER BY park_name");
                        $stmt->execute(); 
                        $stmt->bind_result( $park_name, $phone, $address, $capacity, $latitude, $longitude, $map_location);
                        $parkLists = array(); 
                        while($stmt->fetch()){ 
                            $parkList = array(); 
                            $parkList['park_name']=$park_name; 
                            $parkList['phone'] = $phone;
                            $parkList['address'] = $address;
                            $parkList['capacity'] = $capacity; 
                            $parkList['latitude'] = $latitude; 
                            $parkList['longitude'] = $longitude; 
                            $parkList['map_location'] = $map_location; 
                            array_push($parkLists, $parkList);
                            }             
                        return $parkLists;
                }
                
                public function getParkOwnerSensorData($park_name){
                            $stmt = $this->con->prepare("SELECT `".$park_name."_sensor_data`. * FROM `".$park_name."_sensor_data` where date = CURRENT_DATE");
                            $stmt->execute();
                            return $stmt->get_result()->fetch_assoc();
                }
                
                
               
                public function bookNow($park_name, $user_fullName, $user_phoneNum, $vehicleType, $vehicleRegNo){
                    if($this->isVehiclePending( $park_name, $user_phoneNum, $vehicleType, $vehicleRegNo )){
				return 0; 
			}elseif ($this->isVehicleParked( $park_name, $user_phoneNum, $vehicleType, $vehicleRegNo )) {
                            return 1;
                        }else {
                            $stmt = $this->con->prepare("INSERT INTO `book_notification` (`id`, `park_name`, `user_fullName`, `user_phoneNum`, `date`, `time`, `status`, `approval_status`, `notification_type`, `vehicleType`, `vehicleRegNo`, `see_notification`, `position`, `sensor_status`) VALUES (NULL, ?, ?, ?, CURRENT_DATE, CURRENT_TIME, '1', 'pending', 'New', ?, ?, 'not_seen', '-', '-')");
				$stmt->bind_param("sssss", $park_name, $user_fullName, $user_phoneNum, $vehicleType, $vehicleRegNo);
				if($stmt->execute()){
					return 2; 
				}else{
					return 3; 
				}
                        }
            
        }
                    	
                     
                        private function isVehiclePending($park_name, $user_phoneNum, $vehicleType, $vehicleRegNo){
                         $stmt = $this->con->prepare("SELECT id FROM book_notification WHERE park_name = ? and user_phoneNum = ? and date = CURRENT_DATE and approval_status = 'pending' and vehicleType = ? and vehicleRegNo = ?");
			$stmt->bind_param("ssss", $park_name, $user_phoneNum, $vehicleType, $vehicleRegNo);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
                }
 
                
                
                private function isVehicleParked($park_name, $user_phoneNum, $vehicleType, $vehicleRegNo){
                         $stmt = $this->con->prepare("SELECT id FROM book_notification WHERE park_name = ? and user_phoneNum = ? and approval_status = 'parked' and vehicleType = ? and vehicleRegNo = ? ");
			$stmt->bind_param("ssss", $park_name, $user_phoneNum, $vehicleType, $vehicleRegNo);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
                }
                
                public function rentYourSpaceReg($parkNm, $onerName, $phn, $cap, $add, $pass, $lat, $lang, $mapLoc){
                     if($this->getParkWonerByParkName($parkNm)){
				return 0; 
			}elseif ($this->getParkWonerByPhone($phn)) {
                            return 1;
                        }else if($this->getParkWonerByName($onerName)){
                            return 4;
                        }else {
                            $pssword = md5($pass);
                            $stmt = $this->con->prepare("INSERT INTO `park_owner_list` (`id`, `park_name`, `owner_name`, `phone`, `address`, `capacity`, `latitude`, `longitude`, `map_location`, `password`, `created_at`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
				$stmt->bind_param("sssssssss", $parkNm, $onerName, $phn, $add, $cap, $lat, $lang, $mapLoc, $pssword);
				if($stmt->execute()){
                                    if($this->creatTable_RentYourSpace($parkNm, $cap)){
                                    return 5;
                                }
					return 2; 
				}else{
					return 3; 
				}
                        }
                }
                
                private function getParkWonerByParkName($parkNm){
                        $stmt = $this->con->prepare("SELECT id FROM park_owner_list WHERE park_name = ?  ");
			$stmt->bind_param("s", $parkNm);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
                }
                
                private function getParkWonerByPhone($phn){
                        $stmt = $this->con->prepare("SELECT id FROM park_owner_list WHERE phone = ?  ");
			$stmt->bind_param("s", $phn);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
                }
                
                private function getParkWonerByName($onerName){
                        $stmt = $this->con->prepare("SELECT id FROM park_owner_list WHERE owner_name = ?  ");
			$stmt->bind_param("s", $onerName);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
                }
                
                
                private function creatTable_RentYourSpace($table_name, $col){
                    if($this->ifTableExist($table_name)){
                        $sql = "CREATE TABLE `".$table_name."_sensor_data` (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, available VARCHAR(50) NULL, date DATE)";
                            if( mysqli_query($this->con, $sql) ){
                                if($this->alterTableAddColumn($table_name, $col)){
                                    return ;
                                }
                                    return ;
                                } else{
                                    return ;
                                }
				return ; 
			} 
                }
                   
                private function ifTableExist($table_name){
                    $stmt = "DROP TABLE IF EXISTS `".$table_name."_sensor_data` ";
                    return mysqli_query($this->con, $stmt) ;
                         
                }
                
                private function alterTableAddColumn($table_name, $col){
                    $i;
                    $data = array(); //not need,forget to erase
                    for( $i=1; $i<=$col; $i++){
                            if($i>1){
                                $m = $i-1;
                              $stmt = $this->con->prepare( "ALTER TABLE `".$table_name."_sensor_data` ADD `p".$i."` VARCHAR(50) NULL AFTER `p".$m."` ");
                                $stmt->execute();  
                            } else {
                                 $stmt = $this->con->prepare( "ALTER TABLE `".$table_name."_sensor_data` ADD `p".$i."` VARCHAR(50) NULL AFTER `id` ");
                                 $stmt->execute();
                            }
                    }
                    return;
                }
                
                
                public function phnNcapacityByParkNmNAdd_park_owner($park_name){
                        $stmt = $this->con->prepare("SELECT phone, capacity FROM `park_owner_list` WHERE park_name = ? ");
			$stmt->bind_param("s", $park_name);
			$stmt->execute();
                        return $stmt->get_result()->fetch_assoc();
                }
                
                public function parkStatus_parkedNwhere(  $user_phoneNum, $vehicleType, $vehicleRegNo){
                     $stmt = $this->con->prepare("SELECT `approval_status`, `park_name`, `date`, `time` FROM `book_notification` WHERE `user_phoneNum`=? and `approval_status`='parked'  and `vehicleType`=? and `vehicleRegNo`=? ");
			$stmt->bind_param("sss", $user_phoneNum, $vehicleType, $vehicleRegNo);
			$stmt->execute();
                        return $stmt->get_result()->fetch_assoc();
                }
                    
                    

}