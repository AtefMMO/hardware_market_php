<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$user_id=secureRequest($_POST['user_id']);
$address_id=secureRequest($_POST['address_id']);
deleteAddress($address_id,$user_id);
$response['status'] = "success";
$response["message"] = "Address Deleted Successfully";
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);