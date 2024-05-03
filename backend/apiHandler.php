<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: -1");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headerss, X-Requested-With, Origin, Accept, Pragma, Cache-Control, expires");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 204 No Content");
    die();
}

require_once "/var/www/html/apps/yashTest/config/config.php";
$apiConfig = getConfig('API_CONFIGURATION');

include_once("./database/databaseConnection.php");
$conn = getDbConn(); // This variable Holds the Connection Variable.

// Array For returning the Data:-
$resArr = array(
    "transaction_id" => "",
    "responseCode"   => "0",
    "status"         => "Failure",
    "message"        => "",
);
$http_status = 500;

//receiving json data
$json_data_str = file_get_contents('php://input');
generate_logs("URI Received: ", $json_data_str);
$json_data_arr = json_decode($json_data_str, true);

$transaction_id = (isset($json_data_arr["transaction_id"]) && !empty($json_data_arr["transaction_id"]) ? $json_data_arr["transaction_id"] : "");

if(empty($transaction_id)){
    $resArr["message"] = "Transaction id is Mandatory";
    generate_logs("Response Retuned: ", json_encode($resArr));
    http_response_code(400); // Bad Request
    print json_encode($resArr);
    exit;
}

switch ($transaction_id) {
    case 'CTI_SEND_MAIL':
        include_once("./sendMailService.php");
        $resArr["transaction_id"] = $transaction_id;

        $name    = (isset($json_data_arr["name"])    && !empty($json_data_arr["name"])    ? $json_data_arr["name"]    : "");
        $email   = (isset($json_data_arr["email"])   && !empty($json_data_arr["email"])   ? $json_data_arr["email"]   : "");
        $phone   = (isset($json_data_arr["phone"])   && !empty($json_data_arr["phone"])   ? $json_data_arr["phone"]   : "");
        $message = (isset($json_data_arr["message"]) && !empty($json_data_arr["message"]) ? $json_data_arr["message"] : "");

        if(!empty($name) && !empty($email) && !empty($message)){
            // Function Call to Store it in the Database.
            $qryString = "INSERT INTO table_name () VALUES ()";
            // $query = mysqli_qyery($conn,$qryString);
            // if($query){
            if(true){
                $emailSender = new EmailSender(array(
                    "name"      => $name,
                    "email"     => $email,
                    "phone"     => $phone,
                    "message"   => $message,
                ));

                $result = $emailSender->sendEmail();
                if ($result === "true") {
                    $http_status = 200;
                    $resArr["responseCode"] = "1";
                    $resArr["status"]       = "SUCCESS";
                    $resArr["message"]      = "Data successfully sent";
                } else {
                    generate_logs("Email Sender Gives Error: ",$result);
                    $http_status = 500;
                    $resArr["message"] = "Something Went wrong!";
                }
            }else{
                generate_logs("Mysql Query Filed ==> Query String: ",$qryString);
                $http_status = 500;
                $resArr["message"] = "Something Went wrong!";
            }
        }else{
            generate_logs("Mandatory Params are missing ", "Name: $name | Email: $email | Message: $message ");
            $http_status = 400;
            $resArr["message"] = "Mandatory Params Are Missing";
        }
        break;
    
    default:
        # code...
        break;
}


generate_logs("Response Returned: ",json_encode($resArr));

// This header is set to tell the browser that it should parse the response as json String.
header("Content-Type: application/json");
http_response_code($http_status);
print json_encode($resArr);


function generate_logs($log_type,$message){
    global $apiConfig;
    if(file_exists($apiConfig['_LOG_FILE_PATH_']) && $apiConfig['_ENABLE_LOGS_'] == "true"){
        $logMsg = date("Y-m-d H:i:s") . " == [$log_type] == ";
        if(is_array($message) || is_object($message)){
		$logMsg .= print_r($message, true);
	}else{
		 $logMsg .= $message;
	}

        $logMsg .= PHP_EOL;

        // Writing to the file:-
        file_put_contents($apiConfig['_LOG_FILE_PATH_'], $logMsg, FILE_APPEND);
    }
}
?>
