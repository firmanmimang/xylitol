<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('Item.class.php'));  
$item = new Item();

$arrTwigVar['PAGE_ID'] = 'how-to-join';  
$arrTwigVar['title'] = 'How To Join';  
$arrTwigVar['ACTIVE_MENU'] = array('/'.basename($_SERVER['PHP_SELF']));  // default utk file personalized


$rsFeaturedItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.publish = 1');
foreach($rsFeaturedItem as $key=>$itemRow){ 
    $rsItemImage = $item->getItemImage($itemRow['pkey']);
    $rsFeaturedItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
    $rsFeaturedItem[$key]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);
    $rsFeaturedItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	 
    //$rsFeaturedItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 
  
} 
 
$arrTwigVar['rsFeaturedItem'] = $rsFeaturedItem;


echo $twig->render('how-to-join.html', $arrTwigVar);

?>
