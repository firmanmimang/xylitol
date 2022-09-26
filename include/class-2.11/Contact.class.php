<?php

class Contact extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'contact_us';   
		$this->securityObject = 'Contact'; 
		$this->tableStatus = 'master_status';
       
   	    $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['phone'] = array('phone');
        $this->arrData['mobile'] = array('mobile');
        $this->arrData['email'] = array('email');
        $this->arrData['message'] = array('message');
        $this->arrData['categorykey'] = array('message');
        $this->arrData['statuskey'] = array('selStatus');
   }
   
    
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ','.$this->tableStatus.'  
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	function editData($arrParam){    
	    return ''; 
	}  
	
	 function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['name'];  
		$message = $arr['message'];  
		$email = $arr['email'];   
		$phone = $arr['phone'];   
		$subject = $arr['subject'];  
	     
	    $captchaResponse = $arr['g-recaptcha-response'];  
        $secretkey = $this->loadSetting('reCaptchaSecretKey');
    
        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => $secretkey, 'response' => $captchaResponse);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
                )
        );
         
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseKeys = json_decode($response,true);
          
        if(empty($responseKeys) || !$responseKeys["success"]) {
          $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
        }
         
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}  
		if(empty($email)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);
		} else{
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
					$this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]);	
		}
         /*
		if(empty($phone)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]);
		} 
		if(empty($subject)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['subject'][1]);
		}  */
         
		if(empty($message)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['message'][1]);
		}  
		
		return $arrayToJs;
	 }
    
    function afterUpdateData($arrParam, $action){
        $setting = new Setting();
        $companyEmail = $setting->getDetailByCode('companyEmail');
        if (empty($companyEmail))
            return;
        
        $companyEmail = $companyEmail[0]['value'];
        $this->sendMail($arrParam['name'],$arrParam['email'],$this->lang['contactUs'] .' - ' . $this->domain,$arrParam['message'],$companyEmail);
    }
    
 
    function normalizeParameter($arrParam, $trim=false){
        
        $arrParam['selStatus'] = (isset($arrParam['selStatus'])) ? $arrParam['selStatus'] : '1'; 
        $arrParam['phone'] = (isset($arrParam['phone'])) ? $arrParam['phone'] : ''; 
        $arrParam['mobile'] = (isset($arrParam['mobile'])) ? $arrParam['mobile'] : ''; 
        $arrParam['selCategoryKey'] = (isset($arrParam['selCategoryKey'])) ? $arrParam['selCategoryKey'] : 0;
        $arrParam['subject'] = (isset($arrParam['subject'])) ? $arrParam['subject'] : ''; 
            
            
        return $arrParam;
    }}

?>
