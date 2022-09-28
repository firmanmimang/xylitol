<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false)) {
    $arrReturn = array();
    $arrReturn[0]['valid'] = false;
    $arrReturn[0]['message'] = $class->lang['loginRequired'];; 
    echo json_encode($arrReturn);  
    die;  
}
 
includeClass(array('ItemUploadReceipt.class.php','Warehouse.class.php'));
$itemUploadReceipt = new ItemUploadReceipt();
$warehouse = new Warehouse();

if (isset($_POST) && !empty($_POST['action'])) {

        foreach ($_POST as $k => $v) { 
            if (!is_array($v))
                 $v = trim($v);  

            $arr[$k] = $v;     
        }  

        $arrReturn = array();  

        switch ($_POST['action']) { 
            case 'upload-receipt':
 
                $arr['fromFE'] = '1';
                $arr['code'] = 'xxxxx';
                $arr['hidCustomerKey'] = USERKEY;
                $arr['hidCityKey'] = $_POST['selCity']; 
                $arr['selWarehouseKey'] = $warehouse->getDefaultData(); 
                $arr['trDate'] = date('d / m / Y H:i');
                $arr['selStatus'] = 1;
                $arr['tagKey'] = 0;

                $arrReturn = $itemUploadReceipt->addData($arr);

                // var_dump($arrReturn);
                // die;

                if($arrReturn[0]['valid']){
                    $arrReturn = array();
                    $arrReturn[0]['valid'] = true;
                    $arrReturn[0]['message'] = 'Upload berhasil';
                }

                break;
        } 
 
    
    echo json_encode($arrReturn);  
    die;  
}

	 
?>