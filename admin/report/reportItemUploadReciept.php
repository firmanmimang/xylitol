<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass('ItemUploadReceipt.class.php');
$itemUploadReciept = createObjAndAddToCol(new ItemUploadReceipt()); 

$customer = createObjAndAddToCol(new Customer());    
$item = createObjAndAddToCol(new Item());    
$city = createObjAndAddToCol(new City());    

include '_global.php';

$obj= $itemUploadReciept;
$securityObject = 'ReceiptValidation'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  

  
$arrFilterInformation = array();  
$_POST['selStatus[]'] = array(2);


	$rsItem = $item->searchDataRow(array($item->tableName.'.pkey', $item->tableName.'.name' ),
                                                   'and '.$item->tableName.'.itemtype = 1',
                                                   'and '.$item->tableName.'.statuskey = 1'
                                                );
    
    $rsItem = array_column($rsItem,null,'pkey');	


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code'); 
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['uploadDate']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
// $arrDataStructure['invoiceNumber'] = array('title'=>ucwords($obj->lang['invoiceNumber']),'dbfield' => 'invoicenumber', 'width'=>"150px");
$arrDataStructure['storeName'] = array('title'=>ucwords($obj->lang['storeName']),'dbfield' => 'storename', 'width'=>"150px");
// $arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['customerName'] = array('title'=>ucwords("Nama Pelanggan"),'dbfield' => 'customername', 'width'=>"150px");
$arrDataStructure['customerAddress'] = array('title'=>ucwords("Alamat"),'dbfield' => 'customeraddress', 'width'=>"150px");
$arrDataStructure['customerAddress'] = array('title'=>ucwords("KTP"),'dbfield' => 'customeridnumber', 'width'=>"150px");
$arrDataStructure['customerPhone'] = array('title'=>ucwords($obj->lang['phone']),'dbfield' => 'customermobile', 'width'=>"150px");
$arrDataStructure['customerEmail'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'customeremail', 'width'=>"150px");
$arrDataStructure['customerIG'] = array('title'=>ucwords("username IG"),'dbfield' => 'customerigaccount', 'width'=>"150px");
$arrDataStructure['cityName'] = array('title'=>ucwords($obj->lang['city']),'dbfield' => 'cityname', 'width'=>"150px");

foreach($rsItem as $row => $value){ 
	$arrDataStructure['item'.$value['pkey']] = array('title'=> $value['name'] ,'dbfield' => $value['name'], 'width'=>"200px",'format'=>'number','calculateTotal' => true, "sortable" => false); 
}

$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['totalPoint']),'dbfield' => 'totalpoint', 'align'=>'right', 'width'=>"110px", 'format'=>'number'); 

$arrDataStructure['itemUploadImage'] = array('title'=>ucwords("File Name"),'dbfield' => 'filename', 'width'=>"150px");

$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['receiptValidationReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => $obj->lang['code'], 'filter' => $_POST['salesCode']));
	}
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
        
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['uploadDate'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['invoiceNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.invoicenumber LIKE ('.$class->oDbCon->paramString('%'.$_POST['invoiceNumber'].'%').')';
	 	array_push($arrFilterInformation,array("label" => $obj->lang['invoiceNumber'], 'filter' =>  $_POST['invoiceNumber']));
	} 
    
    if(isset($_POST) && !empty($_POST['storeName'])) {
		$criteria .= ' AND '.$obj->tableName.'.storename LIKE ('.$class->oDbCon->paramString('%'.$_POST['storeName'].'%').')';
	 	array_push($arrFilterInformation,array("label" => $obj->lang['store'], 'filter' =>  $_POST['storeName']));
	} 
    
    
    if(isset($_POST) && !empty($_POST['selCity'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCity']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.citykey in('.$key.')';  

        $rsCriteria = $city->searchData('','',true, ' and '.$city->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$cityName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['city'], 'filter' => $cityName ));
        
	}
     
     if(isset($_POST) && !empty($_POST['selCustomer'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));   
        
       	$criteria .= ' AND customerkey in('.$key.')';  

        $rsCriteria = $customer->searchData('','',true, ' and '.$customer->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$customerName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['customer'], 'filter' => $customerName ));
        
	}	
    
	
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));
        
	}
		 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

     
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
    $tempreport = '';
	 
    if (empty($rs)) 
			$tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
    $rsDetailCol = $obj->getDetailCollections($rs,'refkey');
    

    $totalRs = count($rs);
    for( $i=0;$i<$totalRs;$i++) {   
        $arrHeaderStyle = array(); 
    
        $rsDetail = (!empty($rsDetailCol[$rs[$i]['pkey']])) ? $rsDetailCol[$rs[$i]['pkey']] : array();
 
        for ($j=0;$j<count($rsDetail);$j++){  
            $rs[$i][$rsItem[$rsDetail[$j]['itemkey']]['name']] += $rsDetail[$j]['qty'] ; 
        }
        // has detail

		/*if($isShowDetail)
        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); */

		// bagian ff, untuk ubah filename ke link image pada phpthumb
		$fileName = $rs[$i]['filename'];
		$thumbHash = getPHPThumbHash($rs[$i]['filename']);
		$rs[$i]['filename'] = "https://".DOMAIN_NAME."/phpthumb/phpThumb.php?src=". $class->phpThumbURLSrc.$obj->uploadFolder.$rs[$i]['pkey'].'/'.$fileName."&far=C&hash=".$thumbHash;
		// -----------

        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }  

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

    
//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'order by name asc'),'pkey','name');
$arrCity = $class->convertForCombobox($city->searchData($city->tableName.'.statuskey',1,true),'pkey','name');   
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrCustomer = $class->convertForCombobox($customer->searchData($customer->tableName.'.statuskey',2,true,'','order by name asc'),'pkey','name');
    

$arrTwigVar['inputHidCityKey'] =  $class->inputHidden('hidCityKey');
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputInvoiceNumber'] =  $class->inputText('invoiceNumber');
$arrTwigVar['inputStoreName'] =  $class->inputText('storeName');
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCity'] =  $class->inputSelect('selCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputShowDetail'] =  $class->inputCheckBox('isShowDetail'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportItemUploadReciept.html', $arrTwigVar);  
 
?>
