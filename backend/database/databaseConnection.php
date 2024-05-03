<?php

function getDbConn(){

    require_once "/var/www/html/apps/yashTest/config/config.php";
    $config  = getConfig('DATABASE_CONFIGURATION');
    
    $host   = $config['_HOST_'];
    $user   = $config['_USER_'];
    $pass   = $config['_PASS_'];
    $dbName = $config['_DBNAME'];

    try {
        $conn = mysqli_connect($host, $user,$pass,$dbName);
    }catch(exception $e){
        //error_logs('ERROR: Connection to the Database Failed '. $e->getMessage());
        echo "Connection Failed: " . $e->getMessage();
    }
    return $conn;
}
?>
