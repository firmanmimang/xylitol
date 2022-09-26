<?php   
 
if(empty(DOMAIN_NAME))  die ("account error");

$connectionFile = DOC_ROOT. 'connections/'.DOMAIN_NAME.'.php';
    
if(!is_file($connectionFile))  die ("connection failed");
     
$host = 'localhost';

require_once $connectionFile;      
  
// sementara utk beberapa fitur khusus, seperti updatesecuritymodule
function newConnection($domainName){
    global $class;
    global $host;
    
    $connectionFile = DOC_ROOT. 'connections/'.$domainName.'.php'; 
    if(!is_file($connectionFile))  die ("account error");
    
    include $connectionFile;     
    return new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);
}

define('SECRETKEY', '1234567890'); // ganti secretkey ketika di production
define('LOGIN_SESSION', md5(strtolower(DOMAIN_NAME).SECRETKEY));
define('LOGIN_ADMIN_SESSION', md5(strtolower(DOMAIN_NAME).SECRETKEY.SECRETKEY));

?>