<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 

includeClass(array('ItemUploadReceipt.class.php'));  
$itemUploadReceipt = new ItemUploadReceipt();

$rsItemReciept = $itemUploadReceipt->searchData($itemUploadReceipt->tableName.'.customerkey ',USERKEY,true, 'order by '.$itemUploadReceipt->tableName.'.trdate desc');
for($i=0;$i<count($rsItemReciept);$i++){ 
    if($rsItemReciept[$i]['statuskey'] == 3){ 
        $rsItemReciept[$i]['statusname'] = $rsItemReciept[$i]['statusname'].'<br>'.$rsItemReciept[$i]['cancelreason'];  
        $rsItemReciept[$i]['classstatus'] = 'cancel-status';
    }
}
 
$arrTwigVar ['rsItemReciept'] =  $rsItemReciept;

echo $twig->render('upload-history.html', $arrTwigVar); 
?>