<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Customer.class.php');
$customer = createObjAndAddToCol(new Customer());  
$customerCategory = createObjAndAddToCol(new CustomerCategory());
$city = createObjAndAddToCol(new City()); 

$obj= $customer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
  
$formAction = 'customerList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rs = prepareOnLoadData($obj); 
$_POST['dob'] = '01 / 01 / 2000';
$rsContactPerson = array();
$rsShippingAddress = array();
$arrCurrency = array();

if (!empty($_GET['id'])){ 
           
    $id = $_GET['id'];
    $rsContactPerson = $obj->getContactPerson($id);
    $rsShippingAddress = $obj->getMultipleAddress($id,1);
    
	$_POST['name'] = $rs[0]['name']; 
	$_POST['alias'] = $rs[0]['alias']; 
	$_POST['address'] = $rs[0]['address'];  
	$_POST['zipCode'] = $rs[0]['zipcode']; 
	$_POST['phone'] = $rs[0]['phone']; 
	$_POST['mobile'] = $rs[0]['mobile']; 
	$_POST['email'] = $rs[0]['email']; 
	$_POST['fax'] = $rs[0]['fax']; 
	$_POST['taxid'] = $rs[0]['taxid']; 
	$_POST['taxRegistrationName'] = $rs[0]['taxregistrationname']; 
	$_POST['taxRegistrationAddress'] = $rs[0]['taxregistrationaddress']; 
	$_POST['description'] = $rs[0]['description']; 
	$_POST['selCategory'] = $rs[0]['categorykey']; 
	$_POST['hidCityKey'] = $rs[0]['citykey']; 
	$_POST['creditlimit'] = $obj->formatNumber($rs[0]['creditlimit']); 
	$_POST['selTermOfPayment'] = $rs[0]['termofpaymentkey'];  
	$_POST['selBank'] = $rs[0]['companybankkey'];      
	$_POST['userName'] = $rs[0]['username'];  
	$_POST['selCurrencyPreference'] = $rs[0]['currencypreference'];  
    
    $_POST['sex'] = $rs[0]['sexkey']; 
	$_POST['IDNumber'] = $rs[0]['idnumber']; 
    $_POST['dob'] = $obj->formatDBDate($rs[0]['dateofbirth'],'d / m / Y');   
	
    $_POST['point'] = $obj->formatNumber($rs[0]['point']); 
    
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    $_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if (!empty($rs[0]['saleskey'])){
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];
	}
    

    $_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];

    if (!empty($_POST['hidPlaceOfBirthKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['placeofbirth'],true);
		$_POST['placeOfBirth'] = $rsCity[0]['name'] ;
	}   
    
    $_POST['hidARCOAKey'] = $rs[0]['arcoakey'];  
    if (!empty($rs[0]['arcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['arcoakey']); 
		$_POST['ARCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
	}
		
    $_POST['hidDownpaymentCOAKey'] = $rs[0]['downpaymentcoakey'];  
    if (!empty($rs[0]['downpaymentcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['downpaymentcoakey']); 
		$_POST['downpaymentCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
	}
		
	$editCategoryInactiveCriteria = ' or '.$customerCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
  
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCategory = $class->convertForCombobox($customerCategory->searchData('','',true, ' and ('.$customerCategory->tableName.'.statuskey'. $editCategoryInactiveCriteria.')'),'pkey','name');  
$arrSex = $obj->convertForCombobox($obj->getSex(),'pkey','name');  


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
	  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?> 
		 var opt = {};
		 opt.showItemImage = false;  <?php  //echo ($showItemImage) ? 'true' : 'false';?>;
         var customer = new Customer(tabID,opt); 
         prepareHandler(customer);   
         
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },  
                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.customer[1]
                                            }, 
                                        }
                                    },
                                    email: { 
                                        validators: { 
                                            emailAddress: {
                                                message:  phpErrorMsg.email[3]
                                            }
                                        }
                                    },

                                } ; 
        
        
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
	});
			
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?> 
     
       <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col"> 
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php echo  $obj->inputSelect('selStatus', $arrStatus,array('value' => 2)); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>  
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateOfBirth']); ?></label>  
                                 <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('dob'); ?>
                                </div> 
                            </div>    
                           <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputSelect('sex',$arrSex); ?>
                                </div> 
                            </div>  
                           
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputTextArea('address',array('etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('mobile'); ?>
                                </div> 
                            </div>      
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('email'); ?>
                                </div> 
                            </div>  
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo  $obj->inputText('IDNumber'); ?>  
                                </div> 
                           </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['point']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo  $obj->inputNumber('point'); ?>  
                                </div> 
                           </div>  
                             
                            
                        </div> 
                    
                    </div>       
                    <div class="div-table-col">  
                     
                      <div class="div-tab-panel"> 
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['websiteAccount']); ?></div>
                              <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo  $obj->inputText('userName', array('etc' => 'readonly="readonly"')); ?>  
                                    </div> 
                               </div>  
                                        
                       </div>
                         
                    </div>
             </div>
      </div>  
       
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
