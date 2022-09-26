<?php

class JobOpportunities extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'job_opportunities'; 
		$this->securityObject = 'JobOpportunities'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'job-opportunities/';
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['title'] = array('title');
        $this->arrData['shortdesc'] = array('trDesc');  
        $this->arrData['detail'] = array('txtDetail','raw');  
             
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'title','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->newLoad = true;
       
        $this->overwriteConfig();
   }
   
   
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ','.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	 
	
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$name = $arr['title'];  
	 	   
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobOpportunities'][1]);
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