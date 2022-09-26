<?php

class  ManagementTeam extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'management_team'; 
		$this->tableStatus = 'master_status';
		$this->securityObject = 'ManagementTeam';	  
        $this->uploadFolder = 'management-team/';

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['position'] = array('position'); 
        $this->arrData['trdesc'] = array('description'); 
        $this->arrData['file'] = array('fileName'); 
        $this->arrData['orderlist'] = array('orderList','number'); 
          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'position','title' => 'position','dbfield' => 'position','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->newLoad = true;
        $this->overwriteConfig();
   }
   
	
   function getQuery(){
	    
	   $sql = '
			select
					'.$this->tableName. '.*, 
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName .', 
					'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
                    
 		' .$this->criteria ; 
		  
       return $sql;
    }  

    
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
         	 	
		$name = $arr['name'];  
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
        
		return $arrayToJs;
	 }   
    
    
    
     function normalizeParameter($arrParam, $trim = false){
          
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader'],'',$this->uploadFolder); 
         
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }
    
  }

?>
