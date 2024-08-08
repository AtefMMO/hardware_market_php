<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $id=secureRequest($_GET['id']);
    $addresses=getUserAdresses($id);
    $response['status'] = "success";
    $response["message"] = "User Addresses fetched successfully";
    $response["addresses"] = $addresses;
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);