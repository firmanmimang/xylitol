<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Customer.class.php'));
$customer = new Customer();
$city = new City();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

$rs = $customer->getDataRowById(USERKEY);

$_POST['userName'] = $rs[0]['username'];
$_POST['name'] = $rs[0]['name'];  
$_POST['phone'] = $rs[0]['phone']; 
$_POST['mobile'] = $rs[0]['mobile']; 
$_POST['email'] = $rs[0]['email']; 
$_POST['address'] = $rs[0]['address'];
$_POST['zipCode'] = $rs[0]['zipcode']; 
$_POST['fax'] = $rs[0]['fax'];
$_POST['hidCityKey'] = $rs[0]['citykey']; 
$_POST['hidId'] = $rs[0]['pkey']; 

$_POST['sex'] = $rs[0]['sexkey']; 
$_POST['IDNumber'] = $rs[0]['idnumber'];
$_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];
$_POST['dob'] = (!empty($rs[0]['dateofbirth'])) ? $class->formatDBDate($rs[0]['dateofbirth'],'d / m / Y') : '';

$_POST['FBAccount'] = $rs[0]['fbaccount']; 
$_POST['IGAccount'] = $rs[0]['igaccount']; 

$arrSex = $class->convertForCombobox($class->getSex(),'pkey','name');  
$arrCity = $class->convertForCombobox($city->searchData('','',true,' and '.$city->tableName.'.statuskey = 1','order by '.$city->tableName.'.name asc'),'pkey','name');
$rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['citykey'],true);
if (!empty($rsCity[0]['name'])) 
    $_POST['cityName'] = $rsCity[0]['citycategoryname'];
  
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');

$_POST['action'] ='edit';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
$arrTwigVar ['rs'] =  $rs;
$arrTwigVar ['inputCurrentPassword'] =  $class->inputPassword('currentPassword'); 
$arrTwigVar ['inputNewPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName', array('readonly' => true )); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 

//$arrTwigVar ['inputSelPOB'] =  $class->inputSelect('hidPlaceOfBirthKey',$arrCity); 
$autoCompleteCity =  $class->inputAutoComplete(array(  
                                                            'element' => array('value' => 'cityName',
                                                                               'key' => 'hidCityKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) , 
                                                            'explodeScript' => true
    
                                                          )
                                                    );  

$arrTwigVar ['JSScript']  = str_replace(array('<script type="text/javascript">','</script>'),array('',''),$autoCompleteCity['script']); 
 
$arrTwigVar ['inputCity']  = $autoCompleteCity['input']; 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputMobile'] =  $class->inputText('mobile');
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em"')); 
$arrTwigVar ['inputZipcode'] =  $class->inputText('zipCode');
$arrTwigVar ['inputFax'] =  $class->inputText('fax');


$arrTwigVar ['inputCurrentPasswordPlaceholder'] =  $class->inputPassword('currentPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar ['inputNewPasswordPlaceholder'] =  $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['newPassword'].'"')); 
$arrTwigVar ['inputPasswordConfirmationPlaceholder'] =  $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar ['inputUserNamePlaceholder'] =  $class->inputText('userName', array('readonly' => true ), array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar ['inputNamePlaceholder'] =  $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar ['inputIDNumberPlaceholder'] =  $class->inputText('IDNumber', array( 'etc' => 'placeholder="'.$class->lang['IDNumber'].'"')); 
$arrTwigVar ['inputBirthDatePlaceholder'] =  $class->inputDate('dob', array( 'etc' => 'placeholder="'.$class->lang['dateOfBirth'].'"','add-class'=>'label-style')); 
$arrTwigVar ['inputGenderPlaceholder'] =  $class->inputSelect('sex',$arrSex); 
$arrTwigVar ['inputPhonePlaceholder'] =  $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar ['inputMobilePlaceholder'] =  $class->inputText('mobile', array( 'etc' => 'placeholder="'.$class->lang['mobilePhone'].'"')); 
$arrTwigVar ['inputEmailPlaceholder'] =  $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar ['inputAddressPlaceholder'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em" placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputAddressRowPlaceholder'] =  $class->inputText('address', array( 'etc' => 'placeholder="'.$class->lang['address'].'"')); 
$arrTwigVar ['inputZipcodePlaceholder'] =  $class->inputText('zipCode', array( 'etc' => 'placeholder="'.$class->lang['zipCode'].'"')); 
$arrTwigVar ['inputFaxPlaceholder'] =  $class->inputText('fax', array( 'etc' => 'placeholder="'.$class->lang['fax'].'"')); 
$arrTwigVar ['inputFBPlaceholder'] =  $class->inputText('FBAccount', array( 'etc' => 'placeholder="'.$class->lang['fbAccount'].'"', 'add-class'=>'medsos-account')); 
$arrTwigVar ['inputIGPlaceholder'] =  $class->inputText('IGAccount', array( 'etc' => 'placeholder="'.$class->lang['igAccount'].'"', 'add-class'=>'medsos-account')); 

$arrTwigVar ['ssoTypeKey'] =  $rs[0]['ssotypekey']; 

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn');  

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
 
echo $twig->render('profile.html', $arrTwigVar);

?>