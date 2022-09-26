<?php

class DownloadCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'download_category';  
		$this->securityObject = 'DownloadCategory'; 
	 
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'download','field'=>$defaultFieldName)); 
   }
     
 
}

?>