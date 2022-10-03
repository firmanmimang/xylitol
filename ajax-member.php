<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
	 
includeClass(array('Customer.class.php', 'LoginLog.class.php'));
$customer = new Customer();
$loginLog = new LoginLog();


	if (isset($_POST) && !empty($_POST['type'])) {
		
		 $isAvailable = true;
		if ( $_POST['type'] == 'check' ){
			switch ($_POST['fieldtype']) {
				case 'email':
					$email = $_POST['email'];
                    $isEdit = (isset($_POST['edit']) && $_POST['edit'] == 1) ? true : false;
                        
                    $userkey = ($isEdit) ? base64_decode($_SESSION[$customer->loginSession]['id']) : '';    
					$rsEmail = $customer->isValueExisted($userkey,'email',$email);
					// Check the email existence ...
					if(count ($rsEmail) <> 0)
						$isAvailable = false;
					break;
		
				case 'email-negation':
					$email = $_POST['email'];
					$rsEmail = $customer->isValueExisted('','email',$email);
					// Check the email existence ...
					if(count ($rsEmail) == 0)
						$isAvailable = false;
						
					break;
		
				case 'username': 
					$userName = $_POST['userName'];
					$rsUserName = $customer->isValueExisted('','username',$userName);
					// Check the username existence ...
					if(count ($rsUserName) <> 0)
						$isAvailable = false;
					break;
					
				case 'checkPassword':  
					$username = $_SESSION[$class->loginSession]['username'];
					$password = $_POST['currentPassword'];
					 
					$isAvailable  = $customer->checkPassword(USERKEY,$username,$password);
				 
					break;
			}
		
			echo json_encode(array(
				'valid' => $isAvailable,
			)); 
			die; 
		} 
		
	}	
	
	 
	if (isset($_POST) && !empty($_POST['action'])) {
		
			foreach ($_POST as $k => $v) { 
				if (!is_array($v))
					 $v = trim($v);  
				
				$arr[$k] = $v;     
			}  
			 
			$arrReturn = array();  
			
			switch ($_POST['action']) {
				case 'add':
								$arr['code'] = 'XXXXX';
								$arr['createdBy'] = 0;
								$arr['selStatus'] = 1;   
								$arr['hidCityKey'] = 0; 
								$arr['address1'] = '';  
								$arr['address2'] = '';  
								$arr['zipCode'] = '';  
								$arr['mobile'] = '';  
								$arr['fax'] = '';  
								$arr['description'] = '';  
								$arr['selTermOfPayment'] = 0; 
								$arr['frontendRegistration'] = 1;  
								$arr['fromFE'] = 1;
                    
                                // kalo ad contact darurat, sementatra masukin aj ke contact person
                                if(isset($arr['emergencyContactName']) && !empty($arr['emergencyContactName']) && isset($arr['emergencyContactPhone']) && !empty($arr['emergencyContactPhone'])){
                                    $arr['hidContactPersonDetailKey'][0] = 0;
                                    $arr['cpPosition'][0] = $class->lang['emergencyContact'];
                                    $arr['cpName'][0] = $arr['emergencyContactName'];
                                    $arr['cpPhone'][0] = $arr['emergencyContactPhone']; 
                                }
                     
								$arrReturn = $customer->addData($arr);

								break;

				case 'edit' :	 
								$username = $_SESSION[$class->loginSession]['username'];
								$password = $_POST['currentPassword'];
								 
								$rsCust = $customer->getDataRowById(USERKEY); 
								$arr['code'] = $rsCust[0]['code'];
								$arr['fromFE'] = 1;
								$arr['chkAgree'] = 1;
								$arr['modifiedBy'] = 0;
								$arr['mnv-OAuth'] = ($rsCust[0]['ssotypekey'] > 0) ? 1 : 0; // kalo mau nilai ssokey, nanti kirim beda variable saja
                                   
                                // kalo ad contact darurat, sementatra masukin aj ke contact person
                                if(isset($arr['emergencyContactName']) && !empty($arr['emergencyContactName']) && isset($arr['emergencyContactPhone']) && !empty($arr['emergencyContactPhone'])){
                                    $arr['hidContactPersonDetailKey'][0] = 0;
                                    $arr['cpPosition'][0] = $class->lang['emergencyContact'];
                                    $arr['cpName'][0] = $arr['emergencyContactName'];
                                    $arr['cpPhone'][0] = $arr['emergencyContactPhone']; 
                                }
                     
								if($arr['mnv-OAuth'] == 1 || $customer->checkPassword(USERKEY,$username,$password))
									$arrReturn = $customer->editData($arr);
								else
									$class->addErrorList($arrReturn,false,$class->errorMsg[302]);
                    
								break; 
								
				case 'login' : 
								$userName=$_POST['loginID'];
								$password=$_POST['loginPassword']; 
								 
								if ($loginLog->isLockout($userName,1)){ 
										$lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
										$errorMsg = $class->errorMsg['login'][3];
										
										$patterns = array();
										$patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 
										
										$replacement = array();
										$replacement[count($replacement)] =$lockoutMinutes; 
										 
										$errorMsg = preg_replace($patterns, $replacement, $errorMsg); 
										
										$class->addErrorList($arrReturn,false,$errorMsg);
								 	    break;
							 	} 
								
								$arrLoginLog = array();
								$arrLoginLog['logintype'] = 1;
                                $arrLoginLog['userkey'] =  ''; 
								$arrLoginLog['username'] = $userName;
								$arrLoginLog['statuskey'] = 2; 
								
									  
								$result = $customer->memberLogin($userName,$password); 
								
								if (count ($result) == 0){
								
									if (isset ($_SESSION[$class->loginSession]))
										session_unset($_SESSION[$class->loginSession]); 
								 
									$class->addErrorList($arrReturn,false,$class->errorMsg[300]);
								 
								}
								
								else if ($result[0]['statuskey'] == 1){
									$class->addErrorList($arrReturn,false,$class->errorMsg['login'][1]);
								}
								else if ($result[0]['statuskey'] == 3){
									$class->addErrorList($arrReturn,false,$class->errorMsg['login'][2]);
								}
								else if ($result[0]['statuskey'] == 2){
									 
										$_SESSION[$class->loginSession]['id'] = base64_encode($result[0]['pkey']);
										$_SESSION[$class->loginSession]['name'] = $result[0]['name']; 
										$_SESSION[$class->loginSession]['username'] = $result[0]['username']; 
										$_SESSION[$class->loginSession]['pass'] = $result[0]['password']; 
										$_SESSION[$class->loginSession]['email'] = $result[0]['email'];  
										 
										$class->addErrorList($arrReturn,true,$class->lang['loginSuccessful']); 
										
                                    
                                        if($class->isActiveModule('SalesOrder')){
                                            includeClass(array('SalesOrder.class.php'));  
                                            $salesOrder = new SalesOrder();
                                            $salesOrder->updateUserCartSession(); 
                                        }
                                            
                                        $arrLoginLog['userkey'] =  $result[0]['pkey']; 
										$arrLoginLog['statuskey'] = 1;  
									 
								}	
								
								$loginLog->addData($arrLoginLog); 
								
								break;
								
				case 'recover-account' :	  
								$arrReturn = $customer->requestRecoverAccount($arr);
								break;
								
				case 'resend-activation' :	 
								$arrReturn = $customer->resendActivationEmail($arr);
								break;
                    
                case 'update-password':
								$arr['hidUserKey'] = USERKEY;
								$arrReturn = $customer->updatePassword($arr);
								break;
			
			    /* case 'checkin': 
                                if(!$security->isMemberLogin(false)){
                                    $userName=$_POST['userId'];
                                    $password=$_POST['userPassword']; 

                                    $arrReturn = array();

                                    $rsCust = $customer->memberLogin($userName,$password);  
                                    if (count ($rsCust) == 0) {
                                        $class->addErrorList($arrReturn,false,$class->errorMsg['checkIn'][1]);
                                        break;
                                    }
                                    
                                    $userkey = $rsCust[0]['pkey'];
                                }else{
                                    $userkey = USERKEY;
                                }
                    
                                $rsMember = $customerMembership->searchData('','',true,'and '.$customerMembership->tableName.'.customerkey ='.$customerMembership->oDbCon->paramString($userkey).' and '.$customerMembership->tableName.'.statuskey = 2', 'order by '.$customerMembership->tableName.'.pkey asc');

                                $arr['code'] = 'xxxxx'; 
                                $arr['hidCustomerKey'] = $userkey;
                                $arr['selCustomerMembership'] = $rsMember[0]['pkey']; 
                                $arr['hidSaveAndProceed'] = 1;
                                $arr['trDate'] = date('d / m / Y H:i');
                    
                                $arr['selStatus'] = 1;

                                $arrReturn = $membershipAttendance->addData($arr);
                    
                                if($arrReturn[0]['valid']){
                                    // reset ulang, karena kalo konfirmasi otomatis ad keluar beberapa messge
                                    $arrReturn = array();
                                    $arrReturn[0]['valid'] = true;
                                    $arrReturn[0]['message'] = $class->lang['checkInSuccessful'];
                                } 
                                 
                               
			                    break;*/
			}; 
			
			echo json_encode($arrReturn);  
			die;  
	}
	
	 
?>