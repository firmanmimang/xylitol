<?php
	 
include '../../_config.php';  
include '../../_include-v2.php'; 

includeClass('Customer.class.php');
$customer = createObjAndAddToCol( new Customer()); 
$customerCategory = createObjAndAddToCol( new CustomerCategory()); 

if($class->isActiveModule('ar'))
    $ar = createObjAndAddToCol( new AR()); 

include '_global.php';
$obj= $customer;
$securityObject = 'reportCustomer'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrStatus = $obj->getAllStatus();
$arrCategory = $customerCategory->searchData($customerCategory->tableName.'.statuskey',1);

$arrFilterInformation = array();     
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$_POST['module'] = IMPORT_TEMPLATE['customer'];

switch($EXPORT_TYPE){
     
    default :
            $arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
            $arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code', 'width'=>"100px");
            $arrDataStructure['name'] = array('title'=>ucwords($obj->lang['name']),'dbfield' => 'name', 'width'=>"200px");
            $arrDataStructure['registrationdate'] = array('title'=>ucwords($obj->lang['registrationDate']),'dbfield' => 'createdon', 'width'=>"100px",'format'=>'date','align'=>'center');
            $arrDataStructure['ktp'] = array('title'=>ucwords("KTP"),'dbfield' => 'idnumber', 'width'=>"200px");
            $arrDataStructure['dob'] = array('title'=>ucwords($obj->lang['dateOfBirth']),'dbfield' => 'dateofbirth', 'width'=>"100px",'format'=>'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
            $arrDataStructure['sexname'] = array('title'=>ucwords($obj->lang['sex']),'dbfield' => 'sexname', 'width'=>"100px");
            
            $arrDataStructure['address'] = array('title'=>ucwords($obj->lang['address']),'dbfield' => 'address', 'width'=>"250px", "sortable" => false);
            $arrDataStructure['mobile'] = array('title'=>ucwords($obj->lang['mobilePhone']),'dbfield' => 'mobile', 'width'=>"100px");
            $arrDataStructure['email'] = array('title'=>ucwords($obj->lang['email']),'dbfield' => 'email', 'width'=>"180px");
            $arrDataStructure['ig'] = array('title'=>ucwords("Username IG"),'dbfield' => 'igaccount', 'width'=>"180px");
            $arrDataStructure['point'] = array('title'=>ucwords($obj->lang['point']),'dbfield' => 'point', 'width'=>"80px", 'align' => 'right');
            $arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
}
  
$arrHeaderTemplate = array();  
$arrHeaderTemplate['reportTitle'] = $obj->lang['customerReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure; 
$arrHeaderTemplate['total'] = array();
 
array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['customerCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['customerCode']));
	}
    
	if(isset($_POST) && !empty($_POST['customerName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE  ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' =>  $_POST['customerName']));
	} 
		
	if(isset($_POST) && !empty($_POST['trStartDate'])){
        
		$criteria .= ' and '.$obj->tableName.'.createdon between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59:59'); 
		array_push($arrFilterInformation,array("label" => $obj->lang['registrationDate'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}  
	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
	$rs = $obj->searchData('','',true,$criteria,$order);
     
    $tempreport = ''; 
    
    // ============================= GENERATE DATA ============================= 
 
    for( $i=0;$i<count($rs);$i++) {      
   
        switch($EXPORT_TYPE){
            case 2 : 
                
                break;
            default :

                $arrAddress = array();
                if(!empty($rs[$i]['address']))  array_push($arrAddress,$rs[$i]['address']);
                if(!empty($city))  array_push($arrAddress,$city);   
                $rs[$i]['address'] = implode('<br>',$arrAddress); 
                
                $arrPhone = array();
                if(!empty($rs[$i]['phone']))  array_push($arrPhone,$rs[$i]['phone']);
                if(!empty($rs[$i]['mobile']))  array_push($arrPhone,$rs[$i]['mobile']);
                $rs[$i]['phone'] = implode('<br>',$arrPhone); 
                
                if(empty($rs[$i]['dateofbirth'])){
                    $rs[$i]['sexname'] = ''; 
                }
        }
 
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']); 
        // ===== END FOR EXPORT SECTION
        
        $tempreport .= $return['html']; 
         
        // count subtotal for each col
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]); 
         
    }
		 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrStatus = $class->convertForCombobox($arrStatus,'pkey','status');   
  

$arrTwigVar['importUrl'] = $obj->importUrl; 
$arrTwigVar['inputCustomerCode'] =  $class->inputText('customerCode');  
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName');   
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate',array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate',array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   
      
echo $twig->render('reportCustomer.html', $arrTwigVar);  
 
?>
