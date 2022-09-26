<?php
  
class DiscountScheme extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'discount_scheme_header'; 
		$this->tableCustomer = 'customer'; 
       	$this->tableCustomerCategory = 'customer_category'; 	
       	$this->tableItemCategory = 'item_category'; 	
		$this->tableStatus = 'master_status'; 
		 
		//$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment);
       
		$this->securityObject = 'DiscountScheme'; 
	  
   }
    
  function getQuery(){
	   
	   return '
			SELECT 
                '.$this->tableName.'.* ,
                IF(discounttype = 1,"IDR","%") as discounttypelabel,
                IF('.$this->tableCustomerCategory.'.name is null,"Semua User", '.$this->tableCustomerCategory.'.name) as customercategoryname , 
                IF('.$this->tableItemCategory.'.name is null,"Semua Item", '.$this->tableItemCategory.'.name) as itemcategoryname , 
			   '.$this->tableStatus.'.status as statusname 
			FROM 
               '.$this->tableName.' 
                    left join '.$this->tableCustomerCategory.'
                        on '.$this->tableName.'.customercategorykey = '.$this->tableCustomerCategory.'.pkey
                    left join  '.$this->tableItemCategory.'
                        on '.$this->tableName.'.itemcategorykey = '.$this->tableItemCategory.'.pkey
                    , '.$this->tableStatus.'
			WHERE  
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
                
 		' .$this->criteria ;  
		 
    }
   
   function addData($arrParam){
	  
		$arrayToJs = array(); 
		try{						
		
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $code = $this->getNewCustomCode($arrParam);	 
                $arrParam['code'] = (is_array($code)) ? $code[0] : $code;

                $arrayToJs = $this->validateForm($arrParam);
                if (!empty($arrayToJs)) 
                        return $arrayToJs; 
            
				$pkey = $this->getNextKey($this->tableName);  
            
				$sql = '
						INSERT INTO		
						 '.$this->tableName .' (
                            pkey, 
							code, 
                            trdatestart,
                            trdateend,
                            customerdisctype,
                            customercategorykey,
                            itemdisctype,
                            itemcategorykey,
                            discounttype,
                            discountvalue, 
							trdesc, 
							statuskey,
                            orderlist,
							createdby,
							createdon 
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).', 
							'.$this->oDbCon->paramDate($arrParam['trDateStart'],' / ').', 
							'.$this->oDbCon->paramDate($arrParam['trDateEnd'],' / ').', 
							0, 
                            '.$this->oDbCon->paramString($arrParam['selUserTypeKey']).',
                            0,
                            '.$this->oDbCon->paramString($arrParam['selItemTypeKey']).',
                            '.$this->oDbCon->paramString($arrParam['selDiscountType']).',
                            '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['discount'])).', 
                            '.$this->oDbCon->paramString($arrParam['trDesc']).', 
                            '.$this->oDbCon->paramString($arrParam['selStatus']).', 
                            '.$this->oDbCon->paramString($arrParam['orderList']).', 
							'.$this->oDbCon->paramString($arrParam['createdBy']).', 
							now() 
						)
				';
			 
				$this->oDbCon->execute($sql);       
                $this->setTransactionLog(INSERT_DATA,$pkey);
             
				$this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			
	}
    
	
        
    function editData($arrParam){
		
		$arrayToJs = array(); 
		
		try{  
						 
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
            
                $code = $this->getNewCustomCode($arrParam);	 
                $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
				$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
				if (!empty($arrayToJs)) 
						return $arrayToJs;  

               $sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							code = '.$this->oDbCon->paramString($arrParam['code']).', 
							trdatestart = '.$this->oDbCon->paramDate($arrParam['trDateStart'],' / ').', 
							trdateend = '.$this->oDbCon->paramDate($arrParam['trDateEnd'],' / ').', 
							customerdisctype = 0, 
                            customercategorykey = '.$this->oDbCon->paramString($arrParam['selUserTypeKey']).',
                            itemdisctype = 0,
                            itemcategorykey = '.$this->oDbCon->paramString($arrParam['selItemTypeKey']).',
                            discounttype = '.$this->oDbCon->paramString($arrParam['selDiscountType']).',
                            discountvalue = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['discount'])).', 
                            trdesc = '.$this->oDbCon->paramString($arrParam['trDesc']).', 
                            statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
                            orderlist = '.$this->oDbCon->paramString($arrParam['orderList']).', 
							modifiedby = '.$this->oDbCon->paramString($arrParam['createdBy']).', 
							modifiedon = now() 
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
				';
             					   
				$this->oDbCon->execute($sql);
							
				$this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			 

	} 
	
      
     function validateForm($arr,$pkey = ''){
		$item = new Item();  
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
 		  
		return $arrayToJs;
	 }
    
    function appliedDiscountScheme($userkey = '',$rsItem){
        
        $itemCategory= new ItemCategory();
        
        for($i=0;$i<count($rsItem);$i++) 
            $rsItem[$i]['oldsellingprice'] = $rsItem[$i]['sellingprice']; 

         
        // cek item masuk dlm category diskon gk
        $userCatKey = array();
        array_push($userCatKey ,'0');
        
        if (!empty($userkey)){
            $customer = new Customer();
            $rsCust = $customer->getDataRowById($userkey);
            if (!empty($rsCust[0]['categorykey'])) 
                array_push($userCatKey ,$rsCust[0]['categorykey']);
        }
     
        
        $rsDiscScheme = $this->searchData($this->tableName.'.statuskey',1,true,' and  customercategorykey in ('.implode(',',$userCatKey).') and date(now()) >= trdatestart and date(now()) <= trdateend ', ' order by orderlist  asc, pkey desc ');
        for($j=0;$j<count($rsDiscScheme);$j++){
            
            //$this->setLog ($rsDiscScheme[$j]['code']); 
            
            // ambil kategori dan semua kategori dibawahnya yg kena diskon
            $arrDiscCategory = array(); 
            
            if ($rsDiscScheme[$j]['itemcategorykey'] != 0 ){ 
                array_push($arrDiscCategory,$rsDiscScheme[$j]['itemcategorykey']); 
                $arrChildCategory = $itemCategory->getChildren($rsDiscScheme[$j]['itemcategorykey']);
                for($k=0;$k<count($arrChildCategory);$k++)
                    array_push($arrDiscCategory,$arrChildCategory[$k]); 
            }
                
            $rsDiscScheme[$j]['arrDiscCategory'] = $arrDiscCategory;
                
            for($i=0;$i<count($rsItem);$i++) { 
                $rsItem[$i] = $this->appliedDiscount($rsItem[$i],$rsDiscScheme[$j]);
            }
            
            //break;
        }
        
        return $rsItem;
        
        // select semua disc scheme utk all user
          
        $customer = new Customer();
        $rsCust = $customer->getDataRowById($userkey);
        $usercategorykey = $rsCust[0]['categorykey'];
        
        $userCriteria = (!empty($userkey)) ? ' (customercategorykey = 0 or customercategorykey = '.$this->oDbCon->paramString($usercategorykey).' ) ' : ' customercategorykey = 0 ';
        $sql = 'select 
                    * 
                from 
                    '.$this->tableName.'
                where 
                    '.$userCriteria.' and
                    statuskey = 1 and
                    date(now()) >= trdatestart and date(now()) <= trdateend
                order by 
                    orderlist asc, pkey desc
                ';
        
        $rs = $this->oDbCon->doQuery($sql);
         
    }
    
    function appliedDiscount($rsItem,$rsDiscScheme){
        
            $discount = $rsDiscScheme['discountvalue'];
            $discountType = $rsDiscScheme['discounttype'];   
        
            // jika itemnya sudah ad diskon, return    
            if ((isset($rsItem['hasDisc']) &&  $rsItem['hasDisc']) || $discount == 0 )
                return $rsItem;
        
            // kalo category cek by category
            // kalo by item, cek by item
        
            $applyDisc = false;
         
            if ($rsDiscScheme['itemcategorykey'] == 0)   {  
                $applyDisc = true;
            }else{
                $arrDiscCategory = $rsDiscScheme['arrDiscCategory'];        
                
                // cek apakah kategori item ada diantara kategori ini
                $applyDisc = (in_array($rsItem['categorykey'],$arrDiscCategory)) ? true : false; 
            }
            
                
            if ($applyDisc){
                if($discountType == 2) { 
                    $discountPercentange = $discount;
                    $discountValue = $discount/100 * $rsItem['sellingprice'];
                }else{ 
                    $discountValue = $discount;
                    $discountPercentange = ( $discount / $rsItem['sellingprice'] ) * 100;
                } 
                
                $rsItem['sellingprice'] -= $discountValue; 
                $rsItem['discountPercentage'] = $discountPercentange;
                $rsItem['hasDisc'] = true;

            }
            

        return $rsItem;
           
    }
	  
    
}
?>