<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 
require __DIR__.'/_function_smtp_echo_ff.php';

includeClass("Customer.class.php");

sendBlastEmailReminderPhase1();
    
echo 'done';

function sendBlastEmailReminderPhase1(){
        global $twig;
        global $class;
        
        $customer = new Customer();
        $rsCustomer = $customer->searchDataRow(array('email', 'name'),' and point < 20 and statuskey <> 3 and statuskey <> 1');

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $class->getDefaultEmailVariable(); 
         
        $chunk = array_chunk($rsCustomer,50);
        echo count($chunk) . '<br>';
        // print_r($chunk);
         
        // $twig->render('email-template.html');  
        // $content = $twig->render('email-reminder-phase1-ff.html', $arrTwigVar);

        $chunk[5] = [
            [
                'email'=> 'nissanurlatifah96@gmail.com;',
                'name' => 'Nisa Nurlatifah'
            ],
        ];

        $chunk6 = array_slice( $chunk[6], 12, null, true);

        foreach($chunk[5] as $row){
            $email = $row['email'];
            $arrTwigVar['CUSTOMER_NAME'] = $row['name'];
            $twig->render('email-template.html');
            $content = $twig->render('email-reminder-phase1-ff.html', $arrTwigVar);
            echo $email . '<br>';
            // smtp_mail($email, 'Reminder Pengundian Phase 1', $content, '');
        }		 
}

function sendBlastEmailWinnerPhase1(){
        global $twig;
        global $class;
        
        $customer = new Customer();
        $rsCustomer = $customer->searchDataRow(array('email', 'name'),' and point < 20 and statuskey <> 3 and statuskey <> 1');

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $class->getDefaultEmailVariable(); 
         
        // $twig->render('email-template.html');  
        // $content = $twig->render('email-reminder-phase1-ff.html', $arrTwigVar);

        $rsCustomer = [
            [
                'email'=> 'fhidayat131@gmail.com',
                'name' => 'Firman Mimang'
            ],
            [
                'email'=> 'firman.hidayat@concretejakarta.com',
                'name' => 'Firman Hidayat',
            ],
            [
                'email'=> 'amaliaarimalika@gmail.com',
                'name' => 'Malikey',
            ],
        ];

        foreach($rsCustomer as $row){
            $email = $row['email'];
            $arrTwigVar['CUSTOMER_NAME'] = $row['name'];
            $twig->render('email-template.html');
            $content = $twig->render('email-winner-phase1-ff.html', $arrTwigVar);
            // echo $content;
            smtp_mail($email, 'Pengumuman Pemenang Smile Project IV Phase 1', $content, '');
        }		 
}
?>