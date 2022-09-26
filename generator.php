<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('Voucher.class.php'));  
$voucher = new Voucher();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
if(USERKEY <> 8043){
    die("..");
}

$totalGenerated = 1000;

$arrWinner = array();

try{ 
            
        if(!$class->oDbCon->startTrans())
            throw new Exception($class->errorMsg[100]); 
 


        // ========================================================= others
        if($totalGenerated > 0){  
            $criteria = array();
            array_push($criteria,$voucher->tableName.'.statuskey = 1');
            
            //$voucher->setLog($criteria,true);
            $rsAllVoucher = $voucher->searchData('','',true,' and '.implode(' and ', $criteria));
            shuffle($rsAllVoucher);
        }

        foreach($rsAllVoucher as $row){
            array_push($arrWinner, $row); 
            $totalGenerated--; 
            if($totalGenerated <=0) break;
        } 
        setVoucherWinner($arrWinner);
        // ========================================================= others
     
    

        foreach($arrWinner as $key=>$row){ 
            $email = $row['customeremail'];

            $mail_part = explode('@', $email);
            $username = $mail_part[0]; 
            $mail_part[0] = substr($username, 0, 4)  .'***'; 
            if(strlen($username) > 6) $mail_part[0] .= substr($username, -2);   
            $arrWinner[$key]['customeremail'] =  implode('@', $mail_part);
        }
        $class->oDbCon->endTrans();


    } catch(Exception $e){
        $class->oDbCon->rollback();
        $class->addErrorList($arrayToJs,false, $e->getMessage());   
    }


$arrTwigVar ['rsVoucher'] = $arrWinner;
$arrTwigVar ['rsVoucherjson'] = json_encode($arrWinner);

echo $twig->render('generator.html', $arrTwigVar);


function setVoucherWinner($arrWinner){
    global $class;
    $sql = 'update voucher set statuskey = 2 where pkey in ('.$class->oDbCon->paramString(array_column($arrWinner,'pkey'),',').') ';
    $class->oDbCon->execute($sql);
}
?>