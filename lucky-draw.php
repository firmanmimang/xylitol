<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Voucher.class.php'));  
$voucher = new Voucher();

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ) $pageIndex = $_GET['page']; 
$arrTwigVar ['pageIndex'] =  $pageIndex;

$totalrowsperpage = 48; 
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;


$arrCriteria = array();
$keyword = '';

// if (isset($_GET['keyword']) && !empty($_GET['keyword'])) $_POST['keyword'] = $_GET['keyword'];
if (isset($_GET['keyword'])) $keyword = $_GET['keyword'];
if (isset($_POST['keyword']) && !empty($_POST['keyword'])) $keyword = $_POST['keyword'];

// // echo $limit;
// echo (isset($_GET['keyword']) && !empty($_GET['keyword']));
// echo $_GET['keyword'];
// echo ' br ';
// echo $keyword;
// // print_r($arrCriteria);
// // var_dump($rsVoucher);
// die;


if(!empty($keyword)) {
    array_push($arrCriteria, '('.$voucher->tableName.'.code LIKE '.$class->oDbCon->paramString('%'.$keyword.'%') .' OR '. $voucher->tableCustomer.'.email LIKE '.$class->oDbCon->paramString('%'.$keyword.'%').')') ;
} 

$criteria = implode(' and ', $arrCriteria);  
$criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 

$criteria .= ' and '.$voucher->tableName.'.statuskey in (1) ';

// $criteria .= ' and '.$voucher->tableName.'.typekey in (1) ';

$rsVoucher = $voucher->searchData('','',true,$criteria,$limit);

foreach($rsVoucher as $key=>$row){
    $email = $rsVoucher[$key]['customeremail'];
    
    $mail_part = explode('@', $email);
    $username = $mail_part[0];
    
    $mail_part[0] = substr($username, 0, 4)  .'***';
    
    if(strlen($username) > 6)
     $mail_part[0] .= substr($username, -2);  
    
    $rsVoucher[$key]['customeremail'] =  implode('@', $mail_part);
}
    

$totalPages = ceil( $voucher->getTotalRows($criteria) / $totalrowsperpage); 
$arrTwigVar ['rsVoucher'] = $rsVoucher;
$arrTwigVar ['keyword'] = $keyword;
$arrTwigVar ['inputSearch'] = $class->inputText('keyword', array( 'etc' => 'placeholder="Cari Tiket Kamu..."', 'add-class' => 'txt-search', 'value' => $keyword)); 
$arrTwigVar ['totalPages'] =  $totalPages;   

echo $twig->render('lucky-draw.html', $arrTwigVar); 
?>