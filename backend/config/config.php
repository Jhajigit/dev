<?php
$config_file = "/var/www/html/grofkit/test/config/appConfiguration.ini";

if(!file_exists($config_file)){
    //error_logs("Error: Configuration File Missing");
    exit('Error: Configuration File Missing');
}

$config = parse_ini_file($config_file,true);
$GLOBALS['config'] = $config;


function getConfig($key){
    global $config;
    $result = [];
    if(count($config[$key] > 0)){
        foreach ($config[$key] as $ke => $val) {
            $result[$ke] = $val;
        }
    }else{
        return false;
    }
    return $result;
}

?>
