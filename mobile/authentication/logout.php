<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
$id=secureRequest($_GET['id']);
$stmt = $connection->prepare("UPDATE `users` SET `mobile_token` = NULL WHERE `id` = ?");
$stmt->execute(array($id));
$response['status'] = "success";
$response["message"] = "User Logged Out Successfully";
$response["user"] = [
    "id" => $id,
];
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);