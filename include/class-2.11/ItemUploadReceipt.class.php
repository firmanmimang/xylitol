<?php

class ItemUploadReceipt extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'item_upload_receipt_header';
        $this->tableNameDetail = 'item_upload_receipt_detail';
        $this->tableWarehouse = 'warehouse';
        $this->tableItem = 'item';
        $this->tableCity = 'city';
        $this->tableCustomer = 'customer';
        $this->tableStatus = 'item_upload_receipt_status';
        $this->uploadFolder = 'upload-receipt/';
        $this->securityObject = 'ReceiptValidation';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['point'] = array('point', 'number');
        $this->arrDataDetail['qty'] = array('qty', 'number');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['name'] = array('name');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['storename'] = array('storeName');
        //$this->arrData['invoicenumber'] = array('invoiceNumber');
        $this->arrData['trdate'] = array('trDate', 'date');
        //$this->arrData['receiptdate'] = array('receiptDate','date');
        $this->arrData['description'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['filename'] = array('fileName');
        $this->arrData['cancelreasonkey'] = array('selCancelReasonKey');
        $this->arrData['cancelreason'] = array('cancelReason');

        $this->noCancelReason = '-----';

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'datetime', 'title' => 'uploadDate', 'dbfield' => 'trdate', 'default' => true, 'width' => 110, 'align' => 'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'store', 'title' => 'store', 'dbfield' => 'storename', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'receipt', 'title' => 'invoiceNumber', 'dbfield' => 'invoicenumber', 'default' => true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalpoint', 'title' => 'point', 'dbfield' => 'totalpoint', 'default' => true, 'width' => 50, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cancelreason', 'title' => 'cancelReason', 'dbfield' => 'cancelreason', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'City.class.php',
            'Customer.class.php',
            'Item.class.php',
            'CancelReason.class.php',
            'Voucher.class.php',
        ));

        $this->newLoad = true;
        $this->overwriteConfig();
    }

    function getQuery()
    {
        $sql = '
            SELECT
                ' . $this->tableName . '.* ,  
                ' . $this->tableWarehouse . '.name as warehousename,
                ' . $this->tableCustomer . '.name as customername,
                ' . $this->tableCustomer . '.mobile as customermobile,
                ' . $this->tableCustomer . '.email as customeremail,
                ' . $this->tableCity . '.name as cityname,
                ' . $this->tableStatus . '.status as statusname
                
            FROM ' . $this->tableStatus . ',
                 ' . $this->tableWarehouse . ',
                 ' . $this->tableCustomer . ',
                 ' . $this->tableName . ' 
                    left join   ' . $this->tableCity . ' on  ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey
                    
            WHERE   
                  ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                  ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 

            ' . $this->criteria;

        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $arrItem = $arr['hidItemKey'];
        $arrQtyItem = $arr['qty'];
        $storeName = trim($arr['storeName']);
        $invoiceNumber = trim($arr['invoiceNumber']);
        $image =  $arr['item-image-uploader'];
        $receiptDate = trim($arr['receiptDate']);

        /*  
        if(empty($storeName))
            $this->addErrorList($arrayToJs,false, 'Nama toko harus diisi.'); 
        
        if(empty($invoiceNumber))
            $this->addErrorList($arrayToJs,false, 'Nomor struk harus diisi.'); */

        // storename = '.$this->oDbCon->paramString($storeName).' and 
        // validasi pkey
        $sql = 'select pkey from ' . $this->tableName . ' 
                where 
                    statuskey in (1,2) and 
                    invoicenumber = ' . $this->oDbCon->paramString($invoiceNumber) . ' and 
                    pkey <> ' . $this->oDbCon->paramString($pkey) . '     
                ';

        $rs = $this->oDbCon->doQuery($sql);
        if (!empty($rs))
            $this->addErrorList($arrayToJs, false, 'Nomor struk sudah terdaftar.');


        // validasi cuma dr FE
        if (isset($arr['fromFE']) && $arr['fromFE'] == 1) {
            $hasQty = false;
            foreach ($arrQtyItem as $qty) {
                if ($this->formatNumber($qty) > 0) {
                    $hasQty = true;
                    break;
                }
            }

            // cek tgl periode
            /*  
            $dateDiff1 = $this->dateDiff('01 / 09 / 2021',$receiptDate);   
            $dateDiff2 = $this->dateDiff($receiptDate, '27 / 10 / 2021');   
                    
            if(empty($receiptDate))
                $this->addErrorList($arrayToJs,false, 'Tgl. struk harus diisi.'); 
            else if ($dateDiff1 < 0 || $dateDiff2 < 0)
                $this->addErrorList($arrayToJs,false, 'Tgl. struk tidak valid.'); 
            */

            if (!$hasQty)
                $this->addErrorList($arrayToJs, false, 'Jumlah pembelian harus diisi.');

            if (empty($image))
                $this->addErrorList($arrayToJs, false, 'Foto struk harus diupload.');

            if (empty($arr['chkAgree']))
                $this->addErrorList($arrayToJs, false, $this->errorMsg['registration'][1]);
        }

        // kalo ubah status ke cancel, harus ad alasan
        if ($arr['selStatus'] == 3 && $arr['selCancelReasonKey'] == 0) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[903], true);
        }

        return $arrayToJs;
    }

    function afterUpdateData($arrParam, $action)
    {
        if (isset($arrParam['item-image-uploader'])) {
            $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);
            $sql = 'update ' . $this->tableName . ' set filename = ' . $this->oDbCon->paramString($arrParam['fileName']) . ' where pkey = ' . $this->oDbCon->paramString($arrParam['pkey']);
            $this->oDbCon->execute($sql);
        }

        $this->updatePoint($arrParam['pkey']);

        if ($action == INSERT_DATA) {
            $rsHeader = $this->getDataRowById($arrParam['pkey']);
            // hitung total point yang diupload untuk send email kurang dari 20 point
            $totalUploadPoint = ($arrParam['qty'][0] * 10) + ($arrParam['qty'][1] * 5) + ($arrParam['qty'][2] * 2);
            // ----------------------------------------------------------------------

            $this->sendReceiptUploadedEmail($arrParam['hidCustomerKey'], $arrParam['code']);

            if($totalUploadPoint < 20) {
                $this->sendReceiptPoinsEmail($rsHeader);
            }
        } else {
            $rsHeader = $this->getDataRowById($arrParam['pkey']);
            if ($rsHeader[0]['statuskey'] == 2)
                $this->sendReceiptApprovedEmail($rsHeader);
            else if ($rsHeader[0]['statuskey'] == 3)
                $this->sendReceiptRejectedEmail($rsHeader);
        }
    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $arrParam['invoiceNumber'] =  $str = preg_replace('/[\W]/', '', $arrParam['invoiceNumber']);

        // update nilai point per transaksi 
        $arrItem = $arrParam['hidItemKey'];

        $item = new Item();
        $rsItem = $item->searchDataRow(array($item->tableName . '.pkey,' . $item->tableName . '.pointvalue'), ' and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString($arrItem, ',') . ') ');
        $arrItemPoint = array_column($rsItem, 'pointvalue', 'pkey');

        $arrParam['point'] = array();
        foreach ($arrItem as $itemkey)
            array_push($arrParam['point'], $arrItemPoint[$itemkey]);

        if ($arrParam['selStatus'] <> 3) {
            $arrParam['selCancelReasonKey'] = 0;
            $arrParam['cancelreason'] = '';
        } else {
            $cancelReason = new CancelReason();
            $rsCancelReason = $cancelReason->getDataRowById($arrParam['selCancelReasonKey']);
            $arrParam['cancelReason'] = $rsCancelReason[0]['reason'];
        }

        // kalo bukan dr front end
        if (empty($arrParam['fromFE'])) {
            unset($this->arrData['pkey'][1]);
        }

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

    function validateDelete($id)
    {
        $arrayToJs = array();

        $this->addErrorList($arrayToJs, false, $this->errorMsg[212]);

        return $arrayToJs;
    }

    /*
    function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
        $this->setLog($sourcePath,true);
        $this->setLog($destinationPath,true);
        
		if(!is_dir($sourcePath))  return;
	 
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
    */

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
            ' . $this->tableNameDetail . '.*,
            ' . $this->tableItem . '.name as itemname, 
            ' . $this->tableItem . '.code as itemcode ,
            ' . $this->tableItem . '.sellingprice  
          from
            ' . $this->tableNameDetail . ',
            ' . $this->tableItem . '
          where  
            ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
            ' . $this->tableNameDetail . '.refkey in  (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }


    function changeStatus($id, $status, $reason = '', $copy = false, $autoChangeStatus = false, $ignoreValidation = false)
    {

        if (!is_numeric($status))  die;

        $rsHeader = $this->getDataRowById($id);

        try {
            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus()))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[212], true);

            if ($rsHeader[0]['statuskey'] == $status)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[224], true);
        } catch (Exception $e) {
            return $this->getErrorLog();
            //$this->addErrorList($arrayToJs,false,$e->getMessage());
        }


        try {
            // ================== VALIDATION 
            if (!$ignoreValidation) {
                switch ($status) {
                    case 1:
                        $this->validateInput($rsHeader);
                        break;
                    case 2:
                        if ($rsHeader[0]['statuskey'] < $status)
                            $this->validateConfirm($rsHeader);
                        else
                            $this->validateBackConfirm($rsHeader);
                        break;
                    case 3:
                        $this->validateClose($rsHeader, $reason);
                        break;
                    case 4:
                        $this->validateCancel($rsHeader, $autoChangeStatus);
                        break;
                }
            }

            //make sure we throw error 
            $this->throwIfHasErrorLog();


            // ================== VALIDATION OK !

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            switch ($status) {
                case 2:
                    if ($rsHeader[0]['statuskey'] < $status) {
                        $this->acceptReceipt($rsHeader);
                        //$this->afterAcceptReceipt($rsHeader);
                    } else {
                        $this->backAcceptReceipt($rsHeader);
                        //$this->afterBackAcceptReceipt($rsHeader);
                    }
                    break;
                case 3:
                    $this->declineReceipt($rsHeader);
                    $this->afterDeclineReceipt($rsHeader);
                    break;
                case 4:
                    $this->cancelTrans($rsHeader, $copy);
                    $this->afterCancelTrans($rsHeader);
                    break;
            }

            $sql = 'update ' . $this->tableName . ' set statuskey = ' . $this->oDbCon->paramString($status) . ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            $this->setTransactionLog($status, $id, '', $reason);

            $this->afterStatusChanged($rsHeader);

            $this->oDbCon->endTrans();
            $this->addErrorLog(true, $this->lang['dataHasBeenSuccessfullyUpdated']);
        } catch (Exception $e) {

            $this->oDbCon->rollback();

            if (!empty($e->getMessage()))
                $this->addErrorLog(false, $e->getMessage());
            //$this->addErrorList($arrayToJs,false,$e->getMessage());
        }

        return $this->getErrorLog();
    }


    function validateInput($rsHeader)
    {
        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[212], true);
    }

    function validateBackConfirm($rsHeader)
    {
        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[212], true);
    }

    function validateClose($rsHeader, $reason)
    {
        if ($rsHeader[0]['statuskey'] <> 1) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[212], true);
        } else {
            if (empty(trim($reason)) || $reason == $this->noCancelReason)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[903], true);
        }
    }

    function acceptReceipt($rsHeader)
    {

        $sql = 'update ' . $this->tableName . ' set  
              cancelreason = \'\',cancelreasonkey = 0
              where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
        $this->oDbCon->execute($sql);
    }

    function declineReceipt($rsHeader)
    {
    }

    function afterStatusChanged($rsHeader)
    {
        // kalo dr perubahan status
        // harus set ulang cancelreason
        $pkey = $rsHeader[0]['pkey'];
        $this->updatePoint($pkey);

        // harus ambil ulang status terbaru
        $rsHeader = $this->getDataRowById($pkey);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->sendReceiptApprovedEmail($rsHeader);
        else if ($rsHeader[0]['statuskey'] == 3)
            $this->sendReceiptRejectedEmail($rsHeader);
    }

    function updatePoint($pkey)
    {
        if (empty($pkey)) return;

        $totalPoint = 0;

        // hitung ulang dulu, kalo batal langsung set 0 saja
        // harus ambil ulang status terbaru
        $rsHeader = $this->getDataRowById($pkey);
        if ($rsHeader[0]['statuskey'] == 2) {
            $sql = 'select sum(point*qty) as totalpoint from ' . $this->tableNameDetail . ' where refkey = ' . $this->oDbCon->paramString($pkey);
            $rs = $this->oDbCon->doQuery($sql);

            $totalPoint = $rs[0]['totalpoint'];
        }

        // biarin saja dibawah utk jaga2, recount ulang, kalo nanti bisa berubah status

        $sql = 'update ' . $this->tableName . ' set 
                totalpoint = ' . $this->oDbCon->paramString($totalPoint) . ' 
                where pkey = ' . $this->oDbCon->paramString($pkey);

        $this->oDbCon->execute($sql);

        // update ke total point customer
        $this->resyncCustomerPoint($rsHeader[0]['customerkey']);

        //update voucher
        $this->updateVoucher($rsHeader[0]['customerkey']);
    }

    function updateVoucher($customerkey)
    {
        $voucher = new Voucher();
        $customer = new Customer();

        //cek customer nya yang aktif
        $rsCustomer = $customer->getDataRowById($customerkey);
        if (empty($rsCustomer)) return;

        //cek point customer jika lebih dari 20 
        if ($rsCustomer[0]['point'] >= 20) {
            // bagi 20, agar dapat kelipatannya
            $counter =  floor($rsCustomer[0]['point'] / 20);

            // cek total voucher yg sudah didapat 
            $sql = 'select coalesce(count(pkey),0) as totalvoucher from ' . $voucher->tableName . ' where customerkey = ' . $this->oDbCon->paramString($rsCustomer[0]['pkey'] . ' AND typekey = 1');
            //$this->setLog($sql,true);

            $rsVoucher = $this->oDbCon->doQuery($sql);
            $voucherClaimed = $rsVoucher[0]['totalvoucher'];

            //cek vouchernya blm ada
            //if(empty($rsVoucher)){ 
            $totalVoucher = $counter - $voucherClaimed;
            //$this->setLog($counter.'-'.$voucherClaimed,true);
            //$this->setLog($totalVoucher,true);
            for ($i = 1; $i <= $totalVoucher; $i++) {
                $arr = array();
                $arr['code'] = array('code');
                $arr['startDate'] = date('d / m / Y');
                $arr['hidCustomerKey'] = $rsCustomer[0]['pkey'];
                $arr['value'] = 1;
                $arr['selCategory'] = 2;
                $arr['selType'] = 1;

                $rsVoucherResponse = $voucher->addData($arr);
                $rsVoucherResponse = $rsVoucherResponse[0]['data'];
                $this->sendVoucherEmail($rsVoucherResponse['customerkey'], $rsVoucherResponse['code']);

                // if($i%2 == 0){
                //     $this->sendVoucher40Email($rsVoucherResponse['customerkey'], $rsVoucherResponse['code']);
                // }
            }

            // if($rsCustomer[0]['point'] >= 40){
            //     // bagi 40, agar dapat kelipatannya
            //     $counter =  floor($rsCustomer[0]['point'] / 40);

            //     // cek total voucher yg sudah didapat 
            //     $sql = 'select coalesce(count(pkey),0) as totalvoucher from ' . $voucher->tableName . ' where customerkey = ' . $this->oDbCon->paramString($rsCustomer[0]['pkey'] . ' AND typekey = 2');
            //     //$this->setLog($sql,true);

            //     $rsVoucher = $this->oDbCon->doQuery($sql);
            //     $voucherClaimed = $rsVoucher[0]['totalvoucher'];

            //     //cek vouchernya blm ada
            //     //if(empty($rsVoucher)){ 
            //     $totalVoucher = $counter - $voucherClaimed;
            //     //$this->setLog($counter.'-'.$voucherClaimed,true);
            //     //$this->setLog($totalVoucher,true);
            //     for ($i = 0; $i < $totalVoucher; $i++) {
            //         $arr = array();
            //         $arr['code'] = array('code');
            //         $arr['startDate'] = date('d / m / Y');
            //         $arr['hidCustomerKey'] = $rsCustomer[0]['pkey'];
            //         $arr['value'] = 1;
            //         $arr['selCategory'] = 2;
            //         $arr['selType'] = 2;

            //         $rsVoucherResponse = $voucher->addData($arr);
            //         $rsVoucherResponse = $rsVoucherResponse[0]['data'];
            //         $this->sendVoucher40Email($rsVoucherResponse['customerkey'], $rsVoucherResponse['code']);

            //         // cek total voucher yg sudah didapat 
            //         // $sql = "INSERT INTO ". $voucher->tableName . "(code, categorykey, startdate, typekey, value, ) VALUES ('John', 'Doe', 'john@example.com')";
            //         //$this->setLog($sql,true);

            //         // $rsVoucher = $this->oDbCon->doQuery($sql);
            //     }
            // }
        } else {
            // echo $rsCustomer[0]['point'] .'\n';
            // echo $rsCustomer[0]['point'] % 20;
            // die;
            //delete voucher jika point kurang dari 20

            // ini harus dibenerin, kalo ad yg batalin vouhcer ke sekian
            $sql = 'delete from ' . $voucher->tableName . ' where customerkey = ' . $this->oDbCon->paramString($rsCustomer[0]['pkey']);
            $this->oDbCon->execute($sql);
        }
    }

    function resyncCustomerPoint($customerkey)
    {

        $arrCustomer = array('8212', '8247', '8740', '8102', '8729', '8075', '8769', '8160', '8772', '8770', '8155', '8755', '8224', '8365', '8195', '8792', '8231', '8733', '8220', '8107', '8884', '8774', '8892', '8936', '8783', '8943', '8941', '8643', '8222', '8077', '8895', '8907', '8228', '8794', '8211', '8973', '8965', '8962', '9016', '8977', '8535', '9015', '8913', '9069', '9183', '8967', '9474', '9673', '9676', '9810', '9444', '8235', '9329', '9331', '9303', '8938', '8433', '9087', '9412', '9771', '9066', '9479', '9984', '8732', '9094', '8782', '9894', '8865', '10060', '8258', '10071', '10078', '8935', '10079', '10073', '8987', '10086', '8981', '10094', '10098', '8940', '10103', '8927', '10069', '10110', '10057', '10114', '8812', '9507', '9344', '8438', '10061', '10126', '10050', '10058', '10132', '10133', '10137', '10047', '8903', '9456', '10149', '8181', '8879', '9949', '10216', '10231', '9127', '10251', '10257', '10254', '10265', '10266', '10138', '10262', '10263', '10255', '10085', '10312', '10311', '10310', '10309', '10307', '10306', '10276', '10298', '10297', '10292', '10286', '10285', '10281', '10277', '10304', '10302', '10300', '10299', '10268', '10303', '10279', '10282', '10280', '10278', '10275', '10274', '10296', '10295', '10287', '10284', '10283', '10305', '10317', '10261', '10321', '10320', '10326', '10325', '10327', '10331', '10330', '10343', '10344', '10345', '10346', '10347', '10348', '10349', '10350', '10364', '10335', '10366', '10365', '10371', '10370', '10351', '10360', '10361', '10362', '10363', '10367', '10368', '10372', '10373', '10375', '10376', '10380', '10385', '10384', '10383', '10382', '10381', '10387', '10386', '10390', '10389', '10388', '8218', '10425', '10423', '10431', '8756');

        $addPoint = (in_array($customerkey, $arrCustomer)) ? 5 : 0;

        $sql = 'update ' . $this->tableCustomer . ' 
                set ' . $this->tableCustomer . '.point = ' . $addPoint . ' + (
                    select coalesce(sum(' . $this->tableName . '.totalpoint),0) as totalpoint 
                    from ' . $this->tableName . ' 
                    where ' . $this->tableName . '.statuskey = 2 and ' . $this->tableName . '.customerkey = ' . $this->oDbCon->paramString($customerkey) . ' )
                where ' . $this->tableCustomer . '.pkey = ' . $this->oDbCon->paramString($customerkey);

        // $this->setLog($sql,true);
        $this->oDbCon->execute($sql);
    }


    function sendReceiptUploadedEmail($customerkey, $code)
    {

        global $twig;

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $code;

        $twig->render('email-template.html');
        $content = $twig->render('email-receipt-uploaded.html', $arrTwigVar);

        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <no-reply@lottexylitolsmile.id>' . "\r\n";

        // $this->sendMail('', '', 'Struk berhasil diupload' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);
        mail($rsCust[0]['email'], 'Struk berhasil diupload' . ' - ' . DOMAIN_NAME, $content ,$headers);
        // $this->sendMail('','', 'Struk berhasil diupload' . ' - ' . DOMAIN_NAME,$content,'martinhalimk@gmail.com');

    }


    function sendReceiptApprovedEmail($rsHeader)
    {

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->templateDocPath);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extension_Array());

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/_twig-function.php';

        $customerkey = $rsHeader[0]['customerkey'];

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['POINT_NEEDED'] = 20 - $rsCust[0]['point'];
        $arrTwigVar['TOTAL_POINT'] = $rsCust[0]['point'];
        $arrTwigVar['POINT'] = $rsHeader[0]['totalpoint'];
        $arrTwigVar['TRANS_DATE'] = $rsHeader[0]['trdate'];

        $twig->render('email-template.html');
        $content = $twig->render('email-receipt-approved.html', $arrTwigVar);

        $this->sendMail('', '', 'Verifikasi Struk Berhasil' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);        
    }


    function sendReceiptRejectedEmail($rsHeader)
    {

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->templateDocPath);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extension_Array());

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/_twig-function.php';


        $customerkey = $rsHeader[0]['customerkey'];

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $rsHeader[0]['code'];
        $arrTwigVar['TRANS_DATE'] = $rsHeader[0]['trdate'];
        $arrTwigVar['CANCEL_REASON'] = $rsHeader[0]['cancelreason'];

        $twig->render('email-template.html');
        $content = $twig->render('email-receipt-rejected.html', $arrTwigVar);

        //$this->setLog($content,true);
        $this->sendMail('', '', 'Verifikasi Struk Gagal' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);
    }

    function sendVoucherEmail($customerkey, $code)
    {

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->templateDocPath);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extension_Array());

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/_twig-function.php';

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $code;

        $twig->render('email-template.html');
        $content = $twig->render('email-voucher.html', $arrTwigVar);

        $this->sendMail('', '', 'Tiket Lucky Draw' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);
    }

    function sendReceiptPoinsEmail($rsHeader)
    {

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->templateDocPath);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extension_Array());

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/_twig-function.php';


        $customerkey = $rsHeader[0]['customerkey'];

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $rsHeader[0]['code'];
        $arrTwigVar['TRANS_DATE'] = $rsHeader[0]['trdate'];
        $arrTwigVar['CANCEL_REASON'] = $rsHeader[0]['cancelreason'];

        $twig->render('email-template.html');
        $content = $twig->render('email-notification-poins.html', $arrTwigVar);

        //$this->setLog($content,true);
        $this->sendMail('', '', 'Point Anda Belum Cukup' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);
    }

    function isAgeValid($trdate, $ageLimit = 12)
    {
        // format $trdate = Y-m-d
        $todayYear = date('Y');
        $todayMonth = date('m');
        $todayDate = date('d');

        $dobYear = $this->formatDBDate($trdate, 'Y');
        $dobMonth = $this->formatDBDate($trdate, 'm');
        $dobDate = $this->formatDBDate($trdate, 'd');

        if (($todayYear - $dobYear) <  $ageLimit)  return false; // kalo secara tahun sudah dibawah 12, blm jalan 12
        /*if ($todayYear == ($dobYear+$ageLimit) && $dobMonth > $todayMonth ) return false; // kalo jln 12, tp blm sampe bulannya
        if ($todayYear == ($dobYear+$ageLimit) && $dobMonth == $todayMonth && $dobDate > $todayDate) return false;*/

        return true;
    }

    function sendVoucher40Email ($customerkey, $code){
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->templateDocPath);

        $twig = new Twig_Environment($loader);
        $twig->addExtension(new Twig_Extension_Array());

        require_once  $_SERVER['DOCUMENT_ROOT'] . '/_twig-function.php';

        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);

        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();

        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $code;

        $twig->render('email-template.html');
        $content = $twig->render('email-voucher40.html', $arrTwigVar);

        $this->sendMail('', '', 'Tiket Lucky Draw' . ' - ' . DOMAIN_NAME, $content, $rsCust[0]['email']);
    }
}
