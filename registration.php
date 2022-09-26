<?php
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

$arrTwigVar ['inputPassword'] =  $class->inputPassword('password'); 
$arrTwigVar ['inputPasswordConfirmation'] =  $class->inputPassword('passwordConfirmation'); 
$arrTwigVar ['inputUserName'] =  $class->inputText('userName'); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 


$arrTwigVar['inputUserNamePlaceholder'] = $class->inputText('userName', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPasswordPlaceholder'] = $class->inputPassword('password', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 
$arrTwigVar['inputPasswordConfirmationPlaceholder'] = $class->inputPassword('passwordConfirmation', array( 'etc' => 'placeholder="'.$class->lang['passwordConfirmation'].'"')); 
$arrTwigVar['inputNamePlaceholder'] = $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputPhonePlaceholder'] = $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"'));

$arrTwigVar ['inputChkAgreement'] = $class->inputCheckBox('chkAgree');
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['register']); 


$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
echo $twig->render('registration.html', $arrTwigVar);
?>
