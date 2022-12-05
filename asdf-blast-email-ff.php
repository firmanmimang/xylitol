<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 
require __DIR__.'/_function_smtp_echo_ff.php';

includeClass("Customer.class.php");

// sendBlastEmailReminderPhase2();
    
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

function sendBlastEmailReminderPhase2(){
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

        foreach($chunk[0] as $row){
            $email = $row['email'];
            $arrTwigVar['CUSTOMER_NAME'] = $row['name'];
            $twig->render('email-template.html');
            $content = $twig->render('email-reminder-phase2-ff.html', $arrTwigVar);
            // echo $content . '<br>';
            smtp_mail($email, 'Reminder Pengundian Phase 2', $content, '');
        }		 
}

function sendBlastEmailWinnerPhase1(){
        global $twig;
        global $class;
        
        // $customer = new Customer();
        // $rsCustomer = $customer->searchDataRow(array('email', 'name'),' and point < 20 and statuskey <> 3 and statuskey <> 1');

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $class->getDefaultEmailVariable(); 
         
        // $twig->render('email-template.html');  
        // $content = $twig->render('email-reminder-phase1-ff.html', $arrTwigVar);

        $rsCustomer = [
              [
                'name'=>'Ade Surya R',
                'email'=>'adesuryariady8@gmail.com',
              ],
              [
                'name'=>'Adelyne',
                'email'=>'adelynniesa13@gmail.com',
              ],
              [
                'name'=>'Adli Daffa',
                'email'=>'adlidaffa23@gmail.com',
              ],
              [
                'name'=>'Adzani Diah',
                'email'=>'adzani.diah@gmail.com',
              ],
              [
                'name'=>'Ahmad Setiawan',
                'email'=>'setiawanahmad14.as@gmail.com',
              ],
              [
                'name'=>'Anastasia Ivory',
                'email'=>'ivorytroowlii@gmail.com',
              ],
              [
                'name'=>'Angelia',
                'email'=>'Angeliamiraldi@gmail.com',
              ],
              [
                'name'=>'Anis Nurlaely',
                'email'=>'nurlaelyanis07@gmail.com',
              ],
              [
                'name'=>'Anis Putriyana',
                'email'=>'anissputriyana@gmail.com',
              ],
              [
                'name'=>'anisah zauzah',
                'email'=>'anisahzauzah@yahoo.com',
              ],
              [
                'name'=>'Anna MayangSari',
                'email'=>'annamayangsari10@gmail.com',
              ],
              [
                'name'=>'ansella',
                'email'=>'ansellafatma17@gmail.com',
              ],
              [
                'name'=>'Asmiati',
                'email'=>'asmiatttiii@gmail.com',
              ],
              [
                'name'=>'Assyifa Caesara Viandini',
                'email'=>'sviandini@gmail.com',
              ],
              [
                'name'=>'Aulia Rizka',
                'email'=>'auliarizkaaa18@gmail.com',
              ],
              [
                'name'=>'ayssa witjaksono',
                'email'=>'ayso4556@gmail.com',
              ],
              [
                'name'=>'Ayu Novita Sari',
                'email'=>'ayunamjatop21@gmail.com',
              ],
              [
                'name'=>'Berlianti Suryanita',
                'email'=>'berliantisuryanita@gmail.com',
              ],
              [
                'name'=>'Blandina Valent',
                'email'=>'iniblandinavalent@gmail.com',
              ],
              [
                'name'=>'BONNEY KUMALA',
                'email'=>'bondua@gmail.com',
              ],
              [
                'name'=>'Cacih Cakaesih',
                'email'=>'cakaesihcacih@gmail.com',
              ],
              [
                'name'=>'Carina Permana',
                'email'=>'vacay0827@yahoo.com',
              ],
              [
                'name'=>'Chintyawati',
                'email'=>'chintyawati.tjokro@gmail.com',
              ],
              [
                'name'=>'Christina Celltha Wulandari',
                'email'=>'christinacellthaw@gmail.com',
              ],
              [
                'name'=>'Christine Susanto',
                'email'=>'luckylifegoeson7@gmail.com',
              ],
              [
                'name'=>'Ciptadhi Mulya',
                'email'=>'ciptadhimulya13@gmail.com',
              ],
              [
                'name'=>'Dara Satia',
                'email'=>'madamedarla17@gmail.com',
              ],
              [
                'name'=>'Delvi',
                'email'=>'Nengedoll10@gmail.com',
              ],
              [
                'name'=>'Dena Novita Rosiana',
                'email'=>'nooksxt.naa@gmail.com',
              ],
              [
                'name'=>'Dessy Arphina',
                'email'=>'arphina1@gmail.com',
              ],
              [
                'name'=>'Dessy Nurshiva',
                'email'=>'dessy.nurshiva@gmail.com',
              ],
              [
                'name'=>'Devi Handika',
                'email'=>'devihandika00@gmail.com',
              ],
              [
                'name'=>'Deviyana anggraini',
                'email'=>'dheviyana@yahoo.com',
              ],
              [
                'name'=>'Dewi Surya',
                'email'=>'dewisurya467@gmail.com',
              ],
              [
                'name'=>'Dhea Chairunnisa',
                'email'=>'dheachairnns@gmail.com',
              ],
              [
                'name'=>'Dian',
                'email'=>'yanchoco2910@gmail.com',
              ],
              [
                'name'=>'Dina widiyanti',
                'email'=>'dinawidiyanti9191@gmail.com',
              ],
              [
                'name'=>'Dini Fauziyah',
                'email'=>'fauziyahdindin@gmail.com',
              ],
              [
                'name'=>'Dinni Aprilia',
                'email'=>'dinnaadinni@gmail.com',
              ],
              [
                'name'=>'Ebi',
                'email'=>'ebirahman4@gmail.com',
              ],
              [
                'name'=>'Edi banu Sutanto',
                'email'=>'Edi.netciti@gmail.com',
              ],
              [
                'name'=>'Eka Agustina Arimurti',
                'email'=>'ninaekaagustina@gmail.com',
              ],
              [
                'name'=>'ELIS',
                'email'=>'zhanghuina92@gmail.com',
              ],
              [
                'name'=>'Esther Lowiyanti',
                'email'=>'loww2501.fullsun@gmail.com',
              ],
              [
                'name'=>'Febby',
                'email'=>'febrinaa2612@gmail.com',
              ],
              [
                'name'=>'Felly Hedriyani Laili',
                'email'=>'babysung13@gmail.com',
              ],
              [
                'name'=>'fenna herawati',
                'email'=>'fennaherawati@gmail.com',
              ],
              [
                'name'=>'Fennie',
                'email'=>'niedisini@gmail.com',
              ],
              [
                'name'=>'Fenny Aztari',
                'email'=>'bisnisfenny@gmail.com',
              ],
              [
                'name'=>'Ferdianti Ayu Maharani',
                'email'=>'ferdiantiayu.412@gmail.com',
              ],
              [
                'name'=>'Fiismi',
                'email'=>'fiismii@gmail.com',
              ],
              [
                'name'=>'frila karunia',
                'email'=>'frila204@gmail.com',
              ],
              [
                'name'=>'Hanifah',
                'email'=>'hanifahhh993@gmail.com',
              ],
              [
                'name'=>'Hannah',
                'email'=>'hanhannaeyy@gmail.com',
              ],
              [
                'name'=>'Hanny',
                'email'=>'Hanny_setiadi10@yahoo.com',
              ],
              [
                'name'=>'Ika Mei',
                'email'=>'mrskimseokjin15@gmail.com',
              ],
              [
                'name'=>'Ikrima Maida',
                'email'=>'maidaikrima12@gmail.com',
              ],
              [
                'name'=>'Intan Nur Fitri',
                'email'=>'intannurfitri30@gmail.com',
              ],
              [
                'name'=>'Ira Febrianty',
                'email'=>'irafebrianty.ynwa@gmail.com',
              ],
              [
                'name'=>'Ita agustina',
                'email'=>'itaa6288@gmail.com',
              ],
              [
                'name'=>'Iza Agustin',
                'email'=>'izzawahyu@gmail.com',
              ],
              [
                'name'=>'Janice Valerie',
                'email'=>'janicevalerie2@gmail.com',
              ],
              [
                'name'=>'Janie',
                'email'=>'Janielinqq@gmail.com',
              ],
              [
                'name'=>'Jilly Resmalia',
                'email'=>'jillyresmalia@gmail.com',
              ],
              [
                'name'=>'Josephine Gabrielle',
                'email'=>'gaby.domino82@gmail.com',
              ],
              [
                'name'=>'Khairun Nisa',
                'email'=>'khairunnisa.micky@gmail.com',
              ],
              [
                'name'=>'kristina Dewi',
                'email'=>'dewilebang84@gmail.com',
              ],
              [
                'name'=>'Lidia Natalia',
                'email'=>'lidianatha180295@gmail.com',
              ],
              [
                'name'=>'Lies Rinawaty',
                'email'=>'rinsynrin@gmail.com',
              ],
              [
                'name'=>'linda Purwaningsih',
                'email'=>'purwaningsihnyalinda@gmail.com',
              ],
              [
                'name'=>'Linda Wiliam',
                'email'=>'wiliamlinda.lw@gmail.com',
              ],
              [
                'name'=>'Marice Evadona',
                'email'=>'marice.evadona@yahoo.com',
              ],
              [
                'name'=>'Marina yustiana',
                'email'=>'rinafraditya@gmail.com',
              ],
              [
                'name'=>'Maryati Surya',
                'email'=>'msurya1506@yahoo.com',
              ],
              [
                'name'=>'Maya Fajarianti',
                'email'=>'maya.fajarianti@cita-indonesia.com',
              ],
              [
                'name'=>'Meli Indrawati15',
                'email'=>'meliindrawati55@gmail.com',
              ],
              [
                'name'=>'Merryana',
                'email'=>'2298merryana@gmail.com',
              ],
              [
                'name'=>'Molly',
                'email'=>'Anastasia.molly@gmail.com',
              ],
              [
                'name'=>'Muhammad Irfan Maulana',
                'email'=>'irfanboyoi23@gmail.com',
              ],
              [
                'name'=>'Mutiah Fachrunissa',
                'email'=>'mutiesa@gmail.com',
              ],
              [
                'name'=>'Nabillah',
                'email'=>'Nabfz000@gmail.com',
              ],
              [
                'name'=>'Nadia',
                'email'=>'Ndiajm13@gmail.com',
              ],
              [
                'name'=>'Nadia Surensi',
                'email'=>'nsurensi@gmail.com',
              ],
              [
                'name'=>'nadiarieninta',
                'email'=>'nadiarieninta@yahoo.com',
              ],
              [
                'name'=>'Nisa ul khusna',
                'email'=>'Incess.shasha99@gmail.com',
              ],
              [
                'name'=>'Nisrina Qotrunnada',
                'email'=>'nisrinaqotrun@hotmail.com',
              ],
              [
                'name'=>'Nora Damar Rahayu',
                'email'=>'olaarahayu@gmail.com',
              ],
              [
                'name'=>'Noveana Fernanda E',
                'email'=>'Nophejung92@yahoo.com',
              ],
              [
                'name'=>'Novia',
                'email'=>'Novia94.magdalena@gmail.com',
              ],
              [
                'name'=>'Novia Rahmawati',
                'email'=>'nnovia.rahmawati23@gmail.com',
              ],
              [
                'name'=>'NUR INDAH FITRIYAH',
                'email'=>'fitrieindah@gmail.com',
              ],
              [
                'name'=>'Nuri Tambusai',
                'email'=>'nuririri2005@gmail.com',
              ],
              [
                'name'=>'nyai R imas A (fadli)',
                'email'=>'anisahzauzah603@gmail.com',
              ],
              [
                'name'=>'Okta 24',
                'email'=>'octamcdonough@gmail.com',
              ],
              [
                'name'=>'Pungky W. Wijayani',
                'email'=>'pungky.wahyu@gmail.com',
              ],
              [
                'name'=>'R.G. Agastya',
                'email'=>'papajambang@gmail.com',
              ],
              [
                'name'=>'Rahma Syifa',
                'email'=>'rahmasyfdewi@gmail.com',
              ],
              [
                'name'=>'Ramdhani Kusuma Putra',
                'email'=>'ramdhanihealer99@gmail.com',
              ],
              [
                'name'=>'Rani Nkh',
                'email'=>'rainikhr@gmail.com',
              ],
              [
                'name'=>'Ranti Phussa',
                'email'=>'Rant.phussa28@gmail.com',
              ],
              [
                'name'=>'Regina Sisca',
                'email'=>'reginasiscaf@gmail.com',
              ],
              [
                'name'=>'Retno Setyaningrum',
                'email'=>'noee131997@gmail.com',
              ],
              [
                'name'=>'Riafinola Rahma Vianti',
                'email'=>'riafinolarv@gmail.com',
              ],
              [
                'name'=>'Riska Ayu Suryani',
                'email'=>'riska_ayu94@ymail.com',
              ],
              [
                'name'=>'Rizky Pontoh',
                'email'=>'Lawrizky16@gmail.com',
              ],
              [
                'name'=>'Rosdiana -',
                'email'=>'rosdiana.jmr@gmail.com',
              ],
              [
                'name'=>'Sarah Handayani',
                'email'=>'zywawa79@gmail.com',
              ],
              [
                'name'=>'Sarah Kristimonica',
                'email'=>'sarrmonika@gmail.com',
              ],
              [
                'name'=>'selli sel',
                'email'=>'selviantiselli@gmail.com',
              ],
              [
                'name'=>'Shafira Anindyanari',
                'email'=>'shafira.anindyanr@gmail.com',
              ],
              [
                'name'=>'Shanny Ciam',
                'email'=>'shanny19ciam@gmail.com',
              ],
              [
                'name'=>'Siska Aldrika',
                'email'=>'siskaaldrika@gmail.com',
              ],
              [
                'name'=>'sofia noor rahmi',
                'email'=>'sofianoorrahmi@gmail.com',
              ],
              [
                'name'=>'Stefanny Lukita',
                'email'=>'funfunnie@gmail.com',
              ],
              [
                'name'=>'Susanti kristiani',
                'email'=>'Susantikristiani23@gmail.com',
              ],
              [
                'name'=>'Swara Hati Nurani',
                'email'=>'swanurani@gmail.com',
              ],
              [
                'name'=>'taeci',
                'email'=>'sucishopping3@gmail.com',
              ],
              [
                'name'=>'Teresa',
                'email'=>'t3r3s4.9723@gmail.com',
              ],
              [
                'name'=>'Tia Nur Kusuma Dewanti',
                'email'=>'tiadewanti12@gmail.com',
              ],
              [
                'name'=>'Tiara lorensia',
                'email'=>'tiaralorensia95@gmail.com',
              ],
              [
                'name'=>'Tika puspitasari',
                'email'=>'Poespitasari0611@gmail.com',
              ],
              [
                'name'=>'TITAN SURYANI PAKKU',
                'email'=>'michaelbrant.1990@gmail.com',
              ],
              [
                'name'=>'Titis Wahyu Ningtias',
                'email'=>'titiswahyu00@gmail.com',
              ],
              [
                'name'=>'Triska',
                'email'=>'Triskastefany97@gmail.com',
              ],
              [
                'name'=>'Tuti awaliyah',
                'email'=>'Tutiawlyhh19@gmail.com',
              ],
              [
                'name'=>'Ulfaahoran',
                'email'=>'ulfasurmayanti@gmail.com',
              ],
              [
                'name'=>'Umi kalsum',
                'email'=>'fahhaddad16@gmail.com',
              ],
              [
                'name'=>'V ily_2000',
                'email'=>'vily20002020@gmail.com',
              ],
              [
                'name'=>'Yopi',
                'email'=>'yopanagalistrie@gmail.com',
              ],
              [
                'name'=>'Yudawartika',
                'email'=>'yaduy2543@gmail.com',
              ],
              [
                'name'=>'Yuli',
                'email'=>'yuliniasih13@gmail.com',
              ],
              [
                'name'=>'Yuuki Gray',
                'email'=>'yuukigray3005@gmail.com',
              ],
              [
                'name'=>'Zunairi Nur Arifah',
                'email'=>'zunairi.na@gmail.com',
              ],

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