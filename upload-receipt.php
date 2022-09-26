<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Item.class.php','City.class.php','Customer.class.php','ItemUploadReceipt.class.php'));  
$item = new Item();
$city = new City();
$customer = new Customer();
$itemUploadReceipt = new ItemUploadReceipt();

// cek data customer
$rsCustomer = $customer->getDataRowById(USERKEY);
$errorList = array();
if(empty($rsCustomer)){
    array_push($errorList, $class->lang['loginRequired']);
}else{ 
    $errMsg = array();
    
    if(empty($rsCustomer[0]['idnumber'])) array_push($errMsg, $class->errorMsg['id'][1]);
    if(empty($rsCustomer[0]['address'])) array_push($errMsg, $class->errorMsg['address'][1]);
    if(empty($rsCustomer[0]['mobile'])) array_push($errMsg, $class->errorMsg['mobile'][1]);
    if(empty($rsCustomer[0]['dateofbirth'])) array_push($errMsg, $class->errorMsg['dob'][1]); 
    if(empty($rsCustomer[0]['fbaccount']) && empty($rsCustomer[0]['igaccount'])) array_push($errMsg, $class->errorMsg['medsos'][1]); 
    
    if(count($errMsg) > 0){
        foreach($errMsg as $row) $errMsgList .= '<li>'.$row.'</li>';
        array_push($errorList, 'Kamu harus melengkapi profil kamu terlebih dahulu sebelum berpartisipasi.<br><ul>'.$errMsgList.'</ul>');    
    }else{
        // biar beda pesan errorny
        if(!$itemUploadReceipt->isAgeValid($rsCustomer[0]['dateofbirth']))
            array_push($errorList, $class->errorMsg['dob'][2]);
    }
    
}

$rsItem = $item->searchData('','',true,' and '.$item->tableName.'.statuskey = 1');

$arrCity = $class->convertForCombobox($city->searchData('','',true,' and '.$city->tableName.'.statuskey = 1','order by '.$city->tableName.'.name asc'),'pkey','name');
                            
for($i=0;$i<count($rsItem);$i++){
    
    // pake input text sementara, karena kalo input number selalu muncul default 0
    // nanti validasi manual saja
    
    $rsQtyItem[$i]['itemName'] = $rsItem[$i]['name'];
    $rsQtyItem[$i]['inputHidDetailKey'] = $class->inputHidden('hidDetailKey[]'); // dummy
    $rsQtyItem[$i]['inputQtyItem'] = $class->inputNumber('qty[]',  array('etc' => ''));
    $rsQtyItem[$i]['inputHidItemKey'] = $class->inputHidden('hidItemKey[]', array('value' => $rsItem[$i]['pkey']));
    
}                          

$arrTwigVar ['rsQtyItem'] = $rsQtyItem;
$arrTwigVar ['inputSelCity'] =  $class->inputSelect('selCity', $arrCity); 
$arrTwigVar ['inputStoreName'] =  $class->inputText('storeName', array('etc' => 'placeholder="Nama Toko"')); 
$arrTwigVar ['inputInvoiceNumber'] =  $class->inputText('invoiceNumber',array('etc' => 'placeholder="Nomor Struk"')); 
$arrTwigVar ['inputReceiptDatePlaceholder'] =  $class->inputDate('receiptDate', array( 'etc' => 'placeholder="'.$class->lang['receiptDate'].'"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave','');
$arrTwigVar ['uploadFolder'] = $itemUploadReceipt->uploadFolder;
$arrTwigVar ['inputChkAgreement'] = $class->inputCheckBox('chkAgree');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array('value' => 'upload-receipt')); 
$arrTwigVar ['errorList'] = $errorList;
 
echo $twig->render('upload-receipt.html', $arrTwigVar);
?>