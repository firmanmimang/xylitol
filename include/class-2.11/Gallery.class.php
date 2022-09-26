<?php

class Gallery extends BaseClass{
 
   function __construct(){
		
		  parent::__construct();
       
		  $this->tableName = 'gallery_header';
		  $this->tableImage = 'gallery_image';
		  $this->tableStatus = 'master_status';
		  $this->tableCustomer = 'customer'; 
		  $this->securityObject ='Gallery';
		  $this->uploadFolder = 'gallery/';
		  
           $this->includeClassDependencies(array( 
                  'Customer.class.php'
           ));

	 }
	 
	function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* , 
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableCustomer.'.name as customername 
			FROM '.$this->tableStatus.', '.$this->tableName.' 
					left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
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
			
				
				if (empty($arrParam['hidCustomerKey']))
					$arrParam['hidCustomerKey'] = '';
    
				$sql = '
						INSERT INTO		
						 '.$this->tableName .' (
                            pkey, 
							code,
							name,
							trdesc,
							statuskey,
							customerkey,
							createdby,
							createdon
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).',
							'.$this->oDbCon->paramString($arrParam['name']).',
							'.$this->oDbCon->paramString($arrParam['trDesc']).',
							1,
							'.$this->oDbCon->paramString($arrParam['hidCustomerKey']).', 
							'.$this->oDbCon->paramString($arrParam['createdBy']).', 
							now()
						)
				';
			 
				$this->oDbCon->execute($sql);
				
				$this->updateImage($pkey, $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);
                                
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
				
				
				if (empty($arrParam['hidCustomerKey']))
					$arrParam['hidCustomerKey'] = '';
				 
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							code = 	'.$this->oDbCon->paramString($arrParam['code']).',
							name = 	'.$this->oDbCon->paramString($arrParam['name']).',
							trdesc = '.$this->oDbCon->paramString($arrParam['trDesc']).',
							customerkey = '.$this->oDbCon->paramString($arrParam['hidCustomerKey']).',
							modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
							modifiedon = now() 
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
				';
														   
				$this->oDbCon->execute($sql);
				
				$this->updateImage($arrParam['hidId'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);
				
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
            
				$this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			 

	}
	
	function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		if(!is_dir($sourcePath)) 
			return; 
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  

		//delete previous images	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i]); 
				
				$sql = 'insert into '.$this->tableImage.' (refkey,file) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrImage[$i]).')';	
				$this->oDbCon->execute($sql);	 
				
			}		
		}
			
	} 
	
	function validateForm($arr,$pkey = ''){ 
     
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
		$customerKey = $arr['hidCustomerKey'];  
	  	 
        $arrImage = explode(",",$arr['item-image-uploader']);
        if(count($arrImage) > PLAN_TYPE['maxproductimage'])
            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][2]); 
           
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) > (pow(1024,2) * PLAN_TYPE['maximagesize']))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']/1024). ' KB)' );
        }
           
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['title'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['title'][2]);
		}
		/*
		if(empty($customerKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		} */
		 
		return $arrayToJs;
	 }
	 
	 function getGalleryImage($pkey ){  
		$sql = 'select * from '.$this->tableImage.' where refkey = '.$this->oDbCon->paramString($pkey).' order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
    } 
	
	  
	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
			 
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			  
	
				$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($id);
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
}
?>