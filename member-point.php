<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 

includeClass(array('Customer.class.php'));  
$customer = new Customer();

$arrTwigVar['PAGE_ID'] = 'member-point';  
$arrTwigVar['title'] = 'Jumlah Poin';  
$arrTwigVar['ACTIVE_MENU'] = array('/'.basename($_SERVER['PHP_SELF']));  // default utk file personalized

$rsCust = $customer->getDataRowById(USERKEY);
$arrTwigVar['rsCust'] = $rsCust;

echo $twig->render('member-point.html', $arrTwigVar);

?>
