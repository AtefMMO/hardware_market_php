<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$address_id=secureRequest($_GET['address_id']);
$user_id=secureRequest($_GET['user_id']);
setAddressToPrimary($user_id,$address_id);
$response['status'] = "success";
$response["message"] = "Address Activated Successfully";
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);