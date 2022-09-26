<?php
// dummy class
class Marketplace extends BaseClass{ 
    function __construct(){ 
        parent::__construct(); 
		$this->securityObject = 'Marketplace';  
    } 
    function getQuery(){return '';} 
    function getMarketplaceObj($marketplaceKey = ''){   return array();  } 
    function syncProductsInAllMarketplace(){}
}
 ?>