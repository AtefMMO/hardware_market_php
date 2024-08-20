<?php
include "../connect.php";
include "../core.php";
$response = [];
try{
    $user_id = secureRequest($_POST['user_id']);
    $stmt = $connection->prepare("SELECT * FROM `users` WHERE id = ?");
    $stmt->execute(array($user_id));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user){
        $response['status'] = "success";
        $response['user'] = $user;
    }
    $stmt = $connection->prepare("SELECT * FROM `addresses` WHERE `user_id` = ? AND `is_primary_address` = 1");
    $stmt->execute(array($user_id));
    $primary_address = $stmt->fetch(PDO::FETCH_ASSOC);
    if($primary_address){
        $response['primary_address'] = $primary_address;
    }
}catch(PDOException $e){
    $response['status'] = "error";
    $response['message'] = "Error: ".$e->getMessage();
}
echo json_encode($response);