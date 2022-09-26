<?php
class Download extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'download';
        $this->tableCategory = 'download_category';
		$this->tableStatus = 'master_status';
		$this->securityObject = 'Download'; 
		$this->uploadFileFolder = 'download/';
		 
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
                    '.$this->tableCategory. '.name as categoryname
				from
					'.$this->tableName.','.$this->tableStatus.','.$this->tableCategory.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey
 		' .$this->criteria ; 
		 
    }
   
   
	function addData ($arrParam){
		$arrayToJs = array();
		
		try{
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
            $arrParam = $this->normalizeParameter($arrParam);
	 		$arrayToJs = $this->validateForm($arrParam);
				if (!empty($arrayToJs))
					return $arrayToJs;
	 
			$pkey = $this->getNextKey($this->tableName);  
             
            $externallink = '';
            if (!empty($arrParam['chkExternal'])){
                $externallink = $arrParam['externalLink'];
                $arrParam['item-file-uploader'] = array();
            } 
            
            $fileName = $this->updateImage($pkey, $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);  
		
			$sql = '
					INSERT INTO
					'.$this->tableName.'(
						pkey,
						code,
						name,
                        useexternallink,
                        file,
                        externallink,
                        categorykey,
                        shortdesc,
						statuskey,
						createdby,
						createdon
					)
					VALUES (
						'.$pkey.',
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['name']).',
                        '.$this->oDbCon->paramString($arrParam['chkExternal']).',
						'.$this->oDbCon->paramString($fileName).',
                        '.$this->oDbCon->paramString($externallink).',
                        '.$this->oDbCon->paramString($arrParam['hidCategoryKey']).',
                        '.$this->oDbCon->paramString($arrParam['shortDesc']).',
						'.$this->oDbCon->paramString($arrParam['selStatus']).',
						'.$this->oDbCon->paramString($arrParam['createdBy']).', 
						now()
					)
			';
			
			$this->oDbCon->execute($sql);
			
            $this->setTransactionLog(INSERT_DATA,$pkey);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);
            $arrayToJs[0]['pkey'] = $pkey;
            $arrayToJs[0]['value'] = $arrParam['name'];
            
            
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	
		
		return $arrayToJs;
			
	}
	
	function editData($arrParam){    
		$arrayToJs =  array();
	
		try{		
	  	 	 
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
            $arrParam = $this->normalizeParameter($arrParam);
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
              
            $externallink = '';
            if (!empty($arrParam['chkExternal'])){
                $externallink = $arrParam['externalLink'];
                $arrParam['item-file-uploader'] = array();
            } 
            
            $fileName = $this->updateImage($arrParam['hidId'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);  
             
            
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	 
						 code ='.$this->oDbCon->paramString($arrParam['code']).',			
						 name ='.$this->oDbCon->paramString($arrParam['name']).',
                         file = '.$this->oDbCon->paramString($fileName).',
                         useexternallink = '.$this->oDbCon->paramString($arrParam['chkExternal']).', 
                         externallink = '.$this->oDbCon->paramString($externallink).',
                         categorykey = '.$this->oDbCon->paramString($arrParam['hidCategoryKey']).',
                         shortdesc = '.$this->oDbCon->paramString($arrParam['shortDesc']).',
                         statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
						 modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
						 modifiedon = now()
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
						
				';    

                $this->oDbCon->execute($sql);                
				
               $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
				$this->oDbCon->endTrans(); 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}			
		
		return $arrayToJs; 
	}
	
     function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder; 
         
		if(!is_dir($sourcePath)) 
			return;
	
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
 
 		//delete previous images	    
		$this->deleteAll($destinationPath);   
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage); 
			$this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			return $arrImage[0]; 
		}
		
		return '';
		
	} 
	
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$id);
			
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	    
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
		$categorykey = $arr['hidCategoryKey'];  
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['download'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['download'][2]);
		} 
		 
        if(empty($categorykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}
        
		return $arrayToJs;
	 }	 
    
    function normalizeParameter($arrParam, $trim=false){
        
		    $arrParam = parent::normalizeParameter($arrParam);    
			$arrParam['chkExternal'] = (!empty($arrParam['chkExternal'])) ? 1 : 0;  
        
        return $arrParam;
    }
    
	    
		
}
?>