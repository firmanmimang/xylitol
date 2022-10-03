<?php   
die;
/*
if(!in_array($_SERVER['REMOTE_ADDR'], array('36.70.170.15', '36.69.222.108')) ) 
   header('Location: /underconstruction.php');
    */
    
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

 includeClass(array('ItemUploadReceipt.class.php')); 

$obj = new ItemUploadReceipt();
$obj->sendReceiptUploadedEmail(10061,'LT2-02699'); 
// $obj->sendReceiptUploadedEmail(8258,'LT2-02659');
// $obj->sendReceiptUploadedEmail(8258,'LT2-02666');
// $obj->sendReceiptUploadedEmail(8258,'LT2-02667');

//includeClass(array('Customer.class.php')); 
 
// nanti jadikan default variable
// $arrTwigVar = array();
// $arrTwigVar = $class->getDefaultEmailVariable();
 
// $twig->render('email-template.html');  
// $content = $twig->render('email-evoucher.html', $arrTwigVar);

// $arrCustomer = array('hendrasuhendrawan98@gmail.com','elishevahananiel@yahoo.com','Yulianachrisna96@gmail.com','andrekaskusbangetz@gmail.com','annisaaulianstt20@gmail.com','chaterinedaslim05051@gmail.com','Deediana88@ymail.com','rokayahy779@gmail.com','Ohlidyapp@gmail.com','Tutiawlyhh19@gmail.com','Lissapandacili@gmail.com','gracia.elva@gmail.com','fucie.indahsptri@gmail.com','hardiyanti.arie@gmail.com','renafebi123@gmail.com','hannisanasution@gmail.com','t3r3s4.9723@gmail.com','yanuarbaskara07@gmail.com','kaylapramesti13@gmail.com','dewiirmakartika@gmail.com','tamayaelin402@gmail.com','elsalaytao@gmail.com','ttriawulandari92@gmail.com','yudithgirl@gmail.com','shellanadiaa@gmail.com','sellysrirahayu69@gmail.com','putrimeni32@gmail.com','khalimatusadiyah.mima@gmail.com','Triskastefany97@gmail.com','Agitfachrozi31@gmail.com','dianjuliafitriani99@gmail.com','hp.pringadi@gmail.com','kaisaki2003@gmail.com','dwimarleni1@gmail.com','prelly.lim1486@gmail.com','riski.jfc@gmail.com','nhakimah071@gmail.com','anggieariesta17@gmail.com','abdullahhafidh14@yahoo.com','adelliasyrytn210@gmail.com','kishishana@gmail.com','deksul91@gmail.com','mayaf76@yahoo.com','nanitnot07@gmail.com','lindawatirahman1976@gmail.com','fauziah.ami@gmail.com','samuelinjensitanggang@gmail.com','ningsihratna549@gmail.com','Oromi2978@gmail.com','idewamadealitp@gmail.com','Linatriyani25@gmail.com','nindyaputriapriliani@gmail.com','citrajatinangor98@gmail.com','Maryadi.adie26@gmail.com','anjelinalina16@gmail.com','Putriaapriliakristiani@gmail.com','ihsan_safaat@yahoo.com','rizkasaridewi6@gmail.com','zafiraazzahrapanjaitan0922@gmail.com','fatimahsariah98@gmail.com','novanoviana472@gmail.com','mesianawitaviaa@gmail.com','Nurliausman123@gmail.com','rizkawindap@gmail.com','siti.mariyam02@gmail.com','devyeff@yahoo.com','ratihkusuma.rw@gmail.com','kholifahramadany@gmail.com','tiarasabrina134@gmail.com','dewianggraini220890@gmail.com','nadiahrrn@gmail.com','apriliaekaputri86@gmail.com','ritakrisdayanti25@gmail.com','rifkazahra02@gmail.com','nurhalhal31@gmail.com','yegun168@gmail.com','ajehwtaetae@gmail.com','korimelypratiwi11@gmail.com','Woengusetiaaa@gmail.com','yohannasitepu2003@gmail.com','farafadiahx@gmail.com','three.oentoro@gmail.com','ika2912@gmail.com','yastarihumairantsy99@gmail.com','choirunnisa020@gmail.com','megalantu21@gmail.com','kimchanfood@gmail.com','emirizkika2@gmail.com','wlaurensi@gmail.com','adji.impact@gmail.com','rofianaapriliani077@gmail.com','beecintalazuardi16@gmail.com','nurmala.indrawati@gmail.com','shsdzkr@gmail.com','herlina.suryaningsih22@gmail.com','virlihau19@gmail.com','vazhifa07@gmail.com','dellaokvrikawidyani@gmail.com','wletiziadextra@gmail.com','arumlaksita2001@gmail.com','Anifatuldiah@gmail.com','silviaryantyy@gmail.com','tia.trimega@gmail.com','mellirachmayanti25@gmail.com','rindikoyongkam13@gmail.com','ditsstore1994@gmail.com','kucingmaula@gmail.com','Veromary1993@gmail.com','Vinsentio86@gmail.com','Hany.nurhanifah2@gmail.com','agusti.triwulansari@yahoo.com','komanglestari1234@gmail.com','Kopcusgreenlake@gmail.com','kamilatunnisasuryadi@gmail.com','vily20002020@gmail.com','anakanakmerdeka@gmail.com','veliap467@gmail.com','febyfebriani1992@gmail.com','Vahlevireza300@gmail.com','niya.maulida6@gmail.com','martinhalimk@gmail.com');
// //$arrCustomer = array('martinhalimk@gmail.com');

// foreach($arrCustomer as $email)
//     $class->sendMail('','', 'Dapatkan E-Voucher Alfamart dari LotteXylitol',$content,$email); 
        

//echo $content;
 
echo 'done';
?>