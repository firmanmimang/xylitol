<?php   
/*
if(!in_array($_SERVER['REMOTE_ADDR'], array('36.70.170.15', '36.69.222.108')) ) 
   header('Location: /underconstruction.php');
    */
    
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Item.class.php','Brand.class.php','Partners.class.php','Portfolio.class.php','DiscountScheme.class.php','Gallery.class.php')); 

$item = new Item();
$discountScheme = new DiscountScheme();
$brand = new Brand();
$partners = new Partners();
$portfolio = new Portfolio();
$gallery = new Gallery();

if(!PLAN_TYPE['usefrontend']){  
   header('Location: /admin');
   die;
} 

/* RANDOM PRODUCTS */

$totalRandomProducts = $class->loadSetting('totalRandomProducts'); 

$qohCriteria = '';
$webQOHCriteria = '';

if(!empty($totalRandomProducts)){
    
    if (!IGNORE_QOH){ 
        $qohCriteria = ' having qtyonhand > 0 ';
        $webQOHCriteria = ' and iswebqoh = 1';
    }
    
    $rsRandomItem = $item->searchData('','',true,' and '.$item->tableName.'.statuskey = 1 and '.$item->tableName.'.isvariant = 0','order by rand()',' limit 0,' .$totalRandomProducts, $qohCriteria, $webQOHCriteria );
  
	for($i=0;$i<count($rsRandomItem);$i++){
		$rsItemImage = $item->getItemImage($rsRandomItem[$i]['pkey']);
     	$rsRandomItem[$i]['mainimage'] = $rsItemImage[0]['file'];	
		$rsRandomItem[$i]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);	
	    $rsRandomItem[$i]['linkname'] =  str_replace($class->arrSearch,$class->arrReplace,$rsRandomItem[$i]['name']); 
		 
       
        if (IGNORE_QOH)
            $rsRandomItem[$i]['qtyonhand'] = 99999;
        
	}
     
    $rsRandomItem = $discountScheme->appliedDiscountScheme(USERKEY,$rsRandomItem);
	$arrTwigVar['rsItem'] = $rsRandomItem;
     
    $arrTwigVar ['STRUCTURE_DATA'] = $item->generateStructurData($rsRandomItem);  
}
  
$rsBrand = $brand->searchData($brand->tableName.'.statuskey',1,true,' and  '.$brand->tableName.'.publish = 1');
for($i=0;$i<count($rsBrand);$i++)
    $rsBrand[$i]['phpThumbHash'] = getPHPThumbHash($rsBrand[$i]['file']);

$arrTwigVar['rsBrand'] = $rsBrand; 


$rsPartners = $partners->searchData($partners->tableName.'.statuskey',1,true,' and  '.$partners->tableName.'.isfeatured = 1');
for($i=0;$i<count($rsPartners);$i++)
    $rsPartners[$i]['phpThumbHash'] = getPHPThumbHash($rsPartners[$i]['file']);

$arrTwigVar['rsPartners'] = $rsPartners;  

$rsPortfolio = $portfolio->searchData($portfolio->tableName.'.statuskey',1,true,' order by companyname asc');
for($i=0;$i<count($rsPortfolio);$i++){  
    $rsItemImage = $portfolio->getItemImages($rsPortfolio[$i]['pkey']);
    if(empty($rsItemImage)) continue;
    
    $rsPortfolio[$i]['mainimage'] = $rsItemImage[0]['file'];	
    $rsPortfolio[$i]['phpThumbHash'] = getPHPThumbHash($rsPortfolio[$i]['mainimage']);
}
$arrTwigVar['rsPortfolio'] = $rsPortfolio;

$rsServices = $service->searchData($service->tableName.'.statuskey',1,true);

for($i=0;$i<count($rsServices);$i++){  
    $rsItemImage = $service->getItemImage($rsServices[$i]['pkey']);
    $rsServices[$i]['mainimage'] = $rsItemImage[0]['file'];	
    $rsServices[$i]['phpThumbHash'] = getPHPThumbHash($rsServices[$i]['mainimage']);
    $rsServices[$i]['description'] = $service->getItemDescription($rsServices[$i]['pkey']);	
} 
$arrTwigVar['rsServices'] = $rsServices; 
     

$rsBestSellerItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.isshow = 1', 'order by totalsold desc', 'limit ' . $totalRandomProducts, $qohCriteria, $webQOHCriteria );
foreach($rsBestSellerItem as $key=>$itemRow){ 
    $rsItemImage = $item->getItemImage($itemRow['pkey']);
    $rsBestSellerItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
    $rsBestSellerItem[$key]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);
    $rsBestSellerItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	  
    //$rsBestSellerItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 
    
} 
$arrTwigVar['rsBestSellerItem'] = $rsBestSellerItem;

$rsFeaturedItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.publish = 1');
foreach($rsFeaturedItem as $key=>$itemRow){ 
    $rsItemImage = $item->getItemImage($itemRow['pkey']);
    $rsFeaturedItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
    $rsFeaturedItem[$key]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);
    $rsFeaturedItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	 
    //$rsFeaturedItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 
  
} 
 
$arrTwigVar['rsFeaturedItem'] = $rsFeaturedItem;


/* ===================== NEWS ========================================== */  
$rsLatestNews = $news->searchData($news->tableName.'.statuskey',1,true, '  and publishdate <= now()',' order by '.$news->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
foreach($rsLatestNews as $key=>$newsRow){ 
  $rsLatestNews[$key]['phpThumbHash'] =  getPHPThumbHash($newsRow['file']); 
} 
$arrTwigVar['rsLatestNews'] = $rsLatestNews;


/* ===================== ARTICLES ========================================== */  
$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, ' and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
foreach($rsLatestArticles as $key=>$articlesRow){ 
  $rsLatestArticles[$key]['phpThumbHash'] =  getPHPThumbHash($articlesRow['file']); 
} 
$arrTwigVar['rsLatestArticles'] = $article->updateContentLang($rsLatestArticles);  


/* ===================== ARTICLES ========================================== */  
$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, '  and featured = 1 and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
foreach($rsLatestArticles as $key=>$articlesRow){ 
  $rsLatestArticles[$key]['phpThumbHash'] =  getPHPThumbHash($articlesRow['file']); 
} 
$arrTwigVar['rsLatestFeaturedArticles'] = $article->updateContentLang($rsLatestArticles);    


/* ===================== COURSE ========================================== */  

if($class->isActiveModule('course')){ 
    $rsCourseCategory = $courseCategory->searchData($courseCategory->tableName.'.statuskey',1,true, ' and '.$courseCategory->tableName.'.isshow = 1 and '.$courseCategory->tableName.'.isleaf = 1',' order by '.$courseCategory->tableName.'.orderlist asc, '.$courseCategory->tableName.'.name asc ');
    foreach($rsCourseCategory as $key=>$courseCatgoryRow){ 
      $rsCourse[$key]['phpThumbHash'] =  getPHPThumbHash($courseCatgoryRow['file']); 
    } 
    $arrTwigVar['rsCourseCategories'] = $rsCourseCategory;    
}

/* ===================== Quiz ========================================== */  
if($class->isActiveModule('quiz')){ 
includeClass('Quiz.class.php'); 
$quiz = new Quiz();
$rsQuiz = $quiz->searchData($quiz->tableName.'.statuskey',1,true);
foreach($rsQuiz as $key=>$quizRow){ 
  $rsQuiz[$key]['phpThumbHash'] =  getPHPThumbHash($quizRow['uploadfile']); 
} 
$arrTwigVar['rsQuiz'] = $rsQuiz;    
}

/* ===================== Gallery ========================================== */  
$rsGallery = $gallery->searchData($gallery->tableName.'.statuskey',1,true);
foreach($rsGallery as $key=>$galleryRow){ 
	$rsImage = $gallery->getGalleryImage($galleryRow['pkey']); 
    $rsGallery[$key]['mainimage'] = $rsImage[0]['file'];	
    $rsGallery[$key]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']);
} 
$arrTwigVar['rsGallery'] = $rsGallery;  
$arrTwigVar ['inputHidItemkey'] =  $class->inputHidden('hiditemkey[]');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array('value' => 'addToCart'));
$arrTwigVar ['inputQty'] = $class->inputNumber('orderQty[]',array('value' => '1')); 
$arrTwigVar ['btnAddToCart'] =  $class->inputSubmit('btnSave', $class->lang['addToCart']); 

echo $twig->render('index.html', $arrTwigVar);

?>