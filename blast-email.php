<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 
    
includeClass("Customer.class.php");

// sendBlastEmail();
    
echo 'not active';

function sendBlastEmail(){
		
        global $twig;
        global $class;
        
        $customer = new Customer();
        $rsCustomer = $customer->searchDataRow(array('email'),' and point < 20 and statuskey <> 3 and statuskey <> 1');
        
        
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $class->getDefaultEmailVariable(); 
         
        $twig->render('email-template.html');  
        $content = $twig->render('email-reminder.html', $arrTwigVar);
        
        foreach($rsCustomer as $row){
              $email = $row['email']; 
              echo $email . '<br>';
            //   $class->sendMail('','', DOMAIN_NAME,$content,$email); 
        }
         
        
      
    	 

		 
}
?>