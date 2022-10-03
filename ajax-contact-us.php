<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass("Contact.class.php");
$contact = new Contact();

	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  

    // handling dr footer contact
    if(!empty($arr['hidQuickContact'])) {
        $arr['name'] = $arr['quickContactFrom']; 
        $arr['message'] = $arr['quickContactMessage']; 
        $arr['phone'] = $arr['quickContactPhone']; 
        $arr['email'] = $arr['quickContactEmail']; 
        $arr['subject'] = ''; 
    }
   
	$arrReturn = array(); 
	$arr['code'] = 'XXXXX';
	$arr['createdBy'] = 0;
	$arr['selStatus'] = 1;
	$arrReturn = $contact->addData($arr);
 	  
	echo json_encode($arrReturn);  
	die; 
	
?>