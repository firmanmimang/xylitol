<?php

class Article extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'article'; 
		$this->tableCategory = 'article_category';   
		$this->securityObject = 'Article'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'article/'; 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['shortdesc'] = array('txtShortDescription'); 
        $this->arrData['publishdate'] = array('publishDate','date');
        $this->arrData['detail'] = array('txtDetail','raw'); 
        $this->arrData['featured'] = array('isFeatured');  
        $this->arrData['file'] = array('fileName');
        $this->arrData['statuskey'] = array('selStatus');
          
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'title','dbfield' => 'categoryname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'publishDate','title' => 'publishDate','dbfield' => 'publishdate','default'=>true, 'width' => 100, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->newLoad = true;
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   return '
			select
				'.$this->tableName. '.*,
				'.$this->tableStatus.'.status as statusname , 
				'.$this->tableCategory.'.name as categoryname		
			from 
				'.$this->tableName . ' left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey, '.$this->tableStatus.' 
			where  		 
				'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
   }
   
    
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
	 
        $arrImage = explode(",",$arr['item-image-uploader']); 
        for($i=0;$i<count($arrImage);$i++){
            if (empty($arrImage[$i]))
                continue;
            
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        } 
		
	 	$rs = $this->isValueExisted($pkey,'title',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['article'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['article'][2]);
		}
		  
		return $arrayToJs;
	 }

    
	 
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
			
        
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
    
        
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	    
}

?>